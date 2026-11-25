<?php
/**
 * TiketSetupRequest
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
 * TiketSetupRequest Class Doc Comment
 *
 * @description Fields depend on &#x60;action&#x60;. Every action except &#x60;create-property&#x60;, &#x60;request-activation&#x60; and &#x60;check-activation&#x60; names a property with &#x60;property_id&#x60; or, for a property Repull created that tiket.com has not assigned an id to yet, &#x60;partner_hotel_code&#x60;.
 * @package  Repull
 * @author   OpenAPI Generator team
 * @link     https://openapi-generator.tech
 * @implements ArrayAccess<string, mixed>
 */
class TiketSetupRequest implements ModelInterface, ArrayAccess, JsonSerializable
{
    public const DISCRIMINATOR = null;

    /**
     * The original name of the model.
     *
     * @var string
     */
    protected static string $openAPIModelName = 'TiketSetupRequest';

    /**
     * Array of property to type mappings. Used for (de)serialization
     *
     * @var array<string, string>
     */
    protected static array $openAPITypes = [
        'action' => 'string',
        'property_id' => 'string',
        'partner_hotel_code' => 'string',
        'listing_id' => 'int',
        'hotel_id' => 'string',
        'name' => 'string',
        'currency' => 'string',
        'property_type_id' => 'int',
        'latitude' => 'float',
        'longitude' => 'float',
        'address' => 'string',
        'postal_code' => 'string',
        'country_code' => 'string',
        'state_code' => 'string',
        'company_name' => 'string',
        'company_address' => 'string',
        'star_rating' => 'float',
        'pricing_model' => 'string',
        'description' => 'string',
        'total_rooms' => 'int',
        'floors' => 'int',
        'when_built' => 'int',
        'contacts' => '\Repull\Model\TiketSetupRequestContactsInner[]',
        'quantity' => 'int',
        'max_adults' => 'int',
        'max_children' => 'int',
        'room_ids' => 'string[]',
        'cancellation_policy' => 'string',
        'breakfast' => 'bool',
        'properties' => '\Repull\Model\TiketSetupRequestPropertiesInner[]',
        'rate_plan_ids' => 'string[]',
        'enabled' => 'bool'
    ];

    /**
     * Array of property to format mappings. Used for (de)serialization
     *
     * @var array<string, string|null>
     */
    protected static array $openAPIFormats = [
        'action' => null,
        'property_id' => null,
        'partner_hotel_code' => null,
        'listing_id' => null,
        'hotel_id' => null,
        'name' => null,
        'currency' => null,
        'property_type_id' => null,
        'latitude' => 'double',
        'longitude' => 'double',
        'address' => null,
        'postal_code' => null,
        'country_code' => null,
        'state_code' => null,
        'company_name' => null,
        'company_address' => null,
        'star_rating' => 'double',
        'pricing_model' => null,
        'description' => null,
        'total_rooms' => null,
        'floors' => null,
        'when_built' => null,
        'contacts' => null,
        'quantity' => null,
        'max_adults' => null,
        'max_children' => null,
        'room_ids' => null,
        'cancellation_policy' => null,
        'breakfast' => null,
        'properties' => null,
        'rate_plan_ids' => null,
        'enabled' => null
    ];

