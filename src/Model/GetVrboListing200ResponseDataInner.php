<?php
/**
 * GetVrboListing200ResponseDataInner
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
 * GetVrboListing200ResponseDataInner Class Doc Comment
 *
 * @package  Repull
 * @author   OpenAPI Generator team
 * @link     https://openapi-generator.tech
 * @implements ArrayAccess<string, mixed>
 */
class GetVrboListing200ResponseDataInner implements ModelInterface, ArrayAccess, JsonSerializable
{
    public const DISCRIMINATOR = null;

    /**
     * The original name of the model.
     *
     * @var string
     */
    protected static string $openAPIModelName = 'getVrboListing_200_response_data_inner';

    /**
     * Array of property to type mappings. Used for (de)serialization
     *
     * @var array<string, string>
     */
    protected static array $openAPITypes = [
        'id' => 'string',
        'listing_id' => 'string',
        'vrbo_listing_number' => 'string',
        'unit_id' => 'string',
        'property_id' => 'string',
        'advertiser_id' => 'string',
        'url' => 'string',
        'status' => 'string',
        'live' => 'bool',
        'connected' => 'bool',
        'active' => 'bool',
        'sync_enabled' => 'bool',
        'account_id' => 'string',
        'account_name' => 'string',
        'access_type' => 'string',
        'account_status' => 'string',
        'headline' => 'string',
        'name' => 'string',
        'thumbnail_url' => 'string',
        'currency' => 'string',
        'markup_percent' => 'float',
        'booking_policy' => 'string',
        'capacity' => '\Repull\Model\GetVrboListing200ResponseDataInnerCapacity',
        'bedrooms' => 'int',
        'bathrooms' => 'float',
        'beds' => 'int',
        'check_in_time' => 'string',
        'check_out_time' => 'string',
        'min_stay' => 'int',
        'max_stay' => 'int',
        'min_advance_notice_days' => 'int',
        'address' => '\Repull\Model\GetVrboListing200ResponseDataInnerAddress',
        'rating' => '\Repull\Model\GetVrboListing200ResponseDataInnerRating',
        'host_name' => 'string',
        'linked_at' => 'string',
        'disconnected_at' => 'string',
        'details_synced_at' => 'string',
        'content' => '\Repull\Model\GetVrboListing200ResponseDataInnerContent'
    ];

    /**
     * Array of property to format mappings. Used for (de)serialization
     *
     * @var array<string, string|null>
     */
    protected static array $openAPIFormats = [
        'id' => null,
        'listing_id' => null,
        'vrbo_listing_number' => null,
        'unit_id' => null,
        'property_id' => null,
        'advertiser_id' => null,
        'url' => null,
        'status' => null,
        'live' => null,
        'connected' => null,
        'active' => null,
        'sync_enabled' => null,
        'account_id' => null,
        'account_name' => null,
        'access_type' => null,
        'account_status' => null,
        'headline' => null,
        'name' => null,
        'thumbnail_url' => null,
        'currency' => null,
        'markup_percent' => null,
        'booking_policy' => null,
        'capacity' => null,
        'bedrooms' => null,
        'bathrooms' => null,
        'beds' => null,
        'check_in_time' => null,
        'check_out_time' => null,
        'min_stay' => null,
        'max_stay' => null,
        'min_advance_notice_days' => null,
        'address' => null,
        'rating' => null,
        'host_name' => null,
        'linked_at' => null,
        'disconnected_at' => null,
        'details_synced_at' => null,
        'content' => null
    ];

