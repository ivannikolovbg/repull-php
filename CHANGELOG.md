# Changelog

All notable changes to the Repull PHP SDK are documented here.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [0.2.4] - 2026-05-15

### Added
- **`listings_limit_exceeded` (402) error documentation across every generated model.** The API now returns `402 Payment Required` with `error.code = "listings_limit_exceeded"` when a customer is over their tier's active-listing cap (free=5, starter=50, custom=unlimited). Unlike 429, this is NOT a "wait and retry" condition — `Retry-After` is not set. Recovery: `DELETE` listings to fall under the cap, or upgrade at `repull.dev/dashboard/billing`. `/v1/health`, `/v1/usage/*`, and any `DELETE` are exempt. The 402 envelope mirrors `rate_limit_exceeded` and adds `tier`, `limit`, `active_listings`, `upgrade_url`. Tracks vanio-repull-api PR #66.

### Notes
- Regenerated from `https://api.repull.dev/openapi.json`. Generator: `@openapitools/openapi-generator-cli` with `php-nextgen` template.
- Pre-existing PHPStan warnings on `WebhookEvent::TYPE_*` constants are not introduced by this regen (present on prior tagged releases).

## [0.2.2] - 2026-05-07

### Added
- **`?include=amenities` query param** on `GET /v1/listings/{id}` and `GET /v1/properties/{id}` — opt-in expansion that returns the listing/property with its amenities array hydrated. Pass `include=amenities` to receive the expanded payload; omit to keep the lean default response. Unknown values return 422.
- New `ListingAmenity` model and amenity arrays on `Listing` / `Property` responses.

### Notes
- Regenerated from `https://api.repull.dev/openapi.json`. Tracks vanio-repull-api PRs #59 and #61.
- Generator: `@openapitools/openapi-generator-cli` with `php-nextgen` template.
- Enum validators relaxed in 58 model files for forward compatibility.
- `vendor/bin/phpunit` (4 tests) green.

## [0.2.1] - 2026-05-06

### Changed
- Reservation `primaryGuest` / `occupancy` / `financials` shape regen.

## [0.2.0] - 2026-05-02

### Breaking
- **Canonical pagination envelope.** Every paginated list response is now `{ data: [...], pagination: { nextCursor, hasMore, total? } }`. The bespoke `CursorPagination`, `ReservationPagination`, `MarketBrowsePagination`, and `WebhookDeliveryListResponsePagination` classes are removed; one shared `Pagination` model is used everywhere.
- **All field names are camelCase.** Underscored attribute names on the wire (e.g. `external_id`, `listing_id`, `nightly_rate`, `submitted_at`) are now `externalId`, `listingId`, `nightlyRate`, `submittedAt`. PHP property names on the generated models still use snake_case internally, but the JSON-encoded payload — and the OpenAPI attribute map — is camelCase across every model.
- **All IDs are string-typed.** `Reservation.id`, `Review.id`, `Listing.id`, `Guest.id`, `Conversation.id`, etc. switch from `int` to `string`. Update consumer call sites that compared IDs as integers (`if ($r->getId() === 123)` → `if ($r->getId() === '123'`).
- **`POST /v1/connect/{provider}` (Airbnb)** — the response field renamed `oauthUrl` → `url` to match the multi-channel `ConnectSession` shape. `ConnectSession.url` is now the single canonical field for any hosted Connect URL the SDK returns.
- **`GET /v1/markets`** — response renamed `markets` → `data`, `total_in_filter` → `total` (now nested under `pagination`). Use `$response->getData()` and `$response->getPagination()->getTotal()`.
- **`GET /v1/reviews/{id}`** — returns the bare `Review` object, no `{ review: ... }` wrapper. The `ReviewGetResponse` model is removed; deserialize directly into `Review`.
- **`GET /v1/channels/airbnb/{listings,reservations,messaging,reviews}`** — all wrapped in `{ data, pagination }` envelopes (`AirbnbListingListResponse`, `AirbnbReservationListResponse`, `AirbnbThreadListResponse`, `AirbnbReviewListResponse`). Iterate via `$resp->getData()` and paginate via `$resp->getPagination()->getNextCursor()`.

