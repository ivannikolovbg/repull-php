<?php
/**
 * TiketReservation
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
 * TiketReservation Class Doc Comment
 *
 * @description One booked room of a tiket.com reservation, as tiket.com delivered it.
 * @package  Repull
 * @author   OpenAPI Generator team
 * @link     https://openapi-generator.tech
 * @implements ArrayAccess<string, mixed>
 */
class TiketReservation implements ModelInterface, ArrayAccess, JsonSerializable
{
    public const DISCRIMINATOR = null;

    /**
     * The original name of the model.
     *
     * @var string
     */
    protected static string $openAPIModelName = 'TiketReservation';

    /**
     * Array of property to type mappings. Used for (de)serialization
     *
     * @var array<string, string>
     */
    protected static array $openAPITypes = [
        'reservation_id' => 'string',
        'confirmation_code' => 'string',
        'tiket_reservation_id' => 'string',
        'previous_tiket_reservation_id' => 'string',
        'room_index' => 'int',
        'property_id' => 'string',
        'room_type_code' => 'string',
        'rate_plan_code' => 'string',
        'rate_plan_name' => 'string',
        'check_in' => '\DateTime',
        'check_out' => '\DateTime',
        'adults' => 'int',
        'children' => 'int',
        'infants' => 'int',
        'guest_name' => 'string',
        'guest_country' => 'string',
        'total_amount' => 'float',
        'net_amount' => 'float',
        'currency' => 'string',
        'payment_type' => 'string',
        'payment_method' => 'string',
        'meal_plan_code' => 'string',
        'non_refundable' => 'bool',
        'cancellation_policy' => 'string',
        'special_requests' => 'string',
        'status' => 'string',
        'created_at' => '\DateTime',
        'updated_at' => '\DateTime'
    ];

    /**
     * Array of property to format mappings. Used for (de)serialization
     *
     * @var array<string, string|null>
     */
    protected static array $openAPIFormats = [
        'reservation_id' => null,
        'confirmation_code' => null,
        'tiket_reservation_id' => null,
        'previous_tiket_reservation_id' => null,
        'room_index' => null,
        'property_id' => null,
        'room_type_code' => null,
        'rate_plan_code' => null,
        'rate_plan_name' => null,
        'check_in' => 'date',
        'check_out' => 'date',
        'adults' => null,
        'children' => null,
        'infants' => null,
        'guest_name' => null,
        'guest_country' => null,
        'total_amount' => 'double',
        'net_amount' => 'double',
        'currency' => null,
        'payment_type' => null,
        'payment_method' => null,
        'meal_plan_code' => null,
        'non_refundable' => null,
        'cancellation_policy' => null,
        'special_requests' => null,
        'status' => null,
        'created_at' => 'date-time',
        'updated_at' => 'date-time'
    ];

    /**
     * Array of nullable properties. Used for (de)serialization
     *
     * @var array<string, bool>
     */
    protected static array $openAPINullables = [
        'reservation_id' => true,
        'confirmation_code' => false,
        'tiket_reservation_id' => false,
        'previous_tiket_reservation_id' => true,
        'room_index' => false,
        'property_id' => true,
        'room_type_code' => true,
        'rate_plan_code' => true,
        'rate_plan_name' => true,
        'check_in' => true,
        'check_out' => true,
        'adults' => false,
        'children' => false,
        'infants' => false,
        'guest_name' => true,
        'guest_country' => true,
        'total_amount' => true,
        'net_amount' => true,
        'currency' => true,
        'payment_type' => true,
        'payment_method' => true,
        'meal_plan_code' => true,
        'non_refundable' => true,
        'cancellation_policy' => true,
        'special_requests' => true,
        'status' => false,
        'created_at' => false,
        'updated_at' => false
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
        'reservation_id' => 'reservationId',
        'confirmation_code' => 'confirmationCode',
        'tiket_reservation_id' => 'tiketReservationId',
        'previous_tiket_reservation_id' => 'previousTiketReservationId',
        'room_index' => 'roomIndex',
        'property_id' => 'propertyId',
        'room_type_code' => 'roomTypeCode',
        'rate_plan_code' => 'ratePlanCode',
        'rate_plan_name' => 'ratePlanName',
        'check_in' => 'checkIn',
        'check_out' => 'checkOut',
        'adults' => 'adults',
        'children' => 'children',
        'infants' => 'infants',
        'guest_name' => 'guestName',
        'guest_country' => 'guestCountry',
        'total_amount' => 'totalAmount',
        'net_amount' => 'netAmount',
        'currency' => 'currency',
        'payment_type' => 'paymentType',
        'payment_method' => 'paymentMethod',
        'meal_plan_code' => 'mealPlanCode',
        'non_refundable' => 'nonRefundable',
        'cancellation_policy' => 'cancellationPolicy',
        'special_requests' => 'specialRequests',
        'status' => 'status',
        'created_at' => 'createdAt',
        'updated_at' => 'updatedAt'
    ];

