<?php
/**
 * UpdateVrboListingBookingSettings200ResponseData
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
 * UpdateVrboListingBookingSettings200ResponseData Class Doc Comment
 *
 * @description What Vrbo has stored for the section, read back after the write.
 * @package  Repull
 * @author   OpenAPI Generator team
 * @link     https://openapi-generator.tech
 * @implements ArrayAccess<string, mixed>
 */
class UpdateVrboListingBookingSettings200ResponseData implements ModelInterface, ArrayAccess, JsonSerializable
{
    public const DISCRIMINATOR = null;

    /**
     * The original name of the model.
     *
     * @var string
     */
    protected static string $openAPIModelName = 'updateVrboListingBookingSettings_200_response_data';

    /**
     * Array of property to type mappings. Used for (de)serialization
     *
     * @var array<string, string>
     */
    protected static array $openAPITypes = [
        'booking_type' => 'string',
        'instant_booking_eligible' => 'bool',
        'min_nights' => 'int',
        'min_nights_range' => '\Repull\Model\GetVrboListingBookingSettings200ResponseDataMinNightsRange',
        'min_days_before_booking' => 'int',
        'min_days_before_booking_options' => 'int[]',
        'max_months_before_booking' => 'int',
        'max_months_before_booking_options' => 'int[]',
        'max_length_of_stay' => 'int',
        'access_type' => 'string',
        'access_type_options' => '\Repull\Model\GetVrboListingAmenities200ResponseDataGroupsValueFieldsInnerOptionsInner[]',
        'check_in_time' => 'string',
        'check_out_time' => 'string'
    ];

    /**
     * Array of property to format mappings. Used for (de)serialization
     *
     * @var array<string, string|null>
     */
    protected static array $openAPIFormats = [
        'booking_type' => null,
        'instant_booking_eligible' => null,
        'min_nights' => null,
        'min_nights_range' => null,
        'min_days_before_booking' => null,
        'min_days_before_booking_options' => null,
        'max_months_before_booking' => null,
        'max_months_before_booking_options' => null,
        'max_length_of_stay' => null,
        'access_type' => null,
        'access_type_options' => null,
        'check_in_time' => null,
        'check_out_time' => null
    ];

    /**
     * Array of nullable properties. Used for (de)serialization
     *
     * @var array<string, bool>
     */
    protected static array $openAPINullables = [
        'booking_type' => true,
        'instant_booking_eligible' => true,
        'min_nights' => true,
        'min_nights_range' => false,
        'min_days_before_booking' => true,
        'min_days_before_booking_options' => false,
        'max_months_before_booking' => true,
        'max_months_before_booking_options' => false,
        'max_length_of_stay' => true,
        'access_type' => true,
        'access_type_options' => false,
        'check_in_time' => true,
        'check_out_time' => true
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
        'booking_type' => 'bookingType',
        'instant_booking_eligible' => 'instantBookingEligible',
        'min_nights' => 'minNights',
        'min_nights_range' => 'minNightsRange',
        'min_days_before_booking' => 'minDaysBeforeBooking',
        'min_days_before_booking_options' => 'minDaysBeforeBookingOptions',
        'max_months_before_booking' => 'maxMonthsBeforeBooking',
        'max_months_before_booking_options' => 'maxMonthsBeforeBookingOptions',
        'max_length_of_stay' => 'maxLengthOfStay',
        'access_type' => 'accessType',
        'access_type_options' => 'accessTypeOptions',
        'check_in_time' => 'checkInTime',
        'check_out_time' => 'checkOutTime'
    ];

    /**
     * Array of attributes to setter functions (for deserialization of responses)
     *
     * @var array<string, string>
     */
    protected static array $setters = [
        'booking_type' => 'setBookingType',
        'instant_booking_eligible' => 'setInstantBookingEligible',
        'min_nights' => 'setMinNights',
        'min_nights_range' => 'setMinNightsRange',
        'min_days_before_booking' => 'setMinDaysBeforeBooking',
        'min_days_before_booking_options' => 'setMinDaysBeforeBookingOptions',
        'max_months_before_booking' => 'setMaxMonthsBeforeBooking',
        'max_months_before_booking_options' => 'setMaxMonthsBeforeBookingOptions',
        'max_length_of_stay' => 'setMaxLengthOfStay',
        'access_type' => 'setAccessType',
        'access_type_options' => 'setAccessTypeOptions',
        'check_in_time' => 'setCheckInTime',
        'check_out_time' => 'setCheckOutTime'
    ];

