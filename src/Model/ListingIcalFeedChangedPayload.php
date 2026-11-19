<?php
/**
 * ListingIcalFeedChangedPayload
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
 * ListingIcalFeedChangedPayload Class Doc Comment
 *
 * @description Payload for &#x60;listing.ical_feed_changed&#x60;: Repull changed a Vrbo unit&#39;s imported calendars, or could not. Repull imports the listing&#39;s Repull iCal backup feed into every calendar-synced Vrbo unit, and removes other tools&#39; imported feeds while Repull is the unit&#39;s master calendar.
 * @package  Repull
 * @author   OpenAPI Generator team
 * @link     https://openapi-generator.tech
 * @implements ArrayAccess<string, mixed>
 */
class ListingIcalFeedChangedPayload implements ModelInterface, ArrayAccess, JsonSerializable
{
    public const DISCRIMINATOR = null;

    /**
     * The original name of the model.
     *
     * @var string
     */
    protected static string $openAPIModelName = 'ListingIcalFeedChangedPayload';

    /**
     * Array of property to type mappings. Used for (de)serialization
     *
     * @var array<string, string>
     */
    protected static array $openAPITypes = [
        'object' => '\Repull\Model\ListingWebhookObject',
        'provider' => 'string',
        'action' => 'string',
        'feed' => '\Repull\Model\ListingIcalFeedChangedPayloadFeed',
        'repull_feed_url' => 'string',
        'reason' => 'string',
        'trigger' => 'string',
        'error' => 'string',
        'occurred_at' => '\DateTime',
        'docs_url' => 'string'
    ];

    /**
     * Array of property to format mappings. Used for (de)serialization
     *
     * @var array<string, string|null>
     */
    protected static array $openAPIFormats = [
        'object' => null,
        'provider' => null,
        'action' => null,
        'feed' => null,
        'repull_feed_url' => 'uri',
        'reason' => null,
        'trigger' => null,
        'error' => null,
        'occurred_at' => 'date-time',
        'docs_url' => 'uri'
    ];

    /**
     * Array of nullable properties. Used for (de)serialization
     *
     * @var array<string, bool>
     */
    protected static array $openAPINullables = [
        'object' => false,
        'provider' => false,
        'action' => false,
        'feed' => false,
        'repull_feed_url' => false,
        'reason' => true,
        'trigger' => false,
        'error' => true,
        'occurred_at' => false,
        'docs_url' => false
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
        'object' => 'object',
        'provider' => 'provider',
        'action' => 'action',
        'feed' => 'feed',
        'repull_feed_url' => 'repullFeedUrl',
        'reason' => 'reason',
        'trigger' => 'trigger',
        'error' => 'error',
        'occurred_at' => 'occurredAt',
        'docs_url' => 'docsUrl'
    ];

    /**
     * Array of attributes to setter functions (for deserialization of responses)
     *
     * @var array<string, string>
     */
    protected static array $setters = [
        'object' => 'setObject',
        'provider' => 'setProvider',
        'action' => 'setAction',
        'feed' => 'setFeed',
        'repull_feed_url' => 'setRepullFeedUrl',
        'reason' => 'setReason',
        'trigger' => 'setTrigger',
        'error' => 'setError',
        'occurred_at' => 'setOccurredAt',
        'docs_url' => 'setDocsUrl'
    ];

