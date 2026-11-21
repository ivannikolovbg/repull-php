<?php
/**
 * GetVrboReservation200ResponseData
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
 * GetVrboReservation200ResponseData Class Doc Comment
 *
 * @description The booking as Vrbo shows it; with &#x60;?options&#x3D;&#x60;, Vrbo&#39;s choices for that action.
 * @package  Repull
 * @author   OpenAPI Generator team
 * @link     https://openapi-generator.tech
 * @implements ArrayAccess<string, mixed>
 */
class GetVrboReservation200ResponseData implements ModelInterface, ArrayAccess, JsonSerializable
{
    public const DISCRIMINATOR = null;

    /**
     * The original name of the model.
     *
     * @var string
     */
    protected static string $openAPIModelName = 'getVrboReservation_200_response_data';

    /**
     * Array of property to type mappings. Used for (de)serialization
     *
     * @var array<string, string>
     */
    protected static array $openAPITypes = [
        'reservation_code' => 'string',
        'reservation_uuid' => 'string',
        'state' => 'string',
        'check_in' => 'string',
        'check_out' => 'string',
        'adults' => 'int',
        'children' => 'int',
        'pets' => 'int',
        'guest_name' => 'string',
        'currency' => 'string',
        'guest_lines' => '\Repull\Model\CreateVrboAlteration200ResponseDataPaymentLinesInner[]',
        'host_lines' => '\Repull\Model\CreateVrboAlteration200ResponseDataPaymentLinesInner[]',
        'total_guest_payment' => 'float',
        'payout' => 'float',
        'cancellation_policy' => 'string',
        'damage_deposit' => 'float',
        'available_actions' => '\Repull\Model\GetVrboReservation200ResponseDataAvailableActionsInner[]'
    ];

    /**
     * Array of property to format mappings. Used for (de)serialization
     *
     * @var array<string, string|null>
     */
    protected static array $openAPIFormats = [
        'reservation_code' => null,
        'reservation_uuid' => null,
        'state' => null,
        'check_in' => null,
        'check_out' => null,
        'adults' => null,
        'children' => null,
        'pets' => null,
        'guest_name' => null,
        'currency' => null,
        'guest_lines' => null,
        'host_lines' => null,
        'total_guest_payment' => null,
        'payout' => null,
        'cancellation_policy' => null,
        'damage_deposit' => null,
        'available_actions' => null
    ];

    /**
     * Array of nullable properties. Used for (de)serialization
     *
     * @var array<string, bool>
     */
    protected static array $openAPINullables = [
        'reservation_code' => false,
        'reservation_uuid' => false,
        'state' => true,
        'check_in' => true,
        'check_out' => true,
        'adults' => true,
        'children' => true,
        'pets' => true,
        'guest_name' => true,
        'currency' => true,
        'guest_lines' => false,
        'host_lines' => false,
        'total_guest_payment' => true,
        'payout' => true,
        'cancellation_policy' => true,
        'damage_deposit' => true,
        'available_actions' => false
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
        'reservation_code' => 'reservationCode',
        'reservation_uuid' => 'reservationUuid',
        'state' => 'state',
        'check_in' => 'checkIn',
        'check_out' => 'checkOut',
        'adults' => 'adults',
        'children' => 'children',
        'pets' => 'pets',
        'guest_name' => 'guestName',
        'currency' => 'currency',
        'guest_lines' => 'guestLines',
        'host_lines' => 'hostLines',
        'total_guest_payment' => 'totalGuestPayment',
        'payout' => 'payout',
        'cancellation_policy' => 'cancellationPolicy',
        'damage_deposit' => 'damageDeposit',
        'available_actions' => 'availableActions'
    ];

    /**
     * Array of attributes to setter functions (for deserialization of responses)
     *
     * @var array<string, string>
     */
    protected static array $setters = [
        'reservation_code' => 'setReservationCode',
        'reservation_uuid' => 'setReservationUuid',
        'state' => 'setState',
        'check_in' => 'setCheckIn',
        'check_out' => 'setCheckOut',
        'adults' => 'setAdults',
        'children' => 'setChildren',
        'pets' => 'setPets',
        'guest_name' => 'setGuestName',
        'currency' => 'setCurrency',
        'guest_lines' => 'setGuestLines',
        'host_lines' => 'setHostLines',
        'total_guest_payment' => 'setTotalGuestPayment',
        'payout' => 'setPayout',
        'cancellation_policy' => 'setCancellationPolicy',
        'damage_deposit' => 'setDamageDeposit',
        'available_actions' => 'setAvailableActions'
    ];

