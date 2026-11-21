<?php

declare(strict_types=1);

namespace Repull\Test;

use DateTime;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestInterface;
use Repull\Api\AirbnbApi;
use Repull\Api\AvailabilityApi;
use Repull\Api\ConversationsApi;
use Repull\Api\ReservationsApi;
use Repull\Api\VRBOApi;
use Repull\Configuration;
use Repull\Model\UpdateAirbnbListingRoomRequest;
use Repull\Model\UpdateAirbnbListingRoomRequestRoomAmenitiesInner;
use Repull\Model\UpdateVrboListingAmenitiesRequest;
use Repull\Model\UpdateVrboListingAmenitiesRequestGroups;
use Repull\Model\UpdateVrboListingAmenitiesRequestGroupsEssentials;
use Repull\Model\UpdateVrboListingHouseRulesRequest;
use Repull\Model\UpdateVrboListingHouseRulesRequestPets;
use Repull\Model\UpdateVrboListingPermitsRequest;
use Repull\Model\UpdateVrboListingPermitsRequestSettingsInner;

/**
 * Asserts the exact request each call puts on the wire (method, path, query,
 * JSON body), using a Guzzle mock handler instead of the network.
 *
 * Regression tests for two generator defects (both fixed in the generator
 * setup, not in generated code, so a regen keeps them fixed):
 *  - a `\DateTime` passed to a `format: date` query parameter threw
 *    "Object of class DateTime could not be converted to string" before any
 *    request was sent (templates/ObjectSerializer.mustache + api.mustache);
 *  - a field typed boolean|number|string (primitive oneOf) was a value-less
 *    wrapper class that serialised to `{}` (scripts/prepare-spec.php).
 */
final class RequestSerializationTest extends TestCase
{
    /** @var array<int, array{request: RequestInterface}> */
    private array $history = [];

    private Client $http;

    private Configuration $config;

    protected function setUp(): void
    {
        $this->respondWith('{"data":{}}');
    }

    /** Every request is answered 200 with this JSON body; the test only inspects the request. */
    private function respondWith(string $json): void
    {
        $this->history = [];
        $mock = new MockHandler([new Response(200, ['Content-Type' => 'application/json'], $json)]);
        $stack = HandlerStack::create($mock);
        $stack->push(Middleware::history($this->history));
        $this->http = new Client(['handler' => $stack]);
        $this->config = (new Configuration())->setHost('https://api.repull.dev')->setAccessToken('sk_test');
    }

    /** @return array{0: string, 1: string, 2: string, 3: mixed} method, path, decoded query, decoded JSON body */
    private function sent(): array
    {
        $this->assertCount(1, $this->history, 'exactly one request is sent');
        $r = $this->history[0]['request'];
        $body = (string) $r->getBody();
        return [
            $r->getMethod(),
            $r->getUri()->getPath(),
            rawurldecode($r->getUri()->getQuery()),
            $body === '' ? null : json_decode($body, true, 512, JSON_THROW_ON_ERROR),
        ];
    }

    // ── format: date / date-time query parameters ──────────────────────────────

    public function testVrboRatesSendsDateQuery(): void
    {
        $api = new VRBOApi($this->http, $this->config);
        $api->getVrboListingRates('6199', null, new DateTime('2026-12-01'), new DateTime('2026-12-14'));

        $this->assertSame(
            ['GET', '/v1/channels/vrbo/listings/6199/rates', 'start=2026-12-01&end=2026-12-14', null],
            $this->sent()
        );
    }

    public function testReservationSummarySendsDateQuery(): void
    {
        $api = new ReservationsApi($this->http, $this->config);
        $api->getReservationSummary(new DateTime('2026-01-01'), new DateTime('2026-01-31'), null, 'stay', null, new DateTime('2026-02-01'));

        $this->assertSame(
            ['GET', '/v1/reservations/summary', 'from=2026-01-01&to=2026-01-31&basis=stay&as_of=2026-02-01', null],
            $this->sent()
        );
    }

    public function testAvailabilitySendsRequiredDateQuery(): void
    {
        $api = new AvailabilityApi($this->http, $this->config);
        $api->getAvailability(42, new DateTime('2026-03-01'), new DateTime('2026-03-31'));

        $this->assertSame(['GET', '/v1/availability/42', 'from=2026-03-01&to=2026-03-31', null], $this->sent());
    }

