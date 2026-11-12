<?php
/**
 * GetReservationSummary200ResponseTotalsInner
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
 * GetReservationSummary200ResponseTotalsInner Class Doc Comment
 *
 * @package  Repull
 * @author   OpenAPI Generator team
 * @link     https://openapi-generator.tech
 * @implements ArrayAccess<string, mixed>
 */
class GetReservationSummary200ResponseTotalsInner implements ModelInterface, ArrayAccess, JsonSerializable
{
    public const DISCRIMINATOR = null;

    /**
     * The original name of the model.
     *
     * @var string
     */
    protected static string $openAPIModelName = 'get_reservation_summary_200_response_totals_inner';

    /**
     * Array of property to type mappings. Used for (de)serialization
     *
     * @var array<string, string>
     */
    protected static array $openAPITypes = [
        'month' => 'string',
        'listing_id' => 'string',
        'listing_name' => 'string',
        'channel' => 'string',
        'currency' => 'string',
        'reservations' => 'int',
        'nights' => 'int',
        'revenue' => 'float',
        'payout' => 'float',
        'reservations_with_payout' => 'int',
        'average_nightly_rate' => 'float',
        'average_stay_nights' => 'float',
        'average_lead_time_days' => 'float',
        'occupancy' => 'float',
        'available_nights' => 'float',
        'cancelled' => 'int',
        'cancelled_revenue' => 'float',
        'pending' => 'int',
        'owner_stays' => 'int',
        'owner_nights' => 'int'
    ];

    /**
     * Array of property to format mappings. Used for (de)serialization
     *
     * @var array<string, string|null>
     */
    protected static array $openAPIFormats = [
        'month' => null,
        'listing_id' => null,
        'listing_name' => null,
        'channel' => null,
        'currency' => null,
        'reservations' => null,
        'nights' => null,
        'revenue' => null,
        'payout' => null,
        'reservations_with_payout' => null,
        'average_nightly_rate' => null,
        'average_stay_nights' => null,
        'average_lead_time_days' => null,
        'occupancy' => null,
        'available_nights' => null,
        'cancelled' => null,
        'cancelled_revenue' => null,
        'pending' => null,
        'owner_stays' => null,
        'owner_nights' => null
    ];

    /**
     * Array of nullable properties. Used for (de)serialization
     *
     * @var array<string, bool>
     */
    protected static array $openAPINullables = [
        'month' => false,
        'listing_id' => false,
        'listing_name' => true,
        'channel' => false,
        'currency' => true,
        'reservations' => false,
        'nights' => false,
        'revenue' => false,
        'payout' => false,
        'reservations_with_payout' => false,
        'average_nightly_rate' => true,
        'average_stay_nights' => true,
        'average_lead_time_days' => true,
        'occupancy' => true,
        'available_nights' => true,
        'cancelled' => false,
        'cancelled_revenue' => false,
        'pending' => false,
        'owner_stays' => false,
        'owner_nights' => false
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
        'month' => 'month',
        'listing_id' => 'listingId',
        'listing_name' => 'listingName',
        'channel' => 'channel',
        'currency' => 'currency',
        'reservations' => 'reservations',
        'nights' => 'nights',
        'revenue' => 'revenue',
        'payout' => 'payout',
        'reservations_with_payout' => 'reservationsWithPayout',
        'average_nightly_rate' => 'averageNightlyRate',
        'average_stay_nights' => 'averageStayNights',
        'average_lead_time_days' => 'averageLeadTimeDays',
        'occupancy' => 'occupancy',
        'available_nights' => 'availableNights',
        'cancelled' => 'cancelled',
        'cancelled_revenue' => 'cancelledRevenue',
        'pending' => 'pending',
        'owner_stays' => 'ownerStays',
        'owner_nights' => 'ownerNights'
    ];

    /**
     * Array of attributes to setter functions (for deserialization of responses)
     *
     * @var array<string, string>
     */
    protected static array $setters = [
        'month' => 'setMonth',
        'listing_id' => 'setListingId',
        'listing_name' => 'setListingName',
        'channel' => 'setChannel',
        'currency' => 'setCurrency',
        'reservations' => 'setReservations',
        'nights' => 'setNights',
        'revenue' => 'setRevenue',
        'payout' => 'setPayout',
        'reservations_with_payout' => 'setReservationsWithPayout',
        'average_nightly_rate' => 'setAverageNightlyRate',
        'average_stay_nights' => 'setAverageStayNights',
        'average_lead_time_days' => 'setAverageLeadTimeDays',
        'occupancy' => 'setOccupancy',
        'available_nights' => 'setAvailableNights',
        'cancelled' => 'setCancelled',
        'cancelled_revenue' => 'setCancelledRevenue',
        'pending' => 'setPending',
        'owner_stays' => 'setOwnerStays',
        'owner_nights' => 'setOwnerNights'
    ];

