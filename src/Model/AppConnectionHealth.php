<?php
/**
 * AppConnectionHealth
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
 * AppConnectionHealth Class Doc Comment
 *
 * @description How an app (PriceLabs) is doing on one connection, from its calls with the key.
 * @package  Repull
 * @author   OpenAPI Generator team
 * @link     https://openapi-generator.tech
 * @implements ArrayAccess<string, mixed>
 */
class AppConnectionHealth implements ModelInterface, ArrayAccess, JsonSerializable
{
    public const DISCRIMINATOR = null;

    /**
     * The original name of the model.
     *
     * @var string
     */
    protected static string $openAPIModelName = 'AppConnectionHealth';

    /**
     * Array of property to type mappings. Used for (de)serialization
     *
     * @var array<string, string>
     */
    protected static array $openAPITypes = [
        'status' => 'string',
        'last_request_at' => '\DateTime',
        'last_successful_write_at' => '\DateTime',
        'writes_per_day' => '\Repull\Model\AppConnectionHealthWritesPerDayInner[]',
        'requests24h' => 'int',
        'errors24h' => 'int',
        'error_rate24h' => 'float',
        'requests7d' => 'int',
        'errors7d' => 'int',
        'error_rate7d' => 'float',
        'recent_errors' => '\Repull\Model\AppConnectionHealthRecentErrorsInner[]'
    ];

    /**
     * Array of property to format mappings. Used for (de)serialization
     *
     * @var array<string, string|null>
     */
    protected static array $openAPIFormats = [
        'status' => null,
        'last_request_at' => 'date-time',
        'last_successful_write_at' => 'date-time',
        'writes_per_day' => null,
        'requests24h' => null,
        'errors24h' => null,
        'error_rate24h' => null,
        'requests7d' => null,
        'errors7d' => null,
        'error_rate7d' => null,
        'recent_errors' => null
    ];

    /**
     * Array of nullable properties. Used for (de)serialization
     *
     * @var array<string, bool>
     */
    protected static array $openAPINullables = [
        'status' => false,
        'last_request_at' => true,
        'last_successful_write_at' => true,
        'writes_per_day' => false,
        'requests24h' => false,
        'errors24h' => false,
        'error_rate24h' => false,
        'requests7d' => false,
        'errors7d' => false,
        'error_rate7d' => false,
        'recent_errors' => false
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
        'status' => 'status',
        'last_request_at' => 'lastRequestAt',
        'last_successful_write_at' => 'lastSuccessfulWriteAt',
        'writes_per_day' => 'writesPerDay',
        'requests24h' => 'requests24h',
        'errors24h' => 'errors24h',
        'error_rate24h' => 'errorRate24h',
        'requests7d' => 'requests7d',
        'errors7d' => 'errors7d',
        'error_rate7d' => 'errorRate7d',
        'recent_errors' => 'recentErrors'
    ];

    /**
     * Array of attributes to setter functions (for deserialization of responses)
     *
     * @var array<string, string>
     */
    protected static array $setters = [
        'status' => 'setStatus',
        'last_request_at' => 'setLastRequestAt',
        'last_successful_write_at' => 'setLastSuccessfulWriteAt',
        'writes_per_day' => 'setWritesPerDay',
        'requests24h' => 'setRequests24h',
        'errors24h' => 'setErrors24h',
        'error_rate24h' => 'setErrorRate24h',
        'requests7d' => 'setRequests7d',
        'errors7d' => 'setErrors7d',
        'error_rate7d' => 'setErrorRate7d',
        'recent_errors' => 'setRecentErrors'
    ];

