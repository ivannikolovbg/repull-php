<?php
/**
 * ConnectProvider
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
 * The unified API for vacation rental tech. Connect Airbnb, Booking.com, Vrbo and more channels, plus 14 PMS platforms, through one REST API.  ## Designed for AI agents Every error response on this API includes machine-parseable fields so an LLM (Claude in MCP, Cursor, Cline, GPT, etc.) can self-recover without escalating to a human: - `error.code` — stable string identifier (e.g. `invalid_params`, `rate_limit_exceeded`) - `error.message` — human-readable cause - `error.fix` — exact recovery steps (e.g. \"Pass `check_in_after` as ISO 8601: `?check_in_after=2026-01-15`\") - `error.docs_url` — link to the canonical write-up at `https://repull.dev/docs/errors/{code}` - `error.request_id` — id to correlate with server-side logs - `error.field` / `error.value_received` / `error.valid_values` / `error.did_you_mean` — when the error is parameter-specific - `error.retry_after` — seconds to wait before retrying (rate-limit + transient upstream)  `Access-Control-Expose-Headers` lists `x-request-id` and the `X-RateLimit-*` family so browsers can read them on cross-origin responses.  ## Quick Start 1. Get an API key at https://repull.dev/dashboard 2. Connect a PMS: `POST /v1/connect/{provider}` 3. List properties: `GET /v1/properties` 4. Get reservations: `GET /v1/reservations`  ## Authentication All requests require a Bearer token: ``` Authorization: Bearer sk_live_YOUR_API_KEY ```  ## Request Correlation (X-Request-ID) Every response carries an `X-Request-ID` header, e.g. `X-Request-ID: req_01HXY...`. Include this id in support tickets and bug reports — we can trace the full request lifecycle (auth, rate limit, handler, downstream calls, log row) from a single id.  You may set the header on the inbound request to forward your own trace id; we will echo it back instead of generating a new one. Accepted format: `^[\\\\w.-]{1,128}$`.  The id is also embedded in error envelopes as `request_id` so server-side log diffs work even when the response headers are stripped by an intermediate proxy.  ## Rate Limits The public API enforces a per-API-key sliding-window rate limit on top of the per-tier monthly + daily-AI quotas.  **Default policy:** 600 requests per 60 seconds, per API key. Sliding window — there is no fixed-minute boundary you can burst across.  Every response includes:  | Header | Meaning | |---|---| | `X-RateLimit-Limit` | Requests permitted in the current window. | | `X-RateLimit-Remaining` | Requests left in the current window after this call. | | `X-RateLimit-Reset` | Unix epoch (seconds) when the next slot opens. | | `X-RateLimit-Policy` | Machine-readable policy descriptor, e.g. `600;w=60`. | | `Retry-After` | Seconds to wait before retrying. **Only present on 429 responses.** |  **On 429 (rate_limit_exceeded):** the response body matches the standard error envelope with `code: \"rate_limit_exceeded\"`, plus `limit`, `window_seconds`, `retry_after`, and `request_id` fields. SDKs MUST honor `Retry-After` and use exponential backoff with jitter on subsequent retries — never a tight loop.  Recommended backoff: ``` sleep_ms = (Retry-After * 1000) + random(0..250) ```  Monthly + daily-AI tier quotas (`free`, `starter`, `custom`) are enforced separately and also surface as 429s; they include `tier`, `scope`, `limit`, `used` and `resets_at` fields.  **The way past a request quota.** The monthly quota refusal (`429 rate_limit_exceeded` with `scope: \"monthly\"`), `402 credits_exhausted` and `429 daily_limit_exceeded` also carry `upgrade` (the cheapest plan that covers your usage: its exact monthly price for your listings, its request quotas and a `checkout_url` that opens checkout for that price; `plan: \"custom\"` with a `contact_email` when no self-serve plan covers it), `credits` (pay-as-you-go: `usd_per_1000_requests`, `top_up_url`, your `balance_usd`), `resets_at` and `projected_monthly_requests`. The per-minute 429 (`scope: \"per_api_key\"`) carries none of these: wait `Retry-After`.  ## Plan listing limits Each plan includes a number of **active listings**:  | Tier | Active listings | |---|---| | `free` | 3 | | `starter` | no cap: 10 included, then billed per listing | | `custom` | no cap |  Going over the plan never blocks your API calls. Listings beyond the plan are **held back**: they stay connected and keep syncing, but they are not served. A held-back listing answers `403 listing_inactive` with `reason: \"plan_limit\"`, and collection routes leave it out. Every JSON response then carries a `planNotice` and the `X-Repull-Listings-Held-Back` header, so you know there is more to see.  Choose which listings are active with `POST /v1/listings/status`. Upgrading at `https://repull.dev/dashboard/billing` lifts the cap but does not switch held-back listings on: you choose which to activate afterwards, with the same call or from the dashboard, and each one you switch on is billed from then.  Only a call that would itself take you over the plan is refused, with **`402 listings_limit_exceeded`**: activating listings (`PATCH /v1/listings/{id}` with `{ \"active\": true }`, `POST /v1/listings/status`) or creating one (`POST /v1/listings`). Nothing else changes. The envelope carries the tier, the limit, the active count and an upgrade link next to the standard `code` / `message` / `fix` / `docs_url` / `request_id`.  If an account has more active listings than its plan (for example after a downgrade), calls are still served and `planNotice` says so (code \"listings_over_plan\").
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
 * ConnectProvider Class Doc Comment
 *
 * @description A channel the multi-channel Connect picker can show. Returned by &#x60;GET /v1/connect/providers&#x60; and consumed by SDKs that render their own picker UI.
 * @package  Repull
 * @author   OpenAPI Generator team
 * @link     https://openapi-generator.tech
 * @implements ArrayAccess<string, mixed>
 */