### Added
- **Self-documenting error envelope.** `Error.error` now exposes `code`, `message`, `fix` (recovery steps), `docsUrl`, `requestId`, `field`, `valueReceived`, `validValues`, `didYouMean`, `retryAfter`, plus `support` (links to docs/status/contact). Designed for AI agents and SDK consumers to self-recover without escalating.
- **Rate-limit headers** documented in the API description: `X-RateLimit-Limit`, `X-RateLimit-Remaining`, `X-RateLimit-Reset`, `X-RateLimit-Policy`, `Retry-After`. SDK callers should honor `Retry-After` on 429 with exponential backoff + jitter.
- **`X-Request-ID` header** echoed on every response and embedded in error envelopes as `requestId`.
- **`X-Schema` header** on all 10 read endpoints — pass a custom schema slug to receive responses remapped to your field names.
- **Custom Schemas API** (`SchemaApi`) — 5 CRUD operations: create / list / get / update / delete custom field-mapping schemas.
- **Detail endpoints** added for one-off fetches: `GET /v1/conversations/{id}`, `GET /v1/listings/{id}`, etc., wired into the canonical envelope.
- **Key prefix support** — `sk_test_` (sandbox) and `sk_live_` (production) prefixes are documented as the canonical auth scheme.

### Notes
- Regenerated from `https://api.repull.dev/api/repull/openapi.json` (info.version `1.0.0`).
- Generator: `@openapitools/openapi-generator-cli` with `php-nextgen` template.
- Enum validators relaxed in 33 model files for forward compatibility.
- `composer validate --strict` clean. `vendor/bin/phpunit` (4 tests) green. `vendor/bin/phpstan analyse` (level configured in `phpstan.neon`) clean.

## [0.1.2] - 2026-05-02

### Added
- **Custom Schemas API** (`SchemaApi`) — 5 CRUD operations for managing custom field-mapping schemas:
  - `POST /v1/schema/custom` — create a schema
  - `GET /v1/schema/custom` — list schemas
  - `GET /v1/schema/custom/{id}` — fetch one
  - `PATCH /v1/schema/custom/{id}` — update
  - `DELETE /v1/schema/custom/{id}` — delete
- New `CustomSchema*` model classes: `CustomSchema`, `CustomSchemaCreate`, `CustomSchemaCreateResponse`, `CustomSchemaListResponse`, `CustomSchemaSummary`, `CustomSchemaUpdate`, `CustomSchemaDeleteResponse`, plus `CustomSchemaMappings` typing.
- Optional `X-Schema` header parameter on all read endpoints (10 GETs across reservations, listings, guests, conversations, reviews) — pass a custom schema slug to receive responses remapped to your field names.

### Changed
- **Breaking — Reservation shape**: matches the corrected upstream contract.
  - `propertyId` → `listingId`
  - `guestFirstName` / `guestFirst*` fields → `guestId` + `guestName` + structured `guestDetails`
  - Existing call sites that read `$reservation->getPropertyId()` or `$reservation->getGuestFirstName()` must be updated to `getListingId()` / `getGuestName()` / `getGuestDetails()`.

### Notes
- Regenerated from `https://api.repull.dev/openapi.json` (info.version `1.0.0`).
- Generator: `@openapitools/openapi-generator-cli` with `php-nextgen` template.
- Enum validators relaxed for forward compatibility (31 model files patched).

## [0.1.1] - 2026-05-01

### Added
- Conversations, Guests, and Reviews resources.
- Cursor-paginated reservations endpoint.

## [0.1.0] - 2026-05-01

- Initial release of the Repull PHP SDK (PHP 8.1+, php-nextgen template).
