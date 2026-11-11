# Repull PHP SDK

> PHP SDK for [Repull](https://repull.dev) — one API for vacation-rental tech: Airbnb, Booking.com, Vrbo and more channels, plus 15 PMS platforms, behind one key. Generated from OpenAPI. **PHP 8.1+**.

## Why a PHP SDK?

WordPress is the dominant CMS for direct-booking websites in the vacation-rental industry. If you're building a booking flow, channel-manager dashboard, or guest portal in PHP, the Repull PHP SDK is your on-ramp to:

- Airbnb, Booking.com, Vrbo and more channels through one REST API
- 15 PMS platforms behind the same endpoints and data model
- Reservations, listings, calendars, guest messaging, reviews and webhooks
- White-label Connect flows for linking your users' accounts

## Install

```bash
composer require repull/sdk
```

Published on [Packagist](https://packagist.org/packages/repull/sdk).

## Quick start

```php
<?php
require __DIR__ . '/vendor/autoload.php';

use GuzzleHttp\Client;
use Repull\Api\ReservationsApi;
use Repull\Configuration;

$config = Configuration::getDefaultConfiguration()
    ->setAccessToken(getenv('REPULL_API_KEY'));

$api = new ReservationsApi(new Client(), $config);
$response = $api->v1ReservationsGet(limit: 10);

foreach ($response->getData() as $r) {
    printf(
        "%s  %s → %s  %s  %s %s\n",
        $r->getId(),
        $r->getCheckIn()?->format('Y-m-d'),
        $r->getCheckOut()?->format('Y-m-d'),
        $r->getPlatform(),
        $r->getTotalPrice(),
        $r->getCurrency()
    );
}
```

Run it:

```bash
REPULL_API_KEY=sk_live_... php examples/quickstart.php
```

## Authentication

All requests require a Bearer token (`sk_live_...`). Get a key at <https://repull.dev/dashboard>.

```php
$config = Configuration::getDefaultConfiguration()
    ->setAccessToken('sk_live_...');
```

## Examples

| File | What it does |
| --- | --- |
| [`examples/quickstart.php`](examples/quickstart.php) | List the latest 10 reservations |
| [`examples/connect_airbnb.php`](examples/connect_airbnb.php) | Mint an Airbnb OAuth Connect session |

## What's in the box

One API class per tag of the Repull API — for example `ReservationsApi`, `PropertiesApi`, `ListingsApi`, `ConversationsApi`, `ConnectApi`, `AirbnbApi`, `BookingComApi`, `VRBOApi` and `WebhooksApi`. The full set lives in [`src/Api/`](src/Api).

Plus typed model classes for `Property`, `Reservation`, `Guest`, `CalendarDay`, `Conversation`, `Message`, `Connection`, `WebhookSubscription`, etc.

Full reference: <https://repull.dev/docs>.

## Regenerating from OpenAPI

The SDK is generated from the live spec at <https://api.repull.dev/openapi.json> using the `php-nextgen` generator:

```bash
./scripts/regen.sh
php scripts/relax-enums.php  # forward-compat patch for enum drift
composer phpstan
```

`scripts/relax-enums.php` rewrites strict enum validators in generated models so the SDK accepts unknown values gracefully — necessary because the spec occasionally lags what the live API returns.

## Versioning

The SDK version tracks the OpenAPI document version. The current snapshot is committed at [`openapi/v1.json`](openapi/v1.json).

## License

MIT — see [`LICENSE`](LICENSE).

## Other Repull SDKs

- [`repull-sdk`](https://github.com/ivannikolovbg/repull-sdk) — TypeScript SDK + interactive demo
- [`repull-python`](https://github.com/ivannikolovbg/repull-python) — Python SDK
- [`repull-go`](https://github.com/ivannikolovbg/repull-go) — Go SDK
- [`repull-ruby`](https://github.com/ivannikolovbg/repull-ruby) — Ruby SDK
- [`repull-dotnet`](https://github.com/ivannikolovbg/repull-dotnet) — .NET SDK

---

Powered by [Repull](https://repull.dev).
