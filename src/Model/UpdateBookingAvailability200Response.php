<?php
/**
 * UpdateBookingAvailability200Response
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
 * UpdateBookingAvailability200Response Class Doc Comment
 *
 * @package  Repull
 * @author   OpenAPI Generator team
 * @link     https://openapi-generator.tech
 * @implements ArrayAccess<string, mixed>
 */
class UpdateBookingAvailability200Response implements ModelInterface, ArrayAccess, JsonSerializable
{
    public const DISCRIMINATOR = null;

    /**
     * The original name of the model.
     *
     * @var string
     */
    protected static string $openAPIModelName = 'update_booking_availability_200_response';

    /**
     * Array of property to type mappings. Used for (de)serialization
     *
     * @var array<string, string>
     */
    protected static array $openAPITypes = [
        'hotel_id' => 'string',
        'listing_id' => 'string',
        'property_id' => 'string',
        'requested' => 'int',
        'occupancy' => '\Repull\Model\BookingRateWriteOccupancy[]',
        'applied' => 'string',
        'price' => '\Repull\Model\BookingRateWritePriceHalf',
        'restrictions' => '\Repull\Model\BookingRateWriteRestrictionHalf',
        'verification' => '\Repull\Model\BookingDerivedPricingWriteResponseVerification',
        'booking' => 'array<string,mixed>',
        'errors' => 'array<string,mixed>[]',
        'rate_plan_read_error' => 'string',
        'fields' => 'string[]',
        'dates' => '\Repull\Model\BookingRestrictionRequestRow[]',
        'rejection' => '\Repull\Model\BookingUpstreamFailure',
        'rates' => '\Repull\Model\BookingDerivedPricingWriteResponseRatesInner[]'
    ];

    /**
     * Array of property to format mappings. Used for (de)serialization
     *
     * @var array<string, string|null>
     */
    protected static array $openAPIFormats = [
        'hotel_id' => null,
        'listing_id' => null,
        'property_id' => null,
        'requested' => null,
        'occupancy' => null,
        'applied' => null,
        'price' => null,
        'restrictions' => null,
        'verification' => null,
        'booking' => null,
        'errors' => null,
        'rate_plan_read_error' => null,
        'fields' => null,
        'dates' => null,
        'rejection' => null,
        'rates' => null
    ];

    /**
     * Array of nullable properties. Used for (de)serialization
     *
     * @var array<string, bool>
     */
    protected static array $openAPINullables = [
        'hotel_id' => true,
        'listing_id' => true,
        'property_id' => false,
        'requested' => false,
        'occupancy' => false,
        'applied' => false,
        'price' => false,
        'restrictions' => false,
        'verification' => false,
        'booking' => false,
        'errors' => false,
        'rate_plan_read_error' => true,
        'fields' => false,
        'dates' => false,
        'rejection' => false,
        'rates' => false
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
        'hotel_id' => 'hotelId',
        'listing_id' => 'listingId',
        'property_id' => 'propertyId',
        'requested' => 'requested',
        'occupancy' => 'occupancy',
        'applied' => 'applied',
        'price' => 'price',
        'restrictions' => 'restrictions',
        'verification' => 'verification',
        'booking' => 'booking',
        'errors' => 'errors',
        'rate_plan_read_error' => 'ratePlanReadError',
        'fields' => 'fields',
        'dates' => 'dates',
        'rejection' => 'rejection',
        'rates' => 'rates'
    ];

    /**
     * Array of attributes to setter functions (for deserialization of responses)
     *
     * @var array<string, string>
     */
    protected static array $setters = [
        'hotel_id' => 'setHotelId',
        'listing_id' => 'setListingId',
        'property_id' => 'setPropertyId',
        'requested' => 'setRequested',
        'occupancy' => 'setOccupancy',
        'applied' => 'setApplied',
        'price' => 'setPrice',
        'restrictions' => 'setRestrictions',
        'verification' => 'setVerification',
        'booking' => 'setBooking',
        'errors' => 'setErrors',
        'rate_plan_read_error' => 'setRatePlanReadError',
        'fields' => 'setFields',
        'dates' => 'setDates',
        'rejection' => 'setRejection',
        'rates' => 'setRates'
    ];

