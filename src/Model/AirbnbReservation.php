<?php
/**
 * AirbnbReservation
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
 * AirbnbReservation Class Doc Comment
 *
 * @description An Airbnb reservation as returned by the channel API. Use &#x60;confirmationCode&#x60; to address it in Airbnb operations, and &#x60;reservationId&#x60; to open the same stay on &#x60;GET /v1/reservations/{id}&#x60;. The fields after &#x60;payoutCurrency&#x60; are the Airbnb record as stored (Airbnb&#39;s raw status, amounts as decimal strings, stay datetimes); they are kept for existing integrations.
 * @package  Repull
 * @author   OpenAPI Generator team
 * @link     https://openapi-generator.tech
 * @implements ArrayAccess<string, mixed>
 */
class AirbnbReservation implements ModelInterface, ArrayAccess, JsonSerializable
{
    public const DISCRIMINATOR = null;

    /**
     * The original name of the model.
     *
     * @var string
     */
    protected static string $openAPIModelName = 'AirbnbReservation';

    /**
     * Array of property to type mappings. Used for (de)serialization
     *
     * @var array<string, string>
     */
    protected static array $openAPITypes = [
        'id' => 'string',
        'confirmation_code' => 'string',
        'reservation_id' => 'string',
        'listing_id' => 'string',
        'airbnb_listing_id' => 'string',
        'account_id' => 'string',
        'account_name' => 'string',
        'status' => 'string',
        'check_in' => '\DateTime',
        'check_out' => '\DateTime',
        'guest_name' => 'string',
        'guest_count' => 'int',
        'total_price' => 'float',
        'currency' => 'string',
        'payout' => 'float',
        'payout_currency' => 'string',
        'listing_airbnb_id' => 'string',
        'airbnb_reservation_id' => 'string',
        'host_id' => 'string',
        'guest_airbnb_id' => 'string',
        'guest_first_name' => 'string',
        'guest_last_name' => 'string',
        'guest_profile_picture' => 'string',
        'airbnb_status' => 'string',
        'cancellation_policy' => 'string',
        'cancellation_reason' => 'string',
        'total_paid_amount' => 'string',
        'total_paid_currency' => 'string',
        'expected_payout_amount' => 'string',
        'expected_payout_currency' => 'string',
        'canceled_at' => '\DateTime',
        'created_at' => '\DateTime',
        'updated_at' => '\DateTime',
        'check_in_datetime' => '\DateTime',
        'check_out_datetime' => '\DateTime',
        'time_zone' => 'string',
        'customer_id' => 'string',
        'guest_id' => 'string'
    ];

    /**
     * Array of property to format mappings. Used for (de)serialization
     *
     * @var array<string, string|null>
     */
    protected static array $openAPIFormats = [
        'id' => null,
        'confirmation_code' => null,
        'reservation_id' => null,
        'listing_id' => null,
        'airbnb_listing_id' => null,
        'account_id' => null,
        'account_name' => null,
        'status' => null,
        'check_in' => 'date',
        'check_out' => 'date',
        'guest_name' => null,
        'guest_count' => null,
        'total_price' => null,
        'currency' => null,
        'payout' => null,
        'payout_currency' => null,
        'listing_airbnb_id' => null,
        'airbnb_reservation_id' => null,
        'host_id' => null,
        'guest_airbnb_id' => null,
        'guest_first_name' => null,
        'guest_last_name' => null,
        'guest_profile_picture' => null,
        'airbnb_status' => null,
        'cancellation_policy' => null,
        'cancellation_reason' => null,
        'total_paid_amount' => null,
        'total_paid_currency' => null,
        'expected_payout_amount' => null,
        'expected_payout_currency' => null,
        'canceled_at' => 'date-time',
        'created_at' => 'date-time',
        'updated_at' => 'date-time',
        'check_in_datetime' => 'date-time',
        'check_out_datetime' => 'date-time',
        'time_zone' => null,
        'customer_id' => null,
        'guest_id' => null
    ];