    public function testDateTimeQueryKeepsTime(): void
    {
        $this->respondWith('{"data":[]}');
        $api = new ConversationsApi($this->http, $this->config);
        $api->listConversations(null, null, null, null, null, null, new DateTime('2026-11-20T10:30:00+00:00'));

        $this->assertSame(
            ['GET', '/v1/conversations', 'updated_since=2026-11-20T10:30:00+00:00&order=recent', null],
            $this->sent()
        );
    }

    // ── boolean|number|string (primitive oneOf) body fields ───────────────────

    public function testVrboAmenitiesSendsPlainValues(): void
    {
        $api = new VRBOApi($this->http, $this->config);
        $api->updateVrboListingAmenities('6199', new UpdateVrboListingAmenitiesRequest([
            'groups' => new UpdateVrboListingAmenitiesRequestGroups([
                'essentials' => new UpdateVrboListingAmenitiesRequestGroupsEssentials([
                    'values' => ['HAIR_DRYER' => true, 'TOWELS' => false, 'WIFI_SPEED' => 250, 'PARKING' => 'FREE'],
                ]),
            ]),
        ]));

        $this->assertSame(
            ['PUT', '/v1/channels/vrbo/listings/6199/amenities', '', [
                'groups' => ['essentials' => ['values' => ['HAIR_DRYER' => true, 'TOWELS' => false, 'WIFI_SPEED' => 250, 'PARKING' => 'FREE']]],
            ]],
            $this->sent()
        );
    }

    public function testVrboPermitsSendsPlainValues(): void
    {
        $api = new VRBOApi($this->http, $this->config);
        $api->updateVrboListingPermits('6199', new UpdateVrboListingPermitsRequest([
            'settings' => [
                new UpdateVrboListingPermitsRequestSettingsInner(['name' => 'licenseNumber', 'value' => 'STR-2026-0042']),
                new UpdateVrboListingPermitsRequestSettingsInner(['name' => 'exempt', 'value' => false]),
                new UpdateVrboListingPermitsRequestSettingsInner(['name' => 'bedrooms', 'value' => 3]),
            ],
        ]));

        $this->assertSame(
            ['PUT', '/v1/channels/vrbo/listings/6199/permits', '', ['settings' => [
                ['name' => 'licenseNumber', 'value' => 'STR-2026-0042'],
                ['name' => 'exempt', 'value' => false],
                ['name' => 'bedrooms', 'value' => 3],
            ]]],
            $this->sent()
        );
    }

    public function testVrboHouseRulesPetMaxWeightSendsPlainValue(): void
    {
        $api = new VRBOApi($this->http, $this->config);
        $api->updateVrboListingHouseRules('6199', new UpdateVrboListingHouseRulesRequest([
            'pets' => new UpdateVrboListingHouseRulesRequestPets(['allowed' => true, 'max' => 2, 'max_weight' => 25]),
        ]));

        $this->assertSame(
            ['PUT', '/v1/channels/vrbo/listings/6199/house-rules', '', ['pets' => ['allowed' => true, 'max' => 2, 'maxWeight' => 25]]],
            $this->sent()
        );
    }

    public function testAirbnbRoomAmenityValueSendsPlainValue(): void
    {
        $api = new AirbnbApi($this->http, $this->config);
        $api->updateAirbnbListingRoom('6199', '77', new UpdateAirbnbListingRoomRequest([
            'room_amenities' => [
                new UpdateAirbnbListingRoomRequestRoomAmenitiesInner(['id' => '1', 'name' => 'en_suite_bathroom', 'value' => true]),
                new UpdateAirbnbListingRoomRequestRoomAmenitiesInner(['id' => '2', 'name' => 'tv_size', 'value' => 55]),
            ],
        ]));

        $this->assertSame(
            ['PUT', '/v1/channels/airbnb/listings/6199/rooms', 'roomId=77', ['room_amenities' => [
                ['id' => '1', 'name' => 'en_suite_bathroom', 'value' => true],
                ['id' => '2', 'name' => 'tv_size', 'value' => 55],
            ]]],
            $this->sent()
        );
    }
}
