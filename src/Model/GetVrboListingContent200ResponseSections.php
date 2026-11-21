<?php
/**
 * GetVrboListingContent200ResponseSections
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
 * GetVrboListingContent200ResponseSections Class Doc Comment
 *
 * @description Keyed by section; each is the data of &#x60;GET /v1/channels/vrbo/listings/{id}/{section}&#x60;, or &#x60;{ error: { code, message } }&#x60; when that section failed.
 * @package  Repull
 * @author   OpenAPI Generator team
 * @link     https://openapi-generator.tech
 * @implements ArrayAccess<string, mixed>
 */
class GetVrboListingContent200ResponseSections implements ModelInterface, ArrayAccess, JsonSerializable
{
    public const DISCRIMINATOR = null;

    /**
     * The original name of the model.
     *
     * @var string
     */
    protected static string $openAPIModelName = 'getVrboListingContent_200_response_sections';

    /**
     * Array of property to type mappings. Used for (de)serialization
     *
     * @var array<string, string>
     */
    protected static array $openAPITypes = [
        'description' => '\Repull\Model\GetVrboListingContent200ResponseSectionsDescription',
        'property' => '\Repull\Model\GetVrboListingContent200ResponseSectionsProperty',
        'story' => '\Repull\Model\GetVrboListingContent200ResponseSectionsStory',
        'additional_info' => '\Repull\Model\GetVrboListingContent200ResponseSectionsAdditionalInfo',
        'translations' => '\Repull\Model\GetVrboListingContent200ResponseSectionsTranslations',
        'photos' => '\Repull\Model\GetVrboListingContent200ResponseSectionsPhotos',
        'amenities' => '\Repull\Model\GetVrboListingContent200ResponseSectionsAmenities',
        'rooms' => '\Repull\Model\GetVrboListingContent200ResponseSectionsRooms',
        'location' => '\Repull\Model\GetVrboListingContent200ResponseSectionsLocation',
        'house_rules' => '\Repull\Model\GetVrboListingContent200ResponseSectionsHouseRules',
        'booking_settings' => '\Repull\Model\GetVrboListingContent200ResponseSectionsBookingSettings',
        'cancellation' => '\Repull\Model\GetVrboListingContent200ResponseSectionsCancellation',
        'fees' => '\Repull\Model\GetVrboListingContent200ResponseSectionsFees',
        'pricing' => '\Repull\Model\GetVrboListingContent200ResponseSectionsPricing',
        'rates' => '\Repull\Model\GetVrboListingContent200ResponseSectionsRates',
        'payment_terms' => '\Repull\Model\GetVrboListingContent200ResponseSectionsPaymentTerms',
        'protection' => '\Repull\Model\GetVrboListingContent200ResponseSectionsProtection',
        'taxes' => '\Repull\Model\GetVrboListingContent200ResponseSectionsTaxes',
        'registration' => '\Repull\Model\GetVrboListingContent200ResponseSectionsRegistration',
        'welcome_guide' => '\Repull\Model\GetVrboListingContent200ResponseSectionsWelcomeGuide',
        'videos' => '\Repull\Model\GetVrboListingContent200ResponseSectionsVideos'
    ];

    /**
     * Array of property to format mappings. Used for (de)serialization
     *
     * @var array<string, string|null>
     */
    protected static array $openAPIFormats = [
        'description' => null,
        'property' => null,
        'story' => null,
        'additional_info' => null,
        'translations' => null,
        'photos' => null,
        'amenities' => null,
        'rooms' => null,
        'location' => null,
        'house_rules' => null,
        'booking_settings' => null,
        'cancellation' => null,
        'fees' => null,
        'pricing' => null,
        'rates' => null,
        'payment_terms' => null,
        'protection' => null,
        'taxes' => null,
        'registration' => null,
        'welcome_guide' => null,
        'videos' => null
    ];