class ConnectProvider implements ModelInterface, ArrayAccess, JsonSerializable
{
    public const DISCRIMINATOR = null;

    /**
     * The original name of the model.
     *
     * @var string
     */
    protected static string $openAPIModelName = 'ConnectProvider';

    /**
     * Array of property to type mappings. Used for (de)serialization
     *
     * @var array<string, string>
     */
    protected static array $openAPITypes = [
        'id' => 'string',
        'display_name' => 'string',
        'category' => 'string',
        'connect_pattern' => 'string',
        'status' => 'string',
        'logo_url' => 'string',
        'description' => 'string',
        'docs_url' => 'string',
        'aliases' => 'string[]',
        'migration_capabilities' => 'array<string,mixed>'
    ];

    /**
     * Array of property to format mappings. Used for (de)serialization
     *
     * @var array<string, string|null>
     */
    protected static array $openAPIFormats = [
        'id' => null,
        'display_name' => null,
        'category' => null,
        'connect_pattern' => null,
        'status' => null,
        'logo_url' => 'uri',
        'description' => null,
        'docs_url' => 'uri',
        'aliases' => null,
        'migration_capabilities' => null
    ];

    /**
     * Array of nullable properties. Used for (de)serialization
     *
     * @var array<string, bool>
     */
    protected static array $openAPINullables = [
        'id' => false,
        'display_name' => false,
        'category' => false,
        'connect_pattern' => false,
        'status' => false,
        'logo_url' => false,
        'description' => false,
        'docs_url' => false,
        'aliases' => true,
        'migration_capabilities' => true
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
        'display_name' => 'displayName',
        'category' => 'category',
        'connect_pattern' => 'connectPattern',
        'status' => 'status',
        'logo_url' => 'logoUrl',
        'description' => 'description',
        'docs_url' => 'docsUrl',
        'aliases' => 'aliases',
        'migration_capabilities' => 'migrationCapabilities'
    ];

    /**
     * Array of attributes to setter functions (for deserialization of responses)
     *
     * @var array<string, string>
     */
    protected static array $setters = [
        'id' => 'setId',
        'display_name' => 'setDisplayName',
        'category' => 'setCategory',
        'connect_pattern' => 'setConnectPattern',
        'status' => 'setStatus',
        'logo_url' => 'setLogoUrl',
        'description' => 'setDescription',
        'docs_url' => 'setDocsUrl',
        'aliases' => 'setAliases',
        'migration_capabilities' => 'setMigrationCapabilities'
    ];

