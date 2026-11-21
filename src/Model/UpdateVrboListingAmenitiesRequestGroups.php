<?php
/**
 * UpdateVrboListingAmenitiesRequestGroups
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
 * UpdateVrboListingAmenitiesRequestGroups Class Doc Comment
 *
 * @description Keyed by group: essentials, internet, climateControl, general, office, kitchenAndDining, parking, poolAndSpa, sustainability, entertainment, outdoor, onsiteService, accessibility, homeSafety, safetyDevices, enhancedCleaning, locationAndView, localAttractionsAndActivities, checkoutInstructions.
 * @package  Repull
 * @author   OpenAPI Generator team
 * @link     https://openapi-generator.tech
 * @implements ArrayAccess<string, mixed>
 */
class UpdateVrboListingAmenitiesRequestGroups implements ModelInterface, ArrayAccess, JsonSerializable
{
    public const DISCRIMINATOR = null;

    /**
     * The original name of the model.
     *
     * @var string
     */
    protected static string $openAPIModelName = 'updateVrboListingAmenities_request_groups';

    /**
     * Array of property to type mappings. Used for (de)serialization
     *
     * @var array<string, string>
     */
    protected static array $openAPITypes = [
        'essentials' => '\Repull\Model\UpdateVrboListingAmenitiesRequestGroupsEssentials',
        'internet' => '\Repull\Model\UpdateVrboListingAmenitiesRequestGroupsEssentials',
        'climate_control' => '\Repull\Model\UpdateVrboListingAmenitiesRequestGroupsEssentials',
        'general' => '\Repull\Model\UpdateVrboListingAmenitiesRequestGroupsEssentials',
        'office' => '\Repull\Model\UpdateVrboListingAmenitiesRequestGroupsEssentials',
        'kitchen_and_dining' => '\Repull\Model\UpdateVrboListingAmenitiesRequestGroupsEssentials',
        'parking' => '\Repull\Model\UpdateVrboListingAmenitiesRequestGroupsEssentials',
        'pool_and_spa' => '\Repull\Model\UpdateVrboListingAmenitiesRequestGroupsEssentials',
        'sustainability' => '\Repull\Model\UpdateVrboListingAmenitiesRequestGroupsEssentials',
        'entertainment' => '\Repull\Model\UpdateVrboListingAmenitiesRequestGroupsEssentials',
        'outdoor' => '\Repull\Model\UpdateVrboListingAmenitiesRequestGroupsEssentials',
        'onsite_service' => '\Repull\Model\UpdateVrboListingAmenitiesRequestGroupsEssentials',
        'accessibility' => '\Repull\Model\UpdateVrboListingAmenitiesRequestGroupsEssentials',
        'home_safety' => '\Repull\Model\UpdateVrboListingAmenitiesRequestGroupsEssentials',
        'safety_devices' => '\Repull\Model\UpdateVrboListingAmenitiesRequestGroupsEssentials',
        'enhanced_cleaning' => '\Repull\Model\UpdateVrboListingAmenitiesRequestGroupsEssentials',
        'location_and_view' => '\Repull\Model\UpdateVrboListingAmenitiesRequestGroupsEssentials',
        'local_attractions_and_activities' => '\Repull\Model\UpdateVrboListingAmenitiesRequestGroupsEssentials',
        'checkout_instructions' => '\Repull\Model\UpdateVrboListingAmenitiesRequestGroupsEssentials'
    ];

    /**
     * Array of property to format mappings. Used for (de)serialization
     *
     * @var array<string, string|null>
     */
    protected static array $openAPIFormats = [
        'essentials' => null,
        'internet' => null,
        'climate_control' => null,
        'general' => null,
        'office' => null,
        'kitchen_and_dining' => null,
        'parking' => null,
        'pool_and_spa' => null,
        'sustainability' => null,
        'entertainment' => null,
        'outdoor' => null,
        'onsite_service' => null,
        'accessibility' => null,
        'home_safety' => null,
        'safety_devices' => null,
        'enhanced_cleaning' => null,
        'location_and_view' => null,
        'local_attractions_and_activities' => null,
        'checkout_instructions' => null
    ];

