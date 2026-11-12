<?php
/**
 * GetReservationSummary200Response
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
 * GetReservationSummary200Response Class Doc Comment
 *
 * @package  Repull
 * @author   OpenAPI Generator team
 * @link     https://openapi-generator.tech
 * @implements ArrayAccess<string, mixed>
 */
class GetReservationSummary200Response implements ModelInterface, ArrayAccess, JsonSerializable
{
    public const DISCRIMINATOR = null;

    /**
     * The original name of the model.
     *
     * @var string
     */
    protected static string $openAPIModelName = 'get_reservation_summary_200_response';

    /**
     * Array of property to type mappings. Used for (de)serialization
     *
     * @var array<string, string>
     */
    protected static array $openAPITypes = [
        'from' => '\DateTime',
        'to' => '\DateTime',
        'basis' => 'string',
        'group_by' => 'string[]',
        'as_of' => '\DateTime',
        'listing_count' => 'int',
        'totals' => '\Repull\Model\GetReservationSummary200ResponseTotalsInner[]',
        'groups' => '\Repull\Model\GetReservationSummary200ResponseTotalsInner[]'
    ];

    /**
     * Array of property to format mappings. Used for (de)serialization
     *
     * @var array<string, string|null>
     */
    protected static array $openAPIFormats = [
        'from' => 'date',
        'to' => 'date',
        'basis' => null,
        'group_by' => null,
        'as_of' => 'date',
        'listing_count' => null,
        'totals' => null,
        'groups' => null
    ];

    /**
     * Array of nullable properties. Used for (de)serialization
     *
     * @var array<string, bool>
     */
    protected static array $openAPINullables = [
        'from' => false,
        'to' => false,
        'basis' => false,
        'group_by' => false,
        'as_of' => true,
        'listing_count' => false,
        'totals' => false,
        'groups' => false
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
        'from' => 'from',
        'to' => 'to',
        'basis' => 'basis',
        'group_by' => 'groupBy',
        'as_of' => 'asOf',
        'listing_count' => 'listingCount',
        'totals' => 'totals',
        'groups' => 'groups'
    ];

    /**
     * Array of attributes to setter functions (for deserialization of responses)
     *
     * @var array<string, string>
     */
    protected static array $setters = [
        'from' => 'setFrom',
        'to' => 'setTo',
        'basis' => 'setBasis',
        'group_by' => 'setGroupBy',
        'as_of' => 'setAsOf',
        'listing_count' => 'setListingCount',
        'totals' => 'setTotals',
        'groups' => 'setGroups'
    ];

