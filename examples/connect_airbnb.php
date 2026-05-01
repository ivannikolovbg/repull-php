<?php
/**
 * Connect flow: mint an Airbnb OAuth session and print the consent URL.
 *
 *   REPULL_API_KEY=sk_test_... php examples/connect_airbnb.php
 *
 * Send the user to the printed URL. After they consent on Airbnb, Repull
 * redirects them back to your app. Poll status with v1ConnectProviderGet().
 */

require_once __DIR__ . '/../vendor/autoload.php';

use GuzzleHttp\Client;
use Repull\Api\ConnectApi;
use Repull\Configuration;
use Repull\Model\V1ConnectProviderPostRequest;

$apiKey = getenv('REPULL_API_KEY') ?: throw new RuntimeException('Set REPULL_API_KEY');

$config = Configuration::getDefaultConfiguration()
    ->setAccessToken($apiKey);

$api = new ConnectApi(new Client(), $config);

$body = new V1ConnectProviderPostRequest([
    'redirect_url' => 'https://yourapp.example/airbnb/return',
    'access_type'  => 'full_access', // or 'read_only'
]);

$session = $api->v1ConnectProviderPost('airbnb', $body);

print_r($session);

echo "\nSend the user to the oauthUrl above. Poll status:\n";
echo "  \$api->v1ConnectProviderGet('airbnb');\n";