    /**
     * Array of attributes to setter functions (for deserialization of responses)
     *
     * @var array<string, string>
     */
    protected static array $setters = [
        'reservation_id' => 'setReservationId',
        'confirmation_code' => 'setConfirmationCode',
        'tiket_reservation_id' => 'setTiketReservationId',
        'previous_tiket_reservation_id' => 'setPreviousTiketReservationId',
        'room_index' => 'setRoomIndex',
        'property_id' => 'setPropertyId',
        'room_type_code' => 'setRoomTypeCode',
        'rate_plan_code' => 'setRatePlanCode',
        'rate_plan_name' => 'setRatePlanName',
        'check_in' => 'setCheckIn',
        'check_out' => 'setCheckOut',
        'adults' => 'setAdults',
        'children' => 'setChildren',
        'infants' => 'setInfants',
        'guest_name' => 'setGuestName',
        'guest_country' => 'setGuestCountry',
        'total_amount' => 'setTotalAmount',
        'net_amount' => 'setNetAmount',
        'currency' => 'setCurrency',
        'payment_type' => 'setPaymentType',
        'payment_method' => 'setPaymentMethod',
        'meal_plan_code' => 'setMealPlanCode',
        'non_refundable' => 'setNonRefundable',
        'cancellation_policy' => 'setCancellationPolicy',
        'special_requests' => 'setSpecialRequests',
        'status' => 'setStatus',
        'created_at' => 'setCreatedAt',
        'updated_at' => 'setUpdatedAt'
    ];