    /**
     * Array of attributes to getter functions (for serialization of requests)
     *
     * @var array<string, string>
     */
    protected static array $getters = [
        'from' => 'getFrom',
        'to' => 'getTo',
        'basis' => 'getBasis',
        'group_by' => 'getGroupBy',
        'as_of' => 'getAsOf',
        'listing_count' => 'getListingCount',
        'totals' => 'getTotals',
        'groups' => 'getGroups'
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

    public const BASIS_STAY = 'stay';
    public const BASIS_CHECK_IN = 'check_in';
    public const BASIS_BOOKED = 'booked';
    public const GROUP_BY_MONTH = 'month';
    public const GROUP_BY_LISTING = 'listing';
    public const GROUP_BY_CHANNEL = 'channel';

    /**
     * Gets allowable values of the enum
     *
     * @return string[]
     */
    public static function getBasisAllowableValues()
    {
        return [
            self::BASIS_STAY,
            self::BASIS_CHECK_IN,
            self::BASIS_BOOKED,
        ];
    }

    /**
     * Gets allowable values of the enum
     *
     * @return string[]
     */
    public static function getGroupByAllowableValues()
    {
        return [
            self::GROUP_BY_MONTH,
            self::GROUP_BY_LISTING,
            self::GROUP_BY_CHANNEL,
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
        $this->setIfExists('from', $data ?? [], null);
        $this->setIfExists('to', $data ?? [], null);
        $this->setIfExists('basis', $data ?? [], null);
        $this->setIfExists('group_by', $data ?? [], null);
        $this->setIfExists('as_of', $data ?? [], null);
        $this->setIfExists('listing_count', $data ?? [], null);
        $this->setIfExists('totals', $data ?? [], null);
        $this->setIfExists('groups', $data ?? [], null);
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

        if ($this->container['from'] === null) {
            $invalidProperties[] = "'from' can't be null";
        }
        if ($this->container['to'] === null) {
            $invalidProperties[] = "'to' can't be null";
        }
        if ($this->container['basis'] === null) {
            $invalidProperties[] = "'basis' can't be null";
        }
        $allowedValues = self::getBasisAllowableValues();
        if (!is_null($this->container['basis']) && !in_array($this->container['basis'], $allowedValues, true)) {
            $invalidProperties[] = sprintf(
                "invalid value '%s' for 'basis', must be one of '%s'",
                $this->container['basis'],
                implode("', '", $allowedValues)
            );
        }

        if ($this->container['group_by'] === null) {
            $invalidProperties[] = "'group_by' can't be null";
        }
        if ($this->container['as_of'] === null && !$this->isNullableSetToNull('as_of')) {
            $invalidProperties[] = "'as_of' is required";
        }
        if ($this->container['listing_count'] === null) {
            $invalidProperties[] = "'listing_count' can't be null";
        }
        if ($this->container['totals'] === null) {
            $invalidProperties[] = "'totals' can't be null";
        }
        if ($this->container['groups'] === null) {
            $invalidProperties[] = "'groups' can't be null";
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
     * Gets from
     *
     * @return \DateTime
     */
    public function getFrom(): \DateTime
    {
        return $this->container['from'];
    }

    /**
     * Sets from
     *
     * @param \DateTime $from from
     *
     * @return $this
     */
    public function setFrom(\DateTime $from): static
    {
        if (is_null($from)) {
            throw new InvalidArgumentException('non-nullable from cannot be null');
        }
        $this->container['from'] = $from;

        return $this;
    }

    /**
     * Gets to
     *
     * @return \DateTime
     */
    public function getTo(): \DateTime
    {
        return $this->container['to'];
    }

    /**
     * Sets to
     *
     * @param \DateTime $to to
     *
     * @return $this
     */
    public function setTo(\DateTime $to): static
    {
        if (is_null($to)) {
            throw new InvalidArgumentException('non-nullable to cannot be null');
        }
        $this->container['to'] = $to;

        return $this;
    }

    /**
     * Gets basis
     *
     * @return string
     */
    public function getBasis(): string
    {
        return $this->container['basis'];
    }

    /**
     * Sets basis
     *
     * @param string $basis basis
     *
     * @return $this
     */
    public function setBasis(string $basis): static
    {
        if (is_null($basis)) {
            throw new InvalidArgumentException('non-nullable basis cannot be null');
        }
        // (relax-enums.php) accept unknown enum values for forward compat
        $this->container['basis'] = $basis;

        return $this;
    }

    /**
     * Gets group_by
     *
     * @return string[]
     */
    public function getGroupBy(): array
    {
        return $this->container['group_by'];
    }

    /**
     * Sets group_by
     *
     * @param string[] $group_by group_by
     *
     * @return $this
     */
    public function setGroupBy(array $group_by): static
    {
        if (is_null($group_by)) {
            throw new InvalidArgumentException('non-nullable group_by cannot be null');
        }
        $allowedValues = self::getGroupByAllowableValues();
        if (array_diff($group_by, $allowedValues)) {
            throw new InvalidArgumentException(
                sprintf(
                    "Invalid value for 'group_by', must be one of '%s'",
                    implode("', '", $allowedValues)
                )
            );
        }
        $this->container['group_by'] = $group_by;

        return $this;
    }

    /**
     * Gets as_of
     *
     * @return \DateTime|null
     */
    public function getAsOf(): ?\DateTime
    {
        return $this->container['as_of'];
    }

    /**
     * Sets as_of
     *
     * @param \DateTime|null $as_of as_of
     *
     * @return $this
     */
    public function setAsOf(?\DateTime $as_of): static
    {
        if (is_null($as_of)) {
            array_push($this->openAPINullablesSetToNull, 'as_of');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('as_of', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['as_of'] = $as_of;

        return $this;
    }

    /**
     * Gets listing_count
     *
     * @return int
     */
    public function getListingCount(): int
    {
        return $this->container['listing_count'];
    }

    /**
     * Sets listing_count
     *
     * @param int $listing_count Active listings covered (the occupancy denominator).
     *
     * @return $this
     */
    public function setListingCount(int $listing_count): static
    {
        if (is_null($listing_count)) {
            throw new InvalidArgumentException('non-nullable listing_count cannot be null');
        }
        $this->container['listing_count'] = $listing_count;

        return $this;
    }

    /**
     * Gets totals
     *
     * @return \Repull\Model\GetReservationSummary200ResponseTotalsInner[]
     */
    public function getTotals(): array
    {
        return $this->container['totals'];
    }

    /**
     * Sets totals
     *
     * @param \Repull\Model\GetReservationSummary200ResponseTotalsInner[] $totals totals
     *
     * @return $this
     */
    public function setTotals(array $totals): static
    {
        if (is_null($totals)) {
            throw new InvalidArgumentException('non-nullable totals cannot be null');
        }
        $this->container['totals'] = $totals;

        return $this;
    }

    /**
     * Gets groups
     *
     * @return \Repull\Model\GetReservationSummary200ResponseTotalsInner[]
     */
    public function getGroups(): array
    {
        return $this->container['groups'];
    }

    /**
     * Sets groups
     *
     * @param \Repull\Model\GetReservationSummary200ResponseTotalsInner[] $groups groups
     *
     * @return $this
     */
    public function setGroups(array $groups): static
    {
        if (is_null($groups)) {
            throw new InvalidArgumentException('non-nullable groups cannot be null');
        }
        $this->container['groups'] = $groups;

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


