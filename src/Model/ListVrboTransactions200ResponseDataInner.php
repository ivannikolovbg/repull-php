<?php
/**
 * ListVrboTransactions200ResponseDataInner
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
 * ListVrboTransactions200ResponseDataInner Class Doc Comment
 *
 * @package  Repull
 * @author   OpenAPI Generator team
 * @link     https://openapi-generator.tech
 * @implements ArrayAccess<string, mixed>
 */
class ListVrboTransactions200ResponseDataInner implements ModelInterface, ArrayAccess, JsonSerializable
{
    public const DISCRIMINATOR = null;

    /**
     * The original name of the model.
     *
     * @var string
     */
    protected static string $openAPIModelName = 'listVrboTransactions_200_response_data_inner';

    /**
     * Array of property to type mappings. Used for (de)serialization
     *
     * @var array<string, string>
     */
    protected static array $openAPITypes = [
        'transaction_id' => 'string',
        'account_id' => 'string',
        'account_name' => 'string',
        'status' => 'string',
        'type' => 'string',
        'is_payout' => 'bool',
        'date' => '\DateTime',
        'currency' => 'string',
        'amount' => 'float',
        'payout' => '\Repull\Model\ListVrboTransactions200ResponseDataInnerPayout',
        'confirmation_code' => 'string',
        'reservation_id' => 'int',
        'listing_id' => 'int',
        'vrbo_listing_number' => 'string',
        'on_inactive_listing' => 'bool',
        'guest_name' => 'string',
        'upcoming_status' => 'string',
        'unavailable_fields' => 'string[]',
        'synced_at' => 'string'
    ];

    /**
     * Array of property to format mappings. Used for (de)serialization
     *
     * @var array<string, string|null>
     */
    protected static array $openAPIFormats = [
        'transaction_id' => null,
        'account_id' => null,
        'account_name' => null,
        'status' => null,
        'type' => null,
        'is_payout' => null,
        'date' => 'date',
        'currency' => null,
        'amount' => null,
        'payout' => null,
        'confirmation_code' => null,
        'reservation_id' => null,
        'listing_id' => null,
        'vrbo_listing_number' => null,
        'on_inactive_listing' => null,
        'guest_name' => null,
        'upcoming_status' => null,
        'unavailable_fields' => null,
        'synced_at' => null
    ];

    /**
     * Array of nullable properties. Used for (de)serialization
     *
     * @var array<string, bool>
     */
    protected static array $openAPINullables = [
        'transaction_id' => false,
        'account_id' => true,
        'account_name' => true,
        'status' => false,
        'type' => false,
        'is_payout' => false,
        'date' => true,
        'currency' => true,
        'amount' => true,
        'payout' => false,
        'confirmation_code' => true,
        'reservation_id' => true,
        'listing_id' => true,
        'vrbo_listing_number' => true,
        'on_inactive_listing' => false,
        'guest_name' => true,
        'upcoming_status' => true,
        'unavailable_fields' => false,
        'synced_at' => true
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
        'transaction_id' => 'transactionId',
        'account_id' => 'accountId',
        'account_name' => 'accountName',
        'status' => 'status',
        'type' => 'type',
        'is_payout' => 'isPayout',
        'date' => 'date',
        'currency' => 'currency',
        'amount' => 'amount',
        'payout' => 'payout',
        'confirmation_code' => 'confirmationCode',
        'reservation_id' => 'reservationId',
        'listing_id' => 'listingId',
        'vrbo_listing_number' => 'vrboListingNumber',
        'on_inactive_listing' => 'onInactiveListing',
        'guest_name' => 'guestName',
        'upcoming_status' => 'upcomingStatus',
        'unavailable_fields' => 'unavailableFields',
        'synced_at' => 'syncedAt'
    ];