    /**
     * Array of attributes to getter functions (for serialization of requests)
     *
     * @var array<string, string>
     */
    protected static array $getters = [
        'status' => 'getStatus',
        'last_request_at' => 'getLastRequestAt',
        'last_successful_write_at' => 'getLastSuccessfulWriteAt',
        'writes_per_day' => 'getWritesPerDay',
        'requests24h' => 'getRequests24h',
        'errors24h' => 'getErrors24h',
        'error_rate24h' => 'getErrorRate24h',
        'requests7d' => 'getRequests7d',
        'errors7d' => 'getErrors7d',
        'error_rate7d' => 'getErrorRate7d',
        'recent_errors' => 'getRecentErrors'
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

    public const STATUS_ACTIVE = 'active';
    public const STATUS_IDLE = 'idle';
    public const STATUS_FAILING = 'failing';
    public const STATUS_NEVER_CONNECTED = 'never_connected';

    /**
     * Gets allowable values of the enum
     *
     * @return string[]
     */
    public static function getStatusAllowableValues()
    {
        return [
            self::STATUS_ACTIVE,
            self::STATUS_IDLE,
            self::STATUS_FAILING,
            self::STATUS_NEVER_CONNECTED,
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
        $this->setIfExists('status', $data ?? [], null);
        $this->setIfExists('last_request_at', $data ?? [], null);
        $this->setIfExists('last_successful_write_at', $data ?? [], null);
        $this->setIfExists('writes_per_day', $data ?? [], null);
        $this->setIfExists('requests24h', $data ?? [], null);
        $this->setIfExists('errors24h', $data ?? [], null);
        $this->setIfExists('error_rate24h', $data ?? [], null);
        $this->setIfExists('requests7d', $data ?? [], null);
        $this->setIfExists('errors7d', $data ?? [], null);
        $this->setIfExists('error_rate7d', $data ?? [], null);
        $this->setIfExists('recent_errors', $data ?? [], null);
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

        if ($this->container['writes_per_day'] === null) {
            $invalidProperties[] = "'writes_per_day' can't be null";
        }
        if ($this->container['recent_errors'] === null) {
            $invalidProperties[] = "'recent_errors' can't be null";
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
     * @param string $status `active`: a successful price or minimum-stay push in the last 24 hours. `idle`: the app connected before but has not pushed in the last 24 hours. `failing`: more than 25% of the last 24 hours' requests failed (at least 5 requests); read `recentErrors`. `never_connected`: the app has not used the key yet.
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
     * Gets last_request_at
     *
     * @return \DateTime|null
     */
    public function getLastRequestAt(): ?\DateTime
    {
        return $this->container['last_request_at'];
    }

    /**
     * Sets last_request_at
     *
     * @param \DateTime|null $last_request_at The app's last request with the key, of any kind.
     *
     * @return $this
     */
    public function setLastRequestAt(?\DateTime $last_request_at): static
    {
        if (is_null($last_request_at)) {
            array_push($this->openAPINullablesSetToNull, 'last_request_at');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('last_request_at', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['last_request_at'] = $last_request_at;

        return $this;
    }

    /**
     * Gets last_successful_write_at
     *
     * @return \DateTime|null
     */
    public function getLastSuccessfulWriteAt(): ?\DateTime
    {
        return $this->container['last_successful_write_at'];
    }

    /**
     * Sets last_successful_write_at
     *
     * @param \DateTime|null $last_successful_write_at The app's last successful price or minimum-stay push.
     *
     * @return $this
     */
    public function setLastSuccessfulWriteAt(?\DateTime $last_successful_write_at): static
    {
        if (is_null($last_successful_write_at)) {
            array_push($this->openAPINullablesSetToNull, 'last_successful_write_at');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('last_successful_write_at', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['last_successful_write_at'] = $last_successful_write_at;

        return $this;
    }

    /**
     * Gets writes_per_day
     *
     * @return \Repull\Model\AppConnectionHealthWritesPerDayInner[]
     */
    public function getWritesPerDay(): array
    {
        return $this->container['writes_per_day'];
    }

    /**
     * Sets writes_per_day
     *
     * @param \Repull\Model\AppConnectionHealthWritesPerDayInner[] $writes_per_day Successful price and minimum-stay pushes per UTC day, the last 7 days, oldest first.
     *
     * @return $this
     */
    public function setWritesPerDay(array $writes_per_day): static
    {
        if (is_null($writes_per_day)) {
            throw new InvalidArgumentException('non-nullable writes_per_day cannot be null');
        }
        $this->container['writes_per_day'] = $writes_per_day;

        return $this;
    }

    /**
     * Gets requests24h
     *
     * @return int|null
     */
    public function getRequests24h(): ?int
    {
        return $this->container['requests24h'];
    }

    /**
     * Sets requests24h
     *
     * @param int|null $requests24h requests24h
     *
     * @return $this
     */
    public function setRequests24h(?int $requests24h): static
    {
        if (is_null($requests24h)) {
            throw new InvalidArgumentException('non-nullable requests24h cannot be null');
        }
        $this->container['requests24h'] = $requests24h;

        return $this;
    }

    /**
     * Gets errors24h
     *
     * @return int|null
     */
    public function getErrors24h(): ?int
    {
        return $this->container['errors24h'];
    }

    /**
     * Sets errors24h
     *
     * @param int|null $errors24h errors24h
     *
     * @return $this
     */
    public function setErrors24h(?int $errors24h): static
    {
        if (is_null($errors24h)) {
            throw new InvalidArgumentException('non-nullable errors24h cannot be null');
        }
        $this->container['errors24h'] = $errors24h;

        return $this;
    }

    /**
     * Gets error_rate24h
     *
     * @return float|null
     */
    public function getErrorRate24h(): ?float
    {
        return $this->container['error_rate24h'];
    }

    /**
     * Sets error_rate24h
     *
     * @param float|null $error_rate24h `errors24h / requests24h`; 0 when there were no requests.
     *
     * @return $this
     */
    public function setErrorRate24h(?float $error_rate24h): static
    {
        if (is_null($error_rate24h)) {
            throw new InvalidArgumentException('non-nullable error_rate24h cannot be null');
        }
        $this->container['error_rate24h'] = $error_rate24h;

        return $this;
    }

    /**
     * Gets requests7d
     *
     * @return int|null
     */
    public function getRequests7d(): ?int
    {
        return $this->container['requests7d'];
    }

    /**
     * Sets requests7d
     *
     * @param int|null $requests7d requests7d
     *
     * @return $this
     */
    public function setRequests7d(?int $requests7d): static
    {
        if (is_null($requests7d)) {
            throw new InvalidArgumentException('non-nullable requests7d cannot be null');
        }
        $this->container['requests7d'] = $requests7d;

        return $this;
    }

    /**
     * Gets errors7d
     *
     * @return int|null
     */
    public function getErrors7d(): ?int
    {
        return $this->container['errors7d'];
    }

    /**
     * Sets errors7d
     *
     * @param int|null $errors7d errors7d
     *
     * @return $this
     */
    public function setErrors7d(?int $errors7d): static
    {
        if (is_null($errors7d)) {
            throw new InvalidArgumentException('non-nullable errors7d cannot be null');
        }
        $this->container['errors7d'] = $errors7d;

        return $this;
    }

    /**
     * Gets error_rate7d
     *
     * @return float|null
     */
    public function getErrorRate7d(): ?float
    {
        return $this->container['error_rate7d'];
    }

    /**
     * Sets error_rate7d
     *
     * @param float|null $error_rate7d error_rate7d
     *
     * @return $this
     */
    public function setErrorRate7d(?float $error_rate7d): static
    {
        if (is_null($error_rate7d)) {
            throw new InvalidArgumentException('non-nullable error_rate7d cannot be null');
        }
        $this->container['error_rate7d'] = $error_rate7d;

        return $this;
    }

    /**
     * Gets recent_errors
     *
     * @return \Repull\Model\AppConnectionHealthRecentErrorsInner[]
     */
    public function getRecentErrors(): array
    {
        return $this->container['recent_errors'];
    }

    /**
     * Sets recent_errors
     *
     * @param \Repull\Model\AppConnectionHealthRecentErrorsInner[] $recent_errors The 10 most recent failed requests, newest first.
     *
     * @return $this
     */
    public function setRecentErrors(array $recent_errors): static
    {
        if (is_null($recent_errors)) {
            throw new InvalidArgumentException('non-nullable recent_errors cannot be null');
        }
        $this->container['recent_errors'] = $recent_errors;

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