    /**
     * Array of nullable properties. Used for (de)serialization
     *
     * @var array<string, bool>
     */
    protected static array $openAPINullables = [
        'essentials' => false,
        'internet' => false,
        'climate_control' => false,
        'general' => false,
        'office' => false,
        'kitchen_and_dining' => false,
        'parking' => false,
        'pool_and_spa' => false,
        'sustainability' => false,
        'entertainment' => false,
        'outdoor' => false,
        'onsite_service' => false,
        'accessibility' => false,
        'home_safety' => false,
        'safety_devices' => false,
        'enhanced_cleaning' => false,
        'location_and_view' => false,
        'local_attractions_and_activities' => false,
        'checkout_instructions' => false
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
        'essentials' => 'essentials',
        'internet' => 'internet',
        'climate_control' => 'climateControl',
        'general' => 'general',
        'office' => 'office',
        'kitchen_and_dining' => 'kitchenAndDining',
        'parking' => 'parking',
        'pool_and_spa' => 'poolAndSpa',
        'sustainability' => 'sustainability',
        'entertainment' => 'entertainment',
        'outdoor' => 'outdoor',
        'onsite_service' => 'onsiteService',
        'accessibility' => 'accessibility',
        'home_safety' => 'homeSafety',
        'safety_devices' => 'safetyDevices',
        'enhanced_cleaning' => 'enhancedCleaning',
        'location_and_view' => 'locationAndView',
        'local_attractions_and_activities' => 'localAttractionsAndActivities',
        'checkout_instructions' => 'checkoutInstructions'
    ];

    /**
     * Array of attributes to setter functions (for deserialization of responses)
     *
     * @var array<string, string>
     */
    protected static array $setters = [
        'essentials' => 'setEssentials',
        'internet' => 'setInternet',
        'climate_control' => 'setClimateControl',
        'general' => 'setGeneral',
        'office' => 'setOffice',
        'kitchen_and_dining' => 'setKitchenAndDining',
        'parking' => 'setParking',
        'pool_and_spa' => 'setPoolAndSpa',
        'sustainability' => 'setSustainability',
        'entertainment' => 'setEntertainment',
        'outdoor' => 'setOutdoor',
        'onsite_service' => 'setOnsiteService',
        'accessibility' => 'setAccessibility',
        'home_safety' => 'setHomeSafety',
        'safety_devices' => 'setSafetyDevices',
        'enhanced_cleaning' => 'setEnhancedCleaning',
        'location_and_view' => 'setLocationAndView',
        'local_attractions_and_activities' => 'setLocalAttractionsAndActivities',
        'checkout_instructions' => 'setCheckoutInstructions'
    ];