    /**
     * Array of nullable properties. Used for (de)serialization
     *
     * @var array<string, bool>
     */
    protected static array $openAPINullables = [
        'id' => false,
        'listing_id' => false,
        'vrbo_listing_number' => false,
        'unit_id' => true,
        'property_id' => true,
        'advertiser_id' => true,
        'url' => true,
        'status' => true,
        'live' => false,
        'connected' => false,
        'active' => false,
        'sync_enabled' => false,
        'account_id' => true,
        'account_name' => true,
        'access_type' => true,
        'account_status' => true,
        'headline' => true,
        'name' => true,
        'thumbnail_url' => true,
        'currency' => true,
        'markup_percent' => true,
        'booking_policy' => true,
        'capacity' => false,
        'bedrooms' => true,
        'bathrooms' => true,
        'beds' => true,
        'check_in_time' => true,
        'check_out_time' => true,
        'min_stay' => true,
        'max_stay' => true,
        'min_advance_notice_days' => true,
        'address' => false,
        'rating' => false,
        'host_name' => true,
        'linked_at' => true,
        'disconnected_at' => true,
        'details_synced_at' => true,
        'content' => false
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
        'listing_id' => 'listingId',
        'vrbo_listing_number' => 'vrboListingNumber',
        'unit_id' => 'unitId',
        'property_id' => 'propertyId',
        'advertiser_id' => 'advertiserId',
        'url' => 'url',
        'status' => 'status',
        'live' => 'live',
        'connected' => 'connected',
        'active' => 'active',
        'sync_enabled' => 'syncEnabled',
        'account_id' => 'accountId',
        'account_name' => 'accountName',
        'access_type' => 'accessType',
        'account_status' => 'accountStatus',
        'headline' => 'headline',
        'name' => 'name',
        'thumbnail_url' => 'thumbnailUrl',
        'currency' => 'currency',
        'markup_percent' => 'markupPercent',
        'booking_policy' => 'bookingPolicy',
        'capacity' => 'capacity',
        'bedrooms' => 'bedrooms',
        'bathrooms' => 'bathrooms',
        'beds' => 'beds',
        'check_in_time' => 'checkInTime',
        'check_out_time' => 'checkOutTime',
        'min_stay' => 'minStay',
        'max_stay' => 'maxStay',
        'min_advance_notice_days' => 'minAdvanceNoticeDays',
        'address' => 'address',
        'rating' => 'rating',
        'host_name' => 'hostName',
        'linked_at' => 'linkedAt',
        'disconnected_at' => 'disconnectedAt',
        'details_synced_at' => 'detailsSyncedAt',
        'content' => 'content'
    ];

    /**
     * Array of attributes to setter functions (for deserialization of responses)
     *
     * @var array<string, string>
     */
    protected static array $setters = [
        'id' => 'setId',
        'listing_id' => 'setListingId',
        'vrbo_listing_number' => 'setVrboListingNumber',
        'unit_id' => 'setUnitId',
        'property_id' => 'setPropertyId',
        'advertiser_id' => 'setAdvertiserId',
        'url' => 'setUrl',
        'status' => 'setStatus',
        'live' => 'setLive',
        'connected' => 'setConnected',
        'active' => 'setActive',
        'sync_enabled' => 'setSyncEnabled',
        'account_id' => 'setAccountId',
        'account_name' => 'setAccountName',
        'access_type' => 'setAccessType',
        'account_status' => 'setAccountStatus',
        'headline' => 'setHeadline',
        'name' => 'setName',
        'thumbnail_url' => 'setThumbnailUrl',
        'currency' => 'setCurrency',
        'markup_percent' => 'setMarkupPercent',
        'booking_policy' => 'setBookingPolicy',
        'capacity' => 'setCapacity',
        'bedrooms' => 'setBedrooms',
        'bathrooms' => 'setBathrooms',
        'beds' => 'setBeds',
        'check_in_time' => 'setCheckInTime',
        'check_out_time' => 'setCheckOutTime',
        'min_stay' => 'setMinStay',
        'max_stay' => 'setMaxStay',
        'min_advance_notice_days' => 'setMinAdvanceNoticeDays',
        'address' => 'setAddress',
        'rating' => 'setRating',
        'host_name' => 'setHostName',
        'linked_at' => 'setLinkedAt',
        'disconnected_at' => 'setDisconnectedAt',
        'details_synced_at' => 'setDetailsSyncedAt',
        'content' => 'setContent'
    ];