    /**
     * Array of nullable properties. Used for (de)serialization
     *
     * @var array<string, bool>
     */
    protected static array $openAPINullables = [
        'id' => false,
        'confirmation_code' => true,
        'reservation_id' => true,
        'listing_id' => true,
        'airbnb_listing_id' => true,
        'account_id' => true,
        'account_name' => true,
        'status' => true,
        'check_in' => true,
        'check_out' => true,
        'guest_name' => true,
        'guest_count' => true,
        'total_price' => true,
        'currency' => true,
        'payout' => true,
        'payout_currency' => true,
        'listing_airbnb_id' => true,
        'airbnb_reservation_id' => true,
        'host_id' => true,
        'guest_airbnb_id' => true,
        'guest_first_name' => true,
        'guest_last_name' => true,
        'guest_profile_picture' => true,
        'airbnb_status' => true,
        'cancellation_policy' => true,
        'cancellation_reason' => true,
        'total_paid_amount' => true,
        'total_paid_currency' => true,
        'expected_payout_amount' => true,
        'expected_payout_currency' => true,
        'canceled_at' => true,
        'created_at' => true,
        'updated_at' => true,
        'check_in_datetime' => true,
        'check_out_datetime' => true,
        'time_zone' => true,
        'customer_id' => true,
        'guest_id' => true
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
        'confirmation_code' => 'confirmationCode',
        'reservation_id' => 'reservationId',
        'listing_id' => 'listingId',
        'airbnb_listing_id' => 'airbnbListingId',
        'account_id' => 'accountId',
        'account_name' => 'accountName',
        'status' => 'status',
        'check_in' => 'checkIn',
        'check_out' => 'checkOut',
        'guest_name' => 'guestName',
        'guest_count' => 'guestCount',
        'total_price' => 'totalPrice',
        'currency' => 'currency',
        'payout' => 'payout',
        'payout_currency' => 'payoutCurrency',
        'listing_airbnb_id' => 'listingAirbnbId',
        'airbnb_reservation_id' => 'airbnbReservationId',
        'host_id' => 'hostId',
        'guest_airbnb_id' => 'guestAirbnbId',
        'guest_first_name' => 'guestFirstName',
        'guest_last_name' => 'guestLastName',
        'guest_profile_picture' => 'guestProfilePicture',
        'airbnb_status' => 'airbnbStatus',
        'cancellation_policy' => 'cancellationPolicy',
        'cancellation_reason' => 'cancellationReason',
        'total_paid_amount' => 'totalPaidAmount',
        'total_paid_currency' => 'totalPaidCurrency',
        'expected_payout_amount' => 'expectedPayoutAmount',
        'expected_payout_currency' => 'expectedPayoutCurrency',
        'canceled_at' => 'canceledAt',
        'created_at' => 'createdAt',
        'updated_at' => 'updatedAt',
        'check_in_datetime' => 'checkInDatetime',
        'check_out_datetime' => 'checkOutDatetime',
        'time_zone' => 'timeZone',
        'customer_id' => 'customerId',
        'guest_id' => 'guestId'
    ];