    /**
     * Array of nullable properties. Used for (de)serialization
     *
     * @var array<string, bool>
     */
    protected static array $openAPINullables = [
        'action' => false,
        'property_id' => false,
        'partner_hotel_code' => false,
        'listing_id' => false,
        'hotel_id' => false,
        'name' => false,
        'currency' => false,
        'property_type_id' => false,
        'latitude' => false,
        'longitude' => false,
        'address' => false,
        'postal_code' => false,
        'country_code' => false,
        'state_code' => false,
        'company_name' => false,
        'company_address' => false,
        'star_rating' => false,
        'pricing_model' => false,
        'description' => false,
        'total_rooms' => false,
        'floors' => false,
        'when_built' => false,
        'contacts' => false,
        'quantity' => false,
        'max_adults' => false,
        'max_children' => false,
        'room_ids' => false,
        'cancellation_policy' => false,
        'breakfast' => false,
        'properties' => false,
        'rate_plan_ids' => false,
        'enabled' => false
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
        'action' => 'action',
        'property_id' => 'property_id',
        'partner_hotel_code' => 'partner_hotel_code',
        'listing_id' => 'listing_id',
        'hotel_id' => 'hotel_id',
        'name' => 'name',
        'currency' => 'currency',
        'property_type_id' => 'property_type_id',
        'latitude' => 'latitude',
        'longitude' => 'longitude',
        'address' => 'address',
        'postal_code' => 'postal_code',
        'country_code' => 'country_code',
        'state_code' => 'state_code',
        'company_name' => 'company_name',
        'company_address' => 'company_address',
        'star_rating' => 'star_rating',
        'pricing_model' => 'pricing_model',
        'description' => 'description',
        'total_rooms' => 'total_rooms',
        'floors' => 'floors',
        'when_built' => 'when_built',
        'contacts' => 'contacts',
        'quantity' => 'quantity',
        'max_adults' => 'max_adults',
        'max_children' => 'max_children',
        'room_ids' => 'room_ids',
        'cancellation_policy' => 'cancellation_policy',
        'breakfast' => 'breakfast',
        'properties' => 'properties',
        'rate_plan_ids' => 'rate_plan_ids',
        'enabled' => 'enabled'
    ];

    /**
     * Array of attributes to setter functions (for deserialization of responses)
     *
     * @var array<string, string>
     */
    protected static array $setters = [
        'action' => 'setAction',
        'property_id' => 'setPropertyId',
        'partner_hotel_code' => 'setPartnerHotelCode',
        'listing_id' => 'setListingId',
        'hotel_id' => 'setHotelId',
        'name' => 'setName',
        'currency' => 'setCurrency',
        'property_type_id' => 'setPropertyTypeId',
        'latitude' => 'setLatitude',
        'longitude' => 'setLongitude',
        'address' => 'setAddress',
        'postal_code' => 'setPostalCode',
        'country_code' => 'setCountryCode',
        'state_code' => 'setStateCode',
        'company_name' => 'setCompanyName',
        'company_address' => 'setCompanyAddress',
        'star_rating' => 'setStarRating',
        'pricing_model' => 'setPricingModel',
        'description' => 'setDescription',
        'total_rooms' => 'setTotalRooms',
        'floors' => 'setFloors',
        'when_built' => 'setWhenBuilt',
        'contacts' => 'setContacts',
        'quantity' => 'setQuantity',
        'max_adults' => 'setMaxAdults',
        'max_children' => 'setMaxChildren',
        'room_ids' => 'setRoomIds',
        'cancellation_policy' => 'setCancellationPolicy',
        'breakfast' => 'setBreakfast',
        'properties' => 'setProperties',
        'rate_plan_ids' => 'setRatePlanIds',
        'enabled' => 'setEnabled'
    ];

