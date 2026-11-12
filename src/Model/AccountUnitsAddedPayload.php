<?php
/**
 * AccountUnitsAddedPayload
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
 * AccountUnitsAddedPayload Class Doc Comment
 *
 * @description Payload for &#x60;account.units.added&#x60;. A connected Booking.com Extranet login gained a property (the hotel added the same Booking.com user to it) or a room on a property it already had. One event per property; never for the first import of a fresh connection (&#x60;connect.session.completed&#x60; and &#x60;account.import.*&#x60; cover that), never twice for the same rooms.
 * @package  Repull
 * @author   OpenAPI Generator team
 * @link     https://openapi-generator.tech
 * @implements ArrayAccess<string, mixed>
 */
class AccountUnitsAddedPayload implements ModelInterface, ArrayAccess, JsonSerializable
{
    public const DISCRIMINATOR = null;

    /**
     * The original name of the model.
     *
     * @var string
     */
    protected static string $openAPIModelName = 'AccountUnitsAddedPayload';

    /**
     * Array of property to type mappings. Used for (de)serialization
     *
     * @var array<string, string>
     */
    protected static array $openAPITypes = [
        'workspace_id' => 'int',
        'provider' => 'string',
        'account_id' => 'string',
        'connection_id' => 'string',
        'handle' => 'string',
        'hotels' => '\Repull\Model\AccountUnitsAddedPayloadHotelsInner[]',
        'units' => '\Repull\Model\AccountUnitsAddedPayloadUnitsInner[]',
        'added_at' => '\DateTime',
        'next_steps' => '\Repull\Model\AccountUnitsAddedPayloadNextStepsInner[]'
    ];

    /**
     * Array of property to format mappings. Used for (de)serialization
     *
     * @var array<string, string|null>
     */
    protected static array $openAPIFormats = [
        'workspace_id' => null,
        'provider' => null,
        'account_id' => null,
        'connection_id' => null,
        'handle' => null,
        'hotels' => null,
        'units' => null,
        'added_at' => 'date-time',
        'next_steps' => null
    ];

    /**
     * Array of nullable properties. Used for (de)serialization
     *
     * @var array<string, bool>
     */
    protected static array $openAPINullables = [
        'workspace_id' => false,
        'provider' => false,
        'account_id' => true,
        'connection_id' => true,
        'handle' => false,
        'hotels' => false,
        'units' => false,
        'added_at' => false,
        'next_steps' => false
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
        'workspace_id' => 'workspaceId',
        'provider' => 'provider',
        'account_id' => 'accountId',
        'connection_id' => 'connectionId',
        'handle' => 'handle',
        'hotels' => 'hotels',
        'units' => 'units',
        'added_at' => 'addedAt',
        'next_steps' => 'nextSteps'
    ];

    /**
     * Array of attributes to setter functions (for deserialization of responses)
     *
     * @var array<string, string>
     */
    protected static array $setters = [
        'workspace_id' => 'setWorkspaceId',
        'provider' => 'setProvider',
        'account_id' => 'setAccountId',
        'connection_id' => 'setConnectionId',
        'handle' => 'setHandle',
        'hotels' => 'setHotels',
        'units' => 'setUnits',
        'added_at' => 'setAddedAt',
        'next_steps' => 'setNextSteps'
    ];