    /**
     * Array of attributes to setter functions (for deserialization of responses)
     *
     * @var array<string, string>
     */
    protected static array $setters = [
        'transaction_id' => 'setTransactionId',
        'account_id' => 'setAccountId',
        'account_name' => 'setAccountName',
        'status' => 'setStatus',
        'type' => 'setType',
        'is_payout' => 'setIsPayout',
        'date' => 'setDate',
        'currency' => 'setCurrency',
        'amount' => 'setAmount',
        'payout' => 'setPayout',
        'confirmation_code' => 'setConfirmationCode',
        'reservation_id' => 'setReservationId',
        'listing_id' => 'setListingId',
        'vrbo_listing_number' => 'setVrboListingNumber',
        'on_inactive_listing' => 'setOnInactiveListing',
        'guest_name' => 'setGuestName',
        'upcoming_status' => 'setUpcomingStatus',
        'unavailable_fields' => 'setUnavailableFields',
        'synced_at' => 'setSyncedAt'
    ];

    /**
     * Array of attributes to getter functions (for serialization of requests)
     *
     * @var array<string, string>
     */
    protected static array $getters = [
        'transaction_id' => 'getTransactionId',
        'account_id' => 'getAccountId',
        'account_name' => 'getAccountName',
        'status' => 'getStatus',
        'type' => 'getType',
        'is_payout' => 'getIsPayout',
        'date' => 'getDate',
        'currency' => 'getCurrency',
        'amount' => 'getAmount',
        'payout' => 'getPayout',
        'confirmation_code' => 'getConfirmationCode',
        'reservation_id' => 'getReservationId',
        'listing_id' => 'getListingId',
        'vrbo_listing_number' => 'getVrboListingNumber',
        'on_inactive_listing' => 'getOnInactiveListing',
        'guest_name' => 'getGuestName',
        'upcoming_status' => 'getUpcomingStatus',
        'unavailable_fields' => 'getUnavailableFields',
        'synced_at' => 'getSyncedAt'
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

    public const STATUS_COMPLETED = 'COMPLETED';
    public const STATUS_UPCOMING = 'UPCOMING';

    /**
     * Gets allowable values of the enum
     *
     * @return string[]
     */
    public static function getStatusAllowableValues()
    {
        return [
            self::STATUS_COMPLETED,
            self::STATUS_UPCOMING,
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
        $this->setIfExists('transaction_id', $data ?? [], null);
        $this->setIfExists('account_id', $data ?? [], null);
        $this->setIfExists('account_name', $data ?? [], null);
        $this->setIfExists('status', $data ?? [], null);
        $this->setIfExists('type', $data ?? [], null);
        $this->setIfExists('is_payout', $data ?? [], null);
        $this->setIfExists('date', $data ?? [], null);
        $this->setIfExists('currency', $data ?? [], null);
        $this->setIfExists('amount', $data ?? [], null);
        $this->setIfExists('payout', $data ?? [], null);
        $this->setIfExists('confirmation_code', $data ?? [], null);
        $this->setIfExists('reservation_id', $data ?? [], null);
        $this->setIfExists('listing_id', $data ?? [], null);
        $this->setIfExists('vrbo_listing_number', $data ?? [], null);
        $this->setIfExists('on_inactive_listing', $data ?? [], null);
        $this->setIfExists('guest_name', $data ?? [], null);
        $this->setIfExists('upcoming_status', $data ?? [], null);
        $this->setIfExists('unavailable_fields', $data ?? [], null);
        $this->setIfExists('synced_at', $data ?? [], null);
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

        $allowedValues = self::getStatusAllowableValues();
        if (!is_null($this->container['status']) && !in_array($this->container['status'], $allowedValues, true)) {
            $invalidProperties[] = sprintf(
                "invalid value '%s' for 'status', must be one of '%s'",
                $this->container['status'],
                implode("', '", $allowedValues)
            );
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
     * Gets transaction_id
     *
     * @return string|null
     */
    public function getTransactionId(): ?string
    {
        return $this->container['transaction_id'];
    }

    /**
     * Sets transaction_id
     *
     * @param string|null $transaction_id Stable: the deposit's reference number on a Payout row; `<reference>:<confirmationCode>:<n>` on a line; `upcoming:<confirmationCode>:<n>` on a scheduled payout.
     *
     * @return $this
     */
    public function setTransactionId(?string $transaction_id): static
    {
        if (is_null($transaction_id)) {
            throw new InvalidArgumentException('non-nullable transaction_id cannot be null');
        }
        $this->container['transaction_id'] = $transaction_id;

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
     * @param string|null $account_id account_id
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
     * Gets account_name
     *
     * @return string|null
     */
    public function getAccountName(): ?string
    {
        return $this->container['account_name'];
    }

    /**
     * Sets account_name
     *
     * @param string|null $account_name account_name
     *
     * @return $this
     */
    public function setAccountName(?string $account_name): static
    {
        if (is_null($account_name)) {
            array_push($this->openAPINullablesSetToNull, 'account_name');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('account_name', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['account_name'] = $account_name;

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
        // (relax-enums.php) accept unknown enum values for forward compat
        $this->container['status'] = $status;

        return $this;
    }

    /**
     * Gets type
     *
     * @return string|null
     */
    public function getType(): ?string
    {
        return $this->container['type'];
    }

    /**
     * Sets type
     *
     * @param string|null $type `Payout` (the bank deposit), `Reservation` (a booking it paid), `Adjustment` (money taken back from it).
     *
     * @return $this
     */
    public function setType(?string $type): static
    {
        if (is_null($type)) {
            throw new InvalidArgumentException('non-nullable type cannot be null');
        }
        $this->container['type'] = $type;

        return $this;
    }

    /**
     * Gets is_payout
     *
     * @return bool|null
     */
    public function getIsPayout(): ?bool
    {
        return $this->container['is_payout'];
    }

    /**
     * Sets is_payout
     *
     * @param bool|null $is_payout is_payout
     *
     * @return $this
     */
    public function setIsPayout(?bool $is_payout): static
    {
        if (is_null($is_payout)) {
            throw new InvalidArgumentException('non-nullable is_payout cannot be null');
        }
        $this->container['is_payout'] = $is_payout;

        return $this;
    }

    /**
     * Gets date
     *
     * @return \DateTime|null
     */
    public function getDate(): ?\DateTime
    {
        return $this->container['date'];
    }

    /**
     * Sets date
     *
     * @param \DateTime|null $date date
     *
     * @return $this
     */
    public function setDate(?\DateTime $date): static
    {
        if (is_null($date)) {
            array_push($this->openAPINullablesSetToNull, 'date');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('date', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['date'] = $date;

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
     * Gets amount
     *
     * @return float|null
     */
    public function getAmount(): ?float
    {
        return $this->container['amount'];
    }

    /**
     * Sets amount
     *
     * @param float|null $amount Signed: what this line adds to its deposit. On a Payout row, the amount deposited.
     *
     * @return $this
     */
    public function setAmount(?float $amount): static
    {
        if (is_null($amount)) {
            array_push($this->openAPINullablesSetToNull, 'amount');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('amount', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['amount'] = $amount;

        return $this;
    }

    /**
     * Gets payout
     *
     * @return \Repull\Model\ListVrboTransactions200ResponseDataInnerPayout|null
     */
    public function getPayout(): ?\Repull\Model\ListVrboTransactions200ResponseDataInnerPayout
    {
        return $this->container['payout'];
    }

    /**
     * Sets payout
     *
     * @param \Repull\Model\ListVrboTransactions200ResponseDataInnerPayout|null $payout payout
     *
     * @return $this
     */
    public function setPayout(?\Repull\Model\ListVrboTransactions200ResponseDataInnerPayout $payout): static
    {
        if (is_null($payout)) {
            throw new InvalidArgumentException('non-nullable payout cannot be null');
        }
        $this->container['payout'] = $payout;

        return $this;
    }

    /**
     * Gets confirmation_code
     *
     * @return string|null
     */
    public function getConfirmationCode(): ?string
    {
        return $this->container['confirmation_code'];
    }

    /**
     * Sets confirmation_code
     *
     * @param string|null $confirmation_code confirmation_code
     *
     * @return $this
     */
    public function setConfirmationCode(?string $confirmation_code): static
    {
        if (is_null($confirmation_code)) {
            array_push($this->openAPINullablesSetToNull, 'confirmation_code');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('confirmation_code', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['confirmation_code'] = $confirmation_code;

        return $this;
    }

    /**
     * Gets reservation_id
     *
     * @return int|null
     */
    public function getReservationId(): ?int
    {
        return $this->container['reservation_id'];
    }

    /**
     * Sets reservation_id
     *
     * @param int|null $reservation_id The Repull reservation, when the booking is in the workspace.
     *
     * @return $this
     */
    public function setReservationId(?int $reservation_id): static
    {
        if (is_null($reservation_id)) {
            array_push($this->openAPINullablesSetToNull, 'reservation_id');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('reservation_id', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['reservation_id'] = $reservation_id;

        return $this;
    }

    /**
     * Gets listing_id
     *
     * @return int|null
     */
    public function getListingId(): ?int
    {
        return $this->container['listing_id'];
    }

    /**
     * Sets listing_id
     *
     * @param int|null $listing_id listing_id
     *
     * @return $this
     */
    public function setListingId(?int $listing_id): static
    {
        if (is_null($listing_id)) {
            array_push($this->openAPINullablesSetToNull, 'listing_id');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('listing_id', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['listing_id'] = $listing_id;

        return $this;
    }

    /**
     * Gets vrbo_listing_number
     *
     * @return string|null
     */
    public function getVrboListingNumber(): ?string
    {
        return $this->container['vrbo_listing_number'];
    }

    /**
     * Sets vrbo_listing_number
     *
     * @param string|null $vrbo_listing_number vrbo_listing_number
     *
     * @return $this
     */
    public function setVrboListingNumber(?string $vrbo_listing_number): static
    {
        if (is_null($vrbo_listing_number)) {
            array_push($this->openAPINullablesSetToNull, 'vrbo_listing_number');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('vrbo_listing_number', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['vrbo_listing_number'] = $vrbo_listing_number;

        return $this;
    }

    /**
     * Gets on_inactive_listing
     *
     * @return bool|null
     */
    public function getOnInactiveListing(): ?bool
    {
        return $this->container['on_inactive_listing'];
    }

    /**
     * Sets on_inactive_listing
     *
     * @param bool|null $on_inactive_listing on_inactive_listing
     *
     * @return $this
     */
    public function setOnInactiveListing(?bool $on_inactive_listing): static
    {
        if (is_null($on_inactive_listing)) {
            throw new InvalidArgumentException('non-nullable on_inactive_listing cannot be null');
        }
        $this->container['on_inactive_listing'] = $on_inactive_listing;

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
     * Gets upcoming_status
     *
     * @return string|null
     */
    public function getUpcomingStatus(): ?string
    {
        return $this->container['upcoming_status'];
    }

    /**
     * Sets upcoming_status
     *
     * @param string|null $upcoming_status On an UPCOMING line: Vrbo's status for the payout (`Scheduled`, `Blocked`, …).
     *
     * @return $this
     */
    public function setUpcomingStatus(?string $upcoming_status): static
    {
        if (is_null($upcoming_status)) {
            array_push($this->openAPINullablesSetToNull, 'upcoming_status');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('upcoming_status', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['upcoming_status'] = $upcoming_status;

        return $this;
    }

    /**
     * Gets unavailable_fields
     *
     * @return string[]|null
     */
    public function getUnavailableFields(): ?array
    {
        return $this->container['unavailable_fields'];
    }

    /**
     * Sets unavailable_fields
     *
     * @param string[]|null $unavailable_fields unavailable_fields
     *
     * @return $this
     */
    public function setUnavailableFields(?array $unavailable_fields): static
    {
        if (is_null($unavailable_fields)) {
            throw new InvalidArgumentException('non-nullable unavailable_fields cannot be null');
        }
        $this->container['unavailable_fields'] = $unavailable_fields;

        return $this;
    }

    /**
     * Gets synced_at
     *
     * @return string|null
     */
    public function getSyncedAt(): ?string
    {
        return $this->container['synced_at'];
    }

    /**
     * Sets synced_at
     *
     * @param string|null $synced_at synced_at
     *
     * @return $this
     */
    public function setSyncedAt(?string $synced_at): static
    {
        if (is_null($synced_at)) {
            array_push($this->openAPINullablesSetToNull, 'synced_at');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('synced_at', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['synced_at'] = $synced_at;

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


