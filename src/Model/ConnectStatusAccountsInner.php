<?php
/**
 * ConnectStatusAccountsInner
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
 * ConnectStatusAccountsInner Class Doc Comment
 *
 * @package  Repull
 * @author   OpenAPI Generator team
 * @link     https://openapi-generator.tech
 * @implements ArrayAccess<string, mixed>
 */
class ConnectStatusAccountsInner implements ModelInterface, ArrayAccess, JsonSerializable
{
    public const DISCRIMINATOR = null;

    /**
     * The original name of the model.
     *
     * @var string
     */
    protected static string $openAPIModelName = 'ConnectStatus_accounts_inner';

    /**
     * Array of property to type mappings. Used for (de)serialization
     *
     * @var array<string, string>
     */
    protected static array $openAPITypes = [
        'external_account_id' => 'string',
        'connection_id' => 'string',
        'name' => 'string',
        'picture_url' => 'string',
        'status' => 'string',
        'connected' => 'bool',
        'email' => 'string',
        'access_type' => '\Repull\Model\ConnectionAccessType',
        'action' => 'object',
        'fix_url' => 'string',
        'notification_email' => 'string',
        'import' => '\Repull\Model\VrboImportStatus'
    ];

    /**
     * Array of property to format mappings. Used for (de)serialization
     *
     * @var array<string, string|null>
     */
    protected static array $openAPIFormats = [
        'external_account_id' => null,
        'connection_id' => null,
        'name' => null,
        'picture_url' => null,
        'status' => null,
        'connected' => null,
        'email' => null,
        'access_type' => null,
        'action' => null,
        'fix_url' => null,
        'notification_email' => null,
        'import' => null
    ];

    /**
     * Array of nullable properties. Used for (de)serialization
     *
     * @var array<string, bool>
     */
    protected static array $openAPINullables = [
        'external_account_id' => true,
        'connection_id' => true,
        'name' => true,
        'picture_url' => true,
        'status' => true,
        'connected' => false,
        'email' => true,
        'access_type' => true,
        'action' => true,
        'fix_url' => true,
        'notification_email' => true,
        'import' => true
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
        'external_account_id' => 'externalAccountId',
        'connection_id' => 'connectionId',
        'name' => 'name',
        'picture_url' => 'pictureUrl',
        'status' => 'status',
        'connected' => 'connected',
        'email' => 'email',
        'access_type' => 'accessType',
        'action' => 'action',
        'fix_url' => 'fixUrl',
        'notification_email' => 'notificationEmail',
        'import' => 'import'
    ];

    /**
     * Array of attributes to setter functions (for deserialization of responses)
     *
     * @var array<string, string>
     */
    protected static array $setters = [
        'external_account_id' => 'setExternalAccountId',
        'connection_id' => 'setConnectionId',
        'name' => 'setName',
        'picture_url' => 'setPictureUrl',
        'status' => 'setStatus',
        'connected' => 'setConnected',
        'email' => 'setEmail',
        'access_type' => 'setAccessType',
        'action' => 'setAction',
        'fix_url' => 'setFixUrl',
        'notification_email' => 'setNotificationEmail',
        'import' => 'setImport'
    ];