    /**
     * Array of nullable properties. Used for (de)serialization
     *
     * @var array<string, bool>
     */
    protected static array $openAPINullables = [
        'description' => false,
        'property' => false,
        'story' => false,
        'additional_info' => false,
        'translations' => false,
        'photos' => false,
        'amenities' => false,
        'rooms' => false,
        'location' => false,
        'house_rules' => false,
        'booking_settings' => false,
        'cancellation' => false,
        'fees' => false,
        'pricing' => false,
        'rates' => false,
        'payment_terms' => false,
        'protection' => false,
        'taxes' => false,
        'registration' => false,
        'welcome_guide' => false,
        'videos' => false
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
        'description' => 'description',
        'property' => 'property',
        'story' => 'story',
        'additional_info' => 'additional-info',
        'translations' => 'translations',
        'photos' => 'photos',
        'amenities' => 'amenities',
        'rooms' => 'rooms',
        'location' => 'location',
        'house_rules' => 'house-rules',
        'booking_settings' => 'booking-settings',
        'cancellation' => 'cancellation',
        'fees' => 'fees',
        'pricing' => 'pricing',
        'rates' => 'rates',
        'payment_terms' => 'payment-terms',
        'protection' => 'protection',
        'taxes' => 'taxes',
        'registration' => 'registration',
        'welcome_guide' => 'welcome-guide',
        'videos' => 'videos'
    ];

    /**
     * Array of attributes to setter functions (for deserialization of responses)
     *
     * @var array<string, string>
     */
    protected static array $setters = [
        'description' => 'setDescription',
        'property' => 'setProperty',
        'story' => 'setStory',
        'additional_info' => 'setAdditionalInfo',
        'translations' => 'setTranslations',
        'photos' => 'setPhotos',
        'amenities' => 'setAmenities',
        'rooms' => 'setRooms',
        'location' => 'setLocation',
        'house_rules' => 'setHouseRules',
        'booking_settings' => 'setBookingSettings',
        'cancellation' => 'setCancellation',
        'fees' => 'setFees',
        'pricing' => 'setPricing',
        'rates' => 'setRates',
        'payment_terms' => 'setPaymentTerms',
        'protection' => 'setProtection',
        'taxes' => 'setTaxes',
        'registration' => 'setRegistration',
        'welcome_guide' => 'setWelcomeGuide',
        'videos' => 'setVideos'
    ];

