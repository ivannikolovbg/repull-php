<?php
/**
 * GetVrboListingContent200ResponseSectionsHouseRules
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
 * GetVrboListingContent200ResponseSectionsHouseRules Class Doc Comment
 *
 * @package  Repull
 * @author   OpenAPI Generator team
 * @link     https://openapi-generator.tech
 * @implements ArrayAccess<string, mixed>
 */
class GetVrboListingContent200ResponseSectionsHouseRules implements ModelInterface, ArrayAccess, JsonSerializable
{
    public const DISCRIMINATOR = null;

    /**
     * The original name of the model.
     *
     * @var string
     */
    protected static string $openAPIModelName = 'getVrboListingContent_200_response_sections_house_rules';

    /**
     * Array of property to type mappings. Used for (de)serialization
     *
     * @var array<string, string>
     */
    protected static array $openAPITypes = [
        'check_in_time' => 'string',
        'check_out_time' => 'string',
        'max_guests' => 'int',
        'max_adults' => 'int',
        'occupancy_note' => 'string',
        'minimum_age' => 'int',
        'minimum_age_note' => 'string',
        'children' => '\Repull\Model\GetVrboListingContent200ResponseSectionsHouseRulesChildren',
        'pets' => '\Repull\Model\GetVrboListingContent200ResponseSectionsHouseRulesPets',
        'smoking' => '\Repull\Model\GetVrboListingContent200ResponseSectionsHouseRulesSmoking',
        'events' => '\Repull\Model\GetVrboListingContent200ResponseSectionsHouseRulesEvents',
        'custom_rules' => 'string[]',
        'rental_agreement' => 'mixed',
        'time_options' => 'string[]'
    ];

    /**
     * Array of property to format mappings. Used for (de)serialization
     *
     * @var array<string, string|null>
     */
    protected static array $openAPIFormats = [
        'check_in_time' => null,
        'check_out_time' => null,
        'max_guests' => null,
        'max_adults' => null,
        'occupancy_note' => null,
        'minimum_age' => null,
        'minimum_age_note' => null,
        'children' => null,
        'pets' => null,
        'smoking' => null,
        'events' => null,
        'custom_rules' => null,
        'rental_agreement' => null,
        'time_options' => null
    ];

    /**
     * Array of nullable properties. Used for (de)serialization
     *
     * @var array<string, bool>
     */
    protected static array $openAPINullables = [
        'check_in_time' => true,
        'check_out_time' => true,
        'max_guests' => true,
        'max_adults' => true,
        'occupancy_note' => true,
        'minimum_age' => true,
        'minimum_age_note' => true,
        'children' => false,
        'pets' => false,
        'smoking' => false,
        'events' => false,
        'custom_rules' => false,
        'rental_agreement' => true,
        'time_options' => false
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
        'check_in_time' => 'checkInTime',
        'check_out_time' => 'checkOutTime',
        'max_guests' => 'maxGuests',
        'max_adults' => 'maxAdults',
        'occupancy_note' => 'occupancyNote',
        'minimum_age' => 'minimumAge',
        'minimum_age_note' => 'minimumAgeNote',
        'children' => 'children',
        'pets' => 'pets',
        'smoking' => 'smoking',
        'events' => 'events',
        'custom_rules' => 'customRules',
        'rental_agreement' => 'rentalAgreement',
        'time_options' => 'timeOptions'
    ];

    /**
     * Array of attributes to setter functions (for deserialization of responses)
     *
     * @var array<string, string>
     */
    protected static array $setters = [
        'check_in_time' => 'setCheckInTime',
        'check_out_time' => 'setCheckOutTime',
        'max_guests' => 'setMaxGuests',
        'max_adults' => 'setMaxAdults',
        'occupancy_note' => 'setOccupancyNote',
        'minimum_age' => 'setMinimumAge',
        'minimum_age_note' => 'setMinimumAgeNote',
        'children' => 'setChildren',
        'pets' => 'setPets',
        'smoking' => 'setSmoking',
        'events' => 'setEvents',
        'custom_rules' => 'setCustomRules',
        'rental_agreement' => 'setRentalAgreement',
        'time_options' => 'setTimeOptions'
    ];

