<?php
/**
 * TiketProperty
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
 * TiketProperty Class Doc Comment
 *
 * @description A tiket.com property connected to this workspace. A property is a hotel or building; its room types are what guests book, and each room type maps to one Repull listing.
 * @package  Repull
 * @author   OpenAPI Generator team
 * @link     https://openapi-generator.tech
 * @implements ArrayAccess<string, mixed>
 */
class TiketProperty implements ModelInterface, ArrayAccess, JsonSerializable
{
    public const DISCRIMINATOR = null;

    /**
     * The original name of the model.
     *
     * @var string
     */
    protected static string $openAPIModelName = 'TiketProperty';

    /**
     * Array of property to type mappings. Used for (de)serialization
     *
     * @var array<string, string>
     */
    protected static array $openAPITypes = [
        'property_id' => 'string',
        'partner_hotel_code' => 'string',
        'name' => 'string',
        'currency' => 'string',
        'price_model' => 'string',
        'activation_status' => 'string',
        'content_mapped' => 'bool',
        'sync_enabled' => 'bool',
        'last_products_at' => '\DateTime',
        'last_error' => 'string',
        'mapping_status' => 'string',
        'listings' => '\Repull\Model\TiketPropertyListingsInner[]',
        'rooms' => '\Repull\Model\TiketRoom[]'
    ];

    /**
     * Array of property to format mappings. Used for (de)serialization
     *
     * @var array<string, string|null>
     */
    protected static array $openAPIFormats = [
        'property_id' => null,
        'partner_hotel_code' => null,
        'name' => null,
        'currency' => null,
        'price_model' => null,
        'activation_status' => null,
        'content_mapped' => null,
        'sync_enabled' => null,
        'last_products_at' => 'date-time',
        'last_error' => null,
        'mapping_status' => null,
        'listings' => null,
        'rooms' => null
    ];

    /**
     * Array of nullable properties. Used for (de)serialization
     *
     * @var array<string, bool>
     */
    protected static array $openAPINullables = [
        'property_id' => true,
        'partner_hotel_code' => true,
        'name' => true,
        'currency' => true,
        'price_model' => true,
        'activation_status' => true,
        'content_mapped' => true,
        'sync_enabled' => true,
        'last_products_at' => true,
        'last_error' => true,
        'mapping_status' => false,
        'listings' => false,
        'rooms' => false
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
        'property_id' => 'propertyId',
        'partner_hotel_code' => 'partnerHotelCode',
        'name' => 'name',
        'currency' => 'currency',
        'price_model' => 'priceModel',
        'activation_status' => 'activationStatus',
        'content_mapped' => 'contentMapped',
        'sync_enabled' => 'syncEnabled',
        'last_products_at' => 'lastProductsAt',
        'last_error' => 'lastError',
        'mapping_status' => 'mappingStatus',
        'listings' => 'listings',
        'rooms' => 'rooms'
    ];

    /**
     * Array of attributes to setter functions (for deserialization of responses)
     *
     * @var array<string, string>
     */
    protected static array $setters = [
        'property_id' => 'setPropertyId',
        'partner_hotel_code' => 'setPartnerHotelCode',
        'name' => 'setName',
        'currency' => 'setCurrency',
        'price_model' => 'setPriceModel',
        'activation_status' => 'setActivationStatus',
        'content_mapped' => 'setContentMapped',
        'sync_enabled' => 'setSyncEnabled',
        'last_products_at' => 'setLastProductsAt',
        'last_error' => 'setLastError',
        'mapping_status' => 'setMappingStatus',
        'listings' => 'setListings',
        'rooms' => 'setRooms'
    ];