    /**
     * Array of attributes to getter functions (for serialization of requests)
     *
     * @var array<string, string>
     */
    protected static array $getters = [
        'description' => 'getDescription',
        'property' => 'getProperty',
        'story' => 'getStory',
        'additional_info' => 'getAdditionalInfo',
        'translations' => 'getTranslations',
        'photos' => 'getPhotos',
        'amenities' => 'getAmenities',
        'rooms' => 'getRooms',
        'location' => 'getLocation',
        'house_rules' => 'getHouseRules',
        'booking_settings' => 'getBookingSettings',
        'cancellation' => 'getCancellation',
        'fees' => 'getFees',
        'pricing' => 'getPricing',
        'rates' => 'getRates',
        'payment_terms' => 'getPaymentTerms',
        'protection' => 'getProtection',
        'taxes' => 'getTaxes',
        'registration' => 'getRegistration',
        'welcome_guide' => 'getWelcomeGuide',
        'videos' => 'getVideos'
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
        $this->setIfExists('description', $data ?? [], null);
        $this->setIfExists('property', $data ?? [], null);
        $this->setIfExists('story', $data ?? [], null);
        $this->setIfExists('additional_info', $data ?? [], null);
        $this->setIfExists('translations', $data ?? [], null);
        $this->setIfExists('photos', $data ?? [], null);
        $this->setIfExists('amenities', $data ?? [], null);
        $this->setIfExists('rooms', $data ?? [], null);
        $this->setIfExists('location', $data ?? [], null);
        $this->setIfExists('house_rules', $data ?? [], null);
        $this->setIfExists('booking_settings', $data ?? [], null);
        $this->setIfExists('cancellation', $data ?? [], null);
        $this->setIfExists('fees', $data ?? [], null);
        $this->setIfExists('pricing', $data ?? [], null);
        $this->setIfExists('rates', $data ?? [], null);
        $this->setIfExists('payment_terms', $data ?? [], null);
        $this->setIfExists('protection', $data ?? [], null);
        $this->setIfExists('taxes', $data ?? [], null);
        $this->setIfExists('registration', $data ?? [], null);
        $this->setIfExists('welcome_guide', $data ?? [], null);
        $this->setIfExists('videos', $data ?? [], null);
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
     * Gets description
     *
     * @return \Repull\Model\GetVrboListingContent200ResponseSectionsDescription|null
     */
    public function getDescription(): ?\Repull\Model\GetVrboListingContent200ResponseSectionsDescription
    {
        return $this->container['description'];
    }

    /**
     * Sets description
     *
     * @param \Repull\Model\GetVrboListingContent200ResponseSectionsDescription|null $description description
     *
     * @return $this
     */
    public function setDescription(?\Repull\Model\GetVrboListingContent200ResponseSectionsDescription $description): static
    {
        if (is_null($description)) {
            throw new InvalidArgumentException('non-nullable description cannot be null');
        }
        $this->container['description'] = $description;

        return $this;
    }

    /**
     * Gets property
     *
     * @return \Repull\Model\GetVrboListingContent200ResponseSectionsProperty|null
     */
    public function getProperty(): ?\Repull\Model\GetVrboListingContent200ResponseSectionsProperty
    {
        return $this->container['property'];
    }

    /**
     * Sets property
     *
     * @param \Repull\Model\GetVrboListingContent200ResponseSectionsProperty|null $property property
     *
     * @return $this
     */
    public function setProperty(?\Repull\Model\GetVrboListingContent200ResponseSectionsProperty $property): static
    {
        if (is_null($property)) {
            throw new InvalidArgumentException('non-nullable property cannot be null');
        }
        $this->container['property'] = $property;

        return $this;
    }

    /**
     * Gets story
     *
     * @return \Repull\Model\GetVrboListingContent200ResponseSectionsStory|null
     */
    public function getStory(): ?\Repull\Model\GetVrboListingContent200ResponseSectionsStory
    {
        return $this->container['story'];
    }

    /**
     * Sets story
     *
     * @param \Repull\Model\GetVrboListingContent200ResponseSectionsStory|null $story story
     *
     * @return $this
     */
    public function setStory(?\Repull\Model\GetVrboListingContent200ResponseSectionsStory $story): static
    {
        if (is_null($story)) {
            throw new InvalidArgumentException('non-nullable story cannot be null');
        }
        $this->container['story'] = $story;

        return $this;
    }

    /**
     * Gets additional_info
     *
     * @return \Repull\Model\GetVrboListingContent200ResponseSectionsAdditionalInfo|null
     */
    public function getAdditionalInfo(): ?\Repull\Model\GetVrboListingContent200ResponseSectionsAdditionalInfo
    {
        return $this->container['additional_info'];
    }

    /**
     * Sets additional_info
     *
     * @param \Repull\Model\GetVrboListingContent200ResponseSectionsAdditionalInfo|null $additional_info additional_info
     *
     * @return $this
     */
    public function setAdditionalInfo(?\Repull\Model\GetVrboListingContent200ResponseSectionsAdditionalInfo $additional_info): static
    {
        if (is_null($additional_info)) {
            throw new InvalidArgumentException('non-nullable additional_info cannot be null');
        }
        $this->container['additional_info'] = $additional_info;

        return $this;
    }

    /**
     * Gets translations
     *
     * @return \Repull\Model\GetVrboListingContent200ResponseSectionsTranslations|null
     */
    public function getTranslations(): ?\Repull\Model\GetVrboListingContent200ResponseSectionsTranslations
    {
        return $this->container['translations'];
    }

    /**
     * Sets translations
     *
     * @param \Repull\Model\GetVrboListingContent200ResponseSectionsTranslations|null $translations translations
     *
     * @return $this
     */
    public function setTranslations(?\Repull\Model\GetVrboListingContent200ResponseSectionsTranslations $translations): static
    {
        if (is_null($translations)) {
            throw new InvalidArgumentException('non-nullable translations cannot be null');
        }
        $this->container['translations'] = $translations;

        return $this;
    }

    /**
     * Gets photos
     *
     * @return \Repull\Model\GetVrboListingContent200ResponseSectionsPhotos|null
     */
    public function getPhotos(): ?\Repull\Model\GetVrboListingContent200ResponseSectionsPhotos
    {
        return $this->container['photos'];
    }

    /**
     * Sets photos
     *
     * @param \Repull\Model\GetVrboListingContent200ResponseSectionsPhotos|null $photos photos
     *
     * @return $this
     */
    public function setPhotos(?\Repull\Model\GetVrboListingContent200ResponseSectionsPhotos $photos): static
    {
        if (is_null($photos)) {
            throw new InvalidArgumentException('non-nullable photos cannot be null');
        }
        $this->container['photos'] = $photos;

        return $this;
    }

    /**
     * Gets amenities
     *
     * @return \Repull\Model\GetVrboListingContent200ResponseSectionsAmenities|null
     */
    public function getAmenities(): ?\Repull\Model\GetVrboListingContent200ResponseSectionsAmenities
    {
        return $this->container['amenities'];
    }

    /**
     * Sets amenities
     *
     * @param \Repull\Model\GetVrboListingContent200ResponseSectionsAmenities|null $amenities amenities
     *
     * @return $this
     */
    public function setAmenities(?\Repull\Model\GetVrboListingContent200ResponseSectionsAmenities $amenities): static
    {
        if (is_null($amenities)) {
            throw new InvalidArgumentException('non-nullable amenities cannot be null');
        }
        $this->container['amenities'] = $amenities;

        return $this;
    }

    /**
     * Gets rooms
     *
     * @return \Repull\Model\GetVrboListingContent200ResponseSectionsRooms|null
     */
    public function getRooms(): ?\Repull\Model\GetVrboListingContent200ResponseSectionsRooms
    {
        return $this->container['rooms'];
    }

    /**
     * Sets rooms
     *
     * @param \Repull\Model\GetVrboListingContent200ResponseSectionsRooms|null $rooms rooms
     *
     * @return $this
     */
    public function setRooms(?\Repull\Model\GetVrboListingContent200ResponseSectionsRooms $rooms): static
    {
        if (is_null($rooms)) {
            throw new InvalidArgumentException('non-nullable rooms cannot be null');
        }
        $this->container['rooms'] = $rooms;

        return $this;
    }

    /**
     * Gets location
     *
     * @return \Repull\Model\GetVrboListingContent200ResponseSectionsLocation|null
     */
    public function getLocation(): ?\Repull\Model\GetVrboListingContent200ResponseSectionsLocation
    {
        return $this->container['location'];
    }

    /**
     * Sets location
     *
     * @param \Repull\Model\GetVrboListingContent200ResponseSectionsLocation|null $location location
     *
     * @return $this
     */
    public function setLocation(?\Repull\Model\GetVrboListingContent200ResponseSectionsLocation $location): static
    {
        if (is_null($location)) {
            throw new InvalidArgumentException('non-nullable location cannot be null');
        }
        $this->container['location'] = $location;

        return $this;
    }

    /**
     * Gets house_rules
     *
     * @return \Repull\Model\GetVrboListingContent200ResponseSectionsHouseRules|null
     */
    public function getHouseRules(): ?\Repull\Model\GetVrboListingContent200ResponseSectionsHouseRules
    {
        return $this->container['house_rules'];
    }

    /**
     * Sets house_rules
     *
     * @param \Repull\Model\GetVrboListingContent200ResponseSectionsHouseRules|null $house_rules house_rules
     *
     * @return $this
     */
    public function setHouseRules(?\Repull\Model\GetVrboListingContent200ResponseSectionsHouseRules $house_rules): static
    {
        if (is_null($house_rules)) {
            throw new InvalidArgumentException('non-nullable house_rules cannot be null');
        }
        $this->container['house_rules'] = $house_rules;

        return $this;
    }

    /**
     * Gets booking_settings
     *
     * @return \Repull\Model\GetVrboListingContent200ResponseSectionsBookingSettings|null
     */
    public function getBookingSettings(): ?\Repull\Model\GetVrboListingContent200ResponseSectionsBookingSettings
    {
        return $this->container['booking_settings'];
    }

    /**
     * Sets booking_settings
     *
     * @param \Repull\Model\GetVrboListingContent200ResponseSectionsBookingSettings|null $booking_settings booking_settings
     *
     * @return $this
     */
    public function setBookingSettings(?\Repull\Model\GetVrboListingContent200ResponseSectionsBookingSettings $booking_settings): static
    {
        if (is_null($booking_settings)) {
            throw new InvalidArgumentException('non-nullable booking_settings cannot be null');
        }
        $this->container['booking_settings'] = $booking_settings;

        return $this;
    }

    /**
     * Gets cancellation
     *
     * @return \Repull\Model\GetVrboListingContent200ResponseSectionsCancellation|null
     */
    public function getCancellation(): ?\Repull\Model\GetVrboListingContent200ResponseSectionsCancellation
    {
        return $this->container['cancellation'];
    }

    /**
     * Sets cancellation
     *
     * @param \Repull\Model\GetVrboListingContent200ResponseSectionsCancellation|null $cancellation cancellation
     *
     * @return $this
     */
    public function setCancellation(?\Repull\Model\GetVrboListingContent200ResponseSectionsCancellation $cancellation): static
    {
        if (is_null($cancellation)) {
            throw new InvalidArgumentException('non-nullable cancellation cannot be null');
        }
        $this->container['cancellation'] = $cancellation;

        return $this;
    }

    /**
     * Gets fees
     *
     * @return \Repull\Model\GetVrboListingContent200ResponseSectionsFees|null
     */
    public function getFees(): ?\Repull\Model\GetVrboListingContent200ResponseSectionsFees
    {
        return $this->container['fees'];
    }

    /**
     * Sets fees
     *
     * @param \Repull\Model\GetVrboListingContent200ResponseSectionsFees|null $fees fees
     *
     * @return $this
     */
    public function setFees(?\Repull\Model\GetVrboListingContent200ResponseSectionsFees $fees): static
    {
        if (is_null($fees)) {
            throw new InvalidArgumentException('non-nullable fees cannot be null');
        }
        $this->container['fees'] = $fees;

        return $this;
    }

    /**
     * Gets pricing
     *
     * @return \Repull\Model\GetVrboListingContent200ResponseSectionsPricing|null
     */
    public function getPricing(): ?\Repull\Model\GetVrboListingContent200ResponseSectionsPricing
    {
        return $this->container['pricing'];
    }

    /**
     * Sets pricing
     *
     * @param \Repull\Model\GetVrboListingContent200ResponseSectionsPricing|null $pricing pricing
     *
     * @return $this
     */
    public function setPricing(?\Repull\Model\GetVrboListingContent200ResponseSectionsPricing $pricing): static
    {
        if (is_null($pricing)) {
            throw new InvalidArgumentException('non-nullable pricing cannot be null');
        }
        $this->container['pricing'] = $pricing;

        return $this;
    }

    /**
     * Gets rates
     *
     * @return \Repull\Model\GetVrboListingContent200ResponseSectionsRates|null
     */
    public function getRates(): ?\Repull\Model\GetVrboListingContent200ResponseSectionsRates
    {
        return $this->container['rates'];
    }

    /**
     * Sets rates
     *
     * @param \Repull\Model\GetVrboListingContent200ResponseSectionsRates|null $rates rates
     *
     * @return $this
     */
    public function setRates(?\Repull\Model\GetVrboListingContent200ResponseSectionsRates $rates): static
    {
        if (is_null($rates)) {
            throw new InvalidArgumentException('non-nullable rates cannot be null');
        }
        $this->container['rates'] = $rates;

        return $this;
    }

    /**
     * Gets payment_terms
     *
     * @return \Repull\Model\GetVrboListingContent200ResponseSectionsPaymentTerms|null
     */
    public function getPaymentTerms(): ?\Repull\Model\GetVrboListingContent200ResponseSectionsPaymentTerms
    {
        return $this->container['payment_terms'];
    }

    /**
     * Sets payment_terms
     *
     * @param \Repull\Model\GetVrboListingContent200ResponseSectionsPaymentTerms|null $payment_terms payment_terms
     *
     * @return $this
     */
    public function setPaymentTerms(?\Repull\Model\GetVrboListingContent200ResponseSectionsPaymentTerms $payment_terms): static
    {
        if (is_null($payment_terms)) {
            throw new InvalidArgumentException('non-nullable payment_terms cannot be null');
        }
        $this->container['payment_terms'] = $payment_terms;

        return $this;
    }

    /**
     * Gets protection
     *
     * @return \Repull\Model\GetVrboListingContent200ResponseSectionsProtection|null
     */
    public function getProtection(): ?\Repull\Model\GetVrboListingContent200ResponseSectionsProtection
    {
        return $this->container['protection'];
    }

    /**
     * Sets protection
     *
     * @param \Repull\Model\GetVrboListingContent200ResponseSectionsProtection|null $protection protection
     *
     * @return $this
     */
    public function setProtection(?\Repull\Model\GetVrboListingContent200ResponseSectionsProtection $protection): static
    {
        if (is_null($protection)) {
            throw new InvalidArgumentException('non-nullable protection cannot be null');
        }
        $this->container['protection'] = $protection;

        return $this;
    }

    /**
     * Gets taxes
     *
     * @return \Repull\Model\GetVrboListingContent200ResponseSectionsTaxes|null
     */
    public function getTaxes(): ?\Repull\Model\GetVrboListingContent200ResponseSectionsTaxes
    {
        return $this->container['taxes'];
    }

    /**
     * Sets taxes
     *
     * @param \Repull\Model\GetVrboListingContent200ResponseSectionsTaxes|null $taxes taxes
     *
     * @return $this
     */
    public function setTaxes(?\Repull\Model\GetVrboListingContent200ResponseSectionsTaxes $taxes): static
    {
        if (is_null($taxes)) {
            throw new InvalidArgumentException('non-nullable taxes cannot be null');
        }
        $this->container['taxes'] = $taxes;

        return $this;
    }

    /**
     * Gets registration
     *
     * @return \Repull\Model\GetVrboListingContent200ResponseSectionsRegistration|null
     */
    public function getRegistration(): ?\Repull\Model\GetVrboListingContent200ResponseSectionsRegistration
    {
        return $this->container['registration'];
    }

    /**
     * Sets registration
     *
     * @param \Repull\Model\GetVrboListingContent200ResponseSectionsRegistration|null $registration registration
     *
     * @return $this
     */
    public function setRegistration(?\Repull\Model\GetVrboListingContent200ResponseSectionsRegistration $registration): static
    {
        if (is_null($registration)) {
            throw new InvalidArgumentException('non-nullable registration cannot be null');
        }
        $this->container['registration'] = $registration;

        return $this;
    }

    /**
     * Gets welcome_guide
     *
     * @return \Repull\Model\GetVrboListingContent200ResponseSectionsWelcomeGuide|null
     */
    public function getWelcomeGuide(): ?\Repull\Model\GetVrboListingContent200ResponseSectionsWelcomeGuide
    {
        return $this->container['welcome_guide'];
    }

    /**
     * Sets welcome_guide
     *
     * @param \Repull\Model\GetVrboListingContent200ResponseSectionsWelcomeGuide|null $welcome_guide welcome_guide
     *
     * @return $this
     */
    public function setWelcomeGuide(?\Repull\Model\GetVrboListingContent200ResponseSectionsWelcomeGuide $welcome_guide): static
    {
        if (is_null($welcome_guide)) {
            throw new InvalidArgumentException('non-nullable welcome_guide cannot be null');
        }
        $this->container['welcome_guide'] = $welcome_guide;

        return $this;
    }

    /**
     * Gets videos
     *
     * @return \Repull\Model\GetVrboListingContent200ResponseSectionsVideos|null
     */
    public function getVideos(): ?\Repull\Model\GetVrboListingContent200ResponseSectionsVideos
    {
        return $this->container['videos'];
    }

    /**
     * Sets videos
     *
     * @param \Repull\Model\GetVrboListingContent200ResponseSectionsVideos|null $videos videos
     *
     * @return $this
     */
    public function setVideos(?\Repull\Model\GetVrboListingContent200ResponseSectionsVideos $videos): static
    {
        if (is_null($videos)) {
            throw new InvalidArgumentException('non-nullable videos cannot be null');
        }
        $this->container['videos'] = $videos;

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


