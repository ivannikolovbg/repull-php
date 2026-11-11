<?php
/**
 * ListBookingReservations200Response
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
 * The unified API for vacation rental tech. Connect Airbnb, Booking.com, Vrbo and more channels, plus 14 PMS platforms, through one REST API.  ## Designed for AI agents Every error response on this API includes machine-parseable fields so an LLM (Claude in MCP, Cursor, Cline, GPT, etc.) can self-recover without escalating to a human: - `error.code` — stable string identifier (e.g. `invalid_params`, `rate_limit_exceeded`) - `error.message` — human-readable cause - `error.fix` — exact recovery steps (e.g. \"Pass `check_in_after` as ISO 8601: `?check_in_after=2026-01-15`\") - `error.docs_url` — link to the canonical write-up at `https://repull.dev/docs/errors/{code}` - `error.request_id` — id to correlate with server-side logs - `error.field` / `error.value_received` / `error.valid_values` / `error.did_you_mean` — when the error is parameter-specific - `error.retry_after` — seconds to wait before retrying (rate-limit + transient upstream)  `Access-Control-Expose-Headers` lists `x-request-id` and the `X-RateLimit-*` family so browsers can read them on cross-origin responses.  ## Quick Start 1. Get an API key at https://repull.dev/dashboard 2. Connect a PMS: `POST /v1/connect/{provider}` 3. List properties: `GET /v1/properties` 4. Get reservations: `GET /v1/reservations`  ## Authentication All requests require a Bearer token: ``` Authorization: Bearer sk_live_YOUR_API_KEY ```  ## Request Correlation (X-Request-ID) Every response carries an `X-Request-ID` header, e.g. `X-Request-ID: req_01HXY...`. Include this id in support tickets and bug reports — we can trace the full request lifecycle (auth, rate limit, handler, downstream calls, log row) from a single id.  You may set the header on the inbound request to forward your own trace id; we will echo it back instead of generating a new one. Accepted format: `^[\\\\w.-]{1,128}$`.  The id is also embedded in error envelopes as `request_id` so server-side log diffs work even when the response headers are stripped by an intermediate proxy.  ## Rate Limits The public API enforces a per-API-key sliding-window rate limit on top of the per-tier monthly + daily-AI quotas.  **Default policy:** 600 requests per 60 seconds, per API key. Sliding window — there is no fixed-minute boundary you can burst across.  Every response includes:  | Header | Meaning | |---|---| | `X-RateLimit-Limit` | Requests permitted in the current window. | | `X-RateLimit-Remaining` | Requests left in the current window after this call. | | `X-RateLimit-Reset` | Unix epoch (seconds) when the next slot opens. | | `X-RateLimit-Policy` | Machine-readable policy descriptor, e.g. `600;w=60`. | | `Retry-After` | Seconds to wait before retrying. **Only present on 429 responses.** |  **On 429 (rate_limit_exceeded):** the response body matches the standard error envelope with `code: \"rate_limit_exceeded\"`, plus `limit`, `window_seconds`, `retry_after`, and `request_id` fields. SDKs MUST honor `Retry-After` and use exponential backoff with jitter on subsequent retries — never a tight loop.  Recommended backoff: ``` sleep_ms = (Retry-After * 1000) + random(0..250) ```  Monthly + daily-AI tier quotas (`free`, `starter`, `custom`) are enforced separately and also surface as 429s; they include `tier`, `scope`, `limit`, `used` and `resets_at` fields.  **The way past a request quota.** The monthly quota refusal (`429 rate_limit_exceeded` with `scope: \"monthly\"`), `402 credits_exhausted` and `429 daily_limit_exceeded` also carry `upgrade` (the cheapest plan that covers your usage: its exact monthly price for your listings, its request quotas and a `checkout_url` that opens checkout for that price; `plan: \"custom\"` with a `contact_email` when no self-serve plan covers it), `credits` (pay-as-you-go: `usd_per_1000_requests`, `top_up_url`, your `balance_usd`), `resets_at` and `projected_monthly_requests`. The per-minute 429 (`scope: \"per_api_key\"`) carries none of these: wait `Retry-After`.  ## Plan listing limits Each plan includes a number of **active listings**:  | Tier | Active listings | |---|---| | `free` | 3 | | `starter` | no cap: 10 included, then billed per listing | | `custom` | no cap |  Going over the plan never blocks your API calls. Listings beyond the plan are **held back**: they stay connected and keep syncing, but they are not served. A held-back listing answers `403 listing_inactive` with `reason: \"plan_limit\"`, and collection routes leave it out. Every JSON response then carries a `planNotice` and the `X-Repull-Listings-Held-Back` header, so you know there is more to see.  Choose which listings are active with `POST /v1/listings/status`, or upgrade at `https://repull.dev/dashboard/billing` to activate them all.  Only a call that would itself take you over the plan is refused, with **`402 listings_limit_exceeded`**: activating listings (`PATCH /v1/listings/{id}` with `{ \"active\": true }`, `POST /v1/listings/status`) or creating one (`POST /v1/listings`). Nothing else changes. The envelope carries the tier, the limit, the active count and an upgrade link next to the standard `code` / `message` / `fix` / `docs_url` / `request_id`.  If an account has more active listings than its plan (for example after a downgrade), calls are still served and `planNotice` says so (code \"listings_over_plan\").
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
 * ListBookingReservations200Response Class Doc Comment
 *
 * @package  Repull
 * @author   OpenAPI Generator team
 * @link     https://openapi-generator.tech
 * @implements ArrayAccess<string, mixed>
 */
