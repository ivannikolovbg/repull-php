<?php
/**
 * CreateVrboOffer201Response
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
 * CreateVrboOffer201Response Class Doc Comment
 *
 * @package  Repull
 * @author   OpenAPI Generator team
 * @link     https://openapi-generator.tech
 * @implements ArrayAccess<string, mixed>
 */
class CreateVrboOffer201Response implements ModelInterface, ArrayAccess, JsonSerializable
{
    public const DISCRIMINATOR = null;

    /**
     * The original name of the model.
     *
     * @var string
     */
    protected static string $openAPIModelName = 'create_vrbo_offer_201_response';

    /**
     * Array of property to type mappings. Used for (de)serialization
     *
     * @var array<string, string>
     */
    protected static array $openAPITypes = [
        'conversation_id' => 'string',
        'channel' => 'string',
        'status' => 'string',
        'message' => 'string',
        'id' => 'string',
        'check_in' => 'string',
        'check_out' => 'string',
        'nights' => 'int',
        'guests' => 'array<string,mixed>',
        'total_price' => 'float',
        'currency' => 'string',
        'rental_amount' => 'float',
        'fees' => '\Repull\Model\GetVrboOffer200ResponseFeesInner[]',
        'damage_deposit' => 'float',
        'lines' => 'array<string,mixed>[]'
    ];

    /**
     * Array of property to format mappings. Used for (de)serialization
     *
     * @var array<string, string|null>
     */
    protected static array $openAPIFormats = [
        'conversation_id' => null,
        'channel' => null,
        'status' => null,
        'message' => null,
        'id' => null,
        'check_in' => null,
        'check_out' => null,
        'nights' => null,
        'guests' => null,
        'total_price' => null,
        'currency' => null,
        'rental_amount' => null,
        'fees' => null,
        'damage_deposit' => null,
        'lines' => null
    ];

    /**
     * Array of nullable properties. Used for (de)serialization
     *
     * @var array<string, bool>
     */
    protected static array $openAPINullables = [
        'conversation_id' => false,
        'channel' => false,
        'status' => false,
        'message' => true,
        'id' => false,
        'check_in' => true,
        'check_out' => true,
        'nights' => true,
        'guests' => true,
        'total_price' => true,
        'currency' => true,
        'rental_amount' => true,
        'fees' => false,
        'damage_deposit' => true,
        'lines' => false
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
        'conversation_id' => 'conversationId',
        'channel' => 'channel',
        'status' => 'status',
        'message' => 'message',
        'id' => 'id',
        'check_in' => 'checkIn',
        'check_out' => 'checkOut',
        'nights' => 'nights',
        'guests' => 'guests',
        'total_price' => 'totalPrice',
        'currency' => 'currency',
        'rental_amount' => 'rentalAmount',
        'fees' => 'fees',
        'damage_deposit' => 'damageDeposit',
        'lines' => 'lines'
    ];

    /**
     * Array of attributes to setter functions (for deserialization of responses)
     *
     * @var array<string, string>
     */
    protected static array $setters = [
        'conversation_id' => 'setConversationId',
        'channel' => 'setChannel',
        'status' => 'setStatus',
        'message' => 'setMessage',
        'id' => 'setId',
        'check_in' => 'setCheckIn',
        'check_out' => 'setCheckOut',
        'nights' => 'setNights',
        'guests' => 'setGuests',
        'total_price' => 'setTotalPrice',
        'currency' => 'setCurrency',
        'rental_amount' => 'setRentalAmount',
        'fees' => 'setFees',
        'damage_deposit' => 'setDamageDeposit',
        'lines' => 'setLines'
    ];

