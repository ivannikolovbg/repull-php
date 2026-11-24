<?php
/**
 * TiketAriWriteResponse
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
 * TiketAriWriteResponse Class Doc Comment
 *
 * @description Result of a tiket.com rates / availability / restrictions write.
 * @package  Repull
 * @author   OpenAPI Generator team
 * @link     https://openapi-generator.tech
 * @implements ArrayAccess<string, mixed>
 */
class TiketAriWriteResponse implements ModelInterface, ArrayAccess, JsonSerializable
{
    public const DISCRIMINATOR = null;

    /**
     * The original name of the model.
     *
     * @var string
     */
    protected static string $openAPIModelName = 'TiketAriWriteResponse';

    /**
     * Array of property to type mappings. Used for (de)serialization
     *
     * @var array<string, string>
     */
    protected static array $openAPITypes = [
        'listing_id' => 'string',
        'property_id' => 'string',
        'room_type_code' => 'string',
        'type' => 'string',
        'updates' => 'int',
        'requests' => 'int',
        'echo_tokens' => 'string[]',
        'warnings' => 'array<string,mixed>[]',
        'applied' => 'string',
        'mismatches' => '\Repull\Model\TiketAriMismatch[]',
        'mismatch_count' => 'int'
    ];

    /**
     * Array of property to format mappings. Used for (de)serialization
     *
     * @var array<string, string|null>
     */
    protected static array $openAPIFormats = [
        'listing_id' => null,
        'property_id' => null,
        'room_type_code' => null,
        'type' => null,
        'updates' => null,
        'requests' => null,
        'echo_tokens' => null,
        'warnings' => null,
        'applied' => null,
        'mismatches' => null,
        'mismatch_count' => null
    ];

    /**
     * Array of nullable properties. Used for (de)serialization
     *
     * @var array<string, bool>
     */
    protected static array $openAPINullables = [
        'listing_id' => false,
        'property_id' => false,
        'room_type_code' => false,
        'type' => false,
        'updates' => false,
        'requests' => false,
        'echo_tokens' => false,
        'warnings' => false,
        'applied' => false,
        'mismatches' => false,
        'mismatch_count' => false
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
        'listing_id' => 'listingId',
        'property_id' => 'propertyId',
        'room_type_code' => 'roomTypeCode',
        'type' => 'type',
        'updates' => 'updates',
        'requests' => 'requests',
        'echo_tokens' => 'echoTokens',
        'warnings' => 'warnings',
        'applied' => 'applied',
        'mismatches' => 'mismatches',
        'mismatch_count' => 'mismatchCount'
    ];

    /**
     * Array of attributes to setter functions (for deserialization of responses)
     *
     * @var array<string, string>
     */
    protected static array $setters = [
        'listing_id' => 'setListingId',
        'property_id' => 'setPropertyId',
        'room_type_code' => 'setRoomTypeCode',
        'type' => 'setType',
        'updates' => 'setUpdates',
        'requests' => 'setRequests',
        'echo_tokens' => 'setEchoTokens',
        'warnings' => 'setWarnings',
        'applied' => 'setApplied',
        'mismatches' => 'setMismatches',
        'mismatch_count' => 'setMismatchCount'
    ];

