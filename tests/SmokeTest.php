<?php

declare(strict_types=1);

namespace Repull\Test;

use GuzzleHttp\Client;
use PHPUnit\Framework\TestCase;
use Repull\Api\ReservationsApi;
use Repull\Api\SystemApi;
use Repull\Configuration;

/**
 * Smoke test: every generated class loads, the configuration accepts an API key,
 * and an API client can be constructed end-to-end. No network calls.
 */
final class SmokeTest extends TestCase
{
    public function testConfigurationAcceptsBearerToken(): void
    {
        $config = Configuration::getDefaultConfiguration()
            ->setAccessToken('sk_live_smoke');

        $this->assertSame('sk_live_smoke', $config->getAccessToken());
    }

    public function testReservationsApiInstantiates(): void
    {
        $api = new ReservationsApi(new Client(), Configuration::getDefaultConfiguration());

        $this->assertInstanceOf(ReservationsApi::class, $api);
        $this->assertInstanceOf(Configuration::class, $api->getConfig());
    }

    public function testSystemApiInstantiates(): void
    {
        $api = new SystemApi(new Client(), Configuration::getDefaultConfiguration());

        $this->assertInstanceOf(SystemApi::class, $api);
    }

    /**
     * Forward-compat: relax-enums.php patches the generated Reservation model
     * so unknown platforms/statuses (added to the API after spec snapshot)
     * don't crash the SDK. This test pins that behavior.
     */
    public function testReservationModelAcceptsUnknownPlatform(): void
    {
        $r = new \Repull\Model\Reservation();

        // 'test-flows' is not in the spec enum but the live API returns it.
        $r->setPlatform('test-flows');
        $r->setStatus('accept');

        $this->assertSame('test-flows', $r->getPlatform());
        $this->assertSame('accept', $r->getStatus());
    }

    public function testQuoteReservationIsExposedAndSerializes(): void
    {
        $api = new \Repull\Api\ReservationsApi();
        $this->assertTrue(method_exists($api, 'quoteReservation'));

        $q = new \Repull\Model\ReservationQuoteRequest([
            'listing_id' => 4118,
            'check_in' => new \DateTime('2026-10-01'),
            'check_out' => new \DateTime('2026-10-05'),
            'adults' => 2,
        ]);
        $json = json_decode(json_encode(\Repull\ObjectSerializer::sanitizeForSerialization($q)), true);
        $this->assertSame(4118, $json['listingId']);
        $this->assertSame('2026-10-01', $json['checkIn']);

        $res = \Repull\ObjectSerializer::deserialize(
            json_decode('{"listingId":"4118","available":true,"total":880,"restrictions":[]}'),
            '\\Repull\\Model\\ReservationQuoteResponse'
        );
        $this->assertTrue($res->getAvailable());
        $this->assertSame('4118', $res->getListingId());
    }

    public function testCreateReservationAcceptsPmsFields(): void
    {
        $c = new \Repull\Model\ReservationCreateRequest([
            'listing_id' => 4118,
            'check_in' => new \DateTime('2026-10-01'),
            'check_out' => new \DateTime('2026-10-05'),
            'guest' => new \Repull\Model\ReservationGuestInput(['first_name' => 'Ada']),
            'adults' => 2,
            'children' => 1,
            'total_price' => 880,
            'notes' => 'Late arrival',
            'unit_id' => 'u-1',
            'status' => 'tentative',
            'send_confirmation_email' => false,
        ]);
        $json = json_decode(json_encode(\Repull\ObjectSerializer::sanitizeForSerialization($c)), true);
        $this->assertSame('u-1', $json['unitId']);
        $this->assertSame('tentative', $json['status']);
        $this->assertFalse($json['sendConfirmationEmail']);
        $this->assertEquals(880, $json['totalPrice']);
    }
}
