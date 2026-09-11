# Changelog

All notable changes to the Repull PHP SDK are documented here.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [0.2.14] - 2026-09-11

### Fixed
Regenerated against the live spec after 19 schema corrections were merged upstream. Path/operation inventory is unchanged (124 paths / 174 operations) — only the SHAPES of existing types changed:
- **10 fields renamed snake_case → camelCase** on the wire: `data_freshness` → `dataFreshness` (`AirbnbListingListResponse` and the inline `AirbnbDataFreshness`-bearing responses), `last_synced_at` → `lastSyncedAt`, `fix_url` → `fixUrl` (both on `AirbnbDataFreshness`), `next_cursor` → `nextCursor`, `has_more` → `hasMore` (`Pagination` and cursor-paginated list responses), `monthly_requests` → `monthlyRequests`, `daily_ai_requests` → `dailyAiRequests`, `daily_ai` → `dailyAi`, `dynamic_pricing_listings` → `dynamicPricingListings`, `resets_at` → `resetsAt` (usage/limits responses).
- **3 list responses became bare arrays** instead of `{data, pagination}` wrapper objects: `BookingComApi::listBookingProperties()` now returns `Repull\Model\BookingProperty[]`, `BookingComApi::listBookingConversations()` now returns `Repull\Model\BookingConversation[]`, `VRBOApi::listVrboListings()` now returns `Repull\Model\VrboListing[]`. The `BookingPropertyListResponse`, `BookingConversationListResponse`, and `VrboListingListResponse` wrapper model classes are removed — nothing else referenced them.
- **4 id fields `integer` → `string`**: `AirbnbAlteration::$id`, `AirbnbAlteration::$reservationId`, `AirbnbConnection::$id`, `AirbnbListing::$listingId`.
- **`Property::$latitude` / `Property::$longitude`: `float` → `string`** (decimal degrees, as a string, to avoid float-precision drift).

### Notes
- Regenerated from `https://api.repull.dev/openapi.json`. Generator: `@openapitools/openapi-generator-cli` with `php-nextgen` template.
- `scripts/check-spec-freshness.py` was strengthened to diff full schema shapes (property names, types, required lists), not just the operation inventory — it would have caught this class of drift immediately instead of silently shipping wrong types.
- No hand-maintained files were lost by the `rm -rf src` regen step; the only files that disappeared are the three wrapper model classes above, which no longer exist because their endpoints now return bare arrays.

## [0.2.13] - 2026-09-11

### Added
Regenerated against the live spec (174 operations, 124 paths — path count unchanged, four new METHODS added to existing paths):
- **Guests.** `GuestsApi::createGuest` (`POST /v1/guests`).
- **Reservations.** `ReservationsApi::createReservation` (`POST /v1/reservations`), `ReservationsApi::updateReservation` (`PATCH /v1/reservations/{id}`).
- **Conversations.** `ConversationsApi::sendConversationMessage` (`POST /v1/conversations/{id}/messages`).

### Notes
- Regenerated from `https://api.repull.dev/openapi.json`. Generator: `@openapitools/openapi-generator-cli` with `php-nextgen` template.
- The spec-freshness guard (`scripts/check-spec-freshness.py`) now compares full operations (method + path), not just path keys — these four endpoints were added as new methods on paths that already existed, so a path-only diff would have missed them entirely.

## [0.2.12] - 2026-09-11

### Removed
- **Sandbox API deleted.** `SandboxApi` (`POST /v1/sandbox/reset`, `POST /v1/sandbox/seed`) and its models (`SandboxFixtureRef`, `SandboxResetResult`, `SandboxResetResultDeleted`, `SandboxSeedResult`) are gone — the sandbox was removed from the live API and `sk_test_...` keys now return `401`. README/examples updated to use `sk_live_...`.

### Added
Regenerated against the live spec (89 → 102 → 124 tracked paths over prior drift); this release brings 24 previously-undeclared operations into the SDK:
- **Reviews.** `ReviewsApi::replyToReview` (`POST /v1/reviews/{id}/reply`).
- **Airbnb alterations.** `AirbnbApi::acceptAirbnbAlteration` / `declineAirbnbAlteration` (`POST /v1/channels/airbnb/alterations/{id}/accept` / `/decline`).
- **Booking.com rooms.** `BookingComApi` gains the rooms listing for a Booking.com property (`GET /v1/channels/booking/properties/{id}/rooms`).
- **Booking.com hosted Connect callback.** `ConnectApi::bookingConnectCallback` (`GET /v1/connect/booking/callback`).
- **PMS credential submission.** `ConnectApi::submit{Beds24,Bookingsync,Guesty,Hospitable,Hostaway,Igms,Lodgify,Ownerrez,Smoobu,Vrbo}Credentials` — direct API-key/credential connect flows for ten PMS providers that previously only supported OAuth.
- **Health checks.** `SystemApi` gains `getAtlasHealth`, `getAuthHealth`, `getMcpHealth`, `getWebhooksHealth`, and `getChannelHealth(channel)`.
- **Listing photos.** `ListingsApi::listListingPhotos` / `getListingPhotosUploadUrl` (`GET /v1/listings/{id}/photos`, `POST /v1/listings/{id}/photos/upload-url`).
- **Batch availability.** `AvailabilityApi::batchAvailability` (`POST /v1/availability/batch`).
- **Quotes.** New `POST /v1/quotes` endpoint (pricing quote for a stay).