    /**
     * Array of attributes to getter functions (for serialization of requests)
     *
     * @var array<string, string>
     */
    protected static array $getters = [
        'action' => 'getAction',
        'property_id' => 'getPropertyId',
        'partner_hotel_code' => 'getPartnerHotelCode',
        'listing_id' => 'getListingId',
        'hotel_id' => 'getHotelId',
        'name' => 'getName',
        'currency' => 'getCurrency',
        'property_type_id' => 'getPropertyTypeId',
        'latitude' => 'getLatitude',
        'longitude' => 'getLongitude',
        'address' => 'getAddress',
        'postal_code' => 'getPostalCode',
        'country_code' => 'getCountryCode',
        'state_code' => 'getStateCode',
        'company_name' => 'getCompanyName',
        'company_address' => 'getCompanyAddress',
        'star_rating' => 'getStarRating',
        'pricing_model' => 'getPricingModel',
        'description' => 'getDescription',
        'total_rooms' => 'getTotalRooms',
        'floors' => 'getFloors',
        'when_built' => 'getWhenBuilt',
        'contacts' => 'getContacts',
        'quantity' => 'getQuantity',
        'max_adults' => 'getMaxAdults',
        'max_children' => 'getMaxChildren',
        'room_ids' => 'getRoomIds',
        'cancellation_policy' => 'getCancellationPolicy',
        'breakfast' => 'getBreakfast',
        'properties' => 'getProperties',
        'rate_plan_ids' => 'getRatePlanIds',
        'enabled' => 'getEnabled'
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

    public const ACTION_CREATE_PROPERTY = 'create-property';
    public const ACTION_ADD_ROOM = 'add-room';
    public const ACTION_ADD_RATE_PLAN = 'add-rate-plan';
    public const ACTION_REQUEST_ACTIVATION = 'request-activation';
    public const ACTION_REQUEST_REGISTRATION = 'request-registration';
    public const ACTION_SET_TIKET_HOTEL_ID = 'set-tiket-hotel-id';
    public const ACTION_CHECK_ACTIVATION = 'check-activation';
    public const ACTION_REQUEST_CONTENT_MAPPING = 'request-content-mapping';
    public const ACTION_CONFIRM_CONTENT_MAPPING = 'confirm-content-mapping';
    public const ACTION_REFRESH = 'refresh';
    public const ACTION_DISCONNECT = 'disconnect';
    public const ACTION_SET_PUSH_RATE_PLANS = 'set-push-rate-plans';
    public const ACTION_SET_SYNC = 'set-sync';

    /**
     * Gets allowable values of the enum
     *
     * @return string[]
     */
    public static function getActionAllowableValues()
    {
        return [
            self::ACTION_CREATE_PROPERTY,
            self::ACTION_ADD_ROOM,
            self::ACTION_ADD_RATE_PLAN,
            self::ACTION_REQUEST_ACTIVATION,
            self::ACTION_REQUEST_REGISTRATION,
            self::ACTION_SET_TIKET_HOTEL_ID,
            self::ACTION_CHECK_ACTIVATION,
            self::ACTION_REQUEST_CONTENT_MAPPING,
            self::ACTION_CONFIRM_CONTENT_MAPPING,
            self::ACTION_REFRESH,
            self::ACTION_DISCONNECT,
            self::ACTION_SET_PUSH_RATE_PLANS,
            self::ACTION_SET_SYNC,
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
        $this->setIfExists('action', $data ?? [], null);
        $this->setIfExists('property_id', $data ?? [], null);
        $this->setIfExists('partner_hotel_code', $data ?? [], null);
        $this->setIfExists('listing_id', $data ?? [], null);
        $this->setIfExists('hotel_id', $data ?? [], null);
        $this->setIfExists('name', $data ?? [], null);
        $this->setIfExists('currency', $data ?? [], null);
        $this->setIfExists('property_type_id', $data ?? [], null);
        $this->setIfExists('latitude', $data ?? [], null);
        $this->setIfExists('longitude', $data ?? [], null);
        $this->setIfExists('address', $data ?? [], null);
        $this->setIfExists('postal_code', $data ?? [], null);
        $this->setIfExists('country_code', $data ?? [], null);
        $this->setIfExists('state_code', $data ?? [], null);
        $this->setIfExists('company_name', $data ?? [], null);
        $this->setIfExists('company_address', $data ?? [], null);
        $this->setIfExists('star_rating', $data ?? [], null);
        $this->setIfExists('pricing_model', $data ?? [], null);
        $this->setIfExists('description', $data ?? [], null);
        $this->setIfExists('total_rooms', $data ?? [], null);
        $this->setIfExists('floors', $data ?? [], null);
        $this->setIfExists('when_built', $data ?? [], null);
        $this->setIfExists('contacts', $data ?? [], null);
        $this->setIfExists('quantity', $data ?? [], null);
        $this->setIfExists('max_adults', $data ?? [], null);
        $this->setIfExists('max_children', $data ?? [], null);
        $this->setIfExists('room_ids', $data ?? [], null);
        $this->setIfExists('cancellation_policy', $data ?? [], null);
        $this->setIfExists('breakfast', $data ?? [], null);
        $this->setIfExists('properties', $data ?? [], null);
        $this->setIfExists('rate_plan_ids', $data ?? [], null);
        $this->setIfExists('enabled', $data ?? [], null);
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

        if ($this->container['action'] === null) {
            $invalidProperties[] = "'action' can't be null";
        }
        $allowedValues = self::getActionAllowableValues();
        if (!is_null($this->container['action']) && !in_array($this->container['action'], $allowedValues, true)) {
            $invalidProperties[] = sprintf(
                "invalid value '%s' for 'action', must be one of '%s'",
                $this->container['action'],
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
     * Gets action
     *
     * @return string
     */
    public function getAction(): string
    {
        return $this->container['action'];
    }

    /**
     * Sets action
     *
     * @param string $action action
     *
     * @return $this
     */
    public function setAction(string $action): static
    {
        if (is_null($action)) {
            throw new InvalidArgumentException('non-nullable action cannot be null');
        }
        // (relax-enums.php) accept unknown enum values for forward compat
        $this->container['action'] = $action;

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
            throw new InvalidArgumentException('non-nullable property_id cannot be null');
        }
        $this->container['property_id'] = $property_id;

        return $this;
    }

    /**
     * Gets partner_hotel_code
     *
     * @return string|null
     */
    public function getPartnerHotelCode(): ?string
    {
        return $this->container['partner_hotel_code'];
    }

    /**
     * Sets partner_hotel_code
     *
     * @param string|null $partner_hotel_code Repull's code for a property Repull created.
     *
     * @return $this
     */
    public function setPartnerHotelCode(?string $partner_hotel_code): static
    {
        if (is_null($partner_hotel_code)) {
            throw new InvalidArgumentException('non-nullable partner_hotel_code cannot be null');
        }
        $this->container['partner_hotel_code'] = $partner_hotel_code;

        return $this;
    }

    /**
     * Gets listing_id
     *
     * @return int|null
     */
    public function getListingId(): ?int
    {
        return $this->container['listing_id'];
    }

    /**
     * Sets listing_id
     *
     * @param int|null $listing_id `create-property`, `add-room`, `add-rate-plan`: the listing whose content is used.
     *
     * @return $this
     */
    public function setListingId(?int $listing_id): static
    {
        if (is_null($listing_id)) {
            throw new InvalidArgumentException('non-nullable listing_id cannot be null');
        }
        $this->container['listing_id'] = $listing_id;

        return $this;
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
     * @param string|null $hotel_id `set-tiket-hotel-id`: the Hotel ID tiket.com assigned. `check-activation`: the Hotel ID to check.
     *
     * @return $this
     */
    public function setHotelId(?string $hotel_id): static
    {
        if (is_null($hotel_id)) {
            throw new InvalidArgumentException('non-nullable hotel_id cannot be null');
        }
        $this->container['hotel_id'] = $hotel_id;

        return $this;
    }

    /**
     * Gets name
     *
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->container['name'];
    }

    /**
     * Sets name
     *
     * @param string|null $name `create-property`, `add-room`, `add-rate-plan`: name on tiket.com.
     *
     * @return $this
     */
    public function setName(?string $name): static
    {
        if (is_null($name)) {
            throw new InvalidArgumentException('non-nullable name cannot be null');
        }
        $this->container['name'] = $name;

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
     * @param string|null $currency `create-property`: ISO 4217.
     *
     * @return $this
     */
    public function setCurrency(?string $currency): static
    {
        if (is_null($currency)) {
            throw new InvalidArgumentException('non-nullable currency cannot be null');
        }
        $this->container['currency'] = $currency;

        return $this;
    }

    /**
     * Gets property_type_id
     *
     * @return int|null
     */
    public function getPropertyTypeId(): ?int
    {
        return $this->container['property_type_id'];
    }

    /**
     * Sets property_type_id
     *
     * @param int|null $property_type_id `create-property`: tiket.com property type.
     *
     * @return $this
     */
    public function setPropertyTypeId(?int $property_type_id): static
    {
        if (is_null($property_type_id)) {
            throw new InvalidArgumentException('non-nullable property_type_id cannot be null');
        }
        $this->container['property_type_id'] = $property_type_id;

        return $this;
    }

    /**
     * Gets latitude
     *
     * @return float|null
     */
    public function getLatitude(): ?float
    {
        return $this->container['latitude'];
    }

    /**
     * Sets latitude
     *
     * @param float|null $latitude latitude
     *
     * @return $this
     */
    public function setLatitude(?float $latitude): static
    {
        if (is_null($latitude)) {
            throw new InvalidArgumentException('non-nullable latitude cannot be null');
        }
        $this->container['latitude'] = $latitude;

        return $this;
    }

    /**
     * Gets longitude
     *
     * @return float|null
     */
    public function getLongitude(): ?float
    {
        return $this->container['longitude'];
    }

    /**
     * Sets longitude
     *
     * @param float|null $longitude longitude
     *
     * @return $this
     */
    public function setLongitude(?float $longitude): static
    {
        if (is_null($longitude)) {
            throw new InvalidArgumentException('non-nullable longitude cannot be null');
        }
        $this->container['longitude'] = $longitude;

        return $this;
    }

    /**
     * Gets address
     *
     * @return string|null
     */
    public function getAddress(): ?string
    {
        return $this->container['address'];
    }

    /**
     * Sets address
     *
     * @param string|null $address address
     *
     * @return $this
     */
    public function setAddress(?string $address): static
    {
        if (is_null($address)) {
            throw new InvalidArgumentException('non-nullable address cannot be null');
        }
        $this->container['address'] = $address;

        return $this;
    }

    /**
     * Gets postal_code
     *
     * @return string|null
     */
    public function getPostalCode(): ?string
    {
        return $this->container['postal_code'];
    }

    /**
     * Sets postal_code
     *
     * @param string|null $postal_code postal_code
     *
     * @return $this
     */
    public function setPostalCode(?string $postal_code): static
    {
        if (is_null($postal_code)) {
            throw new InvalidArgumentException('non-nullable postal_code cannot be null');
        }
        $this->container['postal_code'] = $postal_code;

        return $this;
    }

    /**
     * Gets country_code
     *
     * @return string|null
     */
    public function getCountryCode(): ?string
    {
        return $this->container['country_code'];
    }

    /**
     * Sets country_code
     *
     * @param string|null $country_code country_code
     *
     * @return $this
     */
    public function setCountryCode(?string $country_code): static
    {
        if (is_null($country_code)) {
            throw new InvalidArgumentException('non-nullable country_code cannot be null');
        }
        $this->container['country_code'] = $country_code;

        return $this;
    }

    /**
     * Gets state_code
     *
     * @return string|null
     */
    public function getStateCode(): ?string
    {
        return $this->container['state_code'];
    }

    /**
     * Sets state_code
     *
     * @param string|null $state_code state_code
     *
     * @return $this
     */
    public function setStateCode(?string $state_code): static
    {
        if (is_null($state_code)) {
            throw new InvalidArgumentException('non-nullable state_code cannot be null');
        }
        $this->container['state_code'] = $state_code;

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
     * @param string|null $company_name `create-property`: the legal entity that operates the property.
     *
     * @return $this
     */
    public function setCompanyName(?string $company_name): static
    {
        if (is_null($company_name)) {
            throw new InvalidArgumentException('non-nullable company_name cannot be null');
        }
        $this->container['company_name'] = $company_name;

        return $this;
    }

    /**
     * Gets company_address
     *
     * @return string|null
     */
    public function getCompanyAddress(): ?string
    {
        return $this->container['company_address'];
    }

    /**
     * Sets company_address
     *
     * @param string|null $company_address company_address
     *
     * @return $this
     */
    public function setCompanyAddress(?string $company_address): static
    {
        if (is_null($company_address)) {
            throw new InvalidArgumentException('non-nullable company_address cannot be null');
        }
        $this->container['company_address'] = $company_address;

        return $this;
    }

    /**
     * Gets star_rating
     *
     * @return float|null
     */
    public function getStarRating(): ?float
    {
        return $this->container['star_rating'];
    }

    /**
     * Sets star_rating
     *
     * @param float|null $star_rating star_rating
     *
     * @return $this
     */
    public function setStarRating(?float $star_rating): static
    {
        if (is_null($star_rating)) {
            throw new InvalidArgumentException('non-nullable star_rating cannot be null');
        }
        $this->container['star_rating'] = $star_rating;

        return $this;
    }

    /**
     * Gets pricing_model
     *
     * @return string|null
     */
    public function getPricingModel(): ?string
    {
        return $this->container['pricing_model'];
    }

    /**
     * Sets pricing_model
     *
     * @param string|null $pricing_model `create-property`: `Room Based` or `Occupancy Based`.
     *
     * @return $this
     */
    public function setPricingModel(?string $pricing_model): static
    {
        if (is_null($pricing_model)) {
            throw new InvalidArgumentException('non-nullable pricing_model cannot be null');
        }
        $this->container['pricing_model'] = $pricing_model;

        return $this;
    }

    /**
     * Gets description
     *
     * @return string|null
     */
    public function getDescription(): ?string
    {
        return $this->container['description'];
    }

    /**
     * Sets description
     *
     * @param string|null $description description
     *
     * @return $this
     */
    public function setDescription(?string $description): static
    {
        if (is_null($description)) {
            throw new InvalidArgumentException('non-nullable description cannot be null');
        }
        $this->container['description'] = $description;

        return $this;
    }

    /**
     * Gets total_rooms
     *
     * @return int|null
     */
    public function getTotalRooms(): ?int
    {
        return $this->container['total_rooms'];
    }

    /**
     * Sets total_rooms
     *
     * @param int|null $total_rooms total_rooms
     *
     * @return $this
     */
    public function setTotalRooms(?int $total_rooms): static
    {
        if (is_null($total_rooms)) {
            throw new InvalidArgumentException('non-nullable total_rooms cannot be null');
        }
        $this->container['total_rooms'] = $total_rooms;

        return $this;
    }

    /**
     * Gets floors
     *
     * @return int|null
     */
    public function getFloors(): ?int
    {
        return $this->container['floors'];
    }

    /**
     * Sets floors
     *
     * @param int|null $floors floors
     *
     * @return $this
     */
    public function setFloors(?int $floors): static
    {
        if (is_null($floors)) {
            throw new InvalidArgumentException('non-nullable floors cannot be null');
        }
        $this->container['floors'] = $floors;

        return $this;
    }

    /**
     * Gets when_built
     *
     * @return int|null
     */
    public function getWhenBuilt(): ?int
    {
        return $this->container['when_built'];
    }

    /**
     * Sets when_built
     *
     * @param int|null $when_built when_built
     *
     * @return $this
     */
    public function setWhenBuilt(?int $when_built): static
    {
        if (is_null($when_built)) {
            throw new InvalidArgumentException('non-nullable when_built cannot be null');
        }
        $this->container['when_built'] = $when_built;

        return $this;
    }

    /**
     * Gets contacts
     *
     * @return \Repull\Model\TiketSetupRequestContactsInner[]|null
     */
    public function getContacts(): ?array
    {
        return $this->container['contacts'];
    }

    /**
     * Sets contacts
     *
     * @param \Repull\Model\TiketSetupRequestContactsInner[]|null $contacts `create-property`: property contacts.
     *
     * @return $this
     */
    public function setContacts(?array $contacts): static
    {
        if (is_null($contacts)) {
            throw new InvalidArgumentException('non-nullable contacts cannot be null');
        }
        $this->container['contacts'] = $contacts;

        return $this;
    }

    /**
     * Gets quantity
     *
     * @return int|null
     */
    public function getQuantity(): ?int
    {
        return $this->container['quantity'];
    }

    /**
     * Sets quantity
     *
     * @param int|null $quantity `add-room`: rooms of this type.
     *
     * @return $this
     */
    public function setQuantity(?int $quantity): static
    {
        if (is_null($quantity)) {
            throw new InvalidArgumentException('non-nullable quantity cannot be null');
        }
        $this->container['quantity'] = $quantity;

        return $this;
    }

    /**
     * Gets max_adults
     *
     * @return int|null
     */
    public function getMaxAdults(): ?int
    {
        return $this->container['max_adults'];
    }

    /**
     * Sets max_adults
     *
     * @param int|null $max_adults max_adults
     *
     * @return $this
     */
    public function setMaxAdults(?int $max_adults): static
    {
        if (is_null($max_adults)) {
            throw new InvalidArgumentException('non-nullable max_adults cannot be null');
        }
        $this->container['max_adults'] = $max_adults;

        return $this;
    }

    /**
     * Gets max_children
     *
     * @return int|null
     */
    public function getMaxChildren(): ?int
    {
        return $this->container['max_children'];
    }

    /**
     * Sets max_children
     *
     * @param int|null $max_children max_children
     *
     * @return $this
     */
    public function setMaxChildren(?int $max_children): static
    {
        if (is_null($max_children)) {
            throw new InvalidArgumentException('non-nullable max_children cannot be null');
        }
        $this->container['max_children'] = $max_children;

        return $this;
    }

    /**
     * Gets room_ids
     *
     * @return string[]|null
     */
    public function getRoomIds(): ?array
    {
        return $this->container['room_ids'];
    }

    /**
     * Sets room_ids
     *
     * @param string[]|null $room_ids `add-rate-plan`: rooms the rate plan applies to.
     *
     * @return $this
     */
    public function setRoomIds(?array $room_ids): static
    {
        if (is_null($room_ids)) {
            throw new InvalidArgumentException('non-nullable room_ids cannot be null');
        }
        $this->container['room_ids'] = $room_ids;

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
     * @param string|null $cancellation_policy `add-rate-plan`.
     *
     * @return $this
     */
    public function setCancellationPolicy(?string $cancellation_policy): static
    {
        if (is_null($cancellation_policy)) {
            throw new InvalidArgumentException('non-nullable cancellation_policy cannot be null');
        }
        $this->container['cancellation_policy'] = $cancellation_policy;

        return $this;
    }

    /**
     * Gets breakfast
     *
     * @return bool|null
     */
    public function getBreakfast(): ?bool
    {
        return $this->container['breakfast'];
    }

    /**
     * Sets breakfast
     *
     * @param bool|null $breakfast `add-rate-plan`: breakfast included.
     *
     * @return $this
     */
    public function setBreakfast(?bool $breakfast): static
    {
        if (is_null($breakfast)) {
            throw new InvalidArgumentException('non-nullable breakfast cannot be null');
        }
        $this->container['breakfast'] = $breakfast;

        return $this;
    }

    /**
     * Gets properties
     *
     * @return \Repull\Model\TiketSetupRequestPropertiesInner[]|null
     */
    public function getProperties(): ?array
    {
        return $this->container['properties'];
    }

    /**
     * Sets properties
     *
     * @param \Repull\Model\TiketSetupRequestPropertiesInner[]|null $properties `request-activation`: the properties to activate.
     *
     * @return $this
     */
    public function setProperties(?array $properties): static
    {
        if (is_null($properties)) {
            throw new InvalidArgumentException('non-nullable properties cannot be null');
        }
        $this->container['properties'] = $properties;

        return $this;
    }

    /**
     * Gets rate_plan_ids
     *
     * @return string[]|null
     */
    public function getRatePlanIds(): ?array
    {
        return $this->container['rate_plan_ids'];
    }

    /**
     * Sets rate_plan_ids
     *
     * @param string[]|null $rate_plan_ids `set-push-rate-plans`: the `ratePlanId`s that receive prices and restrictions.
     *
     * @return $this
     */
    public function setRatePlanIds(?array $rate_plan_ids): static
    {
        if (is_null($rate_plan_ids)) {
            throw new InvalidArgumentException('non-nullable rate_plan_ids cannot be null');
        }
        $this->container['rate_plan_ids'] = $rate_plan_ids;

        return $this;
    }

    /**
     * Gets enabled
     *
     * @return bool|null
     */
    public function getEnabled(): ?bool
    {
        return $this->container['enabled'];
    }

    /**
     * Sets enabled
     *
     * @param bool|null $enabled `set-sync`: send calendar changes to tiket.com automatically.
     *
     * @return $this
     */
    public function setEnabled(?bool $enabled): static
    {
        if (is_null($enabled)) {
            throw new InvalidArgumentException('non-nullable enabled cannot be null');
        }
        $this->container['enabled'] = $enabled;

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