    /**
     * Array of attributes to getter functions (for serialization of requests)
     *
     * @var array<string, string>
     */
    protected static array $getters = [
        'object' => 'getObject',
        'provider' => 'getProvider',
        'action' => 'getAction',
        'feed' => 'getFeed',
        'repull_feed_url' => 'getRepullFeedUrl',
        'reason' => 'getReason',
        'trigger' => 'getTrigger',
        'error' => 'getError',
        'occurred_at' => 'getOccurredAt',
        'docs_url' => 'getDocsUrl'
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

    public const PROVIDER_VRBO = 'vrbo';
    public const ACTION_REPULL_FEED_ADDED = 'repull_feed_added';
    public const ACTION_REPULL_FEED_ADD_FAILED = 'repull_feed_add_failed';
    public const ACTION_OTHER_FEED_REMOVED = 'other_feed_removed';
    public const ACTION_OTHER_FEED_REMOVE_FAILED = 'other_feed_remove_failed';
    public const REASON_REPULL_FEED_MISSING = 'repull_feed_missing';
    public const REASON_REPULL_IS_MASTER_CALENDAR = 'repull_is_master_calendar';
    public const TRIGGER_CALENDAR_SYNC_ENABLED = 'calendar_sync_enabled';
    public const TRIGGER_DRIFT_CHECK = 'drift_check';

    /**
     * Gets allowable values of the enum
     *
     * @return string[]
     */
    public static function getProviderAllowableValues()
    {
        return [
            self::PROVIDER_VRBO,
        ];
    }

    /**
     * Gets allowable values of the enum
     *
     * @return string[]
     */
    public static function getActionAllowableValues()
    {
        return [
            self::ACTION_REPULL_FEED_ADDED,
            self::ACTION_REPULL_FEED_ADD_FAILED,
            self::ACTION_OTHER_FEED_REMOVED,
            self::ACTION_OTHER_FEED_REMOVE_FAILED,
        ];
    }

    /**
     * Gets allowable values of the enum
     *
     * @return string[]
     */
    public static function getReasonAllowableValues()
    {
        return [
            self::REASON_REPULL_FEED_MISSING,
            self::REASON_REPULL_IS_MASTER_CALENDAR,
        ];
    }

    /**
     * Gets allowable values of the enum
     *
     * @return string[]
     */
    public static function getTriggerAllowableValues()
    {
        return [
            self::TRIGGER_CALENDAR_SYNC_ENABLED,
            self::TRIGGER_DRIFT_CHECK,
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
        $this->setIfExists('object', $data ?? [], null);
        $this->setIfExists('provider', $data ?? [], null);
        $this->setIfExists('action', $data ?? [], null);
        $this->setIfExists('feed', $data ?? [], null);
        $this->setIfExists('repull_feed_url', $data ?? [], null);
        $this->setIfExists('reason', $data ?? [], null);
        $this->setIfExists('trigger', $data ?? [], null);
        $this->setIfExists('error', $data ?? [], null);
        $this->setIfExists('occurred_at', $data ?? [], null);
        $this->setIfExists('docs_url', $data ?? [], null);
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

        if ($this->container['object'] === null) {
            $invalidProperties[] = "'object' can't be null";
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

        if ($this->container['feed'] === null) {
            $invalidProperties[] = "'feed' can't be null";
        }
        if ($this->container['repull_feed_url'] === null) {
            $invalidProperties[] = "'repull_feed_url' can't be null";
        }
        $allowedValues = self::getReasonAllowableValues();
        if (!is_null($this->container['reason']) && !in_array($this->container['reason'], $allowedValues, true)) {
            $invalidProperties[] = sprintf(
                "invalid value '%s' for 'reason', must be one of '%s'",
                $this->container['reason'],
                implode("', '", $allowedValues)
            );
        }

        if ($this->container['trigger'] === null) {
            $invalidProperties[] = "'trigger' can't be null";
        }
        $allowedValues = self::getTriggerAllowableValues();
        if (!is_null($this->container['trigger']) && !in_array($this->container['trigger'], $allowedValues, true)) {
            $invalidProperties[] = sprintf(
                "invalid value '%s' for 'trigger', must be one of '%s'",
                $this->container['trigger'],
                implode("', '", $allowedValues)
            );
        }

        if ($this->container['occurred_at'] === null) {
            $invalidProperties[] = "'occurred_at' can't be null";
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
     * Gets object
     *
     * @return \Repull\Model\ListingWebhookObject
     */
    public function getObject(): \Repull\Model\ListingWebhookObject
    {
        return $this->container['object'];
    }

    /**
     * Sets object
     *
     * @param \Repull\Model\ListingWebhookObject $object object
     *
     * @return $this
     */
    public function setObject(\Repull\Model\ListingWebhookObject $object): static
    {
        if (is_null($object)) {
            throw new InvalidArgumentException('non-nullable object cannot be null');
        }
        $this->container['object'] = $object;

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
     * @param string $provider provider
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
     * @param string $action `repull_feed_added`: the Repull feed is now imported (`reason: \"repull_feed_missing\"` when it had been removed on Vrbo and was put back). `repull_feed_add_failed`: Vrbo did not take it — ask the host to add `repullFeedUrl` in Vrbo → Calendar → Import calendar, named \"Repull\"; nothing else is changed on the unit until it is there. `other_feed_removed`: another tool's imported calendar (`feed`) was removed. `other_feed_remove_failed`: removing it failed — ask the host to remove it in Vrbo → Calendar → Import calendar. Repull also emails the workspace owner when an action failed.
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
     * Gets feed
     *
     * @return \Repull\Model\ListingIcalFeedChangedPayloadFeed
     */
    public function getFeed(): \Repull\Model\ListingIcalFeedChangedPayloadFeed
    {
        return $this->container['feed'];
    }

    /**
     * Sets feed
     *
     * @param \Repull\Model\ListingIcalFeedChangedPayloadFeed $feed feed
     *
     * @return $this
     */
    public function setFeed(\Repull\Model\ListingIcalFeedChangedPayloadFeed $feed): static
    {
        if (is_null($feed)) {
            throw new InvalidArgumentException('non-nullable feed cannot be null');
        }
        $this->container['feed'] = $feed;

        return $this;
    }

    /**
     * Gets repull_feed_url
     *
     * @return string
     */
    public function getRepullFeedUrl(): string
    {
        return $this->container['repull_feed_url'];
    }

    /**
     * Sets repull_feed_url
     *
     * @param string $repull_feed_url The listing's Repull iCal backup feed (`GET /v1/listings/{id}/ical`).
     *
     * @return $this
     */
    public function setRepullFeedUrl(string $repull_feed_url): static
    {
        if (is_null($repull_feed_url)) {
            throw new InvalidArgumentException('non-nullable repull_feed_url cannot be null');
        }
        $this->container['repull_feed_url'] = $repull_feed_url;

        return $this;
    }

    /**
     * Gets reason
     *
     * @return string|null
     */
    public function getReason(): ?string
    {
        return $this->container['reason'];
    }

    /**
     * Sets reason
     *
     * @param string|null $reason Why Repull acted.
     *
     * @return $this
     */
    public function setReason(?string $reason): static
    {
        if (is_null($reason)) {
            array_push($this->openAPINullablesSetToNull, 'reason');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('reason', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        // (relax-enums.php) accept unknown enum values for forward compat
        $this->container['reason'] = $reason;

        return $this;
    }

    /**
     * Gets trigger
     *
     * @return string
     */
    public function getTrigger(): string
    {
        return $this->container['trigger'];
    }

    /**
     * Sets trigger
     *
     * @param string $trigger `calendar_sync_enabled`: calendar sync was just switched on for the unit. `drift_check`: the daily Vrbo calendar check.
     *
     * @return $this
     */
    public function setTrigger(string $trigger): static
    {
        if (is_null($trigger)) {
            throw new InvalidArgumentException('non-nullable trigger cannot be null');
        }
        // (relax-enums.php) accept unknown enum values for forward compat
        $this->container['trigger'] = $trigger;

        return $this;
    }

    /**
     * Gets error
     *
     * @return string|null
     */
    public function getError(): ?string
    {
        return $this->container['error'];
    }

    /**
     * Sets error
     *
     * @param string|null $error Vrbo's answer, on a failed action.
     *
     * @return $this
     */
    public function setError(?string $error): static
    {
        if (is_null($error)) {
            array_push($this->openAPINullablesSetToNull, 'error');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('error', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['error'] = $error;

        return $this;
    }

    /**
     * Gets occurred_at
     *
     * @return \DateTime
     */
    public function getOccurredAt(): \DateTime
    {
        return $this->container['occurred_at'];
    }

    /**
     * Sets occurred_at
     *
     * @param \DateTime $occurred_at occurred_at
     *
     * @return $this
     */
    public function setOccurredAt(\DateTime $occurred_at): static
    {
        if (is_null($occurred_at)) {
            throw new InvalidArgumentException('non-nullable occurred_at cannot be null');
        }
        $this->container['occurred_at'] = $occurred_at;

        return $this;
    }

    /**
     * Gets docs_url
     *
     * @return string|null
     */
    public function getDocsUrl(): ?string
    {
        return $this->container['docs_url'];
    }

    /**
     * Sets docs_url
     *
     * @param string|null $docs_url docs_url
     *
     * @return $this
     */
    public function setDocsUrl(?string $docs_url): static
    {
        if (is_null($docs_url)) {
            throw new InvalidArgumentException('non-nullable docs_url cannot be null');
        }
        $this->container['docs_url'] = $docs_url;

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