    /**
     * Array of attributes to getter functions (for serialization of requests)
     *
     * @var array<string, string>
     */
    protected static array $getters = [
        'essentials' => 'getEssentials',
        'internet' => 'getInternet',
        'climate_control' => 'getClimateControl',
        'general' => 'getGeneral',
        'office' => 'getOffice',
        'kitchen_and_dining' => 'getKitchenAndDining',
        'parking' => 'getParking',
        'pool_and_spa' => 'getPoolAndSpa',
        'sustainability' => 'getSustainability',
        'entertainment' => 'getEntertainment',
        'outdoor' => 'getOutdoor',
        'onsite_service' => 'getOnsiteService',
        'accessibility' => 'getAccessibility',
        'home_safety' => 'getHomeSafety',
        'safety_devices' => 'getSafetyDevices',
        'enhanced_cleaning' => 'getEnhancedCleaning',
        'location_and_view' => 'getLocationAndView',
        'local_attractions_and_activities' => 'getLocalAttractionsAndActivities',
        'checkout_instructions' => 'getCheckoutInstructions'
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
        $this->setIfExists('essentials', $data ?? [], null);
        $this->setIfExists('internet', $data ?? [], null);
        $this->setIfExists('climate_control', $data ?? [], null);
        $this->setIfExists('general', $data ?? [], null);
        $this->setIfExists('office', $data ?? [], null);
        $this->setIfExists('kitchen_and_dining', $data ?? [], null);
        $this->setIfExists('parking', $data ?? [], null);
        $this->setIfExists('pool_and_spa', $data ?? [], null);
        $this->setIfExists('sustainability', $data ?? [], null);
        $this->setIfExists('entertainment', $data ?? [], null);
        $this->setIfExists('outdoor', $data ?? [], null);
        $this->setIfExists('onsite_service', $data ?? [], null);
        $this->setIfExists('accessibility', $data ?? [], null);
        $this->setIfExists('home_safety', $data ?? [], null);
        $this->setIfExists('safety_devices', $data ?? [], null);
        $this->setIfExists('enhanced_cleaning', $data ?? [], null);
        $this->setIfExists('location_and_view', $data ?? [], null);
        $this->setIfExists('local_attractions_and_activities', $data ?? [], null);
        $this->setIfExists('checkout_instructions', $data ?? [], null);
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
     * Gets essentials
     *
     * @return \Repull\Model\UpdateVrboListingAmenitiesRequestGroupsEssentials|null
     */
    public function getEssentials(): ?\Repull\Model\UpdateVrboListingAmenitiesRequestGroupsEssentials
    {
        return $this->container['essentials'];
    }

    /**
     * Sets essentials
     *
     * @param \Repull\Model\UpdateVrboListingAmenitiesRequestGroupsEssentials|null $essentials essentials
     *
     * @return $this
     */
    public function setEssentials(?\Repull\Model\UpdateVrboListingAmenitiesRequestGroupsEssentials $essentials): static
    {
        if (is_null($essentials)) {
            throw new InvalidArgumentException('non-nullable essentials cannot be null');
        }
        $this->container['essentials'] = $essentials;

        return $this;
    }

    /**
     * Gets internet
     *
     * @return \Repull\Model\UpdateVrboListingAmenitiesRequestGroupsEssentials|null
     */
    public function getInternet(): ?\Repull\Model\UpdateVrboListingAmenitiesRequestGroupsEssentials
    {
        return $this->container['internet'];
    }

    /**
     * Sets internet
     *
     * @param \Repull\Model\UpdateVrboListingAmenitiesRequestGroupsEssentials|null $internet internet
     *
     * @return $this
     */
    public function setInternet(?\Repull\Model\UpdateVrboListingAmenitiesRequestGroupsEssentials $internet): static
    {
        if (is_null($internet)) {
            throw new InvalidArgumentException('non-nullable internet cannot be null');
        }
        $this->container['internet'] = $internet;

        return $this;
    }

    /**
     * Gets climate_control
     *
     * @return \Repull\Model\UpdateVrboListingAmenitiesRequestGroupsEssentials|null
     */
    public function getClimateControl(): ?\Repull\Model\UpdateVrboListingAmenitiesRequestGroupsEssentials
    {
        return $this->container['climate_control'];
    }

    /**
     * Sets climate_control
     *
     * @param \Repull\Model\UpdateVrboListingAmenitiesRequestGroupsEssentials|null $climate_control climate_control
     *
     * @return $this
     */
    public function setClimateControl(?\Repull\Model\UpdateVrboListingAmenitiesRequestGroupsEssentials $climate_control): static
    {
        if (is_null($climate_control)) {
            throw new InvalidArgumentException('non-nullable climate_control cannot be null');
        }
        $this->container['climate_control'] = $climate_control;

        return $this;
    }

    /**
     * Gets general
     *
     * @return \Repull\Model\UpdateVrboListingAmenitiesRequestGroupsEssentials|null
     */
    public function getGeneral(): ?\Repull\Model\UpdateVrboListingAmenitiesRequestGroupsEssentials
    {
        return $this->container['general'];
    }

    /**
     * Sets general
     *
     * @param \Repull\Model\UpdateVrboListingAmenitiesRequestGroupsEssentials|null $general general
     *
     * @return $this
     */
    public function setGeneral(?\Repull\Model\UpdateVrboListingAmenitiesRequestGroupsEssentials $general): static
    {
        if (is_null($general)) {
            throw new InvalidArgumentException('non-nullable general cannot be null');
        }
        $this->container['general'] = $general;

        return $this;
    }

    /**
     * Gets office
     *
     * @return \Repull\Model\UpdateVrboListingAmenitiesRequestGroupsEssentials|null
     */
    public function getOffice(): ?\Repull\Model\UpdateVrboListingAmenitiesRequestGroupsEssentials
    {
        return $this->container['office'];
    }

    /**
     * Sets office
     *
     * @param \Repull\Model\UpdateVrboListingAmenitiesRequestGroupsEssentials|null $office office
     *
     * @return $this
     */
    public function setOffice(?\Repull\Model\UpdateVrboListingAmenitiesRequestGroupsEssentials $office): static
    {
        if (is_null($office)) {
            throw new InvalidArgumentException('non-nullable office cannot be null');
        }
        $this->container['office'] = $office;

        return $this;
    }

    /**
     * Gets kitchen_and_dining
     *
     * @return \Repull\Model\UpdateVrboListingAmenitiesRequestGroupsEssentials|null
     */
    public function getKitchenAndDining(): ?\Repull\Model\UpdateVrboListingAmenitiesRequestGroupsEssentials
    {
        return $this->container['kitchen_and_dining'];
    }

    /**
     * Sets kitchen_and_dining
     *
     * @param \Repull\Model\UpdateVrboListingAmenitiesRequestGroupsEssentials|null $kitchen_and_dining kitchen_and_dining
     *
     * @return $this
     */
    public function setKitchenAndDining(?\Repull\Model\UpdateVrboListingAmenitiesRequestGroupsEssentials $kitchen_and_dining): static
    {
        if (is_null($kitchen_and_dining)) {
            throw new InvalidArgumentException('non-nullable kitchen_and_dining cannot be null');
        }
        $this->container['kitchen_and_dining'] = $kitchen_and_dining;

        return $this;
    }

    /**
     * Gets parking
     *
     * @return \Repull\Model\UpdateVrboListingAmenitiesRequestGroupsEssentials|null
     */
    public function getParking(): ?\Repull\Model\UpdateVrboListingAmenitiesRequestGroupsEssentials
    {
        return $this->container['parking'];
    }

    /**
     * Sets parking
     *
     * @param \Repull\Model\UpdateVrboListingAmenitiesRequestGroupsEssentials|null $parking parking
     *
     * @return $this
     */
    public function setParking(?\Repull\Model\UpdateVrboListingAmenitiesRequestGroupsEssentials $parking): static
    {
        if (is_null($parking)) {
            throw new InvalidArgumentException('non-nullable parking cannot be null');
        }
        $this->container['parking'] = $parking;

        return $this;
    }

    /**
     * Gets pool_and_spa
     *
     * @return \Repull\Model\UpdateVrboListingAmenitiesRequestGroupsEssentials|null
     */
    public function getPoolAndSpa(): ?\Repull\Model\UpdateVrboListingAmenitiesRequestGroupsEssentials
    {
        return $this->container['pool_and_spa'];
    }

    /**
     * Sets pool_and_spa
     *
     * @param \Repull\Model\UpdateVrboListingAmenitiesRequestGroupsEssentials|null $pool_and_spa pool_and_spa
     *
     * @return $this
     */
    public function setPoolAndSpa(?\Repull\Model\UpdateVrboListingAmenitiesRequestGroupsEssentials $pool_and_spa): static
    {
        if (is_null($pool_and_spa)) {
            throw new InvalidArgumentException('non-nullable pool_and_spa cannot be null');
        }
        $this->container['pool_and_spa'] = $pool_and_spa;

        return $this;
    }

    /**
     * Gets sustainability
     *
     * @return \Repull\Model\UpdateVrboListingAmenitiesRequestGroupsEssentials|null
     */
    public function getSustainability(): ?\Repull\Model\UpdateVrboListingAmenitiesRequestGroupsEssentials
    {
        return $this->container['sustainability'];
    }

    /**
     * Sets sustainability
     *
     * @param \Repull\Model\UpdateVrboListingAmenitiesRequestGroupsEssentials|null $sustainability sustainability
     *
     * @return $this
     */
    public function setSustainability(?\Repull\Model\UpdateVrboListingAmenitiesRequestGroupsEssentials $sustainability): static
    {
        if (is_null($sustainability)) {
            throw new InvalidArgumentException('non-nullable sustainability cannot be null');
        }
        $this->container['sustainability'] = $sustainability;

        return $this;
    }

    /**
     * Gets entertainment
     *
     * @return \Repull\Model\UpdateVrboListingAmenitiesRequestGroupsEssentials|null
     */
    public function getEntertainment(): ?\Repull\Model\UpdateVrboListingAmenitiesRequestGroupsEssentials
    {
        return $this->container['entertainment'];
    }

    /**
     * Sets entertainment
     *
     * @param \Repull\Model\UpdateVrboListingAmenitiesRequestGroupsEssentials|null $entertainment entertainment
     *
     * @return $this
     */
    public function setEntertainment(?\Repull\Model\UpdateVrboListingAmenitiesRequestGroupsEssentials $entertainment): static
    {
        if (is_null($entertainment)) {
            throw new InvalidArgumentException('non-nullable entertainment cannot be null');
        }
        $this->container['entertainment'] = $entertainment;

        return $this;
    }

    /**
     * Gets outdoor
     *
     * @return \Repull\Model\UpdateVrboListingAmenitiesRequestGroupsEssentials|null
     */
    public function getOutdoor(): ?\Repull\Model\UpdateVrboListingAmenitiesRequestGroupsEssentials
    {
        return $this->container['outdoor'];
    }

    /**
     * Sets outdoor
     *
     * @param \Repull\Model\UpdateVrboListingAmenitiesRequestGroupsEssentials|null $outdoor outdoor
     *
     * @return $this
     */
    public function setOutdoor(?\Repull\Model\UpdateVrboListingAmenitiesRequestGroupsEssentials $outdoor): static
    {
        if (is_null($outdoor)) {
            throw new InvalidArgumentException('non-nullable outdoor cannot be null');
        }
        $this->container['outdoor'] = $outdoor;

        return $this;
    }

    /**
     * Gets onsite_service
     *
     * @return \Repull\Model\UpdateVrboListingAmenitiesRequestGroupsEssentials|null
     */
    public function getOnsiteService(): ?\Repull\Model\UpdateVrboListingAmenitiesRequestGroupsEssentials
    {
        return $this->container['onsite_service'];
    }

    /**
     * Sets onsite_service
     *
     * @param \Repull\Model\UpdateVrboListingAmenitiesRequestGroupsEssentials|null $onsite_service onsite_service
     *
     * @return $this
     */
    public function setOnsiteService(?\Repull\Model\UpdateVrboListingAmenitiesRequestGroupsEssentials $onsite_service): static
    {
        if (is_null($onsite_service)) {
            throw new InvalidArgumentException('non-nullable onsite_service cannot be null');
        }
        $this->container['onsite_service'] = $onsite_service;

        return $this;
    }

    /**
     * Gets accessibility
     *
     * @return \Repull\Model\UpdateVrboListingAmenitiesRequestGroupsEssentials|null
     */
    public function getAccessibility(): ?\Repull\Model\UpdateVrboListingAmenitiesRequestGroupsEssentials
    {
        return $this->container['accessibility'];
    }

    /**
     * Sets accessibility
     *
     * @param \Repull\Model\UpdateVrboListingAmenitiesRequestGroupsEssentials|null $accessibility accessibility
     *
     * @return $this
     */
    public function setAccessibility(?\Repull\Model\UpdateVrboListingAmenitiesRequestGroupsEssentials $accessibility): static
    {
        if (is_null($accessibility)) {
            throw new InvalidArgumentException('non-nullable accessibility cannot be null');
        }
        $this->container['accessibility'] = $accessibility;

        return $this;
    }

    /**
     * Gets home_safety
     *
     * @return \Repull\Model\UpdateVrboListingAmenitiesRequestGroupsEssentials|null
     */
    public function getHomeSafety(): ?\Repull\Model\UpdateVrboListingAmenitiesRequestGroupsEssentials
    {
        return $this->container['home_safety'];
    }

    /**
     * Sets home_safety
     *
     * @param \Repull\Model\UpdateVrboListingAmenitiesRequestGroupsEssentials|null $home_safety home_safety
     *
     * @return $this
     */
    public function setHomeSafety(?\Repull\Model\UpdateVrboListingAmenitiesRequestGroupsEssentials $home_safety): static
    {
        if (is_null($home_safety)) {
            throw new InvalidArgumentException('non-nullable home_safety cannot be null');
        }
        $this->container['home_safety'] = $home_safety;

        return $this;
    }

    /**
     * Gets safety_devices
     *
     * @return \Repull\Model\UpdateVrboListingAmenitiesRequestGroupsEssentials|null
     */
    public function getSafetyDevices(): ?\Repull\Model\UpdateVrboListingAmenitiesRequestGroupsEssentials
    {
        return $this->container['safety_devices'];
    }

    /**
     * Sets safety_devices
     *
     * @param \Repull\Model\UpdateVrboListingAmenitiesRequestGroupsEssentials|null $safety_devices safety_devices
     *
     * @return $this
     */
    public function setSafetyDevices(?\Repull\Model\UpdateVrboListingAmenitiesRequestGroupsEssentials $safety_devices): static
    {
        if (is_null($safety_devices)) {
            throw new InvalidArgumentException('non-nullable safety_devices cannot be null');
        }
        $this->container['safety_devices'] = $safety_devices;

        return $this;
    }

    /**
     * Gets enhanced_cleaning
     *
     * @return \Repull\Model\UpdateVrboListingAmenitiesRequestGroupsEssentials|null
     */
    public function getEnhancedCleaning(): ?\Repull\Model\UpdateVrboListingAmenitiesRequestGroupsEssentials
    {
        return $this->container['enhanced_cleaning'];
    }

    /**
     * Sets enhanced_cleaning
     *
     * @param \Repull\Model\UpdateVrboListingAmenitiesRequestGroupsEssentials|null $enhanced_cleaning enhanced_cleaning
     *
     * @return $this
     */
    public function setEnhancedCleaning(?\Repull\Model\UpdateVrboListingAmenitiesRequestGroupsEssentials $enhanced_cleaning): static
    {
        if (is_null($enhanced_cleaning)) {
            throw new InvalidArgumentException('non-nullable enhanced_cleaning cannot be null');
        }
        $this->container['enhanced_cleaning'] = $enhanced_cleaning;

        return $this;
    }

    /**
     * Gets location_and_view
     *
     * @return \Repull\Model\UpdateVrboListingAmenitiesRequestGroupsEssentials|null
     */
    public function getLocationAndView(): ?\Repull\Model\UpdateVrboListingAmenitiesRequestGroupsEssentials
    {
        return $this->container['location_and_view'];
    }

    /**
     * Sets location_and_view
     *
     * @param \Repull\Model\UpdateVrboListingAmenitiesRequestGroupsEssentials|null $location_and_view location_and_view
     *
     * @return $this
     */
    public function setLocationAndView(?\Repull\Model\UpdateVrboListingAmenitiesRequestGroupsEssentials $location_and_view): static
    {
        if (is_null($location_and_view)) {
            throw new InvalidArgumentException('non-nullable location_and_view cannot be null');
        }
        $this->container['location_and_view'] = $location_and_view;

        return $this;
    }

    /**
     * Gets local_attractions_and_activities
     *
     * @return \Repull\Model\UpdateVrboListingAmenitiesRequestGroupsEssentials|null
     */
    public function getLocalAttractionsAndActivities(): ?\Repull\Model\UpdateVrboListingAmenitiesRequestGroupsEssentials
    {
        return $this->container['local_attractions_and_activities'];
    }

    /**
     * Sets local_attractions_and_activities
     *
     * @param \Repull\Model\UpdateVrboListingAmenitiesRequestGroupsEssentials|null $local_attractions_and_activities local_attractions_and_activities
     *
     * @return $this
     */
    public function setLocalAttractionsAndActivities(?\Repull\Model\UpdateVrboListingAmenitiesRequestGroupsEssentials $local_attractions_and_activities): static
    {
        if (is_null($local_attractions_and_activities)) {
            throw new InvalidArgumentException('non-nullable local_attractions_and_activities cannot be null');
        }
        $this->container['local_attractions_and_activities'] = $local_attractions_and_activities;

        return $this;
    }

    /**
     * Gets checkout_instructions
     *
     * @return \Repull\Model\UpdateVrboListingAmenitiesRequestGroupsEssentials|null
     */
    public function getCheckoutInstructions(): ?\Repull\Model\UpdateVrboListingAmenitiesRequestGroupsEssentials
    {
        return $this->container['checkout_instructions'];
    }

    /**
     * Sets checkout_instructions
     *
     * @param \Repull\Model\UpdateVrboListingAmenitiesRequestGroupsEssentials|null $checkout_instructions checkout_instructions
     *
     * @return $this
     */
    public function setCheckoutInstructions(?\Repull\Model\UpdateVrboListingAmenitiesRequestGroupsEssentials $checkout_instructions): static
    {
        if (is_null($checkout_instructions)) {
            throw new InvalidArgumentException('non-nullable checkout_instructions cannot be null');
        }
        $this->container['checkout_instructions'] = $checkout_instructions;

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