    /**
     * Array of attributes to getter functions (for serialization of requests)
     *
     * @var array<string, string>
     */
    protected static array $getters = [
        'hotel_id' => 'getHotelId',
        'listing_id' => 'getListingId',
        'property_id' => 'getPropertyId',
        'requested' => 'getRequested',
        'occupancy' => 'getOccupancy',
        'applied' => 'getApplied',
        'price' => 'getPrice',
        'restrictions' => 'getRestrictions',
        'verification' => 'getVerification',
        'booking' => 'getBooking',
        'errors' => 'getErrors',
        'rate_plan_read_error' => 'getRatePlanReadError',
        'fields' => 'getFields',
        'dates' => 'getDates',
        'rejection' => 'getRejection',
        'rates' => 'getRates'
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

    public const APPLIED_VERIFIED = 'verified';
    public const APPLIED_MISMATCH = 'mismatch';
    public const APPLIED_UNVERIFIED = 'unverified';

    /**
     * Gets allowable values of the enum
     *
     * @return string[]
     */
    public static function getAppliedAllowableValues()
    {
        return [
            self::APPLIED_VERIFIED,
            self::APPLIED_MISMATCH,
            self::APPLIED_UNVERIFIED,
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
        $this->setIfExists('hotel_id', $data ?? [], null);
        $this->setIfExists('listing_id', $data ?? [], null);
        $this->setIfExists('property_id', $data ?? [], null);
        $this->setIfExists('requested', $data ?? [], null);
        $this->setIfExists('occupancy', $data ?? [], null);
        $this->setIfExists('applied', $data ?? [], null);
        $this->setIfExists('price', $data ?? [], null);
        $this->setIfExists('restrictions', $data ?? [], null);
        $this->setIfExists('verification', $data ?? [], null);
        $this->setIfExists('booking', $data ?? [], null);
        $this->setIfExists('errors', $data ?? [], null);
        $this->setIfExists('rate_plan_read_error', $data ?? [], null);
        $this->setIfExists('fields', $data ?? [], null);
        $this->setIfExists('dates', $data ?? [], null);
        $this->setIfExists('rejection', $data ?? [], null);
        $this->setIfExists('rates', $data ?? [], null);
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

        $allowedValues = self::getAppliedAllowableValues();
        if (!is_null($this->container['applied']) && !in_array($this->container['applied'], $allowedValues, true)) {
            $invalidProperties[] = sprintf(
                "invalid value '%s' for 'applied', must be one of '%s'",
                $this->container['applied'],
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
     * Gets hotel_id
     *
     * @return string|null
     */
    public function getHotelId(): ?string
    {
        return $this->container['hotel_id'];
    }

    /**
     * Sets hotel_id
     *
     * @param string|null $hotel_id hotel_id
     *
     * @return $this
     */
    public function setHotelId(?string $hotel_id): static
    {
        if (is_null($hotel_id)) {
            array_push($this->openAPINullablesSetToNull, 'hotel_id');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('hotel_id', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['hotel_id'] = $hotel_id;

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
     * @param string|null $listing_id listing_id
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
     * @param string|null $property_id property_id
     *
     * @return $this
     */
    public function setPropertyId(?string $property_id): static
    {
        if (is_null($property_id)) {
            throw new InvalidArgumentException('non-nullable property_id cannot be null');
        }
        $this->container['property_id'] = $property_id;

        return $this;
    }

    /**
     * Gets requested
     *
     * @return int|null
     */
    public function getRequested(): ?int
    {
        return $this->container['requested'];
    }

    /**
     * Sets requested
     *
     * @param int|null $requested requested
     *
     * @return $this
     */
    public function setRequested(?int $requested): static
    {
        if (is_null($requested)) {
            throw new InvalidArgumentException('non-nullable requested cannot be null');
        }
        $this->container['requested'] = $requested;

        return $this;
    }

    /**
     * Gets occupancy
     *
     * @return \Repull\Model\BookingRateWriteOccupancy[]|null
     */
    public function getOccupancy(): ?array
    {
        return $this->container['occupancy'];
    }

    /**
     * Sets occupancy
     *
     * @param \Repull\Model\BookingRateWriteOccupancy[]|null $occupancy occupancy
     *
     * @return $this
     */
    public function setOccupancy(?array $occupancy): static
    {
        if (is_null($occupancy)) {
            throw new InvalidArgumentException('non-nullable occupancy cannot be null');
        }
        $this->container['occupancy'] = $occupancy;

        return $this;
    }

    /**
     * Gets applied
     *
     * @return string|null
     */
    public function getApplied(): ?string
    {
        return $this->container['applied'];
    }

    /**
     * Sets applied
     *
     * @param string|null $applied `verified` — read back, each rate plan holds exactly the rules sent. `mismatch` — read back and they differ (`verification.rows`). `unverified` — `verify: false`, or the read failed. A refusal is `422 booking_rejected`, not a 200.
     *
     * @return $this
     */
    public function setApplied(?string $applied): static
    {
        if (is_null($applied)) {
            throw new InvalidArgumentException('non-nullable applied cannot be null');
        }
        // (relax-enums.php) accept unknown enum values for forward compat
        $this->container['applied'] = $applied;

        return $this;
    }

    /**
     * Gets price
     *
     * @return \Repull\Model\BookingRateWritePriceHalf|null
     */
    public function getPrice(): ?\Repull\Model\BookingRateWritePriceHalf
    {
        return $this->container['price'];
    }

    /**
     * Sets price
     *
     * @param \Repull\Model\BookingRateWritePriceHalf|null $price price
     *
     * @return $this
     */
    public function setPrice(?\Repull\Model\BookingRateWritePriceHalf $price): static
    {
        if (is_null($price)) {
            throw new InvalidArgumentException('non-nullable price cannot be null');
        }
        $this->container['price'] = $price;

        return $this;
    }

    /**
     * Gets restrictions
     *
     * @return \Repull\Model\BookingRateWriteRestrictionHalf|null
     */
    public function getRestrictions(): ?\Repull\Model\BookingRateWriteRestrictionHalf
    {
        return $this->container['restrictions'];
    }

    /**
     * Sets restrictions
     *
     * @param \Repull\Model\BookingRateWriteRestrictionHalf|null $restrictions restrictions
     *
     * @return $this
     */
    public function setRestrictions(?\Repull\Model\BookingRateWriteRestrictionHalf $restrictions): static
    {
        if (is_null($restrictions)) {
            throw new InvalidArgumentException('non-nullable restrictions cannot be null');
        }
        $this->container['restrictions'] = $restrictions;

        return $this;
    }

    /**
     * Gets verification
     *
     * @return \Repull\Model\BookingDerivedPricingWriteResponseVerification|null
     */
    public function getVerification(): ?\Repull\Model\BookingDerivedPricingWriteResponseVerification
    {
        return $this->container['verification'];
    }

    /**
     * Sets verification
     *
     * @param \Repull\Model\BookingDerivedPricingWriteResponseVerification|null $verification verification
     *
     * @return $this
     */
    public function setVerification(?\Repull\Model\BookingDerivedPricingWriteResponseVerification $verification): static
    {
        if (is_null($verification)) {
            throw new InvalidArgumentException('non-nullable verification cannot be null');
        }
        $this->container['verification'] = $verification;

        return $this;
    }

    /**
     * Gets booking
     *
     * @return array<string,mixed>|null
     */
    public function getBooking(): ?array
    {
        return $this->container['booking'];
    }

    /**
     * Sets booking
     *
     * @param array<string,mixed>|null $booking Booking.com's answer, verbatim.
     *
     * @return $this
     */
    public function setBooking(?array $booking): static
    {
        if (is_null($booking)) {
            throw new InvalidArgumentException('non-nullable booking cannot be null');
        }
        $this->container['booking'] = $booking;

        return $this;
    }

    /**
     * Gets errors
     *
     * @return array<string,mixed>[]|null
     */
    public function getErrors(): ?array
    {
        return $this->container['errors'];
    }

    /**
     * Sets errors
     *
     * @param array<string,mixed>[]|null $errors errors
     *
     * @return $this
     */
    public function setErrors(?array $errors): static
    {
        if (is_null($errors)) {
            throw new InvalidArgumentException('non-nullable errors cannot be null');
        }
        $this->container['errors'] = $errors;

        return $this;
    }

    /**
     * Gets rate_plan_read_error
     *
     * @return string|null
     */
    public function getRatePlanReadError(): ?string
    {
        return $this->container['rate_plan_read_error'];
    }

    /**
     * Sets rate_plan_read_error
     *
     * @param string|null $rate_plan_read_error Present when Booking.com's rate-plan read did not complete, so an occupancy fell back to the room definition.
     *
     * @return $this
     */
    public function setRatePlanReadError(?string $rate_plan_read_error): static
    {
        if (is_null($rate_plan_read_error)) {
            array_push($this->openAPINullablesSetToNull, 'rate_plan_read_error');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('rate_plan_read_error', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['rate_plan_read_error'] = $rate_plan_read_error;

        return $this;
    }

    /**
     * Gets fields
     *
     * @return string[]|null
     */
    public function getFields(): ?array
    {
        return $this->container['fields'];
    }

    /**
     * Sets fields
     *
     * @param string[]|null $fields fields
     *
     * @return $this
     */
    public function setFields(?array $fields): static
    {
        if (is_null($fields)) {
            throw new InvalidArgumentException('non-nullable fields cannot be null');
        }
        $this->container['fields'] = $fields;

        return $this;
    }

    /**
     * Gets dates
     *
     * @return \Repull\Model\BookingRestrictionRequestRow[]|null
     */
    public function getDates(): ?array
    {
        return $this->container['dates'];
    }

    /**
     * Sets dates
     *
     * @param \Repull\Model\BookingRestrictionRequestRow[]|null $dates dates
     *
     * @return $this
     */
    public function setDates(?array $dates): static
    {
        if (is_null($dates)) {
            throw new InvalidArgumentException('non-nullable dates cannot be null');
        }
        $this->container['dates'] = $dates;

        return $this;
    }

    /**
     * Gets rejection
     *
     * @return \Repull\Model\BookingUpstreamFailure|null
     */
    public function getRejection(): ?\Repull\Model\BookingUpstreamFailure
    {
        return $this->container['rejection'];
    }

    /**
     * Sets rejection
     *
     * @param \Repull\Model\BookingUpstreamFailure|null $rejection rejection
     *
     * @return $this
     */
    public function setRejection(?\Repull\Model\BookingUpstreamFailure $rejection): static
    {
        if (is_null($rejection)) {
            throw new InvalidArgumentException('non-nullable rejection cannot be null');
        }
        $this->container['rejection'] = $rejection;

        return $this;
    }

    /**
     * Gets rates
     *
     * @return \Repull\Model\BookingDerivedPricingWriteResponseRatesInner[]|null
     */
    public function getRates(): ?array
    {
        return $this->container['rates'];
    }

    /**
     * Sets rates
     *
     * @param \Repull\Model\BookingDerivedPricingWriteResponseRatesInner[]|null $rates Per rate plan, the rules it holds now (as read back; as sent when unverified).
     *
     * @return $this
     */
    public function setRates(?array $rates): static
    {
        if (is_null($rates)) {
            throw new InvalidArgumentException('non-nullable rates cannot be null');
        }
        $this->container['rates'] = $rates;

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


