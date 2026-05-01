<?php
/**
 * Quickstart: list the latest 10 reservations.
 *
 *   REPULL_API_KEY=sk_test_... php examples/quickstart.php
 */

require_once __DIR__ . '/../vendor/autoload.php';

use GuzzleHttp\Client;
use Repull\Api\ReservationsApi;
use Repull\Configuration;

$apiKey = getenv('REPULL_API_KEY') ?: throw new RuntimeException('Set REPULL_API_KEY');

$config = Configuration::getDefaultConfiguration()->setAccessToken($apiKey);
$api    = new ReservationsApi(new Client(), $config);

$response = $api->v1ReservationsGet(limit: 10);

foreach ($response->getData() ?? [] as $r) {
    printf(
        "%-8s  %s → %s  %-12s  %s %s\n",
        $r->getId() ?? '-',
        $r->getCheckIn()?->format('Y-m-d') ?? '-',
        $r->getCheckOut()?->format('Y-m-d') ?? '-',
        $r->getPlatform() ?? '-',
        $r->getTotalPrice() ?? '-',
        $r->getCurrency() ?? ''
    );
}
