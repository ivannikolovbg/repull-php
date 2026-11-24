<?php
/**
 * AirbnbReservationDetail
 *
 * PHP version 8.1
 *
 * @package  Repull
 * @author   OpenAPI Generator team
 * @link     https://openapi-generator.tech
 */

/**
 * Repull API
 *
 * The unified API for vacation rental tech. Connect Airbnb, Booking.com, Vrbo and more channels, plus 14 PMS platforms, through one REST API.  ## Designed for AI agents Every error response on this API includes machine-parseable fields so an LLM (Claude in MCP, Cursor, Cline, GPT, etc.) can self-recover without escalating to a human: - `error.code` — stable string identifier (e.g. `invalid_params`, `rate_limit_exceeded`) - `error.message` — human-readable cause - `error.fix` — exact recovery steps (e.g. \"Pass `check_in_after` as ISO 8601: `?check_in_after=2026-01-15`\") - `error.docs_url` — link to the canonical write-up at `https://repull.dev/docs/errors/{code}` - `error.request_id` — id to correlate with server-side logs - `error.field` / `error.value_received` / `error.valid_values` / `error.did_you_mean` — when the error is parameter-specific - `error.retry_after` — seconds to wait before retrying (rate-limit + transient upstream)  `Access-Control-Expose-Headers` lists `x-request-id` and the `X-RateLimit-*` family so browsers can read them on cross-origin responses.  ## Quick Start 1. Get an API key at https://repull.dev/dashboard 2. Connect a PMS: `POST /v1/connect/{provider}` 3. List properties: `GET /v1/properties` 4. Get reservations: `GET /v1/reservations`  ## Authentication All requests require a Bearer token: ``` Authorization: Bearer sk_live_YOUR_API_KEY ```  ## Request Correlation (X-Request-ID) Every response carries an `X-Request-ID` header, e.g. `X-Request-ID: req_01HXY...`. Include this id in support tickets and bug reports — we can trace the full request lifecycle (auth, rate limit, handler, downstream calls, log row) from a single id.  You may set the header on the inbound request to forward your own trace id; we will echo it back instead of generating a new one. Accepted format: `^[\\\\w.-]{1,128}$`.  The id is also embedded in error envelopes as `request_id` so server-side log diffs work even when the response headers are stripped by an intermediate proxy.  ## Rate Limits The public API enforces a per-API-key sliding-window rate limit on top of the per-tier monthly + daily-AI quotas.  **Default policy:** 600 requests per 60 seconds, per API key. Sliding window — there is no fixed-minute boundary you can burst across.  Every response includes:  | Header | Meaning | |---|---| | `X-RateLimit-Limit` | Requests permitted in the current window. | | `X-RateLimit-Remaining` | Requests left in the current window after this call. | | `X-RateLimit-Reset` | Unix epoch (seconds) when the next slot opens. | | `X-RateLimit-Policy` | Machine-readable policy descriptor, e.g. `600;w=60`. | | `Retry-After` | Seconds to wait before retrying. **Only present on 429 responses.** |  **On 429 (rate_limit_exceeded):** the response body matches the standard error envelope with `code: \"rate_limit_exceeded\"`, plus `limit`, `window_seconds`, `retry_after`, and `request_id` fields. SDKs MUST honor `Retry-After` and use exponential backoff with jitter on subsequent retries — never a tight loop.  Recommended backoff: ``` sleep_ms = (Retry-After * 1000) + random(0..250) ```  Monthly + daily-AI tier quotas (`free`, `starter`, `custom`) are enforced separately and also surface as 429s; they include `tier`, `scope`, `limit`, `used` and `resets_at` fields.  **The way past a request quota.** The monthly quota refusal (`429 rate_limit_exceeded` with `scope: \"monthly\"`), `402 credits_exhausted` and `429 daily_limit_exceeded` also carry `upgrade` (the cheapest plan that covers your usage: its exact monthly price for your listings, its request quotas and a `checkout_url` that opens checkout for that price; `plan: \"custom\"` with a `contact_email` when no self-serve plan covers it), `credits` (pay-as-you-go: `usd_per_1000_requests`, `top_up_url`, your `balance_usd`), `resets_at` and `projected_monthly_requests`. The per-minute 429 (`scope: \"per_api_key\"`) carries none of these: wait `Retry-After`.  ## Plan listing limits Each plan includes a number of **active listings**:  | Tier | Active listings | |---|---| | `free` | 3 | | `starter` | no cap: 10 included, then billed per listing | | `custom` | no cap |  Going over the plan never blocks your API calls. Listings beyond the plan are **held back**: they stay connected and keep syncing, but they are not served. A held-back listing answers `403 listing_inactive` with `reason: \"plan_limit\"`, and collection routes leave it out. Every JSON response then carries a `planNotice` and the `X-Repull-Listings-Held-Back` header, so you know there is more to see.  Choose which listings are active with `POST /v1/listings/status`. Upgrading at `https://repull.dev/dashboard/billing` lifts the cap but does not switch held-back listings on: you choose which to activate afterwards, with the same call or from the dashboard, and each one you switch on is billed from then.  Only a call that would itself take you over the plan is refused, with **`402 listings_limit_exceeded`**: activating listings (`PATCH /v1/listings/{id}` with `{ \"active\": true }`, `POST /v1/listings/status`) or creating one (`POST /v1/listings`). Nothing else changes. The envelope carries the tier, the limit, the active count and an upgrade link next to the standard `code` / `message` / `fix` / `docs_url` / `request_id`.  If an account has more active listings than its plan (for example after a downgrade), calls are still served and `planNotice` says so (code \"listings_over_plan\").  ## Failed payments If your plan's renewal payment fails, nothing is blocked: your API calls keep working while Stripe retries the card. Every successful JSON response then carries a `billingNotice` (code `payment_failed`) with the amount, when it failed, the next automatic retry (`nextRetryAt`) and `payUrl`, the invoice to pay. It disappears once the invoice is paid.
 *
 * The version of the OpenAPI document: 1.0.0
 * @generated Generated by: https://openapi-generator.tech
 * Generator version: 7.22.0
 */