    /**
     * Array of attributes to getter functions (for serialization of requests)
     *
     * @var array<string, string>
     */
    protected static array $getters = [
        'reservation_code' => 'getReservationCode',
        'reservation_uuid' => 'getReservationUuid',
        'state' => 'getState',
        'check_in' => 'getCheckIn',
        'check_out' => 'getCheckOut',
        'adults' => 'getAdults',
        'children' => 'getChildren',
        'pets' => 'getPets',
        'guest_name' => 'getGuestName',
        'currency' => 'getCurrency',
        'guest_lines' => 'getGuestLines',
        'host_lines' => 'getHostLines',
        'total_guest_payment' => 'getTotalGuestPayment',
        'payout' => 'getPayout',
        'cancellation_policy' => 'getCancellationPolicy',
        'damage_deposit' => 'getDamageDeposit',
        'available_actions' => 'getAvailableActions'
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
        $this->setIfExists('reservation_code', $data ?? [], null);
        $this->setIfExists('reservation_uuid', $data ?? [], null);
        $this->setIfExists('state', $data ?? [], null);
        $this->setIfExists('check_in', $data ?? [], null);
        $this->setIfExists('check_out', $data ?? [], null);
        $this->setIfExists('adults', $data ?? [], null);
        $this->setIfExists('children', $data ?? [], null);
        $this->setIfExists('pets', $data ?? [], null);
        $this->setIfExists('guest_name', $data ?? [], null);
        $this->setIfExists('currency', $data ?? [], null);
        $this->setIfExists('guest_lines', $data ?? [], null);
        $this->setIfExists('host_lines', $data ?? [], null);
        $this->setIfExists('total_guest_payment', $data ?? [], null);
        $this->setIfExists('payout', $data ?? [], null);
        $this->setIfExists('cancellation_policy', $data ?? [], null);
        $this->setIfExists('damage_deposit', $data ?? [], null);
        $this->setIfExists('available_actions', $data ?? [], null);
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
     * Gets reservation_code
     *
     * @return string|null
     */
    public function getReservationCode(): ?string
    {
        return $this->container['reservation_code'];
    }

    /**
     * Sets reservation_code
     *
     * @param string|null $reservation_code reservation_code
     *
     * @return $this
     */
    public function setReservationCode(?string $reservation_code): static
    {
        if (is_null($reservation_code)) {
            throw new InvalidArgumentException('non-nullable reservation_code cannot be null');
        }
        $this->container['reservation_code'] = $reservation_code;

        return $this;
    }

    /**
     * Gets reservation_uuid
     *
     * @return string|null
     */
    public function getReservationUuid(): ?string
    {
        return $this->container['reservation_uuid'];
    }

    /**
     * Sets reservation_uuid
     *
     * @param string|null $reservation_uuid reservation_uuid
     *
     * @return $this
     */
    public function setReservationUuid(?string $reservation_uuid): static
    {
        if (is_null($reservation_uuid)) {
            throw new InvalidArgumentException('non-nullable reservation_uuid cannot be null');
        }
        $this->container['reservation_uuid'] = $reservation_uuid;

        return $this;
    }

    /**
     * Gets state
     *
     * @return string|null
     */
    public function getState(): ?string
    {
        return $this->container['state'];
    }

    /**
     * Sets state
     *
     * @param string|null $state state
     *
     * @return $this
     */
    public function setState(?string $state): static
    {
        if (is_null($state)) {
            array_push($this->openAPINullablesSetToNull, 'state');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('state', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['state'] = $state;

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
     * Gets adults
     *
     * @return int|null
     */
    public function getAdults(): ?int
    {
        return $this->container['adults'];
    }

    /**
     * Sets adults
     *
     * @param int|null $adults adults
     *
     * @return $this
     */
    public function setAdults(?int $adults): static
    {
        if (is_null($adults)) {
            array_push($this->openAPINullablesSetToNull, 'adults');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('adults', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['adults'] = $adults;

        return $this;
    }

    /**
     * Gets children
     *
     * @return int|null
     */
    public function getChildren(): ?int
    {
        return $this->container['children'];
    }

    /**
     * Sets children
     *
     * @param int|null $children children
     *
     * @return $this
     */
    public function setChildren(?int $children): static
    {
        if (is_null($children)) {
            array_push($this->openAPINullablesSetToNull, 'children');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('children', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['children'] = $children;

        return $this;
    }

    /**
     * Gets pets
     *
     * @return int|null
     */
    public function getPets(): ?int
    {
        return $this->container['pets'];
    }

    /**
     * Sets pets
     *
     * @param int|null $pets pets
     *
     * @return $this
     */
    public function setPets(?int $pets): static
    {
        if (is_null($pets)) {
            array_push($this->openAPINullablesSetToNull, 'pets');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('pets', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['pets'] = $pets;

        return $this;
    }

    /**
     * Gets guest_name
     *
     * @return string|null
     */
    public function getGuestName(): ?string
    {
        return $this->container['guest_name'];
    }

    /**
     * Sets guest_name
     *
     * @param string|null $guest_name guest_name
     *
     * @return $this
     */
    public function setGuestName(?string $guest_name): static
    {
        if (is_null($guest_name)) {
            array_push($this->openAPINullablesSetToNull, 'guest_name');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('guest_name', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['guest_name'] = $guest_name;

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
     * Gets guest_lines
     *
     * @return \Repull\Model\CreateVrboAlteration200ResponseDataPaymentLinesInner[]|null
     */
    public function getGuestLines(): ?array
    {
        return $this->container['guest_lines'];
    }

    /**
     * Sets guest_lines
     *
     * @param \Repull\Model\CreateVrboAlteration200ResponseDataPaymentLinesInner[]|null $guest_lines guest_lines
     *
     * @return $this
     */
    public function setGuestLines(?array $guest_lines): static
    {
        if (is_null($guest_lines)) {
            throw new InvalidArgumentException('non-nullable guest_lines cannot be null');
        }
        $this->container['guest_lines'] = $guest_lines;

        return $this;
    }

    /**
     * Gets host_lines
     *
     * @return \Repull\Model\CreateVrboAlteration200ResponseDataPaymentLinesInner[]|null
     */
    public function getHostLines(): ?array
    {
        return $this->container['host_lines'];
    }

    /**
     * Sets host_lines
     *
     * @param \Repull\Model\CreateVrboAlteration200ResponseDataPaymentLinesInner[]|null $host_lines host_lines
     *
     * @return $this
     */
    public function setHostLines(?array $host_lines): static
    {
        if (is_null($host_lines)) {
            throw new InvalidArgumentException('non-nullable host_lines cannot be null');
        }
        $this->container['host_lines'] = $host_lines;

        return $this;
    }

    /**
     * Gets total_guest_payment
     *
     * @return float|null
     */
    public function getTotalGuestPayment(): ?float
    {
        return $this->container['total_guest_payment'];
    }

    /**
     * Sets total_guest_payment
     *
     * @param float|null $total_guest_payment total_guest_payment
     *
     * @return $this
     */
    public function setTotalGuestPayment(?float $total_guest_payment): static
    {
        if (is_null($total_guest_payment)) {
            array_push($this->openAPINullablesSetToNull, 'total_guest_payment');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('total_guest_payment', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['total_guest_payment'] = $total_guest_payment;

        return $this;
    }

    /**
     * Gets payout
     *
     * @return float|null
     */
    public function getPayout(): ?float
    {
        return $this->container['payout'];
    }

    /**
     * Sets payout
     *
     * @param float|null $payout payout
     *
     * @return $this
     */
    public function setPayout(?float $payout): static
    {
        if (is_null($payout)) {
            array_push($this->openAPINullablesSetToNull, 'payout');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('payout', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['payout'] = $payout;

        return $this;
    }

    /**
     * Gets cancellation_policy
     *
     * @return string|null
     */
    public function getCancellationPolicy(): ?string
    {
        return $this->container['cancellation_policy'];
    }

    /**
     * Sets cancellation_policy
     *
     * @param string|null $cancellation_policy cancellation_policy
     *
     * @return $this
     */
    public function setCancellationPolicy(?string $cancellation_policy): static
    {
        if (is_null($cancellation_policy)) {
            array_push($this->openAPINullablesSetToNull, 'cancellation_policy');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('cancellation_policy', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['cancellation_policy'] = $cancellation_policy;

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
     * Gets available_actions
     *
     * @return \Repull\Model\GetVrboReservation200ResponseDataAvailableActionsInner[]|null
     */
    public function getAvailableActions(): ?array
    {
        return $this->container['available_actions'];
    }

    /**
     * Sets available_actions
     *
     * @param \Repull\Model\GetVrboReservation200ResponseDataAvailableActionsInner[]|null $available_actions available_actions
     *
     * @return $this
     */
    public function setAvailableActions(?array $available_actions): static
    {
        if (is_null($available_actions)) {
            throw new InvalidArgumentException('non-nullable available_actions cannot be null');
        }
        $this->container['available_actions'] = $available_actions;

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