    /**
     * Array of attributes to getter functions (for serialization of requests)
     *
     * @var array<string, string>
     */
    protected static array $getters = [
        'listing_id' => 'getListingId',
        'property_id' => 'getPropertyId',
        'room_type_code' => 'getRoomTypeCode',
        'type' => 'getType',
        'updates' => 'getUpdates',
        'requests' => 'getRequests',
        'echo_tokens' => 'getEchoTokens',
        'warnings' => 'getWarnings',
        'applied' => 'getApplied',
        'mismatches' => 'getMismatches',
        'mismatch_count' => 'getMismatchCount'
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

    public const TYPE_AVAILABILITY = 'availability';
    public const TYPE_RATES = 'rates';
    public const TYPE_RESTRICTIONS = 'restrictions';
    public const APPLIED_VERIFIED = 'verified';
    public const APPLIED_MISMATCH = 'mismatch';
    public const APPLIED_UNVERIFIED = 'unverified';

    /**
     * Gets allowable values of the enum
     *
     * @return string[]
     */
    public static function getTypeAllowableValues()
    {
        return [
            self::TYPE_AVAILABILITY,
            self::TYPE_RATES,
            self::TYPE_RESTRICTIONS,
        ];
    }

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
        $this->setIfExists('listing_id', $data ?? [], null);
        $this->setIfExists('property_id', $data ?? [], null);
        $this->setIfExists('room_type_code', $data ?? [], null);
        $this->setIfExists('type', $data ?? [], null);
        $this->setIfExists('updates', $data ?? [], null);
        $this->setIfExists('requests', $data ?? [], null);
        $this->setIfExists('echo_tokens', $data ?? [], null);
        $this->setIfExists('warnings', $data ?? [], null);
        $this->setIfExists('applied', $data ?? [], null);
        $this->setIfExists('mismatches', $data ?? [], null);
        $this->setIfExists('mismatch_count', $data ?? [], null);
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

        if ($this->container['property_id'] === null) {
            $invalidProperties[] = "'property_id' can't be null";
        }
        if ($this->container['type'] === null) {
            $invalidProperties[] = "'type' can't be null";
        }
        $allowedValues = self::getTypeAllowableValues();
        if (!is_null($this->container['type']) && !in_array($this->container['type'], $allowedValues, true)) {
            $invalidProperties[] = sprintf(
                "invalid value '%s' for 'type', must be one of '%s'",
                $this->container['type'],
                implode("', '", $allowedValues)
            );
        }

        if ($this->container['updates'] === null) {
            $invalidProperties[] = "'updates' can't be null";
        }
        if ($this->container['requests'] === null) {
            $invalidProperties[] = "'requests' can't be null";
        }
        if ($this->container['applied'] === null) {
            $invalidProperties[] = "'applied' can't be null";
        }
        $allowedValues = self::getAppliedAllowableValues();
        if (!is_null($this->container['applied']) && !in_array($this->container['applied'], $allowedValues, true)) {
            $invalidProperties[] = sprintf(
                "invalid value '%s' for 'applied', must be one of '%s'",
                $this->container['applied'],
                implode("', '", $allowedValues)
            );
        }

        if (!is_null($this->container['mismatches']) && (count($this->container['mismatches']) > 100)) {
            $invalidProperties[] = "invalid value for 'mismatches', number of items must be less than or equal to 100.";
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
     * @param string|null $listing_id Present on `PUT /v1/channels/tiket/listings/{id}/pricing`.
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
     * Gets property_id
     *
     * @return string
     */
    public function getPropertyId(): string
    {
        return $this->container['property_id'];
    }

    /**
     * Sets property_id
     *
     * @param string $property_id tiket.com Hotel ID.
     *
     * @return $this
     */
    public function setPropertyId(string $property_id): static
    {
        if (is_null($property_id)) {
            throw new InvalidArgumentException('non-nullable property_id cannot be null');
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
     * @param string|null $room_type_code Present on `PUT /v1/channels/tiket/listings/{id}/pricing`.
     *
     * @return $this
     */
    public function setRoomTypeCode(?string $room_type_code): static
    {
        if (is_null($room_type_code)) {
            throw new InvalidArgumentException('non-nullable room_type_code cannot be null');
        }
        $this->container['room_type_code'] = $room_type_code;

        return $this;
    }

    /**
     * Gets type
     *
     * @return string
     */
    public function getType(): string
    {
        return $this->container['type'];
    }

    /**
     * Sets type
     *
     * @param string $type type
     *
     * @return $this
     */
    public function setType(string $type): static
    {
        if (is_null($type)) {
            throw new InvalidArgumentException('non-nullable type cannot be null');
        }
        // (relax-enums.php) accept unknown enum values for forward compat
        $this->container['type'] = $type;

        return $this;
    }

    /**
     * Gets updates
     *
     * @return int
     */
    public function getUpdates(): int
    {
        return $this->container['updates'];
    }

    /**
     * Sets updates
     *
     * @param int $updates Update rows sent (on the listing route, one per targeted rate plan).
     *
     * @return $this
     */
    public function setUpdates(int $updates): static
    {
        if (is_null($updates)) {
            throw new InvalidArgumentException('non-nullable updates cannot be null');
        }
        $this->container['updates'] = $updates;

        return $this;
    }

    /**
     * Gets requests
     *
     * @return int
     */
    public function getRequests(): int
    {
        return $this->container['requests'];
    }

    /**
     * Sets requests
     *
     * @param int $requests Requests made to tiket.com.
     *
     * @return $this
     */
    public function setRequests(int $requests): static
    {
        if (is_null($requests)) {
            throw new InvalidArgumentException('non-nullable requests cannot be null');
        }
        $this->container['requests'] = $requests;

        return $this;
    }

    /**
     * Gets echo_tokens
     *
     * @return string[]|null
     */
    public function getEchoTokens(): ?array
    {
        return $this->container['echo_tokens'];
    }

    /**
     * Sets echo_tokens
     *
     * @param string[]|null $echo_tokens tiket.com's reference for each request. Quote them to tiket.com support.
     *
     * @return $this
     */
    public function setEchoTokens(?array $echo_tokens): static
    {
        if (is_null($echo_tokens)) {
            throw new InvalidArgumentException('non-nullable echo_tokens cannot be null');
        }
        $this->container['echo_tokens'] = $echo_tokens;

        return $this;
    }

    /**
     * Gets warnings
     *
     * @return array<string,mixed>[]|null
     */
    public function getWarnings(): ?array
    {
        return $this->container['warnings'];
    }

    /**
     * Sets warnings
     *
     * @param array<string,mixed>[]|null $warnings tiket.com's own warnings, verbatim (for example `ARI_PARTIALLY_ERROR` when part of a request was not applied).
     *
     * @return $this
     */
    public function setWarnings(?array $warnings): static
    {
        if (is_null($warnings)) {
            throw new InvalidArgumentException('non-nullable warnings cannot be null');
        }
        $this->container['warnings'] = $warnings;

        return $this;
    }

    /**
     * Gets applied
     *
     * @return string
     */
    public function getApplied(): string
    {
        return $this->container['applied'];
    }

    /**
     * Sets applied
     *
     * @param string $applied `verified` — the read-back matches what was sent. `mismatch` — tiket.com holds something else for at least one night (see `mismatches`). `unverified` — `verify: false` was sent.
     *
     * @return $this
     */
    public function setApplied(string $applied): static
    {
        if (is_null($applied)) {
            throw new InvalidArgumentException('non-nullable applied cannot be null');
        }
        // (relax-enums.php) accept unknown enum values for forward compat
        $this->container['applied'] = $applied;

        return $this;
    }

    /**
     * Gets mismatches
     *
     * @return \Repull\Model\TiketAriMismatch[]|null
     */
    public function getMismatches(): ?array
    {
        return $this->container['mismatches'];
    }

    /**
     * Sets mismatches
     *
     * @param \Repull\Model\TiketAriMismatch[]|null $mismatches Present when `applied` is `mismatch`. At most 100; `mismatchCount` is the total.
     *
     * @return $this
     */
    public function setMismatches(?array $mismatches): static
    {
        if (is_null($mismatches)) {
            throw new InvalidArgumentException('non-nullable mismatches cannot be null');
        }

        if ((count($mismatches) > 100)) {
            throw new InvalidArgumentException('invalid value for $mismatches when calling TiketAriWriteResponse., number of items must be less than or equal to 100.');
        }
        $this->container['mismatches'] = $mismatches;

        return $this;
    }

    /**
     * Gets mismatch_count
     *
     * @return int|null
     */
    public function getMismatchCount(): ?int
    {
        return $this->container['mismatch_count'];
    }

    /**
     * Sets mismatch_count
     *
     * @param int|null $mismatch_count mismatch_count
     *
     * @return $this
     */
    public function setMismatchCount(?int $mismatch_count): static
    {
        if (is_null($mismatch_count)) {
            throw new InvalidArgumentException('non-nullable mismatch_count cannot be null');
        }
        $this->container['mismatch_count'] = $mismatch_count;

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