/**
 * NOTE: This class is auto generated by OpenAPI Generator (https://openapi-generator.tech).
 * https://openapi-generator.tech
 * Do not edit the class manually.
 */

namespace Repull\Model;

use ArrayAccess;
use JsonSerializable;
use InvalidArgumentException;
use ReturnTypeWillChange;
use Repull\ObjectSerializer;

/**
 * AirbnbReservationDetail Class Doc Comment
 *
 * @description One Airbnb reservation: the unified reservation (the same record as &#x60;GET /v1/reservations/{reservationId}&#x60;, as stored), the connected account and payout, and &#x60;airbnb&#x60; — the row &#x60;GET /v1/channels/airbnb/reservations&#x60; returns for it.
 * @package  Repull
 * @author   OpenAPI Generator team
 * @link     https://openapi-generator.tech
 * @implements ArrayAccess<string, mixed>
 */
class AirbnbReservationDetail implements ModelInterface, ArrayAccess, JsonSerializable
{
    public const DISCRIMINATOR = null;

    /**
     * The original name of the model.
     *
     * @var string
     */
    protected static string $openAPIModelName = 'AirbnbReservationDetail';

    /**
     * Array of property to type mappings. Used for (de)serialization
     *
     * @var array<string, string>
     */
    protected static array $openAPITypes = [
        'id' => 'string',
        'reservation_id' => 'string',
        'confirmation_code' => 'string',
        'listing_id' => 'string',
        'airbnb_listing_id' => 'string',
        'account_id' => 'string',
        'account_name' => 'string',
        'guest_id' => 'string',
        'platform' => 'string',
        'pms_source' => 'string',
        'booking_reservation_number' => 'string',
        'unit_external_id' => 'string',
        'unit_name' => 'string',
        'status' => 'string',
        'payment_status' => 'string',
        'check_in' => '\DateTime',
        'check_out' => '\DateTime',
        'guest_details' => 'array<string,mixed>',
        'guest_name' => 'string',
        'guest_count' => 'int',
        'total_price' => 'string',
        'currency' => 'string',
        'payout' => 'float',
        'payout_currency' => 'string',
        'price_breakdown' => 'array<string,mixed>',
        'custom_fields' => 'array<string,mixed>',
        'cancellation_policy' => 'string',
        'cancellation_reason' => 'string',
        'cancellation_date' => '\DateTime',
        'host_notes' => 'string',
        'guest_notes' => 'string',
        'internal_notes' => 'string',
        'time_zone' => 'string',
        'check_in_time' => 'string',
        'check_out_time' => 'string',
        'customer_id' => 'string',
        'airbnb_host_id' => 'string',
        'booked_at' => '\DateTime',
        'airbnb' => '\Repull\Model\AirbnbReservation'
    ];

    /**
     * Array of property to format mappings. Used for (de)serialization
     *
     * @var array<string, string|null>
     */
    protected static array $openAPIFormats = [
        'id' => null,
        'reservation_id' => null,
        'confirmation_code' => null,
        'listing_id' => null,
        'airbnb_listing_id' => null,
        'account_id' => null,
        'account_name' => null,
        'guest_id' => null,
        'platform' => null,
        'pms_source' => null,
        'booking_reservation_number' => null,
        'unit_external_id' => null,
        'unit_name' => null,
        'status' => null,
        'payment_status' => null,
        'check_in' => 'date',
        'check_out' => 'date',
        'guest_details' => null,
        'guest_name' => null,
        'guest_count' => null,
        'total_price' => null,
        'currency' => null,
        'payout' => null,
        'payout_currency' => null,
        'price_breakdown' => null,
        'custom_fields' => null,
        'cancellation_policy' => null,
        'cancellation_reason' => null,
        'cancellation_date' => 'date-time',
        'host_notes' => null,
        'guest_notes' => null,
        'internal_notes' => null,
        'time_zone' => null,
        'check_in_time' => null,
        'check_out_time' => null,
        'customer_id' => null,
        'airbnb_host_id' => null,
        'booked_at' => 'date-time',
        'airbnb' => null
    ];

    /**
     * Array of nullable properties. Used for (de)serialization
     *
     * @var array<string, bool>
     */
    protected static array $openAPINullables = [
        'id' => false,
        'reservation_id' => false,
        'confirmation_code' => false,
        'listing_id' => false,
        'airbnb_listing_id' => true,
        'account_id' => true,
        'account_name' => true,
        'guest_id' => true,
        'platform' => true,
        'pms_source' => true,
        'booking_reservation_number' => true,
        'unit_external_id' => true,
        'unit_name' => true,
        'status' => false,
        'payment_status' => true,
        'check_in' => false,
        'check_out' => false,
        'guest_details' => true,
        'guest_name' => true,
        'guest_count' => true,
        'total_price' => true,
        'currency' => true,
        'payout' => true,
        'payout_currency' => true,
        'price_breakdown' => true,
        'custom_fields' => true,
        'cancellation_policy' => true,
        'cancellation_reason' => true,
        'cancellation_date' => true,
        'host_notes' => true,
        'guest_notes' => true,
        'internal_notes' => true,
        'time_zone' => true,
        'check_in_time' => true,
        'check_out_time' => true,
        'customer_id' => true,
        'airbnb_host_id' => true,
        'booked_at' => true,
        'airbnb' => true
    ];

    /**
     * If a nullable field gets set to null, insert it here
     *
     * @var array<string, bool>
     */
    protected array $openAPINullablesSetToNull = [];

    /**
     * {@inheritdoc}
     */
    public static function openAPITypes(): array
    {
        return self::$openAPITypes;
    }

    /**
     * {@inheritdoc}
     */
    public static function openAPIFormats(): array
    {
        return self::$openAPIFormats;
    }

    /**
     * Array of nullable properties
     *
     * @return array<string, bool>
     */
    protected static function openAPINullables(): array
    {
        return self::$openAPINullables;
    }

    /**
     * Array of nullable field names deliberately set to null
     *
     * @return array<string, bool>
     */
    private function getOpenAPINullablesSetToNull(): array
    {
        return $this->openAPINullablesSetToNull;
    }