    /**
     * Array of attributes to setter functions (for deserialization of responses)
     *
     * @var array<string, string>
     */
    protected static array $setters = [
        'id' => 'setId',
        'confirmation_code' => 'setConfirmationCode',
        'reservation_id' => 'setReservationId',
        'listing_id' => 'setListingId',
        'airbnb_listing_id' => 'setAirbnbListingId',
        'account_id' => 'setAccountId',
        'account_name' => 'setAccountName',
        'status' => 'setStatus',
        'check_in' => 'setCheckIn',
        'check_out' => 'setCheckOut',
        'guest_name' => 'setGuestName',
        'guest_count' => 'setGuestCount',
        'total_price' => 'setTotalPrice',
        'currency' => 'setCurrency',
        'payout' => 'setPayout',
        'payout_currency' => 'setPayoutCurrency',
        'listing_airbnb_id' => 'setListingAirbnbId',
        'airbnb_reservation_id' => 'setAirbnbReservationId',
        'host_id' => 'setHostId',
        'guest_airbnb_id' => 'setGuestAirbnbId',
        'guest_first_name' => 'setGuestFirstName',
        'guest_last_name' => 'setGuestLastName',
        'guest_profile_picture' => 'setGuestProfilePicture',
        'airbnb_status' => 'setAirbnbStatus',
        'cancellation_policy' => 'setCancellationPolicy',
        'cancellation_reason' => 'setCancellationReason',
        'total_paid_amount' => 'setTotalPaidAmount',
        'total_paid_currency' => 'setTotalPaidCurrency',
        'expected_payout_amount' => 'setExpectedPayoutAmount',
        'expected_payout_currency' => 'setExpectedPayoutCurrency',
        'canceled_at' => 'setCanceledAt',
        'created_at' => 'setCreatedAt',
        'updated_at' => 'setUpdatedAt',
        'check_in_datetime' => 'setCheckInDatetime',
        'check_out_datetime' => 'setCheckOutDatetime',
        'time_zone' => 'setTimeZone',
        'customer_id' => 'setCustomerId',
        'guest_id' => 'setGuestId'
    ];