    /**
     * Array of attributes to getter functions (for serialization of requests)
     *
     * @var array<string, string>
     */
    protected static array $getters = [
        'check_in_time' => 'getCheckInTime',
        'check_out_time' => 'getCheckOutTime',
        'max_guests' => 'getMaxGuests',
        'max_adults' => 'getMaxAdults',
        'occupancy_note' => 'getOccupancyNote',
        'minimum_age' => 'getMinimumAge',
        'minimum_age_note' => 'getMinimumAgeNote',
        'children' => 'getChildren',
        'pets' => 'getPets',
        'smoking' => 'getSmoking',
        'events' => 'getEvents',
        'custom_rules' => 'getCustomRules',
        'rental_agreement' => 'getRentalAgreement',
        'time_options' => 'getTimeOptions'
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
        $this->setIfExists('check_in_time', $data ?? [], null);
        $this->setIfExists('check_out_time', $data ?? [], null);
        $this->setIfExists('max_guests', $data ?? [], null);
        $this->setIfExists('max_adults', $data ?? [], null);
        $this->setIfExists('occupancy_note', $data ?? [], null);
        $this->setIfExists('minimum_age', $data ?? [], null);
        $this->setIfExists('minimum_age_note', $data ?? [], null);
        $this->setIfExists('children', $data ?? [], null);
        $this->setIfExists('pets', $data ?? [], null);
        $this->setIfExists('smoking', $data ?? [], null);
        $this->setIfExists('events', $data ?? [], null);
        $this->setIfExists('custom_rules', $data ?? [], null);
        $this->setIfExists('rental_agreement', $data ?? [], null);
        $this->setIfExists('time_options', $data ?? [], null);
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
     * Gets check_in_time
     *
     * @return string|null
     */
    public function getCheckInTime(): ?string
    {
        return $this->container['check_in_time'];
    }

    /**
     * Sets check_in_time
     *
     * @param string|null $check_in_time check_in_time
     *
     * @return $this
     */
    public function setCheckInTime(?string $check_in_time): static
    {
        if (is_null($check_in_time)) {
            array_push($this->openAPINullablesSetToNull, 'check_in_time');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('check_in_time', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['check_in_time'] = $check_in_time;

        return $this;
    }

    /**
     * Gets check_out_time
     *
     * @return string|null
     */
    public function getCheckOutTime(): ?string
    {
        return $this->container['check_out_time'];
    }

    /**
     * Sets check_out_time
     *
     * @param string|null $check_out_time check_out_time
     *
     * @return $this
     */
    public function setCheckOutTime(?string $check_out_time): static
    {
        if (is_null($check_out_time)) {
            array_push($this->openAPINullablesSetToNull, 'check_out_time');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('check_out_time', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['check_out_time'] = $check_out_time;

        return $this;
    }

    /**
     * Gets max_guests
     *
     * @return int|null
     */
    public function getMaxGuests(): ?int
    {
        return $this->container['max_guests'];
    }

    /**
     * Sets max_guests
     *
     * @param int|null $max_guests max_guests
     *
     * @return $this
     */
    public function setMaxGuests(?int $max_guests): static
    {
        if (is_null($max_guests)) {
            array_push($this->openAPINullablesSetToNull, 'max_guests');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('max_guests', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['max_guests'] = $max_guests;

        return $this;
    }

    /**
     * Gets max_adults
     *
     * @return int|null
     */
    public function getMaxAdults(): ?int
    {
        return $this->container['max_adults'];
    }

    /**
     * Sets max_adults
     *
     * @param int|null $max_adults max_adults
     *
     * @return $this
     */
    public function setMaxAdults(?int $max_adults): static
    {
        if (is_null($max_adults)) {
            array_push($this->openAPINullablesSetToNull, 'max_adults');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('max_adults', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['max_adults'] = $max_adults;

        return $this;
    }

    /**
     * Gets occupancy_note
     *
     * @return string|null
     */
    public function getOccupancyNote(): ?string
    {
        return $this->container['occupancy_note'];
    }

    /**
     * Sets occupancy_note
     *
     * @param string|null $occupancy_note occupancy_note
     *
     * @return $this
     */
    public function setOccupancyNote(?string $occupancy_note): static
    {
        if (is_null($occupancy_note)) {
            array_push($this->openAPINullablesSetToNull, 'occupancy_note');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('occupancy_note', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['occupancy_note'] = $occupancy_note;

        return $this;
    }

    /**
     * Gets minimum_age
     *
     * @return int|null
     */
    public function getMinimumAge(): ?int
    {
        return $this->container['minimum_age'];
    }

    /**
     * Sets minimum_age
     *
     * @param int|null $minimum_age minimum_age
     *
     * @return $this
     */
    public function setMinimumAge(?int $minimum_age): static
    {
        if (is_null($minimum_age)) {
            array_push($this->openAPINullablesSetToNull, 'minimum_age');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('minimum_age', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['minimum_age'] = $minimum_age;

        return $this;
    }

    /**
     * Gets minimum_age_note
     *
     * @return string|null
     */
    public function getMinimumAgeNote(): ?string
    {
        return $this->container['minimum_age_note'];
    }

    /**
     * Sets minimum_age_note
     *
     * @param string|null $minimum_age_note minimum_age_note
     *
     * @return $this
     */
    public function setMinimumAgeNote(?string $minimum_age_note): static
    {
        if (is_null($minimum_age_note)) {
            array_push($this->openAPINullablesSetToNull, 'minimum_age_note');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('minimum_age_note', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['minimum_age_note'] = $minimum_age_note;

        return $this;
    }

    /**
     * Gets children
     *
     * @return \Repull\Model\GetVrboListingContent200ResponseSectionsHouseRulesChildren|null
     */
    public function getChildren(): ?\Repull\Model\GetVrboListingContent200ResponseSectionsHouseRulesChildren
    {
        return $this->container['children'];
    }

    /**
     * Sets children
     *
     * @param \Repull\Model\GetVrboListingContent200ResponseSectionsHouseRulesChildren|null $children children
     *
     * @return $this
     */
    public function setChildren(?\Repull\Model\GetVrboListingContent200ResponseSectionsHouseRulesChildren $children): static
    {
        if (is_null($children)) {
            throw new InvalidArgumentException('non-nullable children cannot be null');
        }
        $this->container['children'] = $children;

        return $this;
    }

    /**
     * Gets pets
     *
     * @return \Repull\Model\GetVrboListingContent200ResponseSectionsHouseRulesPets|null
     */
    public function getPets(): ?\Repull\Model\GetVrboListingContent200ResponseSectionsHouseRulesPets
    {
        return $this->container['pets'];
    }

    /**
     * Sets pets
     *
     * @param \Repull\Model\GetVrboListingContent200ResponseSectionsHouseRulesPets|null $pets pets
     *
     * @return $this
     */
    public function setPets(?\Repull\Model\GetVrboListingContent200ResponseSectionsHouseRulesPets $pets): static
    {
        if (is_null($pets)) {
            throw new InvalidArgumentException('non-nullable pets cannot be null');
        }
        $this->container['pets'] = $pets;

        return $this;
    }

    /**
     * Gets smoking
     *
     * @return \Repull\Model\GetVrboListingContent200ResponseSectionsHouseRulesSmoking|null
     */
    public function getSmoking(): ?\Repull\Model\GetVrboListingContent200ResponseSectionsHouseRulesSmoking
    {
        return $this->container['smoking'];
    }

    /**
     * Sets smoking
     *
     * @param \Repull\Model\GetVrboListingContent200ResponseSectionsHouseRulesSmoking|null $smoking smoking
     *
     * @return $this
     */
    public function setSmoking(?\Repull\Model\GetVrboListingContent200ResponseSectionsHouseRulesSmoking $smoking): static
    {
        if (is_null($smoking)) {
            throw new InvalidArgumentException('non-nullable smoking cannot be null');
        }
        $this->container['smoking'] = $smoking;

        return $this;
    }

    /**
     * Gets events
     *
     * @return \Repull\Model\GetVrboListingContent200ResponseSectionsHouseRulesEvents|null
     */
    public function getEvents(): ?\Repull\Model\GetVrboListingContent200ResponseSectionsHouseRulesEvents
    {
        return $this->container['events'];
    }

    /**
     * Sets events
     *
     * @param \Repull\Model\GetVrboListingContent200ResponseSectionsHouseRulesEvents|null $events events
     *
     * @return $this
     */
    public function setEvents(?\Repull\Model\GetVrboListingContent200ResponseSectionsHouseRulesEvents $events): static
    {
        if (is_null($events)) {
            throw new InvalidArgumentException('non-nullable events cannot be null');
        }
        $this->container['events'] = $events;

        return $this;
    }

    /**
     * Gets custom_rules
     *
     * @return string[]|null
     */
    public function getCustomRules(): ?array
    {
        return $this->container['custom_rules'];
    }

    /**
     * Sets custom_rules
     *
     * @param string[]|null $custom_rules custom_rules
     *
     * @return $this
     */
    public function setCustomRules(?array $custom_rules): static
    {
        if (is_null($custom_rules)) {
            throw new InvalidArgumentException('non-nullable custom_rules cannot be null');
        }
        $this->container['custom_rules'] = $custom_rules;

        return $this;
    }

    /**
     * Gets rental_agreement
     *
     * @return mixed|null
     */
    public function getRentalAgreement(): mixed
    {
        return $this->container['rental_agreement'];
    }

    /**
     * Sets rental_agreement
     *
     * @param mixed|null $rental_agreement rental_agreement
     *
     * @return $this
     */
    public function setRentalAgreement(mixed $rental_agreement): static
    {
        if (is_null($rental_agreement)) {
            array_push($this->openAPINullablesSetToNull, 'rental_agreement');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('rental_agreement', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['rental_agreement'] = $rental_agreement;

        return $this;
    }

    /**
     * Gets time_options
     *
     * @return string[]|null
     */
    public function getTimeOptions(): ?array
    {
        return $this->container['time_options'];
    }

    /**
     * Sets time_options
     *
     * @param string[]|null $time_options time_options
     *
     * @return $this
     */
    public function setTimeOptions(?array $time_options): static
    {
        if (is_null($time_options)) {
            throw new InvalidArgumentException('non-nullable time_options cannot be null');
        }
        $this->container['time_options'] = $time_options;

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