class ListBookingReservations200Response implements ModelInterface, ArrayAccess, JsonSerializable
{
    public const DISCRIMINATOR = null;

    /**
     * The original name of the model.
     *
     * @var string
     */
    protected static string $openAPIModelName = 'list_booking_reservations_200_response';

    /**
     * Array of property to type mappings. Used for (de)serialization
     *
     * @var array<string, string>
     */
    protected static array $openAPITypes = [
        'id' => 'string',
        'status' => 'string',
        'date' => 'string',
        'time' => 'string',
        'customer' => 'array<string,mixed>',
        'room' => '\Repull\Model\BookingReservationRoom[]',
        'total_price' => 'float',
        'currency_code' => 'string',
        'payment_status' => 'string',
        'commission_amount' => 'float',
        'cancellation_deadline' => 'string',
        'special_requests' => 'string[]',
        'company_name' => 'string'
    ];

    /**
     * Array of property to format mappings. Used for (de)serialization
     *
     * @var array<string, string|null>
     */
    protected static array $openAPIFormats = [
        'id' => null,
        'status' => null,
        'date' => null,
        'time' => null,
        'customer' => null,
        'room' => null,
        'total_price' => null,
        'currency_code' => null,
        'payment_status' => null,
        'commission_amount' => null,
        'cancellation_deadline' => null,
        'special_requests' => null,
        'company_name' => null
    ];

    /**
     * Array of nullable properties. Used for (de)serialization
     *
     * @var array<string, bool>
     */
    protected static array $openAPINullables = [
        'id' => false,
        'status' => true,
        'date' => true,
        'time' => true,
        'customer' => false,
        'room' => false,
        'total_price' => true,
        'currency_code' => true,
        'payment_status' => true,
        'commission_amount' => true,
        'cancellation_deadline' => true,
        'special_requests' => true,
        'company_name' => true
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
        'status' => 'status',
        'date' => 'date',
        'time' => 'time',
        'customer' => 'customer',
        'room' => 'room',
        'total_price' => 'total_price',
        'currency_code' => 'currency_code',
        'payment_status' => 'payment_status',
        'commission_amount' => 'commission_amount',
        'cancellation_deadline' => 'cancellation_deadline',
        'special_requests' => 'special_requests',
        'company_name' => 'company_name'
    ];