    /**
     * Array of attributes to getter functions (for serialization of requests)
     *
     * @var array<string, string>
     */
    protected static array $getters = [
        'reservation_id' => 'getReservationId',
        'confirmation_code' => 'getConfirmationCode',
        'tiket_reservation_id' => 'getTiketReservationId',
        'previous_tiket_reservation_id' => 'getPreviousTiketReservationId',
        'room_index' => 'getRoomIndex',
        'property_id' => 'getPropertyId',
        'room_type_code' => 'getRoomTypeCode',
        'rate_plan_code' => 'getRatePlanCode',
        'rate_plan_name' => 'getRatePlanName',
        'check_in' => 'getCheckIn',
        'check_out' => 'getCheckOut',
        'adults' => 'getAdults',
        'children' => 'getChildren',
        'infants' => 'getInfants',
        'guest_name' => 'getGuestName',
        'guest_country' => 'getGuestCountry',
        'total_amount' => 'getTotalAmount',
        'net_amount' => 'getNetAmount',
        'currency' => 'getCurrency',
        'payment_type' => 'getPaymentType',
        'payment_method' => 'getPaymentMethod',
        'meal_plan_code' => 'getMealPlanCode',
        'non_refundable' => 'getNonRefundable',
        'cancellation_policy' => 'getCancellationPolicy',
        'special_requests' => 'getSpecialRequests',
        'status' => 'getStatus',
        'created_at' => 'getCreatedAt',
        'updated_at' => 'getUpdatedAt'
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

    public const STATUS_CONFIRMED = 'confirmed';
    public const STATUS_CANCELLED = 'cancelled';

    /**
     * Gets allowable values of the enum
     *
     * @return string[]
     */
    public static function getStatusAllowableValues()
    {
        return [
            self::STATUS_CONFIRMED,
            self::STATUS_CANCELLED,
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
        $this->setIfExists('reservation_id', $data ?? [], null);
        $this->setIfExists('confirmation_code', $data ?? [], null);
        $this->setIfExists('tiket_reservation_id', $data ?? [], null);
        $this->setIfExists('previous_tiket_reservation_id', $data ?? [], null);
        $this->setIfExists('room_index', $data ?? [], null);
        $this->setIfExists('property_id', $data ?? [], null);
        $this->setIfExists('room_type_code', $data ?? [], null);
        $this->setIfExists('rate_plan_code', $data ?? [], null);
        $this->setIfExists('rate_plan_name', $data ?? [], null);
        $this->setIfExists('check_in', $data ?? [], null);
        $this->setIfExists('check_out', $data ?? [], null);
        $this->setIfExists('adults', $data ?? [], null);
        $this->setIfExists('children', $data ?? [], null);
        $this->setIfExists('infants', $data ?? [], null);
        $this->setIfExists('guest_name', $data ?? [], null);
        $this->setIfExists('guest_country', $data ?? [], null);
        $this->setIfExists('total_amount', $data ?? [], null);
        $this->setIfExists('net_amount', $data ?? [], null);
        $this->setIfExists('currency', $data ?? [], null);
        $this->setIfExists('payment_type', $data ?? [], null);
        $this->setIfExists('payment_method', $data ?? [], null);
        $this->setIfExists('meal_plan_code', $data ?? [], null);
        $this->setIfExists('non_refundable', $data ?? [], null);
        $this->setIfExists('cancellation_policy', $data ?? [], null);
        $this->setIfExists('special_requests', $data ?? [], null);
        $this->setIfExists('status', $data ?? [], null);
        $this->setIfExists('created_at', $data ?? [], null);
        $this->setIfExists('updated_at', $data ?? [], null);
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

        $allowedValues = self::getStatusAllowableValues();
        if (!is_null($this->container['status']) && !in_array($this->container['status'], $allowedValues, true)) {
            $invalidProperties[] = sprintf(
                "invalid value '%s' for 'status', must be one of '%s'",
                $this->container['status'],
                implode("', '", $allowedValues)
            );
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
     * @param string|null $reservation_id The Repull reservation this room became (`GET /v1/reservations/{id}`). `null` while the room type is not mapped to a listing; mapping it records the booking.
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
     * @param string|null $confirmation_code `<tiketReservationId>` for the first room, `<tiketReservationId>-2`, `-3`, … for the others.
     *
     * @return $this
     */
    public function setConfirmationCode(?string $confirmation_code): static
    {
        if (is_null($confirmation_code)) {
            throw new InvalidArgumentException('non-nullable confirmation_code cannot be null');
        }
        $this->container['confirmation_code'] = $confirmation_code;

        return $this;
    }

    /**
     * Gets tiket_reservation_id
     *
     * @return string|null
     */
    public function getTiketReservationId(): ?string
    {
        return $this->container['tiket_reservation_id'];
    }

    /**
     * Sets tiket_reservation_id
     *
     * @param string|null $tiket_reservation_id tiket.com's id of the whole booking.
     *
     * @return $this
     */
    public function setTiketReservationId(?string $tiket_reservation_id): static
    {
        if (is_null($tiket_reservation_id)) {
            throw new InvalidArgumentException('non-nullable tiket_reservation_id cannot be null');
        }
        $this->container['tiket_reservation_id'] = $tiket_reservation_id;

        return $this;
    }

    /**
     * Gets previous_tiket_reservation_id
     *
     * @return string|null
     */
    public function getPreviousTiketReservationId(): ?string
    {
        return $this->container['previous_tiket_reservation_id'];
    }

    /**
     * Sets previous_tiket_reservation_id
     *
     * @param string|null $previous_tiket_reservation_id Set when this booking replaced an earlier one. tiket.com sends a date change as a cancellation plus a new booking; Repull applies it as a change to the original reservation.
     *
     * @return $this
     */
    public function setPreviousTiketReservationId(?string $previous_tiket_reservation_id): static
    {
        if (is_null($previous_tiket_reservation_id)) {
            array_push($this->openAPINullablesSetToNull, 'previous_tiket_reservation_id');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('previous_tiket_reservation_id', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['previous_tiket_reservation_id'] = $previous_tiket_reservation_id;

        return $this;
    }

    /**
     * Gets room_index
     *
     * @return int|null
     */
    public function getRoomIndex(): ?int
    {
        return $this->container['room_index'];
    }

    /**
     * Sets room_index
     *
     * @param int|null $room_index 1 for the first room of the booking.
     *
     * @return $this
     */
    public function setRoomIndex(?int $room_index): static
    {
        if (is_null($room_index)) {
            throw new InvalidArgumentException('non-nullable room_index cannot be null');
        }
        $this->container['room_index'] = $room_index;

        return $this;
    }

    /**
     * Gets property_id
     *
     * @return string|null
     */
    public function getPropertyId(): ?string
    {
        return $this->container['property_id'];
    }

    /**
     * Sets property_id
     *
     * @param string|null $property_id tiket.com Hotel ID.
     *
     * @return $this
     */
    public function setPropertyId(?string $property_id): static
    {
        if (is_null($property_id)) {
            array_push($this->openAPINullablesSetToNull, 'property_id');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('property_id', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['property_id'] = $property_id;

        return $this;
    }

    /**
     * Gets room_type_code
     *
     * @return string|null
     */
    public function getRoomTypeCode(): ?string
    {
        return $this->container['room_type_code'];
    }

    /**
     * Sets room_type_code
     *
     * @param string|null $room_type_code room_type_code
     *
     * @return $this
     */
    public function setRoomTypeCode(?string $room_type_code): static
    {
        if (is_null($room_type_code)) {
            array_push($this->openAPINullablesSetToNull, 'room_type_code');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('room_type_code', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['room_type_code'] = $room_type_code;

        return $this;
    }

    /**
     * Gets rate_plan_code
     *
     * @return string|null
     */
    public function getRatePlanCode(): ?string
    {
        return $this->container['rate_plan_code'];
    }

    /**
     * Sets rate_plan_code
     *
     * @param string|null $rate_plan_code rate_plan_code
     *
     * @return $this
     */
    public function setRatePlanCode(?string $rate_plan_code): static
    {
        if (is_null($rate_plan_code)) {
            array_push($this->openAPINullablesSetToNull, 'rate_plan_code');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('rate_plan_code', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['rate_plan_code'] = $rate_plan_code;

        return $this;
    }

    /**
     * Gets rate_plan_name
     *
     * @return string|null
     */
    public function getRatePlanName(): ?string
    {
        return $this->container['rate_plan_name'];
    }

    /**
     * Sets rate_plan_name
     *
     * @param string|null $rate_plan_name rate_plan_name
     *
     * @return $this
     */
    public function setRatePlanName(?string $rate_plan_name): static
    {
        if (is_null($rate_plan_name)) {
            array_push($this->openAPINullablesSetToNull, 'rate_plan_name');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('rate_plan_name', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['rate_plan_name'] = $rate_plan_name;

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
     * Gets adults
     *
     * @return int|null
     */
    public function getAdults(): ?int
    {
        return $this->container['adults'];
    }

    /**
     * Sets adults
     *
     * @param int|null $adults adults
     *
     * @return $this
     */
    public function setAdults(?int $adults): static
    {
        if (is_null($adults)) {
            throw new InvalidArgumentException('non-nullable adults cannot be null');
        }
        $this->container['adults'] = $adults;

        return $this;
    }

    /**
     * Gets children
     *
     * @return int|null
     */
    public function getChildren(): ?int
    {
        return $this->container['children'];
    }

    /**
     * Sets children
     *
     * @param int|null $children children
     *
     * @return $this
     */
    public function setChildren(?int $children): static
    {
        if (is_null($children)) {
            throw new InvalidArgumentException('non-nullable children cannot be null');
        }
        $this->container['children'] = $children;

        return $this;
    }

    /**
     * Gets infants
     *
     * @return int|null
     */
    public function getInfants(): ?int
    {
        return $this->container['infants'];
    }

    /**
     * Sets infants
     *
     * @param int|null $infants infants
     *
     * @return $this
     */
    public function setInfants(?int $infants): static
    {
        if (is_null($infants)) {
            throw new InvalidArgumentException('non-nullable infants cannot be null');
        }
        $this->container['infants'] = $infants;

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
     * Gets guest_country
     *
     * @return string|null
     */
    public function getGuestCountry(): ?string
    {
        return $this->container['guest_country'];
    }

    /**
     * Sets guest_country
     *
     * @param string|null $guest_country guest_country
     *
     * @return $this
     */
    public function setGuestCountry(?string $guest_country): static
    {
        if (is_null($guest_country)) {
            array_push($this->openAPINullablesSetToNull, 'guest_country');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('guest_country', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['guest_country'] = $guest_country;

        return $this;
    }

    /**
     * Gets total_amount
     *
     * @return float|null
     */
    public function getTotalAmount(): ?float
    {
        return $this->container['total_amount'];
    }

    /**
     * Sets total_amount
     *
     * @param float|null $total_amount What the guest paid for this room.
     *
     * @return $this
     */
    public function setTotalAmount(?float $total_amount): static
    {
        if (is_null($total_amount)) {
            array_push($this->openAPINullablesSetToNull, 'total_amount');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('total_amount', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['total_amount'] = $total_amount;

        return $this;
    }

    /**
     * Gets net_amount
     *
     * @return float|null
     */
    public function getNetAmount(): ?float
    {
        return $this->container['net_amount'];
    }

    /**
     * Sets net_amount
     *
     * @param float|null $net_amount What tiket.com pays the property for this room.
     *
     * @return $this
     */
    public function setNetAmount(?float $net_amount): static
    {
        if (is_null($net_amount)) {
            array_push($this->openAPINullablesSetToNull, 'net_amount');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('net_amount', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['net_amount'] = $net_amount;

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
     * Gets payment_type
     *
     * @return string|null
     */
    public function getPaymentType(): ?string
    {
        return $this->container['payment_type'];
    }

    /**
     * Sets payment_type
     *
     * @param string|null $payment_type Payment type as tiket.com sent it.
     *
     * @return $this
     */
    public function setPaymentType(?string $payment_type): static
    {
        if (is_null($payment_type)) {
            array_push($this->openAPINullablesSetToNull, 'payment_type');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('payment_type', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['payment_type'] = $payment_type;

        return $this;
    }

    /**
     * Gets payment_method
     *
     * @return string|null
     */
    public function getPaymentMethod(): ?string
    {
        return $this->container['payment_method'];
    }

    /**
     * Sets payment_method
     *
     * @param string|null $payment_method How tiket.com pays the property: `Virtual Credit Card` or `Bank Transfer`. Card details are never returned by the API.
     *
     * @return $this
     */
    public function setPaymentMethod(?string $payment_method): static
    {
        if (is_null($payment_method)) {
            array_push($this->openAPINullablesSetToNull, 'payment_method');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('payment_method', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['payment_method'] = $payment_method;

        return $this;
    }

    /**
     * Gets meal_plan_code
     *
     * @return string|null
     */
    public function getMealPlanCode(): ?string
    {
        return $this->container['meal_plan_code'];
    }

    /**
     * Sets meal_plan_code
     *
     * @param string|null $meal_plan_code meal_plan_code
     *
     * @return $this
     */
    public function setMealPlanCode(?string $meal_plan_code): static
    {
        if (is_null($meal_plan_code)) {
            array_push($this->openAPINullablesSetToNull, 'meal_plan_code');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('meal_plan_code', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['meal_plan_code'] = $meal_plan_code;

        return $this;
    }

    /**
     * Gets non_refundable
     *
     * @return bool|null
     */
    public function getNonRefundable(): ?bool
    {
        return $this->container['non_refundable'];
    }

    /**
     * Sets non_refundable
     *
     * @param bool|null $non_refundable non_refundable
     *
     * @return $this
     */
    public function setNonRefundable(?bool $non_refundable): static
    {
        if (is_null($non_refundable)) {
            array_push($this->openAPINullablesSetToNull, 'non_refundable');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('non_refundable', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['non_refundable'] = $non_refundable;

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
     * @param string|null $cancellation_policy Cancellation policy text as tiket.com sent it.
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
     * Gets special_requests
     *
     * @return string|null
     */
    public function getSpecialRequests(): ?string
    {
        return $this->container['special_requests'];
    }

    /**
     * Sets special_requests
     *
     * @param string|null $special_requests special_requests
     *
     * @return $this
     */
    public function setSpecialRequests(?string $special_requests): static
    {
        if (is_null($special_requests)) {
            array_push($this->openAPINullablesSetToNull, 'special_requests');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('special_requests', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['special_requests'] = $special_requests;

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
     * @param string|null $status status
     *
     * @return $this
     */
    public function setStatus(?string $status): static
    {
        if (is_null($status)) {
            throw new InvalidArgumentException('non-nullable status cannot be null');
        }
        // (relax-enums.php) accept unknown enum values for forward compat
        $this->container['status'] = $status;

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
            throw new InvalidArgumentException('non-nullable created_at cannot be null');
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
            throw new InvalidArgumentException('non-nullable updated_at cannot be null');
        }
        $this->container['updated_at'] = $updated_at;

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


