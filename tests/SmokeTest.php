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
            ->setAccessToken('sk_test_smoke');

        $this->assertSame('sk_test_smoke', $config->getAccessToken());
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
}