    /**
     * Array of attributes to getter functions (for serialization of requests)
     *
     * @var array<string, string>
     */
    protected static array $getters = [
        'id' => 'getId',
        'listing_id' => 'getListingId',
        'vrbo_listing_number' => 'getVrboListingNumber',
        'unit_id' => 'getUnitId',
        'property_id' => 'getPropertyId',
        'advertiser_id' => 'getAdvertiserId',
        'url' => 'getUrl',
        'status' => 'getStatus',
        'live' => 'getLive',
        'connected' => 'getConnected',
        'active' => 'getActive',
        'sync_enabled' => 'getSyncEnabled',
        'account_id' => 'getAccountId',
        'account_name' => 'getAccountName',
        'access_type' => 'getAccessType',
        'account_status' => 'getAccountStatus',
        'headline' => 'getHeadline',
        'name' => 'getName',
        'thumbnail_url' => 'getThumbnailUrl',
        'currency' => 'getCurrency',
        'markup_percent' => 'getMarkupPercent',
        'booking_policy' => 'getBookingPolicy',
        'capacity' => 'getCapacity',
        'bedrooms' => 'getBedrooms',
        'bathrooms' => 'getBathrooms',
        'beds' => 'getBeds',
        'check_in_time' => 'getCheckInTime',
        'check_out_time' => 'getCheckOutTime',
        'min_stay' => 'getMinStay',
        'max_stay' => 'getMaxStay',
        'min_advance_notice_days' => 'getMinAdvanceNoticeDays',
        'address' => 'getAddress',
        'rating' => 'getRating',
        'host_name' => 'getHostName',
        'linked_at' => 'getLinkedAt',
        'disconnected_at' => 'getDisconnectedAt',
        'details_synced_at' => 'getDetailsSyncedAt',
        'content' => 'getContent'
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

    public const ACCESS_TYPE_FULL_ACCESS = 'full_access';
    public const ACCESS_TYPE_MESSAGING = 'messaging';

    /**
     * Gets allowable values of the enum
     *
     * @return string[]
     */
    public static function getAccessTypeAllowableValues()
    {
        return [
            self::ACCESS_TYPE_FULL_ACCESS,
            self::ACCESS_TYPE_MESSAGING,
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
        $this->setIfExists('listing_id', $data ?? [], null);
        $this->setIfExists('vrbo_listing_number', $data ?? [], null);
        $this->setIfExists('unit_id', $data ?? [], null);
        $this->setIfExists('property_id', $data ?? [], null);
        $this->setIfExists('advertiser_id', $data ?? [], null);
        $this->setIfExists('url', $data ?? [], null);
        $this->setIfExists('status', $data ?? [], null);
        $this->setIfExists('live', $data ?? [], null);
        $this->setIfExists('connected', $data ?? [], null);
        $this->setIfExists('active', $data ?? [], null);
        $this->setIfExists('sync_enabled', $data ?? [], null);
        $this->setIfExists('account_id', $data ?? [], null);
        $this->setIfExists('account_name', $data ?? [], null);
        $this->setIfExists('access_type', $data ?? [], null);
        $this->setIfExists('account_status', $data ?? [], null);
        $this->setIfExists('headline', $data ?? [], null);
        $this->setIfExists('name', $data ?? [], null);
        $this->setIfExists('thumbnail_url', $data ?? [], null);
        $this->setIfExists('currency', $data ?? [], null);
        $this->setIfExists('markup_percent', $data ?? [], null);
        $this->setIfExists('booking_policy', $data ?? [], null);
        $this->setIfExists('capacity', $data ?? [], null);
        $this->setIfExists('bedrooms', $data ?? [], null);
        $this->setIfExists('bathrooms', $data ?? [], null);
        $this->setIfExists('beds', $data ?? [], null);
        $this->setIfExists('check_in_time', $data ?? [], null);
        $this->setIfExists('check_out_time', $data ?? [], null);
        $this->setIfExists('min_stay', $data ?? [], null);
        $this->setIfExists('max_stay', $data ?? [], null);
        $this->setIfExists('min_advance_notice_days', $data ?? [], null);
        $this->setIfExists('address', $data ?? [], null);
        $this->setIfExists('rating', $data ?? [], null);
        $this->setIfExists('host_name', $data ?? [], null);
        $this->setIfExists('linked_at', $data ?? [], null);
        $this->setIfExists('disconnected_at', $data ?? [], null);
        $this->setIfExists('details_synced_at', $data ?? [], null);
        $this->setIfExists('content', $data ?? [], null);
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

        $allowedValues = self::getAccessTypeAllowableValues();
        if (!is_null($this->container['access_type']) && !in_array($this->container['access_type'], $allowedValues, true)) {
            $invalidProperties[] = sprintf(
                "invalid value '%s' for 'access_type', must be one of '%s'",
                $this->container['access_type'],
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
     * @param string|null $id Repull's id for this link.
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
            throw new InvalidArgumentException('non-nullable listing_id cannot be null');
        }
        $this->container['listing_id'] = $listing_id;

        return $this;
    }

    /**
     * Gets vrbo_listing_number
     *
     * @return string|null
     */
    public function getVrboListingNumber(): ?string
    {
        return $this->container['vrbo_listing_number'];
    }

    /**
     * Sets vrbo_listing_number
     *
     * @param string|null $vrbo_listing_number The number in the Vrbo URL (vrbo.com/4512345).
     *
     * @return $this
     */
    public function setVrboListingNumber(?string $vrbo_listing_number): static
    {
        if (is_null($vrbo_listing_number)) {
            throw new InvalidArgumentException('non-nullable vrbo_listing_number cannot be null');
        }
        $this->container['vrbo_listing_number'] = $vrbo_listing_number;

        return $this;
    }

    /**
     * Gets unit_id
     *
     * @return string|null
     */
    public function getUnitId(): ?string
    {
        return $this->container['unit_id'];
    }

    /**
     * Sets unit_id
     *
     * @param string|null $unit_id unit_id
     *
     * @return $this
     */
    public function setUnitId(?string $unit_id): static
    {
        if (is_null($unit_id)) {
            array_push($this->openAPINullablesSetToNull, 'unit_id');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('unit_id', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['unit_id'] = $unit_id;

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
     * @param string|null $property_id Vrbo's property id.
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
     * Gets advertiser_id
     *
     * @return string|null
     */
    public function getAdvertiserId(): ?string
    {
        return $this->container['advertiser_id'];
    }

    /**
     * Sets advertiser_id
     *
     * @param string|null $advertiser_id advertiser_id
     *
     * @return $this
     */
    public function setAdvertiserId(?string $advertiser_id): static
    {
        if (is_null($advertiser_id)) {
            array_push($this->openAPINullablesSetToNull, 'advertiser_id');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('advertiser_id', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['advertiser_id'] = $advertiser_id;

        return $this;
    }

    /**
     * Gets url
     *
     * @return string|null
     */
    public function getUrl(): ?string
    {
        return $this->container['url'];
    }

    /**
     * Sets url
     *
     * @param string|null $url url
     *
     * @return $this
     */
    public function setUrl(?string $url): static
    {
        if (is_null($url)) {
            array_push($this->openAPINullablesSetToNull, 'url');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('url', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['url'] = $url;

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
     * @param string|null $status Vrbo's own status: `LIVE`, `InactiveByOwnerRequest` (hidden), …
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
        $this->container['status'] = $status;

        return $this;
    }

    /**
     * Gets live
     *
     * @return bool|null
     */
    public function getLive(): ?bool
    {
        return $this->container['live'];
    }

    /**
     * Sets live
     *
     * @param bool|null $live live
     *
     * @return $this
     */
    public function setLive(?bool $live): static
    {
        if (is_null($live)) {
            throw new InvalidArgumentException('non-nullable live cannot be null');
        }
        $this->container['live'] = $live;

        return $this;
    }

    /**
     * Gets connected
     *
     * @return bool|null
     */
    public function getConnected(): ?bool
    {
        return $this->container['connected'];
    }

    /**
     * Sets connected
     *
     * @param bool|null $connected Linked through a connected Vrbo account — the link content, calendar and messages go through.
     *
     * @return $this
     */
    public function setConnected(?bool $connected): static
    {
        if (is_null($connected)) {
            throw new InvalidArgumentException('non-nullable connected cannot be null');
        }
        $this->container['connected'] = $connected;

        return $this;
    }

    /**
     * Gets active
     *
     * @return bool|null
     */
    public function getActive(): ?bool
    {
        return $this->container['active'];
    }

    /**
     * Sets active
     *
     * @param bool|null $active active
     *
     * @return $this
     */
    public function setActive(?bool $active): static
    {
        if (is_null($active)) {
            throw new InvalidArgumentException('non-nullable active cannot be null');
        }
        $this->container['active'] = $active;

        return $this;
    }

    /**
     * Gets sync_enabled
     *
     * @return bool|null
     */
    public function getSyncEnabled(): ?bool
    {
        return $this->container['sync_enabled'];
    }

    /**
     * Sets sync_enabled
     *
     * @param bool|null $sync_enabled sync_enabled
     *
     * @return $this
     */
    public function setSyncEnabled(?bool $sync_enabled): static
    {
        if (is_null($sync_enabled)) {
            throw new InvalidArgumentException('non-nullable sync_enabled cannot be null');
        }
        $this->container['sync_enabled'] = $sync_enabled;

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
     * @param string|null $account_id The Vrbo account the unit is on, as `GET /v1/connect/vrbo-login` lists it.
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
        // (relax-enums.php) accept unknown enum values for forward compat
        $this->container['access_type'] = $access_type;

        return $this;
    }

    /**
     * Gets account_status
     *
     * @return string|null
     */
    public function getAccountStatus(): ?string
    {
        return $this->container['account_status'];
    }

    /**
     * Sets account_status
     *
     * @param string|null $account_status account_status
     *
     * @return $this
     */
    public function setAccountStatus(?string $account_status): static
    {
        if (is_null($account_status)) {
            array_push($this->openAPINullablesSetToNull, 'account_status');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('account_status', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['account_status'] = $account_status;

        return $this;
    }

    /**
     * Gets headline
     *
     * @return string|null
     */
    public function getHeadline(): ?string
    {
        return $this->container['headline'];
    }

    /**
     * Sets headline
     *
     * @param string|null $headline headline
     *
     * @return $this
     */
    public function setHeadline(?string $headline): static
    {
        if (is_null($headline)) {
            array_push($this->openAPINullablesSetToNull, 'headline');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('headline', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['headline'] = $headline;

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
     * @param string|null $name name
     *
     * @return $this
     */
    public function setName(?string $name): static
    {
        if (is_null($name)) {
            array_push($this->openAPINullablesSetToNull, 'name');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('name', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['name'] = $name;

        return $this;
    }

    /**
     * Gets thumbnail_url
     *
     * @return string|null
     */
    public function getThumbnailUrl(): ?string
    {
        return $this->container['thumbnail_url'];
    }

    /**
     * Sets thumbnail_url
     *
     * @param string|null $thumbnail_url thumbnail_url
     *
     * @return $this
     */
    public function setThumbnailUrl(?string $thumbnail_url): static
    {
        if (is_null($thumbnail_url)) {
            array_push($this->openAPINullablesSetToNull, 'thumbnail_url');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('thumbnail_url', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['thumbnail_url'] = $thumbnail_url;

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
     * Gets markup_percent
     *
     * @return float|null
     */
    public function getMarkupPercent(): ?float
    {
        return $this->container['markup_percent'];
    }

    /**
     * Sets markup_percent
     *
     * @param float|null $markup_percent The markup Repull adds to this unit's prices.
     *
     * @return $this
     */
    public function setMarkupPercent(?float $markup_percent): static
    {
        if (is_null($markup_percent)) {
            array_push($this->openAPINullablesSetToNull, 'markup_percent');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('markup_percent', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['markup_percent'] = $markup_percent;

        return $this;
    }

    /**
     * Gets booking_policy
     *
     * @return string|null
     */
    public function getBookingPolicy(): ?string
    {
        return $this->container['booking_policy'];
    }

    /**
     * Sets booking_policy
     *
     * @param string|null $booking_policy booking_policy
     *
     * @return $this
     */
    public function setBookingPolicy(?string $booking_policy): static
    {
        if (is_null($booking_policy)) {
            array_push($this->openAPINullablesSetToNull, 'booking_policy');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('booking_policy', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['booking_policy'] = $booking_policy;

        return $this;
    }

    /**
     * Gets capacity
     *
     * @return \Repull\Model\GetVrboListing200ResponseDataInnerCapacity|null
     */
    public function getCapacity(): ?\Repull\Model\GetVrboListing200ResponseDataInnerCapacity
    {
        return $this->container['capacity'];
    }

    /**
     * Sets capacity
     *
     * @param \Repull\Model\GetVrboListing200ResponseDataInnerCapacity|null $capacity capacity
     *
     * @return $this
     */
    public function setCapacity(?\Repull\Model\GetVrboListing200ResponseDataInnerCapacity $capacity): static
    {
        if (is_null($capacity)) {
            throw new InvalidArgumentException('non-nullable capacity cannot be null');
        }
        $this->container['capacity'] = $capacity;

        return $this;
    }

    /**
     * Gets bedrooms
     *
     * @return int|null
     */
    public function getBedrooms(): ?int
    {
        return $this->container['bedrooms'];
    }

    /**
     * Sets bedrooms
     *
     * @param int|null $bedrooms bedrooms
     *
     * @return $this
     */
    public function setBedrooms(?int $bedrooms): static
    {
        if (is_null($bedrooms)) {
            array_push($this->openAPINullablesSetToNull, 'bedrooms');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('bedrooms', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['bedrooms'] = $bedrooms;

        return $this;
    }

    /**
     * Gets bathrooms
     *
     * @return float|null
     */
    public function getBathrooms(): ?float
    {
        return $this->container['bathrooms'];
    }

    /**
     * Sets bathrooms
     *
     * @param float|null $bathrooms bathrooms
     *
     * @return $this
     */
    public function setBathrooms(?float $bathrooms): static
    {
        if (is_null($bathrooms)) {
            array_push($this->openAPINullablesSetToNull, 'bathrooms');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('bathrooms', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['bathrooms'] = $bathrooms;

        return $this;
    }

    /**
     * Gets beds
     *
     * @return int|null
     */
    public function getBeds(): ?int
    {
        return $this->container['beds'];
    }

    /**
     * Sets beds
     *
     * @param int|null $beds beds
     *
     * @return $this
     */
    public function setBeds(?int $beds): static
    {
        if (is_null($beds)) {
            array_push($this->openAPINullablesSetToNull, 'beds');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('beds', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['beds'] = $beds;

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
     * Gets min_stay
     *
     * @return int|null
     */
    public function getMinStay(): ?int
    {
        return $this->container['min_stay'];
    }

    /**
     * Sets min_stay
     *
     * @param int|null $min_stay min_stay
     *
     * @return $this
     */
    public function setMinStay(?int $min_stay): static
    {
        if (is_null($min_stay)) {
            array_push($this->openAPINullablesSetToNull, 'min_stay');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('min_stay', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['min_stay'] = $min_stay;

        return $this;
    }

    /**
     * Gets max_stay
     *
     * @return int|null
     */
    public function getMaxStay(): ?int
    {
        return $this->container['max_stay'];
    }

    /**
     * Sets max_stay
     *
     * @param int|null $max_stay max_stay
     *
     * @return $this
     */
    public function setMaxStay(?int $max_stay): static
    {
        if (is_null($max_stay)) {
            array_push($this->openAPINullablesSetToNull, 'max_stay');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('max_stay', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['max_stay'] = $max_stay;

        return $this;
    }

    /**
     * Gets min_advance_notice_days
     *
     * @return int|null
     */
    public function getMinAdvanceNoticeDays(): ?int
    {
        return $this->container['min_advance_notice_days'];
    }

    /**
     * Sets min_advance_notice_days
     *
     * @param int|null $min_advance_notice_days min_advance_notice_days
     *
     * @return $this
     */
    public function setMinAdvanceNoticeDays(?int $min_advance_notice_days): static
    {
        if (is_null($min_advance_notice_days)) {
            array_push($this->openAPINullablesSetToNull, 'min_advance_notice_days');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('min_advance_notice_days', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['min_advance_notice_days'] = $min_advance_notice_days;

        return $this;
    }

    /**
     * Gets address
     *
     * @return \Repull\Model\GetVrboListing200ResponseDataInnerAddress|null
     */
    public function getAddress(): ?\Repull\Model\GetVrboListing200ResponseDataInnerAddress
    {
        return $this->container['address'];
    }

    /**
     * Sets address
     *
     * @param \Repull\Model\GetVrboListing200ResponseDataInnerAddress|null $address address
     *
     * @return $this
     */
    public function setAddress(?\Repull\Model\GetVrboListing200ResponseDataInnerAddress $address): static
    {
        if (is_null($address)) {
            throw new InvalidArgumentException('non-nullable address cannot be null');
        }
        $this->container['address'] = $address;

        return $this;
    }

    /**
     * Gets rating
     *
     * @return \Repull\Model\GetVrboListing200ResponseDataInnerRating|null
     */
    public function getRating(): ?\Repull\Model\GetVrboListing200ResponseDataInnerRating
    {
        return $this->container['rating'];
    }

    /**
     * Sets rating
     *
     * @param \Repull\Model\GetVrboListing200ResponseDataInnerRating|null $rating rating
     *
     * @return $this
     */
    public function setRating(?\Repull\Model\GetVrboListing200ResponseDataInnerRating $rating): static
    {
        if (is_null($rating)) {
            throw new InvalidArgumentException('non-nullable rating cannot be null');
        }
        $this->container['rating'] = $rating;

        return $this;
    }

    /**
     * Gets host_name
     *
     * @return string|null
     */
    public function getHostName(): ?string
    {
        return $this->container['host_name'];
    }

    /**
     * Sets host_name
     *
     * @param string|null $host_name host_name
     *
     * @return $this
     */
    public function setHostName(?string $host_name): static
    {
        if (is_null($host_name)) {
            array_push($this->openAPINullablesSetToNull, 'host_name');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('host_name', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['host_name'] = $host_name;

        return $this;
    }

    /**
     * Gets linked_at
     *
     * @return string|null
     */
    public function getLinkedAt(): ?string
    {
        return $this->container['linked_at'];
    }

    /**
     * Sets linked_at
     *
     * @param string|null $linked_at linked_at
     *
     * @return $this
     */
    public function setLinkedAt(?string $linked_at): static
    {
        if (is_null($linked_at)) {
            array_push($this->openAPINullablesSetToNull, 'linked_at');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('linked_at', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['linked_at'] = $linked_at;

        return $this;
    }

    /**
     * Gets disconnected_at
     *
     * @return string|null
     */
    public function getDisconnectedAt(): ?string
    {
        return $this->container['disconnected_at'];
    }

    /**
     * Sets disconnected_at
     *
     * @param string|null $disconnected_at disconnected_at
     *
     * @return $this
     */
    public function setDisconnectedAt(?string $disconnected_at): static
    {
        if (is_null($disconnected_at)) {
            array_push($this->openAPINullablesSetToNull, 'disconnected_at');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('disconnected_at', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['disconnected_at'] = $disconnected_at;

        return $this;
    }

    /**
     * Gets details_synced_at
     *
     * @return string|null
     */
    public function getDetailsSyncedAt(): ?string
    {
        return $this->container['details_synced_at'];
    }

    /**
     * Sets details_synced_at
     *
     * @param string|null $details_synced_at details_synced_at
     *
     * @return $this
     */
    public function setDetailsSyncedAt(?string $details_synced_at): static
    {
        if (is_null($details_synced_at)) {
            array_push($this->openAPINullablesSetToNull, 'details_synced_at');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('details_synced_at', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['details_synced_at'] = $details_synced_at;

        return $this;
    }

    /**
     * Gets content
     *
     * @return \Repull\Model\GetVrboListing200ResponseDataInnerContent|null
     */
    public function getContent(): ?\Repull\Model\GetVrboListing200ResponseDataInnerContent
    {
        return $this->container['content'];
    }

    /**
     * Sets content
     *
     * @param \Repull\Model\GetVrboListing200ResponseDataInnerContent|null $content content
     *
     * @return $this
     */
    public function setContent(?\Repull\Model\GetVrboListing200ResponseDataInnerContent $content): static
    {
        if (is_null($content)) {
            throw new InvalidArgumentException('non-nullable content cannot be null');
        }
        $this->container['content'] = $content;

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