    /**
     * Array of attributes to getter functions (for serialization of requests)
     *
     * @var array<string, string>
     */
    protected static array $getters = [
        'booking_type' => 'getBookingType',
        'instant_booking_eligible' => 'getInstantBookingEligible',
        'min_nights' => 'getMinNights',
        'min_nights_range' => 'getMinNightsRange',
        'min_days_before_booking' => 'getMinDaysBeforeBooking',
        'min_days_before_booking_options' => 'getMinDaysBeforeBookingOptions',
        'max_months_before_booking' => 'getMaxMonthsBeforeBooking',
        'max_months_before_booking_options' => 'getMaxMonthsBeforeBookingOptions',
        'max_length_of_stay' => 'getMaxLengthOfStay',
        'access_type' => 'getAccessType',
        'access_type_options' => 'getAccessTypeOptions',
        'check_in_time' => 'getCheckInTime',
        'check_out_time' => 'getCheckOutTime'
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

    public const BOOKING_TYPE_INSTANT = 'INSTANT';
    public const BOOKING_TYPE_QUOTEHOLD = 'QUOTEHOLD';

    /**
     * Gets allowable values of the enum
     *
     * @return string[]
     */
    public static function getBookingTypeAllowableValues()
    {
        return [
            self::BOOKING_TYPE_INSTANT,
            self::BOOKING_TYPE_QUOTEHOLD,
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
        $this->setIfExists('booking_type', $data ?? [], null);
        $this->setIfExists('instant_booking_eligible', $data ?? [], null);
        $this->setIfExists('min_nights', $data ?? [], null);
        $this->setIfExists('min_nights_range', $data ?? [], null);
        $this->setIfExists('min_days_before_booking', $data ?? [], null);
        $this->setIfExists('min_days_before_booking_options', $data ?? [], null);
        $this->setIfExists('max_months_before_booking', $data ?? [], null);
        $this->setIfExists('max_months_before_booking_options', $data ?? [], null);
        $this->setIfExists('max_length_of_stay', $data ?? [], null);
        $this->setIfExists('access_type', $data ?? [], null);
        $this->setIfExists('access_type_options', $data ?? [], null);
        $this->setIfExists('check_in_time', $data ?? [], null);
        $this->setIfExists('check_out_time', $data ?? [], null);
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

        $allowedValues = self::getBookingTypeAllowableValues();
        if (!is_null($this->container['booking_type']) && !in_array($this->container['booking_type'], $allowedValues, true)) {
            $invalidProperties[] = sprintf(
                "invalid value '%s' for 'booking_type', must be one of '%s'",
                $this->container['booking_type'],
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
     * Gets booking_type
     *
     * @return string|null
     */
    public function getBookingType(): ?string
    {
        return $this->container['booking_type'];
    }

    /**
     * Sets booking_type
     *
     * @param string|null $booking_type booking_type
     *
     * @return $this
     */
    public function setBookingType(?string $booking_type): static
    {
        if (is_null($booking_type)) {
            array_push($this->openAPINullablesSetToNull, 'booking_type');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('booking_type', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        // (relax-enums.php) accept unknown enum values for forward compat
        $this->container['booking_type'] = $booking_type;

        return $this;
    }

    /**
     * Gets instant_booking_eligible
     *
     * @return bool|null
     */
    public function getInstantBookingEligible(): ?bool
    {
        return $this->container['instant_booking_eligible'];
    }

    /**
     * Sets instant_booking_eligible
     *
     * @param bool|null $instant_booking_eligible instant_booking_eligible
     *
     * @return $this
     */
    public function setInstantBookingEligible(?bool $instant_booking_eligible): static
    {
        if (is_null($instant_booking_eligible)) {
            array_push($this->openAPINullablesSetToNull, 'instant_booking_eligible');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('instant_booking_eligible', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['instant_booking_eligible'] = $instant_booking_eligible;

        return $this;
    }

    /**
     * Gets min_nights
     *
     * @return int|null
     */
    public function getMinNights(): ?int
    {
        return $this->container['min_nights'];
    }

    /**
     * Sets min_nights
     *
     * @param int|null $min_nights min_nights
     *
     * @return $this
     */
    public function setMinNights(?int $min_nights): static
    {
        if (is_null($min_nights)) {
            array_push($this->openAPINullablesSetToNull, 'min_nights');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('min_nights', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['min_nights'] = $min_nights;

        return $this;
    }

    /**
     * Gets min_nights_range
     *
     * @return \Repull\Model\GetVrboListingBookingSettings200ResponseDataMinNightsRange|null
     */
    public function getMinNightsRange(): ?\Repull\Model\GetVrboListingBookingSettings200ResponseDataMinNightsRange
    {
        return $this->container['min_nights_range'];
    }

    /**
     * Sets min_nights_range
     *
     * @param \Repull\Model\GetVrboListingBookingSettings200ResponseDataMinNightsRange|null $min_nights_range min_nights_range
     *
     * @return $this
     */
    public function setMinNightsRange(?\Repull\Model\GetVrboListingBookingSettings200ResponseDataMinNightsRange $min_nights_range): static
    {
        if (is_null($min_nights_range)) {
            throw new InvalidArgumentException('non-nullable min_nights_range cannot be null');
        }
        $this->container['min_nights_range'] = $min_nights_range;

        return $this;
    }

    /**
     * Gets min_days_before_booking
     *
     * @return int|null
     */
    public function getMinDaysBeforeBooking(): ?int
    {
        return $this->container['min_days_before_booking'];
    }

    /**
     * Sets min_days_before_booking
     *
     * @param int|null $min_days_before_booking min_days_before_booking
     *
     * @return $this
     */
    public function setMinDaysBeforeBooking(?int $min_days_before_booking): static
    {
        if (is_null($min_days_before_booking)) {
            array_push($this->openAPINullablesSetToNull, 'min_days_before_booking');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('min_days_before_booking', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['min_days_before_booking'] = $min_days_before_booking;

        return $this;
    }

    /**
     * Gets min_days_before_booking_options
     *
     * @return int[]|null
     */
    public function getMinDaysBeforeBookingOptions(): ?array
    {
        return $this->container['min_days_before_booking_options'];
    }

    /**
     * Sets min_days_before_booking_options
     *
     * @param int[]|null $min_days_before_booking_options min_days_before_booking_options
     *
     * @return $this
     */
    public function setMinDaysBeforeBookingOptions(?array $min_days_before_booking_options): static
    {
        if (is_null($min_days_before_booking_options)) {
            throw new InvalidArgumentException('non-nullable min_days_before_booking_options cannot be null');
        }
        $this->container['min_days_before_booking_options'] = $min_days_before_booking_options;

        return $this;
    }

    /**
     * Gets max_months_before_booking
     *
     * @return int|null
     */
    public function getMaxMonthsBeforeBooking(): ?int
    {
        return $this->container['max_months_before_booking'];
    }

    /**
     * Sets max_months_before_booking
     *
     * @param int|null $max_months_before_booking max_months_before_booking
     *
     * @return $this
     */
    public function setMaxMonthsBeforeBooking(?int $max_months_before_booking): static
    {
        if (is_null($max_months_before_booking)) {
            array_push($this->openAPINullablesSetToNull, 'max_months_before_booking');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('max_months_before_booking', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['max_months_before_booking'] = $max_months_before_booking;

        return $this;
    }

    /**
     * Gets max_months_before_booking_options
     *
     * @return int[]|null
     */
    public function getMaxMonthsBeforeBookingOptions(): ?array
    {
        return $this->container['max_months_before_booking_options'];
    }

    /**
     * Sets max_months_before_booking_options
     *
     * @param int[]|null $max_months_before_booking_options max_months_before_booking_options
     *
     * @return $this
     */
    public function setMaxMonthsBeforeBookingOptions(?array $max_months_before_booking_options): static
    {
        if (is_null($max_months_before_booking_options)) {
            throw new InvalidArgumentException('non-nullable max_months_before_booking_options cannot be null');
        }
        $this->container['max_months_before_booking_options'] = $max_months_before_booking_options;

        return $this;
    }

    /**
     * Gets max_length_of_stay
     *
     * @return int|null
     */
    public function getMaxLengthOfStay(): ?int
    {
        return $this->container['max_length_of_stay'];
    }

    /**
     * Sets max_length_of_stay
     *
     * @param int|null $max_length_of_stay max_length_of_stay
     *
     * @return $this
     */
    public function setMaxLengthOfStay(?int $max_length_of_stay): static
    {
        if (is_null($max_length_of_stay)) {
            array_push($this->openAPINullablesSetToNull, 'max_length_of_stay');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('max_length_of_stay', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['max_length_of_stay'] = $max_length_of_stay;

        return $this;
    }

    /**
     * Gets access_type
     *
     * @return string|null
     */
    public function getAccessType(): ?string
    {
        return $this->container['access_type'];
    }

    /**
     * Sets access_type
     *
     * @param string|null $access_type access_type
     *
     * @return $this
     */
    public function setAccessType(?string $access_type): static
    {
        if (is_null($access_type)) {
            array_push($this->openAPINullablesSetToNull, 'access_type');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('access_type', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['access_type'] = $access_type;

        return $this;
    }

    /**
     * Gets access_type_options
     *
     * @return \Repull\Model\GetVrboListingAmenities200ResponseDataGroupsValueFieldsInnerOptionsInner[]|null
     */
    public function getAccessTypeOptions(): ?array
    {
        return $this->container['access_type_options'];
    }

    /**
     * Sets access_type_options
     *
     * @param \Repull\Model\GetVrboListingAmenities200ResponseDataGroupsValueFieldsInnerOptionsInner[]|null $access_type_options access_type_options
     *
     * @return $this
     */
    public function setAccessTypeOptions(?array $access_type_options): static
    {
        if (is_null($access_type_options)) {
            throw new InvalidArgumentException('non-nullable access_type_options cannot be null');
        }
        $this->container['access_type_options'] = $access_type_options;

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