    /**
     * Setter - Array of nullable field names deliberately set to null
     *
     * @param array<string, bool> $openAPINullablesSetToNull
     */
    private function setOpenAPINullablesSetToNull(array $openAPINullablesSetToNull): void
    {
        $this->openAPINullablesSetToNull = $openAPINullablesSetToNull;
    }

    /**
     * {@inheritdoc}
     */
    public static function isNullable(string $property): bool
    {
        return self::openAPINullables()[$property] ?? false;
    }

    /**
     * {@inheritdoc}
     */
    public function isNullableSetToNull(string $property): bool
    {
        return in_array($property, $this->getOpenAPINullablesSetToNull(), true);
    }

    /**
     * Array of attributes where the key is the local name,
     * and the value is the original name
     *
     * @var array<string, string>
     */
    protected static array $attributeMap = [
        'id' => 'id',
        'reservation_id' => 'reservationId',
        'confirmation_code' => 'confirmationCode',
        'listing_id' => 'listingId',
        'airbnb_listing_id' => 'airbnbListingId',
        'account_id' => 'accountId',
        'account_name' => 'accountName',
        'guest_id' => 'guestId',
        'platform' => 'platform',
        'pms_source' => 'pmsSource',
        'booking_reservation_number' => 'bookingReservationNumber',
        'unit_external_id' => 'unitExternalId',
        'unit_name' => 'unitName',
        'status' => 'status',
        'payment_status' => 'paymentStatus',
        'check_in' => 'checkIn',
        'check_out' => 'checkOut',
        'guest_details' => 'guestDetails',
        'guest_name' => 'guestName',
        'guest_count' => 'guestCount',
        'total_price' => 'totalPrice',
        'currency' => 'currency',
        'payout' => 'payout',
        'payout_currency' => 'payoutCurrency',
        'price_breakdown' => 'priceBreakdown',
        'custom_fields' => 'customFields',
        'cancellation_policy' => 'cancellationPolicy',
        'cancellation_reason' => 'cancellationReason',
        'cancellation_date' => 'cancellationDate',
        'host_notes' => 'hostNotes',
        'guest_notes' => 'guestNotes',
        'internal_notes' => 'internalNotes',
        'time_zone' => 'timeZone',
        'check_in_time' => 'checkInTime',
        'check_out_time' => 'checkOutTime',
        'customer_id' => 'customerId',
        'airbnb_host_id' => 'airbnbHostId',
        'booked_at' => 'bookedAt',
        'airbnb' => 'airbnb'
    ];

    /**
     * Array of attributes to setter functions (for deserialization of responses)
     *
     * @var array<string, string>
     */
    protected static array $setters = [
        'id' => 'setId',
        'reservation_id' => 'setReservationId',
        'confirmation_code' => 'setConfirmationCode',
        'listing_id' => 'setListingId',
        'airbnb_listing_id' => 'setAirbnbListingId',
        'account_id' => 'setAccountId',
        'account_name' => 'setAccountName',
        'guest_id' => 'setGuestId',
        'platform' => 'setPlatform',
        'pms_source' => 'setPmsSource',
        'booking_reservation_number' => 'setBookingReservationNumber',
        'unit_external_id' => 'setUnitExternalId',
        'unit_name' => 'setUnitName',
        'status' => 'setStatus',
        'payment_status' => 'setPaymentStatus',
        'check_in' => 'setCheckIn',
        'check_out' => 'setCheckOut',
        'guest_details' => 'setGuestDetails',
        'guest_name' => 'setGuestName',
        'guest_count' => 'setGuestCount',
        'total_price' => 'setTotalPrice',
        'currency' => 'setCurrency',
        'payout' => 'setPayout',
        'payout_currency' => 'setPayoutCurrency',
        'price_breakdown' => 'setPriceBreakdown',
        'custom_fields' => 'setCustomFields',
        'cancellation_policy' => 'setCancellationPolicy',
        'cancellation_reason' => 'setCancellationReason',
        'cancellation_date' => 'setCancellationDate',
        'host_notes' => 'setHostNotes',
        'guest_notes' => 'setGuestNotes',
        'internal_notes' => 'setInternalNotes',
        'time_zone' => 'setTimeZone',
        'check_in_time' => 'setCheckInTime',
        'check_out_time' => 'setCheckOutTime',
        'customer_id' => 'setCustomerId',
        'airbnb_host_id' => 'setAirbnbHostId',
        'booked_at' => 'setBookedAt',
        'airbnb' => 'setAirbnb'
    ];

    /**
     * Array of attributes to getter functions (for serialization of requests)
     *
     * @var array<string, string>
     */
    protected static array $getters = [
        'id' => 'getId',
        'reservation_id' => 'getReservationId',
        'confirmation_code' => 'getConfirmationCode',
        'listing_id' => 'getListingId',
        'airbnb_listing_id' => 'getAirbnbListingId',
        'account_id' => 'getAccountId',
        'account_name' => 'getAccountName',
        'guest_id' => 'getGuestId',
        'platform' => 'getPlatform',
        'pms_source' => 'getPmsSource',
        'booking_reservation_number' => 'getBookingReservationNumber',
        'unit_external_id' => 'getUnitExternalId',
        'unit_name' => 'getUnitName',
        'status' => 'getStatus',
        'payment_status' => 'getPaymentStatus',
        'check_in' => 'getCheckIn',
        'check_out' => 'getCheckOut',
        'guest_details' => 'getGuestDetails',
        'guest_name' => 'getGuestName',
        'guest_count' => 'getGuestCount',
        'total_price' => 'getTotalPrice',
        'currency' => 'getCurrency',
        'payout' => 'getPayout',
        'payout_currency' => 'getPayoutCurrency',
        'price_breakdown' => 'getPriceBreakdown',
        'custom_fields' => 'getCustomFields',
        'cancellation_policy' => 'getCancellationPolicy',
        'cancellation_reason' => 'getCancellationReason',
        'cancellation_date' => 'getCancellationDate',
        'host_notes' => 'getHostNotes',
        'guest_notes' => 'getGuestNotes',
        'internal_notes' => 'getInternalNotes',
        'time_zone' => 'getTimeZone',
        'check_in_time' => 'getCheckInTime',
        'check_out_time' => 'getCheckOutTime',
        'customer_id' => 'getCustomerId',
        'airbnb_host_id' => 'getAirbnbHostId',
        'booked_at' => 'getBookedAt',
        'airbnb' => 'getAirbnb'
    ];

