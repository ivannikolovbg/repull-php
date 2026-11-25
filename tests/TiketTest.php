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
use Repull\Api\TiketApi;
use Repull\Configuration;
use Repull\Model\TiketAriUpdate;
use Repull\Model\TiketAriWriteResponse;
use Repull\Model\TiketAvailabilityUpdateRequest;
use Repull\Model\TiketReservationListResponse;

/** tiket.com channel: the request on the wire and the decoded response. */
final class TiketTest extends TestCase
{
    /** @var array<int, array{request: \Psr\Http\Message\RequestInterface}> */
    private array $history = [];

    private function api(string $json): TiketApi
    {
        $this->history = [];
        $stack = HandlerStack::create(new MockHandler([new Response(200, ['Content-Type' => 'application/json'], $json)]));
        $stack->push(Middleware::history($this->history));
        $config = (new Configuration())->setHost('https://api.repull.dev')->setAccessToken('sk_test');
        return new TiketApi(new Client(['handler' => $stack]), $config);
    }

    public function testListReservationsSendsFiltersAndDecodesRows(): void
    {
        $api = $this->api('{"data":[{"reservationId":"42","tiketReservationId":"T1","confirmationCode":"T1","paymentMethod":"Bank Transfer","status":"confirmed"}],"pagination":{"limit":50,"offset":0,"hasMore":false}}');
        $res = $api->listTiketReservations('123', 'confirmed', new DateTime('2026-12-01'));

        $r = $this->history[0]['request'];
        $this->assertSame('GET', $r->getMethod());
        $this->assertSame('/v1/channels/tiket/reservations', $r->getUri()->getPath());
        $this->assertSame('property_id=123&status=confirmed&check_in_from=2026-12-01&limit=50&offset=0', rawurldecode($r->getUri()->getQuery()));
        $this->assertInstanceOf(TiketReservationListResponse::class, $res);
        $this->assertSame('42', $res->getData()[0]->getReservationId());
        $this->assertSame('Bank Transfer', $res->getData()[0]->getPaymentMethod());
    }

    public function testUpdateAvailabilitySendsRatesWrite(): void
    {
        $api = $this->api('{"propertyId":"123","type":"rates","updates":1,"requests":1,"echoTokens":["e1"],"warnings":[],"applied":"verified"}');
        $res = $api->updateTiketAvailability(new TiketAvailabilityUpdateRequest([
            'property_id' => '123',
            'type' => 'rates',
            'updates' => [new TiketAriUpdate([
                'room_type_code' => 'R1', 'rate_plan_code' => 'P1',
                'start' => new DateTime('2026-12-01'), 'end' => new DateTime('2026-12-02'), 'price' => 25000000.0,
            ])],
        ]));

        $r = $this->history[0]['request'];
        $this->assertSame(['PUT', '/v1/channels/tiket/availability'], [$r->getMethod(), $r->getUri()->getPath()]);
        $body = json_decode((string) $r->getBody(), true, 512, JSON_THROW_ON_ERROR);
        $this->assertSame('123', $body['property_id']);
        $this->assertSame(['roomTypeCode' => 'R1', 'ratePlanCode' => 'P1', 'start' => '2026-12-01', 'end' => '2026-12-02', 'price' => 25000000], $body['updates'][0]);
        $this->assertInstanceOf(TiketAriWriteResponse::class, $res);
        $this->assertSame('verified', $res->getApplied());
    }
}