    /**
     * Array of attributes to getter functions (for serialization of requests)
     *
     * @var array<string, string>
     */
    protected static array $getters = [
        'id' => 'getId',
        'display_name' => 'getDisplayName',
        'category' => 'getCategory',
        'connect_pattern' => 'getConnectPattern',
        'status' => 'getStatus',
        'logo_url' => 'getLogoUrl',
        'description' => 'getDescription',
        'docs_url' => 'getDocsUrl',
        'aliases' => 'getAliases',
        'migration_capabilities' => 'getMigrationCapabilities'
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

    public const CATEGORY_OTA = 'ota';
    public const CATEGORY_PMS = 'pms';
    public const CONNECT_PATTERN_OAUTH = 'oauth';
    public const CONNECT_PATTERN_CREDENTIALS = 'credentials';
    public const CONNECT_PATTERN_ACTIVATION = 'activation';
    public const CONNECT_PATTERN_CLAIM = 'claim';
    public const CONNECT_PATTERN_EXTRANET = 'extranet';
    public const STATUS_LIVE = 'live';
    public const STATUS_BETA = 'beta';
    public const STATUS_COMING_SOON = 'coming-soon';
    public const STATUS_REQUEST_ONLY = 'request-only';

    /**
     * Gets allowable values of the enum
     *
     * @return string[]
     */
    public static function getCategoryAllowableValues()
    {
        return [
            self::CATEGORY_OTA,
            self::CATEGORY_PMS,
        ];
    }

    /**
     * Gets allowable values of the enum
     *
     * @return string[]
     */
    public static function getConnectPatternAllowableValues()
    {
        return [
            self::CONNECT_PATTERN_OAUTH,
            self::CONNECT_PATTERN_CREDENTIALS,
            self::CONNECT_PATTERN_ACTIVATION,
            self::CONNECT_PATTERN_CLAIM,
            self::CONNECT_PATTERN_EXTRANET,
        ];
    }

    /**
     * Gets allowable values of the enum
     *
     * @return string[]
     */
    public static function getStatusAllowableValues()
    {
        return [
            self::STATUS_LIVE,
            self::STATUS_BETA,
            self::STATUS_COMING_SOON,
            self::STATUS_REQUEST_ONLY,
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
        $this->setIfExists('display_name', $data ?? [], null);
        $this->setIfExists('category', $data ?? [], null);
        $this->setIfExists('connect_pattern', $data ?? [], null);
        $this->setIfExists('status', $data ?? [], null);
        $this->setIfExists('logo_url', $data ?? [], null);
        $this->setIfExists('description', $data ?? [], null);
        $this->setIfExists('docs_url', $data ?? [], null);
        $this->setIfExists('aliases', $data ?? [], null);
        $this->setIfExists('migration_capabilities', $data ?? [], null);
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
        if ($this->container['display_name'] === null) {
            $invalidProperties[] = "'display_name' can't be null";
        }
        if ($this->container['category'] === null) {
            $invalidProperties[] = "'category' can't be null";
        }
        $allowedValues = self::getCategoryAllowableValues();
        if (!is_null($this->container['category']) && !in_array($this->container['category'], $allowedValues, true)) {
            $invalidProperties[] = sprintf(
                "invalid value '%s' for 'category', must be one of '%s'",
                $this->container['category'],
                implode("', '", $allowedValues)
            );
        }

        if ($this->container['connect_pattern'] === null) {
            $invalidProperties[] = "'connect_pattern' can't be null";
        }
        $allowedValues = self::getConnectPatternAllowableValues();
        if (!is_null($this->container['connect_pattern']) && !in_array($this->container['connect_pattern'], $allowedValues, true)) {
            $invalidProperties[] = sprintf(
                "invalid value '%s' for 'connect_pattern', must be one of '%s'",
                $this->container['connect_pattern'],
                implode("', '", $allowedValues)
            );
        }

        if ($this->container['status'] === null) {
            $invalidProperties[] = "'status' can't be null";
        }
        $allowedValues = self::getStatusAllowableValues();
        if (!is_null($this->container['status']) && !in_array($this->container['status'], $allowedValues, true)) {
            $invalidProperties[] = sprintf(
                "invalid value '%s' for 'status', must be one of '%s'",
                $this->container['status'],
                implode("', '", $allowedValues)
            );
        }

        if ($this->container['logo_url'] === null) {
            $invalidProperties[] = "'logo_url' can't be null";
        }
        if ($this->container['description'] === null) {
            $invalidProperties[] = "'description' can't be null";
        }
        if ($this->container['docs_url'] === null) {
            $invalidProperties[] = "'docs_url' can't be null";
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
     * @param string $id Stable identifier passed back to /select-provider and used in /v1/connect/{provider} routes.
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
     * Gets display_name
     *
     * @return string
     */
    public function getDisplayName(): string
    {
        return $this->container['display_name'];
    }

    /**
     * Sets display_name
     *
     * @param string $display_name display_name
     *
     * @return $this
     */
    public function setDisplayName(string $display_name): static
    {
        if (is_null($display_name)) {
            throw new InvalidArgumentException('non-nullable display_name cannot be null');
        }
        $this->container['display_name'] = $display_name;

        return $this;
    }

    /**
     * Gets category
     *
     * @return string
     */
    public function getCategory(): string
    {
        return $this->container['category'];
    }

    /**
     * Sets category
     *
     * @param string $category Channel category — OTAs are listing marketplaces; PMSes are property management systems.
     *
     * @return $this
     */
    public function setCategory(string $category): static
    {
        if (is_null($category)) {
            throw new InvalidArgumentException('non-nullable category cannot be null');
        }
        // (relax-enums.php) accept unknown enum values for forward compat
        $this->container['category'] = $category;

        return $this;
    }

    /**
     * Gets connect_pattern
     *
     * @return string
     */
    public function getConnectPattern(): string
    {
        return $this->container['connect_pattern'];
    }

    /**
     * Sets connect_pattern
     *
     * @param string $connect_pattern How the host is connected. `oauth`: provider-side consent screen. `credentials`: hosted form collects API keys. `activation`: push-only handshake. `claim`: connectivity-provider designation in the channel's Extranet (Booking.com). `extranet`: the host signs in with their Booking.com Extranet user.
     *
     * @return $this
     */
    public function setConnectPattern(string $connect_pattern): static
    {
        if (is_null($connect_pattern)) {
            throw new InvalidArgumentException('non-nullable connect_pattern cannot be null');
        }
        // (relax-enums.php) accept unknown enum values for forward compat
        $this->container['connect_pattern'] = $connect_pattern;

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
     * @param string $status Pickers should hide / disable `coming-soon` cards. `beta` cards are clickable but show a Beta pill. `request-only` is a PMS Repull doesn't connect yet, listed only with `includeRequestOnly`: picking it collects the host's access details for the Repull team, and the session ends as `requested` instead of connecting.
     *
     * @return $this
     */
    public function setStatus(string $status): static
    {
        if (is_null($status)) {
            throw new InvalidArgumentException('non-nullable status cannot be null');
        }
        // (relax-enums.php) accept unknown enum values for forward compat
        $this->container['status'] = $status;

        return $this;
    }

    /**
     * Gets logo_url
     *
     * @return string
     */
    public function getLogoUrl(): string
    {
        return $this->container['logo_url'];
    }

    /**
     * Sets logo_url
     *
     * @param string $logo_url Logo URL — Clearbit stand-in until self-hosted SVGs land.
     *
     * @return $this
     */
    public function setLogoUrl(string $logo_url): static
    {
        if (is_null($logo_url)) {
            throw new InvalidArgumentException('non-nullable logo_url cannot be null');
        }
        $this->container['logo_url'] = $logo_url;

        return $this;
    }

    /**
     * Gets description
     *
     * @return string
     */
    public function getDescription(): string
    {
        return $this->container['description'];
    }

    /**
     * Sets description
     *
     * @param string $description description
     *
     * @return $this
     */
    public function setDescription(string $description): static
    {
        if (is_null($description)) {
            throw new InvalidArgumentException('non-nullable description cannot be null');
        }
        $this->container['description'] = $description;

        return $this;
    }

    /**
     * Gets docs_url
     *
     * @return string
     */
    public function getDocsUrl(): string
    {
        return $this->container['docs_url'];
    }

    /**
     * Sets docs_url
     *
     * @param string $docs_url docs_url
     *
     * @return $this
     */
    public function setDocsUrl(string $docs_url): static
    {
        if (is_null($docs_url)) {
            throw new InvalidArgumentException('non-nullable docs_url cannot be null');
        }
        $this->container['docs_url'] = $docs_url;

        return $this;
    }

    /**
     * Gets aliases
     *
     * @return string[]|null
     */
    public function getAliases(): ?array
    {
        return $this->container['aliases'];
    }

    /**
     * Sets aliases
     *
     * @param string[]|null $aliases Optional friendly aliases the picker's search box can match.
     *
     * @return $this
     */
    public function setAliases(?array $aliases): static
    {
        if (is_null($aliases)) {
            array_push($this->openAPINullablesSetToNull, 'aliases');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('aliases', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['aliases'] = $aliases;

        return $this;
    }

    /**
     * Gets migration_capabilities
     *
     * @return array<string,mixed>|null
     */
    public function getMigrationCapabilities(): ?array
    {
        return $this->container['migration_capabilities'];
    }

    /**
     * Sets migration_capabilities
     *
     * @param array<string,mixed>|null $migration_capabilities PMS providers: what Repull Migrate can carry across, per entity — `{ read: { listings: { level, notes }, … }, write: { … } }` with `level` `full` | `partial` | `none`. `null` for channels (OTAs).
     *
     * @return $this
     */
    public function setMigrationCapabilities(?array $migration_capabilities): static
    {
        if (is_null($migration_capabilities)) {
            array_push($this->openAPINullablesSetToNull, 'migration_capabilities');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('migration_capabilities', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['migration_capabilities'] = $migration_capabilities;

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