    /**
     * Array of attributes to getter functions (for serialization of requests)
     *
     * @var array<string, string>
     */
    protected static array $getters = [
        'id' => 'getId',
        'confirmation_code' => 'getConfirmationCode',
        'reservation_id' => 'getReservationId',
        'listing_id' => 'getListingId',
        'airbnb_listing_id' => 'getAirbnbListingId',
        'account_id' => 'getAccountId',
        'account_name' => 'getAccountName',
        'status' => 'getStatus',
        'check_in' => 'getCheckIn',
        'check_out' => 'getCheckOut',
        'guest_name' => 'getGuestName',
        'guest_count' => 'getGuestCount',
        'total_price' => 'getTotalPrice',
        'currency' => 'getCurrency',
        'payout' => 'getPayout',
        'payout_currency' => 'getPayoutCurrency',
        'listing_airbnb_id' => 'getListingAirbnbId',
        'airbnb_reservation_id' => 'getAirbnbReservationId',
        'host_id' => 'getHostId',
        'guest_airbnb_id' => 'getGuestAirbnbId',
        'guest_first_name' => 'getGuestFirstName',
        'guest_last_name' => 'getGuestLastName',
        'guest_profile_picture' => 'getGuestProfilePicture',
        'airbnb_status' => 'getAirbnbStatus',
        'cancellation_policy' => 'getCancellationPolicy',
        'cancellation_reason' => 'getCancellationReason',
        'total_paid_amount' => 'getTotalPaidAmount',
        'total_paid_currency' => 'getTotalPaidCurrency',
        'expected_payout_amount' => 'getExpectedPayoutAmount',
        'expected_payout_currency' => 'getExpectedPayoutCurrency',
        'canceled_at' => 'getCanceledAt',
        'created_at' => 'getCreatedAt',
        'updated_at' => 'getUpdatedAt',
        'check_in_datetime' => 'getCheckInDatetime',
        'check_out_datetime' => 'getCheckOutDatetime',
        'time_zone' => 'getTimeZone',
        'customer_id' => 'getCustomerId',
        'guest_id' => 'getGuestId'
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

    public const STATUS_ACCEPTED = 'accepted';
    public const STATUS_PENDING = 'pending';
    public const STATUS_CANCELLED = 'cancelled';
    public const STATUS_DENIED = 'denied';
    public const STATUS_FAILED_VERIFICATION = 'failed_verification';
    public const STATUS_REQUEST_VOIDED = 'request_voided';
    public const STATUS_EXPIRED = 'expired';
    public const STATUS_ERROR = 'error';

    /**
     * Gets allowable values of the enum
     *
     * @return string[]
     */
    public static function getStatusAllowableValues()
    {
        return [
            self::STATUS_ACCEPTED,
            self::STATUS_PENDING,
            self::STATUS_CANCELLED,
            self::STATUS_DENIED,
            self::STATUS_FAILED_VERIFICATION,
            self::STATUS_REQUEST_VOIDED,
            self::STATUS_EXPIRED,
            self::STATUS_ERROR,
        ];
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
        $this->setIfExists('confirmation_code', $data ?? [], null);
        $this->setIfExists('reservation_id', $data ?? [], null);
        $this->setIfExists('listing_id', $data ?? [], null);
        $this->setIfExists('airbnb_listing_id', $data ?? [], null);
        $this->setIfExists('account_id', $data ?? [], null);
        $this->setIfExists('account_name', $data ?? [], null);
        $this->setIfExists('status', $data ?? [], null);
        $this->setIfExists('check_in', $data ?? [], null);
        $this->setIfExists('check_out', $data ?? [], null);
        $this->setIfExists('guest_name', $data ?? [], null);
        $this->setIfExists('guest_count', $data ?? [], null);
        $this->setIfExists('total_price', $data ?? [], null);
        $this->setIfExists('currency', $data ?? [], null);
        $this->setIfExists('payout', $data ?? [], null);
        $this->setIfExists('payout_currency', $data ?? [], null);
        $this->setIfExists('listing_airbnb_id', $data ?? [], null);
        $this->setIfExists('airbnb_reservation_id', $data ?? [], null);
        $this->setIfExists('host_id', $data ?? [], null);
        $this->setIfExists('guest_airbnb_id', $data ?? [], null);
        $this->setIfExists('guest_first_name', $data ?? [], null);
        $this->setIfExists('guest_last_name', $data ?? [], null);
        $this->setIfExists('guest_profile_picture', $data ?? [], null);
        $this->setIfExists('airbnb_status', $data ?? [], null);
        $this->setIfExists('cancellation_policy', $data ?? [], null);
        $this->setIfExists('cancellation_reason', $data ?? [], null);
        $this->setIfExists('total_paid_amount', $data ?? [], null);
        $this->setIfExists('total_paid_currency', $data ?? [], null);
        $this->setIfExists('expected_payout_amount', $data ?? [], null);
        $this->setIfExists('expected_payout_currency', $data ?? [], null);
        $this->setIfExists('canceled_at', $data ?? [], null);
        $this->setIfExists('created_at', $data ?? [], null);
        $this->setIfExists('updated_at', $data ?? [], null);
        $this->setIfExists('check_in_datetime', $data ?? [], null);
        $this->setIfExists('check_out_datetime', $data ?? [], null);
        $this->setIfExists('time_zone', $data ?? [], null);
        $this->setIfExists('customer_id', $data ?? [], null);
        $this->setIfExists('guest_id', $data ?? [], null);
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
        if ($this->container['confirmation_code'] === null && !$this->isNullableSetToNull('confirmation_code')) {
            $invalidProperties[] = "'confirmation_code' is required";
        }
        if ($this->container['reservation_id'] === null && !$this->isNullableSetToNull('reservation_id')) {
            $invalidProperties[] = "'reservation_id' is required";
        }
        if ($this->container['listing_id'] === null && !$this->isNullableSetToNull('listing_id')) {
            $invalidProperties[] = "'listing_id' is required";
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
        if ($this->container['status'] === null && !$this->isNullableSetToNull('status')) {
            $invalidProperties[] = "'status' is required";
        }
        $allowedValues = self::getStatusAllowableValues();
        if (!is_null($this->container['status']) && !in_array($this->container['status'], $allowedValues, true)) {
            $invalidProperties[] = sprintf(
                "invalid value '%s' for 'status', must be one of '%s'",
                $this->container['status'],
                implode("', '", $allowedValues)
            );
        }

        if ($this->container['check_in'] === null && !$this->isNullableSetToNull('check_in')) {
            $invalidProperties[] = "'check_in' is required";
        }
        if ($this->container['check_out'] === null && !$this->isNullableSetToNull('check_out')) {
            $invalidProperties[] = "'check_out' is required";
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
     * @param string $id This Airbnb record's id in Repull.
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
     * Gets confirmation_code
     *
     * @return string|null
     */
    public function getConfirmationCode(): ?string
    {
        return $this->container['confirmation_code'];
    }

    /**
     * Sets confirmation_code
     *
     * @param string|null $confirmation_code Airbnb confirmation code.
     *
     * @return $this
     */
    public function setConfirmationCode(?string $confirmation_code): static
    {
        if (is_null($confirmation_code)) {
            array_push($this->openAPINullablesSetToNull, 'confirmation_code');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('confirmation_code', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['confirmation_code'] = $confirmation_code;

        return $this;
    }

    /**
     * Gets reservation_id
     *
     * @return string|null
     */
    public function getReservationId(): ?string
    {
        return $this->container['reservation_id'];
    }

    /**
     * Sets reservation_id
     *
     * @param string|null $reservation_id The same stay on the unified reservations API: `GET /v1/reservations/{id}`. Null only while that record is still being created.
     *
     * @return $this
     */
    public function setReservationId(?string $reservation_id): static
    {
        if (is_null($reservation_id)) {
            array_push($this->openAPINullablesSetToNull, 'reservation_id');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('reservation_id', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['reservation_id'] = $reservation_id;

        return $this;
    }

    /**
     * Gets listing_id
     *
     * @return string|null
     */
    public function getListingId(): ?string
    {
        return $this->container['listing_id'];
    }

    /**
     * Sets listing_id
     *
     * @param string|null $listing_id The Repull listing id (`GET /v1/listings/{id}`).
     *
     * @return $this
     */
    public function setListingId(?string $listing_id): static
    {
        if (is_null($listing_id)) {
            array_push($this->openAPINullablesSetToNull, 'listing_id');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('listing_id', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
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
     * @param string|null $airbnb_listing_id Airbnb's own listing id — the value `?listing_id=` takes.
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
     * @param string|null $account_id Which connected Airbnb account this row belongs to — the Airbnb host id, as a string (they exceed 2^53). The same value `?account_id=` accepts and `GET /v1/connect/airbnb` returns as `accounts[].externalAccountId`.
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
     * @param string|null $account_name Display name of that connected Airbnb account.
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
     * Gets status
     *
     * @return string|null
     */
    public function getStatus(): ?string
    {
        return $this->container['status'];
    }

    /**
     * Sets status
     *
     * @param string|null $status Normalised status. Airbnb's own value is in `airbnbStatus`.
     *
     * @return $this
     */
    public function setStatus(?string $status): static
    {
        if (is_null($status)) {
            array_push($this->openAPINullablesSetToNull, 'status');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('status', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        // (relax-enums.php) accept unknown enum values for forward compat
        $this->container['status'] = $status;

        return $this;
    }

    /**
     * Gets check_in
     *
     * @return \DateTime|null
     */
    public function getCheckIn(): ?\DateTime
    {
        return $this->container['check_in'];
    }

    /**
     * Sets check_in
     *
     * @param \DateTime|null $check_in check_in
     *
     * @return $this
     */
    public function setCheckIn(?\DateTime $check_in): static
    {
        if (is_null($check_in)) {
            array_push($this->openAPINullablesSetToNull, 'check_in');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('check_in', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['check_in'] = $check_in;

        return $this;
    }

    /**
     * Gets check_out
     *
     * @return \DateTime|null
     */
    public function getCheckOut(): ?\DateTime
    {
        return $this->container['check_out'];
    }

    /**
     * Sets check_out
     *
     * @param \DateTime|null $check_out check_out
     *
     * @return $this
     */
    public function setCheckOut(?\DateTime $check_out): static
    {
        if (is_null($check_out)) {
            array_push($this->openAPINullablesSetToNull, 'check_out');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('check_out', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['check_out'] = $check_out;

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
     * @return float|null
     */
    public function getTotalPrice(): ?float
    {
        return $this->container['total_price'];
    }

    /**
     * Sets total_price
     *
     * @param float|null $total_price What the guest paid, in `currency`.
     *
     * @return $this
     */
    public function setTotalPrice(?float $total_price): static
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
     * Gets listing_airbnb_id
     *
     * @return string|null
     * @deprecated
     */
    public function getListingAirbnbId(): ?string
    {
        return $this->container['listing_airbnb_id'];
    }

    /**
     * Sets listing_airbnb_id
     *
     * @param string|null $listing_airbnb_id Internal link id of the Airbnb listing. Use `airbnbListingId` or `listingId` instead.
     *
     * @return $this
     * @deprecated
     */
    public function setListingAirbnbId(?string $listing_airbnb_id): static
    {
        if (is_null($listing_airbnb_id)) {
            array_push($this->openAPINullablesSetToNull, 'listing_airbnb_id');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('listing_airbnb_id', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['listing_airbnb_id'] = $listing_airbnb_id;

        return $this;
    }

    /**
     * Gets airbnb_reservation_id
     *
     * @return string|null
     * @deprecated
     */
    public function getAirbnbReservationId(): ?string
    {
        return $this->container['airbnb_reservation_id'];
    }

    /**
     * Sets airbnb_reservation_id
     *
     * @param string|null $airbnb_reservation_id Same as `confirmationCode`.
     *
     * @return $this
     * @deprecated
     */
    public function setAirbnbReservationId(?string $airbnb_reservation_id): static
    {
        if (is_null($airbnb_reservation_id)) {
            array_push($this->openAPINullablesSetToNull, 'airbnb_reservation_id');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('airbnb_reservation_id', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['airbnb_reservation_id'] = $airbnb_reservation_id;

        return $this;
    }

    /**
     * Gets host_id
     *
     * @return string|null
     * @deprecated
     */
    public function getHostId(): ?string
    {
        return $this->container['host_id'];
    }

    /**
     * Sets host_id
     *
     * @param string|null $host_id Same as `accountId`.
     *
     * @return $this
     * @deprecated
     */
    public function setHostId(?string $host_id): static
    {
        if (is_null($host_id)) {
            array_push($this->openAPINullablesSetToNull, 'host_id');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('host_id', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['host_id'] = $host_id;

        return $this;
    }

    /**
     * Gets guest_airbnb_id
     *
     * @return string|null
     */
    public function getGuestAirbnbId(): ?string
    {
        return $this->container['guest_airbnb_id'];
    }

    /**
     * Sets guest_airbnb_id
     *
     * @param string|null $guest_airbnb_id The guest's Airbnb user id.
     *
     * @return $this
     */
    public function setGuestAirbnbId(?string $guest_airbnb_id): static
    {
        if (is_null($guest_airbnb_id)) {
            array_push($this->openAPINullablesSetToNull, 'guest_airbnb_id');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('guest_airbnb_id', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['guest_airbnb_id'] = $guest_airbnb_id;

        return $this;
    }

    /**
     * Gets guest_first_name
     *
     * @return string|null
     */
    public function getGuestFirstName(): ?string
    {
        return $this->container['guest_first_name'];
    }

    /**
     * Sets guest_first_name
     *
     * @param string|null $guest_first_name guest_first_name
     *
     * @return $this
     */
    public function setGuestFirstName(?string $guest_first_name): static
    {
        if (is_null($guest_first_name)) {
            array_push($this->openAPINullablesSetToNull, 'guest_first_name');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('guest_first_name', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['guest_first_name'] = $guest_first_name;

        return $this;
    }

    /**
     * Gets guest_last_name
     *
     * @return string|null
     */
    public function getGuestLastName(): ?string
    {
        return $this->container['guest_last_name'];
    }

    /**
     * Sets guest_last_name
     *
     * @param string|null $guest_last_name guest_last_name
     *
     * @return $this
     */
    public function setGuestLastName(?string $guest_last_name): static
    {
        if (is_null($guest_last_name)) {
            array_push($this->openAPINullablesSetToNull, 'guest_last_name');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('guest_last_name', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['guest_last_name'] = $guest_last_name;

        return $this;
    }

    /**
     * Gets guest_profile_picture
     *
     * @return string|null
     */
    public function getGuestProfilePicture(): ?string
    {
        return $this->container['guest_profile_picture'];
    }

    /**
     * Sets guest_profile_picture
     *
     * @param string|null $guest_profile_picture guest_profile_picture
     *
     * @return $this
     */
    public function setGuestProfilePicture(?string $guest_profile_picture): static
    {
        if (is_null($guest_profile_picture)) {
            array_push($this->openAPINullablesSetToNull, 'guest_profile_picture');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('guest_profile_picture', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['guest_profile_picture'] = $guest_profile_picture;

        return $this;
    }

    /**
     * Gets airbnb_status
     *
     * @return string|null
     */
    public function getAirbnbStatus(): ?string
    {
        return $this->container['airbnb_status'];
    }

    /**
     * Sets airbnb_status
     *
     * @param string|null $airbnb_status Airbnb's raw status: `accept`, `pending`, `cancelled_by_guest`, `cancelled_by_host`, `cancelled_by_admin`, `deny`, `failed_verification`, `pending_voided`, `checkpoint_voided`, `timeout`, …
     *
     * @return $this
     */
    public function setAirbnbStatus(?string $airbnb_status): static
    {
        if (is_null($airbnb_status)) {
            array_push($this->openAPINullablesSetToNull, 'airbnb_status');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('airbnb_status', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['airbnb_status'] = $airbnb_status;

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
     * Gets total_paid_amount
     *
     * @return string|null
     */
    public function getTotalPaidAmount(): ?string
    {
        return $this->container['total_paid_amount'];
    }

    /**
     * Sets total_paid_amount
     *
     * @param string|null $total_paid_amount `totalPrice` as a decimal string.
     *
     * @return $this
     */
    public function setTotalPaidAmount(?string $total_paid_amount): static
    {
        if (is_null($total_paid_amount)) {
            array_push($this->openAPINullablesSetToNull, 'total_paid_amount');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('total_paid_amount', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['total_paid_amount'] = $total_paid_amount;

        return $this;
    }

    /**
     * Gets total_paid_currency
     *
     * @return string|null
     */
    public function getTotalPaidCurrency(): ?string
    {
        return $this->container['total_paid_currency'];
    }

    /**
     * Sets total_paid_currency
     *
     * @param string|null $total_paid_currency total_paid_currency
     *
     * @return $this
     */
    public function setTotalPaidCurrency(?string $total_paid_currency): static
    {
        if (is_null($total_paid_currency)) {
            array_push($this->openAPINullablesSetToNull, 'total_paid_currency');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('total_paid_currency', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['total_paid_currency'] = $total_paid_currency;

        return $this;
    }

    /**
     * Gets expected_payout_amount
     *
     * @return string|null
     */
    public function getExpectedPayoutAmount(): ?string
    {
        return $this->container['expected_payout_amount'];
    }

    /**
     * Sets expected_payout_amount
     *
     * @param string|null $expected_payout_amount `payout` as a decimal string.
     *
     * @return $this
     */
    public function setExpectedPayoutAmount(?string $expected_payout_amount): static
    {
        if (is_null($expected_payout_amount)) {
            array_push($this->openAPINullablesSetToNull, 'expected_payout_amount');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('expected_payout_amount', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['expected_payout_amount'] = $expected_payout_amount;

        return $this;
    }

    /**
     * Gets expected_payout_currency
     *
     * @return string|null
     */
    public function getExpectedPayoutCurrency(): ?string
    {
        return $this->container['expected_payout_currency'];
    }

    /**
     * Sets expected_payout_currency
     *
     * @param string|null $expected_payout_currency expected_payout_currency
     *
     * @return $this
     */
    public function setExpectedPayoutCurrency(?string $expected_payout_currency): static
    {
        if (is_null($expected_payout_currency)) {
            array_push($this->openAPINullablesSetToNull, 'expected_payout_currency');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('expected_payout_currency', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['expected_payout_currency'] = $expected_payout_currency;

        return $this;
    }

    /**
     * Gets canceled_at
     *
     * @return \DateTime|null
     */
    public function getCanceledAt(): ?\DateTime
    {
        return $this->container['canceled_at'];
    }

    /**
     * Sets canceled_at
     *
     * @param \DateTime|null $canceled_at canceled_at
     *
     * @return $this
     */
    public function setCanceledAt(?\DateTime $canceled_at): static
    {
        if (is_null($canceled_at)) {
            array_push($this->openAPINullablesSetToNull, 'canceled_at');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('canceled_at', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['canceled_at'] = $canceled_at;

        return $this;
    }

    /**
     * Gets created_at
     *
     * @return \DateTime|null
     */
    public function getCreatedAt(): ?\DateTime
    {
        return $this->container['created_at'];
    }

    /**
     * Sets created_at
     *
     * @param \DateTime|null $created_at created_at
     *
     * @return $this
     */
    public function setCreatedAt(?\DateTime $created_at): static
    {
        if (is_null($created_at)) {
            array_push($this->openAPINullablesSetToNull, 'created_at');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('created_at', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['created_at'] = $created_at;

        return $this;
    }

    /**
     * Gets updated_at
     *
     * @return \DateTime|null
     */
    public function getUpdatedAt(): ?\DateTime
    {
        return $this->container['updated_at'];
    }

    /**
     * Sets updated_at
     *
     * @param \DateTime|null $updated_at updated_at
     *
     * @return $this
     */
    public function setUpdatedAt(?\DateTime $updated_at): static
    {
        if (is_null($updated_at)) {
            array_push($this->openAPINullablesSetToNull, 'updated_at');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('updated_at', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['updated_at'] = $updated_at;

        return $this;
    }

    /**
     * Gets check_in_datetime
     *
     * @return \DateTime|null
     */
    public function getCheckInDatetime(): ?\DateTime
    {
        return $this->container['check_in_datetime'];
    }

    /**
     * Sets check_in_datetime
     *
     * @param \DateTime|null $check_in_datetime Check-in date at 00:00 UTC. Prefer `checkIn`.
     *
     * @return $this
     */
    public function setCheckInDatetime(?\DateTime $check_in_datetime): static
    {
        if (is_null($check_in_datetime)) {
            array_push($this->openAPINullablesSetToNull, 'check_in_datetime');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('check_in_datetime', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['check_in_datetime'] = $check_in_datetime;

        return $this;
    }

    /**
     * Gets check_out_datetime
     *
     * @return \DateTime|null
     */
    public function getCheckOutDatetime(): ?\DateTime
    {
        return $this->container['check_out_datetime'];
    }

    /**
     * Sets check_out_datetime
     *
     * @param \DateTime|null $check_out_datetime Check-out date at 00:00 UTC. Prefer `checkOut`.
     *
     * @return $this
     */
    public function setCheckOutDatetime(?\DateTime $check_out_datetime): static
    {
        if (is_null($check_out_datetime)) {
            array_push($this->openAPINullablesSetToNull, 'check_out_datetime');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('check_out_datetime', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['check_out_datetime'] = $check_out_datetime;

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
     * @param string|null $customer_id Your workspace id.
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
     * @param string|null $guest_id The guest on `GET /v1/guests/{id}`.
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