    /**
     * {@inheritdoc}
     */
    public static function attributeMap(): array
    {
        return self::$attributeMap;
    }

    /**
     * {@inheritdoc}
     */
    public static function setters(): array
    {
        return self::$setters;
    }

    /**
     * {@inheritdoc}
     */
    public static function getters(): array
    {
        return self::$getters;
    }

    /**
     * {@inheritdoc}
     */
    public function getModelName(): string
    {
        return self::$openAPIModelName;
    }


    /**
     * Associative array for storing property values
     *
     * @var array
     */
    protected array $container = [];

    /**
     * Constructor
     *
     * @param array $data Associated array of property values initializing the model
     */
    public function __construct(?array $data = null)
    {
        $this->setIfExists('id', $data ?? [], null);
        $this->setIfExists('reservation_id', $data ?? [], null);
        $this->setIfExists('confirmation_code', $data ?? [], null);
        $this->setIfExists('listing_id', $data ?? [], null);
        $this->setIfExists('airbnb_listing_id', $data ?? [], null);
        $this->setIfExists('account_id', $data ?? [], null);
        $this->setIfExists('account_name', $data ?? [], null);
        $this->setIfExists('guest_id', $data ?? [], null);
        $this->setIfExists('platform', $data ?? [], null);
        $this->setIfExists('pms_source', $data ?? [], null);
        $this->setIfExists('booking_reservation_number', $data ?? [], null);
        $this->setIfExists('unit_external_id', $data ?? [], null);
        $this->setIfExists('unit_name', $data ?? [], null);
        $this->setIfExists('status', $data ?? [], null);
        $this->setIfExists('payment_status', $data ?? [], null);
        $this->setIfExists('check_in', $data ?? [], null);
        $this->setIfExists('check_out', $data ?? [], null);
        $this->setIfExists('guest_details', $data ?? [], null);
        $this->setIfExists('guest_name', $data ?? [], null);
        $this->setIfExists('guest_count', $data ?? [], null);
        $this->setIfExists('total_price', $data ?? [], null);
        $this->setIfExists('currency', $data ?? [], null);
        $this->setIfExists('payout', $data ?? [], null);
        $this->setIfExists('payout_currency', $data ?? [], null);
        $this->setIfExists('price_breakdown', $data ?? [], null);
        $this->setIfExists('custom_fields', $data ?? [], null);
        $this->setIfExists('cancellation_policy', $data ?? [], null);
        $this->setIfExists('cancellation_reason', $data ?? [], null);
        $this->setIfExists('cancellation_date', $data ?? [], null);
        $this->setIfExists('host_notes', $data ?? [], null);
        $this->setIfExists('guest_notes', $data ?? [], null);
        $this->setIfExists('internal_notes', $data ?? [], null);
        $this->setIfExists('time_zone', $data ?? [], null);
        $this->setIfExists('check_in_time', $data ?? [], null);
        $this->setIfExists('check_out_time', $data ?? [], null);
        $this->setIfExists('customer_id', $data ?? [], null);
        $this->setIfExists('airbnb_host_id', $data ?? [], null);
        $this->setIfExists('booked_at', $data ?? [], null);
        $this->setIfExists('airbnb', $data ?? [], null);
    }

    /**
     * Sets $this->container[$variableName] to the given data or to the given default Value; if $variableName
     * is nullable and its value is set to null in the $fields array, then mark it as "set to null" in the
     * $this->openAPINullablesSetToNull array
     *
     * @param string $variableName
     * @param array  $fields
     * @param mixed  $defaultValue
     */
    private function setIfExists(string $variableName, array $fields, mixed $defaultValue): void
    {
        if (self::isNullable($variableName) && array_key_exists($variableName, $fields) && is_null($fields[$variableName])) {
            $this->openAPINullablesSetToNull[] = $variableName;
        }

        $this->container[$variableName] = $fields[$variableName] ?? $defaultValue;
    }

    /**
     * {@inheritdoc}
     */
    public function listInvalidProperties(): array
    {
        $invalidProperties = [];

        if ($this->container['id'] === null) {
            $invalidProperties[] = "'id' can't be null";
        }
        if ($this->container['reservation_id'] === null) {
            $invalidProperties[] = "'reservation_id' can't be null";
        }
        if ($this->container['confirmation_code'] === null) {
            $invalidProperties[] = "'confirmation_code' can't be null";
        }
        if ($this->container['listing_id'] === null) {
            $invalidProperties[] = "'listing_id' can't be null";
        }
        if ($this->container['airbnb_listing_id'] === null && !$this->isNullableSetToNull('airbnb_listing_id')) {
            $invalidProperties[] = "'airbnb_listing_id' is required";
        }
        if ($this->container['account_id'] === null && !$this->isNullableSetToNull('account_id')) {
            $invalidProperties[] = "'account_id' is required";
        }
        if ($this->container['account_name'] === null && !$this->isNullableSetToNull('account_name')) {
            $invalidProperties[] = "'account_name' is required";
        }
        if ($this->container['status'] === null) {
            $invalidProperties[] = "'status' can't be null";
        }
        if ($this->container['check_in'] === null) {
            $invalidProperties[] = "'check_in' can't be null";
        }
        if ($this->container['check_out'] === null) {
            $invalidProperties[] = "'check_out' can't be null";
        }
        if ($this->container['guest_name'] === null && !$this->isNullableSetToNull('guest_name')) {
            $invalidProperties[] = "'guest_name' is required";
        }
        if ($this->container['guest_count'] === null && !$this->isNullableSetToNull('guest_count')) {
            $invalidProperties[] = "'guest_count' is required";
        }
        if ($this->container['total_price'] === null && !$this->isNullableSetToNull('total_price')) {
            $invalidProperties[] = "'total_price' is required";
        }
        if ($this->container['currency'] === null && !$this->isNullableSetToNull('currency')) {
            $invalidProperties[] = "'currency' is required";
        }
        if ($this->container['payout'] === null && !$this->isNullableSetToNull('payout')) {
            $invalidProperties[] = "'payout' is required";
        }
        if ($this->container['payout_currency'] === null && !$this->isNullableSetToNull('payout_currency')) {
            $invalidProperties[] = "'payout_currency' is required";
        }
        if ($this->container['airbnb'] === null && !$this->isNullableSetToNull('airbnb')) {
            $invalidProperties[] = "'airbnb' is required";
        }
        return $invalidProperties;
    }

