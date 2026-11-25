<?php
/**
 * TiketAriUpdate
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
 * TiketAriUpdate Class Doc Comment
 *
 * @description One date range for one room type (and, for &#x60;rates&#x60; and &#x60;restrictions&#x60;, one rate plan). Which fields apply depends on the request&#39;s &#x60;type&#x60;.
 * @package  Repull
 * @author   OpenAPI Generator team
 * @link     https://openapi-generator.tech
 * @implements ArrayAccess<string, mixed>
 */
class TiketAriUpdate implements ModelInterface, ArrayAccess, JsonSerializable
{
    public const DISCRIMINATOR = null;

    /**
     * The original name of the model.
     *
     * @var string
     */
    protected static string $openAPIModelName = 'TiketAriUpdate';

    /**
     * Array of property to type mappings. Used for (de)serialization
     *
     * @var array<string, string>
     */
    protected static array $openAPITypes = [
        'room_type_code' => 'string',
        'rate_plan_code' => 'string',
        'start' => '\DateTime',
        'end' => '\DateTime',
        'available_rooms' => 'int',
        'price' => 'float',
        'occupancy_prices' => '\Repull\Model\TiketOccupancyPrice[]',
        'child_prices' => '\Repull\Model\TiketChildPrice[]',
        'currency' => 'string',
        'min_stay' => 'int',
        'max_stay' => 'int',
        'stop_sell' => 'bool',
        'closed_to_arrival' => 'bool',
        'closed_to_departure' => 'bool',
        'min_advance_days' => 'int',
        'max_advance_days' => 'int'
    ];

    /**
     * Array of property to format mappings. Used for (de)serialization
     *
     * @var array<string, string|null>
     */
    protected static array $openAPIFormats = [
        'room_type_code' => null,
        'rate_plan_code' => null,
        'start' => 'date',
        'end' => 'date',
        'available_rooms' => null,
        'price' => 'double',
        'occupancy_prices' => null,
        'child_prices' => null,
        'currency' => null,
        'min_stay' => null,
        'max_stay' => null,
        'stop_sell' => null,
        'closed_to_arrival' => null,
        'closed_to_departure' => null,
        'min_advance_days' => null,
        'max_advance_days' => null
    ];

    /**
     * Array of nullable properties. Used for (de)serialization
     *
     * @var array<string, bool>
     */
    protected static array $openAPINullables = [
        'room_type_code' => false,
        'rate_plan_code' => false,
        'start' => false,
        'end' => false,
        'available_rooms' => false,
        'price' => false,
        'occupancy_prices' => false,
        'child_prices' => false,
        'currency' => false,
        'min_stay' => false,
        'max_stay' => false,
        'stop_sell' => false,
        'closed_to_arrival' => false,
        'closed_to_departure' => false,
        'min_advance_days' => false,
        'max_advance_days' => false
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
        'room_type_code' => 'roomTypeCode',
        'rate_plan_code' => 'ratePlanCode',
        'start' => 'start',
        'end' => 'end',
        'available_rooms' => 'availableRooms',
        'price' => 'price',
        'occupancy_prices' => 'occupancyPrices',
        'child_prices' => 'childPrices',
        'currency' => 'currency',
        'min_stay' => 'minStay',
        'max_stay' => 'maxStay',
        'stop_sell' => 'stopSell',
        'closed_to_arrival' => 'closedToArrival',
        'closed_to_departure' => 'closedToDeparture',
        'min_advance_days' => 'minAdvanceDays',
        'max_advance_days' => 'maxAdvanceDays'
    ];

    /**
     * Array of attributes to setter functions (for deserialization of responses)
     *
     * @var array<string, string>
     */
    protected static array $setters = [
        'room_type_code' => 'setRoomTypeCode',
        'rate_plan_code' => 'setRatePlanCode',
        'start' => 'setStart',
        'end' => 'setEnd',
        'available_rooms' => 'setAvailableRooms',
        'price' => 'setPrice',
        'occupancy_prices' => 'setOccupancyPrices',
        'child_prices' => 'setChildPrices',
        'currency' => 'setCurrency',
        'min_stay' => 'setMinStay',
        'max_stay' => 'setMaxStay',
        'stop_sell' => 'setStopSell',
        'closed_to_arrival' => 'setClosedToArrival',
        'closed_to_departure' => 'setClosedToDeparture',
        'min_advance_days' => 'setMinAdvanceDays',
        'max_advance_days' => 'setMaxAdvanceDays'
    ];