    /**
     * Array of attributes to setter functions (for deserialization of responses)
     *
     * @var array<string, string>
     */
    protected static array $setters = [
        'id' => 'setId',
        'status' => 'setStatus',
        'date' => 'setDate',
        'time' => 'setTime',
        'customer' => 'setCustomer',
        'room' => 'setRoom',
        'total_price' => 'setTotalPrice',
        'currency_code' => 'setCurrencyCode',
        'payment_status' => 'setPaymentStatus',
        'commission_amount' => 'setCommissionAmount',
        'cancellation_deadline' => 'setCancellationDeadline',
        'special_requests' => 'setSpecialRequests',
        'company_name' => 'setCompanyName'
    ];

    /**
     * Array of attributes to getter functions (for serialization of requests)
     *
     * @var array<string, string>
     */
    protected static array $getters = [
        'id' => 'getId',
        'status' => 'getStatus',
        'date' => 'getDate',
        'time' => 'getTime',
        'customer' => 'getCustomer',
        'room' => 'getRoom',
        'total_price' => 'getTotalPrice',
        'currency_code' => 'getCurrencyCode',
        'payment_status' => 'getPaymentStatus',
        'commission_amount' => 'getCommissionAmount',
        'cancellation_deadline' => 'getCancellationDeadline',
        'special_requests' => 'getSpecialRequests',
        'company_name' => 'getCompanyName'
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

    public const STATUS__NEW = 'new';
    public const STATUS_MODIFIED = 'modified';
    public const STATUS_CANCELLED = 'cancelled';
    public const PAYMENT_STATUS_PENDING = 'pending';
    public const PAYMENT_STATUS_COMPLETED = 'completed';
    public const PAYMENT_STATUS_FAILED = 'failed';

    /**
     * Gets allowable values of the enum
     *
     * @return string[]
     */
    public static function getStatusAllowableValues()
    {
        return [
            self::STATUS__NEW,
            self::STATUS_MODIFIED,
            self::STATUS_CANCELLED,
        ];
    }

    /**
     * Gets allowable values of the enum
     *
     * @return string[]
     */
    public static function getPaymentStatusAllowableValues()
    {
        return [
            self::PAYMENT_STATUS_PENDING,
            self::PAYMENT_STATUS_COMPLETED,
            self::PAYMENT_STATUS_FAILED,
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
        $this->setIfExists('status', $data ?? [], null);
        $this->setIfExists('date', $data ?? [], null);
        $this->setIfExists('time', $data ?? [], null);
        $this->setIfExists('customer', $data ?? [], null);
        $this->setIfExists('room', $data ?? [], null);
        $this->setIfExists('total_price', $data ?? [], null);
        $this->setIfExists('currency_code', $data ?? [], null);
        $this->setIfExists('payment_status', $data ?? [], null);
        $this->setIfExists('commission_amount', $data ?? [], null);
        $this->setIfExists('cancellation_deadline', $data ?? [], null);
        $this->setIfExists('special_requests', $data ?? [], null);
        $this->setIfExists('company_name', $data ?? [], null);
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

        $allowedValues = self::getPaymentStatusAllowableValues();
        if (!is_null($this->container['payment_status']) && !in_array($this->container['payment_status'], $allowedValues, true)) {
            $invalidProperties[] = sprintf(
                "invalid value '%s' for 'payment_status', must be one of '%s'",
                $this->container['payment_status'],
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
     * Gets id
     *
     * @return string|null
     */
    public function getId(): ?string
    {
        return $this->container['id'];
    }

    /**
     * Sets id
     *
     * @param string|null $id Booking.com reservation id.
     *
     * @return $this
     */
    public function setId(?string $id): static
    {
        if (is_null($id)) {
            throw new InvalidArgumentException('non-nullable id cannot be null');
        }
        $this->container['id'] = $id;

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
     * Gets date
     *
     * @return string|null
     */
    public function getDate(): ?string
    {
        return $this->container['date'];
    }

    /**
     * Sets date
     *
     * @param string|null $date Booking date (YYYY-MM-DD).
     *
     * @return $this
     */
    public function setDate(?string $date): static
    {
        if (is_null($date)) {
            array_push($this->openAPINullablesSetToNull, 'date');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('date', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['date'] = $date;

        return $this;
    }

    /**
     * Gets time
     *
     * @return string|null
     */
    public function getTime(): ?string
    {
        return $this->container['time'];
    }

    /**
     * Sets time
     *
     * @param string|null $time Booking time.
     *
     * @return $this
     */
    public function setTime(?string $time): static
    {
        if (is_null($time)) {
            array_push($this->openAPINullablesSetToNull, 'time');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('time', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['time'] = $time;

        return $this;
    }

    /**
     * Gets customer
     *
     * @return array<string,mixed>|null
     */
    public function getCustomer(): ?array
    {
        return $this->container['customer'];
    }

    /**
     * Sets customer
     *
     * @param array<string,mixed>|null $customer Guest / customer block (name, contact, address) in Booking's OTA shape.
     *
     * @return $this
     */
    public function setCustomer(?array $customer): static
    {
        if (is_null($customer)) {
            throw new InvalidArgumentException('non-nullable customer cannot be null');
        }
        $this->container['customer'] = $customer;

        return $this;
    }

    /**
     * Gets room
     *
     * @return \Repull\Model\BookingReservationRoom[]|null
     */
    public function getRoom(): ?array
    {
        return $this->container['room'];
    }

    /**
     * Sets room
     *
     * @param \Repull\Model\BookingReservationRoom[]|null $room Reserved rooms.
     *
     * @return $this
     */
    public function setRoom(?array $room): static
    {
        if (is_null($room)) {
            throw new InvalidArgumentException('non-nullable room cannot be null');
        }
        $this->container['room'] = $room;

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
     * @param float|null $total_price total_price
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
     * Gets currency_code
     *
     * @return string|null
     */
    public function getCurrencyCode(): ?string
    {
        return $this->container['currency_code'];
    }

    /**
     * Sets currency_code
     *
     * @param string|null $currency_code currency_code
     *
     * @return $this
     */
    public function setCurrencyCode(?string $currency_code): static
    {
        if (is_null($currency_code)) {
            array_push($this->openAPINullablesSetToNull, 'currency_code');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('currency_code', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['currency_code'] = $currency_code;

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
        // (relax-enums.php) accept unknown enum values for forward compat
        $this->container['payment_status'] = $payment_status;

        return $this;
    }

    /**
     * Gets commission_amount
     *
     * @return float|null
     */
    public function getCommissionAmount(): ?float
    {
        return $this->container['commission_amount'];
    }

    /**
     * Sets commission_amount
     *
     * @param float|null $commission_amount commission_amount
     *
     * @return $this
     */
    public function setCommissionAmount(?float $commission_amount): static
    {
        if (is_null($commission_amount)) {
            array_push($this->openAPINullablesSetToNull, 'commission_amount');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('commission_amount', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['commission_amount'] = $commission_amount;

        return $this;
    }

    /**
     * Gets cancellation_deadline
     *
     * @return string|null
     */
    public function getCancellationDeadline(): ?string
    {
        return $this->container['cancellation_deadline'];
    }

    /**
     * Sets cancellation_deadline
     *
     * @param string|null $cancellation_deadline cancellation_deadline
     *
     * @return $this
     */
    public function setCancellationDeadline(?string $cancellation_deadline): static
    {
        if (is_null($cancellation_deadline)) {
            array_push($this->openAPINullablesSetToNull, 'cancellation_deadline');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('cancellation_deadline', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['cancellation_deadline'] = $cancellation_deadline;

        return $this;
    }

    /**
     * Gets special_requests
     *
     * @return string[]|null
     */
    public function getSpecialRequests(): ?array
    {
        return $this->container['special_requests'];
    }

    /**
     * Sets special_requests
     *
     * @param string[]|null $special_requests special_requests
     *
     * @return $this
     */
    public function setSpecialRequests(?array $special_requests): static
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
     * Gets company_name
     *
     * @return string|null
     */
    public function getCompanyName(): ?string
    {
        return $this->container['company_name'];
    }

    /**
     * Sets company_name
     *
     * @param string|null $company_name company_name
     *
     * @return $this
     */
    public function setCompanyName(?string $company_name): static
    {
        if (is_null($company_name)) {
            array_push($this->openAPINullablesSetToNull, 'company_name');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('company_name', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['company_name'] = $company_name;

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