    /**
     * Array of attributes to getter functions (for serialization of requests)
     *
     * @var array<string, string>
     */
    protected static array $getters = [
        'month' => 'getMonth',
        'listing_id' => 'getListingId',
        'listing_name' => 'getListingName',
        'channel' => 'getChannel',
        'currency' => 'getCurrency',
        'reservations' => 'getReservations',
        'nights' => 'getNights',
        'revenue' => 'getRevenue',
        'payout' => 'getPayout',
        'reservations_with_payout' => 'getReservationsWithPayout',
        'average_nightly_rate' => 'getAverageNightlyRate',
        'average_stay_nights' => 'getAverageStayNights',
        'average_lead_time_days' => 'getAverageLeadTimeDays',
        'occupancy' => 'getOccupancy',
        'available_nights' => 'getAvailableNights',
        'cancelled' => 'getCancelled',
        'cancelled_revenue' => 'getCancelledRevenue',
        'pending' => 'getPending',
        'owner_stays' => 'getOwnerStays',
        'owner_nights' => 'getOwnerNights'
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
        $this->setIfExists('month', $data ?? [], null);
        $this->setIfExists('listing_id', $data ?? [], null);
        $this->setIfExists('listing_name', $data ?? [], null);
        $this->setIfExists('channel', $data ?? [], null);
        $this->setIfExists('currency', $data ?? [], null);
        $this->setIfExists('reservations', $data ?? [], null);
        $this->setIfExists('nights', $data ?? [], null);
        $this->setIfExists('revenue', $data ?? [], null);
        $this->setIfExists('payout', $data ?? [], null);
        $this->setIfExists('reservations_with_payout', $data ?? [], null);
        $this->setIfExists('average_nightly_rate', $data ?? [], null);
        $this->setIfExists('average_stay_nights', $data ?? [], null);
        $this->setIfExists('average_lead_time_days', $data ?? [], null);
        $this->setIfExists('occupancy', $data ?? [], null);
        $this->setIfExists('available_nights', $data ?? [], null);
        $this->setIfExists('cancelled', $data ?? [], null);
        $this->setIfExists('cancelled_revenue', $data ?? [], null);
        $this->setIfExists('pending', $data ?? [], null);
        $this->setIfExists('owner_stays', $data ?? [], null);
        $this->setIfExists('owner_nights', $data ?? [], null);
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

        if ($this->container['currency'] === null && !$this->isNullableSetToNull('currency')) {
            $invalidProperties[] = "'currency' is required";
        }
        if ($this->container['reservations'] === null) {
            $invalidProperties[] = "'reservations' can't be null";
        }
        if ($this->container['nights'] === null) {
            $invalidProperties[] = "'nights' can't be null";
        }
        if ($this->container['revenue'] === null) {
            $invalidProperties[] = "'revenue' can't be null";
        }
        if ($this->container['payout'] === null) {
            $invalidProperties[] = "'payout' can't be null";
        }
        if ($this->container['cancelled'] === null) {
            $invalidProperties[] = "'cancelled' can't be null";
        }
        if ($this->container['pending'] === null) {
            $invalidProperties[] = "'pending' can't be null";
        }
        if ($this->container['owner_stays'] === null) {
            $invalidProperties[] = "'owner_stays' can't be null";
        }
        if ($this->container['owner_nights'] === null) {
            $invalidProperties[] = "'owner_nights' can't be null";
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
     * Gets month
     *
     * @return string|null
     */
    public function getMonth(): ?string
    {
        return $this->container['month'];
    }

    /**
     * Sets month
     *
     * @param string|null $month Present when grouped by `month` (YYYY-MM).
     *
     * @return $this
     */
    public function setMonth(?string $month): static
    {
        if (is_null($month)) {
            throw new InvalidArgumentException('non-nullable month cannot be null');
        }
        $this->container['month'] = $month;

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
     * @param string|null $listing_id Present when grouped by `listing`.
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
     * Gets listing_name
     *
     * @return string|null
     */
    public function getListingName(): ?string
    {
        return $this->container['listing_name'];
    }

    /**
     * Sets listing_name
     *
     * @param string|null $listing_name Present when grouped by `listing`.
     *
     * @return $this
     */
    public function setListingName(?string $listing_name): static
    {
        if (is_null($listing_name)) {
            array_push($this->openAPINullablesSetToNull, 'listing_name');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('listing_name', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['listing_name'] = $listing_name;

        return $this;
    }

    /**
     * Gets channel
     *
     * @return string|null
     */
    public function getChannel(): ?string
    {
        return $this->container['channel'];
    }

    /**
     * Sets channel
     *
     * @param string|null $channel Present when grouped by `channel`.
     *
     * @return $this
     */
    public function setChannel(?string $channel): static
    {
        if (is_null($channel)) {
            throw new InvalidArgumentException('non-nullable channel cannot be null');
        }
        $this->container['channel'] = $channel;

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
     * @param string|null $currency Money is never added across currencies: a period with two currencies has one row per currency.
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
     * Gets reservations
     *
     * @return int
     */
    public function getReservations(): int
    {
        return $this->container['reservations'];
    }

    /**
     * Sets reservations
     *
     * @param int $reservations Guest stays with at least one night counted (owner stays are in `ownerStays`).
     *
     * @return $this
     */
    public function setReservations(int $reservations): static
    {
        if (is_null($reservations)) {
            throw new InvalidArgumentException('non-nullable reservations cannot be null');
        }
        $this->container['reservations'] = $reservations;

        return $this;
    }

    /**
     * Gets nights
     *
     * @return int
     */
    public function getNights(): int
    {
        return $this->container['nights'];
    }

    /**
     * Sets nights
     *
     * @param int $nights Guest nights counted.
     *
     * @return $this
     */
    public function setNights(int $nights): static
    {
        if (is_null($nights)) {
            throw new InvalidArgumentException('non-nullable nights cannot be null');
        }
        $this->container['nights'] = $nights;

        return $this;
    }

    /**
     * Gets revenue
     *
     * @return float
     */
    public function getRevenue(): float
    {
        return $this->container['revenue'];
    }

    /**
     * Sets revenue
     *
     * @param float $revenue What guests paid (`financials.totalPrice`). With `basis=stay`, a stay that crosses the window or a month edge brings only the share of its nights inside.
     *
     * @return $this
     */
    public function setRevenue(float $revenue): static
    {
        if (is_null($revenue)) {
            throw new InvalidArgumentException('non-nullable revenue cannot be null');
        }
        $this->container['revenue'] = $revenue;

        return $this;
    }

    /**
     * Gets payout
     *
     * @return float
     */
    public function getPayout(): float
    {
        return $this->container['payout'];
    }

    /**
     * Sets payout
     *
     * @param float $payout What the host receives (`financials.host.revenue`), for reservations where it is known.
     *
     * @return $this
     */
    public function setPayout(float $payout): static
    {
        if (is_null($payout)) {
            throw new InvalidArgumentException('non-nullable payout cannot be null');
        }
        $this->container['payout'] = $payout;

        return $this;
    }

    /**
     * Gets reservations_with_payout
     *
     * @return int|null
     */
    public function getReservationsWithPayout(): ?int
    {
        return $this->container['reservations_with_payout'];
    }

    /**
     * Sets reservations_with_payout
     *
     * @param int|null $reservations_with_payout How many reservations `payout` covers.
     *
     * @return $this
     */
    public function setReservationsWithPayout(?int $reservations_with_payout): static
    {
        if (is_null($reservations_with_payout)) {
            throw new InvalidArgumentException('non-nullable reservations_with_payout cannot be null');
        }
        $this->container['reservations_with_payout'] = $reservations_with_payout;

        return $this;
    }

    /**
     * Gets average_nightly_rate
     *
     * @return float|null
     */
    public function getAverageNightlyRate(): ?float
    {
        return $this->container['average_nightly_rate'];
    }

    /**
     * Sets average_nightly_rate
     *
     * @param float|null $average_nightly_rate Accommodation per night (revenue per night when the accommodation subtotal is unknown).
     *
     * @return $this
     */
    public function setAverageNightlyRate(?float $average_nightly_rate): static
    {
        if (is_null($average_nightly_rate)) {
            array_push($this->openAPINullablesSetToNull, 'average_nightly_rate');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('average_nightly_rate', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['average_nightly_rate'] = $average_nightly_rate;

        return $this;
    }

    /**
     * Gets average_stay_nights
     *
     * @return float|null
     */
    public function getAverageStayNights(): ?float
    {
        return $this->container['average_stay_nights'];
    }

    /**
     * Sets average_stay_nights
     *
     * @param float|null $average_stay_nights average_stay_nights
     *
     * @return $this
     */
    public function setAverageStayNights(?float $average_stay_nights): static
    {
        if (is_null($average_stay_nights)) {
            array_push($this->openAPINullablesSetToNull, 'average_stay_nights');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('average_stay_nights', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['average_stay_nights'] = $average_stay_nights;

        return $this;
    }

    /**
     * Gets average_lead_time_days
     *
     * @return float|null
     */
    public function getAverageLeadTimeDays(): ?float
    {
        return $this->container['average_lead_time_days'];
    }

    /**
     * Sets average_lead_time_days
     *
     * @param float|null $average_lead_time_days Days between booking and check-in.
     *
     * @return $this
     */
    public function setAverageLeadTimeDays(?float $average_lead_time_days): static
    {
        if (is_null($average_lead_time_days)) {
            array_push($this->openAPINullablesSetToNull, 'average_lead_time_days');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('average_lead_time_days', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['average_lead_time_days'] = $average_lead_time_days;

        return $this;
    }

    /**
     * Gets occupancy
     *
     * @return float|null
     */
    public function getOccupancy(): ?float
    {
        return $this->container['occupancy'];
    }

    /**
     * Sets occupancy
     *
     * @param float|null $occupancy Guest nights / available nights, 0–1. Only with `basis=stay`, and not when grouped by `channel`. Blocked nights are not subtracted from availability.
     *
     * @return $this
     */
    public function setOccupancy(?float $occupancy): static
    {
        if (is_null($occupancy)) {
            array_push($this->openAPINullablesSetToNull, 'occupancy');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('occupancy', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['occupancy'] = $occupancy;

        return $this;
    }

    /**
     * Gets available_nights
     *
     * @return float|null
     */
    public function getAvailableNights(): ?float
    {
        return $this->container['available_nights'];
    }

    /**
     * Sets available_nights
     *
     * @param float|null $available_nights Active listings × days in the period (one listing per row when grouped by `listing`).
     *
     * @return $this
     */
    public function setAvailableNights(?float $available_nights): static
    {
        if (is_null($available_nights)) {
            array_push($this->openAPINullablesSetToNull, 'available_nights');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('available_nights', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['available_nights'] = $available_nights;

        return $this;
    }

    /**
     * Gets cancelled
     *
     * @return int
     */
    public function getCancelled(): int
    {
        return $this->container['cancelled'];
    }

    /**
     * Sets cancelled
     *
     * @param int $cancelled cancelled
     *
     * @return $this
     */
    public function setCancelled(int $cancelled): static
    {
        if (is_null($cancelled)) {
            throw new InvalidArgumentException('non-nullable cancelled cannot be null');
        }
        $this->container['cancelled'] = $cancelled;

        return $this;
    }

    /**
     * Gets cancelled_revenue
     *
     * @return float|null
     */
    public function getCancelledRevenue(): ?float
    {
        return $this->container['cancelled_revenue'];
    }

    /**
     * Sets cancelled_revenue
     *
     * @param float|null $cancelled_revenue cancelled_revenue
     *
     * @return $this
     */
    public function setCancelledRevenue(?float $cancelled_revenue): static
    {
        if (is_null($cancelled_revenue)) {
            throw new InvalidArgumentException('non-nullable cancelled_revenue cannot be null');
        }
        $this->container['cancelled_revenue'] = $cancelled_revenue;

        return $this;
    }

    /**
     * Gets pending
     *
     * @return int
     */
    public function getPending(): int
    {
        return $this->container['pending'];
    }

    /**
     * Sets pending
     *
     * @param int $pending Requests awaiting an answer; not in revenue or nights.
     *
     * @return $this
     */
    public function setPending(int $pending): static
    {
        if (is_null($pending)) {
            throw new InvalidArgumentException('non-nullable pending cannot be null');
        }
        $this->container['pending'] = $pending;

        return $this;
    }

    /**
     * Gets owner_stays
     *
     * @return int
     */
    public function getOwnerStays(): int
    {
        return $this->container['owner_stays'];
    }

    /**
     * Sets owner_stays
     *
     * @param int $owner_stays owner_stays
     *
     * @return $this
     */
    public function setOwnerStays(int $owner_stays): static
    {
        if (is_null($owner_stays)) {
            throw new InvalidArgumentException('non-nullable owner_stays cannot be null');
        }
        $this->container['owner_stays'] = $owner_stays;

        return $this;
    }

    /**
     * Gets owner_nights
     *
     * @return int
     */
    public function getOwnerNights(): int
    {
        return $this->container['owner_nights'];
    }

    /**
     * Sets owner_nights
     *
     * @param int $owner_nights owner_nights
     *
     * @return $this
     */
    public function setOwnerNights(int $owner_nights): static
    {
        if (is_null($owner_nights)) {
            throw new InvalidArgumentException('non-nullable owner_nights cannot be null');
        }
        $this->container['owner_nights'] = $owner_nights;

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