    /**
     * {@inheritdoc}
     */
    public function valid(): bool
    {
        return count($this->listInvalidProperties()) === 0;
    }


    /**
     * Gets id
     *
     * @return string
     */
    public function getId(): string
    {
        return $this->container['id'];
    }

    /**
     * Sets id
     *
     * @param string $id The unified reservation id (same as `reservationId`).
     *
     * @return $this
     */
    public function setId(string $id): static
    {
        if (is_null($id)) {
            throw new InvalidArgumentException('non-nullable id cannot be null');
        }
        $this->container['id'] = $id;

        return $this;
    }

    /**
     * Gets reservation_id
     *
     * @return string
     */
    public function getReservationId(): string
    {
        return $this->container['reservation_id'];
    }

    /**
     * Sets reservation_id
     *
     * @param string $reservation_id The unified reservation id: `GET /v1/reservations/{id}`.
     *
     * @return $this
     */
    public function setReservationId(string $reservation_id): static
    {
        if (is_null($reservation_id)) {
            throw new InvalidArgumentException('non-nullable reservation_id cannot be null');
        }
        $this->container['reservation_id'] = $reservation_id;

        return $this;
    }

    /**
     * Gets confirmation_code
     *
     * @return string
     */
    public function getConfirmationCode(): string
    {
        return $this->container['confirmation_code'];
    }

    /**
     * Sets confirmation_code
     *
     * @param string $confirmation_code confirmation_code
     *
     * @return $this
     */
    public function setConfirmationCode(string $confirmation_code): static
    {
        if (is_null($confirmation_code)) {
            throw new InvalidArgumentException('non-nullable confirmation_code cannot be null');
        }
        $this->container['confirmation_code'] = $confirmation_code;

        return $this;
    }

    /**
     * Gets listing_id
     *
     * @return string
     */
    public function getListingId(): string
    {
        return $this->container['listing_id'];
    }

    /**
     * Sets listing_id
     *
     * @param string $listing_id The Repull listing id.
     *
     * @return $this
     */
    public function setListingId(string $listing_id): static
    {
        if (is_null($listing_id)) {
            throw new InvalidArgumentException('non-nullable listing_id cannot be null');
        }
        $this->container['listing_id'] = $listing_id;

        return $this;
    }

    /**
     * Gets airbnb_listing_id
     *
     * @return string|null
     */
    public function getAirbnbListingId(): ?string
    {
        return $this->container['airbnb_listing_id'];
    }

