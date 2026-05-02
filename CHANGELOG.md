# Changelog

All notable changes to the Repull PHP SDK are documented here.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

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