    /**
     * Array of attributes to getter functions (for serialization of requests)
     *
     * @var array<string, string>
     */
    protected static array $getters = [
        'conversation_id' => 'getConversationId',
        'channel' => 'getChannel',
        'status' => 'getStatus',
        'message' => 'getMessage',
        'id' => 'getId',
        'check_in' => 'getCheckIn',
        'check_out' => 'getCheckOut',
        'nights' => 'getNights',
        'guests' => 'getGuests',
        'total_price' => 'getTotalPrice',
        'currency' => 'getCurrency',
        'rental_amount' => 'getRentalAmount',
        'fees' => 'getFees',
        'damage_deposit' => 'getDamageDeposit',
        'lines' => 'getLines'
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
        $this->setIfExists('conversation_id', $data ?? [], null);
        $this->setIfExists('channel', $data ?? [], null);
        $this->setIfExists('status', $data ?? [], null);
        $this->setIfExists('message', $data ?? [], null);
        $this->setIfExists('id', $data ?? [], null);
        $this->setIfExists('check_in', $data ?? [], null);
        $this->setIfExists('check_out', $data ?? [], null);
        $this->setIfExists('nights', $data ?? [], null);
        $this->setIfExists('guests', $data ?? [], null);
        $this->setIfExists('total_price', $data ?? [], null);
        $this->setIfExists('currency', $data ?? [], null);
        $this->setIfExists('rental_amount', $data ?? [], null);
        $this->setIfExists('fees', $data ?? [], null);
        $this->setIfExists('damage_deposit', $data ?? [], null);
        $this->setIfExists('lines', $data ?? [], null);
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
     * Gets conversation_id
     *
     * @return string|null
     */
    public function getConversationId(): ?string
    {
        return $this->container['conversation_id'];
    }

    /**
     * Sets conversation_id
     *
     * @param string|null $conversation_id conversation_id
     *
     * @return $this
     */
    public function setConversationId(?string $conversation_id): static
    {
        if (is_null($conversation_id)) {
            throw new InvalidArgumentException('non-nullable conversation_id cannot be null');
        }
        $this->container['conversation_id'] = $conversation_id;

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
     * @param string|null $channel channel
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
     * Gets status
     *
     * @return string|null
     */
    public function getStatus(): ?string
    {
        return $this->container['status'];
    }

    /**
     * Sets status
     *
     * @param string|null $status status
     *
     * @return $this
     */
    public function setStatus(?string $status): static
    {
        if (is_null($status)) {
            throw new InvalidArgumentException('non-nullable status cannot be null');
        }
        $this->container['status'] = $status;

        return $this;
    }

    /**
     * Gets message
     *
     * @return string|null
     */
    public function getMessage(): ?string
    {
        return $this->container['message'];
    }

    /**
     * Sets message
     *
     * @param string|null $message message
     *
     * @return $this
     */
    public function setMessage(?string $message): static
    {
        if (is_null($message)) {
            array_push($this->openAPINullablesSetToNull, 'message');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('message', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['message'] = $message;

        return $this;
    }

    /**
     * Gets id
     *
     * @return string|null
     */
    public function getId(): ?string
    {
        return $this->container['id'];
    }

    /**
     * Sets id
     *
     * @param string|null $id Vrbo keeps one live quote per conversation: always `current`.
     *
     * @return $this
     */
    public function setId(?string $id): static
    {
        if (is_null($id)) {
            throw new InvalidArgumentException('non-nullable id cannot be null');
        }
        $this->container['id'] = $id;

        return $this;
    }

    /**
     * Gets check_in
     *
     * @return string|null
     */
    public function getCheckIn(): ?string
    {
        return $this->container['check_in'];
    }

    /**
     * Sets check_in
     *
     * @param string|null $check_in check_in
     *
     * @return $this
     */
    public function setCheckIn(?string $check_in): static
    {
        if (is_null($check_in)) {
            array_push($this->openAPINullablesSetToNull, 'check_in');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('check_in', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['check_in'] = $check_in;

        return $this;
    }

    /**
     * Gets check_out
     *
     * @return string|null
     */
    public function getCheckOut(): ?string
    {
        return $this->container['check_out'];
    }

    /**
     * Sets check_out
     *
     * @param string|null $check_out check_out
     *
     * @return $this
     */
    public function setCheckOut(?string $check_out): static
    {
        if (is_null($check_out)) {
            array_push($this->openAPINullablesSetToNull, 'check_out');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('check_out', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['check_out'] = $check_out;

        return $this;
    }

    /**
     * Gets nights
     *
     * @return int|null
     */
    public function getNights(): ?int
    {
        return $this->container['nights'];
    }

    /**
     * Sets nights
     *
     * @param int|null $nights nights
     *
     * @return $this
     */
    public function setNights(?int $nights): static
    {
        if (is_null($nights)) {
            array_push($this->openAPINullablesSetToNull, 'nights');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('nights', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['nights'] = $nights;

        return $this;
    }

    /**
     * Gets guests
     *
     * @return array<string,mixed>|null
     */
    public function getGuests(): ?array
    {
        return $this->container['guests'];
    }

    /**
     * Sets guests
     *
     * @param array<string,mixed>|null $guests guests
     *
     * @return $this
     */
    public function setGuests(?array $guests): static
    {
        if (is_null($guests)) {
            array_push($this->openAPINullablesSetToNull, 'guests');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('guests', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['guests'] = $guests;

        return $this;
    }

    /**
     * Gets total_price
     *
     * @return float|null
     */
    public function getTotalPrice(): ?float
    {
        return $this->container['total_price'];
    }

    /**
     * Sets total_price
     *
     * @param float|null $total_price Vrbo's guest total (with its taxes and service fee).
     *
     * @return $this
     */
    public function setTotalPrice(?float $total_price): static
    {
        if (is_null($total_price)) {
            array_push($this->openAPINullablesSetToNull, 'total_price');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('total_price', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['total_price'] = $total_price;

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
     * Gets rental_amount
     *
     * @return float|null
     */
    public function getRentalAmount(): ?float
    {
        return $this->container['rental_amount'];
    }

    /**
     * Sets rental_amount
     *
     * @param float|null $rental_amount rental_amount
     *
     * @return $this
     */
    public function setRentalAmount(?float $rental_amount): static
    {
        if (is_null($rental_amount)) {
            array_push($this->openAPINullablesSetToNull, 'rental_amount');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('rental_amount', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['rental_amount'] = $rental_amount;

        return $this;
    }

    /**
     * Gets fees
     *
     * @return \Repull\Model\GetVrboOffer200ResponseFeesInner[]|null
     */
    public function getFees(): ?array
    {
        return $this->container['fees'];
    }

    /**
     * Sets fees
     *
     * @param \Repull\Model\GetVrboOffer200ResponseFeesInner[]|null $fees fees
     *
     * @return $this
     */
    public function setFees(?array $fees): static
    {
        if (is_null($fees)) {
            throw new InvalidArgumentException('non-nullable fees cannot be null');
        }
        $this->container['fees'] = $fees;

        return $this;
    }

    /**
     * Gets damage_deposit
     *
     * @return float|null
     */
    public function getDamageDeposit(): ?float
    {
        return $this->container['damage_deposit'];
    }

    /**
     * Sets damage_deposit
     *
     * @param float|null $damage_deposit damage_deposit
     *
     * @return $this
     */
    public function setDamageDeposit(?float $damage_deposit): static
    {
        if (is_null($damage_deposit)) {
            array_push($this->openAPINullablesSetToNull, 'damage_deposit');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('damage_deposit', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['damage_deposit'] = $damage_deposit;

        return $this;
    }

    /**
     * Gets lines
     *
     * @return array<string,mixed>[]|null
     */
    public function getLines(): ?array
    {
        return $this->container['lines'];
    }

    /**
     * Sets lines
     *
     * @param array<string,mixed>[]|null $lines lines
     *
     * @return $this
     */
    public function setLines(?array $lines): static
    {
        if (is_null($lines)) {
            throw new InvalidArgumentException('non-nullable lines cannot be null');
        }
        $this->container['lines'] = $lines;

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