    /**
     * Array of attributes to getter functions (for serialization of requests)
     *
     * @var array<string, string>
     */
    protected static array $getters = [
        'property_id' => 'getPropertyId',
        'partner_hotel_code' => 'getPartnerHotelCode',
        'name' => 'getName',
        'currency' => 'getCurrency',
        'price_model' => 'getPriceModel',
        'activation_status' => 'getActivationStatus',
        'content_mapped' => 'getContentMapped',
        'sync_enabled' => 'getSyncEnabled',
        'last_products_at' => 'getLastProductsAt',
        'last_error' => 'getLastError',
        'mapping_status' => 'getMappingStatus',
        'listings' => 'getListings',
        'rooms' => 'getRooms'
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

    public const MAPPING_STATUS_MAPPED = 'mapped';
    public const MAPPING_STATUS_PARTIAL = 'partial';
    public const MAPPING_STATUS_UNMAPPED = 'unmapped';

    /**
     * Gets allowable values of the enum
     *
     * @return string[]
     */
    public static function getMappingStatusAllowableValues()
    {
        return [
            self::MAPPING_STATUS_MAPPED,
            self::MAPPING_STATUS_PARTIAL,
            self::MAPPING_STATUS_UNMAPPED,
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
        $this->setIfExists('property_id', $data ?? [], null);
        $this->setIfExists('partner_hotel_code', $data ?? [], null);
        $this->setIfExists('name', $data ?? [], null);
        $this->setIfExists('currency', $data ?? [], null);
        $this->setIfExists('price_model', $data ?? [], null);
        $this->setIfExists('activation_status', $data ?? [], null);
        $this->setIfExists('content_mapped', $data ?? [], null);
        $this->setIfExists('sync_enabled', $data ?? [], null);
        $this->setIfExists('last_products_at', $data ?? [], null);
        $this->setIfExists('last_error', $data ?? [], null);
        $this->setIfExists('mapping_status', $data ?? [], null);
        $this->setIfExists('listings', $data ?? [], null);
        $this->setIfExists('rooms', $data ?? [], null);
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

        $allowedValues = self::getMappingStatusAllowableValues();
        if (!is_null($this->container['mapping_status']) && !in_array($this->container['mapping_status'], $allowedValues, true)) {
            $invalidProperties[] = sprintf(
                "invalid value '%s' for 'mapping_status', must be one of '%s'",
                $this->container['mapping_status'],
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
     * @param string|null $property_id tiket.com Hotel ID. `null` for a property Repull created that tiket.com has not assigned an id to yet.
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
     * @param string|null $partner_hotel_code Repull's code for a property Repull created on tiket.com.
     *
     * @return $this
     */
    public function setPartnerHotelCode(?string $partner_hotel_code): static
    {
        if (is_null($partner_hotel_code)) {
            array_push($this->openAPINullablesSetToNull, 'partner_hotel_code');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('partner_hotel_code', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['partner_hotel_code'] = $partner_hotel_code;

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
     * Gets price_model
     *
     * @return string|null
     */
    public function getPriceModel(): ?string
    {
        return $this->container['price_model'];
    }

    /**
     * Sets price_model
     *
     * @param string|null $price_model `Room Based` (one `price` per night) or `Occupancy Based` (`occupancyPrices` per number of guests). Set per property in the tiket.com extranet.
     *
     * @return $this
     */
    public function setPriceModel(?string $price_model): static
    {
        if (is_null($price_model)) {
            array_push($this->openAPINullablesSetToNull, 'price_model');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('price_model', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['price_model'] = $price_model;

        return $this;
    }

    /**
     * Gets activation_status
     *
     * @return string|null
     */
    public function getActivationStatus(): ?string
    {
        return $this->container['activation_status'];
    }

    /**
     * Sets activation_status
     *
     * @param string|null $activation_status Whether tiket.com has activated Repull as the property's channel manager, `awaiting_activation` until tiket.com activates Repull, then `active`; `disconnected` after `disconnect`.
     *
     * @return $this
     */
    public function setActivationStatus(?string $activation_status): static
    {
        if (is_null($activation_status)) {
            array_push($this->openAPINullablesSetToNull, 'activation_status');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('activation_status', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['activation_status'] = $activation_status;

        return $this;
    }

    /**
     * Gets content_mapped
     *
     * @return bool|null
     */
    public function getContentMapped(): ?bool
    {
        return $this->container['content_mapped'];
    }

    /**
     * Sets content_mapped
     *
     * @param bool|null $content_mapped `true` when tiket.com has linked Repull's codes to the property, so `POST /v1/listings/{id}/publish/tiket` can send content.
     *
     * @return $this
     */
    public function setContentMapped(?bool $content_mapped): static
    {
        if (is_null($content_mapped)) {
            array_push($this->openAPINullablesSetToNull, 'content_mapped');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('content_mapped', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['content_mapped'] = $content_mapped;

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
     * @param bool|null $sync_enabled Whether calendar changes are sent to tiket.com automatically (`set-sync`).
     *
     * @return $this
     */
    public function setSyncEnabled(?bool $sync_enabled): static
    {
        if (is_null($sync_enabled)) {
            array_push($this->openAPINullablesSetToNull, 'sync_enabled');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('sync_enabled', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['sync_enabled'] = $sync_enabled;

        return $this;
    }

    /**
     * Gets last_products_at
     *
     * @return \DateTime|null
     */
    public function getLastProductsAt(): ?\DateTime
    {
        return $this->container['last_products_at'];
    }

    /**
     * Sets last_products_at
     *
     * @param \DateTime|null $last_products_at When room types and rate plans were last read from tiket.com.
     *
     * @return $this
     */
    public function setLastProductsAt(?\DateTime $last_products_at): static
    {
        if (is_null($last_products_at)) {
            array_push($this->openAPINullablesSetToNull, 'last_products_at');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('last_products_at', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['last_products_at'] = $last_products_at;

        return $this;
    }

    /**
     * Gets last_error
     *
     * @return string|null
     */
    public function getLastError(): ?string
    {
        return $this->container['last_error'];
    }

    /**
     * Sets last_error
     *
     * @param string|null $last_error last_error
     *
     * @return $this
     */
    public function setLastError(?string $last_error): static
    {
        if (is_null($last_error)) {
            array_push($this->openAPINullablesSetToNull, 'last_error');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('last_error', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['last_error'] = $last_error;

        return $this;
    }

    /**
     * Gets mapping_status
     *
     * @return string|null
     */
    public function getMappingStatus(): ?string
    {
        return $this->container['mapping_status'];
    }

    /**
     * Sets mapping_status
     *
     * @param string|null $mapping_status `mapped` — every room type has a listing; `partial` — some do; `unmapped` — none (a real onboarding state, not an error).
     *
     * @return $this
     */
    public function setMappingStatus(?string $mapping_status): static
    {
        if (is_null($mapping_status)) {
            throw new InvalidArgumentException('non-nullable mapping_status cannot be null');
        }
        // (relax-enums.php) accept unknown enum values for forward compat
        $this->container['mapping_status'] = $mapping_status;

        return $this;
    }

    /**
     * Gets listings
     *
     * @return \Repull\Model\TiketPropertyListingsInner[]|null
     */
    public function getListings(): ?array
    {
        return $this->container['listings'];
    }

    /**
     * Sets listings
     *
     * @param \Repull\Model\TiketPropertyListingsInner[]|null $listings listings
     *
     * @return $this
     */
    public function setListings(?array $listings): static
    {
        if (is_null($listings)) {
            throw new InvalidArgumentException('non-nullable listings cannot be null');
        }
        $this->container['listings'] = $listings;

        return $this;
    }

    /**
     * Gets rooms
     *
     * @return \Repull\Model\TiketRoom[]|null
     */
    public function getRooms(): ?array
    {
        return $this->container['rooms'];
    }

    /**
     * Sets rooms
     *
     * @param \Repull\Model\TiketRoom[]|null $rooms rooms
     *
     * @return $this
     */
    public function setRooms(?array $rooms): static
    {
        if (is_null($rooms)) {
            throw new InvalidArgumentException('non-nullable rooms cannot be null');
        }
        $this->container['rooms'] = $rooms;

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