    /**
     * Sets airbnb_listing_id
     *
     * @param string|null $airbnb_listing_id Airbnb's own listing id.
     *
     * @return $this
     */
    public function setAirbnbListingId(?string $airbnb_listing_id): static
    {
        if (is_null($airbnb_listing_id)) {
            array_push($this->openAPINullablesSetToNull, 'airbnb_listing_id');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('airbnb_listing_id', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['airbnb_listing_id'] = $airbnb_listing_id;

        return $this;
    }

    /**
     * Gets account_id
     *
     * @return string|null
     */
    public function getAccountId(): ?string
    {
        return $this->container['account_id'];
    }

    /**
     * Sets account_id
     *
     * @param string|null $account_id The connected Airbnb account (host id, as a string).
     *
     * @return $this
     */
    public function setAccountId(?string $account_id): static
    {
        if (is_null($account_id)) {
            array_push($this->openAPINullablesSetToNull, 'account_id');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('account_id', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['account_id'] = $account_id;

        return $this;
    }

    /**
     * Gets account_name
     *
     * @return string|null
     */
    public function getAccountName(): ?string
    {
        return $this->container['account_name'];
    }

    /**
     * Sets account_name
     *
     * @param string|null $account_name account_name
     *
     * @return $this
     */
    public function setAccountName(?string $account_name): static
    {
        if (is_null($account_name)) {
            array_push($this->openAPINullablesSetToNull, 'account_name');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('account_name', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['account_name'] = $account_name;

        return $this;
    }

    /**
     * Gets guest_id
     *
     * @return string|null
     */
    public function getGuestId(): ?string
    {
        return $this->container['guest_id'];
    }

    /**
     * Sets guest_id
     *
     * @param string|null $guest_id guest_id
     *
     * @return $this
     */
    public function setGuestId(?string $guest_id): static
    {
        if (is_null($guest_id)) {
            array_push($this->openAPINullablesSetToNull, 'guest_id');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('guest_id', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['guest_id'] = $guest_id;

        return $this;
    }

    /**
     * Gets platform
     *
     * @return string|null
     */
    public function getPlatform(): ?string
    {
        return $this->container['platform'];
    }

    /**
     * Sets platform
     *
     * @param string|null $platform platform
     *
     * @return $this
     */
    public function setPlatform(?string $platform): static
    {
        if (is_null($platform)) {
            array_push($this->openAPINullablesSetToNull, 'platform');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('platform', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['platform'] = $platform;

        return $this;
    }

    /**
     * Gets pms_source
     *
     * @return string|null
     */
    public function getPmsSource(): ?string
    {
        return $this->container['pms_source'];
    }

    /**
     * Sets pms_source
     *
     * @param string|null $pms_source pms_source
     *
     * @return $this
     */
    public function setPmsSource(?string $pms_source): static
    {
        if (is_null($pms_source)) {
            array_push($this->openAPINullablesSetToNull, 'pms_source');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('pms_source', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['pms_source'] = $pms_source;

        return $this;
    }

    /**
     * Gets booking_reservation_number
     *
     * @return string|null
     */
    public function getBookingReservationNumber(): ?string
    {
        return $this->container['booking_reservation_number'];
    }

    /**
     * Sets booking_reservation_number
     *
     * @param string|null $booking_reservation_number booking_reservation_number
     *
     * @return $this
     */
    public function setBookingReservationNumber(?string $booking_reservation_number): static
    {
        if (is_null($booking_reservation_number)) {
            array_push($this->openAPINullablesSetToNull, 'booking_reservation_number');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('booking_reservation_number', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['booking_reservation_number'] = $booking_reservation_number;

        return $this;
    }

    /**
     * Gets unit_external_id
     *
     * @return string|null
     */
    public function getUnitExternalId(): ?string
    {
        return $this->container['unit_external_id'];
    }

    /**
     * Sets unit_external_id
     *
     * @param string|null $unit_external_id unit_external_id
     *
     * @return $this
     */
    public function setUnitExternalId(?string $unit_external_id): static
    {
        if (is_null($unit_external_id)) {
            array_push($this->openAPINullablesSetToNull, 'unit_external_id');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('unit_external_id', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['unit_external_id'] = $unit_external_id;

        return $this;
    }

    /**
     * Gets unit_name
     *
     * @return string|null
     */
    public function getUnitName(): ?string
    {
        return $this->container['unit_name'];
    }

    /**
     * Sets unit_name
     *
     * @param string|null $unit_name unit_name
     *
     * @return $this
     */
    public function setUnitName(?string $unit_name): static
    {
        if (is_null($unit_name)) {
            array_push($this->openAPINullablesSetToNull, 'unit_name');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('unit_name', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['unit_name'] = $unit_name;

        return $this;
    }

    /**
     * Gets status
     *
     * @return string
     */
    public function getStatus(): string
    {
        return $this->container['status'];
    }

    /**
     * Sets status
     *
     * @param string $status Airbnb's status as stored on the reservation (`accept`, `cancelled`, `decline`, `pending_voided`, …). The normalised value is `airbnb.status`.
     *
     * @return $this
     */
    public function setStatus(string $status): static
    {
        if (is_null($status)) {
            throw new InvalidArgumentException('non-nullable status cannot be null');
        }
        $this->container['status'] = $status;

        return $this;
    }

    /**
     * Gets payment_status
     *
     * @return string|null
     */
    public function getPaymentStatus(): ?string
    {
        return $this->container['payment_status'];
    }

    /**
     * Sets payment_status
     *
     * @param string|null $payment_status payment_status
     *
     * @return $this
     */
    public function setPaymentStatus(?string $payment_status): static
    {
        if (is_null($payment_status)) {
            array_push($this->openAPINullablesSetToNull, 'payment_status');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('payment_status', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['payment_status'] = $payment_status;

        return $this;
    }

    /**
     * Gets check_in
     *
     * @return \DateTime
     */
    public function getCheckIn(): \DateTime
    {
        return $this->container['check_in'];
    }

    /**
     * Sets check_in
     *
     * @param \DateTime $check_in check_in
     *
     * @return $this
     */
    public function setCheckIn(\DateTime $check_in): static
    {
        if (is_null($check_in)) {
            throw new InvalidArgumentException('non-nullable check_in cannot be null');
        }
        $this->container['check_in'] = $check_in;

        return $this;
    }

    /**
     * Gets check_out
     *
     * @return \DateTime
     */
    public function getCheckOut(): \DateTime
    {
        return $this->container['check_out'];
    }

    /**
     * Sets check_out
     *
     * @param \DateTime $check_out check_out
     *
     * @return $this
     */
    public function setCheckOut(\DateTime $check_out): static
    {
        if (is_null($check_out)) {
            throw new InvalidArgumentException('non-nullable check_out cannot be null');
        }
        $this->container['check_out'] = $check_out;

        return $this;
    }

    /**
     * Gets guest_details
     *
     * @return array<string,mixed>|null
     */
    public function getGuestDetails(): ?array
    {
        return $this->container['guest_details'];
    }

    /**
     * Sets guest_details
     *
     * @param array<string,mixed>|null $guest_details Guest counts: `numberOfGuests`, `numberOfAdults`, `numberOfChildren`, `numberOfInfants`, `numberOfPets`.
     *
     * @return $this
     */
    public function setGuestDetails(?array $guest_details): static
    {
        if (is_null($guest_details)) {
            array_push($this->openAPINullablesSetToNull, 'guest_details');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('guest_details', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['guest_details'] = $guest_details;

        return $this;
    }

    /**
     * Gets guest_name
     *
     * @return string|null
     */
    public function getGuestName(): ?string
    {
        return $this->container['guest_name'];
    }

    /**
     * Sets guest_name
     *
     * @param string|null $guest_name guest_name
     *
     * @return $this
     */
    public function setGuestName(?string $guest_name): static
    {
        if (is_null($guest_name)) {
            array_push($this->openAPINullablesSetToNull, 'guest_name');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('guest_name', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['guest_name'] = $guest_name;

        return $this;
    }

    /**
     * Gets guest_count
     *
     * @return int|null
     */
    public function getGuestCount(): ?int
    {
        return $this->container['guest_count'];
    }

    /**
     * Sets guest_count
     *
     * @param int|null $guest_count guest_count
     *
     * @return $this
     */
    public function setGuestCount(?int $guest_count): static
    {
        if (is_null($guest_count)) {
            array_push($this->openAPINullablesSetToNull, 'guest_count');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('guest_count', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['guest_count'] = $guest_count;

        return $this;
    }

    /**
     * Gets total_price
     *
     * @return string|null
     */
    public function getTotalPrice(): ?string
    {
        return $this->container['total_price'];
    }

    /**
     * Sets total_price
     *
     * @param string|null $total_price Total as a decimal string, in `currency`.
     *
     * @return $this
     */
    public function setTotalPrice(?string $total_price): static
    {
        if (is_null($total_price)) {
            array_push($this->openAPINullablesSetToNull, 'total_price');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('total_price', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['total_price'] = $total_price;

        return $this;
    }

    /**
     * Gets currency
     *
     * @return string|null
     */
    public function getCurrency(): ?string
    {
        return $this->container['currency'];
    }

    /**
     * Sets currency
     *
     * @param string|null $currency currency
     *
     * @return $this
     */
    public function setCurrency(?string $currency): static
    {
        if (is_null($currency)) {
            array_push($this->openAPINullablesSetToNull, 'currency');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('currency', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['currency'] = $currency;

        return $this;
    }

    /**
     * Gets payout
     *
     * @return float|null
     */
    public function getPayout(): ?float
    {
        return $this->container['payout'];
    }

    /**
     * Sets payout
     *
     * @param float|null $payout What the host is expected to receive, in `payoutCurrency`.
     *
     * @return $this
     */
    public function setPayout(?float $payout): static
    {
        if (is_null($payout)) {
            array_push($this->openAPINullablesSetToNull, 'payout');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('payout', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['payout'] = $payout;

        return $this;
    }

    /**
     * Gets payout_currency
     *
     * @return string|null
     */
    public function getPayoutCurrency(): ?string
    {
        return $this->container['payout_currency'];
    }

    /**
     * Sets payout_currency
     *
     * @param string|null $payout_currency payout_currency
     *
     * @return $this
     */
    public function setPayoutCurrency(?string $payout_currency): static
    {
        if (is_null($payout_currency)) {
            array_push($this->openAPINullablesSetToNull, 'payout_currency');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('payout_currency', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['payout_currency'] = $payout_currency;

        return $this;
    }

    /**
     * Gets price_breakdown
     *
     * @return array<string,mixed>|null
     */
    public function getPriceBreakdown(): ?array
    {
        return $this->container['price_breakdown'];
    }

    /**
     * Sets price_breakdown
     *
     * @param array<string,mixed>|null $price_breakdown Nightly rates, fees, taxes, discounts, `hostPayout`.
     *
     * @return $this
     */
    public function setPriceBreakdown(?array $price_breakdown): static
    {
        if (is_null($price_breakdown)) {
            array_push($this->openAPINullablesSetToNull, 'price_breakdown');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('price_breakdown', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['price_breakdown'] = $price_breakdown;

        return $this;
    }

    /**
     * Gets custom_fields
     *
     * @return array<string,mixed>|null
     */
    public function getCustomFields(): ?array
    {
        return $this->container['custom_fields'];
    }

    /**
     * Sets custom_fields
     *
     * @param array<string,mixed>|null $custom_fields custom_fields
     *
     * @return $this
     */
    public function setCustomFields(?array $custom_fields): static
    {
        if (is_null($custom_fields)) {
            array_push($this->openAPINullablesSetToNull, 'custom_fields');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('custom_fields', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['custom_fields'] = $custom_fields;

        return $this;
    }

    /**
     * Gets cancellation_policy
     *
     * @return string|null
     */
    public function getCancellationPolicy(): ?string
    {
        return $this->container['cancellation_policy'];
    }

    /**
     * Sets cancellation_policy
     *
     * @param string|null $cancellation_policy cancellation_policy
     *
     * @return $this
     */
    public function setCancellationPolicy(?string $cancellation_policy): static
    {
        if (is_null($cancellation_policy)) {
            array_push($this->openAPINullablesSetToNull, 'cancellation_policy');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('cancellation_policy', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['cancellation_policy'] = $cancellation_policy;

        return $this;
    }

    /**
     * Gets cancellation_reason
     *
     * @return string|null
     */
    public function getCancellationReason(): ?string
    {
        return $this->container['cancellation_reason'];
    }

    /**
     * Sets cancellation_reason
     *
     * @param string|null $cancellation_reason cancellation_reason
     *
     * @return $this
     */
    public function setCancellationReason(?string $cancellation_reason): static
    {
        if (is_null($cancellation_reason)) {
            array_push($this->openAPINullablesSetToNull, 'cancellation_reason');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('cancellation_reason', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['cancellation_reason'] = $cancellation_reason;

        return $this;
    }

    /**
     * Gets cancellation_date
     *
     * @return \DateTime|null
     */
    public function getCancellationDate(): ?\DateTime
    {
        return $this->container['cancellation_date'];
    }

    /**
     * Sets cancellation_date
     *
     * @param \DateTime|null $cancellation_date cancellation_date
     *
     * @return $this
     */
    public function setCancellationDate(?\DateTime $cancellation_date): static
    {
        if (is_null($cancellation_date)) {
            array_push($this->openAPINullablesSetToNull, 'cancellation_date');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('cancellation_date', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['cancellation_date'] = $cancellation_date;

        return $this;
    }

    /**
     * Gets host_notes
     *
     * @return string|null
     */
    public function getHostNotes(): ?string
    {
        return $this->container['host_notes'];
    }

    /**
     * Sets host_notes
     *
     * @param string|null $host_notes host_notes
     *
     * @return $this
     */
    public function setHostNotes(?string $host_notes): static
    {
        if (is_null($host_notes)) {
            array_push($this->openAPINullablesSetToNull, 'host_notes');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('host_notes', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['host_notes'] = $host_notes;

        return $this;
    }

    /**
     * Gets guest_notes
     *
     * @return string|null
     */
    public function getGuestNotes(): ?string
    {
        return $this->container['guest_notes'];
    }

    /**
     * Sets guest_notes
     *
     * @param string|null $guest_notes guest_notes
     *
     * @return $this
     */
    public function setGuestNotes(?string $guest_notes): static
    {
        if (is_null($guest_notes)) {
            array_push($this->openAPINullablesSetToNull, 'guest_notes');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('guest_notes', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['guest_notes'] = $guest_notes;

        return $this;
    }

    /**
     * Gets internal_notes
     *
     * @return string|null
     */
    public function getInternalNotes(): ?string
    {
        return $this->container['internal_notes'];
    }

    /**
     * Sets internal_notes
     *
     * @param string|null $internal_notes internal_notes
     *
     * @return $this
     */
    public function setInternalNotes(?string $internal_notes): static
    {
        if (is_null($internal_notes)) {
            array_push($this->openAPINullablesSetToNull, 'internal_notes');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('internal_notes', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['internal_notes'] = $internal_notes;

        return $this;
    }

    /**
     * Gets time_zone
     *
     * @return string|null
     */
    public function getTimeZone(): ?string
    {
        return $this->container['time_zone'];
    }

    /**
     * Sets time_zone
     *
     * @param string|null $time_zone time_zone
     *
     * @return $this
     */
    public function setTimeZone(?string $time_zone): static
    {
        if (is_null($time_zone)) {
            array_push($this->openAPINullablesSetToNull, 'time_zone');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('time_zone', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['time_zone'] = $time_zone;

        return $this;
    }

    /**
     * Gets check_in_time
     *
     * @return string|null
     */
    public function getCheckInTime(): ?string
    {
        return $this->container['check_in_time'];
    }

    /**
     * Sets check_in_time
     *
     * @param string|null $check_in_time check_in_time
     *
     * @return $this
     */
    public function setCheckInTime(?string $check_in_time): static
    {
        if (is_null($check_in_time)) {
            array_push($this->openAPINullablesSetToNull, 'check_in_time');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('check_in_time', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['check_in_time'] = $check_in_time;

        return $this;
    }

    /**
     * Gets check_out_time
     *
     * @return string|null
     */
    public function getCheckOutTime(): ?string
    {
        return $this->container['check_out_time'];
    }

    /**
     * Sets check_out_time
     *
     * @param string|null $check_out_time check_out_time
     *
     * @return $this
     */
    public function setCheckOutTime(?string $check_out_time): static
    {
        if (is_null($check_out_time)) {
            array_push($this->openAPINullablesSetToNull, 'check_out_time');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('check_out_time', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['check_out_time'] = $check_out_time;

        return $this;
    }

    /**
     * Gets customer_id
     *
     * @return string|null
     */
    public function getCustomerId(): ?string
    {
        return $this->container['customer_id'];
    }

    /**
     * Sets customer_id
     *
     * @param string|null $customer_id customer_id
     *
     * @return $this
     */
    public function setCustomerId(?string $customer_id): static
    {
        if (is_null($customer_id)) {
            array_push($this->openAPINullablesSetToNull, 'customer_id');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('customer_id', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['customer_id'] = $customer_id;

        return $this;
    }

    /**
     * Gets airbnb_host_id
     *
     * @return string|null
     * @deprecated
     */
    public function getAirbnbHostId(): ?string
    {
        return $this->container['airbnb_host_id'];
    }

    /**
     * Sets airbnb_host_id
     *
     * @param string|null $airbnb_host_id Use `accountId`.
     *
     * @return $this
     * @deprecated
     */
    public function setAirbnbHostId(?string $airbnb_host_id): static
    {
        if (is_null($airbnb_host_id)) {
            array_push($this->openAPINullablesSetToNull, 'airbnb_host_id');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('airbnb_host_id', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['airbnb_host_id'] = $airbnb_host_id;

        return $this;
    }

    /**
     * Gets booked_at
     *
     * @return \DateTime|null
     */
    public function getBookedAt(): ?\DateTime
    {
        return $this->container['booked_at'];
    }

    /**
     * Sets booked_at
     *
     * @param \DateTime|null $booked_at booked_at
     *
     * @return $this
     */
    public function setBookedAt(?\DateTime $booked_at): static
    {
        if (is_null($booked_at)) {
            array_push($this->openAPINullablesSetToNull, 'booked_at');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('booked_at', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['booked_at'] = $booked_at;

        return $this;
    }

    /**
     * Gets airbnb
     *
     * @return \Repull\Model\AirbnbReservation|null
     */
    public function getAirbnb(): ?\Repull\Model\AirbnbReservation
    {
        return $this->container['airbnb'];
    }

    /**
     * Sets airbnb
     *
     * @param \Repull\Model\AirbnbReservation|null $airbnb The Airbnb record, in the list endpoint's shape. Null only while it is still being imported.
     *
     * @return $this
     */
    public function setAirbnb(?\Repull\Model\AirbnbReservation $airbnb): static
    {
        if (is_null($airbnb)) {
            array_push($this->openAPINullablesSetToNull, 'airbnb');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('airbnb', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['airbnb'] = $airbnb;

        return $this;
    }
    /**
     * Returns true if offset exists. False otherwise.
     *
     * @param int|string $offset Offset
     *
     * @return boolean
     */
    public function offsetExists(mixed $offset): bool
    {
        return isset($this->container[$offset]);
    }

    /**
     * Gets offset.
     *
     * @param int|string $offset Offset
     *
     * @return mixed|null
     */
    #[ReturnTypeWillChange]
    public function offsetGet(mixed $offset): mixed
    {
        return $this->container[$offset] ?? null;
    }

    /**
     * Sets value based on offset.
     *
     * @param int|string|null $offset Offset
     * @param mixed    $value  Value to be set
     *
     * @return void
     */
    public function offsetSet(mixed $offset, mixed $value): void
    {
        if (is_null($offset)) {
            $this->container[] = $value;
        } else {
            $this->container[$offset] = $value;
        }
    }

    /**
     * Unsets offset.
     *
     * @param int|string $offset Offset
     *
     * @return void
     */
    public function offsetUnset(mixed $offset): void
    {
        unset($this->container[$offset]);
    }

    /**
     * Serializes the object to a value that can be serialized natively by json_encode().
     * @link https://www.php.net/manual/en/jsonserializable.jsonserialize.php
     *
     * @return mixed Returns data which can be serialized by json_encode(), which is a value
     * of any type other than a resource.
     */
    #[ReturnTypeWillChange]
    public function jsonSerialize(): mixed
    {
       return ObjectSerializer::sanitizeForSerialization($this);
    }

    /**
     * Gets the string presentation of the object
     *
     * @return string
     */
    public function __toString(): string
    {
        return json_encode(
            ObjectSerializer::sanitizeForSerialization($this),
            JSON_PRETTY_PRINT
        );
    }

    /**
     * Gets a header-safe presentation of the object
     *
     * @return string
     */
    public function toHeaderValue(): string
    {
        return json_encode(ObjectSerializer::sanitizeForSerialization($this));
    }
}