### Notes
- Regenerated from `https://api.repull.dev/openapi.json`. Generator: `@openapitools/openapi-generator-cli` with `php-nextgen` template.
- `POST /v1/reviews/{id}/reply` was missing its `{id}` path-parameter declaration in the live spec, which fails openapi-generator's spec validation and — if validation is skipped — silently drops `id` from the generated method signature (`ReviewsApi::replyToReview` would have built requests against the literal, unsubstituted `/v1/reviews/{id}/reply` URL). A fix for the spec source (`vanio-repull-api`) has been prepared and committed locally on that repo's `fix/reviews-reply-path-param` branch (not yet merged/deployed). This SDK was generated against a locally-patched copy of the spec carrying that same parameter declaration (`id: integer, in: path, required`) so `replyToReview(int $id, ...)` works correctly today; the *committed* `openapi/v1.json` in this repo is untouched and remains byte-for-byte identical to the live spec.
- Examples (`examples/quickstart.php`, `examples/connect_airbnb.php`) updated for current method names (`listReservations`, `createConnection`, `getConnectStatus`) — they referenced pre-rename method names (`v1ReservationsGet`, `v1ConnectProviderPost`, `v1ConnectProviderGet`) that no longer exist in the generated client.

## [0.2.9] - 2026-07-26

### Added
- **Deactivate a listing.** `ListingsApi::deactivateListing` (`DELETE /v1/listings/{id}`) unpublishes/deactivates a listing.
- **Reactivate / toggle a listing.** `ListingsApi::updateListingActive` (`PATCH /v1/listings/{id}`) updates a listing's active state.

### Notes
- `POST /v1/listings` now documents a `402` response (billing/quota required) in addition to `201`/`400`.
- Regenerated from `https://api.repull.dev/openapi.json`. Generator: `@openapitools/openapi-generator-cli` with `php-nextgen` template.

## [0.2.7] - 2026-06-25

### Added
- **Booking.com hosted Connect flow.** New methods on `ConnectApi` for connecting a Booking.com hotel: `verifyBookingHotel` (`POST /v1/connect/booking/verify`), `listConnectBookingRooms` (`GET /v1/connect/booking/rooms`), and `mapConnectBookingRooms` (`POST /v1/connect/booking/map-rooms`), plus `createConnectSession` (`POST /v1/connect`) for the multi-channel Connect picker session. `CreateConnectionRequest::$redirect_url` now applies to Airbnb + Booking.com hosted connect flows (previously Airbnb only).
- **`channel` filter on `GET /v1/properties`.** `PropertiesApi::listProperties` accepts an optional `channel` argument to filter to properties with an active link on the given OTA/channel (`airbnb`, `booking`, `vrbo`). Omit to include every channel.
- **`channels` array on `Property`.** Each property now returns `channels` — the OTAs/channels it is actively published on (e.g. `airbnb`, `booking`, `vrbo`). Empty array when the property has no active channel links. Accessor: `Property::getChannels()`.

### Notes
- Regenerated from `https://api.repull.dev/openapi.json`. Generator: `@openapitools/openapi-generator-cli` with `php-nextgen` template.
- Enum validators relaxed in 58 model files for forward compatibility; const-name `/`→`_` fix applied by `scripts/relax-enums.php`.

## [0.2.6] - 2026-06-24

### Fixed
- **Valid PHP constant names for webhook event types.** The `php-nextgen` generator turned dotted enum values (`account.created`, `ai.operation.completed`, `reservation.message.received`, …) into constant names with an illegal `/` separator (`WebhookEvent::TYPE_ACCOUNT/CREATED`), producing a `Syntax error, unexpected '/'` that failed `phpstan` and made `src/Model/WebhookEvent.php` un-parseable. The post-codegen patcher (`scripts/relax-enums.php`) now rewrites `/` to `_` in generated const declarations and `self::` references, yielding valid names (`WebhookEvent::TYPE_ACCOUNT_CREATED`, `TYPE_AI_OPERATION_COMPLETED`, `TYPE_RESERVATION_MESSAGE_RECEIVED`, …). Reproduced on generator 7.22.0 and 7.23.0, so the fix is version-independent.

### Notes
- Includes the `messaging` Airbnb Connect access scope from 0.2.5 (`CreateConnectionRequest::ACCESS_TYPE_MESSAGING`).
- Regenerated from `https://api.repull.dev/openapi.json`. Generator: `@openapitools/openapi-generator-cli` with `php-nextgen` template.

## [0.2.5] - 2026-06-24

### Added
- **`messaging` Airbnb Connect access scope (read + send guest messages, no property management).** `POST /v1/connect/airbnb` now accepts `accessType: "messaging"` alongside `read_only` and `full_access`. The `messaging` scope grants read scopes plus message read/send but NOT property management, so it can coexist with another app (e.g. an existing PMS) that already holds property management on the same Airbnb account. Exposed as `CreateConnectionRequest::ACCESS_TYPE_MESSAGING`.

### Notes
- Regenerated from `https://api.repull.dev/openapi.json`. Generator: `@openapitools/openapi-generator-cli` with `php-nextgen` template.
- Enum validators relaxed in 58 model files for forward compatibility.

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