    /**
     * Array of attributes to getter functions (for serialization of requests)
     *
     * @var array<string, string>
     */
    protected static array $getters = [
        'external_account_id' => 'getExternalAccountId',
        'connection_id' => 'getConnectionId',
        'name' => 'getName',
        'picture_url' => 'getPictureUrl',
        'status' => 'getStatus',
        'connected' => 'getConnected',
        'email' => 'getEmail',
        'access_type' => 'getAccessType',
        'action' => 'getAction',
        'fix_url' => 'getFixUrl',
        'notification_email' => 'getNotificationEmail',
        'import' => 'getImport'
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
        $this->setIfExists('external_account_id', $data ?? [], null);
        $this->setIfExists('connection_id', $data ?? [], null);
        $this->setIfExists('name', $data ?? [], null);
        $this->setIfExists('picture_url', $data ?? [], null);
        $this->setIfExists('status', $data ?? [], null);
        $this->setIfExists('connected', $data ?? [], null);
        $this->setIfExists('email', $data ?? [], null);
        $this->setIfExists('access_type', $data ?? [], null);
        $this->setIfExists('action', $data ?? [], null);
        $this->setIfExists('fix_url', $data ?? [], null);
        $this->setIfExists('notification_email', $data ?? [], null);
        $this->setIfExists('import', $data ?? [], null);
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
     * Gets external_account_id
     *
     * @return string|null
     */
    public function getExternalAccountId(): ?string
    {
        return $this->container['external_account_id'];
    }

    /**
     * Sets external_account_id
     *
     * @param string|null $external_account_id Airbnb: the host id, as a string (it can exceed 2^53). Booking.com: the hotel id. Vrbo: Vrbo's own account id; `null` until it is captured at sign-in. Extranet: the Booking.com account id. PMS: the PMS account id.
     *
     * @return $this
     */
    public function setExternalAccountId(?string $external_account_id): static
    {
        if (is_null($external_account_id)) {
            array_push($this->openAPINullablesSetToNull, 'external_account_id');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('external_account_id', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['external_account_id'] = $external_account_id;

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
     * @param string|null $connection_id Our connection record (`X-Account-Id`). Airbnb: the workspace's Airbnb connection. Vrbo: the workspace's Vrbo connection, `null` when it has none.
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
     * Gets name
     *
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->container['name'];
    }

    /**
     * Sets name
     *
     * @param string|null $name name
     *
     * @return $this
     */
    public function setName(?string $name): static
    {
        if (is_null($name)) {
            array_push($this->openAPINullablesSetToNull, 'name');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('name', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['name'] = $name;

        return $this;
    }

    /**
     * Gets picture_url
     *
     * @return string|null
     */
    public function getPictureUrl(): ?string
    {
        return $this->container['picture_url'];
    }

    /**
     * Sets picture_url
     *
     * @param string|null $picture_url picture_url
     *
     * @return $this
     */
    public function setPictureUrl(?string $picture_url): static
    {
        if (is_null($picture_url)) {
            array_push($this->openAPINullablesSetToNull, 'picture_url');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('picture_url', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['picture_url'] = $picture_url;

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
            array_push($this->openAPINullablesSetToNull, 'status');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('status', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['status'] = $status;

        return $this;
    }

    /**
     * Gets connected
     *
     * @return bool|null
     */
    public function getConnected(): ?bool
    {
        return $this->container['connected'];
    }

    /**
     * Sets connected
     *
     * @param bool|null $connected True while the account is active and its authorization is usable.
     *
     * @return $this
     */
    public function setConnected(?bool $connected): static
    {
        if (is_null($connected)) {
            throw new InvalidArgumentException('non-nullable connected cannot be null');
        }
        $this->container['connected'] = $connected;

        return $this;
    }

    /**
     * Gets email
     *
     * @return string|null
     */
    public function getEmail(): ?string
    {
        return $this->container['email'];
    }

    /**
     * Sets email
     *
     * @param string|null $email Vrbo only: the account email.
     *
     * @return $this
     */
    public function setEmail(?string $email): static
    {
        if (is_null($email)) {
            array_push($this->openAPINullablesSetToNull, 'email');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('email', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['email'] = $email;

        return $this;
    }

    /**
     * Gets access_type
     *
     * @return \Repull\Model\ConnectionAccessType|null
     */
    public function getAccessType(): ?\Repull\Model\ConnectionAccessType
    {
        return $this->container['access_type'];
    }

    /**
     * Sets access_type
     *
     * @param \Repull\Model\ConnectionAccessType|null $access_type What this account may do. Airbnb: the tier the host's token was granted with (`read_only`, `messaging`, `full_access`). Vrbo: `messaging` (bookings and messages, no calendar push) or `full_access` (also pushes prices and availability). `null` when unknown.
     *
     * @return $this
     */
    public function setAccessType(?\Repull\Model\ConnectionAccessType $access_type): static
    {
        if (is_null($access_type)) {
            array_push($this->openAPINullablesSetToNull, 'access_type');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('access_type', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['access_type'] = $access_type;

        return $this;
    }

    /**
     * Gets action
     *
     * @return object|null
     */
    public function getAction(): ?object
    {
        return $this->container['action'];
    }

    /**
     * Sets action
     *
     * @param object|null $action Booking.com: `{ required, reason: \"needs_mapping\", message }` while a room of this hotel is matched to no listing (nothing syncs for that room). Null when nothing is needed.
     *
     * @return $this
     */
    public function setAction(?object $action): static
    {
        if (is_null($action)) {
            array_push($this->openAPINullablesSetToNull, 'action');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('action', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['action'] = $action;

        return $this;
    }

    /**
     * Gets fix_url
     *
     * @return string|null
     */
    public function getFixUrl(): ?string
    {
        return $this->container['fix_url'];
    }

    /**
     * Sets fix_url
     *
     * @param string|null $fix_url Booking.com: a link that opens room mapping for this hotel (no new Connect session needed). Send the host to it when `action` is set.
     *
     * @return $this
     */
    public function setFixUrl(?string $fix_url): static
    {
        if (is_null($fix_url)) {
            array_push($this->openAPINullablesSetToNull, 'fix_url');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('fix_url', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['fix_url'] = $fix_url;

        return $this;
    }

    /**
     * Gets notification_email
     *
     * @return string|null
     */
    public function getNotificationEmail(): ?string
    {
        return $this->container['notification_email'];
    }

    /**
     * Sets notification_email
     *
     * @param string|null $notification_email Vrbo only. The address the host can add on Vrbo as a secondary email (with message and reservation notifications on) so new bookings, inquiries and messages arrive within a minute instead of at the scheduled check. Ask every host to add it: notifications are how Vrbo updates reach Repull, the scheduled check is the backup. Hosted Connect shows it on its mapping screen; show it in your own app when you create sessions with `mapping: \"api\"`.
     *
     * @return $this
     */
    public function setNotificationEmail(?string $notification_email): static
    {
        if (is_null($notification_email)) {
            array_push($this->openAPINullablesSetToNull, 'notification_email');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('notification_email', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['notification_email'] = $notification_email;

        return $this;
    }

    /**
     * Gets import
     *
     * @return \Repull\Model\VrboImportStatus|null
     */
    public function getImport(): ?\Repull\Model\VrboImportStatus
    {
        return $this->container['import'];
    }

    /**
     * Sets import
     *
     * @param \Repull\Model\VrboImportStatus|null $import Vrbo only: where the account import stands.
     *
     * @return $this
     */
    public function setImport(?\Repull\Model\VrboImportStatus $import): static
    {
        if (is_null($import)) {
            array_push($this->openAPINullablesSetToNull, 'import');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('import', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['import'] = $import;

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