    /**
     * Array of attributes to getter functions (for serialization of requests)
     *
     * @var array<string, string>
     */
    protected static array $getters = [
        'room_type_code' => 'getRoomTypeCode',
        'rate_plan_code' => 'getRatePlanCode',
        'start' => 'getStart',
        'end' => 'getEnd',
        'available_rooms' => 'getAvailableRooms',
        'price' => 'getPrice',
        'occupancy_prices' => 'getOccupancyPrices',
        'child_prices' => 'getChildPrices',
        'currency' => 'getCurrency',
        'min_stay' => 'getMinStay',
        'max_stay' => 'getMaxStay',
        'stop_sell' => 'getStopSell',
        'closed_to_arrival' => 'getClosedToArrival',
        'closed_to_departure' => 'getClosedToDeparture',
        'min_advance_days' => 'getMinAdvanceDays',
        'max_advance_days' => 'getMaxAdvanceDays'
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
        $this->setIfExists('room_type_code', $data ?? [], null);
        $this->setIfExists('rate_plan_code', $data ?? [], null);
        $this->setIfExists('start', $data ?? [], null);
        $this->setIfExists('end', $data ?? [], null);
        $this->setIfExists('available_rooms', $data ?? [], null);
        $this->setIfExists('price', $data ?? [], null);
        $this->setIfExists('occupancy_prices', $data ?? [], null);
        $this->setIfExists('child_prices', $data ?? [], null);
        $this->setIfExists('currency', $data ?? [], null);
        $this->setIfExists('min_stay', $data ?? [], null);
        $this->setIfExists('max_stay', $data ?? [], null);
        $this->setIfExists('stop_sell', $data ?? [], null);
        $this->setIfExists('closed_to_arrival', $data ?? [], null);
        $this->setIfExists('closed_to_departure', $data ?? [], null);
        $this->setIfExists('min_advance_days', $data ?? [], null);
        $this->setIfExists('max_advance_days', $data ?? [], null);
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

        if ($this->container['start'] === null) {
            $invalidProperties[] = "'start' can't be null";
        }
        if ($this->container['end'] === null) {
            $invalidProperties[] = "'end' can't be null";
        }
        if (!is_null($this->container['available_rooms']) && ($this->container['available_rooms'] < 0)) {
            $invalidProperties[] = "invalid value for 'available_rooms', must be bigger than or equal to 0.";
        }

        if (!is_null($this->container['min_stay']) && ($this->container['min_stay'] > 365)) {
            $invalidProperties[] = "invalid value for 'min_stay', must be smaller than or equal to 365.";
        }

        if (!is_null($this->container['min_stay']) && ($this->container['min_stay'] < 0)) {
            $invalidProperties[] = "invalid value for 'min_stay', must be bigger than or equal to 0.";
        }

        if (!is_null($this->container['max_stay']) && ($this->container['max_stay'] > 365)) {
            $invalidProperties[] = "invalid value for 'max_stay', must be smaller than or equal to 365.";
        }

        if (!is_null($this->container['max_stay']) && ($this->container['max_stay'] < 0)) {
            $invalidProperties[] = "invalid value for 'max_stay', must be bigger than or equal to 0.";
        }

        if (!is_null($this->container['min_advance_days']) && ($this->container['min_advance_days'] > 500)) {
            $invalidProperties[] = "invalid value for 'min_advance_days', must be smaller than or equal to 500.";
        }

        if (!is_null($this->container['min_advance_days']) && ($this->container['min_advance_days'] < 0)) {
            $invalidProperties[] = "invalid value for 'min_advance_days', must be bigger than or equal to 0.";
        }

        if (!is_null($this->container['max_advance_days']) && ($this->container['max_advance_days'] > 500)) {
            $invalidProperties[] = "invalid value for 'max_advance_days', must be smaller than or equal to 500.";
        }

        if (!is_null($this->container['max_advance_days']) && ($this->container['max_advance_days'] < 0)) {
            $invalidProperties[] = "invalid value for 'max_advance_days', must be bigger than or equal to 0.";
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
     * @param string|null $room_type_code tiket.com room type id. Required on `PUT /v1/channels/tiket/availability`; on the listing route it is always the listing's room.
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
     * Gets rate_plan_code
     *
     * @return string|null
     */
    public function getRatePlanCode(): ?string
    {
        return $this->container['rate_plan_code'];
    }

    /**
     * Sets rate_plan_code
     *
     * @param string|null $rate_plan_code tiket.com rate plan id. Required for `rates` and `restrictions` on `PUT /v1/channels/tiket/availability`; on the listing route it defaults to every rate plan of the room selected for pushes.
     *
     * @return $this
     */
    public function setRatePlanCode(?string $rate_plan_code): static
    {
        if (is_null($rate_plan_code)) {
            throw new InvalidArgumentException('non-nullable rate_plan_code cannot be null');
        }
        $this->container['rate_plan_code'] = $rate_plan_code;

        return $this;
    }

    /**
     * Gets start
     *
     * @return \DateTime
     */
    public function getStart(): \DateTime
    {
        return $this->container['start'];
    }

    /**
     * Sets start
     *
     * @param \DateTime $start First night. tiket.com accepts today up to 500 days ahead.
     *
     * @return $this
     */
    public function setStart(\DateTime $start): static
    {
        if (is_null($start)) {
            throw new InvalidArgumentException('non-nullable start cannot be null');
        }
        $this->container['start'] = $start;

        return $this;
    }

    /**
     * Gets end
     *
     * @return \DateTime
     */
    public function getEnd(): \DateTime
    {
        return $this->container['end'];
    }

    /**
     * Sets end
     *
     * @param \DateTime $end Last night, inclusive.
     *
     * @return $this
     */
    public function setEnd(\DateTime $end): static
    {
        if (is_null($end)) {
            throw new InvalidArgumentException('non-nullable end cannot be null');
        }
        $this->container['end'] = $end;

        return $this;
    }

    /**
     * Gets available_rooms
     *
     * @return int|null
     */
    public function getAvailableRooms(): ?int
    {
        return $this->container['available_rooms'];
    }

    /**
     * Sets available_rooms
     *
     * @param int|null $available_rooms `availability`: rooms to sell.
     *
     * @return $this
     */
    public function setAvailableRooms(?int $available_rooms): static
    {
        if (is_null($available_rooms)) {
            throw new InvalidArgumentException('non-nullable available_rooms cannot be null');
        }

        if (($available_rooms < 0)) {
            throw new InvalidArgumentException('invalid value for $available_rooms when calling TiketAriUpdate., must be bigger than or equal to 0.');
        }

        $this->container['available_rooms'] = $available_rooms;

        return $this;
    }

    /**
     * Gets price
     *
     * @return float|null
     */
    public function getPrice(): ?float
    {
        return $this->container['price'];
    }

    /**
     * Sets price
     *
     * @param float|null $price `rates` on a Room Based property: price per night, tax included. Greater than 0.
     *
     * @return $this
     */
    public function setPrice(?float $price): static
    {
        if (is_null($price)) {
            throw new InvalidArgumentException('non-nullable price cannot be null');
        }
        $this->container['price'] = $price;

        return $this;
    }

    /**
     * Gets occupancy_prices
     *
     * @return \Repull\Model\TiketOccupancyPrice[]|null
     */
    public function getOccupancyPrices(): ?array
    {
        return $this->container['occupancy_prices'];
    }

    /**
     * Sets occupancy_prices
     *
     * @param \Repull\Model\TiketOccupancyPrice[]|null $occupancy_prices `rates` on an Occupancy Based property. Send `price` or `occupancyPrices`, not both.
     *
     * @return $this
     */
    public function setOccupancyPrices(?array $occupancy_prices): static
    {
        if (is_null($occupancy_prices)) {
            throw new InvalidArgumentException('non-nullable occupancy_prices cannot be null');
        }
        $this->container['occupancy_prices'] = $occupancy_prices;

        return $this;
    }

    /**
     * Gets child_prices
     *
     * @return \Repull\Model\TiketChildPrice[]|null
     */
    public function getChildPrices(): ?array
    {
        return $this->container['child_prices'];
    }

    /**
     * Sets child_prices
     *
     * @param \Repull\Model\TiketChildPrice[]|null $child_prices Children prices. **Children rates are paused.** tiket.com has asked every channel manager not to send children rates until it fixes an issue on its side, so a write that includes `childPrices` is refused with `422 invalid_params` and nothing is sent. Children rates set in the tiket.com extranet still apply. Send `price` or `occupancyPrices` without `childPrices`.
     *
     * @return $this
     */
    public function setChildPrices(?array $child_prices): static
    {
        if (is_null($child_prices)) {
            throw new InvalidArgumentException('non-nullable child_prices cannot be null');
        }
        $this->container['child_prices'] = $child_prices;

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
     * @param string|null $currency ISO 4217. Defaults to the property currency.
     *
     * @return $this
     */
    public function setCurrency(?string $currency): static
    {
        if (is_null($currency)) {
            throw new InvalidArgumentException('non-nullable currency cannot be null');
        }
        $this->container['currency'] = $currency;

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
     * @param int|null $min_stay `restrictions`: minimum nights.
     *
     * @return $this
     */
    public function setMinStay(?int $min_stay): static
    {
        if (is_null($min_stay)) {
            throw new InvalidArgumentException('non-nullable min_stay cannot be null');
        }

        if (($min_stay > 365)) {
            throw new InvalidArgumentException('invalid value for $min_stay when calling TiketAriUpdate., must be smaller than or equal to 365.');
        }
        if (($min_stay < 0)) {
            throw new InvalidArgumentException('invalid value for $min_stay when calling TiketAriUpdate., must be bigger than or equal to 0.');
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
     * @param int|null $max_stay `restrictions`: maximum nights.
     *
     * @return $this
     */
    public function setMaxStay(?int $max_stay): static
    {
        if (is_null($max_stay)) {
            throw new InvalidArgumentException('non-nullable max_stay cannot be null');
        }

        if (($max_stay > 365)) {
            throw new InvalidArgumentException('invalid value for $max_stay when calling TiketAriUpdate., must be smaller than or equal to 365.');
        }
        if (($max_stay < 0)) {
            throw new InvalidArgumentException('invalid value for $max_stay when calling TiketAriUpdate., must be bigger than or equal to 0.');
        }

        $this->container['max_stay'] = $max_stay;

        return $this;
    }

    /**
     * Gets stop_sell
     *
     * @return bool|null
     */
    public function getStopSell(): ?bool
    {
        return $this->container['stop_sell'];
    }

    /**
     * Sets stop_sell
     *
     * @param bool|null $stop_sell `restrictions`: close (`true`) or open (`false`) the rate plan. With `availability` and a `ratePlanCode`, also closes that rate plan.
     *
     * @return $this
     */
    public function setStopSell(?bool $stop_sell): static
    {
        if (is_null($stop_sell)) {
            throw new InvalidArgumentException('non-nullable stop_sell cannot be null');
        }
        $this->container['stop_sell'] = $stop_sell;

        return $this;
    }

    /**
     * Gets closed_to_arrival
     *
     * @return bool|null
     */
    public function getClosedToArrival(): ?bool
    {
        return $this->container['closed_to_arrival'];
    }

    /**
     * Sets closed_to_arrival
     *
     * @param bool|null $closed_to_arrival `restrictions`.
     *
     * @return $this
     */
    public function setClosedToArrival(?bool $closed_to_arrival): static
    {
        if (is_null($closed_to_arrival)) {
            throw new InvalidArgumentException('non-nullable closed_to_arrival cannot be null');
        }
        $this->container['closed_to_arrival'] = $closed_to_arrival;

        return $this;
    }

    /**
     * Gets closed_to_departure
     *
     * @return bool|null
     */
    public function getClosedToDeparture(): ?bool
    {
        return $this->container['closed_to_departure'];
    }

    /**
     * Sets closed_to_departure
     *
     * @param bool|null $closed_to_departure `restrictions`.
     *
     * @return $this
     */
    public function setClosedToDeparture(?bool $closed_to_departure): static
    {
        if (is_null($closed_to_departure)) {
            throw new InvalidArgumentException('non-nullable closed_to_departure cannot be null');
        }
        $this->container['closed_to_departure'] = $closed_to_departure;

        return $this;
    }

    /**
     * Gets min_advance_days
     *
     * @return int|null
     */
    public function getMinAdvanceDays(): ?int
    {
        return $this->container['min_advance_days'];
    }

    /**
     * Sets min_advance_days
     *
     * @param int|null $min_advance_days `restrictions`: minimum days between booking and arrival.
     *
     * @return $this
     */
    public function setMinAdvanceDays(?int $min_advance_days): static
    {
        if (is_null($min_advance_days)) {
            throw new InvalidArgumentException('non-nullable min_advance_days cannot be null');
        }

        if (($min_advance_days > 500)) {
            throw new InvalidArgumentException('invalid value for $min_advance_days when calling TiketAriUpdate., must be smaller than or equal to 500.');
        }
        if (($min_advance_days < 0)) {
            throw new InvalidArgumentException('invalid value for $min_advance_days when calling TiketAriUpdate., must be bigger than or equal to 0.');
        }

        $this->container['min_advance_days'] = $min_advance_days;

        return $this;
    }

    /**
     * Gets max_advance_days
     *
     * @return int|null
     */
    public function getMaxAdvanceDays(): ?int
    {
        return $this->container['max_advance_days'];
    }

    /**
     * Sets max_advance_days
     *
     * @param int|null $max_advance_days `restrictions`: maximum days between booking and arrival.
     *
     * @return $this
     */
    public function setMaxAdvanceDays(?int $max_advance_days): static
    {
        if (is_null($max_advance_days)) {
            throw new InvalidArgumentException('non-nullable max_advance_days cannot be null');
        }

        if (($max_advance_days > 500)) {
            throw new InvalidArgumentException('invalid value for $max_advance_days when calling TiketAriUpdate., must be smaller than or equal to 500.');
        }
        if (($max_advance_days < 0)) {
            throw new InvalidArgumentException('invalid value for $max_advance_days when calling TiketAriUpdate., must be bigger than or equal to 0.');
        }

        $this->container['max_advance_days'] = $max_advance_days;

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