    /**
     * Array of attributes to getter functions (for serialization of requests)
     *
     * @var array<string, string>
     */
    protected static array $getters = [
        'workspace_id' => 'getWorkspaceId',
        'provider' => 'getProvider',
        'account_id' => 'getAccountId',
        'connection_id' => 'getConnectionId',
        'handle' => 'getHandle',
        'hotels' => 'getHotels',
        'units' => 'getUnits',
        'added_at' => 'getAddedAt',
        'next_steps' => 'getNextSteps'
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

    public const PROVIDER_BOOKING_EXTRANET = 'booking_extranet';

    /**
     * Gets allowable values of the enum
     *
     * @return string[]
     */
    public static function getProviderAllowableValues()
    {
        return [
            self::PROVIDER_BOOKING_EXTRANET,
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
        $this->setIfExists('workspace_id', $data ?? [], null);
        $this->setIfExists('provider', $data ?? [], null);
        $this->setIfExists('account_id', $data ?? [], null);
        $this->setIfExists('connection_id', $data ?? [], null);
        $this->setIfExists('handle', $data ?? [], null);
        $this->setIfExists('hotels', $data ?? [], null);
        $this->setIfExists('units', $data ?? [], null);
        $this->setIfExists('added_at', $data ?? [], null);
        $this->setIfExists('next_steps', $data ?? [], null);
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

        if ($this->container['workspace_id'] === null) {
            $invalidProperties[] = "'workspace_id' can't be null";
        }
        if ($this->container['provider'] === null) {
            $invalidProperties[] = "'provider' can't be null";
        }
        $allowedValues = self::getProviderAllowableValues();
        if (!is_null($this->container['provider']) && !in_array($this->container['provider'], $allowedValues, true)) {
            $invalidProperties[] = sprintf(
                "invalid value '%s' for 'provider', must be one of '%s'",
                $this->container['provider'],
                implode("', '", $allowedValues)
            );
        }

        if ($this->container['account_id'] === null && !$this->isNullableSetToNull('account_id')) {
            $invalidProperties[] = "'account_id' is required";
        }
        if ($this->container['connection_id'] === null && !$this->isNullableSetToNull('connection_id')) {
            $invalidProperties[] = "'connection_id' is required";
        }
        if ($this->container['handle'] === null) {
            $invalidProperties[] = "'handle' can't be null";
        }
        if ($this->container['hotels'] === null) {
            $invalidProperties[] = "'hotels' can't be null";
        }
        if ($this->container['units'] === null) {
            $invalidProperties[] = "'units' can't be null";
        }
        if ($this->container['added_at'] === null) {
            $invalidProperties[] = "'added_at' can't be null";
        }
        if ($this->container['next_steps'] === null) {
            $invalidProperties[] = "'next_steps' can't be null";
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
     * Gets workspace_id
     *
     * @return int
     */
    public function getWorkspaceId(): int
    {
        return $this->container['workspace_id'];
    }

    /**
     * Sets workspace_id
     *
     * @param int $workspace_id The workspace the login is connected in.
     *
     * @return $this
     */
    public function setWorkspaceId(int $workspace_id): static
    {
        if (is_null($workspace_id)) {
            throw new InvalidArgumentException('non-nullable workspace_id cannot be null');
        }
        $this->container['workspace_id'] = $workspace_id;

        return $this;
    }

    /**
     * Gets provider
     *
     * @return string
     */
    public function getProvider(): string
    {
        return $this->container['provider'];
    }

    /**
     * Sets provider
     *
     * @param string $provider The channel. Today only Booking.com Extranet logins send this event.
     *
     * @return $this
     */
    public function setProvider(string $provider): static
    {
        if (is_null($provider)) {
            throw new InvalidArgumentException('non-nullable provider cannot be null');
        }
        // (relax-enums.php) accept unknown enum values for forward compat
        $this->container['provider'] = $provider;

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
     * @param string|null $account_id The Booking.com account id behind the Extranet login — the same value as `account.externalAccountId`.
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
     * Gets connection_id
     *
     * @return string|null
     */
    public function getConnectionId(): ?string
    {
        return $this->container['connection_id'];
    }

    /**
     * Sets connection_id
     *
     * @param string|null $connection_id Our connection record id: the `X-Account-Id` value, the same as `account.id`.
     *
     * @return $this
     */
    public function setConnectionId(?string $connection_id): static
    {
        if (is_null($connection_id)) {
            array_push($this->openAPINullablesSetToNull, 'connection_id');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('connection_id', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['connection_id'] = $connection_id;

        return $this;
    }

    /**
     * Gets handle
     *
     * @return string
     */
    public function getHandle(): string
    {
        return $this->container['handle'];
    }

    /**
     * Sets handle
     *
     * @param string $handle The connection handle the units and mappings endpoints take: `booking_extranet:{accountId}`.
     *
     * @return $this
     */
    public function setHandle(string $handle): static
    {
        if (is_null($handle)) {
            throw new InvalidArgumentException('non-nullable handle cannot be null');
        }
        $this->container['handle'] = $handle;

        return $this;
    }

    /**
     * Gets hotels
     *
     * @return \Repull\Model\AccountUnitsAddedPayloadHotelsInner[]
     */
    public function getHotels(): array
    {
        return $this->container['hotels'];
    }

    /**
     * Sets hotels
     *
     * @param \Repull\Model\AccountUnitsAddedPayloadHotelsInner[] $hotels The property the new units belong to.
     *
     * @return $this
     */
    public function setHotels(array $hotels): static
    {
        if (is_null($hotels)) {
            throw new InvalidArgumentException('non-nullable hotels cannot be null');
        }
        $this->container['hotels'] = $hotels;

        return $this;
    }

    /**
     * Gets units
     *
     * @return \Repull\Model\AccountUnitsAddedPayloadUnitsInner[]
     */
    public function getUnits(): array
    {
        return $this->container['units'];
    }

    /**
     * Sets units
     *
     * @param \Repull\Model\AccountUnitsAddedPayloadUnitsInner[] $units The new units, as `GET /v1/connections/{id}/units` lists them.
     *
     * @return $this
     */
    public function setUnits(array $units): static
    {
        if (is_null($units)) {
            throw new InvalidArgumentException('non-nullable units cannot be null');
        }
        $this->container['units'] = $units;

        return $this;
    }

    /**
     * Gets added_at
     *
     * @return \DateTime
     */
    public function getAddedAt(): \DateTime
    {
        return $this->container['added_at'];
    }

    /**
     * Sets added_at
     *
     * @param \DateTime $added_at When the sync found the new units.
     *
     * @return $this
     */
    public function setAddedAt(\DateTime $added_at): static
    {
        if (is_null($added_at)) {
            throw new InvalidArgumentException('non-nullable added_at cannot be null');
        }
        $this->container['added_at'] = $added_at;

        return $this;
    }

    /**
     * Gets next_steps
     *
     * @return \Repull\Model\AccountUnitsAddedPayloadNextStepsInner[]
     */
    public function getNextSteps(): array
    {
        return $this->container['next_steps'];
    }

    /**
     * Sets next_steps
     *
     * @param \Repull\Model\AccountUnitsAddedPayloadNextStepsInner[] $next_steps What to call next: list the units, then map them.
     *
     * @return $this
     */
    public function setNextSteps(array $next_steps): static
    {
        if (is_null($next_steps)) {
            throw new InvalidArgumentException('non-nullable next_steps cannot be null');
        }
        $this->container['next_steps'] = $next_steps;

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


