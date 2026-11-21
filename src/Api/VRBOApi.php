<?php
/**
 * VRBOApi
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

namespace Repull\Api;

use InvalidArgumentException;
use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\MultipartStream;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\RequestOptions;
use GuzzleHttp\Promise\PromiseInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Repull\ApiException;
use Repull\Configuration;
use Repull\HeaderSelector;
use Repull\FormDataProcessor;
use Repull\ObjectSerializer;

/**
 * VRBOApi Class Doc Comment
 *
 * @package  Repull
 * @author   OpenAPI Generator team
 * @link     https://openapi-generator.tech
 */
class VRBOApi
{
    /**
     * @var ClientInterface
     */
    protected ClientInterface $client;

    /**
     * @var Configuration
     */
    protected Configuration $config;

    /**
     * @var HeaderSelector
     */
    protected HeaderSelector $headerSelector;

    /**
     * @var int Host index
     */
    protected int $hostIndex;

    /** @var string[] $contentTypes **/
    public const contentTypes = [
        'addVrboListingVideo' => [
            'application/json',
        ],
        'deleteVrboListingVideos' => [
            'application/json',
        ],
        'getVrboListingAdditionalInfo' => [
            'application/json',
        ],
        'getVrboListingAmenities' => [
            'application/json',
        ],
        'getVrboListingBookingSettings' => [
            'application/json',
        ],
        'getVrboListingCancellation' => [
            'application/json',
        ],
        'getVrboListingContent' => [
            'application/json',
        ],
        'getVrboListingDescription' => [
            'application/json',
        ],
        'getVrboListingFees' => [
            'application/json',
        ],
        'getVrboListingHouseRules' => [
            'application/json',
        ],
        'getVrboListingLocation' => [
            'application/json',
        ],
        'getVrboListingPaymentTerms' => [
            'application/json',
        ],
        'getVrboListingPhotos' => [
            'application/json',
        ],
        'getVrboListingPricing' => [
            'application/json',
        ],
        'getVrboListingProperty' => [
            'application/json',
        ],
        'getVrboListingProtection' => [
            'application/json',
        ],
        'getVrboListingRates' => [
            'application/json',
        ],
        'getVrboListingRegistration' => [
            'application/json',
        ],
        'getVrboListingRooms' => [
            'application/json',
        ],
        'getVrboListingStory' => [
            'application/json',
        ],
        'getVrboListingTaxes' => [
            'application/json',
        ],
        'getVrboListingTranslations' => [
            'application/json',
        ],
        'getVrboListingVideos' => [
            'application/json',
        ],
        'getVrboListingWelcomeGuide' => [
            'application/json',
        ],
        'listVrboListings' => [
            'application/json',
        ],
        'listVrboReservations' => [
            'application/json',
        ],
        'updateVrboListingAdditionalInfo' => [
            'application/json',
        ],
        'updateVrboListingAmenities' => [
            'application/json',
        ],
        'updateVrboListingBookingSettings' => [
            'application/json',
        ],
        'updateVrboListingCancellation' => [
            'application/json',
        ],
        'updateVrboListingDescription' => [
            'application/json',
        ],
        'updateVrboListingFees' => [
            'application/json',
        ],
        'updateVrboListingHouseRules' => [
            'application/json',
        ],
        'updateVrboListingLocation' => [
            'application/json',
        ],
        'updateVrboListingPaymentTerms' => [
            'application/json',
        ],
        'updateVrboListingPhotos' => [
            'application/json',
        ],
        'updateVrboListingPricing' => [
            'application/json',
        ],
        'updateVrboListingProperty' => [
            'application/json',
        ],
        'updateVrboListingProtection' => [
            'application/json',
        ],
        'updateVrboListingRates' => [
            'application/json',
        ],
        'updateVrboListingRegistration' => [
            'application/json',
        ],
        'updateVrboListingRooms' => [
            'application/json',
        ],
        'updateVrboListingStory' => [
            'application/json',
        ],
        'updateVrboListingTaxes' => [
            'application/json',
        ],
        'updateVrboListingTranslations' => [
            'application/json',
        ],
        'updateVrboListingWelcomeGuide' => [
            'application/json',
        ],
    ];

    /**
     * @param ClientInterface|null $client
     * @param Configuration|null   $config
     * @param HeaderSelector|null  $selector
     * @param int                  $hostIndex (Optional) host index to select the list of hosts if defined in the OpenAPI spec
     */
    public function __construct(
        ?ClientInterface $client = null,
        ?Configuration $config = null,
        ?HeaderSelector $selector = null,
        int $hostIndex = 0
    ) {
        $this->client = $client ?: new Client();
        $this->config = $config ?: Configuration::getDefaultConfiguration();
        $this->headerSelector = $selector ?: new HeaderSelector();
        $this->hostIndex = $hostIndex;
    }

    /**
     * Set the host index
     *
     * @param int $hostIndex Host index (required)
     */
    public function setHostIndex(int $hostIndex): void
    {
        $this->hostIndex = $hostIndex;
    }

    /**
     * Get the host index
     *
     * @return int Host index
     */
    public function getHostIndex(): int
    {
        return $this->hostIndex;
    }

    /**
     * @return Configuration
     */
    public function getConfig(): Configuration
    {
        return $this->config;
    }

    /**
     * Operation addVrboListingVideo
     *
     * Add a video to the Vrbo listing
     *
     * @param  string $id Repull listing id (numeric string) of a listing linked to Vrbo. (required)
     * @param  \Repull\Model\AddVrboListingVideoRequest $add_vrbo_listing_video_request add_vrbo_listing_video_request (required)
     * @param  string|null $vrbo_listing_number The Vrbo listing number to work on, when the listing is linked to more than one Vrbo unit. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['addVrboListingVideo'] to see the possible values for this operation
     *
     * @throws ApiException on non-2xx response or if the response body is not in the expected format
     * @throws InvalidArgumentException
     * @return \Repull\Model\AddVrboListingVideo200Response|\Repull\Model\Error
     */
    public function addVrboListingVideo(
        string $id,
        \Repull\Model\AddVrboListingVideoRequest $add_vrbo_listing_video_request,
        ?string $vrbo_listing_number = null,
        string $contentType = self::contentTypes['addVrboListingVideo'][0]
    ): \Repull\Model\AddVrboListingVideo200Response|\Repull\Model\Error
    {
        list($response) = $this->addVrboListingVideoWithHttpInfo($id, $add_vrbo_listing_video_request, $vrbo_listing_number, $contentType);
        return $response;
    }

    /**
     * Operation addVrboListingVideoWithHttpInfo
     *
     * Add a video to the Vrbo listing
     *
     * @param  string $id Repull listing id (numeric string) of a listing linked to Vrbo. (required)
     * @param  \Repull\Model\AddVrboListingVideoRequest $add_vrbo_listing_video_request (required)
     * @param  string|null $vrbo_listing_number The Vrbo listing number to work on, when the listing is linked to more than one Vrbo unit. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['addVrboListingVideo'] to see the possible values for this operation
     *
     * @throws ApiException on non-2xx response or if the response body is not in the expected format
     * @throws InvalidArgumentException
     * @return array of \Repull\Model\AddVrboListingVideo200Response|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error, HTTP status code, HTTP response headers (array of strings)
     */
    public function addVrboListingVideoWithHttpInfo(
        string $id,
        \Repull\Model\AddVrboListingVideoRequest $add_vrbo_listing_video_request,
        ?string $vrbo_listing_number = null,
        string $contentType = self::contentTypes['addVrboListingVideo'][0]
    ): array
    {
        $request = $this->addVrboListingVideoRequest($id, $add_vrbo_listing_video_request, $vrbo_listing_number, $contentType);

        try {
            $options = $this->createHttpClientOption();
            try {
                $response = $this->client->send($request, $options);
            } catch (RequestException $e) {
                throw new ApiException(
                    "[{$e->getCode()}] {$e->getMessage()}",
                    (int) $e->getCode(),
                    $e->getResponse() ? $e->getResponse()->getHeaders() : null,
                    $e->getResponse() ? (string) $e->getResponse()->getBody() : null
                );
            } catch (ConnectException $e) {
                throw new ApiException(
                    "[{$e->getCode()}] {$e->getMessage()}",
                    (int) $e->getCode(),
                    null,
                    null
                );
            }

            $statusCode = $response->getStatusCode();

            switch($statusCode) {
                case 200:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\AddVrboListingVideo200Response',
                        $request,
                        $response,
                    );
                case 401:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 403:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 404:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 409:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 422:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 500:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 502:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 503:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 504:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
            }
            

            if ($statusCode < 200 || $statusCode > 299) {
                throw new ApiException(
                    sprintf(
                        '[%d] Error connecting to the API (%s)',
                        $statusCode,
                        (string) $request->getUri()
                    ),
                    $statusCode,
                    $response->getHeaders(),
                    (string) $response->getBody()
                );
            }

            return $this->handleResponseWithDataType(
                '\Repull\Model\AddVrboListingVideo200Response',
                $request,
                $response,
            );
        } catch (ApiException $e) {
            switch ($e->getCode()) {
                case 200:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\AddVrboListingVideo200Response',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 401:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 403:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 404:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 409:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 422:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 500:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 502:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 503:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 504:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
            }
        
            throw $e;
        }
    }

    /**
     * Operation addVrboListingVideoAsync
     *
     * Add a video to the Vrbo listing
     *
     * @param  string $id Repull listing id (numeric string) of a listing linked to Vrbo. (required)
     * @param  \Repull\Model\AddVrboListingVideoRequest $add_vrbo_listing_video_request (required)
     * @param  string|null $vrbo_listing_number The Vrbo listing number to work on, when the listing is linked to more than one Vrbo unit. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['addVrboListingVideo'] to see the possible values for this operation
     *
     * @throws InvalidArgumentException
     * @return PromiseInterface
     */
    public function addVrboListingVideoAsync(
        string $id,
        \Repull\Model\AddVrboListingVideoRequest $add_vrbo_listing_video_request,
        ?string $vrbo_listing_number = null,
        string $contentType = self::contentTypes['addVrboListingVideo'][0]
    ): PromiseInterface
    {
        return $this->addVrboListingVideoAsyncWithHttpInfo($id, $add_vrbo_listing_video_request, $vrbo_listing_number, $contentType)
            ->then(
                function ($response) {
                    return $response[0];
                }
            );
    }

    /**
     * Operation addVrboListingVideoAsyncWithHttpInfo
     *
     * Add a video to the Vrbo listing
     *
     * @param  string $id Repull listing id (numeric string) of a listing linked to Vrbo. (required)
     * @param  \Repull\Model\AddVrboListingVideoRequest $add_vrbo_listing_video_request (required)
     * @param  string|null $vrbo_listing_number The Vrbo listing number to work on, when the listing is linked to more than one Vrbo unit. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['addVrboListingVideo'] to see the possible values for this operation
     *
     * @throws InvalidArgumentException
     * @return PromiseInterface
     */
    public function addVrboListingVideoAsyncWithHttpInfo(
        string $id,
        \Repull\Model\AddVrboListingVideoRequest $add_vrbo_listing_video_request,
        ?string $vrbo_listing_number = null,
        string $contentType = self::contentTypes['addVrboListingVideo'][0]
    ): PromiseInterface
    {
        $returnType = '\Repull\Model\AddVrboListingVideo200Response';
        $request = $this->addVrboListingVideoRequest($id, $add_vrbo_listing_video_request, $vrbo_listing_number, $contentType);

        return $this->client
            ->sendAsync($request, $this->createHttpClientOption())
            ->then(
                function ($response) use ($returnType) {
                    if (in_array($returnType, ['\SplFileObject', '\Psr\Http\Message\StreamInterface'])) {
                        $content = $response->getBody(); //stream goes to serializer
                    } else {
                        $content = (string) $response->getBody();
                        if ($returnType !== 'string') {
                            $content = json_decode($content);
                        }
                    }

                    return [
                        ObjectSerializer::deserialize($content, $returnType, []),
                        $response->getStatusCode(),
                        $response->getHeaders()
                    ];
                },
                function ($exception) {
                    $response = $exception->getResponse();
                    $statusCode = $response->getStatusCode();
                    throw new ApiException(
                        sprintf(
                            '[%d] Error connecting to the API (%s)',
                            $statusCode,
                            $exception->getRequest()->getUri()
                        ),
                        $statusCode,
                        $response->getHeaders(),
                        (string) $response->getBody()
                    );
                }
            );
    }

    /**
     * Create request for operation 'addVrboListingVideo'
     *
     * @param  string $id Repull listing id (numeric string) of a listing linked to Vrbo. (required)
     * @param  \Repull\Model\AddVrboListingVideoRequest $add_vrbo_listing_video_request (required)
     * @param  string|null $vrbo_listing_number The Vrbo listing number to work on, when the listing is linked to more than one Vrbo unit. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['addVrboListingVideo'] to see the possible values for this operation
     *
     * @throws InvalidArgumentException
     * @return \GuzzleHttp\Psr7\Request
     */
    public function addVrboListingVideoRequest(
        string $id,
        \Repull\Model\AddVrboListingVideoRequest $add_vrbo_listing_video_request,
        ?string $vrbo_listing_number = null,
        string $contentType = self::contentTypes['addVrboListingVideo'][0]
    ): Request
    {

        // verify the required parameter 'id' is set
        if ($id === null || (is_array($id) && count($id) === 0)) {
            throw new InvalidArgumentException(
                'Missing the required parameter $id when calling addVrboListingVideo'
            );
        }

        // verify the required parameter 'add_vrbo_listing_video_request' is set
        if ($add_vrbo_listing_video_request === null || (is_array($add_vrbo_listing_video_request) && count($add_vrbo_listing_video_request) === 0)) {
            throw new InvalidArgumentException(
                'Missing the required parameter $add_vrbo_listing_video_request when calling addVrboListingVideo'
            );
        }



        $resourcePath = '/v1/channels/vrbo/listings/{id}/videos';
        $formParams = [];
        $queryParams = [];
        $headerParams = [];
        $httpBody = '';
        $multipart = false;

        // query params
        $queryParams = array_merge($queryParams, ObjectSerializer::toQueryValue(
            $vrbo_listing_number,
            'vrboListingNumber', // param base name
            'string', // openApiType
            'form', // style
            true, // explode
            false, // required
            null // openApiFormat
        ) ?? []);


        // path params
        if ($id !== null) {
            $resourcePath = str_replace(
                '{id}',
                ObjectSerializer::toPathValue($id),
                $resourcePath
            );
        }


        $headers = $this->headerSelector->selectHeaders(
            ['application/json', ],
            $contentType,
            $multipart
        );

        // for model (json/xml)
        if (isset($add_vrbo_listing_video_request)) {
            if (stripos($headers['Content-Type'], 'application/json') !== false) {
                # if Content-Type contains "application/json", json_encode the body
                $httpBody = \GuzzleHttp\Utils::jsonEncode(ObjectSerializer::sanitizeForSerialization($add_vrbo_listing_video_request));
            } else {
                $httpBody = $add_vrbo_listing_video_request;
            }
        } elseif (count($formParams) > 0) {
            if ($multipart) {
                $multipartContents = [];
                foreach ($formParams as $formParamName => $formParamValue) {
                    $formParamValueItems = is_array($formParamValue) ? $formParamValue : [$formParamValue];
                    foreach ($formParamValueItems as $formParamValueItem) {
                        $multipartContents[] = [
                            'name' => $formParamName,
                            'contents' => $formParamValueItem
                        ];
                    }
                }
                // for HTTP post (form)
                $httpBody = new MultipartStream($multipartContents);

            } elseif (stripos($headers['Content-Type'], 'application/json') !== false) {
                # if Content-Type contains "application/json", json_encode the form parameters
                $httpBody = \GuzzleHttp\Utils::jsonEncode($formParams);
            } else {
                // for HTTP post (form)
                $httpBody = ObjectSerializer::buildQuery($formParams);
            }
        }

        // this endpoint requires Bearer (API Key) authentication (access token)
        if (!empty($this->config->getAccessToken())) {
            $headers['Authorization'] = 'Bearer ' . $this->config->getAccessToken();
        }

        $defaultHeaders = [];
        if ($this->config->getUserAgent()) {
            $defaultHeaders['User-Agent'] = $this->config->getUserAgent();
        }

        $headers = array_merge(
            $defaultHeaders,
            $headerParams,
            $headers
        );

        $operationHost = $this->config->getHost();
        $query = ObjectSerializer::buildQuery($queryParams);
        return new Request(
            'POST',
            $operationHost . $resourcePath . ($query ? "?{$query}" : ''),
            $headers,
            $httpBody
        );
    }

    /**
     * Operation deleteVrboListingVideos
     *
     * Remove videos from the Vrbo listing
     *
     * @param  string $id Repull listing id (numeric string) of a listing linked to Vrbo. (required)
     * @param  string $video_id The &#x60;id&#x60; of the video to remove (comma-separated for several). (required)
     * @param  string|null $vrbo_listing_number The Vrbo listing number to work on, when the listing is linked to more than one Vrbo unit. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['deleteVrboListingVideos'] to see the possible values for this operation
     *
     * @throws ApiException on non-2xx response or if the response body is not in the expected format
     * @throws InvalidArgumentException
     * @return \Repull\Model\AddVrboListingVideo200Response|\Repull\Model\Error
     */
    public function deleteVrboListingVideos(
        string $id,
        string $video_id,
        ?string $vrbo_listing_number = null,
        string $contentType = self::contentTypes['deleteVrboListingVideos'][0]
    ): \Repull\Model\AddVrboListingVideo200Response|\Repull\Model\Error
    {
        list($response) = $this->deleteVrboListingVideosWithHttpInfo($id, $video_id, $vrbo_listing_number, $contentType);
        return $response;
    }

    /**
     * Operation deleteVrboListingVideosWithHttpInfo
     *
     * Remove videos from the Vrbo listing
     *
     * @param  string $id Repull listing id (numeric string) of a listing linked to Vrbo. (required)
     * @param  string $video_id The &#x60;id&#x60; of the video to remove (comma-separated for several). (required)
     * @param  string|null $vrbo_listing_number The Vrbo listing number to work on, when the listing is linked to more than one Vrbo unit. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['deleteVrboListingVideos'] to see the possible values for this operation
     *
     * @throws ApiException on non-2xx response or if the response body is not in the expected format
     * @throws InvalidArgumentException
     * @return array of \Repull\Model\AddVrboListingVideo200Response|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error, HTTP status code, HTTP response headers (array of strings)
     */
    public function deleteVrboListingVideosWithHttpInfo(
        string $id,
        string $video_id,
        ?string $vrbo_listing_number = null,
        string $contentType = self::contentTypes['deleteVrboListingVideos'][0]
    ): array
    {
        $request = $this->deleteVrboListingVideosRequest($id, $video_id, $vrbo_listing_number, $contentType);

        try {
            $options = $this->createHttpClientOption();
            try {
                $response = $this->client->send($request, $options);
            } catch (RequestException $e) {
                throw new ApiException(
                    "[{$e->getCode()}] {$e->getMessage()}",
                    (int) $e->getCode(),
                    $e->getResponse() ? $e->getResponse()->getHeaders() : null,
                    $e->getResponse() ? (string) $e->getResponse()->getBody() : null
                );
            } catch (ConnectException $e) {
                throw new ApiException(
                    "[{$e->getCode()}] {$e->getMessage()}",
                    (int) $e->getCode(),
                    null,
                    null
                );
            }

            $statusCode = $response->getStatusCode();

            switch($statusCode) {
                case 200:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\AddVrboListingVideo200Response',
                        $request,
                        $response,
                    );
                case 401:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 403:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 404:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 409:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 422:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 500:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 502:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 503:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 504:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
            }
            

            if ($statusCode < 200 || $statusCode > 299) {
                throw new ApiException(
                    sprintf(
                        '[%d] Error connecting to the API (%s)',
                        $statusCode,
                        (string) $request->getUri()
                    ),
                    $statusCode,
                    $response->getHeaders(),
                    (string) $response->getBody()
                );
            }

            return $this->handleResponseWithDataType(
                '\Repull\Model\AddVrboListingVideo200Response',
                $request,
                $response,
            );
        } catch (ApiException $e) {
            switch ($e->getCode()) {
                case 200:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\AddVrboListingVideo200Response',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 401:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 403:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 404:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 409:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 422:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 500:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 502:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 503:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 504:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
            }
        
            throw $e;
        }
    }

    /**
     * Operation deleteVrboListingVideosAsync
     *
     * Remove videos from the Vrbo listing
     *
     * @param  string $id Repull listing id (numeric string) of a listing linked to Vrbo. (required)
     * @param  string $video_id The &#x60;id&#x60; of the video to remove (comma-separated for several). (required)
     * @param  string|null $vrbo_listing_number The Vrbo listing number to work on, when the listing is linked to more than one Vrbo unit. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['deleteVrboListingVideos'] to see the possible values for this operation
     *
     * @throws InvalidArgumentException
     * @return PromiseInterface
     */
    public function deleteVrboListingVideosAsync(
        string $id,
        string $video_id,
        ?string $vrbo_listing_number = null,
        string $contentType = self::contentTypes['deleteVrboListingVideos'][0]
    ): PromiseInterface
    {
        return $this->deleteVrboListingVideosAsyncWithHttpInfo($id, $video_id, $vrbo_listing_number, $contentType)
            ->then(
                function ($response) {
                    return $response[0];
                }
            );
    }

    /**
     * Operation deleteVrboListingVideosAsyncWithHttpInfo
     *
     * Remove videos from the Vrbo listing
     *
     * @param  string $id Repull listing id (numeric string) of a listing linked to Vrbo. (required)
     * @param  string $video_id The &#x60;id&#x60; of the video to remove (comma-separated for several). (required)
     * @param  string|null $vrbo_listing_number The Vrbo listing number to work on, when the listing is linked to more than one Vrbo unit. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['deleteVrboListingVideos'] to see the possible values for this operation
     *
     * @throws InvalidArgumentException
     * @return PromiseInterface
     */
    public function deleteVrboListingVideosAsyncWithHttpInfo(
        string $id,
        string $video_id,
        ?string $vrbo_listing_number = null,
        string $contentType = self::contentTypes['deleteVrboListingVideos'][0]
    ): PromiseInterface
    {
        $returnType = '\Repull\Model\AddVrboListingVideo200Response';
        $request = $this->deleteVrboListingVideosRequest($id, $video_id, $vrbo_listing_number, $contentType);

        return $this->client
            ->sendAsync($request, $this->createHttpClientOption())
            ->then(
                function ($response) use ($returnType) {
                    if (in_array($returnType, ['\SplFileObject', '\Psr\Http\Message\StreamInterface'])) {
                        $content = $response->getBody(); //stream goes to serializer
                    } else {
                        $content = (string) $response->getBody();
                        if ($returnType !== 'string') {
                            $content = json_decode($content);
                        }
                    }

                    return [
                        ObjectSerializer::deserialize($content, $returnType, []),
                        $response->getStatusCode(),
                        $response->getHeaders()
                    ];
                },
                function ($exception) {
                    $response = $exception->getResponse();
                    $statusCode = $response->getStatusCode();
                    throw new ApiException(
                        sprintf(
                            '[%d] Error connecting to the API (%s)',
                            $statusCode,
                            $exception->getRequest()->getUri()
                        ),
                        $statusCode,
                        $response->getHeaders(),
                        (string) $response->getBody()
                    );
                }
            );
    }

    /**
     * Create request for operation 'deleteVrboListingVideos'
     *
     * @param  string $id Repull listing id (numeric string) of a listing linked to Vrbo. (required)
     * @param  string $video_id The &#x60;id&#x60; of the video to remove (comma-separated for several). (required)
     * @param  string|null $vrbo_listing_number The Vrbo listing number to work on, when the listing is linked to more than one Vrbo unit. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['deleteVrboListingVideos'] to see the possible values for this operation
     *
     * @throws InvalidArgumentException
     * @return \GuzzleHttp\Psr7\Request
     */
    public function deleteVrboListingVideosRequest(
        string $id,
        string $video_id,
        ?string $vrbo_listing_number = null,
        string $contentType = self::contentTypes['deleteVrboListingVideos'][0]
    ): Request
    {

        // verify the required parameter 'id' is set
        if ($id === null || (is_array($id) && count($id) === 0)) {
            throw new InvalidArgumentException(
                'Missing the required parameter $id when calling deleteVrboListingVideos'
            );
        }

        // verify the required parameter 'video_id' is set
        if ($video_id === null || (is_array($video_id) && count($video_id) === 0)) {
            throw new InvalidArgumentException(
                'Missing the required parameter $video_id when calling deleteVrboListingVideos'
            );
        }



        $resourcePath = '/v1/channels/vrbo/listings/{id}/videos';
        $formParams = [];
        $queryParams = [];
        $headerParams = [];
        $httpBody = '';
        $multipart = false;

        // query params
        $queryParams = array_merge($queryParams, ObjectSerializer::toQueryValue(
            $video_id,
            'videoId', // param base name
            'string', // openApiType
            'form', // style
            true, // explode
            true, // required
            null // openApiFormat
        ) ?? []);
        // query params
        $queryParams = array_merge($queryParams, ObjectSerializer::toQueryValue(
            $vrbo_listing_number,
            'vrboListingNumber', // param base name
            'string', // openApiType
            'form', // style
            true, // explode
            false, // required
            null // openApiFormat
        ) ?? []);


        // path params
        if ($id !== null) {
            $resourcePath = str_replace(
                '{id}',
                ObjectSerializer::toPathValue($id),
                $resourcePath
            );
        }


        $headers = $this->headerSelector->selectHeaders(
            ['application/json', ],
            $contentType,
            $multipart
        );

        // for model (json/xml)
        if (count($formParams) > 0) {
            if ($multipart) {
                $multipartContents = [];
                foreach ($formParams as $formParamName => $formParamValue) {
                    $formParamValueItems = is_array($formParamValue) ? $formParamValue : [$formParamValue];
                    foreach ($formParamValueItems as $formParamValueItem) {
                        $multipartContents[] = [
                            'name' => $formParamName,
                            'contents' => $formParamValueItem
                        ];
                    }
                }
                // for HTTP post (form)
                $httpBody = new MultipartStream($multipartContents);

            } elseif (stripos($headers['Content-Type'], 'application/json') !== false) {
                # if Content-Type contains "application/json", json_encode the form parameters
                $httpBody = \GuzzleHttp\Utils::jsonEncode($formParams);
            } else {
                // for HTTP post (form)
                $httpBody = ObjectSerializer::buildQuery($formParams);
            }
        }

        // this endpoint requires Bearer (API Key) authentication (access token)
        if (!empty($this->config->getAccessToken())) {
            $headers['Authorization'] = 'Bearer ' . $this->config->getAccessToken();
        }

        $defaultHeaders = [];
        if ($this->config->getUserAgent()) {
            $defaultHeaders['User-Agent'] = $this->config->getUserAgent();
        }

        $headers = array_merge(
            $defaultHeaders,
            $headerParams,
            $headers
        );

        $operationHost = $this->config->getHost();
        $query = ObjectSerializer::buildQuery($queryParams);
        return new Request(
            'DELETE',
            $operationHost . $resourcePath . ($query ? "?{$query}" : ''),
            $headers,
            $httpBody
        );
    }

    /**
     * Operation getVrboListingAdditionalInfo
     *
     * Get the Vrbo additional details
     *
     * @param  string $id Repull listing id (numeric string) of a listing linked to Vrbo. (required)
     * @param  string|null $vrbo_listing_number The Vrbo listing number to work on, when the listing is linked to more than one Vrbo unit. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['getVrboListingAdditionalInfo'] to see the possible values for this operation
     *
     * @throws ApiException on non-2xx response or if the response body is not in the expected format
     * @throws InvalidArgumentException
     * @return \Repull\Model\GetVrboListingAdditionalInfo200Response|\Repull\Model\Error
     */
    public function getVrboListingAdditionalInfo(
        string $id,
        ?string $vrbo_listing_number = null,
        string $contentType = self::contentTypes['getVrboListingAdditionalInfo'][0]
    ): \Repull\Model\GetVrboListingAdditionalInfo200Response|\Repull\Model\Error
    {
        list($response) = $this->getVrboListingAdditionalInfoWithHttpInfo($id, $vrbo_listing_number, $contentType);
        return $response;
    }

    /**
     * Operation getVrboListingAdditionalInfoWithHttpInfo
     *
     * Get the Vrbo additional details
     *
     * @param  string $id Repull listing id (numeric string) of a listing linked to Vrbo. (required)
     * @param  string|null $vrbo_listing_number The Vrbo listing number to work on, when the listing is linked to more than one Vrbo unit. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['getVrboListingAdditionalInfo'] to see the possible values for this operation
     *
     * @throws ApiException on non-2xx response or if the response body is not in the expected format
     * @throws InvalidArgumentException
     * @return array of \Repull\Model\GetVrboListingAdditionalInfo200Response|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error, HTTP status code, HTTP response headers (array of strings)
     */
    public function getVrboListingAdditionalInfoWithHttpInfo(
        string $id,
        ?string $vrbo_listing_number = null,
        string $contentType = self::contentTypes['getVrboListingAdditionalInfo'][0]
    ): array
    {
        $request = $this->getVrboListingAdditionalInfoRequest($id, $vrbo_listing_number, $contentType);

        try {
            $options = $this->createHttpClientOption();
            try {
                $response = $this->client->send($request, $options);
            } catch (RequestException $e) {
                throw new ApiException(
                    "[{$e->getCode()}] {$e->getMessage()}",
                    (int) $e->getCode(),
                    $e->getResponse() ? $e->getResponse()->getHeaders() : null,
                    $e->getResponse() ? (string) $e->getResponse()->getBody() : null
                );
            } catch (ConnectException $e) {
                throw new ApiException(
                    "[{$e->getCode()}] {$e->getMessage()}",
                    (int) $e->getCode(),
                    null,
                    null
                );
            }

            $statusCode = $response->getStatusCode();

            switch($statusCode) {
                case 200:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\GetVrboListingAdditionalInfo200Response',
                        $request,
                        $response,
                    );
                case 401:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 403:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 404:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 409:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 422:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 500:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 502:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 503:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 504:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
            }
            

            if ($statusCode < 200 || $statusCode > 299) {
                throw new ApiException(
                    sprintf(
                        '[%d] Error connecting to the API (%s)',
                        $statusCode,
                        (string) $request->getUri()
                    ),
                    $statusCode,
                    $response->getHeaders(),
                    (string) $response->getBody()
                );
            }

            return $this->handleResponseWithDataType(
                '\Repull\Model\GetVrboListingAdditionalInfo200Response',
                $request,
                $response,
            );
        } catch (ApiException $e) {
            switch ($e->getCode()) {
                case 200:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\GetVrboListingAdditionalInfo200Response',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 401:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 403:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 404:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 409:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 422:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 500:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 502:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 503:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 504:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
            }
        
            throw $e;
        }
    }

    /**
     * Operation getVrboListingAdditionalInfoAsync
     *
     * Get the Vrbo additional details
     *
     * @param  string $id Repull listing id (numeric string) of a listing linked to Vrbo. (required)
     * @param  string|null $vrbo_listing_number The Vrbo listing number to work on, when the listing is linked to more than one Vrbo unit. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['getVrboListingAdditionalInfo'] to see the possible values for this operation
     *
     * @throws InvalidArgumentException
     * @return PromiseInterface
     */
    public function getVrboListingAdditionalInfoAsync(
        string $id,
        ?string $vrbo_listing_number = null,
        string $contentType = self::contentTypes['getVrboListingAdditionalInfo'][0]
    ): PromiseInterface
    {
        return $this->getVrboListingAdditionalInfoAsyncWithHttpInfo($id, $vrbo_listing_number, $contentType)
            ->then(
                function ($response) {
                    return $response[0];
                }
            );
    }

    /**
     * Operation getVrboListingAdditionalInfoAsyncWithHttpInfo
     *
     * Get the Vrbo additional details
     *
     * @param  string $id Repull listing id (numeric string) of a listing linked to Vrbo. (required)
     * @param  string|null $vrbo_listing_number The Vrbo listing number to work on, when the listing is linked to more than one Vrbo unit. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['getVrboListingAdditionalInfo'] to see the possible values for this operation
     *
     * @throws InvalidArgumentException
     * @return PromiseInterface
     */
    public function getVrboListingAdditionalInfoAsyncWithHttpInfo(
        string $id,
        ?string $vrbo_listing_number = null,
        string $contentType = self::contentTypes['getVrboListingAdditionalInfo'][0]
    ): PromiseInterface
    {
        $returnType = '\Repull\Model\GetVrboListingAdditionalInfo200Response';
        $request = $this->getVrboListingAdditionalInfoRequest($id, $vrbo_listing_number, $contentType);

        return $this->client
            ->sendAsync($request, $this->createHttpClientOption())
            ->then(
                function ($response) use ($returnType) {
                    if (in_array($returnType, ['\SplFileObject', '\Psr\Http\Message\StreamInterface'])) {
                        $content = $response->getBody(); //stream goes to serializer
                    } else {
                        $content = (string) $response->getBody();
                        if ($returnType !== 'string') {
                            $content = json_decode($content);
                        }
                    }

                    return [
                        ObjectSerializer::deserialize($content, $returnType, []),
                        $response->getStatusCode(),
                        $response->getHeaders()
                    ];
                },
                function ($exception) {
                    $response = $exception->getResponse();
                    $statusCode = $response->getStatusCode();
                    throw new ApiException(
                        sprintf(
                            '[%d] Error connecting to the API (%s)',
                            $statusCode,
                            $exception->getRequest()->getUri()
                        ),
                        $statusCode,
                        $response->getHeaders(),
                        (string) $response->getBody()
                    );
                }
            );
    }

    /**
     * Create request for operation 'getVrboListingAdditionalInfo'
     *
     * @param  string $id Repull listing id (numeric string) of a listing linked to Vrbo. (required)
     * @param  string|null $vrbo_listing_number The Vrbo listing number to work on, when the listing is linked to more than one Vrbo unit. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['getVrboListingAdditionalInfo'] to see the possible values for this operation
     *
     * @throws InvalidArgumentException
     * @return \GuzzleHttp\Psr7\Request
     */
    public function getVrboListingAdditionalInfoRequest(
        string $id,
        ?string $vrbo_listing_number = null,
        string $contentType = self::contentTypes['getVrboListingAdditionalInfo'][0]
    ): Request
    {

        // verify the required parameter 'id' is set
        if ($id === null || (is_array($id) && count($id) === 0)) {
            throw new InvalidArgumentException(
                'Missing the required parameter $id when calling getVrboListingAdditionalInfo'
            );
        }



        $resourcePath = '/v1/channels/vrbo/listings/{id}/additional-info';
        $formParams = [];
        $queryParams = [];
        $headerParams = [];
        $httpBody = '';
        $multipart = false;

        // query params
        $queryParams = array_merge($queryParams, ObjectSerializer::toQueryValue(
            $vrbo_listing_number,
            'vrboListingNumber', // param base name
            'string', // openApiType
            'form', // style
            true, // explode
            false, // required
            null // openApiFormat
        ) ?? []);


        // path params
        if ($id !== null) {
            $resourcePath = str_replace(
                '{id}',
                ObjectSerializer::toPathValue($id),
                $resourcePath
            );
        }


        $headers = $this->headerSelector->selectHeaders(
            ['application/json', ],
            $contentType,
            $multipart
        );

        // for model (json/xml)
        if (count($formParams) > 0) {
            if ($multipart) {
                $multipartContents = [];
                foreach ($formParams as $formParamName => $formParamValue) {
                    $formParamValueItems = is_array($formParamValue) ? $formParamValue : [$formParamValue];
                    foreach ($formParamValueItems as $formParamValueItem) {
                        $multipartContents[] = [
                            'name' => $formParamName,
                            'contents' => $formParamValueItem
                        ];
                    }
                }
                // for HTTP post (form)
                $httpBody = new MultipartStream($multipartContents);

            } elseif (stripos($headers['Content-Type'], 'application/json') !== false) {
                # if Content-Type contains "application/json", json_encode the form parameters
                $httpBody = \GuzzleHttp\Utils::jsonEncode($formParams);
            } else {
                // for HTTP post (form)
                $httpBody = ObjectSerializer::buildQuery($formParams);
            }
        }

        // this endpoint requires Bearer (API Key) authentication (access token)
        if (!empty($this->config->getAccessToken())) {
            $headers['Authorization'] = 'Bearer ' . $this->config->getAccessToken();
        }

        $defaultHeaders = [];
        if ($this->config->getUserAgent()) {
            $defaultHeaders['User-Agent'] = $this->config->getUserAgent();
        }

        $headers = array_merge(
            $defaultHeaders,
            $headerParams,
            $headers
        );

        $operationHost = $this->config->getHost();
        $query = ObjectSerializer::buildQuery($queryParams);
        return new Request(
            'GET',
            $operationHost . $resourcePath . ($query ? "?{$query}" : ''),
            $headers,
            $httpBody
        );
    }

    /**
     * Operation getVrboListingAmenities
     *
     * Get the Vrbo amenities
     *
     * @param  string $id Repull listing id (numeric string) of a listing linked to Vrbo. (required)
     * @param  string|null $vrbo_listing_number The Vrbo listing number to work on, when the listing is linked to more than one Vrbo unit. (optional)
     * @param  string|null $groups Amenity groups to read, comma-separated (default: all 19 — one Vrbo call each): essentials, internet, climateControl, general, office, kitchenAndDining, parking, poolAndSpa, sustainability, entertainment, outdoor, onsiteService, accessibility, homeSafety, safetyDevices, enhancedCleaning, locationAndView, localAttractionsAndActivities, checkoutInstructions. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['getVrboListingAmenities'] to see the possible values for this operation
     *
     * @throws ApiException on non-2xx response or if the response body is not in the expected format
     * @throws InvalidArgumentException
     * @return \Repull\Model\GetVrboListingAmenities200Response|\Repull\Model\Error
     */
    public function getVrboListingAmenities(
        string $id,
        ?string $vrbo_listing_number = null,
        ?string $groups = null,
        string $contentType = self::contentTypes['getVrboListingAmenities'][0]
    ): \Repull\Model\GetVrboListingAmenities200Response|\Repull\Model\Error
    {
        list($response) = $this->getVrboListingAmenitiesWithHttpInfo($id, $vrbo_listing_number, $groups, $contentType);
        return $response;
    }

    /**
     * Operation getVrboListingAmenitiesWithHttpInfo
     *
     * Get the Vrbo amenities
     *
     * @param  string $id Repull listing id (numeric string) of a listing linked to Vrbo. (required)
     * @param  string|null $vrbo_listing_number The Vrbo listing number to work on, when the listing is linked to more than one Vrbo unit. (optional)
     * @param  string|null $groups Amenity groups to read, comma-separated (default: all 19 — one Vrbo call each): essentials, internet, climateControl, general, office, kitchenAndDining, parking, poolAndSpa, sustainability, entertainment, outdoor, onsiteService, accessibility, homeSafety, safetyDevices, enhancedCleaning, locationAndView, localAttractionsAndActivities, checkoutInstructions. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['getVrboListingAmenities'] to see the possible values for this operation
     *
     * @throws ApiException on non-2xx response or if the response body is not in the expected format
     * @throws InvalidArgumentException
     * @return array of \Repull\Model\GetVrboListingAmenities200Response|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error, HTTP status code, HTTP response headers (array of strings)
     */
    public function getVrboListingAmenitiesWithHttpInfo(
        string $id,
        ?string $vrbo_listing_number = null,
        ?string $groups = null,
        string $contentType = self::contentTypes['getVrboListingAmenities'][0]
    ): array
    {
        $request = $this->getVrboListingAmenitiesRequest($id, $vrbo_listing_number, $groups, $contentType);

        try {
            $options = $this->createHttpClientOption();
            try {
                $response = $this->client->send($request, $options);
            } catch (RequestException $e) {
                throw new ApiException(
                    "[{$e->getCode()}] {$e->getMessage()}",
                    (int) $e->getCode(),
                    $e->getResponse() ? $e->getResponse()->getHeaders() : null,
                    $e->getResponse() ? (string) $e->getResponse()->getBody() : null
                );
            } catch (ConnectException $e) {
                throw new ApiException(
                    "[{$e->getCode()}] {$e->getMessage()}",
                    (int) $e->getCode(),
                    null,
                    null
                );
            }

            $statusCode = $response->getStatusCode();

            switch($statusCode) {
                case 200:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\GetVrboListingAmenities200Response',
                        $request,
                        $response,
                    );
                case 401:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 403:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 404:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 409:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 422:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 500:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 502:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 503:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 504:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
            }
            

            if ($statusCode < 200 || $statusCode > 299) {
                throw new ApiException(
                    sprintf(
                        '[%d] Error connecting to the API (%s)',
                        $statusCode,
                        (string) $request->getUri()
                    ),
                    $statusCode,
                    $response->getHeaders(),
                    (string) $response->getBody()
                );
            }

            return $this->handleResponseWithDataType(
                '\Repull\Model\GetVrboListingAmenities200Response',
                $request,
                $response,
            );
        } catch (ApiException $e) {
            switch ($e->getCode()) {
                case 200:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\GetVrboListingAmenities200Response',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 401:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 403:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 404:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 409:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 422:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 500:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 502:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 503:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 504:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
            }
        
            throw $e;
        }
    }

    /**
     * Operation getVrboListingAmenitiesAsync
     *
     * Get the Vrbo amenities
     *
     * @param  string $id Repull listing id (numeric string) of a listing linked to Vrbo. (required)
     * @param  string|null $vrbo_listing_number The Vrbo listing number to work on, when the listing is linked to more than one Vrbo unit. (optional)
     * @param  string|null $groups Amenity groups to read, comma-separated (default: all 19 — one Vrbo call each): essentials, internet, climateControl, general, office, kitchenAndDining, parking, poolAndSpa, sustainability, entertainment, outdoor, onsiteService, accessibility, homeSafety, safetyDevices, enhancedCleaning, locationAndView, localAttractionsAndActivities, checkoutInstructions. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['getVrboListingAmenities'] to see the possible values for this operation
     *
     * @throws InvalidArgumentException
     * @return PromiseInterface
     */
    public function getVrboListingAmenitiesAsync(
        string $id,
        ?string $vrbo_listing_number = null,
        ?string $groups = null,
        string $contentType = self::contentTypes['getVrboListingAmenities'][0]
    ): PromiseInterface
    {
        return $this->getVrboListingAmenitiesAsyncWithHttpInfo($id, $vrbo_listing_number, $groups, $contentType)
            ->then(
                function ($response) {
                    return $response[0];
                }
            );
    }

    /**
     * Operation getVrboListingAmenitiesAsyncWithHttpInfo
     *
     * Get the Vrbo amenities
     *
     * @param  string $id Repull listing id (numeric string) of a listing linked to Vrbo. (required)
     * @param  string|null $vrbo_listing_number The Vrbo listing number to work on, when the listing is linked to more than one Vrbo unit. (optional)
     * @param  string|null $groups Amenity groups to read, comma-separated (default: all 19 — one Vrbo call each): essentials, internet, climateControl, general, office, kitchenAndDining, parking, poolAndSpa, sustainability, entertainment, outdoor, onsiteService, accessibility, homeSafety, safetyDevices, enhancedCleaning, locationAndView, localAttractionsAndActivities, checkoutInstructions. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['getVrboListingAmenities'] to see the possible values for this operation
     *
     * @throws InvalidArgumentException
     * @return PromiseInterface
     */
    public function getVrboListingAmenitiesAsyncWithHttpInfo(
        string $id,
        ?string $vrbo_listing_number = null,
        ?string $groups = null,
        string $contentType = self::contentTypes['getVrboListingAmenities'][0]
    ): PromiseInterface
    {
        $returnType = '\Repull\Model\GetVrboListingAmenities200Response';
        $request = $this->getVrboListingAmenitiesRequest($id, $vrbo_listing_number, $groups, $contentType);

        return $this->client
            ->sendAsync($request, $this->createHttpClientOption())
            ->then(
                function ($response) use ($returnType) {
                    if (in_array($returnType, ['\SplFileObject', '\Psr\Http\Message\StreamInterface'])) {
                        $content = $response->getBody(); //stream goes to serializer
                    } else {
                        $content = (string) $response->getBody();
                        if ($returnType !== 'string') {
                            $content = json_decode($content);
                        }
                    }

                    return [
                        ObjectSerializer::deserialize($content, $returnType, []),
                        $response->getStatusCode(),
                        $response->getHeaders()
                    ];
                },
                function ($exception) {
                    $response = $exception->getResponse();
                    $statusCode = $response->getStatusCode();
                    throw new ApiException(
                        sprintf(
                            '[%d] Error connecting to the API (%s)',
                            $statusCode,
                            $exception->getRequest()->getUri()
                        ),
                        $statusCode,
                        $response->getHeaders(),
                        (string) $response->getBody()
                    );
                }
            );
    }

    /**
     * Create request for operation 'getVrboListingAmenities'
     *
     * @param  string $id Repull listing id (numeric string) of a listing linked to Vrbo. (required)
     * @param  string|null $vrbo_listing_number The Vrbo listing number to work on, when the listing is linked to more than one Vrbo unit. (optional)
     * @param  string|null $groups Amenity groups to read, comma-separated (default: all 19 — one Vrbo call each): essentials, internet, climateControl, general, office, kitchenAndDining, parking, poolAndSpa, sustainability, entertainment, outdoor, onsiteService, accessibility, homeSafety, safetyDevices, enhancedCleaning, locationAndView, localAttractionsAndActivities, checkoutInstructions. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['getVrboListingAmenities'] to see the possible values for this operation
     *
     * @throws InvalidArgumentException
     * @return \GuzzleHttp\Psr7\Request
     */
    public function getVrboListingAmenitiesRequest(
        string $id,
        ?string $vrbo_listing_number = null,
        ?string $groups = null,
        string $contentType = self::contentTypes['getVrboListingAmenities'][0]
    ): Request
    {

        // verify the required parameter 'id' is set
        if ($id === null || (is_array($id) && count($id) === 0)) {
            throw new InvalidArgumentException(
                'Missing the required parameter $id when calling getVrboListingAmenities'
            );
        }




        $resourcePath = '/v1/channels/vrbo/listings/{id}/amenities';
        $formParams = [];
        $queryParams = [];
        $headerParams = [];
        $httpBody = '';
        $multipart = false;

        // query params
        $queryParams = array_merge($queryParams, ObjectSerializer::toQueryValue(
            $vrbo_listing_number,
            'vrboListingNumber', // param base name
            'string', // openApiType
            'form', // style
            true, // explode
            false, // required
            null // openApiFormat
        ) ?? []);
        // query params
        $queryParams = array_merge($queryParams, ObjectSerializer::toQueryValue(
            $groups,
            'groups', // param base name
            'string', // openApiType
            'form', // style
            true, // explode
            false, // required
            null // openApiFormat
        ) ?? []);


        // path params
        if ($id !== null) {
            $resourcePath = str_replace(
                '{id}',
                ObjectSerializer::toPathValue($id),
                $resourcePath
            );
        }


        $headers = $this->headerSelector->selectHeaders(
            ['application/json', ],
            $contentType,
            $multipart
        );

        // for model (json/xml)
        if (count($formParams) > 0) {
            if ($multipart) {
                $multipartContents = [];
                foreach ($formParams as $formParamName => $formParamValue) {
                    $formParamValueItems = is_array($formParamValue) ? $formParamValue : [$formParamValue];
                    foreach ($formParamValueItems as $formParamValueItem) {
                        $multipartContents[] = [
                            'name' => $formParamName,
                            'contents' => $formParamValueItem
                        ];
                    }
                }
                // for HTTP post (form)
                $httpBody = new MultipartStream($multipartContents);

            } elseif (stripos($headers['Content-Type'], 'application/json') !== false) {
                # if Content-Type contains "application/json", json_encode the form parameters
                $httpBody = \GuzzleHttp\Utils::jsonEncode($formParams);
            } else {
                // for HTTP post (form)
                $httpBody = ObjectSerializer::buildQuery($formParams);
            }
        }

        // this endpoint requires Bearer (API Key) authentication (access token)
        if (!empty($this->config->getAccessToken())) {
            $headers['Authorization'] = 'Bearer ' . $this->config->getAccessToken();
        }

        $defaultHeaders = [];
        if ($this->config->getUserAgent()) {
            $defaultHeaders['User-Agent'] = $this->config->getUserAgent();
        }

        $headers = array_merge(
            $defaultHeaders,
            $headerParams,
            $headers
        );

        $operationHost = $this->config->getHost();
        $query = ObjectSerializer::buildQuery($queryParams);
        return new Request(
            'GET',
            $operationHost . $resourcePath . ($query ? "?{$query}" : ''),
            $headers,
            $httpBody
        );
    }

    /**
     * Operation getVrboListingBookingSettings
     *
     * Get the Vrbo booking settings
     *
     * @param  string $id Repull listing id (numeric string) of a listing linked to Vrbo. (required)
     * @param  string|null $vrbo_listing_number The Vrbo listing number to work on, when the listing is linked to more than one Vrbo unit. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['getVrboListingBookingSettings'] to see the possible values for this operation
     *
     * @throws ApiException on non-2xx response or if the response body is not in the expected format
     * @throws InvalidArgumentException
     * @return \Repull\Model\GetVrboListingBookingSettings200Response|\Repull\Model\Error
     */
    public function getVrboListingBookingSettings(
        string $id,
        ?string $vrbo_listing_number = null,
        string $contentType = self::contentTypes['getVrboListingBookingSettings'][0]
    ): \Repull\Model\GetVrboListingBookingSettings200Response|\Repull\Model\Error
    {
        list($response) = $this->getVrboListingBookingSettingsWithHttpInfo($id, $vrbo_listing_number, $contentType);
        return $response;
    }

    /**
     * Operation getVrboListingBookingSettingsWithHttpInfo
     *
     * Get the Vrbo booking settings
     *
     * @param  string $id Repull listing id (numeric string) of a listing linked to Vrbo. (required)
     * @param  string|null $vrbo_listing_number The Vrbo listing number to work on, when the listing is linked to more than one Vrbo unit. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['getVrboListingBookingSettings'] to see the possible values for this operation
     *
     * @throws ApiException on non-2xx response or if the response body is not in the expected format
     * @throws InvalidArgumentException
     * @return array of \Repull\Model\GetVrboListingBookingSettings200Response|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error, HTTP status code, HTTP response headers (array of strings)
     */
    public function getVrboListingBookingSettingsWithHttpInfo(
        string $id,
        ?string $vrbo_listing_number = null,
        string $contentType = self::contentTypes['getVrboListingBookingSettings'][0]
    ): array
    {
        $request = $this->getVrboListingBookingSettingsRequest($id, $vrbo_listing_number, $contentType);

        try {
            $options = $this->createHttpClientOption();
            try {
                $response = $this->client->send($request, $options);
            } catch (RequestException $e) {
                throw new ApiException(
                    "[{$e->getCode()}] {$e->getMessage()}",
                    (int) $e->getCode(),
                    $e->getResponse() ? $e->getResponse()->getHeaders() : null,
                    $e->getResponse() ? (string) $e->getResponse()->getBody() : null
                );
            } catch (ConnectException $e) {
                throw new ApiException(
                    "[{$e->getCode()}] {$e->getMessage()}",
                    (int) $e->getCode(),
                    null,
                    null
                );
            }

            $statusCode = $response->getStatusCode();

            switch($statusCode) {
                case 200:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\GetVrboListingBookingSettings200Response',
                        $request,
                        $response,
                    );
                case 401:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 403:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 404:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 409:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 422:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 500:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 502:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 503:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 504:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
            }
            

            if ($statusCode < 200 || $statusCode > 299) {
                throw new ApiException(
                    sprintf(
                        '[%d] Error connecting to the API (%s)',
                        $statusCode,
                        (string) $request->getUri()
                    ),
                    $statusCode,
                    $response->getHeaders(),
                    (string) $response->getBody()
                );
            }

            return $this->handleResponseWithDataType(
                '\Repull\Model\GetVrboListingBookingSettings200Response',
                $request,
                $response,
            );
        } catch (ApiException $e) {
            switch ($e->getCode()) {
                case 200:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\GetVrboListingBookingSettings200Response',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 401:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 403:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 404:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 409:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 422:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 500:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 502:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 503:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 504:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
            }
        
            throw $e;
        }
    }

    /**
     * Operation getVrboListingBookingSettingsAsync
     *
     * Get the Vrbo booking settings
     *
     * @param  string $id Repull listing id (numeric string) of a listing linked to Vrbo. (required)
     * @param  string|null $vrbo_listing_number The Vrbo listing number to work on, when the listing is linked to more than one Vrbo unit. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['getVrboListingBookingSettings'] to see the possible values for this operation
     *
     * @throws InvalidArgumentException
     * @return PromiseInterface
     */
    public function getVrboListingBookingSettingsAsync(
        string $id,
        ?string $vrbo_listing_number = null,
        string $contentType = self::contentTypes['getVrboListingBookingSettings'][0]
    ): PromiseInterface
    {
        return $this->getVrboListingBookingSettingsAsyncWithHttpInfo($id, $vrbo_listing_number, $contentType)
            ->then(
                function ($response) {
                    return $response[0];
                }
            );
    }

    /**
     * Operation getVrboListingBookingSettingsAsyncWithHttpInfo
     *
     * Get the Vrbo booking settings
     *
     * @param  string $id Repull listing id (numeric string) of a listing linked to Vrbo. (required)
     * @param  string|null $vrbo_listing_number The Vrbo listing number to work on, when the listing is linked to more than one Vrbo unit. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['getVrboListingBookingSettings'] to see the possible values for this operation
     *
     * @throws InvalidArgumentException
     * @return PromiseInterface
     */
    public function getVrboListingBookingSettingsAsyncWithHttpInfo(
        string $id,
        ?string $vrbo_listing_number = null,
        string $contentType = self::contentTypes['getVrboListingBookingSettings'][0]
    ): PromiseInterface
    {
        $returnType = '\Repull\Model\GetVrboListingBookingSettings200Response';
        $request = $this->getVrboListingBookingSettingsRequest($id, $vrbo_listing_number, $contentType);

        return $this->client
            ->sendAsync($request, $this->createHttpClientOption())
            ->then(
                function ($response) use ($returnType) {
                    if (in_array($returnType, ['\SplFileObject', '\Psr\Http\Message\StreamInterface'])) {
                        $content = $response->getBody(); //stream goes to serializer
                    } else {
                        $content = (string) $response->getBody();
                        if ($returnType !== 'string') {
                            $content = json_decode($content);
                        }
                    }

                    return [
                        ObjectSerializer::deserialize($content, $returnType, []),
                        $response->getStatusCode(),
                        $response->getHeaders()
                    ];
                },
                function ($exception) {
                    $response = $exception->getResponse();
                    $statusCode = $response->getStatusCode();
                    throw new ApiException(
                        sprintf(
                            '[%d] Error connecting to the API (%s)',
                            $statusCode,
                            $exception->getRequest()->getUri()
                        ),
                        $statusCode,
                        $response->getHeaders(),
                        (string) $response->getBody()
                    );
                }
            );
    }

    /**
     * Create request for operation 'getVrboListingBookingSettings'
     *
     * @param  string $id Repull listing id (numeric string) of a listing linked to Vrbo. (required)
     * @param  string|null $vrbo_listing_number The Vrbo listing number to work on, when the listing is linked to more than one Vrbo unit. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['getVrboListingBookingSettings'] to see the possible values for this operation
     *
     * @throws InvalidArgumentException
     * @return \GuzzleHttp\Psr7\Request
     */
    public function getVrboListingBookingSettingsRequest(
        string $id,
        ?string $vrbo_listing_number = null,
        string $contentType = self::contentTypes['getVrboListingBookingSettings'][0]
    ): Request
    {

        // verify the required parameter 'id' is set
        if ($id === null || (is_array($id) && count($id) === 0)) {
            throw new InvalidArgumentException(
                'Missing the required parameter $id when calling getVrboListingBookingSettings'
            );
        }



        $resourcePath = '/v1/channels/vrbo/listings/{id}/booking-settings';
        $formParams = [];
        $queryParams = [];
        $headerParams = [];
        $httpBody = '';
        $multipart = false;

        // query params
        $queryParams = array_merge($queryParams, ObjectSerializer::toQueryValue(
            $vrbo_listing_number,
            'vrboListingNumber', // param base name
            'string', // openApiType
            'form', // style
            true, // explode
            false, // required
            null // openApiFormat
        ) ?? []);


        // path params
        if ($id !== null) {
            $resourcePath = str_replace(
                '{id}',
                ObjectSerializer::toPathValue($id),
                $resourcePath
            );
        }


        $headers = $this->headerSelector->selectHeaders(
            ['application/json', ],
            $contentType,
            $multipart
        );

        // for model (json/xml)
        if (count($formParams) > 0) {
            if ($multipart) {
                $multipartContents = [];
                foreach ($formParams as $formParamName => $formParamValue) {
                    $formParamValueItems = is_array($formParamValue) ? $formParamValue : [$formParamValue];
                    foreach ($formParamValueItems as $formParamValueItem) {
                        $multipartContents[] = [
                            'name' => $formParamName,
                            'contents' => $formParamValueItem
                        ];
                    }
                }
                // for HTTP post (form)
                $httpBody = new MultipartStream($multipartContents);

            } elseif (stripos($headers['Content-Type'], 'application/json') !== false) {
                # if Content-Type contains "application/json", json_encode the form parameters
                $httpBody = \GuzzleHttp\Utils::jsonEncode($formParams);
            } else {
                // for HTTP post (form)
                $httpBody = ObjectSerializer::buildQuery($formParams);
            }
        }

        // this endpoint requires Bearer (API Key) authentication (access token)
        if (!empty($this->config->getAccessToken())) {
            $headers['Authorization'] = 'Bearer ' . $this->config->getAccessToken();
        }

        $defaultHeaders = [];
        if ($this->config->getUserAgent()) {
            $defaultHeaders['User-Agent'] = $this->config->getUserAgent();
        }

        $headers = array_merge(
            $defaultHeaders,
            $headerParams,
            $headers
        );

        $operationHost = $this->config->getHost();
        $query = ObjectSerializer::buildQuery($queryParams);
        return new Request(
            'GET',
            $operationHost . $resourcePath . ($query ? "?{$query}" : ''),
            $headers,
            $httpBody
        );
    }

    /**
     * Operation getVrboListingCancellation
     *
     * Get the Vrbo cancellation policy
     *
     * @param  string $id Repull listing id (numeric string) of a listing linked to Vrbo. (required)
     * @param  string|null $vrbo_listing_number The Vrbo listing number to work on, when the listing is linked to more than one Vrbo unit. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['getVrboListingCancellation'] to see the possible values for this operation
     *
     * @throws ApiException on non-2xx response or if the response body is not in the expected format
     * @throws InvalidArgumentException
     * @return \Repull\Model\GetVrboListingCancellation200Response|\Repull\Model\Error
     */
    public function getVrboListingCancellation(
        string $id,
        ?string $vrbo_listing_number = null,
        string $contentType = self::contentTypes['getVrboListingCancellation'][0]
    ): \Repull\Model\GetVrboListingCancellation200Response|\Repull\Model\Error
    {
        list($response) = $this->getVrboListingCancellationWithHttpInfo($id, $vrbo_listing_number, $contentType);
        return $response;
    }

    /**
     * Operation getVrboListingCancellationWithHttpInfo
     *
     * Get the Vrbo cancellation policy
     *
     * @param  string $id Repull listing id (numeric string) of a listing linked to Vrbo. (required)
     * @param  string|null $vrbo_listing_number The Vrbo listing number to work on, when the listing is linked to more than one Vrbo unit. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['getVrboListingCancellation'] to see the possible values for this operation
     *
     * @throws ApiException on non-2xx response or if the response body is not in the expected format
     * @throws InvalidArgumentException
     * @return array of \Repull\Model\GetVrboListingCancellation200Response|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error, HTTP status code, HTTP response headers (array of strings)
     */
    public function getVrboListingCancellationWithHttpInfo(
        string $id,
        ?string $vrbo_listing_number = null,
        string $contentType = self::contentTypes['getVrboListingCancellation'][0]
    ): array
    {
        $request = $this->getVrboListingCancellationRequest($id, $vrbo_listing_number, $contentType);

        try {
            $options = $this->createHttpClientOption();
            try {
                $response = $this->client->send($request, $options);
            } catch (RequestException $e) {
                throw new ApiException(
                    "[{$e->getCode()}] {$e->getMessage()}",
                    (int) $e->getCode(),
                    $e->getResponse() ? $e->getResponse()->getHeaders() : null,
                    $e->getResponse() ? (string) $e->getResponse()->getBody() : null
                );
            } catch (ConnectException $e) {
                throw new ApiException(
                    "[{$e->getCode()}] {$e->getMessage()}",
                    (int) $e->getCode(),
                    null,
                    null
                );
            }

            $statusCode = $response->getStatusCode();

            switch($statusCode) {
                case 200:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\GetVrboListingCancellation200Response',
                        $request,
                        $response,
                    );
                case 401:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 403:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 404:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 409:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 422:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 500:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 502:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 503:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 504:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
            }
            

            if ($statusCode < 200 || $statusCode > 299) {
                throw new ApiException(
                    sprintf(
                        '[%d] Error connecting to the API (%s)',
                        $statusCode,
                        (string) $request->getUri()
                    ),
                    $statusCode,
                    $response->getHeaders(),
                    (string) $response->getBody()
                );
            }

            return $this->handleResponseWithDataType(
                '\Repull\Model\GetVrboListingCancellation200Response',
                $request,
                $response,
            );
        } catch (ApiException $e) {
            switch ($e->getCode()) {
                case 200:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\GetVrboListingCancellation200Response',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 401:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 403:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 404:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 409:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 422:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 500:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 502:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 503:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 504:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
            }
        
            throw $e;
        }
    }

    /**
     * Operation getVrboListingCancellationAsync
     *
     * Get the Vrbo cancellation policy
     *
     * @param  string $id Repull listing id (numeric string) of a listing linked to Vrbo. (required)
     * @param  string|null $vrbo_listing_number The Vrbo listing number to work on, when the listing is linked to more than one Vrbo unit. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['getVrboListingCancellation'] to see the possible values for this operation
     *
     * @throws InvalidArgumentException
     * @return PromiseInterface
     */
    public function getVrboListingCancellationAsync(
        string $id,
        ?string $vrbo_listing_number = null,
        string $contentType = self::contentTypes['getVrboListingCancellation'][0]
    ): PromiseInterface
    {
        return $this->getVrboListingCancellationAsyncWithHttpInfo($id, $vrbo_listing_number, $contentType)
            ->then(
                function ($response) {
                    return $response[0];
                }
            );
    }

    /**
     * Operation getVrboListingCancellationAsyncWithHttpInfo
     *
     * Get the Vrbo cancellation policy
     *
     * @param  string $id Repull listing id (numeric string) of a listing linked to Vrbo. (required)
     * @param  string|null $vrbo_listing_number The Vrbo listing number to work on, when the listing is linked to more than one Vrbo unit. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['getVrboListingCancellation'] to see the possible values for this operation
     *
     * @throws InvalidArgumentException
     * @return PromiseInterface
     */
    public function getVrboListingCancellationAsyncWithHttpInfo(
        string $id,
        ?string $vrbo_listing_number = null,
        string $contentType = self::contentTypes['getVrboListingCancellation'][0]
    ): PromiseInterface
    {
        $returnType = '\Repull\Model\GetVrboListingCancellation200Response';
        $request = $this->getVrboListingCancellationRequest($id, $vrbo_listing_number, $contentType);

        return $this->client
            ->sendAsync($request, $this->createHttpClientOption())
            ->then(
                function ($response) use ($returnType) {
                    if (in_array($returnType, ['\SplFileObject', '\Psr\Http\Message\StreamInterface'])) {
                        $content = $response->getBody(); //stream goes to serializer
                    } else {
                        $content = (string) $response->getBody();
                        if ($returnType !== 'string') {
                            $content = json_decode($content);
                        }
                    }

                    return [
                        ObjectSerializer::deserialize($content, $returnType, []),
                        $response->getStatusCode(),
                        $response->getHeaders()
                    ];
                },
                function ($exception) {
                    $response = $exception->getResponse();
                    $statusCode = $response->getStatusCode();
                    throw new ApiException(
                        sprintf(
                            '[%d] Error connecting to the API (%s)',
                            $statusCode,
                            $exception->getRequest()->getUri()
                        ),
                        $statusCode,
                        $response->getHeaders(),
                        (string) $response->getBody()
                    );
                }
            );
    }

    /**
     * Create request for operation 'getVrboListingCancellation'
     *
     * @param  string $id Repull listing id (numeric string) of a listing linked to Vrbo. (required)
     * @param  string|null $vrbo_listing_number The Vrbo listing number to work on, when the listing is linked to more than one Vrbo unit. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['getVrboListingCancellation'] to see the possible values for this operation
     *
     * @throws InvalidArgumentException
     * @return \GuzzleHttp\Psr7\Request
     */
    public function getVrboListingCancellationRequest(
        string $id,
        ?string $vrbo_listing_number = null,
        string $contentType = self::contentTypes['getVrboListingCancellation'][0]
    ): Request
    {

        // verify the required parameter 'id' is set
        if ($id === null || (is_array($id) && count($id) === 0)) {
            throw new InvalidArgumentException(
                'Missing the required parameter $id when calling getVrboListingCancellation'
            );
        }



        $resourcePath = '/v1/channels/vrbo/listings/{id}/cancellation';
        $formParams = [];
        $queryParams = [];
        $headerParams = [];
        $httpBody = '';
        $multipart = false;

        // query params
        $queryParams = array_merge($queryParams, ObjectSerializer::toQueryValue(
            $vrbo_listing_number,
            'vrboListingNumber', // param base name
            'string', // openApiType
            'form', // style
            true, // explode
            false, // required
            null // openApiFormat
        ) ?? []);


        // path params
        if ($id !== null) {
            $resourcePath = str_replace(
                '{id}',
                ObjectSerializer::toPathValue($id),
                $resourcePath
            );
        }


        $headers = $this->headerSelector->selectHeaders(
            ['application/json', ],
            $contentType,
            $multipart
        );

        // for model (json/xml)
        if (count($formParams) > 0) {
            if ($multipart) {
                $multipartContents = [];
                foreach ($formParams as $formParamName => $formParamValue) {
                    $formParamValueItems = is_array($formParamValue) ? $formParamValue : [$formParamValue];
                    foreach ($formParamValueItems as $formParamValueItem) {
                        $multipartContents[] = [
                            'name' => $formParamName,
                            'contents' => $formParamValueItem
                        ];
                    }
                }
                // for HTTP post (form)
                $httpBody = new MultipartStream($multipartContents);

            } elseif (stripos($headers['Content-Type'], 'application/json') !== false) {
                # if Content-Type contains "application/json", json_encode the form parameters
                $httpBody = \GuzzleHttp\Utils::jsonEncode($formParams);
            } else {
                // for HTTP post (form)
                $httpBody = ObjectSerializer::buildQuery($formParams);
            }
        }

        // this endpoint requires Bearer (API Key) authentication (access token)
        if (!empty($this->config->getAccessToken())) {
            $headers['Authorization'] = 'Bearer ' . $this->config->getAccessToken();
        }

        $defaultHeaders = [];
        if ($this->config->getUserAgent()) {
            $defaultHeaders['User-Agent'] = $this->config->getUserAgent();
        }

        $headers = array_merge(
            $defaultHeaders,
            $headerParams,
            $headers
        );

        $operationHost = $this->config->getHost();
        $query = ObjectSerializer::buildQuery($queryParams);
        return new Request(
            'GET',
            $operationHost . $resourcePath . ($query ? "?{$query}" : ''),
            $headers,
            $httpBody
        );
    }

    /**
     * Operation getVrboListingContent
     *
     * Get several Vrbo listing sections
     *
     * @param  string $id Repull listing id (numeric string) of a listing linked to Vrbo. (required)
     * @param  string $sections Comma-separated sections, or &#x60;all&#x60;: description, property, story, additional-info, translations, photos, amenities, rooms, location, house-rules, booking-settings, cancellation, fees, pricing, rates, payment-terms, protection, taxes, registration, welcome-guide, videos. (required)
     * @param  string|null $vrbo_listing_number The Vrbo listing number to work on, when the listing is linked to more than one Vrbo unit. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['getVrboListingContent'] to see the possible values for this operation
     *
     * @throws ApiException on non-2xx response or if the response body is not in the expected format
     * @throws InvalidArgumentException
     * @return \Repull\Model\GetVrboListingContent200Response|\Repull\Model\Error
     */
    public function getVrboListingContent(
        string $id,
        string $sections,
        ?string $vrbo_listing_number = null,
        string $contentType = self::contentTypes['getVrboListingContent'][0]
    ): \Repull\Model\GetVrboListingContent200Response|\Repull\Model\Error
    {
        list($response) = $this->getVrboListingContentWithHttpInfo($id, $sections, $vrbo_listing_number, $contentType);
        return $response;
    }

    /**
     * Operation getVrboListingContentWithHttpInfo
     *
     * Get several Vrbo listing sections
     *
     * @param  string $id Repull listing id (numeric string) of a listing linked to Vrbo. (required)
     * @param  string $sections Comma-separated sections, or &#x60;all&#x60;: description, property, story, additional-info, translations, photos, amenities, rooms, location, house-rules, booking-settings, cancellation, fees, pricing, rates, payment-terms, protection, taxes, registration, welcome-guide, videos. (required)
     * @param  string|null $vrbo_listing_number The Vrbo listing number to work on, when the listing is linked to more than one Vrbo unit. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['getVrboListingContent'] to see the possible values for this operation
     *
     * @throws ApiException on non-2xx response or if the response body is not in the expected format
     * @throws InvalidArgumentException
     * @return array of \Repull\Model\GetVrboListingContent200Response|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error, HTTP status code, HTTP response headers (array of strings)
     */
    public function getVrboListingContentWithHttpInfo(
        string $id,
        string $sections,
        ?string $vrbo_listing_number = null,
        string $contentType = self::contentTypes['getVrboListingContent'][0]
    ): array
    {
        $request = $this->getVrboListingContentRequest($id, $sections, $vrbo_listing_number, $contentType);

        try {
            $options = $this->createHttpClientOption();
            try {
                $response = $this->client->send($request, $options);
            } catch (RequestException $e) {
                throw new ApiException(
                    "[{$e->getCode()}] {$e->getMessage()}",
                    (int) $e->getCode(),
                    $e->getResponse() ? $e->getResponse()->getHeaders() : null,
                    $e->getResponse() ? (string) $e->getResponse()->getBody() : null
                );
            } catch (ConnectException $e) {
                throw new ApiException(
                    "[{$e->getCode()}] {$e->getMessage()}",
                    (int) $e->getCode(),
                    null,
                    null
                );
            }

            $statusCode = $response->getStatusCode();

            switch($statusCode) {
                case 200:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\GetVrboListingContent200Response',
                        $request,
                        $response,
                    );
                case 401:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 403:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 404:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 409:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 422:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 500:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 502:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 503:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 504:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
            }
            

            if ($statusCode < 200 || $statusCode > 299) {
                throw new ApiException(
                    sprintf(
                        '[%d] Error connecting to the API (%s)',
                        $statusCode,
                        (string) $request->getUri()
                    ),
                    $statusCode,
                    $response->getHeaders(),
                    (string) $response->getBody()
                );
            }

            return $this->handleResponseWithDataType(
                '\Repull\Model\GetVrboListingContent200Response',
                $request,
                $response,
            );
        } catch (ApiException $e) {
            switch ($e->getCode()) {
                case 200:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\GetVrboListingContent200Response',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 401:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 403:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 404:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 409:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 422:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 500:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 502:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 503:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 504:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
            }
        
            throw $e;
        }
    }

    /**
     * Operation getVrboListingContentAsync
     *
     * Get several Vrbo listing sections
     *
     * @param  string $id Repull listing id (numeric string) of a listing linked to Vrbo. (required)
     * @param  string $sections Comma-separated sections, or &#x60;all&#x60;: description, property, story, additional-info, translations, photos, amenities, rooms, location, house-rules, booking-settings, cancellation, fees, pricing, rates, payment-terms, protection, taxes, registration, welcome-guide, videos. (required)
     * @param  string|null $vrbo_listing_number The Vrbo listing number to work on, when the listing is linked to more than one Vrbo unit. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['getVrboListingContent'] to see the possible values for this operation
     *
     * @throws InvalidArgumentException
     * @return PromiseInterface
     */
    public function getVrboListingContentAsync(
        string $id,
        string $sections,
        ?string $vrbo_listing_number = null,
        string $contentType = self::contentTypes['getVrboListingContent'][0]
    ): PromiseInterface
    {
        return $this->getVrboListingContentAsyncWithHttpInfo($id, $sections, $vrbo_listing_number, $contentType)
            ->then(
                function ($response) {
                    return $response[0];
                }
            );
    }

    /**
     * Operation getVrboListingContentAsyncWithHttpInfo
     *
     * Get several Vrbo listing sections
     *
     * @param  string $id Repull listing id (numeric string) of a listing linked to Vrbo. (required)
     * @param  string $sections Comma-separated sections, or &#x60;all&#x60;: description, property, story, additional-info, translations, photos, amenities, rooms, location, house-rules, booking-settings, cancellation, fees, pricing, rates, payment-terms, protection, taxes, registration, welcome-guide, videos. (required)
     * @param  string|null $vrbo_listing_number The Vrbo listing number to work on, when the listing is linked to more than one Vrbo unit. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['getVrboListingContent'] to see the possible values for this operation
     *
     * @throws InvalidArgumentException
     * @return PromiseInterface
     */
    public function getVrboListingContentAsyncWithHttpInfo(
        string $id,
        string $sections,
        ?string $vrbo_listing_number = null,
        string $contentType = self::contentTypes['getVrboListingContent'][0]
    ): PromiseInterface
    {
        $returnType = '\Repull\Model\GetVrboListingContent200Response';
        $request = $this->getVrboListingContentRequest($id, $sections, $vrbo_listing_number, $contentType);

        return $this->client
            ->sendAsync($request, $this->createHttpClientOption())
            ->then(
                function ($response) use ($returnType) {
                    if (in_array($returnType, ['\SplFileObject', '\Psr\Http\Message\StreamInterface'])) {
                        $content = $response->getBody(); //stream goes to serializer
                    } else {
                        $content = (string) $response->getBody();
                        if ($returnType !== 'string') {
                            $content = json_decode($content);
                        }
                    }

                    return [
                        ObjectSerializer::deserialize($content, $returnType, []),
                        $response->getStatusCode(),
                        $response->getHeaders()
                    ];
                },
                function ($exception) {
                    $response = $exception->getResponse();
                    $statusCode = $response->getStatusCode();
                    throw new ApiException(
                        sprintf(
                            '[%d] Error connecting to the API (%s)',
                            $statusCode,
                            $exception->getRequest()->getUri()
                        ),
                        $statusCode,
                        $response->getHeaders(),
                        (string) $response->getBody()
                    );
                }
            );
    }

    /**
     * Create request for operation 'getVrboListingContent'
     *
     * @param  string $id Repull listing id (numeric string) of a listing linked to Vrbo. (required)
     * @param  string $sections Comma-separated sections, or &#x60;all&#x60;: description, property, story, additional-info, translations, photos, amenities, rooms, location, house-rules, booking-settings, cancellation, fees, pricing, rates, payment-terms, protection, taxes, registration, welcome-guide, videos. (required)
     * @param  string|null $vrbo_listing_number The Vrbo listing number to work on, when the listing is linked to more than one Vrbo unit. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['getVrboListingContent'] to see the possible values for this operation
     *
     * @throws InvalidArgumentException
     * @return \GuzzleHttp\Psr7\Request
     */
    public function getVrboListingContentRequest(
        string $id,
        string $sections,
        ?string $vrbo_listing_number = null,
        string $contentType = self::contentTypes['getVrboListingContent'][0]
    ): Request
    {

        // verify the required parameter 'id' is set
        if ($id === null || (is_array($id) && count($id) === 0)) {
            throw new InvalidArgumentException(
                'Missing the required parameter $id when calling getVrboListingContent'
            );
        }

        // verify the required parameter 'sections' is set
        if ($sections === null || (is_array($sections) && count($sections) === 0)) {
            throw new InvalidArgumentException(
                'Missing the required parameter $sections when calling getVrboListingContent'
            );
        }



        $resourcePath = '/v1/channels/vrbo/listings/{id}/content';
        $formParams = [];
        $queryParams = [];
        $headerParams = [];
        $httpBody = '';
        $multipart = false;

        // query params
        $queryParams = array_merge($queryParams, ObjectSerializer::toQueryValue(
            $sections,
            'sections', // param base name
            'string', // openApiType
            'form', // style
            true, // explode
            true, // required
            null // openApiFormat
        ) ?? []);
        // query params
        $queryParams = array_merge($queryParams, ObjectSerializer::toQueryValue(
            $vrbo_listing_number,
            'vrboListingNumber', // param base name
            'string', // openApiType
            'form', // style
            true, // explode
            false, // required
            null // openApiFormat
        ) ?? []);


        // path params
        if ($id !== null) {
            $resourcePath = str_replace(
                '{id}',
                ObjectSerializer::toPathValue($id),
                $resourcePath
            );
        }


        $headers = $this->headerSelector->selectHeaders(
            ['application/json', ],
            $contentType,
            $multipart
        );

        // for model (json/xml)
        if (count($formParams) > 0) {
            if ($multipart) {
                $multipartContents = [];
                foreach ($formParams as $formParamName => $formParamValue) {
                    $formParamValueItems = is_array($formParamValue) ? $formParamValue : [$formParamValue];
                    foreach ($formParamValueItems as $formParamValueItem) {
                        $multipartContents[] = [
                            'name' => $formParamName,
                            'contents' => $formParamValueItem
                        ];
                    }
                }
                // for HTTP post (form)
                $httpBody = new MultipartStream($multipartContents);

            } elseif (stripos($headers['Content-Type'], 'application/json') !== false) {
                # if Content-Type contains "application/json", json_encode the form parameters
                $httpBody = \GuzzleHttp\Utils::jsonEncode($formParams);
            } else {
                // for HTTP post (form)
                $httpBody = ObjectSerializer::buildQuery($formParams);
            }
        }

        // this endpoint requires Bearer (API Key) authentication (access token)
        if (!empty($this->config->getAccessToken())) {
            $headers['Authorization'] = 'Bearer ' . $this->config->getAccessToken();
        }

        $defaultHeaders = [];
        if ($this->config->getUserAgent()) {
            $defaultHeaders['User-Agent'] = $this->config->getUserAgent();
        }

        $headers = array_merge(
            $defaultHeaders,
            $headerParams,
            $headers
        );

        $operationHost = $this->config->getHost();
        $query = ObjectSerializer::buildQuery($queryParams);
        return new Request(
            'GET',
            $operationHost . $resourcePath . ($query ? "?{$query}" : ''),
            $headers,
            $httpBody
        );
    }

    /**
     * Operation getVrboListingDescription
     *
     * Get the Vrbo headline and description
     *
     * @param  string $id Repull listing id (numeric string) of a listing linked to Vrbo. (required)
     * @param  string|null $vrbo_listing_number The Vrbo listing number to work on, when the listing is linked to more than one Vrbo unit. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['getVrboListingDescription'] to see the possible values for this operation
     *
     * @throws ApiException on non-2xx response or if the response body is not in the expected format
     * @throws InvalidArgumentException
     * @return \Repull\Model\GetVrboListingDescription200Response|\Repull\Model\Error
     */
    public function getVrboListingDescription(
        string $id,
        ?string $vrbo_listing_number = null,
        string $contentType = self::contentTypes['getVrboListingDescription'][0]
    ): \Repull\Model\GetVrboListingDescription200Response|\Repull\Model\Error
    {
        list($response) = $this->getVrboListingDescriptionWithHttpInfo($id, $vrbo_listing_number, $contentType);
        return $response;
    }

    /**
     * Operation getVrboListingDescriptionWithHttpInfo
     *
     * Get the Vrbo headline and description
     *
     * @param  string $id Repull listing id (numeric string) of a listing linked to Vrbo. (required)
     * @param  string|null $vrbo_listing_number The Vrbo listing number to work on, when the listing is linked to more than one Vrbo unit. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['getVrboListingDescription'] to see the possible values for this operation
     *
     * @throws ApiException on non-2xx response or if the response body is not in the expected format
     * @throws InvalidArgumentException
     * @return array of \Repull\Model\GetVrboListingDescription200Response|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error, HTTP status code, HTTP response headers (array of strings)
     */
    public function getVrboListingDescriptionWithHttpInfo(
        string $id,
        ?string $vrbo_listing_number = null,
        string $contentType = self::contentTypes['getVrboListingDescription'][0]
    ): array
    {
        $request = $this->getVrboListingDescriptionRequest($id, $vrbo_listing_number, $contentType);

        try {
            $options = $this->createHttpClientOption();
            try {
                $response = $this->client->send($request, $options);
            } catch (RequestException $e) {
                throw new ApiException(
                    "[{$e->getCode()}] {$e->getMessage()}",
                    (int) $e->getCode(),
                    $e->getResponse() ? $e->getResponse()->getHeaders() : null,
                    $e->getResponse() ? (string) $e->getResponse()->getBody() : null
                );
            } catch (ConnectException $e) {
                throw new ApiException(
                    "[{$e->getCode()}] {$e->getMessage()}",
                    (int) $e->getCode(),
                    null,
                    null
                );
            }

            $statusCode = $response->getStatusCode();

            switch($statusCode) {
                case 200:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\GetVrboListingDescription200Response',
                        $request,
                        $response,
                    );
                case 401:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 403:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 404:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 409:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 422:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 500:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 502:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 503:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 504:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
            }
            

            if ($statusCode < 200 || $statusCode > 299) {
                throw new ApiException(
                    sprintf(
                        '[%d] Error connecting to the API (%s)',
                        $statusCode,
                        (string) $request->getUri()
                    ),
                    $statusCode,
                    $response->getHeaders(),
                    (string) $response->getBody()
                );
            }

            return $this->handleResponseWithDataType(
                '\Repull\Model\GetVrboListingDescription200Response',
                $request,
                $response,
            );
        } catch (ApiException $e) {
            switch ($e->getCode()) {
                case 200:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\GetVrboListingDescription200Response',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 401:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 403:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 404:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 409:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 422:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 500:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 502:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 503:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 504:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
            }
        
            throw $e;
        }
    }

    /**
     * Operation getVrboListingDescriptionAsync
     *
     * Get the Vrbo headline and description
     *
     * @param  string $id Repull listing id (numeric string) of a listing linked to Vrbo. (required)
     * @param  string|null $vrbo_listing_number The Vrbo listing number to work on, when the listing is linked to more than one Vrbo unit. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['getVrboListingDescription'] to see the possible values for this operation
     *
     * @throws InvalidArgumentException
     * @return PromiseInterface
     */
    public function getVrboListingDescriptionAsync(
        string $id,
        ?string $vrbo_listing_number = null,
        string $contentType = self::contentTypes['getVrboListingDescription'][0]
    ): PromiseInterface
    {
        return $this->getVrboListingDescriptionAsyncWithHttpInfo($id, $vrbo_listing_number, $contentType)
            ->then(
                function ($response) {
                    return $response[0];
                }
            );
    }

    /**
     * Operation getVrboListingDescriptionAsyncWithHttpInfo
     *
     * Get the Vrbo headline and description
     *
     * @param  string $id Repull listing id (numeric string) of a listing linked to Vrbo. (required)
     * @param  string|null $vrbo_listing_number The Vrbo listing number to work on, when the listing is linked to more than one Vrbo unit. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['getVrboListingDescription'] to see the possible values for this operation
     *
     * @throws InvalidArgumentException
     * @return PromiseInterface
     */
    public function getVrboListingDescriptionAsyncWithHttpInfo(
        string $id,
        ?string $vrbo_listing_number = null,
        string $contentType = self::contentTypes['getVrboListingDescription'][0]
    ): PromiseInterface
    {
        $returnType = '\Repull\Model\GetVrboListingDescription200Response';
        $request = $this->getVrboListingDescriptionRequest($id, $vrbo_listing_number, $contentType);

        return $this->client
            ->sendAsync($request, $this->createHttpClientOption())
            ->then(
                function ($response) use ($returnType) {
                    if (in_array($returnType, ['\SplFileObject', '\Psr\Http\Message\StreamInterface'])) {
                        $content = $response->getBody(); //stream goes to serializer
                    } else {
                        $content = (string) $response->getBody();
                        if ($returnType !== 'string') {
                            $content = json_decode($content);
                        }
                    }

                    return [
                        ObjectSerializer::deserialize($content, $returnType, []),
                        $response->getStatusCode(),
                        $response->getHeaders()
                    ];
                },
                function ($exception) {
                    $response = $exception->getResponse();
                    $statusCode = $response->getStatusCode();
                    throw new ApiException(
                        sprintf(
                            '[%d] Error connecting to the API (%s)',
                            $statusCode,
                            $exception->getRequest()->getUri()
                        ),
                        $statusCode,
                        $response->getHeaders(),
                        (string) $response->getBody()
                    );
                }
            );
    }

    /**
     * Create request for operation 'getVrboListingDescription'
     *
     * @param  string $id Repull listing id (numeric string) of a listing linked to Vrbo. (required)
     * @param  string|null $vrbo_listing_number The Vrbo listing number to work on, when the listing is linked to more than one Vrbo unit. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['getVrboListingDescription'] to see the possible values for this operation
     *
     * @throws InvalidArgumentException
     * @return \GuzzleHttp\Psr7\Request
     */
    public function getVrboListingDescriptionRequest(
        string $id,
        ?string $vrbo_listing_number = null,
        string $contentType = self::contentTypes['getVrboListingDescription'][0]
    ): Request
    {

        // verify the required parameter 'id' is set
        if ($id === null || (is_array($id) && count($id) === 0)) {
            throw new InvalidArgumentException(
                'Missing the required parameter $id when calling getVrboListingDescription'
            );
        }



        $resourcePath = '/v1/channels/vrbo/listings/{id}/description';
        $formParams = [];
        $queryParams = [];
        $headerParams = [];
        $httpBody = '';
        $multipart = false;

        // query params
        $queryParams = array_merge($queryParams, ObjectSerializer::toQueryValue(
            $vrbo_listing_number,
            'vrboListingNumber', // param base name
            'string', // openApiType
            'form', // style
            true, // explode
            false, // required
            null // openApiFormat
        ) ?? []);


        // path params
        if ($id !== null) {
            $resourcePath = str_replace(
                '{id}',
                ObjectSerializer::toPathValue($id),
                $resourcePath
            );
        }


        $headers = $this->headerSelector->selectHeaders(
            ['application/json', ],
            $contentType,
            $multipart
        );

        // for model (json/xml)
        if (count($formParams) > 0) {
            if ($multipart) {
                $multipartContents = [];
                foreach ($formParams as $formParamName => $formParamValue) {
                    $formParamValueItems = is_array($formParamValue) ? $formParamValue : [$formParamValue];
                    foreach ($formParamValueItems as $formParamValueItem) {
                        $multipartContents[] = [
                            'name' => $formParamName,
                            'contents' => $formParamValueItem
                        ];
                    }
                }
                // for HTTP post (form)
                $httpBody = new MultipartStream($multipartContents);

            } elseif (stripos($headers['Content-Type'], 'application/json') !== false) {
                # if Content-Type contains "application/json", json_encode the form parameters
                $httpBody = \GuzzleHttp\Utils::jsonEncode($formParams);
            } else {
                // for HTTP post (form)
                $httpBody = ObjectSerializer::buildQuery($formParams);
            }
        }

        // this endpoint requires Bearer (API Key) authentication (access token)
        if (!empty($this->config->getAccessToken())) {
            $headers['Authorization'] = 'Bearer ' . $this->config->getAccessToken();
        }

        $defaultHeaders = [];
        if ($this->config->getUserAgent()) {
            $defaultHeaders['User-Agent'] = $this->config->getUserAgent();
        }

        $headers = array_merge(
            $defaultHeaders,
            $headerParams,
            $headers
        );

        $operationHost = $this->config->getHost();
        $query = ObjectSerializer::buildQuery($queryParams);
        return new Request(
            'GET',
            $operationHost . $resourcePath . ($query ? "?{$query}" : ''),
            $headers,
            $httpBody
        );
    }

    /**
     * Operation getVrboListingFees
     *
     * Get the Vrbo fees
     *
     * @param  string $id Repull listing id (numeric string) of a listing linked to Vrbo. (required)
     * @param  string|null $vrbo_listing_number The Vrbo listing number to work on, when the listing is linked to more than one Vrbo unit. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['getVrboListingFees'] to see the possible values for this operation
     *
     * @throws ApiException on non-2xx response or if the response body is not in the expected format
     * @throws InvalidArgumentException
     * @return \Repull\Model\GetVrboListingFees200Response|\Repull\Model\Error
     */
    public function getVrboListingFees(
        string $id,
        ?string $vrbo_listing_number = null,
        string $contentType = self::contentTypes['getVrboListingFees'][0]
    ): \Repull\Model\GetVrboListingFees200Response|\Repull\Model\Error
    {
        list($response) = $this->getVrboListingFeesWithHttpInfo($id, $vrbo_listing_number, $contentType);
        return $response;
    }

    /**
     * Operation getVrboListingFeesWithHttpInfo
     *
     * Get the Vrbo fees
     *
     * @param  string $id Repull listing id (numeric string) of a listing linked to Vrbo. (required)
     * @param  string|null $vrbo_listing_number The Vrbo listing number to work on, when the listing is linked to more than one Vrbo unit. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['getVrboListingFees'] to see the possible values for this operation
     *
     * @throws ApiException on non-2xx response or if the response body is not in the expected format
     * @throws InvalidArgumentException
     * @return array of \Repull\Model\GetVrboListingFees200Response|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error, HTTP status code, HTTP response headers (array of strings)
     */
    public function getVrboListingFeesWithHttpInfo(
        string $id,
        ?string $vrbo_listing_number = null,
        string $contentType = self::contentTypes['getVrboListingFees'][0]
    ): array
    {
        $request = $this->getVrboListingFeesRequest($id, $vrbo_listing_number, $contentType);

        try {
            $options = $this->createHttpClientOption();
            try {
                $response = $this->client->send($request, $options);
            } catch (RequestException $e) {
                throw new ApiException(
                    "[{$e->getCode()}] {$e->getMessage()}",
                    (int) $e->getCode(),
                    $e->getResponse() ? $e->getResponse()->getHeaders() : null,
                    $e->getResponse() ? (string) $e->getResponse()->getBody() : null
                );
            } catch (ConnectException $e) {
                throw new ApiException(
                    "[{$e->getCode()}] {$e->getMessage()}",
                    (int) $e->getCode(),
                    null,
                    null
                );
            }

            $statusCode = $response->getStatusCode();

            switch($statusCode) {
                case 200:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\GetVrboListingFees200Response',
                        $request,
                        $response,
                    );
                case 401:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 403:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 404:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 409:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 422:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 500:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 502:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 503:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 504:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
            }
            

            if ($statusCode < 200 || $statusCode > 299) {
                throw new ApiException(
                    sprintf(
                        '[%d] Error connecting to the API (%s)',
                        $statusCode,
                        (string) $request->getUri()
                    ),
                    $statusCode,
                    $response->getHeaders(),
                    (string) $response->getBody()
                );
            }

            return $this->handleResponseWithDataType(
                '\Repull\Model\GetVrboListingFees200Response',
                $request,
                $response,
            );
        } catch (ApiException $e) {
            switch ($e->getCode()) {
                case 200:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\GetVrboListingFees200Response',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 401:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 403:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 404:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 409:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 422:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 500:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 502:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 503:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 504:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
            }
        
            throw $e;
        }
    }

    /**
     * Operation getVrboListingFeesAsync
     *
     * Get the Vrbo fees
     *
     * @param  string $id Repull listing id (numeric string) of a listing linked to Vrbo. (required)
     * @param  string|null $vrbo_listing_number The Vrbo listing number to work on, when the listing is linked to more than one Vrbo unit. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['getVrboListingFees'] to see the possible values for this operation
     *
     * @throws InvalidArgumentException
     * @return PromiseInterface
     */
    public function getVrboListingFeesAsync(
        string $id,
        ?string $vrbo_listing_number = null,
        string $contentType = self::contentTypes['getVrboListingFees'][0]
    ): PromiseInterface
    {
        return $this->getVrboListingFeesAsyncWithHttpInfo($id, $vrbo_listing_number, $contentType)
            ->then(
                function ($response) {
                    return $response[0];
                }
            );
    }

    /**
     * Operation getVrboListingFeesAsyncWithHttpInfo
     *
     * Get the Vrbo fees
     *
     * @param  string $id Repull listing id (numeric string) of a listing linked to Vrbo. (required)
     * @param  string|null $vrbo_listing_number The Vrbo listing number to work on, when the listing is linked to more than one Vrbo unit. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['getVrboListingFees'] to see the possible values for this operation
     *
     * @throws InvalidArgumentException
     * @return PromiseInterface
     */
    public function getVrboListingFeesAsyncWithHttpInfo(
        string $id,
        ?string $vrbo_listing_number = null,
        string $contentType = self::contentTypes['getVrboListingFees'][0]
    ): PromiseInterface
    {
        $returnType = '\Repull\Model\GetVrboListingFees200Response';
        $request = $this->getVrboListingFeesRequest($id, $vrbo_listing_number, $contentType);

        return $this->client
            ->sendAsync($request, $this->createHttpClientOption())
            ->then(
                function ($response) use ($returnType) {
                    if (in_array($returnType, ['\SplFileObject', '\Psr\Http\Message\StreamInterface'])) {
                        $content = $response->getBody(); //stream goes to serializer
                    } else {
                        $content = (string) $response->getBody();
                        if ($returnType !== 'string') {
                            $content = json_decode($content);
                        }
                    }

                    return [
                        ObjectSerializer::deserialize($content, $returnType, []),
                        $response->getStatusCode(),
                        $response->getHeaders()
                    ];
                },
                function ($exception) {
                    $response = $exception->getResponse();
                    $statusCode = $response->getStatusCode();
                    throw new ApiException(
                        sprintf(
                            '[%d] Error connecting to the API (%s)',
                            $statusCode,
                            $exception->getRequest()->getUri()
                        ),
                        $statusCode,
                        $response->getHeaders(),
                        (string) $response->getBody()
                    );
                }
            );
    }

    /**
     * Create request for operation 'getVrboListingFees'
     *
     * @param  string $id Repull listing id (numeric string) of a listing linked to Vrbo. (required)
     * @param  string|null $vrbo_listing_number The Vrbo listing number to work on, when the listing is linked to more than one Vrbo unit. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['getVrboListingFees'] to see the possible values for this operation
     *
     * @throws InvalidArgumentException
     * @return \GuzzleHttp\Psr7\Request
     */
    public function getVrboListingFeesRequest(
        string $id,
        ?string $vrbo_listing_number = null,
        string $contentType = self::contentTypes['getVrboListingFees'][0]
    ): Request
    {

        // verify the required parameter 'id' is set
        if ($id === null || (is_array($id) && count($id) === 0)) {
            throw new InvalidArgumentException(
                'Missing the required parameter $id when calling getVrboListingFees'
            );
        }



        $resourcePath = '/v1/channels/vrbo/listings/{id}/fees';
        $formParams = [];
        $queryParams = [];
        $headerParams = [];
        $httpBody = '';
        $multipart = false;

        // query params
        $queryParams = array_merge($queryParams, ObjectSerializer::toQueryValue(
            $vrbo_listing_number,
            'vrboListingNumber', // param base name
            'string', // openApiType
            'form', // style
            true, // explode
            false, // required
            null // openApiFormat
        ) ?? []);


        // path params
        if ($id !== null) {
            $resourcePath = str_replace(
                '{id}',
                ObjectSerializer::toPathValue($id),
                $resourcePath
            );
        }


        $headers = $this->headerSelector->selectHeaders(
            ['application/json', ],
            $contentType,
            $multipart
        );

        // for model (json/xml)
        if (count($formParams) > 0) {
            if ($multipart) {
                $multipartContents = [];
                foreach ($formParams as $formParamName => $formParamValue) {
                    $formParamValueItems = is_array($formParamValue) ? $formParamValue : [$formParamValue];
                    foreach ($formParamValueItems as $formParamValueItem) {
                        $multipartContents[] = [
                            'name' => $formParamName,
                            'contents' => $formParamValueItem
                        ];
                    }
                }
                // for HTTP post (form)
                $httpBody = new MultipartStream($multipartContents);

            } elseif (stripos($headers['Content-Type'], 'application/json') !== false) {
                # if Content-Type contains "application/json", json_encode the form parameters
                $httpBody = \GuzzleHttp\Utils::jsonEncode($formParams);
            } else {
                // for HTTP post (form)
                $httpBody = ObjectSerializer::buildQuery($formParams);
            }
        }

        // this endpoint requires Bearer (API Key) authentication (access token)
        if (!empty($this->config->getAccessToken())) {
            $headers['Authorization'] = 'Bearer ' . $this->config->getAccessToken();
        }

        $defaultHeaders = [];
        if ($this->config->getUserAgent()) {
            $defaultHeaders['User-Agent'] = $this->config->getUserAgent();
        }

        $headers = array_merge(
            $defaultHeaders,
            $headerParams,
            $headers
        );

        $operationHost = $this->config->getHost();
        $query = ObjectSerializer::buildQuery($queryParams);
        return new Request(
            'GET',
            $operationHost . $resourcePath . ($query ? "?{$query}" : ''),
            $headers,
            $httpBody
        );
    }

    /**
     * Operation getVrboListingHouseRules
     *
     * Get the Vrbo house rules
     *
     * @param  string $id Repull listing id (numeric string) of a listing linked to Vrbo. (required)
     * @param  string|null $vrbo_listing_number The Vrbo listing number to work on, when the listing is linked to more than one Vrbo unit. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['getVrboListingHouseRules'] to see the possible values for this operation
     *
     * @throws ApiException on non-2xx response or if the response body is not in the expected format
     * @throws InvalidArgumentException
     * @return \Repull\Model\GetVrboListingHouseRules200Response|\Repull\Model\Error
     */
    public function getVrboListingHouseRules(
        string $id,
        ?string $vrbo_listing_number = null,
        string $contentType = self::contentTypes['getVrboListingHouseRules'][0]
    ): \Repull\Model\GetVrboListingHouseRules200Response|\Repull\Model\Error
    {
        list($response) = $this->getVrboListingHouseRulesWithHttpInfo($id, $vrbo_listing_number, $contentType);
        return $response;
    }

    /**
     * Operation getVrboListingHouseRulesWithHttpInfo
     *
     * Get the Vrbo house rules
     *
     * @param  string $id Repull listing id (numeric string) of a listing linked to Vrbo. (required)
     * @param  string|null $vrbo_listing_number The Vrbo listing number to work on, when the listing is linked to more than one Vrbo unit. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['getVrboListingHouseRules'] to see the possible values for this operation
     *
     * @throws ApiException on non-2xx response or if the response body is not in the expected format
     * @throws InvalidArgumentException
     * @return array of \Repull\Model\GetVrboListingHouseRules200Response|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error, HTTP status code, HTTP response headers (array of strings)
     */
    public function getVrboListingHouseRulesWithHttpInfo(
        string $id,
        ?string $vrbo_listing_number = null,
        string $contentType = self::contentTypes['getVrboListingHouseRules'][0]
    ): array
    {
        $request = $this->getVrboListingHouseRulesRequest($id, $vrbo_listing_number, $contentType);

        try {
            $options = $this->createHttpClientOption();
            try {
                $response = $this->client->send($request, $options);
            } catch (RequestException $e) {
                throw new ApiException(
                    "[{$e->getCode()}] {$e->getMessage()}",
                    (int) $e->getCode(),
                    $e->getResponse() ? $e->getResponse()->getHeaders() : null,
                    $e->getResponse() ? (string) $e->getResponse()->getBody() : null
                );
            } catch (ConnectException $e) {
                throw new ApiException(
                    "[{$e->getCode()}] {$e->getMessage()}",
                    (int) $e->getCode(),
                    null,
                    null
                );
            }

            $statusCode = $response->getStatusCode();

            switch($statusCode) {
                case 200:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\GetVrboListingHouseRules200Response',
                        $request,
                        $response,
                    );
                case 401:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 403:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 404:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 409:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 422:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 500:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 502:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 503:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 504:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
            }
            

            if ($statusCode < 200 || $statusCode > 299) {
                throw new ApiException(
                    sprintf(
                        '[%d] Error connecting to the API (%s)',
                        $statusCode,
                        (string) $request->getUri()
                    ),
                    $statusCode,
                    $response->getHeaders(),
                    (string) $response->getBody()
                );
            }

            return $this->handleResponseWithDataType(
                '\Repull\Model\GetVrboListingHouseRules200Response',
                $request,
                $response,
            );
        } catch (ApiException $e) {
            switch ($e->getCode()) {
                case 200:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\GetVrboListingHouseRules200Response',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 401:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 403:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 404:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 409:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 422:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 500:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 502:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 503:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 504:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
            }
        
            throw $e;
        }
    }

    /**
     * Operation getVrboListingHouseRulesAsync
     *
     * Get the Vrbo house rules
     *
     * @param  string $id Repull listing id (numeric string) of a listing linked to Vrbo. (required)
     * @param  string|null $vrbo_listing_number The Vrbo listing number to work on, when the listing is linked to more than one Vrbo unit. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['getVrboListingHouseRules'] to see the possible values for this operation
     *
     * @throws InvalidArgumentException
     * @return PromiseInterface
     */
    public function getVrboListingHouseRulesAsync(
        string $id,
        ?string $vrbo_listing_number = null,
        string $contentType = self::contentTypes['getVrboListingHouseRules'][0]
    ): PromiseInterface
    {
        return $this->getVrboListingHouseRulesAsyncWithHttpInfo($id, $vrbo_listing_number, $contentType)
            ->then(
                function ($response) {
                    return $response[0];
                }
            );
    }

    /**
     * Operation getVrboListingHouseRulesAsyncWithHttpInfo
     *
     * Get the Vrbo house rules
     *
     * @param  string $id Repull listing id (numeric string) of a listing linked to Vrbo. (required)
     * @param  string|null $vrbo_listing_number The Vrbo listing number to work on, when the listing is linked to more than one Vrbo unit. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['getVrboListingHouseRules'] to see the possible values for this operation
     *
     * @throws InvalidArgumentException
     * @return PromiseInterface
     */
    public function getVrboListingHouseRulesAsyncWithHttpInfo(
        string $id,
        ?string $vrbo_listing_number = null,
        string $contentType = self::contentTypes['getVrboListingHouseRules'][0]
    ): PromiseInterface
    {
        $returnType = '\Repull\Model\GetVrboListingHouseRules200Response';
        $request = $this->getVrboListingHouseRulesRequest($id, $vrbo_listing_number, $contentType);

        return $this->client
            ->sendAsync($request, $this->createHttpClientOption())
            ->then(
                function ($response) use ($returnType) {
                    if (in_array($returnType, ['\SplFileObject', '\Psr\Http\Message\StreamInterface'])) {
                        $content = $response->getBody(); //stream goes to serializer
                    } else {
                        $content = (string) $response->getBody();
                        if ($returnType !== 'string') {
                            $content = json_decode($content);
                        }
                    }

                    return [
                        ObjectSerializer::deserialize($content, $returnType, []),
                        $response->getStatusCode(),
                        $response->getHeaders()
                    ];
                },
                function ($exception) {
                    $response = $exception->getResponse();
                    $statusCode = $response->getStatusCode();
                    throw new ApiException(
                        sprintf(
                            '[%d] Error connecting to the API (%s)',
                            $statusCode,
                            $exception->getRequest()->getUri()
                        ),
                        $statusCode,
                        $response->getHeaders(),
                        (string) $response->getBody()
                    );
                }
            );
    }

    /**
     * Create request for operation 'getVrboListingHouseRules'
     *
     * @param  string $id Repull listing id (numeric string) of a listing linked to Vrbo. (required)
     * @param  string|null $vrbo_listing_number The Vrbo listing number to work on, when the listing is linked to more than one Vrbo unit. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['getVrboListingHouseRules'] to see the possible values for this operation
     *
     * @throws InvalidArgumentException
     * @return \GuzzleHttp\Psr7\Request
     */
    public function getVrboListingHouseRulesRequest(
        string $id,
        ?string $vrbo_listing_number = null,
        string $contentType = self::contentTypes['getVrboListingHouseRules'][0]
    ): Request
    {

        // verify the required parameter 'id' is set
        if ($id === null || (is_array($id) && count($id) === 0)) {
            throw new InvalidArgumentException(
                'Missing the required parameter $id when calling getVrboListingHouseRules'
            );
        }



        $resourcePath = '/v1/channels/vrbo/listings/{id}/house-rules';
        $formParams = [];
        $queryParams = [];
        $headerParams = [];
        $httpBody = '';
        $multipart = false;

        // query params
        $queryParams = array_merge($queryParams, ObjectSerializer::toQueryValue(
            $vrbo_listing_number,
            'vrboListingNumber', // param base name
            'string', // openApiType
            'form', // style
            true, // explode
            false, // required
            null // openApiFormat
        ) ?? []);


        // path params
        if ($id !== null) {
            $resourcePath = str_replace(
                '{id}',
                ObjectSerializer::toPathValue($id),
                $resourcePath
            );
        }


        $headers = $this->headerSelector->selectHeaders(
            ['application/json', ],
            $contentType,
            $multipart
        );

        // for model (json/xml)
        if (count($formParams) > 0) {
            if ($multipart) {
                $multipartContents = [];
                foreach ($formParams as $formParamName => $formParamValue) {
                    $formParamValueItems = is_array($formParamValue) ? $formParamValue : [$formParamValue];
                    foreach ($formParamValueItems as $formParamValueItem) {
                        $multipartContents[] = [
                            'name' => $formParamName,
                            'contents' => $formParamValueItem
                        ];
                    }
                }
                // for HTTP post (form)
                $httpBody = new MultipartStream($multipartContents);

            } elseif (stripos($headers['Content-Type'], 'application/json') !== false) {
                # if Content-Type contains "application/json", json_encode the form parameters
                $httpBody = \GuzzleHttp\Utils::jsonEncode($formParams);
            } else {
                // for HTTP post (form)
                $httpBody = ObjectSerializer::buildQuery($formParams);
            }
        }

        // this endpoint requires Bearer (API Key) authentication (access token)
        if (!empty($this->config->getAccessToken())) {
            $headers['Authorization'] = 'Bearer ' . $this->config->getAccessToken();
        }

        $defaultHeaders = [];
        if ($this->config->getUserAgent()) {
            $defaultHeaders['User-Agent'] = $this->config->getUserAgent();
        }

        $headers = array_merge(
            $defaultHeaders,
            $headerParams,
            $headers
        );

        $operationHost = $this->config->getHost();
        $query = ObjectSerializer::buildQuery($queryParams);
        return new Request(
            'GET',
            $operationHost . $resourcePath . ($query ? "?{$query}" : ''),
            $headers,
            $httpBody
        );
    }

    /**
     * Operation getVrboListingLocation
     *
     * Get the Vrbo map setting
     *
     * @param  string $id Repull listing id (numeric string) of a listing linked to Vrbo. (required)
     * @param  string|null $vrbo_listing_number The Vrbo listing number to work on, when the listing is linked to more than one Vrbo unit. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['getVrboListingLocation'] to see the possible values for this operation
     *
     * @throws ApiException on non-2xx response or if the response body is not in the expected format
     * @throws InvalidArgumentException
     * @return \Repull\Model\GetVrboListingLocation200Response|\Repull\Model\Error
     */
    public function getVrboListingLocation(
        string $id,
        ?string $vrbo_listing_number = null,
        string $contentType = self::contentTypes['getVrboListingLocation'][0]
    ): \Repull\Model\GetVrboListingLocation200Response|\Repull\Model\Error
    {
        list($response) = $this->getVrboListingLocationWithHttpInfo($id, $vrbo_listing_number, $contentType);
        return $response;
    }

    /**
     * Operation getVrboListingLocationWithHttpInfo
     *
     * Get the Vrbo map setting
     *
     * @param  string $id Repull listing id (numeric string) of a listing linked to Vrbo. (required)
     * @param  string|null $vrbo_listing_number The Vrbo listing number to work on, when the listing is linked to more than one Vrbo unit. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['getVrboListingLocation'] to see the possible values for this operation
     *
     * @throws ApiException on non-2xx response or if the response body is not in the expected format
     * @throws InvalidArgumentException
     * @return array of \Repull\Model\GetVrboListingLocation200Response|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error, HTTP status code, HTTP response headers (array of strings)
     */
    public function getVrboListingLocationWithHttpInfo(
        string $id,
        ?string $vrbo_listing_number = null,
        string $contentType = self::contentTypes['getVrboListingLocation'][0]
    ): array
    {
        $request = $this->getVrboListingLocationRequest($id, $vrbo_listing_number, $contentType);

        try {
            $options = $this->createHttpClientOption();
            try {
                $response = $this->client->send($request, $options);
            } catch (RequestException $e) {
                throw new ApiException(
                    "[{$e->getCode()}] {$e->getMessage()}",
                    (int) $e->getCode(),
                    $e->getResponse() ? $e->getResponse()->getHeaders() : null,
                    $e->getResponse() ? (string) $e->getResponse()->getBody() : null
                );
            } catch (ConnectException $e) {
                throw new ApiException(
                    "[{$e->getCode()}] {$e->getMessage()}",
                    (int) $e->getCode(),
                    null,
                    null
                );
            }

            $statusCode = $response->getStatusCode();

            switch($statusCode) {
                case 200:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\GetVrboListingLocation200Response',
                        $request,
                        $response,
                    );
                case 401:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 403:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 404:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 409:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 422:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 500:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 502:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 503:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 504:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
            }
            

            if ($statusCode < 200 || $statusCode > 299) {
                throw new ApiException(
                    sprintf(
                        '[%d] Error connecting to the API (%s)',
                        $statusCode,
                        (string) $request->getUri()
                    ),
                    $statusCode,
                    $response->getHeaders(),
                    (string) $response->getBody()
                );
            }

            return $this->handleResponseWithDataType(
                '\Repull\Model\GetVrboListingLocation200Response',
                $request,
                $response,
            );
        } catch (ApiException $e) {
            switch ($e->getCode()) {
                case 200:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\GetVrboListingLocation200Response',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 401:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 403:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 404:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 409:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 422:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 500:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 502:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 503:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 504:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
            }
        
            throw $e;
        }
    }

    /**
     * Operation getVrboListingLocationAsync
     *
     * Get the Vrbo map setting
     *
     * @param  string $id Repull listing id (numeric string) of a listing linked to Vrbo. (required)
     * @param  string|null $vrbo_listing_number The Vrbo listing number to work on, when the listing is linked to more than one Vrbo unit. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['getVrboListingLocation'] to see the possible values for this operation
     *
     * @throws InvalidArgumentException
     * @return PromiseInterface
     */
    public function getVrboListingLocationAsync(
        string $id,
        ?string $vrbo_listing_number = null,
        string $contentType = self::contentTypes['getVrboListingLocation'][0]
    ): PromiseInterface
    {
        return $this->getVrboListingLocationAsyncWithHttpInfo($id, $vrbo_listing_number, $contentType)
            ->then(
                function ($response) {
                    return $response[0];
                }
            );
    }

    /**
     * Operation getVrboListingLocationAsyncWithHttpInfo
     *
     * Get the Vrbo map setting
     *
     * @param  string $id Repull listing id (numeric string) of a listing linked to Vrbo. (required)
     * @param  string|null $vrbo_listing_number The Vrbo listing number to work on, when the listing is linked to more than one Vrbo unit. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['getVrboListingLocation'] to see the possible values for this operation
     *
     * @throws InvalidArgumentException
     * @return PromiseInterface
     */
    public function getVrboListingLocationAsyncWithHttpInfo(
        string $id,
        ?string $vrbo_listing_number = null,
        string $contentType = self::contentTypes['getVrboListingLocation'][0]
    ): PromiseInterface
    {
        $returnType = '\Repull\Model\GetVrboListingLocation200Response';
        $request = $this->getVrboListingLocationRequest($id, $vrbo_listing_number, $contentType);

        return $this->client
            ->sendAsync($request, $this->createHttpClientOption())
            ->then(
                function ($response) use ($returnType) {
                    if (in_array($returnType, ['\SplFileObject', '\Psr\Http\Message\StreamInterface'])) {
                        $content = $response->getBody(); //stream goes to serializer
                    } else {
                        $content = (string) $response->getBody();
                        if ($returnType !== 'string') {
                            $content = json_decode($content);
                        }
                    }

                    return [
                        ObjectSerializer::deserialize($content, $returnType, []),
                        $response->getStatusCode(),
                        $response->getHeaders()
                    ];
                },
                function ($exception) {
                    $response = $exception->getResponse();
                    $statusCode = $response->getStatusCode();
                    throw new ApiException(
                        sprintf(
                            '[%d] Error connecting to the API (%s)',
                            $statusCode,
                            $exception->getRequest()->getUri()
                        ),
                        $statusCode,
                        $response->getHeaders(),
                        (string) $response->getBody()
                    );
                }
            );
    }

    /**
     * Create request for operation 'getVrboListingLocation'
     *
     * @param  string $id Repull listing id (numeric string) of a listing linked to Vrbo. (required)
     * @param  string|null $vrbo_listing_number The Vrbo listing number to work on, when the listing is linked to more than one Vrbo unit. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['getVrboListingLocation'] to see the possible values for this operation
     *
     * @throws InvalidArgumentException
     * @return \GuzzleHttp\Psr7\Request
     */
    public function getVrboListingLocationRequest(
        string $id,
        ?string $vrbo_listing_number = null,
        string $contentType = self::contentTypes['getVrboListingLocation'][0]
    ): Request
    {

        // verify the required parameter 'id' is set
        if ($id === null || (is_array($id) && count($id) === 0)) {
            throw new InvalidArgumentException(
                'Missing the required parameter $id when calling getVrboListingLocation'
            );
        }



        $resourcePath = '/v1/channels/vrbo/listings/{id}/location';
        $formParams = [];
        $queryParams = [];
        $headerParams = [];
        $httpBody = '';
        $multipart = false;

        // query params
        $queryParams = array_merge($queryParams, ObjectSerializer::toQueryValue(
            $vrbo_listing_number,
            'vrboListingNumber', // param base name
            'string', // openApiType
            'form', // style
            true, // explode
            false, // required
            null // openApiFormat
        ) ?? []);


        // path params
        if ($id !== null) {
            $resourcePath = str_replace(
                '{id}',
                ObjectSerializer::toPathValue($id),
                $resourcePath
            );
        }


        $headers = $this->headerSelector->selectHeaders(
            ['application/json', ],
            $contentType,
            $multipart
        );

        // for model (json/xml)
        if (count($formParams) > 0) {
            if ($multipart) {
                $multipartContents = [];
                foreach ($formParams as $formParamName => $formParamValue) {
                    $formParamValueItems = is_array($formParamValue) ? $formParamValue : [$formParamValue];
                    foreach ($formParamValueItems as $formParamValueItem) {
                        $multipartContents[] = [
                            'name' => $formParamName,
                            'contents' => $formParamValueItem
                        ];
                    }
                }
                // for HTTP post (form)
                $httpBody = new MultipartStream($multipartContents);

            } elseif (stripos($headers['Content-Type'], 'application/json') !== false) {
                # if Content-Type contains "application/json", json_encode the form parameters
                $httpBody = \GuzzleHttp\Utils::jsonEncode($formParams);
            } else {
                // for HTTP post (form)
                $httpBody = ObjectSerializer::buildQuery($formParams);
            }
        }

        // this endpoint requires Bearer (API Key) authentication (access token)
        if (!empty($this->config->getAccessToken())) {
            $headers['Authorization'] = 'Bearer ' . $this->config->getAccessToken();
        }

        $defaultHeaders = [];
        if ($this->config->getUserAgent()) {
            $defaultHeaders['User-Agent'] = $this->config->getUserAgent();
        }

        $headers = array_merge(
            $defaultHeaders,
            $headerParams,
            $headers
        );

        $operationHost = $this->config->getHost();
        $query = ObjectSerializer::buildQuery($queryParams);
        return new Request(
            'GET',
            $operationHost . $resourcePath . ($query ? "?{$query}" : ''),
            $headers,
            $httpBody
        );
    }

    /**
     * Operation getVrboListingPaymentTerms
     *
     * Get the Vrbo payment terms
     *
     * @param  string $id Repull listing id (numeric string) of a listing linked to Vrbo. (required)
     * @param  string|null $vrbo_listing_number The Vrbo listing number to work on, when the listing is linked to more than one Vrbo unit. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['getVrboListingPaymentTerms'] to see the possible values for this operation
     *
     * @throws ApiException on non-2xx response or if the response body is not in the expected format
     * @throws InvalidArgumentException
     * @return \Repull\Model\GetVrboListingPaymentTerms200Response|\Repull\Model\Error
     */
    public function getVrboListingPaymentTerms(
        string $id,
        ?string $vrbo_listing_number = null,
        string $contentType = self::contentTypes['getVrboListingPaymentTerms'][0]
    ): \Repull\Model\GetVrboListingPaymentTerms200Response|\Repull\Model\Error
    {
        list($response) = $this->getVrboListingPaymentTermsWithHttpInfo($id, $vrbo_listing_number, $contentType);
        return $response;
    }

    /**
     * Operation getVrboListingPaymentTermsWithHttpInfo
     *
     * Get the Vrbo payment terms
     *
     * @param  string $id Repull listing id (numeric string) of a listing linked to Vrbo. (required)
     * @param  string|null $vrbo_listing_number The Vrbo listing number to work on, when the listing is linked to more than one Vrbo unit. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['getVrboListingPaymentTerms'] to see the possible values for this operation
     *
     * @throws ApiException on non-2xx response or if the response body is not in the expected format
     * @throws InvalidArgumentException
     * @return array of \Repull\Model\GetVrboListingPaymentTerms200Response|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error, HTTP status code, HTTP response headers (array of strings)
     */
    public function getVrboListingPaymentTermsWithHttpInfo(
        string $id,
        ?string $vrbo_listing_number = null,
        string $contentType = self::contentTypes['getVrboListingPaymentTerms'][0]
    ): array
    {
        $request = $this->getVrboListingPaymentTermsRequest($id, $vrbo_listing_number, $contentType);

        try {
            $options = $this->createHttpClientOption();
            try {
                $response = $this->client->send($request, $options);
            } catch (RequestException $e) {
                throw new ApiException(
                    "[{$e->getCode()}] {$e->getMessage()}",
                    (int) $e->getCode(),
                    $e->getResponse() ? $e->getResponse()->getHeaders() : null,
                    $e->getResponse() ? (string) $e->getResponse()->getBody() : null
                );
            } catch (ConnectException $e) {
                throw new ApiException(
                    "[{$e->getCode()}] {$e->getMessage()}",
                    (int) $e->getCode(),
                    null,
                    null
                );
            }

            $statusCode = $response->getStatusCode();

            switch($statusCode) {
                case 200:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\GetVrboListingPaymentTerms200Response',
                        $request,
                        $response,
                    );
                case 401:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 403:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 404:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 409:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 422:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 500:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 502:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 503:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 504:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
            }
            

            if ($statusCode < 200 || $statusCode > 299) {
                throw new ApiException(
                    sprintf(
                        '[%d] Error connecting to the API (%s)',
                        $statusCode,
                        (string) $request->getUri()
                    ),
                    $statusCode,
                    $response->getHeaders(),
                    (string) $response->getBody()
                );
            }

            return $this->handleResponseWithDataType(
                '\Repull\Model\GetVrboListingPaymentTerms200Response',
                $request,
                $response,
            );
        } catch (ApiException $e) {
            switch ($e->getCode()) {
                case 200:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\GetVrboListingPaymentTerms200Response',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 401:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 403:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 404:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 409:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 422:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 500:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 502:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 503:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 504:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
            }
        
            throw $e;
        }
    }

    /**
     * Operation getVrboListingPaymentTermsAsync
     *
     * Get the Vrbo payment terms
     *
     * @param  string $id Repull listing id (numeric string) of a listing linked to Vrbo. (required)
     * @param  string|null $vrbo_listing_number The Vrbo listing number to work on, when the listing is linked to more than one Vrbo unit. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['getVrboListingPaymentTerms'] to see the possible values for this operation
     *
     * @throws InvalidArgumentException
     * @return PromiseInterface
     */
    public function getVrboListingPaymentTermsAsync(
        string $id,
        ?string $vrbo_listing_number = null,
        string $contentType = self::contentTypes['getVrboListingPaymentTerms'][0]
    ): PromiseInterface
    {
        return $this->getVrboListingPaymentTermsAsyncWithHttpInfo($id, $vrbo_listing_number, $contentType)
            ->then(
                function ($response) {
                    return $response[0];
                }
            );
    }

    /**
     * Operation getVrboListingPaymentTermsAsyncWithHttpInfo
     *
     * Get the Vrbo payment terms
     *
     * @param  string $id Repull listing id (numeric string) of a listing linked to Vrbo. (required)
     * @param  string|null $vrbo_listing_number The Vrbo listing number to work on, when the listing is linked to more than one Vrbo unit. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['getVrboListingPaymentTerms'] to see the possible values for this operation
     *
     * @throws InvalidArgumentException
     * @return PromiseInterface
     */
    public function getVrboListingPaymentTermsAsyncWithHttpInfo(
        string $id,
        ?string $vrbo_listing_number = null,
        string $contentType = self::contentTypes['getVrboListingPaymentTerms'][0]
    ): PromiseInterface
    {
        $returnType = '\Repull\Model\GetVrboListingPaymentTerms200Response';
        $request = $this->getVrboListingPaymentTermsRequest($id, $vrbo_listing_number, $contentType);

        return $this->client
            ->sendAsync($request, $this->createHttpClientOption())
            ->then(
                function ($response) use ($returnType) {
                    if (in_array($returnType, ['\SplFileObject', '\Psr\Http\Message\StreamInterface'])) {
                        $content = $response->getBody(); //stream goes to serializer
                    } else {
                        $content = (string) $response->getBody();
                        if ($returnType !== 'string') {
                            $content = json_decode($content);
                        }
                    }

                    return [
                        ObjectSerializer::deserialize($content, $returnType, []),
                        $response->getStatusCode(),
                        $response->getHeaders()
                    ];
                },
                function ($exception) {
                    $response = $exception->getResponse();
                    $statusCode = $response->getStatusCode();
                    throw new ApiException(
                        sprintf(
                            '[%d] Error connecting to the API (%s)',
                            $statusCode,
                            $exception->getRequest()->getUri()
                        ),
                        $statusCode,
                        $response->getHeaders(),
                        (string) $response->getBody()
                    );
                }
            );
    }

    /**
     * Create request for operation 'getVrboListingPaymentTerms'
     *
     * @param  string $id Repull listing id (numeric string) of a listing linked to Vrbo. (required)
     * @param  string|null $vrbo_listing_number The Vrbo listing number to work on, when the listing is linked to more than one Vrbo unit. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['getVrboListingPaymentTerms'] to see the possible values for this operation
     *
     * @throws InvalidArgumentException
     * @return \GuzzleHttp\Psr7\Request
     */
    public function getVrboListingPaymentTermsRequest(
        string $id,
        ?string $vrbo_listing_number = null,
        string $contentType = self::contentTypes['getVrboListingPaymentTerms'][0]
    ): Request
    {

        // verify the required parameter 'id' is set
        if ($id === null || (is_array($id) && count($id) === 0)) {
            throw new InvalidArgumentException(
                'Missing the required parameter $id when calling getVrboListingPaymentTerms'
            );
        }



        $resourcePath = '/v1/channels/vrbo/listings/{id}/payment-terms';
        $formParams = [];
        $queryParams = [];
        $headerParams = [];
        $httpBody = '';
        $multipart = false;

        // query params
        $queryParams = array_merge($queryParams, ObjectSerializer::toQueryValue(
            $vrbo_listing_number,
            'vrboListingNumber', // param base name
            'string', // openApiType
            'form', // style
            true, // explode
            false, // required
            null // openApiFormat
        ) ?? []);


        // path params
        if ($id !== null) {
            $resourcePath = str_replace(
                '{id}',
                ObjectSerializer::toPathValue($id),
                $resourcePath
            );
        }


        $headers = $this->headerSelector->selectHeaders(
            ['application/json', ],
            $contentType,
            $multipart
        );

        // for model (json/xml)
        if (count($formParams) > 0) {
            if ($multipart) {
                $multipartContents = [];
                foreach ($formParams as $formParamName => $formParamValue) {
                    $formParamValueItems = is_array($formParamValue) ? $formParamValue : [$formParamValue];
                    foreach ($formParamValueItems as $formParamValueItem) {
                        $multipartContents[] = [
                            'name' => $formParamName,
                            'contents' => $formParamValueItem
                        ];
                    }
                }
                // for HTTP post (form)
                $httpBody = new MultipartStream($multipartContents);

            } elseif (stripos($headers['Content-Type'], 'application/json') !== false) {
                # if Content-Type contains "application/json", json_encode the form parameters
                $httpBody = \GuzzleHttp\Utils::jsonEncode($formParams);
            } else {
                // for HTTP post (form)
                $httpBody = ObjectSerializer::buildQuery($formParams);
            }
        }

        // this endpoint requires Bearer (API Key) authentication (access token)
        if (!empty($this->config->getAccessToken())) {
            $headers['Authorization'] = 'Bearer ' . $this->config->getAccessToken();
        }

        $defaultHeaders = [];
        if ($this->config->getUserAgent()) {
            $defaultHeaders['User-Agent'] = $this->config->getUserAgent();
        }

        $headers = array_merge(
            $defaultHeaders,
            $headerParams,
            $headers
        );

        $operationHost = $this->config->getHost();
        $query = ObjectSerializer::buildQuery($queryParams);
        return new Request(
            'GET',
            $operationHost . $resourcePath . ($query ? "?{$query}" : ''),
            $headers,
            $httpBody
        );
    }

    /**
     * Operation getVrboListingPhotos
     *
     * Get the Vrbo photos
     *
     * @param  string $id Repull listing id (numeric string) of a listing linked to Vrbo. (required)
     * @param  string|null $vrbo_listing_number The Vrbo listing number to work on, when the listing is linked to more than one Vrbo unit. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['getVrboListingPhotos'] to see the possible values for this operation
     *
     * @throws ApiException on non-2xx response or if the response body is not in the expected format
     * @throws InvalidArgumentException
     * @return \Repull\Model\GetVrboListingPhotos200Response|\Repull\Model\Error
     */
    public function getVrboListingPhotos(
        string $id,
        ?string $vrbo_listing_number = null,
        string $contentType = self::contentTypes['getVrboListingPhotos'][0]
    ): \Repull\Model\GetVrboListingPhotos200Response|\Repull\Model\Error
    {
        list($response) = $this->getVrboListingPhotosWithHttpInfo($id, $vrbo_listing_number, $contentType);
        return $response;
    }

    /**
     * Operation getVrboListingPhotosWithHttpInfo
     *
     * Get the Vrbo photos
     *
     * @param  string $id Repull listing id (numeric string) of a listing linked to Vrbo. (required)
     * @param  string|null $vrbo_listing_number The Vrbo listing number to work on, when the listing is linked to more than one Vrbo unit. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['getVrboListingPhotos'] to see the possible values for this operation
     *
     * @throws ApiException on non-2xx response or if the response body is not in the expected format
     * @throws InvalidArgumentException
     * @return array of \Repull\Model\GetVrboListingPhotos200Response|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error, HTTP status code, HTTP response headers (array of strings)
     */
    public function getVrboListingPhotosWithHttpInfo(
        string $id,
        ?string $vrbo_listing_number = null,
        string $contentType = self::contentTypes['getVrboListingPhotos'][0]
    ): array
    {
        $request = $this->getVrboListingPhotosRequest($id, $vrbo_listing_number, $contentType);

        try {
            $options = $this->createHttpClientOption();
            try {
                $response = $this->client->send($request, $options);
            } catch (RequestException $e) {
                throw new ApiException(
                    "[{$e->getCode()}] {$e->getMessage()}",
                    (int) $e->getCode(),
                    $e->getResponse() ? $e->getResponse()->getHeaders() : null,
                    $e->getResponse() ? (string) $e->getResponse()->getBody() : null
                );
            } catch (ConnectException $e) {
                throw new ApiException(
                    "[{$e->getCode()}] {$e->getMessage()}",
                    (int) $e->getCode(),
                    null,
                    null
                );
            }

            $statusCode = $response->getStatusCode();

            switch($statusCode) {
                case 200:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\GetVrboListingPhotos200Response',
                        $request,
                        $response,
                    );
                case 401:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 403:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 404:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 409:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 422:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 500:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 502:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 503:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 504:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
            }
            

            if ($statusCode < 200 || $statusCode > 299) {
                throw new ApiException(
                    sprintf(
                        '[%d] Error connecting to the API (%s)',
                        $statusCode,
                        (string) $request->getUri()
                    ),
                    $statusCode,
                    $response->getHeaders(),
                    (string) $response->getBody()
                );
            }

            return $this->handleResponseWithDataType(
                '\Repull\Model\GetVrboListingPhotos200Response',
                $request,
                $response,
            );
        } catch (ApiException $e) {
            switch ($e->getCode()) {
                case 200:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\GetVrboListingPhotos200Response',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 401:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 403:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 404:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 409:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 422:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 500:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 502:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 503:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 504:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
            }
        
            throw $e;
        }
    }

    /**
     * Operation getVrboListingPhotosAsync
     *
     * Get the Vrbo photos
     *
     * @param  string $id Repull listing id (numeric string) of a listing linked to Vrbo. (required)
     * @param  string|null $vrbo_listing_number The Vrbo listing number to work on, when the listing is linked to more than one Vrbo unit. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['getVrboListingPhotos'] to see the possible values for this operation
     *
     * @throws InvalidArgumentException
     * @return PromiseInterface
     */
    public function getVrboListingPhotosAsync(
        string $id,
        ?string $vrbo_listing_number = null,
        string $contentType = self::contentTypes['getVrboListingPhotos'][0]
    ): PromiseInterface
    {
        return $this->getVrboListingPhotosAsyncWithHttpInfo($id, $vrbo_listing_number, $contentType)
            ->then(
                function ($response) {
                    return $response[0];
                }
            );
    }

    /**
     * Operation getVrboListingPhotosAsyncWithHttpInfo
     *
     * Get the Vrbo photos
     *
     * @param  string $id Repull listing id (numeric string) of a listing linked to Vrbo. (required)
     * @param  string|null $vrbo_listing_number The Vrbo listing number to work on, when the listing is linked to more than one Vrbo unit. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['getVrboListingPhotos'] to see the possible values for this operation
     *
     * @throws InvalidArgumentException
     * @return PromiseInterface
     */
    public function getVrboListingPhotosAsyncWithHttpInfo(
        string $id,
        ?string $vrbo_listing_number = null,
        string $contentType = self::contentTypes['getVrboListingPhotos'][0]
    ): PromiseInterface
    {
        $returnType = '\Repull\Model\GetVrboListingPhotos200Response';
        $request = $this->getVrboListingPhotosRequest($id, $vrbo_listing_number, $contentType);

        return $this->client
            ->sendAsync($request, $this->createHttpClientOption())
            ->then(
                function ($response) use ($returnType) {
                    if (in_array($returnType, ['\SplFileObject', '\Psr\Http\Message\StreamInterface'])) {
                        $content = $response->getBody(); //stream goes to serializer
                    } else {
                        $content = (string) $response->getBody();
                        if ($returnType !== 'string') {
                            $content = json_decode($content);
                        }
                    }

                    return [
                        ObjectSerializer::deserialize($content, $returnType, []),
                        $response->getStatusCode(),
                        $response->getHeaders()
                    ];
                },
                function ($exception) {
                    $response = $exception->getResponse();
                    $statusCode = $response->getStatusCode();
                    throw new ApiException(
                        sprintf(
                            '[%d] Error connecting to the API (%s)',
                            $statusCode,
                            $exception->getRequest()->getUri()
                        ),
                        $statusCode,
                        $response->getHeaders(),
                        (string) $response->getBody()
                    );
                }
            );
    }

    /**
     * Create request for operation 'getVrboListingPhotos'
     *
     * @param  string $id Repull listing id (numeric string) of a listing linked to Vrbo. (required)
     * @param  string|null $vrbo_listing_number The Vrbo listing number to work on, when the listing is linked to more than one Vrbo unit. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['getVrboListingPhotos'] to see the possible values for this operation
     *
     * @throws InvalidArgumentException
     * @return \GuzzleHttp\Psr7\Request
     */
    public function getVrboListingPhotosRequest(
        string $id,
        ?string $vrbo_listing_number = null,
        string $contentType = self::contentTypes['getVrboListingPhotos'][0]
    ): Request
    {

        // verify the required parameter 'id' is set
        if ($id === null || (is_array($id) && count($id) === 0)) {
            throw new InvalidArgumentException(
                'Missing the required parameter $id when calling getVrboListingPhotos'
            );
        }



        $resourcePath = '/v1/channels/vrbo/listings/{id}/photos';
        $formParams = [];
        $queryParams = [];
        $headerParams = [];
        $httpBody = '';
        $multipart = false;

        // query params
        $queryParams = array_merge($queryParams, ObjectSerializer::toQueryValue(
            $vrbo_listing_number,
            'vrboListingNumber', // param base name
            'string', // openApiType
            'form', // style
            true, // explode
            false, // required
            null // openApiFormat
        ) ?? []);


        // path params
        if ($id !== null) {
            $resourcePath = str_replace(
                '{id}',
                ObjectSerializer::toPathValue($id),
                $resourcePath
            );
        }


        $headers = $this->headerSelector->selectHeaders(
            ['application/json', ],
            $contentType,
            $multipart
        );

        // for model (json/xml)
        if (count($formParams) > 0) {
            if ($multipart) {
                $multipartContents = [];
                foreach ($formParams as $formParamName => $formParamValue) {
                    $formParamValueItems = is_array($formParamValue) ? $formParamValue : [$formParamValue];
                    foreach ($formParamValueItems as $formParamValueItem) {
                        $multipartContents[] = [
                            'name' => $formParamName,
                            'contents' => $formParamValueItem
                        ];
                    }
                }
                // for HTTP post (form)
                $httpBody = new MultipartStream($multipartContents);

            } elseif (stripos($headers['Content-Type'], 'application/json') !== false) {
                # if Content-Type contains "application/json", json_encode the form parameters
                $httpBody = \GuzzleHttp\Utils::jsonEncode($formParams);
            } else {
                // for HTTP post (form)
                $httpBody = ObjectSerializer::buildQuery($formParams);
            }
        }

        // this endpoint requires Bearer (API Key) authentication (access token)
        if (!empty($this->config->getAccessToken())) {
            $headers['Authorization'] = 'Bearer ' . $this->config->getAccessToken();
        }

        $defaultHeaders = [];
        if ($this->config->getUserAgent()) {
            $defaultHeaders['User-Agent'] = $this->config->getUserAgent();
        }

        $headers = array_merge(
            $defaultHeaders,
            $headerParams,
            $headers
        );

        $operationHost = $this->config->getHost();
        $query = ObjectSerializer::buildQuery($queryParams);
        return new Request(
            'GET',
            $operationHost . $resourcePath . ($query ? "?{$query}" : ''),
            $headers,
            $httpBody
        );
    }

    /**
     * Operation getVrboListingPricing
     *
     * Get the Vrbo base pricing
     *
     * @param  string $id Repull listing id (numeric string) of a listing linked to Vrbo. (required)
     * @param  string|null $vrbo_listing_number The Vrbo listing number to work on, when the listing is linked to more than one Vrbo unit. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['getVrboListingPricing'] to see the possible values for this operation
     *
     * @throws ApiException on non-2xx response or if the response body is not in the expected format
     * @throws InvalidArgumentException
     * @return \Repull\Model\GetVrboListingPricing200Response|\Repull\Model\Error
     */
    public function getVrboListingPricing(
        string $id,
        ?string $vrbo_listing_number = null,
        string $contentType = self::contentTypes['getVrboListingPricing'][0]
    ): \Repull\Model\GetVrboListingPricing200Response|\Repull\Model\Error
    {
        list($response) = $this->getVrboListingPricingWithHttpInfo($id, $vrbo_listing_number, $contentType);
        return $response;
    }

    /**
     * Operation getVrboListingPricingWithHttpInfo
     *
     * Get the Vrbo base pricing
     *
     * @param  string $id Repull listing id (numeric string) of a listing linked to Vrbo. (required)
     * @param  string|null $vrbo_listing_number The Vrbo listing number to work on, when the listing is linked to more than one Vrbo unit. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['getVrboListingPricing'] to see the possible values for this operation
     *
     * @throws ApiException on non-2xx response or if the response body is not in the expected format
     * @throws InvalidArgumentException
     * @return array of \Repull\Model\GetVrboListingPricing200Response|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error, HTTP status code, HTTP response headers (array of strings)
     */
    public function getVrboListingPricingWithHttpInfo(
        string $id,
        ?string $vrbo_listing_number = null,
        string $contentType = self::contentTypes['getVrboListingPricing'][0]
    ): array
    {
        $request = $this->getVrboListingPricingRequest($id, $vrbo_listing_number, $contentType);

        try {
            $options = $this->createHttpClientOption();
            try {
                $response = $this->client->send($request, $options);
            } catch (RequestException $e) {
                throw new ApiException(
                    "[{$e->getCode()}] {$e->getMessage()}",
                    (int) $e->getCode(),
                    $e->getResponse() ? $e->getResponse()->getHeaders() : null,
                    $e->getResponse() ? (string) $e->getResponse()->getBody() : null
                );
            } catch (ConnectException $e) {
                throw new ApiException(
                    "[{$e->getCode()}] {$e->getMessage()}",
                    (int) $e->getCode(),
                    null,
                    null
                );
            }

            $statusCode = $response->getStatusCode();

            switch($statusCode) {
                case 200:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\GetVrboListingPricing200Response',
                        $request,
                        $response,
                    );
                case 401:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 403:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 404:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 409:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 422:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 500:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 502:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 503:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 504:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
            }
            

            if ($statusCode < 200 || $statusCode > 299) {
                throw new ApiException(
                    sprintf(
                        '[%d] Error connecting to the API (%s)',
                        $statusCode,
                        (string) $request->getUri()
                    ),
                    $statusCode,
                    $response->getHeaders(),
                    (string) $response->getBody()
                );
            }

            return $this->handleResponseWithDataType(
                '\Repull\Model\GetVrboListingPricing200Response',
                $request,
                $response,
            );
        } catch (ApiException $e) {
            switch ($e->getCode()) {
                case 200:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\GetVrboListingPricing200Response',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 401:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 403:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 404:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 409:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 422:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 500:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 502:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 503:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 504:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
            }
        
            throw $e;
        }
    }

    /**
     * Operation getVrboListingPricingAsync
     *
     * Get the Vrbo base pricing
     *
     * @param  string $id Repull listing id (numeric string) of a listing linked to Vrbo. (required)
     * @param  string|null $vrbo_listing_number The Vrbo listing number to work on, when the listing is linked to more than one Vrbo unit. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['getVrboListingPricing'] to see the possible values for this operation
     *
     * @throws InvalidArgumentException
     * @return PromiseInterface
     */
    public function getVrboListingPricingAsync(
        string $id,
        ?string $vrbo_listing_number = null,
        string $contentType = self::contentTypes['getVrboListingPricing'][0]
    ): PromiseInterface
    {
        return $this->getVrboListingPricingAsyncWithHttpInfo($id, $vrbo_listing_number, $contentType)
            ->then(
                function ($response) {
                    return $response[0];
                }
            );
    }

    /**
     * Operation getVrboListingPricingAsyncWithHttpInfo
     *
     * Get the Vrbo base pricing
     *
     * @param  string $id Repull listing id (numeric string) of a listing linked to Vrbo. (required)
     * @param  string|null $vrbo_listing_number The Vrbo listing number to work on, when the listing is linked to more than one Vrbo unit. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['getVrboListingPricing'] to see the possible values for this operation
     *
     * @throws InvalidArgumentException
     * @return PromiseInterface
     */
    public function getVrboListingPricingAsyncWithHttpInfo(
        string $id,
        ?string $vrbo_listing_number = null,
        string $contentType = self::contentTypes['getVrboListingPricing'][0]
    ): PromiseInterface
    {
        $returnType = '\Repull\Model\GetVrboListingPricing200Response';
        $request = $this->getVrboListingPricingRequest($id, $vrbo_listing_number, $contentType);

        return $this->client
            ->sendAsync($request, $this->createHttpClientOption())
            ->then(
                function ($response) use ($returnType) {
                    if (in_array($returnType, ['\SplFileObject', '\Psr\Http\Message\StreamInterface'])) {
                        $content = $response->getBody(); //stream goes to serializer
                    } else {
                        $content = (string) $response->getBody();
                        if ($returnType !== 'string') {
                            $content = json_decode($content);
                        }
                    }

                    return [
                        ObjectSerializer::deserialize($content, $returnType, []),
                        $response->getStatusCode(),
                        $response->getHeaders()
                    ];
                },
                function ($exception) {
                    $response = $exception->getResponse();
                    $statusCode = $response->getStatusCode();
                    throw new ApiException(
                        sprintf(
                            '[%d] Error connecting to the API (%s)',
                            $statusCode,
                            $exception->getRequest()->getUri()
                        ),
                        $statusCode,
                        $response->getHeaders(),
                        (string) $response->getBody()
                    );
                }
            );
    }

    /**
     * Create request for operation 'getVrboListingPricing'
     *
     * @param  string $id Repull listing id (numeric string) of a listing linked to Vrbo. (required)
     * @param  string|null $vrbo_listing_number The Vrbo listing number to work on, when the listing is linked to more than one Vrbo unit. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['getVrboListingPricing'] to see the possible values for this operation
     *
     * @throws InvalidArgumentException
     * @return \GuzzleHttp\Psr7\Request
     */
    public function getVrboListingPricingRequest(
        string $id,
        ?string $vrbo_listing_number = null,
        string $contentType = self::contentTypes['getVrboListingPricing'][0]
    ): Request
    {

        // verify the required parameter 'id' is set
        if ($id === null || (is_array($id) && count($id) === 0)) {
            throw new InvalidArgumentException(
                'Missing the required parameter $id when calling getVrboListingPricing'
            );
        }



        $resourcePath = '/v1/channels/vrbo/listings/{id}/pricing';
        $formParams = [];
        $queryParams = [];
        $headerParams = [];
        $httpBody = '';
        $multipart = false;

        // query params
        $queryParams = array_merge($queryParams, ObjectSerializer::toQueryValue(
            $vrbo_listing_number,
            'vrboListingNumber', // param base name
            'string', // openApiType
            'form', // style
            true, // explode
            false, // required
            null // openApiFormat
        ) ?? []);


        // path params
        if ($id !== null) {
            $resourcePath = str_replace(
                '{id}',
                ObjectSerializer::toPathValue($id),
                $resourcePath
            );
        }


        $headers = $this->headerSelector->selectHeaders(
            ['application/json', ],
            $contentType,
            $multipart
        );

        // for model (json/xml)
        if (count($formParams) > 0) {
            if ($multipart) {
                $multipartContents = [];
                foreach ($formParams as $formParamName => $formParamValue) {
                    $formParamValueItems = is_array($formParamValue) ? $formParamValue : [$formParamValue];
                    foreach ($formParamValueItems as $formParamValueItem) {
                        $multipartContents[] = [
                            'name' => $formParamName,
                            'contents' => $formParamValueItem
                        ];
                    }
                }
                // for HTTP post (form)
                $httpBody = new MultipartStream($multipartContents);

            } elseif (stripos($headers['Content-Type'], 'application/json') !== false) {
                # if Content-Type contains "application/json", json_encode the form parameters
                $httpBody = \GuzzleHttp\Utils::jsonEncode($formParams);
            } else {
                // for HTTP post (form)
                $httpBody = ObjectSerializer::buildQuery($formParams);
            }
        }

        // this endpoint requires Bearer (API Key) authentication (access token)
        if (!empty($this->config->getAccessToken())) {
            $headers['Authorization'] = 'Bearer ' . $this->config->getAccessToken();
        }

        $defaultHeaders = [];
        if ($this->config->getUserAgent()) {
            $defaultHeaders['User-Agent'] = $this->config->getUserAgent();
        }

        $headers = array_merge(
            $defaultHeaders,
            $headerParams,
            $headers
        );

        $operationHost = $this->config->getHost();
        $query = ObjectSerializer::buildQuery($queryParams);
        return new Request(
            'GET',
            $operationHost . $resourcePath . ($query ? "?{$query}" : ''),
            $headers,
            $httpBody
        );
    }

    /**
     * Operation getVrboListingProperty
     *
     * Get the Vrbo property details
     *
     * @param  string $id Repull listing id (numeric string) of a listing linked to Vrbo. (required)
     * @param  string|null $vrbo_listing_number The Vrbo listing number to work on, when the listing is linked to more than one Vrbo unit. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['getVrboListingProperty'] to see the possible values for this operation
     *
     * @throws ApiException on non-2xx response or if the response body is not in the expected format
     * @throws InvalidArgumentException
     * @return \Repull\Model\GetVrboListingProperty200Response|\Repull\Model\Error
     */
    public function getVrboListingProperty(
        string $id,
        ?string $vrbo_listing_number = null,
        string $contentType = self::contentTypes['getVrboListingProperty'][0]
    ): \Repull\Model\GetVrboListingProperty200Response|\Repull\Model\Error
    {
        list($response) = $this->getVrboListingPropertyWithHttpInfo($id, $vrbo_listing_number, $contentType);
        return $response;
    }

    /**
     * Operation getVrboListingPropertyWithHttpInfo
     *
     * Get the Vrbo property details
     *
     * @param  string $id Repull listing id (numeric string) of a listing linked to Vrbo. (required)
     * @param  string|null $vrbo_listing_number The Vrbo listing number to work on, when the listing is linked to more than one Vrbo unit. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['getVrboListingProperty'] to see the possible values for this operation
     *
     * @throws ApiException on non-2xx response or if the response body is not in the expected format
     * @throws InvalidArgumentException
     * @return array of \Repull\Model\GetVrboListingProperty200Response|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error, HTTP status code, HTTP response headers (array of strings)
     */
    public function getVrboListingPropertyWithHttpInfo(
        string $id,
        ?string $vrbo_listing_number = null,
        string $contentType = self::contentTypes['getVrboListingProperty'][0]
    ): array
    {
        $request = $this->getVrboListingPropertyRequest($id, $vrbo_listing_number, $contentType);

        try {
            $options = $this->createHttpClientOption();
            try {
                $response = $this->client->send($request, $options);
            } catch (RequestException $e) {
                throw new ApiException(
                    "[{$e->getCode()}] {$e->getMessage()}",
                    (int) $e->getCode(),
                    $e->getResponse() ? $e->getResponse()->getHeaders() : null,
                    $e->getResponse() ? (string) $e->getResponse()->getBody() : null
                );
            } catch (ConnectException $e) {
                throw new ApiException(
                    "[{$e->getCode()}] {$e->getMessage()}",
                    (int) $e->getCode(),
                    null,
                    null
                );
            }

            $statusCode = $response->getStatusCode();

            switch($statusCode) {
                case 200:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\GetVrboListingProperty200Response',
                        $request,
                        $response,
                    );
                case 401:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 403:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 404:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 409:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 422:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 500:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 502:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 503:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 504:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
            }
            

            if ($statusCode < 200 || $statusCode > 299) {
                throw new ApiException(
                    sprintf(
                        '[%d] Error connecting to the API (%s)',
                        $statusCode,
                        (string) $request->getUri()
                    ),
                    $statusCode,
                    $response->getHeaders(),
                    (string) $response->getBody()
                );
            }

            return $this->handleResponseWithDataType(
                '\Repull\Model\GetVrboListingProperty200Response',
                $request,
                $response,
            );
        } catch (ApiException $e) {
            switch ($e->getCode()) {
                case 200:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\GetVrboListingProperty200Response',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 401:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 403:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 404:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 409:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 422:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 500:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 502:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 503:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 504:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
            }
        
            throw $e;
        }
    }

    /**
     * Operation getVrboListingPropertyAsync
     *
     * Get the Vrbo property details
     *
     * @param  string $id Repull listing id (numeric string) of a listing linked to Vrbo. (required)
     * @param  string|null $vrbo_listing_number The Vrbo listing number to work on, when the listing is linked to more than one Vrbo unit. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['getVrboListingProperty'] to see the possible values for this operation
     *
     * @throws InvalidArgumentException
     * @return PromiseInterface
     */
    public function getVrboListingPropertyAsync(
        string $id,
        ?string $vrbo_listing_number = null,
        string $contentType = self::contentTypes['getVrboListingProperty'][0]
    ): PromiseInterface
    {
        return $this->getVrboListingPropertyAsyncWithHttpInfo($id, $vrbo_listing_number, $contentType)
            ->then(
                function ($response) {
                    return $response[0];
                }
            );
    }

    /**
     * Operation getVrboListingPropertyAsyncWithHttpInfo
     *
     * Get the Vrbo property details
     *
     * @param  string $id Repull listing id (numeric string) of a listing linked to Vrbo. (required)
     * @param  string|null $vrbo_listing_number The Vrbo listing number to work on, when the listing is linked to more than one Vrbo unit. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['getVrboListingProperty'] to see the possible values for this operation
     *
     * @throws InvalidArgumentException
     * @return PromiseInterface
     */
    public function getVrboListingPropertyAsyncWithHttpInfo(
        string $id,
        ?string $vrbo_listing_number = null,
        string $contentType = self::contentTypes['getVrboListingProperty'][0]
    ): PromiseInterface
    {
        $returnType = '\Repull\Model\GetVrboListingProperty200Response';
        $request = $this->getVrboListingPropertyRequest($id, $vrbo_listing_number, $contentType);

        return $this->client
            ->sendAsync($request, $this->createHttpClientOption())
            ->then(
                function ($response) use ($returnType) {
                    if (in_array($returnType, ['\SplFileObject', '\Psr\Http\Message\StreamInterface'])) {
                        $content = $response->getBody(); //stream goes to serializer
                    } else {
                        $content = (string) $response->getBody();
                        if ($returnType !== 'string') {
                            $content = json_decode($content);
                        }
                    }

                    return [
                        ObjectSerializer::deserialize($content, $returnType, []),
                        $response->getStatusCode(),
                        $response->getHeaders()
                    ];
                },
                function ($exception) {
                    $response = $exception->getResponse();
                    $statusCode = $response->getStatusCode();
                    throw new ApiException(
                        sprintf(
                            '[%d] Error connecting to the API (%s)',
                            $statusCode,
                            $exception->getRequest()->getUri()
                        ),
                        $statusCode,
                        $response->getHeaders(),
                        (string) $response->getBody()
                    );
                }
            );
    }

    /**
     * Create request for operation 'getVrboListingProperty'
     *
     * @param  string $id Repull listing id (numeric string) of a listing linked to Vrbo. (required)
     * @param  string|null $vrbo_listing_number The Vrbo listing number to work on, when the listing is linked to more than one Vrbo unit. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['getVrboListingProperty'] to see the possible values for this operation
     *
     * @throws InvalidArgumentException
     * @return \GuzzleHttp\Psr7\Request
     */
    public function getVrboListingPropertyRequest(
        string $id,
        ?string $vrbo_listing_number = null,
        string $contentType = self::contentTypes['getVrboListingProperty'][0]
    ): Request
    {

        // verify the required parameter 'id' is set
        if ($id === null || (is_array($id) && count($id) === 0)) {
            throw new InvalidArgumentException(
                'Missing the required parameter $id when calling getVrboListingProperty'
            );
        }



        $resourcePath = '/v1/channels/vrbo/listings/{id}/property';
        $formParams = [];
        $queryParams = [];
        $headerParams = [];
        $httpBody = '';
        $multipart = false;

        // query params
        $queryParams = array_merge($queryParams, ObjectSerializer::toQueryValue(
            $vrbo_listing_number,
            'vrboListingNumber', // param base name
            'string', // openApiType
            'form', // style
            true, // explode
            false, // required
            null // openApiFormat
        ) ?? []);


        // path params
        if ($id !== null) {
            $resourcePath = str_replace(
                '{id}',
                ObjectSerializer::toPathValue($id),
                $resourcePath
            );
        }


        $headers = $this->headerSelector->selectHeaders(
            ['application/json', ],
            $contentType,
            $multipart
        );

        // for model (json/xml)
        if (count($formParams) > 0) {
            if ($multipart) {
                $multipartContents = [];
                foreach ($formParams as $formParamName => $formParamValue) {
                    $formParamValueItems = is_array($formParamValue) ? $formParamValue : [$formParamValue];
                    foreach ($formParamValueItems as $formParamValueItem) {
                        $multipartContents[] = [
                            'name' => $formParamName,
                            'contents' => $formParamValueItem
                        ];
                    }
                }
                // for HTTP post (form)
                $httpBody = new MultipartStream($multipartContents);

            } elseif (stripos($headers['Content-Type'], 'application/json') !== false) {
                # if Content-Type contains "application/json", json_encode the form parameters
                $httpBody = \GuzzleHttp\Utils::jsonEncode($formParams);
            } else {
                // for HTTP post (form)
                $httpBody = ObjectSerializer::buildQuery($formParams);
            }
        }

        // this endpoint requires Bearer (API Key) authentication (access token)
        if (!empty($this->config->getAccessToken())) {
            $headers['Authorization'] = 'Bearer ' . $this->config->getAccessToken();
        }

        $defaultHeaders = [];
        if ($this->config->getUserAgent()) {
            $defaultHeaders['User-Agent'] = $this->config->getUserAgent();
        }

        $headers = array_merge(
            $defaultHeaders,
            $headerParams,
            $headers
        );

        $operationHost = $this->config->getHost();
        $query = ObjectSerializer::buildQuery($queryParams);
        return new Request(
            'GET',
            $operationHost . $resourcePath . ($query ? "?{$query}" : ''),
            $headers,
            $httpBody
        );
    }

    /**
     * Operation getVrboListingProtection
     *
     * Get the Vrbo damage protection
     *
     * @param  string $id Repull listing id (numeric string) of a listing linked to Vrbo. (required)
     * @param  string|null $vrbo_listing_number The Vrbo listing number to work on, when the listing is linked to more than one Vrbo unit. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['getVrboListingProtection'] to see the possible values for this operation
     *
     * @throws ApiException on non-2xx response or if the response body is not in the expected format
     * @throws InvalidArgumentException
     * @return \Repull\Model\GetVrboListingProtection200Response|\Repull\Model\Error
     */
    public function getVrboListingProtection(
        string $id,
        ?string $vrbo_listing_number = null,
        string $contentType = self::contentTypes['getVrboListingProtection'][0]
    ): \Repull\Model\GetVrboListingProtection200Response|\Repull\Model\Error
    {
        list($response) = $this->getVrboListingProtectionWithHttpInfo($id, $vrbo_listing_number, $contentType);
        return $response;
    }

    /**
     * Operation getVrboListingProtectionWithHttpInfo
     *
     * Get the Vrbo damage protection
     *
     * @param  string $id Repull listing id (numeric string) of a listing linked to Vrbo. (required)
     * @param  string|null $vrbo_listing_number The Vrbo listing number to work on, when the listing is linked to more than one Vrbo unit. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['getVrboListingProtection'] to see the possible values for this operation
     *
     * @throws ApiException on non-2xx response or if the response body is not in the expected format
     * @throws InvalidArgumentException
     * @return array of \Repull\Model\GetVrboListingProtection200Response|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error, HTTP status code, HTTP response headers (array of strings)
     */
    public function getVrboListingProtectionWithHttpInfo(
        string $id,
        ?string $vrbo_listing_number = null,
        string $contentType = self::contentTypes['getVrboListingProtection'][0]
    ): array
    {
        $request = $this->getVrboListingProtectionRequest($id, $vrbo_listing_number, $contentType);

        try {
            $options = $this->createHttpClientOption();
            try {
                $response = $this->client->send($request, $options);
            } catch (RequestException $e) {
                throw new ApiException(
                    "[{$e->getCode()}] {$e->getMessage()}",
                    (int) $e->getCode(),
                    $e->getResponse() ? $e->getResponse()->getHeaders() : null,
                    $e->getResponse() ? (string) $e->getResponse()->getBody() : null
                );
            } catch (ConnectException $e) {
                throw new ApiException(
                    "[{$e->getCode()}] {$e->getMessage()}",
                    (int) $e->getCode(),
                    null,
                    null
                );
            }

            $statusCode = $response->getStatusCode();

            switch($statusCode) {
                case 200:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\GetVrboListingProtection200Response',
                        $request,
                        $response,
                    );
                case 401:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 403:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 404:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 409:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 422:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 500:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 502:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 503:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 504:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
            }
            

            if ($statusCode < 200 || $statusCode > 299) {
                throw new ApiException(
                    sprintf(
                        '[%d] Error connecting to the API (%s)',
                        $statusCode,
                        (string) $request->getUri()
                    ),
                    $statusCode,
                    $response->getHeaders(),
                    (string) $response->getBody()
                );
            }

            return $this->handleResponseWithDataType(
                '\Repull\Model\GetVrboListingProtection200Response',
                $request,
                $response,
            );
        } catch (ApiException $e) {
            switch ($e->getCode()) {
                case 200:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\GetVrboListingProtection200Response',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 401:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 403:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 404:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 409:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 422:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 500:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 502:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 503:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 504:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
            }
        
            throw $e;
        }
    }

    /**
     * Operation getVrboListingProtectionAsync
     *
     * Get the Vrbo damage protection
     *
     * @param  string $id Repull listing id (numeric string) of a listing linked to Vrbo. (required)
     * @param  string|null $vrbo_listing_number The Vrbo listing number to work on, when the listing is linked to more than one Vrbo unit. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['getVrboListingProtection'] to see the possible values for this operation
     *
     * @throws InvalidArgumentException
     * @return PromiseInterface
     */
    public function getVrboListingProtectionAsync(
        string $id,
        ?string $vrbo_listing_number = null,
        string $contentType = self::contentTypes['getVrboListingProtection'][0]
    ): PromiseInterface
    {
        return $this->getVrboListingProtectionAsyncWithHttpInfo($id, $vrbo_listing_number, $contentType)
            ->then(
                function ($response) {
                    return $response[0];
                }
            );
    }

    /**
     * Operation getVrboListingProtectionAsyncWithHttpInfo
     *
     * Get the Vrbo damage protection
     *
     * @param  string $id Repull listing id (numeric string) of a listing linked to Vrbo. (required)
     * @param  string|null $vrbo_listing_number The Vrbo listing number to work on, when the listing is linked to more than one Vrbo unit. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['getVrboListingProtection'] to see the possible values for this operation
     *
     * @throws InvalidArgumentException
     * @return PromiseInterface
     */
    public function getVrboListingProtectionAsyncWithHttpInfo(
        string $id,
        ?string $vrbo_listing_number = null,
        string $contentType = self::contentTypes['getVrboListingProtection'][0]
    ): PromiseInterface
    {
        $returnType = '\Repull\Model\GetVrboListingProtection200Response';
        $request = $this->getVrboListingProtectionRequest($id, $vrbo_listing_number, $contentType);

        return $this->client
            ->sendAsync($request, $this->createHttpClientOption())
            ->then(
                function ($response) use ($returnType) {
                    if (in_array($returnType, ['\SplFileObject', '\Psr\Http\Message\StreamInterface'])) {
                        $content = $response->getBody(); //stream goes to serializer
                    } else {
                        $content = (string) $response->getBody();
                        if ($returnType !== 'string') {
                            $content = json_decode($content);
                        }
                    }

                    return [
                        ObjectSerializer::deserialize($content, $returnType, []),
                        $response->getStatusCode(),
                        $response->getHeaders()
                    ];
                },
                function ($exception) {
                    $response = $exception->getResponse();
                    $statusCode = $response->getStatusCode();
                    throw new ApiException(
                        sprintf(
                            '[%d] Error connecting to the API (%s)',
                            $statusCode,
                            $exception->getRequest()->getUri()
                        ),
                        $statusCode,
                        $response->getHeaders(),
                        (string) $response->getBody()
                    );
                }
            );
    }

    /**
     * Create request for operation 'getVrboListingProtection'
     *
     * @param  string $id Repull listing id (numeric string) of a listing linked to Vrbo. (required)
     * @param  string|null $vrbo_listing_number The Vrbo listing number to work on, when the listing is linked to more than one Vrbo unit. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['getVrboListingProtection'] to see the possible values for this operation
     *
     * @throws InvalidArgumentException
     * @return \GuzzleHttp\Psr7\Request
     */
    public function getVrboListingProtectionRequest(
        string $id,
        ?string $vrbo_listing_number = null,
        string $contentType = self::contentTypes['getVrboListingProtection'][0]
    ): Request
    {

        // verify the required parameter 'id' is set
        if ($id === null || (is_array($id) && count($id) === 0)) {
            throw new InvalidArgumentException(
                'Missing the required parameter $id when calling getVrboListingProtection'
            );
        }



        $resourcePath = '/v1/channels/vrbo/listings/{id}/protection';
        $formParams = [];
        $queryParams = [];
        $headerParams = [];
        $httpBody = '';
        $multipart = false;

        // query params
        $queryParams = array_merge($queryParams, ObjectSerializer::toQueryValue(
            $vrbo_listing_number,
            'vrboListingNumber', // param base name
            'string', // openApiType
            'form', // style
            true, // explode
            false, // required
            null // openApiFormat
        ) ?? []);


        // path params
        if ($id !== null) {
            $resourcePath = str_replace(
                '{id}',
                ObjectSerializer::toPathValue($id),
                $resourcePath
            );
        }


        $headers = $this->headerSelector->selectHeaders(
            ['application/json', ],
            $contentType,
            $multipart
        );

        // for model (json/xml)
        if (count($formParams) > 0) {
            if ($multipart) {
                $multipartContents = [];
                foreach ($formParams as $formParamName => $formParamValue) {
                    $formParamValueItems = is_array($formParamValue) ? $formParamValue : [$formParamValue];
                    foreach ($formParamValueItems as $formParamValueItem) {
                        $multipartContents[] = [
                            'name' => $formParamName,
                            'contents' => $formParamValueItem
                        ];
                    }
                }
                // for HTTP post (form)
                $httpBody = new MultipartStream($multipartContents);

            } elseif (stripos($headers['Content-Type'], 'application/json') !== false) {
                # if Content-Type contains "application/json", json_encode the form parameters
                $httpBody = \GuzzleHttp\Utils::jsonEncode($formParams);
            } else {
                // for HTTP post (form)
                $httpBody = ObjectSerializer::buildQuery($formParams);
            }
        }

        // this endpoint requires Bearer (API Key) authentication (access token)
        if (!empty($this->config->getAccessToken())) {
            $headers['Authorization'] = 'Bearer ' . $this->config->getAccessToken();
        }

        $defaultHeaders = [];
        if ($this->config->getUserAgent()) {
            $defaultHeaders['User-Agent'] = $this->config->getUserAgent();
        }

        $headers = array_merge(
            $defaultHeaders,
            $headerParams,
            $headers
        );

        $operationHost = $this->config->getHost();
        $query = ObjectSerializer::buildQuery($queryParams);
        return new Request(
            'GET',
            $operationHost . $resourcePath . ($query ? "?{$query}" : ''),
            $headers,
            $httpBody
        );
    }

    /**
     * Operation getVrboListingRates
     *
     * Get the Vrbo rates and stay discounts
     *
     * @param  string $id Repull listing id (numeric string) of a listing linked to Vrbo. (required)
     * @param  string|null $vrbo_listing_number The Vrbo listing number to work on, when the listing is linked to more than one Vrbo unit. (optional)
     * @param  \DateTime|null $start First night to read (default today). (optional)
     * @param  \DateTime|null $end Last night to read (default start + 13 days; at most 3 years). (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['getVrboListingRates'] to see the possible values for this operation
     *
     * @throws ApiException on non-2xx response or if the response body is not in the expected format
     * @throws InvalidArgumentException
     * @return \Repull\Model\GetVrboListingRates200Response|\Repull\Model\Error
     */
    public function getVrboListingRates(
        string $id,
        ?string $vrbo_listing_number = null,
        ?\DateTime $start = null,
        ?\DateTime $end = null,
        string $contentType = self::contentTypes['getVrboListingRates'][0]
    ): \Repull\Model\GetVrboListingRates200Response|\Repull\Model\Error
    {
        list($response) = $this->getVrboListingRatesWithHttpInfo($id, $vrbo_listing_number, $start, $end, $contentType);
        return $response;
    }

    /**
     * Operation getVrboListingRatesWithHttpInfo
     *
     * Get the Vrbo rates and stay discounts
     *
     * @param  string $id Repull listing id (numeric string) of a listing linked to Vrbo. (required)
     * @param  string|null $vrbo_listing_number The Vrbo listing number to work on, when the listing is linked to more than one Vrbo unit. (optional)
     * @param  \DateTime|null $start First night to read (default today). (optional)
     * @param  \DateTime|null $end Last night to read (default start + 13 days; at most 3 years). (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['getVrboListingRates'] to see the possible values for this operation
     *
     * @throws ApiException on non-2xx response or if the response body is not in the expected format
     * @throws InvalidArgumentException
     * @return array of \Repull\Model\GetVrboListingRates200Response|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error, HTTP status code, HTTP response headers (array of strings)
     */
    public function getVrboListingRatesWithHttpInfo(
        string $id,
        ?string $vrbo_listing_number = null,
        ?\DateTime $start = null,
        ?\DateTime $end = null,
        string $contentType = self::contentTypes['getVrboListingRates'][0]
    ): array
    {
        $request = $this->getVrboListingRatesRequest($id, $vrbo_listing_number, $start, $end, $contentType);

        try {
            $options = $this->createHttpClientOption();
            try {
                $response = $this->client->send($request, $options);
            } catch (RequestException $e) {
                throw new ApiException(
                    "[{$e->getCode()}] {$e->getMessage()}",
                    (int) $e->getCode(),
                    $e->getResponse() ? $e->getResponse()->getHeaders() : null,
                    $e->getResponse() ? (string) $e->getResponse()->getBody() : null
                );
            } catch (ConnectException $e) {
                throw new ApiException(
                    "[{$e->getCode()}] {$e->getMessage()}",
                    (int) $e->getCode(),
                    null,
                    null
                );
            }

            $statusCode = $response->getStatusCode();

            switch($statusCode) {
                case 200:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\GetVrboListingRates200Response',
                        $request,
                        $response,
                    );
                case 401:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 403:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 404:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 409:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 422:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 500:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 502:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 503:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 504:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
            }
            

            if ($statusCode < 200 || $statusCode > 299) {
                throw new ApiException(
                    sprintf(
                        '[%d] Error connecting to the API (%s)',
                        $statusCode,
                        (string) $request->getUri()
                    ),
                    $statusCode,
                    $response->getHeaders(),
                    (string) $response->getBody()
                );
            }

            return $this->handleResponseWithDataType(
                '\Repull\Model\GetVrboListingRates200Response',
                $request,
                $response,
            );
        } catch (ApiException $e) {
            switch ($e->getCode()) {
                case 200:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\GetVrboListingRates200Response',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 401:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 403:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 404:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 409:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 422:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 500:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 502:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 503:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 504:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
            }
        
            throw $e;
        }
    }

    /**
     * Operation getVrboListingRatesAsync
     *
     * Get the Vrbo rates and stay discounts
     *
     * @param  string $id Repull listing id (numeric string) of a listing linked to Vrbo. (required)
     * @param  string|null $vrbo_listing_number The Vrbo listing number to work on, when the listing is linked to more than one Vrbo unit. (optional)
     * @param  \DateTime|null $start First night to read (default today). (optional)
     * @param  \DateTime|null $end Last night to read (default start + 13 days; at most 3 years). (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['getVrboListingRates'] to see the possible values for this operation
     *
     * @throws InvalidArgumentException
     * @return PromiseInterface
     */
    public function getVrboListingRatesAsync(
        string $id,
        ?string $vrbo_listing_number = null,
        ?\DateTime $start = null,
        ?\DateTime $end = null,
        string $contentType = self::contentTypes['getVrboListingRates'][0]
    ): PromiseInterface
    {
        return $this->getVrboListingRatesAsyncWithHttpInfo($id, $vrbo_listing_number, $start, $end, $contentType)
            ->then(
                function ($response) {
                    return $response[0];
                }
            );
    }

    /**
     * Operation getVrboListingRatesAsyncWithHttpInfo
     *
     * Get the Vrbo rates and stay discounts
     *
     * @param  string $id Repull listing id (numeric string) of a listing linked to Vrbo. (required)
     * @param  string|null $vrbo_listing_number The Vrbo listing number to work on, when the listing is linked to more than one Vrbo unit. (optional)
     * @param  \DateTime|null $start First night to read (default today). (optional)
     * @param  \DateTime|null $end Last night to read (default start + 13 days; at most 3 years). (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['getVrboListingRates'] to see the possible values for this operation
     *
     * @throws InvalidArgumentException
     * @return PromiseInterface
     */
    public function getVrboListingRatesAsyncWithHttpInfo(
        string $id,
        ?string $vrbo_listing_number = null,
        ?\DateTime $start = null,
        ?\DateTime $end = null,
        string $contentType = self::contentTypes['getVrboListingRates'][0]
    ): PromiseInterface
    {
        $returnType = '\Repull\Model\GetVrboListingRates200Response';
        $request = $this->getVrboListingRatesRequest($id, $vrbo_listing_number, $start, $end, $contentType);

        return $this->client
            ->sendAsync($request, $this->createHttpClientOption())
            ->then(
                function ($response) use ($returnType) {
                    if (in_array($returnType, ['\SplFileObject', '\Psr\Http\Message\StreamInterface'])) {
                        $content = $response->getBody(); //stream goes to serializer
                    } else {
                        $content = (string) $response->getBody();
                        if ($returnType !== 'string') {
                            $content = json_decode($content);
                        }
                    }

                    return [
                        ObjectSerializer::deserialize($content, $returnType, []),
                        $response->getStatusCode(),
                        $response->getHeaders()
                    ];
                },
                function ($exception) {
                    $response = $exception->getResponse();
                    $statusCode = $response->getStatusCode();
                    throw new ApiException(
                        sprintf(
                            '[%d] Error connecting to the API (%s)',
                            $statusCode,
                            $exception->getRequest()->getUri()
                        ),
                        $statusCode,
                        $response->getHeaders(),
                        (string) $response->getBody()
                    );
                }
            );
    }

    /**
     * Create request for operation 'getVrboListingRates'
     *
     * @param  string $id Repull listing id (numeric string) of a listing linked to Vrbo. (required)
     * @param  string|null $vrbo_listing_number The Vrbo listing number to work on, when the listing is linked to more than one Vrbo unit. (optional)
     * @param  \DateTime|null $start First night to read (default today). (optional)
     * @param  \DateTime|null $end Last night to read (default start + 13 days; at most 3 years). (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['getVrboListingRates'] to see the possible values for this operation
     *
     * @throws InvalidArgumentException
     * @return \GuzzleHttp\Psr7\Request
     */
    public function getVrboListingRatesRequest(
        string $id,
        ?string $vrbo_listing_number = null,
        ?\DateTime $start = null,
        ?\DateTime $end = null,
        string $contentType = self::contentTypes['getVrboListingRates'][0]
    ): Request
    {

        // verify the required parameter 'id' is set
        if ($id === null || (is_array($id) && count($id) === 0)) {
            throw new InvalidArgumentException(
                'Missing the required parameter $id when calling getVrboListingRates'
            );
        }





        $resourcePath = '/v1/channels/vrbo/listings/{id}/rates';
        $formParams = [];
        $queryParams = [];
        $headerParams = [];
        $httpBody = '';
        $multipart = false;

        // query params
        $queryParams = array_merge($queryParams, ObjectSerializer::toQueryValue(
            $vrbo_listing_number,
            'vrboListingNumber', // param base name
            'string', // openApiType
            'form', // style
            true, // explode
            false, // required
            null // openApiFormat
        ) ?? []);
        // query params
        $queryParams = array_merge($queryParams, ObjectSerializer::toQueryValue(
            $start,
            'start', // param base name
            'string', // openApiType
            'form', // style
            true, // explode
            false, // required
            'date' // openApiFormat
        ) ?? []);
        // query params
        $queryParams = array_merge($queryParams, ObjectSerializer::toQueryValue(
            $end,
            'end', // param base name
            'string', // openApiType
            'form', // style
            true, // explode
            false, // required
            'date' // openApiFormat
        ) ?? []);


        // path params
        if ($id !== null) {
            $resourcePath = str_replace(
                '{id}',
                ObjectSerializer::toPathValue($id),
                $resourcePath
            );
        }


        $headers = $this->headerSelector->selectHeaders(
            ['application/json', ],
            $contentType,
            $multipart
        );

        // for model (json/xml)
        if (count($formParams) > 0) {
            if ($multipart) {
                $multipartContents = [];
                foreach ($formParams as $formParamName => $formParamValue) {
                    $formParamValueItems = is_array($formParamValue) ? $formParamValue : [$formParamValue];
                    foreach ($formParamValueItems as $formParamValueItem) {
                        $multipartContents[] = [
                            'name' => $formParamName,
                            'contents' => $formParamValueItem
                        ];
                    }
                }
                // for HTTP post (form)
                $httpBody = new MultipartStream($multipartContents);

            } elseif (stripos($headers['Content-Type'], 'application/json') !== false) {
                # if Content-Type contains "application/json", json_encode the form parameters
                $httpBody = \GuzzleHttp\Utils::jsonEncode($formParams);
            } else {
                // for HTTP post (form)
                $httpBody = ObjectSerializer::buildQuery($formParams);
            }
        }

        // this endpoint requires Bearer (API Key) authentication (access token)
        if (!empty($this->config->getAccessToken())) {
            $headers['Authorization'] = 'Bearer ' . $this->config->getAccessToken();
        }

        $defaultHeaders = [];
        if ($this->config->getUserAgent()) {
            $defaultHeaders['User-Agent'] = $this->config->getUserAgent();
        }

        $headers = array_merge(
            $defaultHeaders,
            $headerParams,
            $headers
        );

        $operationHost = $this->config->getHost();
        $query = ObjectSerializer::buildQuery($queryParams);
        return new Request(
            'GET',
            $operationHost . $resourcePath . ($query ? "?{$query}" : ''),
            $headers,
            $httpBody
        );
    }

    /**
     * Operation getVrboListingRegistration
     *
     * Get the Vrbo registration / licence
     *
     * @param  string $id Repull listing id (numeric string) of a listing linked to Vrbo. (required)
     * @param  string|null $vrbo_listing_number The Vrbo listing number to work on, when the listing is linked to more than one Vrbo unit. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['getVrboListingRegistration'] to see the possible values for this operation
     *
     * @throws ApiException on non-2xx response or if the response body is not in the expected format
     * @throws InvalidArgumentException
     * @return \Repull\Model\GetVrboListingRegistration200Response|\Repull\Model\Error
     */
    public function getVrboListingRegistration(
        string $id,
        ?string $vrbo_listing_number = null,
        string $contentType = self::contentTypes['getVrboListingRegistration'][0]
    ): \Repull\Model\GetVrboListingRegistration200Response|\Repull\Model\Error
    {
        list($response) = $this->getVrboListingRegistrationWithHttpInfo($id, $vrbo_listing_number, $contentType);
        return $response;
    }

    /**
     * Operation getVrboListingRegistrationWithHttpInfo
     *
     * Get the Vrbo registration / licence
     *
     * @param  string $id Repull listing id (numeric string) of a listing linked to Vrbo. (required)
     * @param  string|null $vrbo_listing_number The Vrbo listing number to work on, when the listing is linked to more than one Vrbo unit. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['getVrboListingRegistration'] to see the possible values for this operation
     *
     * @throws ApiException on non-2xx response or if the response body is not in the expected format
     * @throws InvalidArgumentException
     * @return array of \Repull\Model\GetVrboListingRegistration200Response|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error, HTTP status code, HTTP response headers (array of strings)
     */
    public function getVrboListingRegistrationWithHttpInfo(
        string $id,
        ?string $vrbo_listing_number = null,
        string $contentType = self::contentTypes['getVrboListingRegistration'][0]
    ): array
    {
        $request = $this->getVrboListingRegistrationRequest($id, $vrbo_listing_number, $contentType);

        try {
            $options = $this->createHttpClientOption();
            try {
                $response = $this->client->send($request, $options);
            } catch (RequestException $e) {
                throw new ApiException(
                    "[{$e->getCode()}] {$e->getMessage()}",
                    (int) $e->getCode(),
                    $e->getResponse() ? $e->getResponse()->getHeaders() : null,
                    $e->getResponse() ? (string) $e->getResponse()->getBody() : null
                );
            } catch (ConnectException $e) {
                throw new ApiException(
                    "[{$e->getCode()}] {$e->getMessage()}",
                    (int) $e->getCode(),
                    null,
                    null
                );
            }

            $statusCode = $response->getStatusCode();

            switch($statusCode) {
                case 200:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\GetVrboListingRegistration200Response',
                        $request,
                        $response,
                    );
                case 401:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 403:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 404:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 409:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 422:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 500:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 502:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 503:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 504:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
            }
            

            if ($statusCode < 200 || $statusCode > 299) {
                throw new ApiException(
                    sprintf(
                        '[%d] Error connecting to the API (%s)',
                        $statusCode,
                        (string) $request->getUri()
                    ),
                    $statusCode,
                    $response->getHeaders(),
                    (string) $response->getBody()
                );
            }

            return $this->handleResponseWithDataType(
                '\Repull\Model\GetVrboListingRegistration200Response',
                $request,
                $response,
            );
        } catch (ApiException $e) {
            switch ($e->getCode()) {
                case 200:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\GetVrboListingRegistration200Response',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 401:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 403:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 404:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 409:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 422:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 500:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 502:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 503:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 504:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
            }
        
            throw $e;
        }
    }

    /**
     * Operation getVrboListingRegistrationAsync
     *
     * Get the Vrbo registration / licence
     *
     * @param  string $id Repull listing id (numeric string) of a listing linked to Vrbo. (required)
     * @param  string|null $vrbo_listing_number The Vrbo listing number to work on, when the listing is linked to more than one Vrbo unit. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['getVrboListingRegistration'] to see the possible values for this operation
     *
     * @throws InvalidArgumentException
     * @return PromiseInterface
     */
    public function getVrboListingRegistrationAsync(
        string $id,
        ?string $vrbo_listing_number = null,
        string $contentType = self::contentTypes['getVrboListingRegistration'][0]
    ): PromiseInterface
    {
        return $this->getVrboListingRegistrationAsyncWithHttpInfo($id, $vrbo_listing_number, $contentType)
            ->then(
                function ($response) {
                    return $response[0];
                }
            );
    }

    /**
     * Operation getVrboListingRegistrationAsyncWithHttpInfo
     *
     * Get the Vrbo registration / licence
     *
     * @param  string $id Repull listing id (numeric string) of a listing linked to Vrbo. (required)
     * @param  string|null $vrbo_listing_number The Vrbo listing number to work on, when the listing is linked to more than one Vrbo unit. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['getVrboListingRegistration'] to see the possible values for this operation
     *
     * @throws InvalidArgumentException
     * @return PromiseInterface
     */
    public function getVrboListingRegistrationAsyncWithHttpInfo(
        string $id,
        ?string $vrbo_listing_number = null,
        string $contentType = self::contentTypes['getVrboListingRegistration'][0]
    ): PromiseInterface
    {
        $returnType = '\Repull\Model\GetVrboListingRegistration200Response';
        $request = $this->getVrboListingRegistrationRequest($id, $vrbo_listing_number, $contentType);

        return $this->client
            ->sendAsync($request, $this->createHttpClientOption())
            ->then(
                function ($response) use ($returnType) {
                    if (in_array($returnType, ['\SplFileObject', '\Psr\Http\Message\StreamInterface'])) {
                        $content = $response->getBody(); //stream goes to serializer
                    } else {
                        $content = (string) $response->getBody();
                        if ($returnType !== 'string') {
                            $content = json_decode($content);
                        }
                    }

                    return [
                        ObjectSerializer::deserialize($content, $returnType, []),
                        $response->getStatusCode(),
                        $response->getHeaders()
                    ];
                },
                function ($exception) {
                    $response = $exception->getResponse();
                    $statusCode = $response->getStatusCode();
                    throw new ApiException(
                        sprintf(
                            '[%d] Error connecting to the API (%s)',
                            $statusCode,
                            $exception->getRequest()->getUri()
                        ),
                        $statusCode,
                        $response->getHeaders(),
                        (string) $response->getBody()
                    );
                }
            );
    }

    /**
     * Create request for operation 'getVrboListingRegistration'
     *
     * @param  string $id Repull listing id (numeric string) of a listing linked to Vrbo. (required)
     * @param  string|null $vrbo_listing_number The Vrbo listing number to work on, when the listing is linked to more than one Vrbo unit. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['getVrboListingRegistration'] to see the possible values for this operation
     *
     * @throws InvalidArgumentException
     * @return \GuzzleHttp\Psr7\Request
     */
    public function getVrboListingRegistrationRequest(
        string $id,
        ?string $vrbo_listing_number = null,
        string $contentType = self::contentTypes['getVrboListingRegistration'][0]
    ): Request
    {

        // verify the required parameter 'id' is set
        if ($id === null || (is_array($id) && count($id) === 0)) {
            throw new InvalidArgumentException(
                'Missing the required parameter $id when calling getVrboListingRegistration'
            );
        }



        $resourcePath = '/v1/channels/vrbo/listings/{id}/registration';
        $formParams = [];
        $queryParams = [];
        $headerParams = [];
        $httpBody = '';
        $multipart = false;

        // query params
        $queryParams = array_merge($queryParams, ObjectSerializer::toQueryValue(
            $vrbo_listing_number,
            'vrboListingNumber', // param base name
            'string', // openApiType
            'form', // style
            true, // explode
            false, // required
            null // openApiFormat
        ) ?? []);


        // path params
        if ($id !== null) {
            $resourcePath = str_replace(
                '{id}',
                ObjectSerializer::toPathValue($id),
                $resourcePath
            );
        }


        $headers = $this->headerSelector->selectHeaders(
            ['application/json', ],
            $contentType,
            $multipart
        );

        // for model (json/xml)
        if (count($formParams) > 0) {
            if ($multipart) {
                $multipartContents = [];
                foreach ($formParams as $formParamName => $formParamValue) {
                    $formParamValueItems = is_array($formParamValue) ? $formParamValue : [$formParamValue];
                    foreach ($formParamValueItems as $formParamValueItem) {
                        $multipartContents[] = [
                            'name' => $formParamName,
                            'contents' => $formParamValueItem
                        ];
                    }
                }
                // for HTTP post (form)
                $httpBody = new MultipartStream($multipartContents);

            } elseif (stripos($headers['Content-Type'], 'application/json') !== false) {
                # if Content-Type contains "application/json", json_encode the form parameters
                $httpBody = \GuzzleHttp\Utils::jsonEncode($formParams);
            } else {
                // for HTTP post (form)
                $httpBody = ObjectSerializer::buildQuery($formParams);
            }
        }

        // this endpoint requires Bearer (API Key) authentication (access token)
        if (!empty($this->config->getAccessToken())) {
            $headers['Authorization'] = 'Bearer ' . $this->config->getAccessToken();
        }

        $defaultHeaders = [];
        if ($this->config->getUserAgent()) {
            $defaultHeaders['User-Agent'] = $this->config->getUserAgent();
        }

        $headers = array_merge(
            $defaultHeaders,
            $headerParams,
            $headers
        );

        $operationHost = $this->config->getHost();
        $query = ObjectSerializer::buildQuery($queryParams);
        return new Request(
            'GET',
            $operationHost . $resourcePath . ($query ? "?{$query}" : ''),
            $headers,
            $httpBody
        );
    }

    /**
     * Operation getVrboListingRooms
     *
     * Get the Vrbo rooms and spaces
     *
     * @param  string $id Repull listing id (numeric string) of a listing linked to Vrbo. (required)
     * @param  string|null $vrbo_listing_number The Vrbo listing number to work on, when the listing is linked to more than one Vrbo unit. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['getVrboListingRooms'] to see the possible values for this operation
     *
     * @throws ApiException on non-2xx response or if the response body is not in the expected format
     * @throws InvalidArgumentException
     * @return \Repull\Model\GetVrboListingRooms200Response|\Repull\Model\Error
     */
    public function getVrboListingRooms(
        string $id,
        ?string $vrbo_listing_number = null,
        string $contentType = self::contentTypes['getVrboListingRooms'][0]
    ): \Repull\Model\GetVrboListingRooms200Response|\Repull\Model\Error
    {
        list($response) = $this->getVrboListingRoomsWithHttpInfo($id, $vrbo_listing_number, $contentType);
        return $response;
    }

    /**
     * Operation getVrboListingRoomsWithHttpInfo
     *
     * Get the Vrbo rooms and spaces
     *
     * @param  string $id Repull listing id (numeric string) of a listing linked to Vrbo. (required)
     * @param  string|null $vrbo_listing_number The Vrbo listing number to work on, when the listing is linked to more than one Vrbo unit. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['getVrboListingRooms'] to see the possible values for this operation
     *
     * @throws ApiException on non-2xx response or if the response body is not in the expected format
     * @throws InvalidArgumentException
     * @return array of \Repull\Model\GetVrboListingRooms200Response|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error, HTTP status code, HTTP response headers (array of strings)
     */
    public function getVrboListingRoomsWithHttpInfo(
        string $id,
        ?string $vrbo_listing_number = null,
        string $contentType = self::contentTypes['getVrboListingRooms'][0]
    ): array
    {
        $request = $this->getVrboListingRoomsRequest($id, $vrbo_listing_number, $contentType);

        try {
            $options = $this->createHttpClientOption();
            try {
                $response = $this->client->send($request, $options);
            } catch (RequestException $e) {
                throw new ApiException(
                    "[{$e->getCode()}] {$e->getMessage()}",
                    (int) $e->getCode(),
                    $e->getResponse() ? $e->getResponse()->getHeaders() : null,
                    $e->getResponse() ? (string) $e->getResponse()->getBody() : null
                );
            } catch (ConnectException $e) {
                throw new ApiException(
                    "[{$e->getCode()}] {$e->getMessage()}",
                    (int) $e->getCode(),
                    null,
                    null
                );
            }

            $statusCode = $response->getStatusCode();

            switch($statusCode) {
                case 200:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\GetVrboListingRooms200Response',
                        $request,
                        $response,
                    );
                case 401:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 403:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 404:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 409:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 422:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 500:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 502:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 503:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 504:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
            }
            

            if ($statusCode < 200 || $statusCode > 299) {
                throw new ApiException(
                    sprintf(
                        '[%d] Error connecting to the API (%s)',
                        $statusCode,
                        (string) $request->getUri()
                    ),
                    $statusCode,
                    $response->getHeaders(),
                    (string) $response->getBody()
                );
            }

            return $this->handleResponseWithDataType(
                '\Repull\Model\GetVrboListingRooms200Response',
                $request,
                $response,
            );
        } catch (ApiException $e) {
            switch ($e->getCode()) {
                case 200:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\GetVrboListingRooms200Response',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 401:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 403:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 404:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 409:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 422:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 500:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 502:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 503:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 504:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
            }
        
            throw $e;
        }
    }

    /**
     * Operation getVrboListingRoomsAsync
     *
     * Get the Vrbo rooms and spaces
     *
     * @param  string $id Repull listing id (numeric string) of a listing linked to Vrbo. (required)
     * @param  string|null $vrbo_listing_number The Vrbo listing number to work on, when the listing is linked to more than one Vrbo unit. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['getVrboListingRooms'] to see the possible values for this operation
     *
     * @throws InvalidArgumentException
     * @return PromiseInterface
     */
    public function getVrboListingRoomsAsync(
        string $id,
        ?string $vrbo_listing_number = null,
        string $contentType = self::contentTypes['getVrboListingRooms'][0]
    ): PromiseInterface
    {
        return $this->getVrboListingRoomsAsyncWithHttpInfo($id, $vrbo_listing_number, $contentType)
            ->then(
                function ($response) {
                    return $response[0];
                }
            );
    }

    /**
     * Operation getVrboListingRoomsAsyncWithHttpInfo
     *
     * Get the Vrbo rooms and spaces
     *
     * @param  string $id Repull listing id (numeric string) of a listing linked to Vrbo. (required)
     * @param  string|null $vrbo_listing_number The Vrbo listing number to work on, when the listing is linked to more than one Vrbo unit. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['getVrboListingRooms'] to see the possible values for this operation
     *
     * @throws InvalidArgumentException
     * @return PromiseInterface
     */
    public function getVrboListingRoomsAsyncWithHttpInfo(
        string $id,
        ?string $vrbo_listing_number = null,
        string $contentType = self::contentTypes['getVrboListingRooms'][0]
    ): PromiseInterface
    {
        $returnType = '\Repull\Model\GetVrboListingRooms200Response';
        $request = $this->getVrboListingRoomsRequest($id, $vrbo_listing_number, $contentType);

        return $this->client
            ->sendAsync($request, $this->createHttpClientOption())
            ->then(
                function ($response) use ($returnType) {
                    if (in_array($returnType, ['\SplFileObject', '\Psr\Http\Message\StreamInterface'])) {
                        $content = $response->getBody(); //stream goes to serializer
                    } else {
                        $content = (string) $response->getBody();
                        if ($returnType !== 'string') {
                            $content = json_decode($content);
                        }
                    }

                    return [
                        ObjectSerializer::deserialize($content, $returnType, []),
                        $response->getStatusCode(),
                        $response->getHeaders()
                    ];
                },
                function ($exception) {
                    $response = $exception->getResponse();
                    $statusCode = $response->getStatusCode();
                    throw new ApiException(
                        sprintf(
                            '[%d] Error connecting to the API (%s)',
                            $statusCode,
                            $exception->getRequest()->getUri()
                        ),
                        $statusCode,
                        $response->getHeaders(),
                        (string) $response->getBody()
                    );
                }
            );
    }

    /**
     * Create request for operation 'getVrboListingRooms'
     *
     * @param  string $id Repull listing id (numeric string) of a listing linked to Vrbo. (required)
     * @param  string|null $vrbo_listing_number The Vrbo listing number to work on, when the listing is linked to more than one Vrbo unit. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['getVrboListingRooms'] to see the possible values for this operation
     *
     * @throws InvalidArgumentException
     * @return \GuzzleHttp\Psr7\Request
     */
    public function getVrboListingRoomsRequest(
        string $id,
        ?string $vrbo_listing_number = null,
        string $contentType = self::contentTypes['getVrboListingRooms'][0]
    ): Request
    {

        // verify the required parameter 'id' is set
        if ($id === null || (is_array($id) && count($id) === 0)) {
            throw new InvalidArgumentException(
                'Missing the required parameter $id when calling getVrboListingRooms'
            );
        }



        $resourcePath = '/v1/channels/vrbo/listings/{id}/rooms';
        $formParams = [];
        $queryParams = [];
        $headerParams = [];
        $httpBody = '';
        $multipart = false;

        // query params
        $queryParams = array_merge($queryParams, ObjectSerializer::toQueryValue(
            $vrbo_listing_number,
            'vrboListingNumber', // param base name
            'string', // openApiType
            'form', // style
            true, // explode
            false, // required
            null // openApiFormat
        ) ?? []);


        // path params
        if ($id !== null) {
            $resourcePath = str_replace(
                '{id}',
                ObjectSerializer::toPathValue($id),
                $resourcePath
            );
        }


        $headers = $this->headerSelector->selectHeaders(
            ['application/json', ],
            $contentType,
            $multipart
        );

        // for model (json/xml)
        if (count($formParams) > 0) {
            if ($multipart) {
                $multipartContents = [];
                foreach ($formParams as $formParamName => $formParamValue) {
                    $formParamValueItems = is_array($formParamValue) ? $formParamValue : [$formParamValue];
                    foreach ($formParamValueItems as $formParamValueItem) {
                        $multipartContents[] = [
                            'name' => $formParamName,
                            'contents' => $formParamValueItem
                        ];
                    }
                }
                // for HTTP post (form)
                $httpBody = new MultipartStream($multipartContents);

            } elseif (stripos($headers['Content-Type'], 'application/json') !== false) {
                # if Content-Type contains "application/json", json_encode the form parameters
                $httpBody = \GuzzleHttp\Utils::jsonEncode($formParams);
            } else {
                // for HTTP post (form)
                $httpBody = ObjectSerializer::buildQuery($formParams);
            }
        }

        // this endpoint requires Bearer (API Key) authentication (access token)
        if (!empty($this->config->getAccessToken())) {
            $headers['Authorization'] = 'Bearer ' . $this->config->getAccessToken();
        }

        $defaultHeaders = [];
        if ($this->config->getUserAgent()) {
            $defaultHeaders['User-Agent'] = $this->config->getUserAgent();
        }

        $headers = array_merge(
            $defaultHeaders,
            $headerParams,
            $headers
        );

        $operationHost = $this->config->getHost();
        $query = ObjectSerializer::buildQuery($queryParams);
        return new Request(
            'GET',
            $operationHost . $resourcePath . ($query ? "?{$query}" : ''),
            $headers,
            $httpBody
        );
    }

    /**
     * Operation getVrboListingStory
     *
     * Get the Vrbo host story
     *
     * @param  string $id Repull listing id (numeric string) of a listing linked to Vrbo. (required)
     * @param  string|null $vrbo_listing_number The Vrbo listing number to work on, when the listing is linked to more than one Vrbo unit. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['getVrboListingStory'] to see the possible values for this operation
     *
     * @throws ApiException on non-2xx response or if the response body is not in the expected format
     * @throws InvalidArgumentException
     * @return \Repull\Model\GetVrboListingStory200Response|\Repull\Model\Error
     */
    public function getVrboListingStory(
        string $id,
        ?string $vrbo_listing_number = null,
        string $contentType = self::contentTypes['getVrboListingStory'][0]
    ): \Repull\Model\GetVrboListingStory200Response|\Repull\Model\Error
    {
        list($response) = $this->getVrboListingStoryWithHttpInfo($id, $vrbo_listing_number, $contentType);
        return $response;
    }

    /**
     * Operation getVrboListingStoryWithHttpInfo
     *
     * Get the Vrbo host story
     *
     * @param  string $id Repull listing id (numeric string) of a listing linked to Vrbo. (required)
     * @param  string|null $vrbo_listing_number The Vrbo listing number to work on, when the listing is linked to more than one Vrbo unit. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['getVrboListingStory'] to see the possible values for this operation
     *
     * @throws ApiException on non-2xx response or if the response body is not in the expected format
     * @throws InvalidArgumentException
     * @return array of \Repull\Model\GetVrboListingStory200Response|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error, HTTP status code, HTTP response headers (array of strings)
     */
    public function getVrboListingStoryWithHttpInfo(
        string $id,
        ?string $vrbo_listing_number = null,
        string $contentType = self::contentTypes['getVrboListingStory'][0]
    ): array
    {
        $request = $this->getVrboListingStoryRequest($id, $vrbo_listing_number, $contentType);

        try {
            $options = $this->createHttpClientOption();
            try {
                $response = $this->client->send($request, $options);
            } catch (RequestException $e) {
                throw new ApiException(
                    "[{$e->getCode()}] {$e->getMessage()}",
                    (int) $e->getCode(),
                    $e->getResponse() ? $e->getResponse()->getHeaders() : null,
                    $e->getResponse() ? (string) $e->getResponse()->getBody() : null
                );
            } catch (ConnectException $e) {
                throw new ApiException(
                    "[{$e->getCode()}] {$e->getMessage()}",
                    (int) $e->getCode(),
                    null,
                    null
                );
            }

            $statusCode = $response->getStatusCode();

            switch($statusCode) {
                case 200:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\GetVrboListingStory200Response',
                        $request,
                        $response,
                    );
                case 401:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 403:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 404:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 409:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 422:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 500:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 502:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 503:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 504:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
            }
            

            if ($statusCode < 200 || $statusCode > 299) {
                throw new ApiException(
                    sprintf(
                        '[%d] Error connecting to the API (%s)',
                        $statusCode,
                        (string) $request->getUri()
                    ),
                    $statusCode,
                    $response->getHeaders(),
                    (string) $response->getBody()
                );
            }

            return $this->handleResponseWithDataType(
                '\Repull\Model\GetVrboListingStory200Response',
                $request,
                $response,
            );
        } catch (ApiException $e) {
            switch ($e->getCode()) {
                case 200:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\GetVrboListingStory200Response',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 401:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 403:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 404:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 409:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 422:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 500:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 502:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 503:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 504:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
            }
        
            throw $e;
        }
    }

    /**
     * Operation getVrboListingStoryAsync
     *
     * Get the Vrbo host story
     *
     * @param  string $id Repull listing id (numeric string) of a listing linked to Vrbo. (required)
     * @param  string|null $vrbo_listing_number The Vrbo listing number to work on, when the listing is linked to more than one Vrbo unit. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['getVrboListingStory'] to see the possible values for this operation
     *
     * @throws InvalidArgumentException
     * @return PromiseInterface
     */
    public function getVrboListingStoryAsync(
        string $id,
        ?string $vrbo_listing_number = null,
        string $contentType = self::contentTypes['getVrboListingStory'][0]
    ): PromiseInterface
    {
        return $this->getVrboListingStoryAsyncWithHttpInfo($id, $vrbo_listing_number, $contentType)
            ->then(
                function ($response) {
                    return $response[0];
                }
            );
    }

    /**
     * Operation getVrboListingStoryAsyncWithHttpInfo
     *
     * Get the Vrbo host story
     *
     * @param  string $id Repull listing id (numeric string) of a listing linked to Vrbo. (required)
     * @param  string|null $vrbo_listing_number The Vrbo listing number to work on, when the listing is linked to more than one Vrbo unit. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['getVrboListingStory'] to see the possible values for this operation
     *
     * @throws InvalidArgumentException
     * @return PromiseInterface
     */
    public function getVrboListingStoryAsyncWithHttpInfo(
        string $id,
        ?string $vrbo_listing_number = null,
        string $contentType = self::contentTypes['getVrboListingStory'][0]
    ): PromiseInterface
    {
        $returnType = '\Repull\Model\GetVrboListingStory200Response';
        $request = $this->getVrboListingStoryRequest($id, $vrbo_listing_number, $contentType);

        return $this->client
            ->sendAsync($request, $this->createHttpClientOption())
            ->then(
                function ($response) use ($returnType) {
                    if (in_array($returnType, ['\SplFileObject', '\Psr\Http\Message\StreamInterface'])) {
                        $content = $response->getBody(); //stream goes to serializer
                    } else {
                        $content = (string) $response->getBody();
                        if ($returnType !== 'string') {
                            $content = json_decode($content);
                        }
                    }

                    return [
                        ObjectSerializer::deserialize($content, $returnType, []),
                        $response->getStatusCode(),
                        $response->getHeaders()
                    ];
                },
                function ($exception) {
                    $response = $exception->getResponse();
                    $statusCode = $response->getStatusCode();
                    throw new ApiException(
                        sprintf(
                            '[%d] Error connecting to the API (%s)',
                            $statusCode,
                            $exception->getRequest()->getUri()
                        ),
                        $statusCode,
                        $response->getHeaders(),
                        (string) $response->getBody()
                    );
                }
            );
    }

    /**
     * Create request for operation 'getVrboListingStory'
     *
     * @param  string $id Repull listing id (numeric string) of a listing linked to Vrbo. (required)
     * @param  string|null $vrbo_listing_number The Vrbo listing number to work on, when the listing is linked to more than one Vrbo unit. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['getVrboListingStory'] to see the possible values for this operation
     *
     * @throws InvalidArgumentException
     * @return \GuzzleHttp\Psr7\Request
     */
    public function getVrboListingStoryRequest(
        string $id,
        ?string $vrbo_listing_number = null,
        string $contentType = self::contentTypes['getVrboListingStory'][0]
    ): Request
    {

        // verify the required parameter 'id' is set
        if ($id === null || (is_array($id) && count($id) === 0)) {
            throw new InvalidArgumentException(
                'Missing the required parameter $id when calling getVrboListingStory'
            );
        }



        $resourcePath = '/v1/channels/vrbo/listings/{id}/story';
        $formParams = [];
        $queryParams = [];
        $headerParams = [];
        $httpBody = '';
        $multipart = false;

        // query params
        $queryParams = array_merge($queryParams, ObjectSerializer::toQueryValue(
            $vrbo_listing_number,
            'vrboListingNumber', // param base name
            'string', // openApiType
            'form', // style
            true, // explode
            false, // required
            null // openApiFormat
        ) ?? []);


        // path params
        if ($id !== null) {
            $resourcePath = str_replace(
                '{id}',
                ObjectSerializer::toPathValue($id),
                $resourcePath
            );
        }


        $headers = $this->headerSelector->selectHeaders(
            ['application/json', ],
            $contentType,
            $multipart
        );

        // for model (json/xml)
        if (count($formParams) > 0) {
            if ($multipart) {
                $multipartContents = [];
                foreach ($formParams as $formParamName => $formParamValue) {
                    $formParamValueItems = is_array($formParamValue) ? $formParamValue : [$formParamValue];
                    foreach ($formParamValueItems as $formParamValueItem) {
                        $multipartContents[] = [
                            'name' => $formParamName,
                            'contents' => $formParamValueItem
                        ];
                    }
                }
                // for HTTP post (form)
                $httpBody = new MultipartStream($multipartContents);

            } elseif (stripos($headers['Content-Type'], 'application/json') !== false) {
                # if Content-Type contains "application/json", json_encode the form parameters
                $httpBody = \GuzzleHttp\Utils::jsonEncode($formParams);
            } else {
                // for HTTP post (form)
                $httpBody = ObjectSerializer::buildQuery($formParams);
            }
        }

        // this endpoint requires Bearer (API Key) authentication (access token)
        if (!empty($this->config->getAccessToken())) {
            $headers['Authorization'] = 'Bearer ' . $this->config->getAccessToken();
        }

        $defaultHeaders = [];
        if ($this->config->getUserAgent()) {
            $defaultHeaders['User-Agent'] = $this->config->getUserAgent();
        }

        $headers = array_merge(
            $defaultHeaders,
            $headerParams,
            $headers
        );

        $operationHost = $this->config->getHost();
        $query = ObjectSerializer::buildQuery($queryParams);
        return new Request(
            'GET',
            $operationHost . $resourcePath . ($query ? "?{$query}" : ''),
            $headers,
            $httpBody
        );
    }

    /**
     * Operation getVrboListingTaxes
     *
     * Get the Vrbo taxes
     *
     * @param  string $id Repull listing id (numeric string) of a listing linked to Vrbo. (required)
     * @param  string|null $vrbo_listing_number The Vrbo listing number to work on, when the listing is linked to more than one Vrbo unit. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['getVrboListingTaxes'] to see the possible values for this operation
     *
     * @throws ApiException on non-2xx response or if the response body is not in the expected format
     * @throws InvalidArgumentException
     * @return \Repull\Model\GetVrboListingTaxes200Response|\Repull\Model\Error
     */
    public function getVrboListingTaxes(
        string $id,
        ?string $vrbo_listing_number = null,
        string $contentType = self::contentTypes['getVrboListingTaxes'][0]
    ): \Repull\Model\GetVrboListingTaxes200Response|\Repull\Model\Error
    {
        list($response) = $this->getVrboListingTaxesWithHttpInfo($id, $vrbo_listing_number, $contentType);
        return $response;
    }

    /**
     * Operation getVrboListingTaxesWithHttpInfo
     *
     * Get the Vrbo taxes
     *
     * @param  string $id Repull listing id (numeric string) of a listing linked to Vrbo. (required)
     * @param  string|null $vrbo_listing_number The Vrbo listing number to work on, when the listing is linked to more than one Vrbo unit. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['getVrboListingTaxes'] to see the possible values for this operation
     *
     * @throws ApiException on non-2xx response or if the response body is not in the expected format
     * @throws InvalidArgumentException
     * @return array of \Repull\Model\GetVrboListingTaxes200Response|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error, HTTP status code, HTTP response headers (array of strings)
     */
    public function getVrboListingTaxesWithHttpInfo(
        string $id,
        ?string $vrbo_listing_number = null,
        string $contentType = self::contentTypes['getVrboListingTaxes'][0]
    ): array
    {
        $request = $this->getVrboListingTaxesRequest($id, $vrbo_listing_number, $contentType);

        try {
            $options = $this->createHttpClientOption();
            try {
                $response = $this->client->send($request, $options);
            } catch (RequestException $e) {
                throw new ApiException(
                    "[{$e->getCode()}] {$e->getMessage()}",
                    (int) $e->getCode(),
                    $e->getResponse() ? $e->getResponse()->getHeaders() : null,
                    $e->getResponse() ? (string) $e->getResponse()->getBody() : null
                );
            } catch (ConnectException $e) {
                throw new ApiException(
                    "[{$e->getCode()}] {$e->getMessage()}",
                    (int) $e->getCode(),
                    null,
                    null
                );
            }

            $statusCode = $response->getStatusCode();

            switch($statusCode) {
                case 200:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\GetVrboListingTaxes200Response',
                        $request,
                        $response,
                    );
                case 401:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 403:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 404:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 409:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 422:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 500:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 502:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 503:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 504:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
            }
            

            if ($statusCode < 200 || $statusCode > 299) {
                throw new ApiException(
                    sprintf(
                        '[%d] Error connecting to the API (%s)',
                        $statusCode,
                        (string) $request->getUri()
                    ),
                    $statusCode,
                    $response->getHeaders(),
                    (string) $response->getBody()
                );
            }

            return $this->handleResponseWithDataType(
                '\Repull\Model\GetVrboListingTaxes200Response',
                $request,
                $response,
            );
        } catch (ApiException $e) {
            switch ($e->getCode()) {
                case 200:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\GetVrboListingTaxes200Response',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 401:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 403:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 404:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 409:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 422:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 500:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 502:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 503:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 504:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
            }
        
            throw $e;
        }
    }

    /**
     * Operation getVrboListingTaxesAsync
     *
     * Get the Vrbo taxes
     *
     * @param  string $id Repull listing id (numeric string) of a listing linked to Vrbo. (required)
     * @param  string|null $vrbo_listing_number The Vrbo listing number to work on, when the listing is linked to more than one Vrbo unit. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['getVrboListingTaxes'] to see the possible values for this operation
     *
     * @throws InvalidArgumentException
     * @return PromiseInterface
     */
    public function getVrboListingTaxesAsync(
        string $id,
        ?string $vrbo_listing_number = null,
        string $contentType = self::contentTypes['getVrboListingTaxes'][0]
    ): PromiseInterface
    {
        return $this->getVrboListingTaxesAsyncWithHttpInfo($id, $vrbo_listing_number, $contentType)
            ->then(
                function ($response) {
                    return $response[0];
                }
            );
    }

    /**
     * Operation getVrboListingTaxesAsyncWithHttpInfo
     *
     * Get the Vrbo taxes
     *
     * @param  string $id Repull listing id (numeric string) of a listing linked to Vrbo. (required)
     * @param  string|null $vrbo_listing_number The Vrbo listing number to work on, when the listing is linked to more than one Vrbo unit. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['getVrboListingTaxes'] to see the possible values for this operation
     *
     * @throws InvalidArgumentException
     * @return PromiseInterface
     */
    public function getVrboListingTaxesAsyncWithHttpInfo(
        string $id,
        ?string $vrbo_listing_number = null,
        string $contentType = self::contentTypes['getVrboListingTaxes'][0]
    ): PromiseInterface
    {
        $returnType = '\Repull\Model\GetVrboListingTaxes200Response';
        $request = $this->getVrboListingTaxesRequest($id, $vrbo_listing_number, $contentType);

        return $this->client
            ->sendAsync($request, $this->createHttpClientOption())
            ->then(
                function ($response) use ($returnType) {
                    if (in_array($returnType, ['\SplFileObject', '\Psr\Http\Message\StreamInterface'])) {
                        $content = $response->getBody(); //stream goes to serializer
                    } else {
                        $content = (string) $response->getBody();
                        if ($returnType !== 'string') {
                            $content = json_decode($content);
                        }
                    }

                    return [
                        ObjectSerializer::deserialize($content, $returnType, []),
                        $response->getStatusCode(),
                        $response->getHeaders()
                    ];
                },
                function ($exception) {
                    $response = $exception->getResponse();
                    $statusCode = $response->getStatusCode();
                    throw new ApiException(
                        sprintf(
                            '[%d] Error connecting to the API (%s)',
                            $statusCode,
                            $exception->getRequest()->getUri()
                        ),
                        $statusCode,
                        $response->getHeaders(),
                        (string) $response->getBody()
                    );
                }
            );
    }

    /**
     * Create request for operation 'getVrboListingTaxes'
     *
     * @param  string $id Repull listing id (numeric string) of a listing linked to Vrbo. (required)
     * @param  string|null $vrbo_listing_number The Vrbo listing number to work on, when the listing is linked to more than one Vrbo unit. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['getVrboListingTaxes'] to see the possible values for this operation
     *
     * @throws InvalidArgumentException
     * @return \GuzzleHttp\Psr7\Request
     */
    public function getVrboListingTaxesRequest(
        string $id,
        ?string $vrbo_listing_number = null,
        string $contentType = self::contentTypes['getVrboListingTaxes'][0]
    ): Request
    {

        // verify the required parameter 'id' is set
        if ($id === null || (is_array($id) && count($id) === 0)) {
            throw new InvalidArgumentException(
                'Missing the required parameter $id when calling getVrboListingTaxes'
            );
        }



        $resourcePath = '/v1/channels/vrbo/listings/{id}/taxes';
        $formParams = [];
        $queryParams = [];
        $headerParams = [];
        $httpBody = '';
        $multipart = false;

        // query params
        $queryParams = array_merge($queryParams, ObjectSerializer::toQueryValue(
            $vrbo_listing_number,
            'vrboListingNumber', // param base name
            'string', // openApiType
            'form', // style
            true, // explode
            false, // required
            null // openApiFormat
        ) ?? []);


        // path params
        if ($id !== null) {
            $resourcePath = str_replace(
                '{id}',
                ObjectSerializer::toPathValue($id),
                $resourcePath
            );
        }


        $headers = $this->headerSelector->selectHeaders(
            ['application/json', ],
            $contentType,
            $multipart
        );

        // for model (json/xml)
        if (count($formParams) > 0) {
            if ($multipart) {
                $multipartContents = [];
                foreach ($formParams as $formParamName => $formParamValue) {
                    $formParamValueItems = is_array($formParamValue) ? $formParamValue : [$formParamValue];
                    foreach ($formParamValueItems as $formParamValueItem) {
                        $multipartContents[] = [
                            'name' => $formParamName,
                            'contents' => $formParamValueItem
                        ];
                    }
                }
                // for HTTP post (form)
                $httpBody = new MultipartStream($multipartContents);

            } elseif (stripos($headers['Content-Type'], 'application/json') !== false) {
                # if Content-Type contains "application/json", json_encode the form parameters
                $httpBody = \GuzzleHttp\Utils::jsonEncode($formParams);
            } else {
                // for HTTP post (form)
                $httpBody = ObjectSerializer::buildQuery($formParams);
            }
        }

        // this endpoint requires Bearer (API Key) authentication (access token)
        if (!empty($this->config->getAccessToken())) {
            $headers['Authorization'] = 'Bearer ' . $this->config->getAccessToken();
        }

        $defaultHeaders = [];
        if ($this->config->getUserAgent()) {
            $defaultHeaders['User-Agent'] = $this->config->getUserAgent();
        }

        $headers = array_merge(
            $defaultHeaders,
            $headerParams,
            $headers
        );

        $operationHost = $this->config->getHost();
        $query = ObjectSerializer::buildQuery($queryParams);
        return new Request(
            'GET',
            $operationHost . $resourcePath . ($query ? "?{$query}" : ''),
            $headers,
            $httpBody
        );
    }

    /**
     * Operation getVrboListingTranslations
     *
     * Get the Vrbo manual translations
     *
     * @param  string $id Repull listing id (numeric string) of a listing linked to Vrbo. (required)
     * @param  string|null $vrbo_listing_number The Vrbo listing number to work on, when the listing is linked to more than one Vrbo unit. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['getVrboListingTranslations'] to see the possible values for this operation
     *
     * @throws ApiException on non-2xx response or if the response body is not in the expected format
     * @throws InvalidArgumentException
     * @return \Repull\Model\GetVrboListingTranslations200Response|\Repull\Model\Error
     */
    public function getVrboListingTranslations(
        string $id,
        ?string $vrbo_listing_number = null,
        string $contentType = self::contentTypes['getVrboListingTranslations'][0]
    ): \Repull\Model\GetVrboListingTranslations200Response|\Repull\Model\Error
    {
        list($response) = $this->getVrboListingTranslationsWithHttpInfo($id, $vrbo_listing_number, $contentType);
        return $response;
    }

    /**
     * Operation getVrboListingTranslationsWithHttpInfo
     *
     * Get the Vrbo manual translations
     *
     * @param  string $id Repull listing id (numeric string) of a listing linked to Vrbo. (required)
     * @param  string|null $vrbo_listing_number The Vrbo listing number to work on, when the listing is linked to more than one Vrbo unit. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['getVrboListingTranslations'] to see the possible values for this operation
     *
     * @throws ApiException on non-2xx response or if the response body is not in the expected format
     * @throws InvalidArgumentException
     * @return array of \Repull\Model\GetVrboListingTranslations200Response|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error, HTTP status code, HTTP response headers (array of strings)
     */
    public function getVrboListingTranslationsWithHttpInfo(
        string $id,
        ?string $vrbo_listing_number = null,
        string $contentType = self::contentTypes['getVrboListingTranslations'][0]
    ): array
    {
        $request = $this->getVrboListingTranslationsRequest($id, $vrbo_listing_number, $contentType);

        try {
            $options = $this->createHttpClientOption();
            try {
                $response = $this->client->send($request, $options);
            } catch (RequestException $e) {
                throw new ApiException(
                    "[{$e->getCode()}] {$e->getMessage()}",
                    (int) $e->getCode(),
                    $e->getResponse() ? $e->getResponse()->getHeaders() : null,
                    $e->getResponse() ? (string) $e->getResponse()->getBody() : null
                );
            } catch (ConnectException $e) {
                throw new ApiException(
                    "[{$e->getCode()}] {$e->getMessage()}",
                    (int) $e->getCode(),
                    null,
                    null
                );
            }

            $statusCode = $response->getStatusCode();

            switch($statusCode) {
                case 200:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\GetVrboListingTranslations200Response',
                        $request,
                        $response,
                    );
                case 401:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 403:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 404:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 409:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 422:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 500:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 502:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 503:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 504:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
            }
            

            if ($statusCode < 200 || $statusCode > 299) {
                throw new ApiException(
                    sprintf(
                        '[%d] Error connecting to the API (%s)',
                        $statusCode,
                        (string) $request->getUri()
                    ),
                    $statusCode,
                    $response->getHeaders(),
                    (string) $response->getBody()
                );
            }

            return $this->handleResponseWithDataType(
                '\Repull\Model\GetVrboListingTranslations200Response',
                $request,
                $response,
            );
        } catch (ApiException $e) {
            switch ($e->getCode()) {
                case 200:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\GetVrboListingTranslations200Response',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 401:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 403:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 404:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 409:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 422:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 500:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 502:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 503:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 504:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
            }
        
            throw $e;
        }
    }

    /**
     * Operation getVrboListingTranslationsAsync
     *
     * Get the Vrbo manual translations
     *
     * @param  string $id Repull listing id (numeric string) of a listing linked to Vrbo. (required)
     * @param  string|null $vrbo_listing_number The Vrbo listing number to work on, when the listing is linked to more than one Vrbo unit. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['getVrboListingTranslations'] to see the possible values for this operation
     *
     * @throws InvalidArgumentException
     * @return PromiseInterface
     */
    public function getVrboListingTranslationsAsync(
        string $id,
        ?string $vrbo_listing_number = null,
        string $contentType = self::contentTypes['getVrboListingTranslations'][0]
    ): PromiseInterface
    {
        return $this->getVrboListingTranslationsAsyncWithHttpInfo($id, $vrbo_listing_number, $contentType)
            ->then(
                function ($response) {
                    return $response[0];
                }
            );
    }

    /**
     * Operation getVrboListingTranslationsAsyncWithHttpInfo
     *
     * Get the Vrbo manual translations
     *
     * @param  string $id Repull listing id (numeric string) of a listing linked to Vrbo. (required)
     * @param  string|null $vrbo_listing_number The Vrbo listing number to work on, when the listing is linked to more than one Vrbo unit. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['getVrboListingTranslations'] to see the possible values for this operation
     *
     * @throws InvalidArgumentException
     * @return PromiseInterface
     */
    public function getVrboListingTranslationsAsyncWithHttpInfo(
        string $id,
        ?string $vrbo_listing_number = null,
        string $contentType = self::contentTypes['getVrboListingTranslations'][0]
    ): PromiseInterface
    {
        $returnType = '\Repull\Model\GetVrboListingTranslations200Response';
        $request = $this->getVrboListingTranslationsRequest($id, $vrbo_listing_number, $contentType);

        return $this->client
            ->sendAsync($request, $this->createHttpClientOption())
            ->then(
                function ($response) use ($returnType) {
                    if (in_array($returnType, ['\SplFileObject', '\Psr\Http\Message\StreamInterface'])) {
                        $content = $response->getBody(); //stream goes to serializer
                    } else {
                        $content = (string) $response->getBody();
                        if ($returnType !== 'string') {
                            $content = json_decode($content);
                        }
                    }

                    return [
                        ObjectSerializer::deserialize($content, $returnType, []),
                        $response->getStatusCode(),
                        $response->getHeaders()
                    ];
                },
                function ($exception) {
                    $response = $exception->getResponse();
                    $statusCode = $response->getStatusCode();
                    throw new ApiException(
                        sprintf(
                            '[%d] Error connecting to the API (%s)',
                            $statusCode,
                            $exception->getRequest()->getUri()
                        ),
                        $statusCode,
                        $response->getHeaders(),
                        (string) $response->getBody()
                    );
                }
            );
    }

    /**
     * Create request for operation 'getVrboListingTranslations'
     *
     * @param  string $id Repull listing id (numeric string) of a listing linked to Vrbo. (required)
     * @param  string|null $vrbo_listing_number The Vrbo listing number to work on, when the listing is linked to more than one Vrbo unit. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['getVrboListingTranslations'] to see the possible values for this operation
     *
     * @throws InvalidArgumentException
     * @return \GuzzleHttp\Psr7\Request
     */
    public function getVrboListingTranslationsRequest(
        string $id,
        ?string $vrbo_listing_number = null,
        string $contentType = self::contentTypes['getVrboListingTranslations'][0]
    ): Request
    {

        // verify the required parameter 'id' is set
        if ($id === null || (is_array($id) && count($id) === 0)) {
            throw new InvalidArgumentException(
                'Missing the required parameter $id when calling getVrboListingTranslations'
            );
        }



        $resourcePath = '/v1/channels/vrbo/listings/{id}/translations';
        $formParams = [];
        $queryParams = [];
        $headerParams = [];
        $httpBody = '';
        $multipart = false;

        // query params
        $queryParams = array_merge($queryParams, ObjectSerializer::toQueryValue(
            $vrbo_listing_number,
            'vrboListingNumber', // param base name
            'string', // openApiType
            'form', // style
            true, // explode
            false, // required
            null // openApiFormat
        ) ?? []);


        // path params
        if ($id !== null) {
            $resourcePath = str_replace(
                '{id}',
                ObjectSerializer::toPathValue($id),
                $resourcePath
            );
        }


        $headers = $this->headerSelector->selectHeaders(
            ['application/json', ],
            $contentType,
            $multipart
        );

        // for model (json/xml)
        if (count($formParams) > 0) {
            if ($multipart) {
                $multipartContents = [];
                foreach ($formParams as $formParamName => $formParamValue) {
                    $formParamValueItems = is_array($formParamValue) ? $formParamValue : [$formParamValue];
                    foreach ($formParamValueItems as $formParamValueItem) {
                        $multipartContents[] = [
                            'name' => $formParamName,
                            'contents' => $formParamValueItem
                        ];
                    }
                }
                // for HTTP post (form)
                $httpBody = new MultipartStream($multipartContents);

            } elseif (stripos($headers['Content-Type'], 'application/json') !== false) {
                # if Content-Type contains "application/json", json_encode the form parameters
                $httpBody = \GuzzleHttp\Utils::jsonEncode($formParams);
            } else {
                // for HTTP post (form)
                $httpBody = ObjectSerializer::buildQuery($formParams);
            }
        }

        // this endpoint requires Bearer (API Key) authentication (access token)
        if (!empty($this->config->getAccessToken())) {
            $headers['Authorization'] = 'Bearer ' . $this->config->getAccessToken();
        }

        $defaultHeaders = [];
        if ($this->config->getUserAgent()) {
            $defaultHeaders['User-Agent'] = $this->config->getUserAgent();
        }

        $headers = array_merge(
            $defaultHeaders,
            $headerParams,
            $headers
        );

        $operationHost = $this->config->getHost();
        $query = ObjectSerializer::buildQuery($queryParams);
        return new Request(
            'GET',
            $operationHost . $resourcePath . ($query ? "?{$query}" : ''),
            $headers,
            $httpBody
        );
    }

    /**
     * Operation getVrboListingVideos
     *
     * Get the Vrbo videos
     *
     * @param  string $id Repull listing id (numeric string) of a listing linked to Vrbo. (required)
     * @param  string|null $vrbo_listing_number The Vrbo listing number to work on, when the listing is linked to more than one Vrbo unit. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['getVrboListingVideos'] to see the possible values for this operation
     *
     * @throws ApiException on non-2xx response or if the response body is not in the expected format
     * @throws InvalidArgumentException
     * @return \Repull\Model\GetVrboListingVideos200Response|\Repull\Model\Error
     */
    public function getVrboListingVideos(
        string $id,
        ?string $vrbo_listing_number = null,
        string $contentType = self::contentTypes['getVrboListingVideos'][0]
    ): \Repull\Model\GetVrboListingVideos200Response|\Repull\Model\Error
    {
        list($response) = $this->getVrboListingVideosWithHttpInfo($id, $vrbo_listing_number, $contentType);
        return $response;
    }

    /**
     * Operation getVrboListingVideosWithHttpInfo
     *
     * Get the Vrbo videos
     *
     * @param  string $id Repull listing id (numeric string) of a listing linked to Vrbo. (required)
     * @param  string|null $vrbo_listing_number The Vrbo listing number to work on, when the listing is linked to more than one Vrbo unit. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['getVrboListingVideos'] to see the possible values for this operation
     *
     * @throws ApiException on non-2xx response or if the response body is not in the expected format
     * @throws InvalidArgumentException
     * @return array of \Repull\Model\GetVrboListingVideos200Response|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error, HTTP status code, HTTP response headers (array of strings)
     */
    public function getVrboListingVideosWithHttpInfo(
        string $id,
        ?string $vrbo_listing_number = null,
        string $contentType = self::contentTypes['getVrboListingVideos'][0]
    ): array
    {
        $request = $this->getVrboListingVideosRequest($id, $vrbo_listing_number, $contentType);

        try {
            $options = $this->createHttpClientOption();
            try {
                $response = $this->client->send($request, $options);
            } catch (RequestException $e) {
                throw new ApiException(
                    "[{$e->getCode()}] {$e->getMessage()}",
                    (int) $e->getCode(),
                    $e->getResponse() ? $e->getResponse()->getHeaders() : null,
                    $e->getResponse() ? (string) $e->getResponse()->getBody() : null
                );
            } catch (ConnectException $e) {
                throw new ApiException(
                    "[{$e->getCode()}] {$e->getMessage()}",
                    (int) $e->getCode(),
                    null,
                    null
                );
            }

            $statusCode = $response->getStatusCode();

            switch($statusCode) {
                case 200:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\GetVrboListingVideos200Response',
                        $request,
                        $response,
                    );
                case 401:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 403:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 404:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 409:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 422:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 500:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 502:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 503:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 504:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
            }
            

            if ($statusCode < 200 || $statusCode > 299) {
                throw new ApiException(
                    sprintf(
                        '[%d] Error connecting to the API (%s)',
                        $statusCode,
                        (string) $request->getUri()
                    ),
                    $statusCode,
                    $response->getHeaders(),
                    (string) $response->getBody()
                );
            }

            return $this->handleResponseWithDataType(
                '\Repull\Model\GetVrboListingVideos200Response',
                $request,
                $response,
            );
        } catch (ApiException $e) {
            switch ($e->getCode()) {
                case 200:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\GetVrboListingVideos200Response',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 401:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 403:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 404:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 409:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 422:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 500:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 502:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 503:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 504:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
            }
        
            throw $e;
        }
    }

    /**
     * Operation getVrboListingVideosAsync
     *
     * Get the Vrbo videos
     *
     * @param  string $id Repull listing id (numeric string) of a listing linked to Vrbo. (required)
     * @param  string|null $vrbo_listing_number The Vrbo listing number to work on, when the listing is linked to more than one Vrbo unit. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['getVrboListingVideos'] to see the possible values for this operation
     *
     * @throws InvalidArgumentException
     * @return PromiseInterface
     */
    public function getVrboListingVideosAsync(
        string $id,
        ?string $vrbo_listing_number = null,
        string $contentType = self::contentTypes['getVrboListingVideos'][0]
    ): PromiseInterface
    {
        return $this->getVrboListingVideosAsyncWithHttpInfo($id, $vrbo_listing_number, $contentType)
            ->then(
                function ($response) {
                    return $response[0];
                }
            );
    }

    /**
     * Operation getVrboListingVideosAsyncWithHttpInfo
     *
     * Get the Vrbo videos
     *
     * @param  string $id Repull listing id (numeric string) of a listing linked to Vrbo. (required)
     * @param  string|null $vrbo_listing_number The Vrbo listing number to work on, when the listing is linked to more than one Vrbo unit. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['getVrboListingVideos'] to see the possible values for this operation
     *
     * @throws InvalidArgumentException
     * @return PromiseInterface
     */
    public function getVrboListingVideosAsyncWithHttpInfo(
        string $id,
        ?string $vrbo_listing_number = null,
        string $contentType = self::contentTypes['getVrboListingVideos'][0]
    ): PromiseInterface
    {
        $returnType = '\Repull\Model\GetVrboListingVideos200Response';
        $request = $this->getVrboListingVideosRequest($id, $vrbo_listing_number, $contentType);

        return $this->client
            ->sendAsync($request, $this->createHttpClientOption())
            ->then(
                function ($response) use ($returnType) {
                    if (in_array($returnType, ['\SplFileObject', '\Psr\Http\Message\StreamInterface'])) {
                        $content = $response->getBody(); //stream goes to serializer
                    } else {
                        $content = (string) $response->getBody();
                        if ($returnType !== 'string') {
                            $content = json_decode($content);
                        }
                    }

                    return [
                        ObjectSerializer::deserialize($content, $returnType, []),
                        $response->getStatusCode(),
                        $response->getHeaders()
                    ];
                },
                function ($exception) {
                    $response = $exception->getResponse();
                    $statusCode = $response->getStatusCode();
                    throw new ApiException(
                        sprintf(
                            '[%d] Error connecting to the API (%s)',
                            $statusCode,
                            $exception->getRequest()->getUri()
                        ),
                        $statusCode,
                        $response->getHeaders(),
                        (string) $response->getBody()
                    );
                }
            );
    }

    /**
     * Create request for operation 'getVrboListingVideos'
     *
     * @param  string $id Repull listing id (numeric string) of a listing linked to Vrbo. (required)
     * @param  string|null $vrbo_listing_number The Vrbo listing number to work on, when the listing is linked to more than one Vrbo unit. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['getVrboListingVideos'] to see the possible values for this operation
     *
     * @throws InvalidArgumentException
     * @return \GuzzleHttp\Psr7\Request
     */
    public function getVrboListingVideosRequest(
        string $id,
        ?string $vrbo_listing_number = null,
        string $contentType = self::contentTypes['getVrboListingVideos'][0]
    ): Request
    {

        // verify the required parameter 'id' is set
        if ($id === null || (is_array($id) && count($id) === 0)) {
            throw new InvalidArgumentException(
                'Missing the required parameter $id when calling getVrboListingVideos'
            );
        }



        $resourcePath = '/v1/channels/vrbo/listings/{id}/videos';
        $formParams = [];
        $queryParams = [];
        $headerParams = [];
        $httpBody = '';
        $multipart = false;

        // query params
        $queryParams = array_merge($queryParams, ObjectSerializer::toQueryValue(
            $vrbo_listing_number,
            'vrboListingNumber', // param base name
            'string', // openApiType
            'form', // style
            true, // explode
            false, // required
            null // openApiFormat
        ) ?? []);


        // path params
        if ($id !== null) {
            $resourcePath = str_replace(
                '{id}',
                ObjectSerializer::toPathValue($id),
                $resourcePath
            );
        }


        $headers = $this->headerSelector->selectHeaders(
            ['application/json', ],
            $contentType,
            $multipart
        );

        // for model (json/xml)
        if (count($formParams) > 0) {
            if ($multipart) {
                $multipartContents = [];
                foreach ($formParams as $formParamName => $formParamValue) {
                    $formParamValueItems = is_array($formParamValue) ? $formParamValue : [$formParamValue];
                    foreach ($formParamValueItems as $formParamValueItem) {
                        $multipartContents[] = [
                            'name' => $formParamName,
                            'contents' => $formParamValueItem
                        ];
                    }
                }
                // for HTTP post (form)
                $httpBody = new MultipartStream($multipartContents);

            } elseif (stripos($headers['Content-Type'], 'application/json') !== false) {
                # if Content-Type contains "application/json", json_encode the form parameters
                $httpBody = \GuzzleHttp\Utils::jsonEncode($formParams);
            } else {
                // for HTTP post (form)
                $httpBody = ObjectSerializer::buildQuery($formParams);
            }
        }

        // this endpoint requires Bearer (API Key) authentication (access token)
        if (!empty($this->config->getAccessToken())) {
            $headers['Authorization'] = 'Bearer ' . $this->config->getAccessToken();
        }

        $defaultHeaders = [];
        if ($this->config->getUserAgent()) {
            $defaultHeaders['User-Agent'] = $this->config->getUserAgent();
        }

        $headers = array_merge(
            $defaultHeaders,
            $headerParams,
            $headers
        );

        $operationHost = $this->config->getHost();
        $query = ObjectSerializer::buildQuery($queryParams);
        return new Request(
            'GET',
            $operationHost . $resourcePath . ($query ? "?{$query}" : ''),
            $headers,
            $httpBody
        );
    }

    /**
     * Operation getVrboListingWelcomeGuide
     *
     * Get the Vrbo welcome guide
     *
     * @param  string $id Repull listing id (numeric string) of a listing linked to Vrbo. (required)
     * @param  string|null $vrbo_listing_number The Vrbo listing number to work on, when the listing is linked to more than one Vrbo unit. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['getVrboListingWelcomeGuide'] to see the possible values for this operation
     *
     * @throws ApiException on non-2xx response or if the response body is not in the expected format
     * @throws InvalidArgumentException
     * @return \Repull\Model\GetVrboListingWelcomeGuide200Response|\Repull\Model\Error
     */
    public function getVrboListingWelcomeGuide(
        string $id,
        ?string $vrbo_listing_number = null,
        string $contentType = self::contentTypes['getVrboListingWelcomeGuide'][0]
    ): \Repull\Model\GetVrboListingWelcomeGuide200Response|\Repull\Model\Error
    {
        list($response) = $this->getVrboListingWelcomeGuideWithHttpInfo($id, $vrbo_listing_number, $contentType);
        return $response;
    }

    /**
     * Operation getVrboListingWelcomeGuideWithHttpInfo
     *
     * Get the Vrbo welcome guide
     *
     * @param  string $id Repull listing id (numeric string) of a listing linked to Vrbo. (required)
     * @param  string|null $vrbo_listing_number The Vrbo listing number to work on, when the listing is linked to more than one Vrbo unit. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['getVrboListingWelcomeGuide'] to see the possible values for this operation
     *
     * @throws ApiException on non-2xx response or if the response body is not in the expected format
     * @throws InvalidArgumentException
     * @return array of \Repull\Model\GetVrboListingWelcomeGuide200Response|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error, HTTP status code, HTTP response headers (array of strings)
     */
    public function getVrboListingWelcomeGuideWithHttpInfo(
        string $id,
        ?string $vrbo_listing_number = null,
        string $contentType = self::contentTypes['getVrboListingWelcomeGuide'][0]
    ): array
    {
        $request = $this->getVrboListingWelcomeGuideRequest($id, $vrbo_listing_number, $contentType);

        try {
            $options = $this->createHttpClientOption();
            try {
                $response = $this->client->send($request, $options);
            } catch (RequestException $e) {
                throw new ApiException(
                    "[{$e->getCode()}] {$e->getMessage()}",
                    (int) $e->getCode(),
                    $e->getResponse() ? $e->getResponse()->getHeaders() : null,
                    $e->getResponse() ? (string) $e->getResponse()->getBody() : null
                );
            } catch (ConnectException $e) {
                throw new ApiException(
                    "[{$e->getCode()}] {$e->getMessage()}",
                    (int) $e->getCode(),
                    null,
                    null
                );
            }

            $statusCode = $response->getStatusCode();

            switch($statusCode) {
                case 200:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\GetVrboListingWelcomeGuide200Response',
                        $request,
                        $response,
                    );
                case 401:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 403:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 404:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 409:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 422:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 500:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 502:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 503:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 504:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
            }
            

            if ($statusCode < 200 || $statusCode > 299) {
                throw new ApiException(
                    sprintf(
                        '[%d] Error connecting to the API (%s)',
                        $statusCode,
                        (string) $request->getUri()
                    ),
                    $statusCode,
                    $response->getHeaders(),
                    (string) $response->getBody()
                );
            }

            return $this->handleResponseWithDataType(
                '\Repull\Model\GetVrboListingWelcomeGuide200Response',
                $request,
                $response,
            );
        } catch (ApiException $e) {
            switch ($e->getCode()) {
                case 200:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\GetVrboListingWelcomeGuide200Response',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 401:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 403:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 404:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 409:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 422:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 500:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 502:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 503:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 504:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
            }
        
            throw $e;
        }
    }

    /**
     * Operation getVrboListingWelcomeGuideAsync
     *
     * Get the Vrbo welcome guide
     *
     * @param  string $id Repull listing id (numeric string) of a listing linked to Vrbo. (required)
     * @param  string|null $vrbo_listing_number The Vrbo listing number to work on, when the listing is linked to more than one Vrbo unit. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['getVrboListingWelcomeGuide'] to see the possible values for this operation
     *
     * @throws InvalidArgumentException
     * @return PromiseInterface
     */
    public function getVrboListingWelcomeGuideAsync(
        string $id,
        ?string $vrbo_listing_number = null,
        string $contentType = self::contentTypes['getVrboListingWelcomeGuide'][0]
    ): PromiseInterface
    {
        return $this->getVrboListingWelcomeGuideAsyncWithHttpInfo($id, $vrbo_listing_number, $contentType)
            ->then(
                function ($response) {
                    return $response[0];
                }
            );
    }

    /**
     * Operation getVrboListingWelcomeGuideAsyncWithHttpInfo
     *
     * Get the Vrbo welcome guide
     *
     * @param  string $id Repull listing id (numeric string) of a listing linked to Vrbo. (required)
     * @param  string|null $vrbo_listing_number The Vrbo listing number to work on, when the listing is linked to more than one Vrbo unit. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['getVrboListingWelcomeGuide'] to see the possible values for this operation
     *
     * @throws InvalidArgumentException
     * @return PromiseInterface
     */
    public function getVrboListingWelcomeGuideAsyncWithHttpInfo(
        string $id,
        ?string $vrbo_listing_number = null,
        string $contentType = self::contentTypes['getVrboListingWelcomeGuide'][0]
    ): PromiseInterface
    {
        $returnType = '\Repull\Model\GetVrboListingWelcomeGuide200Response';
        $request = $this->getVrboListingWelcomeGuideRequest($id, $vrbo_listing_number, $contentType);

        return $this->client
            ->sendAsync($request, $this->createHttpClientOption())
            ->then(
                function ($response) use ($returnType) {
                    if (in_array($returnType, ['\SplFileObject', '\Psr\Http\Message\StreamInterface'])) {
                        $content = $response->getBody(); //stream goes to serializer
                    } else {
                        $content = (string) $response->getBody();
                        if ($returnType !== 'string') {
                            $content = json_decode($content);
                        }
                    }

                    return [
                        ObjectSerializer::deserialize($content, $returnType, []),
                        $response->getStatusCode(),
                        $response->getHeaders()
                    ];
                },
                function ($exception) {
                    $response = $exception->getResponse();
                    $statusCode = $response->getStatusCode();
                    throw new ApiException(
                        sprintf(
                            '[%d] Error connecting to the API (%s)',
                            $statusCode,
                            $exception->getRequest()->getUri()
                        ),
                        $statusCode,
                        $response->getHeaders(),
                        (string) $response->getBody()
                    );
                }
            );
    }

    /**
     * Create request for operation 'getVrboListingWelcomeGuide'
     *
     * @param  string $id Repull listing id (numeric string) of a listing linked to Vrbo. (required)
     * @param  string|null $vrbo_listing_number The Vrbo listing number to work on, when the listing is linked to more than one Vrbo unit. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['getVrboListingWelcomeGuide'] to see the possible values for this operation
     *
     * @throws InvalidArgumentException
     * @return \GuzzleHttp\Psr7\Request
     */
    public function getVrboListingWelcomeGuideRequest(
        string $id,
        ?string $vrbo_listing_number = null,
        string $contentType = self::contentTypes['getVrboListingWelcomeGuide'][0]
    ): Request
    {

        // verify the required parameter 'id' is set
        if ($id === null || (is_array($id) && count($id) === 0)) {
            throw new InvalidArgumentException(
                'Missing the required parameter $id when calling getVrboListingWelcomeGuide'
            );
        }



        $resourcePath = '/v1/channels/vrbo/listings/{id}/welcome-guide';
        $formParams = [];
        $queryParams = [];
        $headerParams = [];
        $httpBody = '';
        $multipart = false;

        // query params
        $queryParams = array_merge($queryParams, ObjectSerializer::toQueryValue(
            $vrbo_listing_number,
            'vrboListingNumber', // param base name
            'string', // openApiType
            'form', // style
            true, // explode
            false, // required
            null // openApiFormat
        ) ?? []);


        // path params
        if ($id !== null) {
            $resourcePath = str_replace(
                '{id}',
                ObjectSerializer::toPathValue($id),
                $resourcePath
            );
        }


        $headers = $this->headerSelector->selectHeaders(
            ['application/json', ],
            $contentType,
            $multipart
        );

        // for model (json/xml)
        if (count($formParams) > 0) {
            if ($multipart) {
                $multipartContents = [];
                foreach ($formParams as $formParamName => $formParamValue) {
                    $formParamValueItems = is_array($formParamValue) ? $formParamValue : [$formParamValue];
                    foreach ($formParamValueItems as $formParamValueItem) {
                        $multipartContents[] = [
                            'name' => $formParamName,
                            'contents' => $formParamValueItem
                        ];
                    }
                }
                // for HTTP post (form)
                $httpBody = new MultipartStream($multipartContents);

            } elseif (stripos($headers['Content-Type'], 'application/json') !== false) {
                # if Content-Type contains "application/json", json_encode the form parameters
                $httpBody = \GuzzleHttp\Utils::jsonEncode($formParams);
            } else {
                // for HTTP post (form)
                $httpBody = ObjectSerializer::buildQuery($formParams);
            }
        }

        // this endpoint requires Bearer (API Key) authentication (access token)
        if (!empty($this->config->getAccessToken())) {
            $headers['Authorization'] = 'Bearer ' . $this->config->getAccessToken();
        }

        $defaultHeaders = [];
        if ($this->config->getUserAgent()) {
            $defaultHeaders['User-Agent'] = $this->config->getUserAgent();
        }

        $headers = array_merge(
            $defaultHeaders,
            $headerParams,
            $headers
        );

        $operationHost = $this->config->getHost();
        $query = ObjectSerializer::buildQuery($queryParams);
        return new Request(
            'GET',
            $operationHost . $resourcePath . ($query ? "?{$query}" : ''),
            $headers,
            $httpBody
        );
    }

    /**
     * Operation listVrboListings
     *
     * List VRBO listings
     *
     * @param  string|null $status &#x60;active&#x60; (default) leaves inactive listings out. &#x60;inactive&#x60; returns only them and &#x60;all&#x60; returns both. An inactive listing comes back with identity fields only (ids, &#x60;name&#x60;, &#x60;city&#x60;, &#x60;status&#x60;, &#x60;inactiveReason&#x60;, its account), which is enough to show what can be activated. Every row carries &#x60;status&#x60;. (optional, default to 'active')
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['listVrboListings'] to see the possible values for this operation
     *
     * @throws ApiException on non-2xx response or if the response body is not in the expected format
     * @throws InvalidArgumentException
     * @return \Repull\Model\VrboListing[]
     */
    public function listVrboListings(
        ?string $status = 'active',
        string $contentType = self::contentTypes['listVrboListings'][0]
    ): array
    {
        list($response) = $this->listVrboListingsWithHttpInfo($status, $contentType);
        return $response;
    }

    /**
     * Operation listVrboListingsWithHttpInfo
     *
     * List VRBO listings
     *
     * @param  string|null $status &#x60;active&#x60; (default) leaves inactive listings out. &#x60;inactive&#x60; returns only them and &#x60;all&#x60; returns both. An inactive listing comes back with identity fields only (ids, &#x60;name&#x60;, &#x60;city&#x60;, &#x60;status&#x60;, &#x60;inactiveReason&#x60;, its account), which is enough to show what can be activated. Every row carries &#x60;status&#x60;. (optional, default to 'active')
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['listVrboListings'] to see the possible values for this operation
     *
     * @throws ApiException on non-2xx response or if the response body is not in the expected format
     * @throws InvalidArgumentException
     * @return array of \Repull\Model\VrboListing[], HTTP status code, HTTP response headers (array of strings)
     */
    public function listVrboListingsWithHttpInfo(
        ?string $status = 'active',
        string $contentType = self::contentTypes['listVrboListings'][0]
    ): array
    {
        $request = $this->listVrboListingsRequest($status, $contentType);

        try {
            $options = $this->createHttpClientOption();
            try {
                $response = $this->client->send($request, $options);
            } catch (RequestException $e) {
                throw new ApiException(
                    "[{$e->getCode()}] {$e->getMessage()}",
                    (int) $e->getCode(),
                    $e->getResponse() ? $e->getResponse()->getHeaders() : null,
                    $e->getResponse() ? (string) $e->getResponse()->getBody() : null
                );
            } catch (ConnectException $e) {
                throw new ApiException(
                    "[{$e->getCode()}] {$e->getMessage()}",
                    (int) $e->getCode(),
                    null,
                    null
                );
            }

            $statusCode = $response->getStatusCode();

            switch($statusCode) {
                case 200:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\VrboListing[]',
                        $request,
                        $response,
                    );
            }
            

            if ($statusCode < 200 || $statusCode > 299) {
                throw new ApiException(
                    sprintf(
                        '[%d] Error connecting to the API (%s)',
                        $statusCode,
                        (string) $request->getUri()
                    ),
                    $statusCode,
                    $response->getHeaders(),
                    (string) $response->getBody()
                );
            }

            return $this->handleResponseWithDataType(
                '\Repull\Model\VrboListing[]',
                $request,
                $response,
            );
        } catch (ApiException $e) {
            switch ($e->getCode()) {
                case 200:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\VrboListing[]',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
            }
        
            throw $e;
        }
    }

    /**
     * Operation listVrboListingsAsync
     *
     * List VRBO listings
     *
     * @param  string|null $status &#x60;active&#x60; (default) leaves inactive listings out. &#x60;inactive&#x60; returns only them and &#x60;all&#x60; returns both. An inactive listing comes back with identity fields only (ids, &#x60;name&#x60;, &#x60;city&#x60;, &#x60;status&#x60;, &#x60;inactiveReason&#x60;, its account), which is enough to show what can be activated. Every row carries &#x60;status&#x60;. (optional, default to 'active')
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['listVrboListings'] to see the possible values for this operation
     *
     * @throws InvalidArgumentException
     * @return PromiseInterface
     */
    public function listVrboListingsAsync(
        ?string $status = 'active',
        string $contentType = self::contentTypes['listVrboListings'][0]
    ): PromiseInterface
    {
        return $this->listVrboListingsAsyncWithHttpInfo($status, $contentType)
            ->then(
                function ($response) {
                    return $response[0];
                }
            );
    }

    /**
     * Operation listVrboListingsAsyncWithHttpInfo
     *
     * List VRBO listings
     *
     * @param  string|null $status &#x60;active&#x60; (default) leaves inactive listings out. &#x60;inactive&#x60; returns only them and &#x60;all&#x60; returns both. An inactive listing comes back with identity fields only (ids, &#x60;name&#x60;, &#x60;city&#x60;, &#x60;status&#x60;, &#x60;inactiveReason&#x60;, its account), which is enough to show what can be activated. Every row carries &#x60;status&#x60;. (optional, default to 'active')
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['listVrboListings'] to see the possible values for this operation
     *
     * @throws InvalidArgumentException
     * @return PromiseInterface
     */
    public function listVrboListingsAsyncWithHttpInfo(
        ?string $status = 'active',
        string $contentType = self::contentTypes['listVrboListings'][0]
    ): PromiseInterface
    {
        $returnType = '\Repull\Model\VrboListing[]';
        $request = $this->listVrboListingsRequest($status, $contentType);

        return $this->client
            ->sendAsync($request, $this->createHttpClientOption())
            ->then(
                function ($response) use ($returnType) {
                    if (in_array($returnType, ['\SplFileObject', '\Psr\Http\Message\StreamInterface'])) {
                        $content = $response->getBody(); //stream goes to serializer
                    } else {
                        $content = (string) $response->getBody();
                        if ($returnType !== 'string') {
                            $content = json_decode($content);
                        }
                    }

                    return [
                        ObjectSerializer::deserialize($content, $returnType, []),
                        $response->getStatusCode(),
                        $response->getHeaders()
                    ];
                },
                function ($exception) {
                    $response = $exception->getResponse();
                    $statusCode = $response->getStatusCode();
                    throw new ApiException(
                        sprintf(
                            '[%d] Error connecting to the API (%s)',
                            $statusCode,
                            $exception->getRequest()->getUri()
                        ),
                        $statusCode,
                        $response->getHeaders(),
                        (string) $response->getBody()
                    );
                }
            );
    }

    /**
     * Create request for operation 'listVrboListings'
     *
     * @param  string|null $status &#x60;active&#x60; (default) leaves inactive listings out. &#x60;inactive&#x60; returns only them and &#x60;all&#x60; returns both. An inactive listing comes back with identity fields only (ids, &#x60;name&#x60;, &#x60;city&#x60;, &#x60;status&#x60;, &#x60;inactiveReason&#x60;, its account), which is enough to show what can be activated. Every row carries &#x60;status&#x60;. (optional, default to 'active')
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['listVrboListings'] to see the possible values for this operation
     *
     * @throws InvalidArgumentException
     * @return \GuzzleHttp\Psr7\Request
     */
    public function listVrboListingsRequest(
        ?string $status = 'active',
        string $contentType = self::contentTypes['listVrboListings'][0]
    ): Request
    {



        $resourcePath = '/v1/channels/vrbo/listings';
        $formParams = [];
        $queryParams = [];
        $headerParams = [];
        $httpBody = '';
        $multipart = false;

        // query params
        $queryParams = array_merge($queryParams, ObjectSerializer::toQueryValue(
            $status,
            'status', // param base name
            'string', // openApiType
            'form', // style
            true, // explode
            false, // required
            null // openApiFormat
        ) ?? []);




        $headers = $this->headerSelector->selectHeaders(
            ['application/json', ],
            $contentType,
            $multipart
        );

        // for model (json/xml)
        if (count($formParams) > 0) {
            if ($multipart) {
                $multipartContents = [];
                foreach ($formParams as $formParamName => $formParamValue) {
                    $formParamValueItems = is_array($formParamValue) ? $formParamValue : [$formParamValue];
                    foreach ($formParamValueItems as $formParamValueItem) {
                        $multipartContents[] = [
                            'name' => $formParamName,
                            'contents' => $formParamValueItem
                        ];
                    }
                }
                // for HTTP post (form)
                $httpBody = new MultipartStream($multipartContents);

            } elseif (stripos($headers['Content-Type'], 'application/json') !== false) {
                # if Content-Type contains "application/json", json_encode the form parameters
                $httpBody = \GuzzleHttp\Utils::jsonEncode($formParams);
            } else {
                // for HTTP post (form)
                $httpBody = ObjectSerializer::buildQuery($formParams);
            }
        }

        // this endpoint requires Bearer (API Key) authentication (access token)
        if (!empty($this->config->getAccessToken())) {
            $headers['Authorization'] = 'Bearer ' . $this->config->getAccessToken();
        }

        $defaultHeaders = [];
        if ($this->config->getUserAgent()) {
            $defaultHeaders['User-Agent'] = $this->config->getUserAgent();
        }

        $headers = array_merge(
            $defaultHeaders,
            $headerParams,
            $headers
        );

        $operationHost = $this->config->getHost();
        $query = ObjectSerializer::buildQuery($queryParams);
        return new Request(
            'GET',
            $operationHost . $resourcePath . ($query ? "?{$query}" : ''),
            $headers,
            $httpBody
        );
    }

    /**
     * Operation listVrboReservations
     *
     * List VRBO reservations
     *
     * @param  string|null $cursor Opaque cursor returned in the previous response&#39;s &#x60;pagination.nextCursor&#x60;. (optional)
     * @param  int|null $offset First-class alias for cursor-based pagination. Mutually exclusive with &#x60;cursor&#x60; — passing both returns 422. Accepts integers in &#x60;[0, 10000]&#x60;; deeper walks must use &#x60;cursor&#x60; (constant per-page cost). The response always includes &#x60;pagination.nextCursor&#x60; so consumers can switch from offset → cursor mid-walk for deep pagination without re-keying. (optional, default to 0)
     * @param  int|null $limit limit (optional, default to 50)
     * @param  bool|null $include_total When &#x60;true&#x60; (default), the response&#39;s &#x60;pagination.total&#x60; carries the count of rows matching the current filter, across all pages. Pass &#x60;false&#x60; to skip the count for very large workspaces where the per-page COUNT(*) cost matters. (optional, default to true)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['listVrboReservations'] to see the possible values for this operation
     *
     * @throws ApiException on non-2xx response or if the response body is not in the expected format
     * @throws InvalidArgumentException
     * @return \Repull\Model\VrboReservationListResponse
     */
    public function listVrboReservations(
        ?string $cursor = null,
        ?int $offset = 0,
        ?int $limit = 50,
        ?bool $include_total = true,
        string $contentType = self::contentTypes['listVrboReservations'][0]
    ): \Repull\Model\VrboReservationListResponse
    {
        list($response) = $this->listVrboReservationsWithHttpInfo($cursor, $offset, $limit, $include_total, $contentType);
        return $response;
    }

    /**
     * Operation listVrboReservationsWithHttpInfo
     *
     * List VRBO reservations
     *
     * @param  string|null $cursor Opaque cursor returned in the previous response&#39;s &#x60;pagination.nextCursor&#x60;. (optional)
     * @param  int|null $offset First-class alias for cursor-based pagination. Mutually exclusive with &#x60;cursor&#x60; — passing both returns 422. Accepts integers in &#x60;[0, 10000]&#x60;; deeper walks must use &#x60;cursor&#x60; (constant per-page cost). The response always includes &#x60;pagination.nextCursor&#x60; so consumers can switch from offset → cursor mid-walk for deep pagination without re-keying. (optional, default to 0)
     * @param  int|null $limit (optional, default to 50)
     * @param  bool|null $include_total When &#x60;true&#x60; (default), the response&#39;s &#x60;pagination.total&#x60; carries the count of rows matching the current filter, across all pages. Pass &#x60;false&#x60; to skip the count for very large workspaces where the per-page COUNT(*) cost matters. (optional, default to true)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['listVrboReservations'] to see the possible values for this operation
     *
     * @throws ApiException on non-2xx response or if the response body is not in the expected format
     * @throws InvalidArgumentException
     * @return array of \Repull\Model\VrboReservationListResponse, HTTP status code, HTTP response headers (array of strings)
     */
    public function listVrboReservationsWithHttpInfo(
        ?string $cursor = null,
        ?int $offset = 0,
        ?int $limit = 50,
        ?bool $include_total = true,
        string $contentType = self::contentTypes['listVrboReservations'][0]
    ): array
    {
        $request = $this->listVrboReservationsRequest($cursor, $offset, $limit, $include_total, $contentType);

        try {
            $options = $this->createHttpClientOption();
            try {
                $response = $this->client->send($request, $options);
            } catch (RequestException $e) {
                throw new ApiException(
                    "[{$e->getCode()}] {$e->getMessage()}",
                    (int) $e->getCode(),
                    $e->getResponse() ? $e->getResponse()->getHeaders() : null,
                    $e->getResponse() ? (string) $e->getResponse()->getBody() : null
                );
            } catch (ConnectException $e) {
                throw new ApiException(
                    "[{$e->getCode()}] {$e->getMessage()}",
                    (int) $e->getCode(),
                    null,
                    null
                );
            }

            $statusCode = $response->getStatusCode();

            switch($statusCode) {
                case 200:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\VrboReservationListResponse',
                        $request,
                        $response,
                    );
            }
            

            if ($statusCode < 200 || $statusCode > 299) {
                throw new ApiException(
                    sprintf(
                        '[%d] Error connecting to the API (%s)',
                        $statusCode,
                        (string) $request->getUri()
                    ),
                    $statusCode,
                    $response->getHeaders(),
                    (string) $response->getBody()
                );
            }

            return $this->handleResponseWithDataType(
                '\Repull\Model\VrboReservationListResponse',
                $request,
                $response,
            );
        } catch (ApiException $e) {
            switch ($e->getCode()) {
                case 200:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\VrboReservationListResponse',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
            }
        
            throw $e;
        }
    }

    /**
     * Operation listVrboReservationsAsync
     *
     * List VRBO reservations
     *
     * @param  string|null $cursor Opaque cursor returned in the previous response&#39;s &#x60;pagination.nextCursor&#x60;. (optional)
     * @param  int|null $offset First-class alias for cursor-based pagination. Mutually exclusive with &#x60;cursor&#x60; — passing both returns 422. Accepts integers in &#x60;[0, 10000]&#x60;; deeper walks must use &#x60;cursor&#x60; (constant per-page cost). The response always includes &#x60;pagination.nextCursor&#x60; so consumers can switch from offset → cursor mid-walk for deep pagination without re-keying. (optional, default to 0)
     * @param  int|null $limit (optional, default to 50)
     * @param  bool|null $include_total When &#x60;true&#x60; (default), the response&#39;s &#x60;pagination.total&#x60; carries the count of rows matching the current filter, across all pages. Pass &#x60;false&#x60; to skip the count for very large workspaces where the per-page COUNT(*) cost matters. (optional, default to true)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['listVrboReservations'] to see the possible values for this operation
     *
     * @throws InvalidArgumentException
     * @return PromiseInterface
     */
    public function listVrboReservationsAsync(
        ?string $cursor = null,
        ?int $offset = 0,
        ?int $limit = 50,
        ?bool $include_total = true,
        string $contentType = self::contentTypes['listVrboReservations'][0]
    ): PromiseInterface
    {
        return $this->listVrboReservationsAsyncWithHttpInfo($cursor, $offset, $limit, $include_total, $contentType)
            ->then(
                function ($response) {
                    return $response[0];
                }
            );
    }

    /**
     * Operation listVrboReservationsAsyncWithHttpInfo
     *
     * List VRBO reservations
     *
     * @param  string|null $cursor Opaque cursor returned in the previous response&#39;s &#x60;pagination.nextCursor&#x60;. (optional)
     * @param  int|null $offset First-class alias for cursor-based pagination. Mutually exclusive with &#x60;cursor&#x60; — passing both returns 422. Accepts integers in &#x60;[0, 10000]&#x60;; deeper walks must use &#x60;cursor&#x60; (constant per-page cost). The response always includes &#x60;pagination.nextCursor&#x60; so consumers can switch from offset → cursor mid-walk for deep pagination without re-keying. (optional, default to 0)
     * @param  int|null $limit (optional, default to 50)
     * @param  bool|null $include_total When &#x60;true&#x60; (default), the response&#39;s &#x60;pagination.total&#x60; carries the count of rows matching the current filter, across all pages. Pass &#x60;false&#x60; to skip the count for very large workspaces where the per-page COUNT(*) cost matters. (optional, default to true)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['listVrboReservations'] to see the possible values for this operation
     *
     * @throws InvalidArgumentException
     * @return PromiseInterface
     */
    public function listVrboReservationsAsyncWithHttpInfo(
        ?string $cursor = null,
        ?int $offset = 0,
        ?int $limit = 50,
        ?bool $include_total = true,
        string $contentType = self::contentTypes['listVrboReservations'][0]
    ): PromiseInterface
    {
        $returnType = '\Repull\Model\VrboReservationListResponse';
        $request = $this->listVrboReservationsRequest($cursor, $offset, $limit, $include_total, $contentType);

        return $this->client
            ->sendAsync($request, $this->createHttpClientOption())
            ->then(
                function ($response) use ($returnType) {
                    if (in_array($returnType, ['\SplFileObject', '\Psr\Http\Message\StreamInterface'])) {
                        $content = $response->getBody(); //stream goes to serializer
                    } else {
                        $content = (string) $response->getBody();
                        if ($returnType !== 'string') {
                            $content = json_decode($content);
                        }
                    }

                    return [
                        ObjectSerializer::deserialize($content, $returnType, []),
                        $response->getStatusCode(),
                        $response->getHeaders()
                    ];
                },
                function ($exception) {
                    $response = $exception->getResponse();
                    $statusCode = $response->getStatusCode();
                    throw new ApiException(
                        sprintf(
                            '[%d] Error connecting to the API (%s)',
                            $statusCode,
                            $exception->getRequest()->getUri()
                        ),
                        $statusCode,
                        $response->getHeaders(),
                        (string) $response->getBody()
                    );
                }
            );
    }

    /**
     * Create request for operation 'listVrboReservations'
     *
     * @param  string|null $cursor Opaque cursor returned in the previous response&#39;s &#x60;pagination.nextCursor&#x60;. (optional)
     * @param  int|null $offset First-class alias for cursor-based pagination. Mutually exclusive with &#x60;cursor&#x60; — passing both returns 422. Accepts integers in &#x60;[0, 10000]&#x60;; deeper walks must use &#x60;cursor&#x60; (constant per-page cost). The response always includes &#x60;pagination.nextCursor&#x60; so consumers can switch from offset → cursor mid-walk for deep pagination without re-keying. (optional, default to 0)
     * @param  int|null $limit (optional, default to 50)
     * @param  bool|null $include_total When &#x60;true&#x60; (default), the response&#39;s &#x60;pagination.total&#x60; carries the count of rows matching the current filter, across all pages. Pass &#x60;false&#x60; to skip the count for very large workspaces where the per-page COUNT(*) cost matters. (optional, default to true)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['listVrboReservations'] to see the possible values for this operation
     *
     * @throws InvalidArgumentException
     * @return \GuzzleHttp\Psr7\Request
     */
    public function listVrboReservationsRequest(
        ?string $cursor = null,
        ?int $offset = 0,
        ?int $limit = 50,
        ?bool $include_total = true,
        string $contentType = self::contentTypes['listVrboReservations'][0]
    ): Request
    {


        if ($offset !== null && $offset > 10000) {
            throw new InvalidArgumentException('invalid value for "$offset" when calling VRBOApi.listVrboReservations, must be smaller than or equal to 10000.');
        }
        if ($offset !== null && $offset < 0) {
            throw new InvalidArgumentException('invalid value for "$offset" when calling VRBOApi.listVrboReservations, must be bigger than or equal to 0.');
        }
        
        if ($limit !== null && $limit > 100) {
            throw new InvalidArgumentException('invalid value for "$limit" when calling VRBOApi.listVrboReservations, must be smaller than or equal to 100.');
        }
        if ($limit !== null && $limit < 1) {
            throw new InvalidArgumentException('invalid value for "$limit" when calling VRBOApi.listVrboReservations, must be bigger than or equal to 1.');
        }
        


        $resourcePath = '/v1/channels/vrbo/reservations';
        $formParams = [];
        $queryParams = [];
        $headerParams = [];
        $httpBody = '';
        $multipart = false;

        // query params
        $queryParams = array_merge($queryParams, ObjectSerializer::toQueryValue(
            $cursor,
            'cursor', // param base name
            'string', // openApiType
            'form', // style
            true, // explode
            false, // required
            null // openApiFormat
        ) ?? []);
        // query params
        $queryParams = array_merge($queryParams, ObjectSerializer::toQueryValue(
            $offset,
            'offset', // param base name
            'integer', // openApiType
            'form', // style
            true, // explode
            false, // required
            null // openApiFormat
        ) ?? []);
        // query params
        $queryParams = array_merge($queryParams, ObjectSerializer::toQueryValue(
            $limit,
            'limit', // param base name
            'integer', // openApiType
            'form', // style
            true, // explode
            false, // required
            null // openApiFormat
        ) ?? []);
        // query params
        $queryParams = array_merge($queryParams, ObjectSerializer::toQueryValue(
            $include_total,
            'include_total', // param base name
            'boolean', // openApiType
            'form', // style
            true, // explode
            false, // required
            null // openApiFormat
        ) ?? []);




        $headers = $this->headerSelector->selectHeaders(
            ['application/json', ],
            $contentType,
            $multipart
        );

        // for model (json/xml)
        if (count($formParams) > 0) {
            if ($multipart) {
                $multipartContents = [];
                foreach ($formParams as $formParamName => $formParamValue) {
                    $formParamValueItems = is_array($formParamValue) ? $formParamValue : [$formParamValue];
                    foreach ($formParamValueItems as $formParamValueItem) {
                        $multipartContents[] = [
                            'name' => $formParamName,
                            'contents' => $formParamValueItem
                        ];
                    }
                }
                // for HTTP post (form)
                $httpBody = new MultipartStream($multipartContents);

            } elseif (stripos($headers['Content-Type'], 'application/json') !== false) {
                # if Content-Type contains "application/json", json_encode the form parameters
                $httpBody = \GuzzleHttp\Utils::jsonEncode($formParams);
            } else {
                // for HTTP post (form)
                $httpBody = ObjectSerializer::buildQuery($formParams);
            }
        }

        // this endpoint requires Bearer (API Key) authentication (access token)
        if (!empty($this->config->getAccessToken())) {
            $headers['Authorization'] = 'Bearer ' . $this->config->getAccessToken();
        }

        $defaultHeaders = [];
        if ($this->config->getUserAgent()) {
            $defaultHeaders['User-Agent'] = $this->config->getUserAgent();
        }

        $headers = array_merge(
            $defaultHeaders,
            $headerParams,
            $headers
        );

        $operationHost = $this->config->getHost();
        $query = ObjectSerializer::buildQuery($queryParams);
        return new Request(
            'GET',
            $operationHost . $resourcePath . ($query ? "?{$query}" : ''),
            $headers,
            $httpBody
        );
    }

    /**
     * Operation updateVrboListingAdditionalInfo
     *
     * Update the Vrbo additional details
     *
     * @param  string $id Repull listing id (numeric string) of a listing linked to Vrbo. (required)
     * @param  \Repull\Model\UpdateVrboListingAdditionalInfoRequest $update_vrbo_listing_additional_info_request update_vrbo_listing_additional_info_request (required)
     * @param  string|null $vrbo_listing_number The Vrbo listing number to work on, when the listing is linked to more than one Vrbo unit. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['updateVrboListingAdditionalInfo'] to see the possible values for this operation
     *
     * @throws ApiException on non-2xx response or if the response body is not in the expected format
     * @throws InvalidArgumentException
     * @return \Repull\Model\UpdateVrboListingAdditionalInfo200Response|\Repull\Model\Error
     */
    public function updateVrboListingAdditionalInfo(
        string $id,
        \Repull\Model\UpdateVrboListingAdditionalInfoRequest $update_vrbo_listing_additional_info_request,
        ?string $vrbo_listing_number = null,
        string $contentType = self::contentTypes['updateVrboListingAdditionalInfo'][0]
    ): \Repull\Model\UpdateVrboListingAdditionalInfo200Response|\Repull\Model\Error
    {
        list($response) = $this->updateVrboListingAdditionalInfoWithHttpInfo($id, $update_vrbo_listing_additional_info_request, $vrbo_listing_number, $contentType);
        return $response;
    }

    /**
     * Operation updateVrboListingAdditionalInfoWithHttpInfo
     *
     * Update the Vrbo additional details
     *
     * @param  string $id Repull listing id (numeric string) of a listing linked to Vrbo. (required)
     * @param  \Repull\Model\UpdateVrboListingAdditionalInfoRequest $update_vrbo_listing_additional_info_request (required)
     * @param  string|null $vrbo_listing_number The Vrbo listing number to work on, when the listing is linked to more than one Vrbo unit. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['updateVrboListingAdditionalInfo'] to see the possible values for this operation
     *
     * @throws ApiException on non-2xx response or if the response body is not in the expected format
     * @throws InvalidArgumentException
     * @return array of \Repull\Model\UpdateVrboListingAdditionalInfo200Response|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error, HTTP status code, HTTP response headers (array of strings)
     */
    public function updateVrboListingAdditionalInfoWithHttpInfo(
        string $id,
        \Repull\Model\UpdateVrboListingAdditionalInfoRequest $update_vrbo_listing_additional_info_request,
        ?string $vrbo_listing_number = null,
        string $contentType = self::contentTypes['updateVrboListingAdditionalInfo'][0]
    ): array
    {
        $request = $this->updateVrboListingAdditionalInfoRequest($id, $update_vrbo_listing_additional_info_request, $vrbo_listing_number, $contentType);

        try {
            $options = $this->createHttpClientOption();
            try {
                $response = $this->client->send($request, $options);
            } catch (RequestException $e) {
                throw new ApiException(
                    "[{$e->getCode()}] {$e->getMessage()}",
                    (int) $e->getCode(),
                    $e->getResponse() ? $e->getResponse()->getHeaders() : null,
                    $e->getResponse() ? (string) $e->getResponse()->getBody() : null
                );
            } catch (ConnectException $e) {
                throw new ApiException(
                    "[{$e->getCode()}] {$e->getMessage()}",
                    (int) $e->getCode(),
                    null,
                    null
                );
            }

            $statusCode = $response->getStatusCode();

            switch($statusCode) {
                case 200:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\UpdateVrboListingAdditionalInfo200Response',
                        $request,
                        $response,
                    );
                case 401:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 403:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 404:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 409:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 422:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 500:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 502:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 503:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 504:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
            }
            

            if ($statusCode < 200 || $statusCode > 299) {
                throw new ApiException(
                    sprintf(
                        '[%d] Error connecting to the API (%s)',
                        $statusCode,
                        (string) $request->getUri()
                    ),
                    $statusCode,
                    $response->getHeaders(),
                    (string) $response->getBody()
                );
            }

            return $this->handleResponseWithDataType(
                '\Repull\Model\UpdateVrboListingAdditionalInfo200Response',
                $request,
                $response,
            );
        } catch (ApiException $e) {
            switch ($e->getCode()) {
                case 200:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\UpdateVrboListingAdditionalInfo200Response',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 401:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 403:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 404:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 409:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 422:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 500:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 502:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 503:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 504:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
            }
        
            throw $e;
        }
    }

    /**
     * Operation updateVrboListingAdditionalInfoAsync
     *
     * Update the Vrbo additional details
     *
     * @param  string $id Repull listing id (numeric string) of a listing linked to Vrbo. (required)
     * @param  \Repull\Model\UpdateVrboListingAdditionalInfoRequest $update_vrbo_listing_additional_info_request (required)
     * @param  string|null $vrbo_listing_number The Vrbo listing number to work on, when the listing is linked to more than one Vrbo unit. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['updateVrboListingAdditionalInfo'] to see the possible values for this operation
     *
     * @throws InvalidArgumentException
     * @return PromiseInterface
     */
    public function updateVrboListingAdditionalInfoAsync(
        string $id,
        \Repull\Model\UpdateVrboListingAdditionalInfoRequest $update_vrbo_listing_additional_info_request,
        ?string $vrbo_listing_number = null,
        string $contentType = self::contentTypes['updateVrboListingAdditionalInfo'][0]
    ): PromiseInterface
    {
        return $this->updateVrboListingAdditionalInfoAsyncWithHttpInfo($id, $update_vrbo_listing_additional_info_request, $vrbo_listing_number, $contentType)
            ->then(
                function ($response) {
                    return $response[0];
                }
            );
    }

    /**
     * Operation updateVrboListingAdditionalInfoAsyncWithHttpInfo
     *
     * Update the Vrbo additional details
     *
     * @param  string $id Repull listing id (numeric string) of a listing linked to Vrbo. (required)
     * @param  \Repull\Model\UpdateVrboListingAdditionalInfoRequest $update_vrbo_listing_additional_info_request (required)
     * @param  string|null $vrbo_listing_number The Vrbo listing number to work on, when the listing is linked to more than one Vrbo unit. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['updateVrboListingAdditionalInfo'] to see the possible values for this operation
     *
     * @throws InvalidArgumentException
     * @return PromiseInterface
     */
    public function updateVrboListingAdditionalInfoAsyncWithHttpInfo(
        string $id,
        \Repull\Model\UpdateVrboListingAdditionalInfoRequest $update_vrbo_listing_additional_info_request,
        ?string $vrbo_listing_number = null,
        string $contentType = self::contentTypes['updateVrboListingAdditionalInfo'][0]
    ): PromiseInterface
    {
        $returnType = '\Repull\Model\UpdateVrboListingAdditionalInfo200Response';
        $request = $this->updateVrboListingAdditionalInfoRequest($id, $update_vrbo_listing_additional_info_request, $vrbo_listing_number, $contentType);

        return $this->client
            ->sendAsync($request, $this->createHttpClientOption())
            ->then(
                function ($response) use ($returnType) {
                    if (in_array($returnType, ['\SplFileObject', '\Psr\Http\Message\StreamInterface'])) {
                        $content = $response->getBody(); //stream goes to serializer
                    } else {
                        $content = (string) $response->getBody();
                        if ($returnType !== 'string') {
                            $content = json_decode($content);
                        }
                    }

                    return [
                        ObjectSerializer::deserialize($content, $returnType, []),
                        $response->getStatusCode(),
                        $response->getHeaders()
                    ];
                },
                function ($exception) {
                    $response = $exception->getResponse();
                    $statusCode = $response->getStatusCode();
                    throw new ApiException(
                        sprintf(
                            '[%d] Error connecting to the API (%s)',
                            $statusCode,
                            $exception->getRequest()->getUri()
                        ),
                        $statusCode,
                        $response->getHeaders(),
                        (string) $response->getBody()
                    );
                }
            );
    }

    /**
     * Create request for operation 'updateVrboListingAdditionalInfo'
     *
     * @param  string $id Repull listing id (numeric string) of a listing linked to Vrbo. (required)
     * @param  \Repull\Model\UpdateVrboListingAdditionalInfoRequest $update_vrbo_listing_additional_info_request (required)
     * @param  string|null $vrbo_listing_number The Vrbo listing number to work on, when the listing is linked to more than one Vrbo unit. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['updateVrboListingAdditionalInfo'] to see the possible values for this operation
     *
     * @throws InvalidArgumentException
     * @return \GuzzleHttp\Psr7\Request
     */
    public function updateVrboListingAdditionalInfoRequest(
        string $id,
        \Repull\Model\UpdateVrboListingAdditionalInfoRequest $update_vrbo_listing_additional_info_request,
        ?string $vrbo_listing_number = null,
        string $contentType = self::contentTypes['updateVrboListingAdditionalInfo'][0]
    ): Request
    {

        // verify the required parameter 'id' is set
        if ($id === null || (is_array($id) && count($id) === 0)) {
            throw new InvalidArgumentException(
                'Missing the required parameter $id when calling updateVrboListingAdditionalInfo'
            );
        }

        // verify the required parameter 'update_vrbo_listing_additional_info_request' is set
        if ($update_vrbo_listing_additional_info_request === null || (is_array($update_vrbo_listing_additional_info_request) && count($update_vrbo_listing_additional_info_request) === 0)) {
            throw new InvalidArgumentException(
                'Missing the required parameter $update_vrbo_listing_additional_info_request when calling updateVrboListingAdditionalInfo'
            );
        }



        $resourcePath = '/v1/channels/vrbo/listings/{id}/additional-info';
        $formParams = [];
        $queryParams = [];
        $headerParams = [];
        $httpBody = '';
        $multipart = false;

        // query params
        $queryParams = array_merge($queryParams, ObjectSerializer::toQueryValue(
            $vrbo_listing_number,
            'vrboListingNumber', // param base name
            'string', // openApiType
            'form', // style
            true, // explode
            false, // required
            null // openApiFormat
        ) ?? []);


        // path params
        if ($id !== null) {
            $resourcePath = str_replace(
                '{id}',
                ObjectSerializer::toPathValue($id),
                $resourcePath
            );
        }


        $headers = $this->headerSelector->selectHeaders(
            ['application/json', ],
            $contentType,
            $multipart
        );

        // for model (json/xml)
        if (isset($update_vrbo_listing_additional_info_request)) {
            if (stripos($headers['Content-Type'], 'application/json') !== false) {
                # if Content-Type contains "application/json", json_encode the body
                $httpBody = \GuzzleHttp\Utils::jsonEncode(ObjectSerializer::sanitizeForSerialization($update_vrbo_listing_additional_info_request));
            } else {
                $httpBody = $update_vrbo_listing_additional_info_request;
            }
        } elseif (count($formParams) > 0) {
            if ($multipart) {
                $multipartContents = [];
                foreach ($formParams as $formParamName => $formParamValue) {
                    $formParamValueItems = is_array($formParamValue) ? $formParamValue : [$formParamValue];
                    foreach ($formParamValueItems as $formParamValueItem) {
                        $multipartContents[] = [
                            'name' => $formParamName,
                            'contents' => $formParamValueItem
                        ];
                    }
                }
                // for HTTP post (form)
                $httpBody = new MultipartStream($multipartContents);

            } elseif (stripos($headers['Content-Type'], 'application/json') !== false) {
                # if Content-Type contains "application/json", json_encode the form parameters
                $httpBody = \GuzzleHttp\Utils::jsonEncode($formParams);
            } else {
                // for HTTP post (form)
                $httpBody = ObjectSerializer::buildQuery($formParams);
            }
        }

        // this endpoint requires Bearer (API Key) authentication (access token)
        if (!empty($this->config->getAccessToken())) {
            $headers['Authorization'] = 'Bearer ' . $this->config->getAccessToken();
        }

        $defaultHeaders = [];
        if ($this->config->getUserAgent()) {
            $defaultHeaders['User-Agent'] = $this->config->getUserAgent();
        }

        $headers = array_merge(
            $defaultHeaders,
            $headerParams,
            $headers
        );

        $operationHost = $this->config->getHost();
        $query = ObjectSerializer::buildQuery($queryParams);
        return new Request(
            'PUT',
            $operationHost . $resourcePath . ($query ? "?{$query}" : ''),
            $headers,
            $httpBody
        );
    }

    /**
     * Operation updateVrboListingAmenities
     *
     * Update the Vrbo amenities
     *
     * @param  string $id Repull listing id (numeric string) of a listing linked to Vrbo. (required)
     * @param  \Repull\Model\UpdateVrboListingAmenitiesRequest $update_vrbo_listing_amenities_request update_vrbo_listing_amenities_request (required)
     * @param  string|null $vrbo_listing_number The Vrbo listing number to work on, when the listing is linked to more than one Vrbo unit. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['updateVrboListingAmenities'] to see the possible values for this operation
     *
     * @throws ApiException on non-2xx response or if the response body is not in the expected format
     * @throws InvalidArgumentException
     * @return \Repull\Model\UpdateVrboListingAmenities200Response|\Repull\Model\Error
     */
    public function updateVrboListingAmenities(
        string $id,
        \Repull\Model\UpdateVrboListingAmenitiesRequest $update_vrbo_listing_amenities_request,
        ?string $vrbo_listing_number = null,
        string $contentType = self::contentTypes['updateVrboListingAmenities'][0]
    ): \Repull\Model\UpdateVrboListingAmenities200Response|\Repull\Model\Error
    {
        list($response) = $this->updateVrboListingAmenitiesWithHttpInfo($id, $update_vrbo_listing_amenities_request, $vrbo_listing_number, $contentType);
        return $response;
    }

    /**
     * Operation updateVrboListingAmenitiesWithHttpInfo
     *
     * Update the Vrbo amenities
     *
     * @param  string $id Repull listing id (numeric string) of a listing linked to Vrbo. (required)
     * @param  \Repull\Model\UpdateVrboListingAmenitiesRequest $update_vrbo_listing_amenities_request (required)
     * @param  string|null $vrbo_listing_number The Vrbo listing number to work on, when the listing is linked to more than one Vrbo unit. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['updateVrboListingAmenities'] to see the possible values for this operation
     *
     * @throws ApiException on non-2xx response or if the response body is not in the expected format
     * @throws InvalidArgumentException
     * @return array of \Repull\Model\UpdateVrboListingAmenities200Response|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error, HTTP status code, HTTP response headers (array of strings)
     */
    public function updateVrboListingAmenitiesWithHttpInfo(
        string $id,
        \Repull\Model\UpdateVrboListingAmenitiesRequest $update_vrbo_listing_amenities_request,
        ?string $vrbo_listing_number = null,
        string $contentType = self::contentTypes['updateVrboListingAmenities'][0]
    ): array
    {
        $request = $this->updateVrboListingAmenitiesRequest($id, $update_vrbo_listing_amenities_request, $vrbo_listing_number, $contentType);

        try {
            $options = $this->createHttpClientOption();
            try {
                $response = $this->client->send($request, $options);
            } catch (RequestException $e) {
                throw new ApiException(
                    "[{$e->getCode()}] {$e->getMessage()}",
                    (int) $e->getCode(),
                    $e->getResponse() ? $e->getResponse()->getHeaders() : null,
                    $e->getResponse() ? (string) $e->getResponse()->getBody() : null
                );
            } catch (ConnectException $e) {
                throw new ApiException(
                    "[{$e->getCode()}] {$e->getMessage()}",
                    (int) $e->getCode(),
                    null,
                    null
                );
            }

            $statusCode = $response->getStatusCode();

            switch($statusCode) {
                case 200:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\UpdateVrboListingAmenities200Response',
                        $request,
                        $response,
                    );
                case 401:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 403:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 404:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 409:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 422:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 500:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 502:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 503:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 504:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
            }
            

            if ($statusCode < 200 || $statusCode > 299) {
                throw new ApiException(
                    sprintf(
                        '[%d] Error connecting to the API (%s)',
                        $statusCode,
                        (string) $request->getUri()
                    ),
                    $statusCode,
                    $response->getHeaders(),
                    (string) $response->getBody()
                );
            }

            return $this->handleResponseWithDataType(
                '\Repull\Model\UpdateVrboListingAmenities200Response',
                $request,
                $response,
            );
        } catch (ApiException $e) {
            switch ($e->getCode()) {
                case 200:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\UpdateVrboListingAmenities200Response',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 401:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 403:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 404:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 409:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 422:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 500:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 502:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 503:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 504:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
            }
        
            throw $e;
        }
    }

    /**
     * Operation updateVrboListingAmenitiesAsync
     *
     * Update the Vrbo amenities
     *
     * @param  string $id Repull listing id (numeric string) of a listing linked to Vrbo. (required)
     * @param  \Repull\Model\UpdateVrboListingAmenitiesRequest $update_vrbo_listing_amenities_request (required)
     * @param  string|null $vrbo_listing_number The Vrbo listing number to work on, when the listing is linked to more than one Vrbo unit. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['updateVrboListingAmenities'] to see the possible values for this operation
     *
     * @throws InvalidArgumentException
     * @return PromiseInterface
     */
    public function updateVrboListingAmenitiesAsync(
        string $id,
        \Repull\Model\UpdateVrboListingAmenitiesRequest $update_vrbo_listing_amenities_request,
        ?string $vrbo_listing_number = null,
        string $contentType = self::contentTypes['updateVrboListingAmenities'][0]
    ): PromiseInterface
    {
        return $this->updateVrboListingAmenitiesAsyncWithHttpInfo($id, $update_vrbo_listing_amenities_request, $vrbo_listing_number, $contentType)
            ->then(
                function ($response) {
                    return $response[0];
                }
            );
    }

    /**
     * Operation updateVrboListingAmenitiesAsyncWithHttpInfo
     *
     * Update the Vrbo amenities
     *
     * @param  string $id Repull listing id (numeric string) of a listing linked to Vrbo. (required)
     * @param  \Repull\Model\UpdateVrboListingAmenitiesRequest $update_vrbo_listing_amenities_request (required)
     * @param  string|null $vrbo_listing_number The Vrbo listing number to work on, when the listing is linked to more than one Vrbo unit. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['updateVrboListingAmenities'] to see the possible values for this operation
     *
     * @throws InvalidArgumentException
     * @return PromiseInterface
     */
    public function updateVrboListingAmenitiesAsyncWithHttpInfo(
        string $id,
        \Repull\Model\UpdateVrboListingAmenitiesRequest $update_vrbo_listing_amenities_request,
        ?string $vrbo_listing_number = null,
        string $contentType = self::contentTypes['updateVrboListingAmenities'][0]
    ): PromiseInterface
    {
        $returnType = '\Repull\Model\UpdateVrboListingAmenities200Response';
        $request = $this->updateVrboListingAmenitiesRequest($id, $update_vrbo_listing_amenities_request, $vrbo_listing_number, $contentType);

        return $this->client
            ->sendAsync($request, $this->createHttpClientOption())
            ->then(
                function ($response) use ($returnType) {
                    if (in_array($returnType, ['\SplFileObject', '\Psr\Http\Message\StreamInterface'])) {
                        $content = $response->getBody(); //stream goes to serializer
                    } else {
                        $content = (string) $response->getBody();
                        if ($returnType !== 'string') {
                            $content = json_decode($content);
                        }
                    }

                    return [
                        ObjectSerializer::deserialize($content, $returnType, []),
                        $response->getStatusCode(),
                        $response->getHeaders()
                    ];
                },
                function ($exception) {
                    $response = $exception->getResponse();
                    $statusCode = $response->getStatusCode();
                    throw new ApiException(
                        sprintf(
                            '[%d] Error connecting to the API (%s)',
                            $statusCode,
                            $exception->getRequest()->getUri()
                        ),
                        $statusCode,
                        $response->getHeaders(),
                        (string) $response->getBody()
                    );
                }
            );
    }

    /**
     * Create request for operation 'updateVrboListingAmenities'
     *
     * @param  string $id Repull listing id (numeric string) of a listing linked to Vrbo. (required)
     * @param  \Repull\Model\UpdateVrboListingAmenitiesRequest $update_vrbo_listing_amenities_request (required)
     * @param  string|null $vrbo_listing_number The Vrbo listing number to work on, when the listing is linked to more than one Vrbo unit. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['updateVrboListingAmenities'] to see the possible values for this operation
     *
     * @throws InvalidArgumentException
     * @return \GuzzleHttp\Psr7\Request
     */
    public function updateVrboListingAmenitiesRequest(
        string $id,
        \Repull\Model\UpdateVrboListingAmenitiesRequest $update_vrbo_listing_amenities_request,
        ?string $vrbo_listing_number = null,
        string $contentType = self::contentTypes['updateVrboListingAmenities'][0]
    ): Request
    {

        // verify the required parameter 'id' is set
        if ($id === null || (is_array($id) && count($id) === 0)) {
            throw new InvalidArgumentException(
                'Missing the required parameter $id when calling updateVrboListingAmenities'
            );
        }

        // verify the required parameter 'update_vrbo_listing_amenities_request' is set
        if ($update_vrbo_listing_amenities_request === null || (is_array($update_vrbo_listing_amenities_request) && count($update_vrbo_listing_amenities_request) === 0)) {
            throw new InvalidArgumentException(
                'Missing the required parameter $update_vrbo_listing_amenities_request when calling updateVrboListingAmenities'
            );
        }



        $resourcePath = '/v1/channels/vrbo/listings/{id}/amenities';
        $formParams = [];
        $queryParams = [];
        $headerParams = [];
        $httpBody = '';
        $multipart = false;

        // query params
        $queryParams = array_merge($queryParams, ObjectSerializer::toQueryValue(
            $vrbo_listing_number,
            'vrboListingNumber', // param base name
            'string', // openApiType
            'form', // style
            true, // explode
            false, // required
            null // openApiFormat
        ) ?? []);


        // path params
        if ($id !== null) {
            $resourcePath = str_replace(
                '{id}',
                ObjectSerializer::toPathValue($id),
                $resourcePath
            );
        }


        $headers = $this->headerSelector->selectHeaders(
            ['application/json', ],
            $contentType,
            $multipart
        );

        // for model (json/xml)
        if (isset($update_vrbo_listing_amenities_request)) {
            if (stripos($headers['Content-Type'], 'application/json') !== false) {
                # if Content-Type contains "application/json", json_encode the body
                $httpBody = \GuzzleHttp\Utils::jsonEncode(ObjectSerializer::sanitizeForSerialization($update_vrbo_listing_amenities_request));
            } else {
                $httpBody = $update_vrbo_listing_amenities_request;
            }
        } elseif (count($formParams) > 0) {
            if ($multipart) {
                $multipartContents = [];
                foreach ($formParams as $formParamName => $formParamValue) {
                    $formParamValueItems = is_array($formParamValue) ? $formParamValue : [$formParamValue];
                    foreach ($formParamValueItems as $formParamValueItem) {
                        $multipartContents[] = [
                            'name' => $formParamName,
                            'contents' => $formParamValueItem
                        ];
                    }
                }
                // for HTTP post (form)
                $httpBody = new MultipartStream($multipartContents);

            } elseif (stripos($headers['Content-Type'], 'application/json') !== false) {
                # if Content-Type contains "application/json", json_encode the form parameters
                $httpBody = \GuzzleHttp\Utils::jsonEncode($formParams);
            } else {
                // for HTTP post (form)
                $httpBody = ObjectSerializer::buildQuery($formParams);
            }
        }

        // this endpoint requires Bearer (API Key) authentication (access token)
        if (!empty($this->config->getAccessToken())) {
            $headers['Authorization'] = 'Bearer ' . $this->config->getAccessToken();
        }

        $defaultHeaders = [];
        if ($this->config->getUserAgent()) {
            $defaultHeaders['User-Agent'] = $this->config->getUserAgent();
        }

        $headers = array_merge(
            $defaultHeaders,
            $headerParams,
            $headers
        );

        $operationHost = $this->config->getHost();
        $query = ObjectSerializer::buildQuery($queryParams);
        return new Request(
            'PUT',
            $operationHost . $resourcePath . ($query ? "?{$query}" : ''),
            $headers,
            $httpBody
        );
    }

    /**
     * Operation updateVrboListingBookingSettings
     *
     * Update the Vrbo booking settings
     *
     * @param  string $id Repull listing id (numeric string) of a listing linked to Vrbo. (required)
     * @param  \Repull\Model\UpdateVrboListingBookingSettingsRequest $update_vrbo_listing_booking_settings_request update_vrbo_listing_booking_settings_request (required)
     * @param  string|null $vrbo_listing_number The Vrbo listing number to work on, when the listing is linked to more than one Vrbo unit. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['updateVrboListingBookingSettings'] to see the possible values for this operation
     *
     * @throws ApiException on non-2xx response or if the response body is not in the expected format
     * @throws InvalidArgumentException
     * @return \Repull\Model\UpdateVrboListingBookingSettings200Response|\Repull\Model\Error
     */
    public function updateVrboListingBookingSettings(
        string $id,
        \Repull\Model\UpdateVrboListingBookingSettingsRequest $update_vrbo_listing_booking_settings_request,
        ?string $vrbo_listing_number = null,
        string $contentType = self::contentTypes['updateVrboListingBookingSettings'][0]
    ): \Repull\Model\UpdateVrboListingBookingSettings200Response|\Repull\Model\Error
    {
        list($response) = $this->updateVrboListingBookingSettingsWithHttpInfo($id, $update_vrbo_listing_booking_settings_request, $vrbo_listing_number, $contentType);
        return $response;
    }

    /**
     * Operation updateVrboListingBookingSettingsWithHttpInfo
     *
     * Update the Vrbo booking settings
     *
     * @param  string $id Repull listing id (numeric string) of a listing linked to Vrbo. (required)
     * @param  \Repull\Model\UpdateVrboListingBookingSettingsRequest $update_vrbo_listing_booking_settings_request (required)
     * @param  string|null $vrbo_listing_number The Vrbo listing number to work on, when the listing is linked to more than one Vrbo unit. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['updateVrboListingBookingSettings'] to see the possible values for this operation
     *
     * @throws ApiException on non-2xx response or if the response body is not in the expected format
     * @throws InvalidArgumentException
     * @return array of \Repull\Model\UpdateVrboListingBookingSettings200Response|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error, HTTP status code, HTTP response headers (array of strings)
     */
    public function updateVrboListingBookingSettingsWithHttpInfo(
        string $id,
        \Repull\Model\UpdateVrboListingBookingSettingsRequest $update_vrbo_listing_booking_settings_request,
        ?string $vrbo_listing_number = null,
        string $contentType = self::contentTypes['updateVrboListingBookingSettings'][0]
    ): array
    {
        $request = $this->updateVrboListingBookingSettingsRequest($id, $update_vrbo_listing_booking_settings_request, $vrbo_listing_number, $contentType);

        try {
            $options = $this->createHttpClientOption();
            try {
                $response = $this->client->send($request, $options);
            } catch (RequestException $e) {
                throw new ApiException(
                    "[{$e->getCode()}] {$e->getMessage()}",
                    (int) $e->getCode(),
                    $e->getResponse() ? $e->getResponse()->getHeaders() : null,
                    $e->getResponse() ? (string) $e->getResponse()->getBody() : null
                );
            } catch (ConnectException $e) {
                throw new ApiException(
                    "[{$e->getCode()}] {$e->getMessage()}",
                    (int) $e->getCode(),
                    null,
                    null
                );
            }

            $statusCode = $response->getStatusCode();

            switch($statusCode) {
                case 200:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\UpdateVrboListingBookingSettings200Response',
                        $request,
                        $response,
                    );
                case 401:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 403:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 404:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 409:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 422:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 500:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 502:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 503:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 504:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
            }
            

            if ($statusCode < 200 || $statusCode > 299) {
                throw new ApiException(
                    sprintf(
                        '[%d] Error connecting to the API (%s)',
                        $statusCode,
                        (string) $request->getUri()
                    ),
                    $statusCode,
                    $response->getHeaders(),
                    (string) $response->getBody()
                );
            }

            return $this->handleResponseWithDataType(
                '\Repull\Model\UpdateVrboListingBookingSettings200Response',
                $request,
                $response,
            );
        } catch (ApiException $e) {
            switch ($e->getCode()) {
                case 200:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\UpdateVrboListingBookingSettings200Response',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 401:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 403:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 404:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 409:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 422:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 500:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 502:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 503:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 504:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
            }
        
            throw $e;
        }
    }

    /**
     * Operation updateVrboListingBookingSettingsAsync
     *
     * Update the Vrbo booking settings
     *
     * @param  string $id Repull listing id (numeric string) of a listing linked to Vrbo. (required)
     * @param  \Repull\Model\UpdateVrboListingBookingSettingsRequest $update_vrbo_listing_booking_settings_request (required)
     * @param  string|null $vrbo_listing_number The Vrbo listing number to work on, when the listing is linked to more than one Vrbo unit. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['updateVrboListingBookingSettings'] to see the possible values for this operation
     *
     * @throws InvalidArgumentException
     * @return PromiseInterface
     */
    public function updateVrboListingBookingSettingsAsync(
        string $id,
        \Repull\Model\UpdateVrboListingBookingSettingsRequest $update_vrbo_listing_booking_settings_request,
        ?string $vrbo_listing_number = null,
        string $contentType = self::contentTypes['updateVrboListingBookingSettings'][0]
    ): PromiseInterface
    {
        return $this->updateVrboListingBookingSettingsAsyncWithHttpInfo($id, $update_vrbo_listing_booking_settings_request, $vrbo_listing_number, $contentType)
            ->then(
                function ($response) {
                    return $response[0];
                }
            );
    }

    /**
     * Operation updateVrboListingBookingSettingsAsyncWithHttpInfo
     *
     * Update the Vrbo booking settings
     *
     * @param  string $id Repull listing id (numeric string) of a listing linked to Vrbo. (required)
     * @param  \Repull\Model\UpdateVrboListingBookingSettingsRequest $update_vrbo_listing_booking_settings_request (required)
     * @param  string|null $vrbo_listing_number The Vrbo listing number to work on, when the listing is linked to more than one Vrbo unit. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['updateVrboListingBookingSettings'] to see the possible values for this operation
     *
     * @throws InvalidArgumentException
     * @return PromiseInterface
     */
    public function updateVrboListingBookingSettingsAsyncWithHttpInfo(
        string $id,
        \Repull\Model\UpdateVrboListingBookingSettingsRequest $update_vrbo_listing_booking_settings_request,
        ?string $vrbo_listing_number = null,
        string $contentType = self::contentTypes['updateVrboListingBookingSettings'][0]
    ): PromiseInterface
    {
        $returnType = '\Repull\Model\UpdateVrboListingBookingSettings200Response';
        $request = $this->updateVrboListingBookingSettingsRequest($id, $update_vrbo_listing_booking_settings_request, $vrbo_listing_number, $contentType);

        return $this->client
            ->sendAsync($request, $this->createHttpClientOption())
            ->then(
                function ($response) use ($returnType) {
                    if (in_array($returnType, ['\SplFileObject', '\Psr\Http\Message\StreamInterface'])) {
                        $content = $response->getBody(); //stream goes to serializer
                    } else {
                        $content = (string) $response->getBody();
                        if ($returnType !== 'string') {
                            $content = json_decode($content);
                        }
                    }

                    return [
                        ObjectSerializer::deserialize($content, $returnType, []),
                        $response->getStatusCode(),
                        $response->getHeaders()
                    ];
                },
                function ($exception) {
                    $response = $exception->getResponse();
                    $statusCode = $response->getStatusCode();
                    throw new ApiException(
                        sprintf(
                            '[%d] Error connecting to the API (%s)',
                            $statusCode,
                            $exception->getRequest()->getUri()
                        ),
                        $statusCode,
                        $response->getHeaders(),
                        (string) $response->getBody()
                    );
                }
            );
    }

    /**
     * Create request for operation 'updateVrboListingBookingSettings'
     *
     * @param  string $id Repull listing id (numeric string) of a listing linked to Vrbo. (required)
     * @param  \Repull\Model\UpdateVrboListingBookingSettingsRequest $update_vrbo_listing_booking_settings_request (required)
     * @param  string|null $vrbo_listing_number The Vrbo listing number to work on, when the listing is linked to more than one Vrbo unit. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['updateVrboListingBookingSettings'] to see the possible values for this operation
     *
     * @throws InvalidArgumentException
     * @return \GuzzleHttp\Psr7\Request
     */
    public function updateVrboListingBookingSettingsRequest(
        string $id,
        \Repull\Model\UpdateVrboListingBookingSettingsRequest $update_vrbo_listing_booking_settings_request,
        ?string $vrbo_listing_number = null,
        string $contentType = self::contentTypes['updateVrboListingBookingSettings'][0]
    ): Request
    {

        // verify the required parameter 'id' is set
        if ($id === null || (is_array($id) && count($id) === 0)) {
            throw new InvalidArgumentException(
                'Missing the required parameter $id when calling updateVrboListingBookingSettings'
            );
        }

        // verify the required parameter 'update_vrbo_listing_booking_settings_request' is set
        if ($update_vrbo_listing_booking_settings_request === null || (is_array($update_vrbo_listing_booking_settings_request) && count($update_vrbo_listing_booking_settings_request) === 0)) {
            throw new InvalidArgumentException(
                'Missing the required parameter $update_vrbo_listing_booking_settings_request when calling updateVrboListingBookingSettings'
            );
        }



        $resourcePath = '/v1/channels/vrbo/listings/{id}/booking-settings';
        $formParams = [];
        $queryParams = [];
        $headerParams = [];
        $httpBody = '';
        $multipart = false;

        // query params
        $queryParams = array_merge($queryParams, ObjectSerializer::toQueryValue(
            $vrbo_listing_number,
            'vrboListingNumber', // param base name
            'string', // openApiType
            'form', // style
            true, // explode
            false, // required
            null // openApiFormat
        ) ?? []);


        // path params
        if ($id !== null) {
            $resourcePath = str_replace(
                '{id}',
                ObjectSerializer::toPathValue($id),
                $resourcePath
            );
        }


        $headers = $this->headerSelector->selectHeaders(
            ['application/json', ],
            $contentType,
            $multipart
        );

        // for model (json/xml)
        if (isset($update_vrbo_listing_booking_settings_request)) {
            if (stripos($headers['Content-Type'], 'application/json') !== false) {
                # if Content-Type contains "application/json", json_encode the body
                $httpBody = \GuzzleHttp\Utils::jsonEncode(ObjectSerializer::sanitizeForSerialization($update_vrbo_listing_booking_settings_request));
            } else {
                $httpBody = $update_vrbo_listing_booking_settings_request;
            }
        } elseif (count($formParams) > 0) {
            if ($multipart) {
                $multipartContents = [];
                foreach ($formParams as $formParamName => $formParamValue) {
                    $formParamValueItems = is_array($formParamValue) ? $formParamValue : [$formParamValue];
                    foreach ($formParamValueItems as $formParamValueItem) {
                        $multipartContents[] = [
                            'name' => $formParamName,
                            'contents' => $formParamValueItem
                        ];
                    }
                }
                // for HTTP post (form)
                $httpBody = new MultipartStream($multipartContents);

            } elseif (stripos($headers['Content-Type'], 'application/json') !== false) {
                # if Content-Type contains "application/json", json_encode the form parameters
                $httpBody = \GuzzleHttp\Utils::jsonEncode($formParams);
            } else {
                // for HTTP post (form)
                $httpBody = ObjectSerializer::buildQuery($formParams);
            }
        }

        // this endpoint requires Bearer (API Key) authentication (access token)
        if (!empty($this->config->getAccessToken())) {
            $headers['Authorization'] = 'Bearer ' . $this->config->getAccessToken();
        }

        $defaultHeaders = [];
        if ($this->config->getUserAgent()) {
            $defaultHeaders['User-Agent'] = $this->config->getUserAgent();
        }

        $headers = array_merge(
            $defaultHeaders,
            $headerParams,
            $headers
        );

        $operationHost = $this->config->getHost();
        $query = ObjectSerializer::buildQuery($queryParams);
        return new Request(
            'PUT',
            $operationHost . $resourcePath . ($query ? "?{$query}" : ''),
            $headers,
            $httpBody
        );
    }

    /**
     * Operation updateVrboListingCancellation
     *
     * Update the Vrbo cancellation policy
     *
     * @param  string $id Repull listing id (numeric string) of a listing linked to Vrbo. (required)
     * @param  \Repull\Model\UpdateVrboListingCancellationRequest $update_vrbo_listing_cancellation_request update_vrbo_listing_cancellation_request (required)
     * @param  string|null $vrbo_listing_number The Vrbo listing number to work on, when the listing is linked to more than one Vrbo unit. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['updateVrboListingCancellation'] to see the possible values for this operation
     *
     * @throws ApiException on non-2xx response or if the response body is not in the expected format
     * @throws InvalidArgumentException
     * @return \Repull\Model\UpdateVrboListingCancellation200Response|\Repull\Model\Error
     */
    public function updateVrboListingCancellation(
        string $id,
        \Repull\Model\UpdateVrboListingCancellationRequest $update_vrbo_listing_cancellation_request,
        ?string $vrbo_listing_number = null,
        string $contentType = self::contentTypes['updateVrboListingCancellation'][0]
    ): \Repull\Model\UpdateVrboListingCancellation200Response|\Repull\Model\Error
    {
        list($response) = $this->updateVrboListingCancellationWithHttpInfo($id, $update_vrbo_listing_cancellation_request, $vrbo_listing_number, $contentType);
        return $response;
    }

    /**
     * Operation updateVrboListingCancellationWithHttpInfo
     *
     * Update the Vrbo cancellation policy
     *
     * @param  string $id Repull listing id (numeric string) of a listing linked to Vrbo. (required)
     * @param  \Repull\Model\UpdateVrboListingCancellationRequest $update_vrbo_listing_cancellation_request (required)
     * @param  string|null $vrbo_listing_number The Vrbo listing number to work on, when the listing is linked to more than one Vrbo unit. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['updateVrboListingCancellation'] to see the possible values for this operation
     *
     * @throws ApiException on non-2xx response or if the response body is not in the expected format
     * @throws InvalidArgumentException
     * @return array of \Repull\Model\UpdateVrboListingCancellation200Response|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error, HTTP status code, HTTP response headers (array of strings)
     */
    public function updateVrboListingCancellationWithHttpInfo(
        string $id,
        \Repull\Model\UpdateVrboListingCancellationRequest $update_vrbo_listing_cancellation_request,
        ?string $vrbo_listing_number = null,
        string $contentType = self::contentTypes['updateVrboListingCancellation'][0]
    ): array
    {
        $request = $this->updateVrboListingCancellationRequest($id, $update_vrbo_listing_cancellation_request, $vrbo_listing_number, $contentType);

        try {
            $options = $this->createHttpClientOption();
            try {
                $response = $this->client->send($request, $options);
            } catch (RequestException $e) {
                throw new ApiException(
                    "[{$e->getCode()}] {$e->getMessage()}",
                    (int) $e->getCode(),
                    $e->getResponse() ? $e->getResponse()->getHeaders() : null,
                    $e->getResponse() ? (string) $e->getResponse()->getBody() : null
                );
            } catch (ConnectException $e) {
                throw new ApiException(
                    "[{$e->getCode()}] {$e->getMessage()}",
                    (int) $e->getCode(),
                    null,
                    null
                );
            }

            $statusCode = $response->getStatusCode();

            switch($statusCode) {
                case 200:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\UpdateVrboListingCancellation200Response',
                        $request,
                        $response,
                    );
                case 401:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 403:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 404:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 409:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 422:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 500:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 502:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 503:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 504:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
            }
            

            if ($statusCode < 200 || $statusCode > 299) {
                throw new ApiException(
                    sprintf(
                        '[%d] Error connecting to the API (%s)',
                        $statusCode,
                        (string) $request->getUri()
                    ),
                    $statusCode,
                    $response->getHeaders(),
                    (string) $response->getBody()
                );
            }

            return $this->handleResponseWithDataType(
                '\Repull\Model\UpdateVrboListingCancellation200Response',
                $request,
                $response,
            );
        } catch (ApiException $e) {
            switch ($e->getCode()) {
                case 200:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\UpdateVrboListingCancellation200Response',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 401:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 403:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 404:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 409:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 422:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 500:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 502:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 503:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 504:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
            }
        
            throw $e;
        }
    }

    /**
     * Operation updateVrboListingCancellationAsync
     *
     * Update the Vrbo cancellation policy
     *
     * @param  string $id Repull listing id (numeric string) of a listing linked to Vrbo. (required)
     * @param  \Repull\Model\UpdateVrboListingCancellationRequest $update_vrbo_listing_cancellation_request (required)
     * @param  string|null $vrbo_listing_number The Vrbo listing number to work on, when the listing is linked to more than one Vrbo unit. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['updateVrboListingCancellation'] to see the possible values for this operation
     *
     * @throws InvalidArgumentException
     * @return PromiseInterface
     */
    public function updateVrboListingCancellationAsync(
        string $id,
        \Repull\Model\UpdateVrboListingCancellationRequest $update_vrbo_listing_cancellation_request,
        ?string $vrbo_listing_number = null,
        string $contentType = self::contentTypes['updateVrboListingCancellation'][0]
    ): PromiseInterface
    {
        return $this->updateVrboListingCancellationAsyncWithHttpInfo($id, $update_vrbo_listing_cancellation_request, $vrbo_listing_number, $contentType)
            ->then(
                function ($response) {
                    return $response[0];
                }
            );
    }

    /**
     * Operation updateVrboListingCancellationAsyncWithHttpInfo
     *
     * Update the Vrbo cancellation policy
     *
     * @param  string $id Repull listing id (numeric string) of a listing linked to Vrbo. (required)
     * @param  \Repull\Model\UpdateVrboListingCancellationRequest $update_vrbo_listing_cancellation_request (required)
     * @param  string|null $vrbo_listing_number The Vrbo listing number to work on, when the listing is linked to more than one Vrbo unit. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['updateVrboListingCancellation'] to see the possible values for this operation
     *
     * @throws InvalidArgumentException
     * @return PromiseInterface
     */
    public function updateVrboListingCancellationAsyncWithHttpInfo(
        string $id,
        \Repull\Model\UpdateVrboListingCancellationRequest $update_vrbo_listing_cancellation_request,
        ?string $vrbo_listing_number = null,
        string $contentType = self::contentTypes['updateVrboListingCancellation'][0]
    ): PromiseInterface
    {
        $returnType = '\Repull\Model\UpdateVrboListingCancellation200Response';
        $request = $this->updateVrboListingCancellationRequest($id, $update_vrbo_listing_cancellation_request, $vrbo_listing_number, $contentType);

        return $this->client
            ->sendAsync($request, $this->createHttpClientOption())
            ->then(
                function ($response) use ($returnType) {
                    if (in_array($returnType, ['\SplFileObject', '\Psr\Http\Message\StreamInterface'])) {
                        $content = $response->getBody(); //stream goes to serializer
                    } else {
                        $content = (string) $response->getBody();
                        if ($returnType !== 'string') {
                            $content = json_decode($content);
                        }
                    }

                    return [
                        ObjectSerializer::deserialize($content, $returnType, []),
                        $response->getStatusCode(),
                        $response->getHeaders()
                    ];
                },
                function ($exception) {
                    $response = $exception->getResponse();
                    $statusCode = $response->getStatusCode();
                    throw new ApiException(
                        sprintf(
                            '[%d] Error connecting to the API (%s)',
                            $statusCode,
                            $exception->getRequest()->getUri()
                        ),
                        $statusCode,
                        $response->getHeaders(),
                        (string) $response->getBody()
                    );
                }
            );
    }

    /**
     * Create request for operation 'updateVrboListingCancellation'
     *
     * @param  string $id Repull listing id (numeric string) of a listing linked to Vrbo. (required)
     * @param  \Repull\Model\UpdateVrboListingCancellationRequest $update_vrbo_listing_cancellation_request (required)
     * @param  string|null $vrbo_listing_number The Vrbo listing number to work on, when the listing is linked to more than one Vrbo unit. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['updateVrboListingCancellation'] to see the possible values for this operation
     *
     * @throws InvalidArgumentException
     * @return \GuzzleHttp\Psr7\Request
     */
    public function updateVrboListingCancellationRequest(
        string $id,
        \Repull\Model\UpdateVrboListingCancellationRequest $update_vrbo_listing_cancellation_request,
        ?string $vrbo_listing_number = null,
        string $contentType = self::contentTypes['updateVrboListingCancellation'][0]
    ): Request
    {

        // verify the required parameter 'id' is set
        if ($id === null || (is_array($id) && count($id) === 0)) {
            throw new InvalidArgumentException(
                'Missing the required parameter $id when calling updateVrboListingCancellation'
            );
        }

        // verify the required parameter 'update_vrbo_listing_cancellation_request' is set
        if ($update_vrbo_listing_cancellation_request === null || (is_array($update_vrbo_listing_cancellation_request) && count($update_vrbo_listing_cancellation_request) === 0)) {
            throw new InvalidArgumentException(
                'Missing the required parameter $update_vrbo_listing_cancellation_request when calling updateVrboListingCancellation'
            );
        }



        $resourcePath = '/v1/channels/vrbo/listings/{id}/cancellation';
        $formParams = [];
        $queryParams = [];
        $headerParams = [];
        $httpBody = '';
        $multipart = false;

        // query params
        $queryParams = array_merge($queryParams, ObjectSerializer::toQueryValue(
            $vrbo_listing_number,
            'vrboListingNumber', // param base name
            'string', // openApiType
            'form', // style
            true, // explode
            false, // required
            null // openApiFormat
        ) ?? []);


        // path params
        if ($id !== null) {
            $resourcePath = str_replace(
                '{id}',
                ObjectSerializer::toPathValue($id),
                $resourcePath
            );
        }


        $headers = $this->headerSelector->selectHeaders(
            ['application/json', ],
            $contentType,
            $multipart
        );

        // for model (json/xml)
        if (isset($update_vrbo_listing_cancellation_request)) {
            if (stripos($headers['Content-Type'], 'application/json') !== false) {
                # if Content-Type contains "application/json", json_encode the body
                $httpBody = \GuzzleHttp\Utils::jsonEncode(ObjectSerializer::sanitizeForSerialization($update_vrbo_listing_cancellation_request));
            } else {
                $httpBody = $update_vrbo_listing_cancellation_request;
            }
        } elseif (count($formParams) > 0) {
            if ($multipart) {
                $multipartContents = [];
                foreach ($formParams as $formParamName => $formParamValue) {
                    $formParamValueItems = is_array($formParamValue) ? $formParamValue : [$formParamValue];
                    foreach ($formParamValueItems as $formParamValueItem) {
                        $multipartContents[] = [
                            'name' => $formParamName,
                            'contents' => $formParamValueItem
                        ];
                    }
                }
                // for HTTP post (form)
                $httpBody = new MultipartStream($multipartContents);

            } elseif (stripos($headers['Content-Type'], 'application/json') !== false) {
                # if Content-Type contains "application/json", json_encode the form parameters
                $httpBody = \GuzzleHttp\Utils::jsonEncode($formParams);
            } else {
                // for HTTP post (form)
                $httpBody = ObjectSerializer::buildQuery($formParams);
            }
        }

        // this endpoint requires Bearer (API Key) authentication (access token)
        if (!empty($this->config->getAccessToken())) {
            $headers['Authorization'] = 'Bearer ' . $this->config->getAccessToken();
        }

        $defaultHeaders = [];
        if ($this->config->getUserAgent()) {
            $defaultHeaders['User-Agent'] = $this->config->getUserAgent();
        }

        $headers = array_merge(
            $defaultHeaders,
            $headerParams,
            $headers
        );

        $operationHost = $this->config->getHost();
        $query = ObjectSerializer::buildQuery($queryParams);
        return new Request(
            'PUT',
            $operationHost . $resourcePath . ($query ? "?{$query}" : ''),
            $headers,
            $httpBody
        );
    }

    /**
     * Operation updateVrboListingDescription
     *
     * Update the Vrbo headline and description
     *
     * @param  string $id Repull listing id (numeric string) of a listing linked to Vrbo. (required)
     * @param  \Repull\Model\UpdateVrboListingDescriptionRequest $update_vrbo_listing_description_request update_vrbo_listing_description_request (required)
     * @param  string|null $vrbo_listing_number The Vrbo listing number to work on, when the listing is linked to more than one Vrbo unit. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['updateVrboListingDescription'] to see the possible values for this operation
     *
     * @throws ApiException on non-2xx response or if the response body is not in the expected format
     * @throws InvalidArgumentException
     * @return \Repull\Model\UpdateVrboListingDescription200Response|\Repull\Model\Error
     */
    public function updateVrboListingDescription(
        string $id,
        \Repull\Model\UpdateVrboListingDescriptionRequest $update_vrbo_listing_description_request,
        ?string $vrbo_listing_number = null,
        string $contentType = self::contentTypes['updateVrboListingDescription'][0]
    ): \Repull\Model\UpdateVrboListingDescription200Response|\Repull\Model\Error
    {
        list($response) = $this->updateVrboListingDescriptionWithHttpInfo($id, $update_vrbo_listing_description_request, $vrbo_listing_number, $contentType);
        return $response;
    }

    /**
     * Operation updateVrboListingDescriptionWithHttpInfo
     *
     * Update the Vrbo headline and description
     *
     * @param  string $id Repull listing id (numeric string) of a listing linked to Vrbo. (required)
     * @param  \Repull\Model\UpdateVrboListingDescriptionRequest $update_vrbo_listing_description_request (required)
     * @param  string|null $vrbo_listing_number The Vrbo listing number to work on, when the listing is linked to more than one Vrbo unit. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['updateVrboListingDescription'] to see the possible values for this operation
     *
     * @throws ApiException on non-2xx response or if the response body is not in the expected format
     * @throws InvalidArgumentException
     * @return array of \Repull\Model\UpdateVrboListingDescription200Response|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error, HTTP status code, HTTP response headers (array of strings)
     */
    public function updateVrboListingDescriptionWithHttpInfo(
        string $id,
        \Repull\Model\UpdateVrboListingDescriptionRequest $update_vrbo_listing_description_request,
        ?string $vrbo_listing_number = null,
        string $contentType = self::contentTypes['updateVrboListingDescription'][0]
    ): array
    {
        $request = $this->updateVrboListingDescriptionRequest($id, $update_vrbo_listing_description_request, $vrbo_listing_number, $contentType);

        try {
            $options = $this->createHttpClientOption();
            try {
                $response = $this->client->send($request, $options);
            } catch (RequestException $e) {
                throw new ApiException(
                    "[{$e->getCode()}] {$e->getMessage()}",
                    (int) $e->getCode(),
                    $e->getResponse() ? $e->getResponse()->getHeaders() : null,
                    $e->getResponse() ? (string) $e->getResponse()->getBody() : null
                );
            } catch (ConnectException $e) {
                throw new ApiException(
                    "[{$e->getCode()}] {$e->getMessage()}",
                    (int) $e->getCode(),
                    null,
                    null
                );
            }

            $statusCode = $response->getStatusCode();

            switch($statusCode) {
                case 200:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\UpdateVrboListingDescription200Response',
                        $request,
                        $response,
                    );
                case 401:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 403:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 404:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 409:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 422:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 500:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 502:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 503:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 504:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
            }
            

            if ($statusCode < 200 || $statusCode > 299) {
                throw new ApiException(
                    sprintf(
                        '[%d] Error connecting to the API (%s)',
                        $statusCode,
                        (string) $request->getUri()
                    ),
                    $statusCode,
                    $response->getHeaders(),
                    (string) $response->getBody()
                );
            }

            return $this->handleResponseWithDataType(
                '\Repull\Model\UpdateVrboListingDescription200Response',
                $request,
                $response,
            );
        } catch (ApiException $e) {
            switch ($e->getCode()) {
                case 200:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\UpdateVrboListingDescription200Response',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 401:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 403:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 404:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 409:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 422:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 500:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 502:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 503:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 504:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
            }
        
            throw $e;
        }
    }

    /**
     * Operation updateVrboListingDescriptionAsync
     *
     * Update the Vrbo headline and description
     *
     * @param  string $id Repull listing id (numeric string) of a listing linked to Vrbo. (required)
     * @param  \Repull\Model\UpdateVrboListingDescriptionRequest $update_vrbo_listing_description_request (required)
     * @param  string|null $vrbo_listing_number The Vrbo listing number to work on, when the listing is linked to more than one Vrbo unit. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['updateVrboListingDescription'] to see the possible values for this operation
     *
     * @throws InvalidArgumentException
     * @return PromiseInterface
     */
    public function updateVrboListingDescriptionAsync(
        string $id,
        \Repull\Model\UpdateVrboListingDescriptionRequest $update_vrbo_listing_description_request,
        ?string $vrbo_listing_number = null,
        string $contentType = self::contentTypes['updateVrboListingDescription'][0]
    ): PromiseInterface
    {
        return $this->updateVrboListingDescriptionAsyncWithHttpInfo($id, $update_vrbo_listing_description_request, $vrbo_listing_number, $contentType)
            ->then(
                function ($response) {
                    return $response[0];
                }
            );
    }

    /**
     * Operation updateVrboListingDescriptionAsyncWithHttpInfo
     *
     * Update the Vrbo headline and description
     *
     * @param  string $id Repull listing id (numeric string) of a listing linked to Vrbo. (required)
     * @param  \Repull\Model\UpdateVrboListingDescriptionRequest $update_vrbo_listing_description_request (required)
     * @param  string|null $vrbo_listing_number The Vrbo listing number to work on, when the listing is linked to more than one Vrbo unit. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['updateVrboListingDescription'] to see the possible values for this operation
     *
     * @throws InvalidArgumentException
     * @return PromiseInterface
     */
    public function updateVrboListingDescriptionAsyncWithHttpInfo(
        string $id,
        \Repull\Model\UpdateVrboListingDescriptionRequest $update_vrbo_listing_description_request,
        ?string $vrbo_listing_number = null,
        string $contentType = self::contentTypes['updateVrboListingDescription'][0]
    ): PromiseInterface
    {
        $returnType = '\Repull\Model\UpdateVrboListingDescription200Response';
        $request = $this->updateVrboListingDescriptionRequest($id, $update_vrbo_listing_description_request, $vrbo_listing_number, $contentType);

        return $this->client
            ->sendAsync($request, $this->createHttpClientOption())
            ->then(
                function ($response) use ($returnType) {
                    if (in_array($returnType, ['\SplFileObject', '\Psr\Http\Message\StreamInterface'])) {
                        $content = $response->getBody(); //stream goes to serializer
                    } else {
                        $content = (string) $response->getBody();
                        if ($returnType !== 'string') {
                            $content = json_decode($content);
                        }
                    }

                    return [
                        ObjectSerializer::deserialize($content, $returnType, []),
                        $response->getStatusCode(),
                        $response->getHeaders()
                    ];
                },
                function ($exception) {
                    $response = $exception->getResponse();
                    $statusCode = $response->getStatusCode();
                    throw new ApiException(
                        sprintf(
                            '[%d] Error connecting to the API (%s)',
                            $statusCode,
                            $exception->getRequest()->getUri()
                        ),
                        $statusCode,
                        $response->getHeaders(),
                        (string) $response->getBody()
                    );
                }
            );
    }

    /**
     * Create request for operation 'updateVrboListingDescription'
     *
     * @param  string $id Repull listing id (numeric string) of a listing linked to Vrbo. (required)
     * @param  \Repull\Model\UpdateVrboListingDescriptionRequest $update_vrbo_listing_description_request (required)
     * @param  string|null $vrbo_listing_number The Vrbo listing number to work on, when the listing is linked to more than one Vrbo unit. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['updateVrboListingDescription'] to see the possible values for this operation
     *
     * @throws InvalidArgumentException
     * @return \GuzzleHttp\Psr7\Request
     */
    public function updateVrboListingDescriptionRequest(
        string $id,
        \Repull\Model\UpdateVrboListingDescriptionRequest $update_vrbo_listing_description_request,
        ?string $vrbo_listing_number = null,
        string $contentType = self::contentTypes['updateVrboListingDescription'][0]
    ): Request
    {

        // verify the required parameter 'id' is set
        if ($id === null || (is_array($id) && count($id) === 0)) {
            throw new InvalidArgumentException(
                'Missing the required parameter $id when calling updateVrboListingDescription'
            );
        }

        // verify the required parameter 'update_vrbo_listing_description_request' is set
        if ($update_vrbo_listing_description_request === null || (is_array($update_vrbo_listing_description_request) && count($update_vrbo_listing_description_request) === 0)) {
            throw new InvalidArgumentException(
                'Missing the required parameter $update_vrbo_listing_description_request when calling updateVrboListingDescription'
            );
        }



        $resourcePath = '/v1/channels/vrbo/listings/{id}/description';
        $formParams = [];
        $queryParams = [];
        $headerParams = [];
        $httpBody = '';
        $multipart = false;

        // query params
        $queryParams = array_merge($queryParams, ObjectSerializer::toQueryValue(
            $vrbo_listing_number,
            'vrboListingNumber', // param base name
            'string', // openApiType
            'form', // style
            true, // explode
            false, // required
            null // openApiFormat
        ) ?? []);


        // path params
        if ($id !== null) {
            $resourcePath = str_replace(
                '{id}',
                ObjectSerializer::toPathValue($id),
                $resourcePath
            );
        }


        $headers = $this->headerSelector->selectHeaders(
            ['application/json', ],
            $contentType,
            $multipart
        );

        // for model (json/xml)
        if (isset($update_vrbo_listing_description_request)) {
            if (stripos($headers['Content-Type'], 'application/json') !== false) {
                # if Content-Type contains "application/json", json_encode the body
                $httpBody = \GuzzleHttp\Utils::jsonEncode(ObjectSerializer::sanitizeForSerialization($update_vrbo_listing_description_request));
            } else {
                $httpBody = $update_vrbo_listing_description_request;
            }
        } elseif (count($formParams) > 0) {
            if ($multipart) {
                $multipartContents = [];
                foreach ($formParams as $formParamName => $formParamValue) {
                    $formParamValueItems = is_array($formParamValue) ? $formParamValue : [$formParamValue];
                    foreach ($formParamValueItems as $formParamValueItem) {
                        $multipartContents[] = [
                            'name' => $formParamName,
                            'contents' => $formParamValueItem
                        ];
                    }
                }
                // for HTTP post (form)
                $httpBody = new MultipartStream($multipartContents);

            } elseif (stripos($headers['Content-Type'], 'application/json') !== false) {
                # if Content-Type contains "application/json", json_encode the form parameters
                $httpBody = \GuzzleHttp\Utils::jsonEncode($formParams);
            } else {
                // for HTTP post (form)
                $httpBody = ObjectSerializer::buildQuery($formParams);
            }
        }

        // this endpoint requires Bearer (API Key) authentication (access token)
        if (!empty($this->config->getAccessToken())) {
            $headers['Authorization'] = 'Bearer ' . $this->config->getAccessToken();
        }

        $defaultHeaders = [];
        if ($this->config->getUserAgent()) {
            $defaultHeaders['User-Agent'] = $this->config->getUserAgent();
        }

        $headers = array_merge(
            $defaultHeaders,
            $headerParams,
            $headers
        );

        $operationHost = $this->config->getHost();
        $query = ObjectSerializer::buildQuery($queryParams);
        return new Request(
            'PUT',
            $operationHost . $resourcePath . ($query ? "?{$query}" : ''),
            $headers,
            $httpBody
        );
    }

    /**
     * Operation updateVrboListingFees
     *
     * Update the Vrbo fees
     *
     * @param  string $id Repull listing id (numeric string) of a listing linked to Vrbo. (required)
     * @param  \Repull\Model\UpdateVrboListingFeesRequest $update_vrbo_listing_fees_request update_vrbo_listing_fees_request (required)
     * @param  string|null $vrbo_listing_number The Vrbo listing number to work on, when the listing is linked to more than one Vrbo unit. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['updateVrboListingFees'] to see the possible values for this operation
     *
     * @throws ApiException on non-2xx response or if the response body is not in the expected format
     * @throws InvalidArgumentException
     * @return \Repull\Model\UpdateVrboListingFees200Response|\Repull\Model\Error
     */
    public function updateVrboListingFees(
        string $id,
        \Repull\Model\UpdateVrboListingFeesRequest $update_vrbo_listing_fees_request,
        ?string $vrbo_listing_number = null,
        string $contentType = self::contentTypes['updateVrboListingFees'][0]
    ): \Repull\Model\UpdateVrboListingFees200Response|\Repull\Model\Error
    {
        list($response) = $this->updateVrboListingFeesWithHttpInfo($id, $update_vrbo_listing_fees_request, $vrbo_listing_number, $contentType);
        return $response;
    }

    /**
     * Operation updateVrboListingFeesWithHttpInfo
     *
     * Update the Vrbo fees
     *
     * @param  string $id Repull listing id (numeric string) of a listing linked to Vrbo. (required)
     * @param  \Repull\Model\UpdateVrboListingFeesRequest $update_vrbo_listing_fees_request (required)
     * @param  string|null $vrbo_listing_number The Vrbo listing number to work on, when the listing is linked to more than one Vrbo unit. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['updateVrboListingFees'] to see the possible values for this operation
     *
     * @throws ApiException on non-2xx response or if the response body is not in the expected format
     * @throws InvalidArgumentException
     * @return array of \Repull\Model\UpdateVrboListingFees200Response|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error, HTTP status code, HTTP response headers (array of strings)
     */
    public function updateVrboListingFeesWithHttpInfo(
        string $id,
        \Repull\Model\UpdateVrboListingFeesRequest $update_vrbo_listing_fees_request,
        ?string $vrbo_listing_number = null,
        string $contentType = self::contentTypes['updateVrboListingFees'][0]
    ): array
    {
        $request = $this->updateVrboListingFeesRequest($id, $update_vrbo_listing_fees_request, $vrbo_listing_number, $contentType);

        try {
            $options = $this->createHttpClientOption();
            try {
                $response = $this->client->send($request, $options);
            } catch (RequestException $e) {
                throw new ApiException(
                    "[{$e->getCode()}] {$e->getMessage()}",
                    (int) $e->getCode(),
                    $e->getResponse() ? $e->getResponse()->getHeaders() : null,
                    $e->getResponse() ? (string) $e->getResponse()->getBody() : null
                );
            } catch (ConnectException $e) {
                throw new ApiException(
                    "[{$e->getCode()}] {$e->getMessage()}",
                    (int) $e->getCode(),
                    null,
                    null
                );
            }

            $statusCode = $response->getStatusCode();

            switch($statusCode) {
                case 200:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\UpdateVrboListingFees200Response',
                        $request,
                        $response,
                    );
                case 401:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 403:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 404:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 409:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 422:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 500:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 502:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 503:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 504:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
            }
            

            if ($statusCode < 200 || $statusCode > 299) {
                throw new ApiException(
                    sprintf(
                        '[%d] Error connecting to the API (%s)',
                        $statusCode,
                        (string) $request->getUri()
                    ),
                    $statusCode,
                    $response->getHeaders(),
                    (string) $response->getBody()
                );
            }

            return $this->handleResponseWithDataType(
                '\Repull\Model\UpdateVrboListingFees200Response',
                $request,
                $response,
            );
        } catch (ApiException $e) {
            switch ($e->getCode()) {
                case 200:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\UpdateVrboListingFees200Response',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 401:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 403:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 404:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 409:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 422:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 500:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 502:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 503:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 504:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
            }
        
            throw $e;
        }
    }

    /**
     * Operation updateVrboListingFeesAsync
     *
     * Update the Vrbo fees
     *
     * @param  string $id Repull listing id (numeric string) of a listing linked to Vrbo. (required)
     * @param  \Repull\Model\UpdateVrboListingFeesRequest $update_vrbo_listing_fees_request (required)
     * @param  string|null $vrbo_listing_number The Vrbo listing number to work on, when the listing is linked to more than one Vrbo unit. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['updateVrboListingFees'] to see the possible values for this operation
     *
     * @throws InvalidArgumentException
     * @return PromiseInterface
     */
    public function updateVrboListingFeesAsync(
        string $id,
        \Repull\Model\UpdateVrboListingFeesRequest $update_vrbo_listing_fees_request,
        ?string $vrbo_listing_number = null,
        string $contentType = self::contentTypes['updateVrboListingFees'][0]
    ): PromiseInterface
    {
        return $this->updateVrboListingFeesAsyncWithHttpInfo($id, $update_vrbo_listing_fees_request, $vrbo_listing_number, $contentType)
            ->then(
                function ($response) {
                    return $response[0];
                }
            );
    }

    /**
     * Operation updateVrboListingFeesAsyncWithHttpInfo
     *
     * Update the Vrbo fees
     *
     * @param  string $id Repull listing id (numeric string) of a listing linked to Vrbo. (required)
     * @param  \Repull\Model\UpdateVrboListingFeesRequest $update_vrbo_listing_fees_request (required)
     * @param  string|null $vrbo_listing_number The Vrbo listing number to work on, when the listing is linked to more than one Vrbo unit. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['updateVrboListingFees'] to see the possible values for this operation
     *
     * @throws InvalidArgumentException
     * @return PromiseInterface
     */
    public function updateVrboListingFeesAsyncWithHttpInfo(
        string $id,
        \Repull\Model\UpdateVrboListingFeesRequest $update_vrbo_listing_fees_request,
        ?string $vrbo_listing_number = null,
        string $contentType = self::contentTypes['updateVrboListingFees'][0]
    ): PromiseInterface
    {
        $returnType = '\Repull\Model\UpdateVrboListingFees200Response';
        $request = $this->updateVrboListingFeesRequest($id, $update_vrbo_listing_fees_request, $vrbo_listing_number, $contentType);

        return $this->client
            ->sendAsync($request, $this->createHttpClientOption())
            ->then(
                function ($response) use ($returnType) {
                    if (in_array($returnType, ['\SplFileObject', '\Psr\Http\Message\StreamInterface'])) {
                        $content = $response->getBody(); //stream goes to serializer
                    } else {
                        $content = (string) $response->getBody();
                        if ($returnType !== 'string') {
                            $content = json_decode($content);
                        }
                    }

                    return [
                        ObjectSerializer::deserialize($content, $returnType, []),
                        $response->getStatusCode(),
                        $response->getHeaders()
                    ];
                },
                function ($exception) {
                    $response = $exception->getResponse();
                    $statusCode = $response->getStatusCode();
                    throw new ApiException(
                        sprintf(
                            '[%d] Error connecting to the API (%s)',
                            $statusCode,
                            $exception->getRequest()->getUri()
                        ),
                        $statusCode,
                        $response->getHeaders(),
                        (string) $response->getBody()
                    );
                }
            );
    }

    /**
     * Create request for operation 'updateVrboListingFees'
     *
     * @param  string $id Repull listing id (numeric string) of a listing linked to Vrbo. (required)
     * @param  \Repull\Model\UpdateVrboListingFeesRequest $update_vrbo_listing_fees_request (required)
     * @param  string|null $vrbo_listing_number The Vrbo listing number to work on, when the listing is linked to more than one Vrbo unit. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['updateVrboListingFees'] to see the possible values for this operation
     *
     * @throws InvalidArgumentException
     * @return \GuzzleHttp\Psr7\Request
     */
    public function updateVrboListingFeesRequest(
        string $id,
        \Repull\Model\UpdateVrboListingFeesRequest $update_vrbo_listing_fees_request,
        ?string $vrbo_listing_number = null,
        string $contentType = self::contentTypes['updateVrboListingFees'][0]
    ): Request
    {

        // verify the required parameter 'id' is set
        if ($id === null || (is_array($id) && count($id) === 0)) {
            throw new InvalidArgumentException(
                'Missing the required parameter $id when calling updateVrboListingFees'
            );
        }

        // verify the required parameter 'update_vrbo_listing_fees_request' is set
        if ($update_vrbo_listing_fees_request === null || (is_array($update_vrbo_listing_fees_request) && count($update_vrbo_listing_fees_request) === 0)) {
            throw new InvalidArgumentException(
                'Missing the required parameter $update_vrbo_listing_fees_request when calling updateVrboListingFees'
            );
        }



        $resourcePath = '/v1/channels/vrbo/listings/{id}/fees';
        $formParams = [];
        $queryParams = [];
        $headerParams = [];
        $httpBody = '';
        $multipart = false;

        // query params
        $queryParams = array_merge($queryParams, ObjectSerializer::toQueryValue(
            $vrbo_listing_number,
            'vrboListingNumber', // param base name
            'string', // openApiType
            'form', // style
            true, // explode
            false, // required
            null // openApiFormat
        ) ?? []);


        // path params
        if ($id !== null) {
            $resourcePath = str_replace(
                '{id}',
                ObjectSerializer::toPathValue($id),
                $resourcePath
            );
        }


        $headers = $this->headerSelector->selectHeaders(
            ['application/json', ],
            $contentType,
            $multipart
        );

        // for model (json/xml)
        if (isset($update_vrbo_listing_fees_request)) {
            if (stripos($headers['Content-Type'], 'application/json') !== false) {
                # if Content-Type contains "application/json", json_encode the body
                $httpBody = \GuzzleHttp\Utils::jsonEncode(ObjectSerializer::sanitizeForSerialization($update_vrbo_listing_fees_request));
            } else {
                $httpBody = $update_vrbo_listing_fees_request;
            }
        } elseif (count($formParams) > 0) {
            if ($multipart) {
                $multipartContents = [];
                foreach ($formParams as $formParamName => $formParamValue) {
                    $formParamValueItems = is_array($formParamValue) ? $formParamValue : [$formParamValue];
                    foreach ($formParamValueItems as $formParamValueItem) {
                        $multipartContents[] = [
                            'name' => $formParamName,
                            'contents' => $formParamValueItem
                        ];
                    }
                }
                // for HTTP post (form)
                $httpBody = new MultipartStream($multipartContents);

            } elseif (stripos($headers['Content-Type'], 'application/json') !== false) {
                # if Content-Type contains "application/json", json_encode the form parameters
                $httpBody = \GuzzleHttp\Utils::jsonEncode($formParams);
            } else {
                // for HTTP post (form)
                $httpBody = ObjectSerializer::buildQuery($formParams);
            }
        }

        // this endpoint requires Bearer (API Key) authentication (access token)
        if (!empty($this->config->getAccessToken())) {
            $headers['Authorization'] = 'Bearer ' . $this->config->getAccessToken();
        }

        $defaultHeaders = [];
        if ($this->config->getUserAgent()) {
            $defaultHeaders['User-Agent'] = $this->config->getUserAgent();
        }

        $headers = array_merge(
            $defaultHeaders,
            $headerParams,
            $headers
        );

        $operationHost = $this->config->getHost();
        $query = ObjectSerializer::buildQuery($queryParams);
        return new Request(
            'PUT',
            $operationHost . $resourcePath . ($query ? "?{$query}" : ''),
            $headers,
            $httpBody
        );
    }

    /**
     * Operation updateVrboListingHouseRules
     *
     * Update the Vrbo house rules
     *
     * @param  string $id Repull listing id (numeric string) of a listing linked to Vrbo. (required)
     * @param  \Repull\Model\UpdateVrboListingHouseRulesRequest $update_vrbo_listing_house_rules_request update_vrbo_listing_house_rules_request (required)
     * @param  string|null $vrbo_listing_number The Vrbo listing number to work on, when the listing is linked to more than one Vrbo unit. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['updateVrboListingHouseRules'] to see the possible values for this operation
     *
     * @throws ApiException on non-2xx response or if the response body is not in the expected format
     * @throws InvalidArgumentException
     * @return \Repull\Model\UpdateVrboListingHouseRules200Response|\Repull\Model\Error
     */
    public function updateVrboListingHouseRules(
        string $id,
        \Repull\Model\UpdateVrboListingHouseRulesRequest $update_vrbo_listing_house_rules_request,
        ?string $vrbo_listing_number = null,
        string $contentType = self::contentTypes['updateVrboListingHouseRules'][0]
    ): \Repull\Model\UpdateVrboListingHouseRules200Response|\Repull\Model\Error
    {
        list($response) = $this->updateVrboListingHouseRulesWithHttpInfo($id, $update_vrbo_listing_house_rules_request, $vrbo_listing_number, $contentType);
        return $response;
    }

    /**
     * Operation updateVrboListingHouseRulesWithHttpInfo
     *
     * Update the Vrbo house rules
     *
     * @param  string $id Repull listing id (numeric string) of a listing linked to Vrbo. (required)
     * @param  \Repull\Model\UpdateVrboListingHouseRulesRequest $update_vrbo_listing_house_rules_request (required)
     * @param  string|null $vrbo_listing_number The Vrbo listing number to work on, when the listing is linked to more than one Vrbo unit. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['updateVrboListingHouseRules'] to see the possible values for this operation
     *
     * @throws ApiException on non-2xx response or if the response body is not in the expected format
     * @throws InvalidArgumentException
     * @return array of \Repull\Model\UpdateVrboListingHouseRules200Response|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error, HTTP status code, HTTP response headers (array of strings)
     */
    public function updateVrboListingHouseRulesWithHttpInfo(
        string $id,
        \Repull\Model\UpdateVrboListingHouseRulesRequest $update_vrbo_listing_house_rules_request,
        ?string $vrbo_listing_number = null,
        string $contentType = self::contentTypes['updateVrboListingHouseRules'][0]
    ): array
    {
        $request = $this->updateVrboListingHouseRulesRequest($id, $update_vrbo_listing_house_rules_request, $vrbo_listing_number, $contentType);

        try {
            $options = $this->createHttpClientOption();
            try {
                $response = $this->client->send($request, $options);
            } catch (RequestException $e) {
                throw new ApiException(
                    "[{$e->getCode()}] {$e->getMessage()}",
                    (int) $e->getCode(),
                    $e->getResponse() ? $e->getResponse()->getHeaders() : null,
                    $e->getResponse() ? (string) $e->getResponse()->getBody() : null
                );
            } catch (ConnectException $e) {
                throw new ApiException(
                    "[{$e->getCode()}] {$e->getMessage()}",
                    (int) $e->getCode(),
                    null,
                    null
                );
            }

            $statusCode = $response->getStatusCode();

            switch($statusCode) {
                case 200:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\UpdateVrboListingHouseRules200Response',
                        $request,
                        $response,
                    );
                case 401:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 403:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 404:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 409:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 422:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 500:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 502:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 503:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 504:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
            }
            

            if ($statusCode < 200 || $statusCode > 299) {
                throw new ApiException(
                    sprintf(
                        '[%d] Error connecting to the API (%s)',
                        $statusCode,
                        (string) $request->getUri()
                    ),
                    $statusCode,
                    $response->getHeaders(),
                    (string) $response->getBody()
                );
            }

            return $this->handleResponseWithDataType(
                '\Repull\Model\UpdateVrboListingHouseRules200Response',
                $request,
                $response,
            );
        } catch (ApiException $e) {
            switch ($e->getCode()) {
                case 200:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\UpdateVrboListingHouseRules200Response',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 401:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 403:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 404:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 409:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 422:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 500:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 502:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 503:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 504:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
            }
        
            throw $e;
        }
    }

    /**
     * Operation updateVrboListingHouseRulesAsync
     *
     * Update the Vrbo house rules
     *
     * @param  string $id Repull listing id (numeric string) of a listing linked to Vrbo. (required)
     * @param  \Repull\Model\UpdateVrboListingHouseRulesRequest $update_vrbo_listing_house_rules_request (required)
     * @param  string|null $vrbo_listing_number The Vrbo listing number to work on, when the listing is linked to more than one Vrbo unit. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['updateVrboListingHouseRules'] to see the possible values for this operation
     *
     * @throws InvalidArgumentException
     * @return PromiseInterface
     */
    public function updateVrboListingHouseRulesAsync(
        string $id,
        \Repull\Model\UpdateVrboListingHouseRulesRequest $update_vrbo_listing_house_rules_request,
        ?string $vrbo_listing_number = null,
        string $contentType = self::contentTypes['updateVrboListingHouseRules'][0]
    ): PromiseInterface
    {
        return $this->updateVrboListingHouseRulesAsyncWithHttpInfo($id, $update_vrbo_listing_house_rules_request, $vrbo_listing_number, $contentType)
            ->then(
                function ($response) {
                    return $response[0];
                }
            );
    }

    /**
     * Operation updateVrboListingHouseRulesAsyncWithHttpInfo
     *
     * Update the Vrbo house rules
     *
     * @param  string $id Repull listing id (numeric string) of a listing linked to Vrbo. (required)
     * @param  \Repull\Model\UpdateVrboListingHouseRulesRequest $update_vrbo_listing_house_rules_request (required)
     * @param  string|null $vrbo_listing_number The Vrbo listing number to work on, when the listing is linked to more than one Vrbo unit. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['updateVrboListingHouseRules'] to see the possible values for this operation
     *
     * @throws InvalidArgumentException
     * @return PromiseInterface
     */
    public function updateVrboListingHouseRulesAsyncWithHttpInfo(
        string $id,
        \Repull\Model\UpdateVrboListingHouseRulesRequest $update_vrbo_listing_house_rules_request,
        ?string $vrbo_listing_number = null,
        string $contentType = self::contentTypes['updateVrboListingHouseRules'][0]
    ): PromiseInterface
    {
        $returnType = '\Repull\Model\UpdateVrboListingHouseRules200Response';
        $request = $this->updateVrboListingHouseRulesRequest($id, $update_vrbo_listing_house_rules_request, $vrbo_listing_number, $contentType);

        return $this->client
            ->sendAsync($request, $this->createHttpClientOption())
            ->then(
                function ($response) use ($returnType) {
                    if (in_array($returnType, ['\SplFileObject', '\Psr\Http\Message\StreamInterface'])) {
                        $content = $response->getBody(); //stream goes to serializer
                    } else {
                        $content = (string) $response->getBody();
                        if ($returnType !== 'string') {
                            $content = json_decode($content);
                        }
                    }

                    return [
                        ObjectSerializer::deserialize($content, $returnType, []),
                        $response->getStatusCode(),
                        $response->getHeaders()
                    ];
                },
                function ($exception) {
                    $response = $exception->getResponse();
                    $statusCode = $response->getStatusCode();
                    throw new ApiException(
                        sprintf(
                            '[%d] Error connecting to the API (%s)',
                            $statusCode,
                            $exception->getRequest()->getUri()
                        ),
                        $statusCode,
                        $response->getHeaders(),
                        (string) $response->getBody()
                    );
                }
            );
    }

    /**
     * Create request for operation 'updateVrboListingHouseRules'
     *
     * @param  string $id Repull listing id (numeric string) of a listing linked to Vrbo. (required)
     * @param  \Repull\Model\UpdateVrboListingHouseRulesRequest $update_vrbo_listing_house_rules_request (required)
     * @param  string|null $vrbo_listing_number The Vrbo listing number to work on, when the listing is linked to more than one Vrbo unit. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['updateVrboListingHouseRules'] to see the possible values for this operation
     *
     * @throws InvalidArgumentException
     * @return \GuzzleHttp\Psr7\Request
     */
    public function updateVrboListingHouseRulesRequest(
        string $id,
        \Repull\Model\UpdateVrboListingHouseRulesRequest $update_vrbo_listing_house_rules_request,
        ?string $vrbo_listing_number = null,
        string $contentType = self::contentTypes['updateVrboListingHouseRules'][0]
    ): Request
    {

        // verify the required parameter 'id' is set
        if ($id === null || (is_array($id) && count($id) === 0)) {
            throw new InvalidArgumentException(
                'Missing the required parameter $id when calling updateVrboListingHouseRules'
            );
        }

        // verify the required parameter 'update_vrbo_listing_house_rules_request' is set
        if ($update_vrbo_listing_house_rules_request === null || (is_array($update_vrbo_listing_house_rules_request) && count($update_vrbo_listing_house_rules_request) === 0)) {
            throw new InvalidArgumentException(
                'Missing the required parameter $update_vrbo_listing_house_rules_request when calling updateVrboListingHouseRules'
            );
        }



        $resourcePath = '/v1/channels/vrbo/listings/{id}/house-rules';
        $formParams = [];
        $queryParams = [];
        $headerParams = [];
        $httpBody = '';
        $multipart = false;

        // query params
        $queryParams = array_merge($queryParams, ObjectSerializer::toQueryValue(
            $vrbo_listing_number,
            'vrboListingNumber', // param base name
            'string', // openApiType
            'form', // style
            true, // explode
            false, // required
            null // openApiFormat
        ) ?? []);


        // path params
        if ($id !== null) {
            $resourcePath = str_replace(
                '{id}',
                ObjectSerializer::toPathValue($id),
                $resourcePath
            );
        }


        $headers = $this->headerSelector->selectHeaders(
            ['application/json', ],
            $contentType,
            $multipart
        );

        // for model (json/xml)
        if (isset($update_vrbo_listing_house_rules_request)) {
            if (stripos($headers['Content-Type'], 'application/json') !== false) {
                # if Content-Type contains "application/json", json_encode the body
                $httpBody = \GuzzleHttp\Utils::jsonEncode(ObjectSerializer::sanitizeForSerialization($update_vrbo_listing_house_rules_request));
            } else {
                $httpBody = $update_vrbo_listing_house_rules_request;
            }
        } elseif (count($formParams) > 0) {
            if ($multipart) {
                $multipartContents = [];
                foreach ($formParams as $formParamName => $formParamValue) {
                    $formParamValueItems = is_array($formParamValue) ? $formParamValue : [$formParamValue];
                    foreach ($formParamValueItems as $formParamValueItem) {
                        $multipartContents[] = [
                            'name' => $formParamName,
                            'contents' => $formParamValueItem
                        ];
                    }
                }
                // for HTTP post (form)
                $httpBody = new MultipartStream($multipartContents);

            } elseif (stripos($headers['Content-Type'], 'application/json') !== false) {
                # if Content-Type contains "application/json", json_encode the form parameters
                $httpBody = \GuzzleHttp\Utils::jsonEncode($formParams);
            } else {
                // for HTTP post (form)
                $httpBody = ObjectSerializer::buildQuery($formParams);
            }
        }

        // this endpoint requires Bearer (API Key) authentication (access token)
        if (!empty($this->config->getAccessToken())) {
            $headers['Authorization'] = 'Bearer ' . $this->config->getAccessToken();
        }

        $defaultHeaders = [];
        if ($this->config->getUserAgent()) {
            $defaultHeaders['User-Agent'] = $this->config->getUserAgent();
        }

        $headers = array_merge(
            $defaultHeaders,
            $headerParams,
            $headers
        );

        $operationHost = $this->config->getHost();
        $query = ObjectSerializer::buildQuery($queryParams);
        return new Request(
            'PUT',
            $operationHost . $resourcePath . ($query ? "?{$query}" : ''),
            $headers,
            $httpBody
        );
    }

    /**
     * Operation updateVrboListingLocation
     *
     * Update the Vrbo map setting
     *
     * @param  string $id Repull listing id (numeric string) of a listing linked to Vrbo. (required)
     * @param  \Repull\Model\UpdateVrboListingLocationRequest $update_vrbo_listing_location_request update_vrbo_listing_location_request (required)
     * @param  string|null $vrbo_listing_number The Vrbo listing number to work on, when the listing is linked to more than one Vrbo unit. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['updateVrboListingLocation'] to see the possible values for this operation
     *
     * @throws ApiException on non-2xx response or if the response body is not in the expected format
     * @throws InvalidArgumentException
     * @return \Repull\Model\UpdateVrboListingLocation200Response|\Repull\Model\Error
     */
    public function updateVrboListingLocation(
        string $id,
        \Repull\Model\UpdateVrboListingLocationRequest $update_vrbo_listing_location_request,
        ?string $vrbo_listing_number = null,
        string $contentType = self::contentTypes['updateVrboListingLocation'][0]
    ): \Repull\Model\UpdateVrboListingLocation200Response|\Repull\Model\Error
    {
        list($response) = $this->updateVrboListingLocationWithHttpInfo($id, $update_vrbo_listing_location_request, $vrbo_listing_number, $contentType);
        return $response;
    }

    /**
     * Operation updateVrboListingLocationWithHttpInfo
     *
     * Update the Vrbo map setting
     *
     * @param  string $id Repull listing id (numeric string) of a listing linked to Vrbo. (required)
     * @param  \Repull\Model\UpdateVrboListingLocationRequest $update_vrbo_listing_location_request (required)
     * @param  string|null $vrbo_listing_number The Vrbo listing number to work on, when the listing is linked to more than one Vrbo unit. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['updateVrboListingLocation'] to see the possible values for this operation
     *
     * @throws ApiException on non-2xx response or if the response body is not in the expected format
     * @throws InvalidArgumentException
     * @return array of \Repull\Model\UpdateVrboListingLocation200Response|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error, HTTP status code, HTTP response headers (array of strings)
     */
    public function updateVrboListingLocationWithHttpInfo(
        string $id,
        \Repull\Model\UpdateVrboListingLocationRequest $update_vrbo_listing_location_request,
        ?string $vrbo_listing_number = null,
        string $contentType = self::contentTypes['updateVrboListingLocation'][0]
    ): array
    {
        $request = $this->updateVrboListingLocationRequest($id, $update_vrbo_listing_location_request, $vrbo_listing_number, $contentType);

        try {
            $options = $this->createHttpClientOption();
            try {
                $response = $this->client->send($request, $options);
            } catch (RequestException $e) {
                throw new ApiException(
                    "[{$e->getCode()}] {$e->getMessage()}",
                    (int) $e->getCode(),
                    $e->getResponse() ? $e->getResponse()->getHeaders() : null,
                    $e->getResponse() ? (string) $e->getResponse()->getBody() : null
                );
            } catch (ConnectException $e) {
                throw new ApiException(
                    "[{$e->getCode()}] {$e->getMessage()}",
                    (int) $e->getCode(),
                    null,
                    null
                );
            }

            $statusCode = $response->getStatusCode();

            switch($statusCode) {
                case 200:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\UpdateVrboListingLocation200Response',
                        $request,
                        $response,
                    );
                case 401:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 403:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 404:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 409:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 422:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 500:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 502:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 503:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 504:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
            }
            

            if ($statusCode < 200 || $statusCode > 299) {
                throw new ApiException(
                    sprintf(
                        '[%d] Error connecting to the API (%s)',
                        $statusCode,
                        (string) $request->getUri()
                    ),
                    $statusCode,
                    $response->getHeaders(),
                    (string) $response->getBody()
                );
            }

            return $this->handleResponseWithDataType(
                '\Repull\Model\UpdateVrboListingLocation200Response',
                $request,
                $response,
            );
        } catch (ApiException $e) {
            switch ($e->getCode()) {
                case 200:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\UpdateVrboListingLocation200Response',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 401:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 403:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 404:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 409:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 422:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 500:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 502:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 503:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 504:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
            }
        
            throw $e;
        }
    }

    /**
     * Operation updateVrboListingLocationAsync
     *
     * Update the Vrbo map setting
     *
     * @param  string $id Repull listing id (numeric string) of a listing linked to Vrbo. (required)
     * @param  \Repull\Model\UpdateVrboListingLocationRequest $update_vrbo_listing_location_request (required)
     * @param  string|null $vrbo_listing_number The Vrbo listing number to work on, when the listing is linked to more than one Vrbo unit. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['updateVrboListingLocation'] to see the possible values for this operation
     *
     * @throws InvalidArgumentException
     * @return PromiseInterface
     */
    public function updateVrboListingLocationAsync(
        string $id,
        \Repull\Model\UpdateVrboListingLocationRequest $update_vrbo_listing_location_request,
        ?string $vrbo_listing_number = null,
        string $contentType = self::contentTypes['updateVrboListingLocation'][0]
    ): PromiseInterface
    {
        return $this->updateVrboListingLocationAsyncWithHttpInfo($id, $update_vrbo_listing_location_request, $vrbo_listing_number, $contentType)
            ->then(
                function ($response) {
                    return $response[0];
                }
            );
    }

    /**
     * Operation updateVrboListingLocationAsyncWithHttpInfo
     *
     * Update the Vrbo map setting
     *
     * @param  string $id Repull listing id (numeric string) of a listing linked to Vrbo. (required)
     * @param  \Repull\Model\UpdateVrboListingLocationRequest $update_vrbo_listing_location_request (required)
     * @param  string|null $vrbo_listing_number The Vrbo listing number to work on, when the listing is linked to more than one Vrbo unit. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['updateVrboListingLocation'] to see the possible values for this operation
     *
     * @throws InvalidArgumentException
     * @return PromiseInterface
     */
    public function updateVrboListingLocationAsyncWithHttpInfo(
        string $id,
        \Repull\Model\UpdateVrboListingLocationRequest $update_vrbo_listing_location_request,
        ?string $vrbo_listing_number = null,
        string $contentType = self::contentTypes['updateVrboListingLocation'][0]
    ): PromiseInterface
    {
        $returnType = '\Repull\Model\UpdateVrboListingLocation200Response';
        $request = $this->updateVrboListingLocationRequest($id, $update_vrbo_listing_location_request, $vrbo_listing_number, $contentType);

        return $this->client
            ->sendAsync($request, $this->createHttpClientOption())
            ->then(
                function ($response) use ($returnType) {
                    if (in_array($returnType, ['\SplFileObject', '\Psr\Http\Message\StreamInterface'])) {
                        $content = $response->getBody(); //stream goes to serializer
                    } else {
                        $content = (string) $response->getBody();
                        if ($returnType !== 'string') {
                            $content = json_decode($content);
                        }
                    }

                    return [
                        ObjectSerializer::deserialize($content, $returnType, []),
                        $response->getStatusCode(),
                        $response->getHeaders()
                    ];
                },
                function ($exception) {
                    $response = $exception->getResponse();
                    $statusCode = $response->getStatusCode();
                    throw new ApiException(
                        sprintf(
                            '[%d] Error connecting to the API (%s)',
                            $statusCode,
                            $exception->getRequest()->getUri()
                        ),
                        $statusCode,
                        $response->getHeaders(),
                        (string) $response->getBody()
                    );
                }
            );
    }

    /**
     * Create request for operation 'updateVrboListingLocation'
     *
     * @param  string $id Repull listing id (numeric string) of a listing linked to Vrbo. (required)
     * @param  \Repull\Model\UpdateVrboListingLocationRequest $update_vrbo_listing_location_request (required)
     * @param  string|null $vrbo_listing_number The Vrbo listing number to work on, when the listing is linked to more than one Vrbo unit. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['updateVrboListingLocation'] to see the possible values for this operation
     *
     * @throws InvalidArgumentException
     * @return \GuzzleHttp\Psr7\Request
     */
    public function updateVrboListingLocationRequest(
        string $id,
        \Repull\Model\UpdateVrboListingLocationRequest $update_vrbo_listing_location_request,
        ?string $vrbo_listing_number = null,
        string $contentType = self::contentTypes['updateVrboListingLocation'][0]
    ): Request
    {

        // verify the required parameter 'id' is set
        if ($id === null || (is_array($id) && count($id) === 0)) {
            throw new InvalidArgumentException(
                'Missing the required parameter $id when calling updateVrboListingLocation'
            );
        }

        // verify the required parameter 'update_vrbo_listing_location_request' is set
        if ($update_vrbo_listing_location_request === null || (is_array($update_vrbo_listing_location_request) && count($update_vrbo_listing_location_request) === 0)) {
            throw new InvalidArgumentException(
                'Missing the required parameter $update_vrbo_listing_location_request when calling updateVrboListingLocation'
            );
        }



        $resourcePath = '/v1/channels/vrbo/listings/{id}/location';
        $formParams = [];
        $queryParams = [];
        $headerParams = [];
        $httpBody = '';
        $multipart = false;

        // query params
        $queryParams = array_merge($queryParams, ObjectSerializer::toQueryValue(
            $vrbo_listing_number,
            'vrboListingNumber', // param base name
            'string', // openApiType
            'form', // style
            true, // explode
            false, // required
            null // openApiFormat
        ) ?? []);


        // path params
        if ($id !== null) {
            $resourcePath = str_replace(
                '{id}',
                ObjectSerializer::toPathValue($id),
                $resourcePath
            );
        }


        $headers = $this->headerSelector->selectHeaders(
            ['application/json', ],
            $contentType,
            $multipart
        );

        // for model (json/xml)
        if (isset($update_vrbo_listing_location_request)) {
            if (stripos($headers['Content-Type'], 'application/json') !== false) {
                # if Content-Type contains "application/json", json_encode the body
                $httpBody = \GuzzleHttp\Utils::jsonEncode(ObjectSerializer::sanitizeForSerialization($update_vrbo_listing_location_request));
            } else {
                $httpBody = $update_vrbo_listing_location_request;
            }
        } elseif (count($formParams) > 0) {
            if ($multipart) {
                $multipartContents = [];
                foreach ($formParams as $formParamName => $formParamValue) {
                    $formParamValueItems = is_array($formParamValue) ? $formParamValue : [$formParamValue];
                    foreach ($formParamValueItems as $formParamValueItem) {
                        $multipartContents[] = [
                            'name' => $formParamName,
                            'contents' => $formParamValueItem
                        ];
                    }
                }
                // for HTTP post (form)
                $httpBody = new MultipartStream($multipartContents);

            } elseif (stripos($headers['Content-Type'], 'application/json') !== false) {
                # if Content-Type contains "application/json", json_encode the form parameters
                $httpBody = \GuzzleHttp\Utils::jsonEncode($formParams);
            } else {
                // for HTTP post (form)
                $httpBody = ObjectSerializer::buildQuery($formParams);
            }
        }

        // this endpoint requires Bearer (API Key) authentication (access token)
        if (!empty($this->config->getAccessToken())) {
            $headers['Authorization'] = 'Bearer ' . $this->config->getAccessToken();
        }

        $defaultHeaders = [];
        if ($this->config->getUserAgent()) {
            $defaultHeaders['User-Agent'] = $this->config->getUserAgent();
        }

        $headers = array_merge(
            $defaultHeaders,
            $headerParams,
            $headers
        );

        $operationHost = $this->config->getHost();
        $query = ObjectSerializer::buildQuery($queryParams);
        return new Request(
            'PUT',
            $operationHost . $resourcePath . ($query ? "?{$query}" : ''),
            $headers,
            $httpBody
        );
    }

    /**
     * Operation updateVrboListingPaymentTerms
     *
     * Update the Vrbo payment terms
     *
     * @param  string $id Repull listing id (numeric string) of a listing linked to Vrbo. (required)
     * @param  \Repull\Model\UpdateVrboListingPaymentTermsRequest $update_vrbo_listing_payment_terms_request update_vrbo_listing_payment_terms_request (required)
     * @param  string|null $vrbo_listing_number The Vrbo listing number to work on, when the listing is linked to more than one Vrbo unit. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['updateVrboListingPaymentTerms'] to see the possible values for this operation
     *
     * @throws ApiException on non-2xx response or if the response body is not in the expected format
     * @throws InvalidArgumentException
     * @return \Repull\Model\UpdateVrboListingPaymentTerms200Response|\Repull\Model\Error
     */
    public function updateVrboListingPaymentTerms(
        string $id,
        \Repull\Model\UpdateVrboListingPaymentTermsRequest $update_vrbo_listing_payment_terms_request,
        ?string $vrbo_listing_number = null,
        string $contentType = self::contentTypes['updateVrboListingPaymentTerms'][0]
    ): \Repull\Model\UpdateVrboListingPaymentTerms200Response|\Repull\Model\Error
    {
        list($response) = $this->updateVrboListingPaymentTermsWithHttpInfo($id, $update_vrbo_listing_payment_terms_request, $vrbo_listing_number, $contentType);
        return $response;
    }

    /**
     * Operation updateVrboListingPaymentTermsWithHttpInfo
     *
     * Update the Vrbo payment terms
     *
     * @param  string $id Repull listing id (numeric string) of a listing linked to Vrbo. (required)
     * @param  \Repull\Model\UpdateVrboListingPaymentTermsRequest $update_vrbo_listing_payment_terms_request (required)
     * @param  string|null $vrbo_listing_number The Vrbo listing number to work on, when the listing is linked to more than one Vrbo unit. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['updateVrboListingPaymentTerms'] to see the possible values for this operation
     *
     * @throws ApiException on non-2xx response or if the response body is not in the expected format
     * @throws InvalidArgumentException
     * @return array of \Repull\Model\UpdateVrboListingPaymentTerms200Response|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error, HTTP status code, HTTP response headers (array of strings)
     */
    public function updateVrboListingPaymentTermsWithHttpInfo(
        string $id,
        \Repull\Model\UpdateVrboListingPaymentTermsRequest $update_vrbo_listing_payment_terms_request,
        ?string $vrbo_listing_number = null,
        string $contentType = self::contentTypes['updateVrboListingPaymentTerms'][0]
    ): array
    {
        $request = $this->updateVrboListingPaymentTermsRequest($id, $update_vrbo_listing_payment_terms_request, $vrbo_listing_number, $contentType);

        try {
            $options = $this->createHttpClientOption();
            try {
                $response = $this->client->send($request, $options);
            } catch (RequestException $e) {
                throw new ApiException(
                    "[{$e->getCode()}] {$e->getMessage()}",
                    (int) $e->getCode(),
                    $e->getResponse() ? $e->getResponse()->getHeaders() : null,
                    $e->getResponse() ? (string) $e->getResponse()->getBody() : null
                );
            } catch (ConnectException $e) {
                throw new ApiException(
                    "[{$e->getCode()}] {$e->getMessage()}",
                    (int) $e->getCode(),
                    null,
                    null
                );
            }

            $statusCode = $response->getStatusCode();

            switch($statusCode) {
                case 200:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\UpdateVrboListingPaymentTerms200Response',
                        $request,
                        $response,
                    );
                case 401:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 403:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 404:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 409:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 422:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 500:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 502:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 503:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 504:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
            }
            

            if ($statusCode < 200 || $statusCode > 299) {
                throw new ApiException(
                    sprintf(
                        '[%d] Error connecting to the API (%s)',
                        $statusCode,
                        (string) $request->getUri()
                    ),
                    $statusCode,
                    $response->getHeaders(),
                    (string) $response->getBody()
                );
            }

            return $this->handleResponseWithDataType(
                '\Repull\Model\UpdateVrboListingPaymentTerms200Response',
                $request,
                $response,
            );
        } catch (ApiException $e) {
            switch ($e->getCode()) {
                case 200:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\UpdateVrboListingPaymentTerms200Response',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 401:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 403:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 404:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 409:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 422:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 500:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 502:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 503:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 504:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
            }
        
            throw $e;
        }
    }

    /**
     * Operation updateVrboListingPaymentTermsAsync
     *
     * Update the Vrbo payment terms
     *
     * @param  string $id Repull listing id (numeric string) of a listing linked to Vrbo. (required)
     * @param  \Repull\Model\UpdateVrboListingPaymentTermsRequest $update_vrbo_listing_payment_terms_request (required)
     * @param  string|null $vrbo_listing_number The Vrbo listing number to work on, when the listing is linked to more than one Vrbo unit. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['updateVrboListingPaymentTerms'] to see the possible values for this operation
     *
     * @throws InvalidArgumentException
     * @return PromiseInterface
     */
    public function updateVrboListingPaymentTermsAsync(
        string $id,
        \Repull\Model\UpdateVrboListingPaymentTermsRequest $update_vrbo_listing_payment_terms_request,
        ?string $vrbo_listing_number = null,
        string $contentType = self::contentTypes['updateVrboListingPaymentTerms'][0]
    ): PromiseInterface
    {
        return $this->updateVrboListingPaymentTermsAsyncWithHttpInfo($id, $update_vrbo_listing_payment_terms_request, $vrbo_listing_number, $contentType)
            ->then(
                function ($response) {
                    return $response[0];
                }
            );
    }

    /**
     * Operation updateVrboListingPaymentTermsAsyncWithHttpInfo
     *
     * Update the Vrbo payment terms
     *
     * @param  string $id Repull listing id (numeric string) of a listing linked to Vrbo. (required)
     * @param  \Repull\Model\UpdateVrboListingPaymentTermsRequest $update_vrbo_listing_payment_terms_request (required)
     * @param  string|null $vrbo_listing_number The Vrbo listing number to work on, when the listing is linked to more than one Vrbo unit. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['updateVrboListingPaymentTerms'] to see the possible values for this operation
     *
     * @throws InvalidArgumentException
     * @return PromiseInterface
     */
    public function updateVrboListingPaymentTermsAsyncWithHttpInfo(
        string $id,
        \Repull\Model\UpdateVrboListingPaymentTermsRequest $update_vrbo_listing_payment_terms_request,
        ?string $vrbo_listing_number = null,
        string $contentType = self::contentTypes['updateVrboListingPaymentTerms'][0]
    ): PromiseInterface
    {
        $returnType = '\Repull\Model\UpdateVrboListingPaymentTerms200Response';
        $request = $this->updateVrboListingPaymentTermsRequest($id, $update_vrbo_listing_payment_terms_request, $vrbo_listing_number, $contentType);

        return $this->client
            ->sendAsync($request, $this->createHttpClientOption())
            ->then(
                function ($response) use ($returnType) {
                    if (in_array($returnType, ['\SplFileObject', '\Psr\Http\Message\StreamInterface'])) {
                        $content = $response->getBody(); //stream goes to serializer
                    } else {
                        $content = (string) $response->getBody();
                        if ($returnType !== 'string') {
                            $content = json_decode($content);
                        }
                    }

                    return [
                        ObjectSerializer::deserialize($content, $returnType, []),
                        $response->getStatusCode(),
                        $response->getHeaders()
                    ];
                },
                function ($exception) {
                    $response = $exception->getResponse();
                    $statusCode = $response->getStatusCode();
                    throw new ApiException(
                        sprintf(
                            '[%d] Error connecting to the API (%s)',
                            $statusCode,
                            $exception->getRequest()->getUri()
                        ),
                        $statusCode,
                        $response->getHeaders(),
                        (string) $response->getBody()
                    );
                }
            );
    }

    /**
     * Create request for operation 'updateVrboListingPaymentTerms'
     *
     * @param  string $id Repull listing id (numeric string) of a listing linked to Vrbo. (required)
     * @param  \Repull\Model\UpdateVrboListingPaymentTermsRequest $update_vrbo_listing_payment_terms_request (required)
     * @param  string|null $vrbo_listing_number The Vrbo listing number to work on, when the listing is linked to more than one Vrbo unit. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['updateVrboListingPaymentTerms'] to see the possible values for this operation
     *
     * @throws InvalidArgumentException
     * @return \GuzzleHttp\Psr7\Request
     */
    public function updateVrboListingPaymentTermsRequest(
        string $id,
        \Repull\Model\UpdateVrboListingPaymentTermsRequest $update_vrbo_listing_payment_terms_request,
        ?string $vrbo_listing_number = null,
        string $contentType = self::contentTypes['updateVrboListingPaymentTerms'][0]
    ): Request
    {

        // verify the required parameter 'id' is set
        if ($id === null || (is_array($id) && count($id) === 0)) {
            throw new InvalidArgumentException(
                'Missing the required parameter $id when calling updateVrboListingPaymentTerms'
            );
        }

        // verify the required parameter 'update_vrbo_listing_payment_terms_request' is set
        if ($update_vrbo_listing_payment_terms_request === null || (is_array($update_vrbo_listing_payment_terms_request) && count($update_vrbo_listing_payment_terms_request) === 0)) {
            throw new InvalidArgumentException(
                'Missing the required parameter $update_vrbo_listing_payment_terms_request when calling updateVrboListingPaymentTerms'
            );
        }



        $resourcePath = '/v1/channels/vrbo/listings/{id}/payment-terms';
        $formParams = [];
        $queryParams = [];
        $headerParams = [];
        $httpBody = '';
        $multipart = false;

        // query params
        $queryParams = array_merge($queryParams, ObjectSerializer::toQueryValue(
            $vrbo_listing_number,
            'vrboListingNumber', // param base name
            'string', // openApiType
            'form', // style
            true, // explode
            false, // required
            null // openApiFormat
        ) ?? []);


        // path params
        if ($id !== null) {
            $resourcePath = str_replace(
                '{id}',
                ObjectSerializer::toPathValue($id),
                $resourcePath
            );
        }


        $headers = $this->headerSelector->selectHeaders(
            ['application/json', ],
            $contentType,
            $multipart
        );

        // for model (json/xml)
        if (isset($update_vrbo_listing_payment_terms_request)) {
            if (stripos($headers['Content-Type'], 'application/json') !== false) {
                # if Content-Type contains "application/json", json_encode the body
                $httpBody = \GuzzleHttp\Utils::jsonEncode(ObjectSerializer::sanitizeForSerialization($update_vrbo_listing_payment_terms_request));
            } else {
                $httpBody = $update_vrbo_listing_payment_terms_request;
            }
        } elseif (count($formParams) > 0) {
            if ($multipart) {
                $multipartContents = [];
                foreach ($formParams as $formParamName => $formParamValue) {
                    $formParamValueItems = is_array($formParamValue) ? $formParamValue : [$formParamValue];
                    foreach ($formParamValueItems as $formParamValueItem) {
                        $multipartContents[] = [
                            'name' => $formParamName,
                            'contents' => $formParamValueItem
                        ];
                    }
                }
                // for HTTP post (form)
                $httpBody = new MultipartStream($multipartContents);

            } elseif (stripos($headers['Content-Type'], 'application/json') !== false) {
                # if Content-Type contains "application/json", json_encode the form parameters
                $httpBody = \GuzzleHttp\Utils::jsonEncode($formParams);
            } else {
                // for HTTP post (form)
                $httpBody = ObjectSerializer::buildQuery($formParams);
            }
        }

        // this endpoint requires Bearer (API Key) authentication (access token)
        if (!empty($this->config->getAccessToken())) {
            $headers['Authorization'] = 'Bearer ' . $this->config->getAccessToken();
        }

        $defaultHeaders = [];
        if ($this->config->getUserAgent()) {
            $defaultHeaders['User-Agent'] = $this->config->getUserAgent();
        }

        $headers = array_merge(
            $defaultHeaders,
            $headerParams,
            $headers
        );

        $operationHost = $this->config->getHost();
        $query = ObjectSerializer::buildQuery($queryParams);
        return new Request(
            'PUT',
            $operationHost . $resourcePath . ($query ? "?{$query}" : ''),
            $headers,
            $httpBody
        );
    }

    /**
     * Operation updateVrboListingPhotos
     *
     * Update the Vrbo photos
     *
     * @param  string $id Repull listing id (numeric string) of a listing linked to Vrbo. (required)
     * @param  \Repull\Model\UpdateVrboListingPhotosRequest $update_vrbo_listing_photos_request update_vrbo_listing_photos_request (required)
     * @param  string|null $vrbo_listing_number The Vrbo listing number to work on, when the listing is linked to more than one Vrbo unit. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['updateVrboListingPhotos'] to see the possible values for this operation
     *
     * @throws ApiException on non-2xx response or if the response body is not in the expected format
     * @throws InvalidArgumentException
     * @return \Repull\Model\UpdateVrboListingPhotos200Response|\Repull\Model\Error
     */
    public function updateVrboListingPhotos(
        string $id,
        \Repull\Model\UpdateVrboListingPhotosRequest $update_vrbo_listing_photos_request,
        ?string $vrbo_listing_number = null,
        string $contentType = self::contentTypes['updateVrboListingPhotos'][0]
    ): \Repull\Model\UpdateVrboListingPhotos200Response|\Repull\Model\Error
    {
        list($response) = $this->updateVrboListingPhotosWithHttpInfo($id, $update_vrbo_listing_photos_request, $vrbo_listing_number, $contentType);
        return $response;
    }

    /**
     * Operation updateVrboListingPhotosWithHttpInfo
     *
     * Update the Vrbo photos
     *
     * @param  string $id Repull listing id (numeric string) of a listing linked to Vrbo. (required)
     * @param  \Repull\Model\UpdateVrboListingPhotosRequest $update_vrbo_listing_photos_request (required)
     * @param  string|null $vrbo_listing_number The Vrbo listing number to work on, when the listing is linked to more than one Vrbo unit. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['updateVrboListingPhotos'] to see the possible values for this operation
     *
     * @throws ApiException on non-2xx response or if the response body is not in the expected format
     * @throws InvalidArgumentException
     * @return array of \Repull\Model\UpdateVrboListingPhotos200Response|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error, HTTP status code, HTTP response headers (array of strings)
     */
    public function updateVrboListingPhotosWithHttpInfo(
        string $id,
        \Repull\Model\UpdateVrboListingPhotosRequest $update_vrbo_listing_photos_request,
        ?string $vrbo_listing_number = null,
        string $contentType = self::contentTypes['updateVrboListingPhotos'][0]
    ): array
    {
        $request = $this->updateVrboListingPhotosRequest($id, $update_vrbo_listing_photos_request, $vrbo_listing_number, $contentType);

        try {
            $options = $this->createHttpClientOption();
            try {
                $response = $this->client->send($request, $options);
            } catch (RequestException $e) {
                throw new ApiException(
                    "[{$e->getCode()}] {$e->getMessage()}",
                    (int) $e->getCode(),
                    $e->getResponse() ? $e->getResponse()->getHeaders() : null,
                    $e->getResponse() ? (string) $e->getResponse()->getBody() : null
                );
            } catch (ConnectException $e) {
                throw new ApiException(
                    "[{$e->getCode()}] {$e->getMessage()}",
                    (int) $e->getCode(),
                    null,
                    null
                );
            }

            $statusCode = $response->getStatusCode();

            switch($statusCode) {
                case 200:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\UpdateVrboListingPhotos200Response',
                        $request,
                        $response,
                    );
                case 401:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 403:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 404:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 409:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 422:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 500:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 502:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 503:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 504:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
            }
            

            if ($statusCode < 200 || $statusCode > 299) {
                throw new ApiException(
                    sprintf(
                        '[%d] Error connecting to the API (%s)',
                        $statusCode,
                        (string) $request->getUri()
                    ),
                    $statusCode,
                    $response->getHeaders(),
                    (string) $response->getBody()
                );
            }

            return $this->handleResponseWithDataType(
                '\Repull\Model\UpdateVrboListingPhotos200Response',
                $request,
                $response,
            );
        } catch (ApiException $e) {
            switch ($e->getCode()) {
                case 200:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\UpdateVrboListingPhotos200Response',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 401:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 403:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 404:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 409:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 422:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 500:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 502:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 503:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 504:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
            }
        
            throw $e;
        }
    }

    /**
     * Operation updateVrboListingPhotosAsync
     *
     * Update the Vrbo photos
     *
     * @param  string $id Repull listing id (numeric string) of a listing linked to Vrbo. (required)
     * @param  \Repull\Model\UpdateVrboListingPhotosRequest $update_vrbo_listing_photos_request (required)
     * @param  string|null $vrbo_listing_number The Vrbo listing number to work on, when the listing is linked to more than one Vrbo unit. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['updateVrboListingPhotos'] to see the possible values for this operation
     *
     * @throws InvalidArgumentException
     * @return PromiseInterface
     */
    public function updateVrboListingPhotosAsync(
        string $id,
        \Repull\Model\UpdateVrboListingPhotosRequest $update_vrbo_listing_photos_request,
        ?string $vrbo_listing_number = null,
        string $contentType = self::contentTypes['updateVrboListingPhotos'][0]
    ): PromiseInterface
    {
        return $this->updateVrboListingPhotosAsyncWithHttpInfo($id, $update_vrbo_listing_photos_request, $vrbo_listing_number, $contentType)
            ->then(
                function ($response) {
                    return $response[0];
                }
            );
    }

    /**
     * Operation updateVrboListingPhotosAsyncWithHttpInfo
     *
     * Update the Vrbo photos
     *
     * @param  string $id Repull listing id (numeric string) of a listing linked to Vrbo. (required)
     * @param  \Repull\Model\UpdateVrboListingPhotosRequest $update_vrbo_listing_photos_request (required)
     * @param  string|null $vrbo_listing_number The Vrbo listing number to work on, when the listing is linked to more than one Vrbo unit. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['updateVrboListingPhotos'] to see the possible values for this operation
     *
     * @throws InvalidArgumentException
     * @return PromiseInterface
     */
    public function updateVrboListingPhotosAsyncWithHttpInfo(
        string $id,
        \Repull\Model\UpdateVrboListingPhotosRequest $update_vrbo_listing_photos_request,
        ?string $vrbo_listing_number = null,
        string $contentType = self::contentTypes['updateVrboListingPhotos'][0]
    ): PromiseInterface
    {
        $returnType = '\Repull\Model\UpdateVrboListingPhotos200Response';
        $request = $this->updateVrboListingPhotosRequest($id, $update_vrbo_listing_photos_request, $vrbo_listing_number, $contentType);

        return $this->client
            ->sendAsync($request, $this->createHttpClientOption())
            ->then(
                function ($response) use ($returnType) {
                    if (in_array($returnType, ['\SplFileObject', '\Psr\Http\Message\StreamInterface'])) {
                        $content = $response->getBody(); //stream goes to serializer
                    } else {
                        $content = (string) $response->getBody();
                        if ($returnType !== 'string') {
                            $content = json_decode($content);
                        }
                    }

                    return [
                        ObjectSerializer::deserialize($content, $returnType, []),
                        $response->getStatusCode(),
                        $response->getHeaders()
                    ];
                },
                function ($exception) {
                    $response = $exception->getResponse();
                    $statusCode = $response->getStatusCode();
                    throw new ApiException(
                        sprintf(
                            '[%d] Error connecting to the API (%s)',
                            $statusCode,
                            $exception->getRequest()->getUri()
                        ),
                        $statusCode,
                        $response->getHeaders(),
                        (string) $response->getBody()
                    );
                }
            );
    }

    /**
     * Create request for operation 'updateVrboListingPhotos'
     *
     * @param  string $id Repull listing id (numeric string) of a listing linked to Vrbo. (required)
     * @param  \Repull\Model\UpdateVrboListingPhotosRequest $update_vrbo_listing_photos_request (required)
     * @param  string|null $vrbo_listing_number The Vrbo listing number to work on, when the listing is linked to more than one Vrbo unit. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['updateVrboListingPhotos'] to see the possible values for this operation
     *
     * @throws InvalidArgumentException
     * @return \GuzzleHttp\Psr7\Request
     */
    public function updateVrboListingPhotosRequest(
        string $id,
        \Repull\Model\UpdateVrboListingPhotosRequest $update_vrbo_listing_photos_request,
        ?string $vrbo_listing_number = null,
        string $contentType = self::contentTypes['updateVrboListingPhotos'][0]
    ): Request
    {

        // verify the required parameter 'id' is set
        if ($id === null || (is_array($id) && count($id) === 0)) {
            throw new InvalidArgumentException(
                'Missing the required parameter $id when calling updateVrboListingPhotos'
            );
        }

        // verify the required parameter 'update_vrbo_listing_photos_request' is set
        if ($update_vrbo_listing_photos_request === null || (is_array($update_vrbo_listing_photos_request) && count($update_vrbo_listing_photos_request) === 0)) {
            throw new InvalidArgumentException(
                'Missing the required parameter $update_vrbo_listing_photos_request when calling updateVrboListingPhotos'
            );
        }



        $resourcePath = '/v1/channels/vrbo/listings/{id}/photos';
        $formParams = [];
        $queryParams = [];
        $headerParams = [];
        $httpBody = '';
        $multipart = false;

        // query params
        $queryParams = array_merge($queryParams, ObjectSerializer::toQueryValue(
            $vrbo_listing_number,
            'vrboListingNumber', // param base name
            'string', // openApiType
            'form', // style
            true, // explode
            false, // required
            null // openApiFormat
        ) ?? []);


        // path params
        if ($id !== null) {
            $resourcePath = str_replace(
                '{id}',
                ObjectSerializer::toPathValue($id),
                $resourcePath
            );
        }


        $headers = $this->headerSelector->selectHeaders(
            ['application/json', ],
            $contentType,
            $multipart
        );

        // for model (json/xml)
        if (isset($update_vrbo_listing_photos_request)) {
            if (stripos($headers['Content-Type'], 'application/json') !== false) {
                # if Content-Type contains "application/json", json_encode the body
                $httpBody = \GuzzleHttp\Utils::jsonEncode(ObjectSerializer::sanitizeForSerialization($update_vrbo_listing_photos_request));
            } else {
                $httpBody = $update_vrbo_listing_photos_request;
            }
        } elseif (count($formParams) > 0) {
            if ($multipart) {
                $multipartContents = [];
                foreach ($formParams as $formParamName => $formParamValue) {
                    $formParamValueItems = is_array($formParamValue) ? $formParamValue : [$formParamValue];
                    foreach ($formParamValueItems as $formParamValueItem) {
                        $multipartContents[] = [
                            'name' => $formParamName,
                            'contents' => $formParamValueItem
                        ];
                    }
                }
                // for HTTP post (form)
                $httpBody = new MultipartStream($multipartContents);

            } elseif (stripos($headers['Content-Type'], 'application/json') !== false) {
                # if Content-Type contains "application/json", json_encode the form parameters
                $httpBody = \GuzzleHttp\Utils::jsonEncode($formParams);
            } else {
                // for HTTP post (form)
                $httpBody = ObjectSerializer::buildQuery($formParams);
            }
        }

        // this endpoint requires Bearer (API Key) authentication (access token)
        if (!empty($this->config->getAccessToken())) {
            $headers['Authorization'] = 'Bearer ' . $this->config->getAccessToken();
        }

        $defaultHeaders = [];
        if ($this->config->getUserAgent()) {
            $defaultHeaders['User-Agent'] = $this->config->getUserAgent();
        }

        $headers = array_merge(
            $defaultHeaders,
            $headerParams,
            $headers
        );

        $operationHost = $this->config->getHost();
        $query = ObjectSerializer::buildQuery($queryParams);
        return new Request(
            'PUT',
            $operationHost . $resourcePath . ($query ? "?{$query}" : ''),
            $headers,
            $httpBody
        );
    }

    /**
     * Operation updateVrboListingPricing
     *
     * Update the Vrbo base pricing
     *
     * @param  string $id Repull listing id (numeric string) of a listing linked to Vrbo. (required)
     * @param  \Repull\Model\UpdateVrboListingPricingRequest $update_vrbo_listing_pricing_request update_vrbo_listing_pricing_request (required)
     * @param  string|null $vrbo_listing_number The Vrbo listing number to work on, when the listing is linked to more than one Vrbo unit. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['updateVrboListingPricing'] to see the possible values for this operation
     *
     * @throws ApiException on non-2xx response or if the response body is not in the expected format
     * @throws InvalidArgumentException
     * @return \Repull\Model\UpdateVrboListingPricing200Response|\Repull\Model\Error
     */
    public function updateVrboListingPricing(
        string $id,
        \Repull\Model\UpdateVrboListingPricingRequest $update_vrbo_listing_pricing_request,
        ?string $vrbo_listing_number = null,
        string $contentType = self::contentTypes['updateVrboListingPricing'][0]
    ): \Repull\Model\UpdateVrboListingPricing200Response|\Repull\Model\Error
    {
        list($response) = $this->updateVrboListingPricingWithHttpInfo($id, $update_vrbo_listing_pricing_request, $vrbo_listing_number, $contentType);
        return $response;
    }

    /**
     * Operation updateVrboListingPricingWithHttpInfo
     *
     * Update the Vrbo base pricing
     *
     * @param  string $id Repull listing id (numeric string) of a listing linked to Vrbo. (required)
     * @param  \Repull\Model\UpdateVrboListingPricingRequest $update_vrbo_listing_pricing_request (required)
     * @param  string|null $vrbo_listing_number The Vrbo listing number to work on, when the listing is linked to more than one Vrbo unit. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['updateVrboListingPricing'] to see the possible values for this operation
     *
     * @throws ApiException on non-2xx response or if the response body is not in the expected format
     * @throws InvalidArgumentException
     * @return array of \Repull\Model\UpdateVrboListingPricing200Response|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error, HTTP status code, HTTP response headers (array of strings)
     */
    public function updateVrboListingPricingWithHttpInfo(
        string $id,
        \Repull\Model\UpdateVrboListingPricingRequest $update_vrbo_listing_pricing_request,
        ?string $vrbo_listing_number = null,
        string $contentType = self::contentTypes['updateVrboListingPricing'][0]
    ): array
    {
        $request = $this->updateVrboListingPricingRequest($id, $update_vrbo_listing_pricing_request, $vrbo_listing_number, $contentType);

        try {
            $options = $this->createHttpClientOption();
            try {
                $response = $this->client->send($request, $options);
            } catch (RequestException $e) {
                throw new ApiException(
                    "[{$e->getCode()}] {$e->getMessage()}",
                    (int) $e->getCode(),
                    $e->getResponse() ? $e->getResponse()->getHeaders() : null,
                    $e->getResponse() ? (string) $e->getResponse()->getBody() : null
                );
            } catch (ConnectException $e) {
                throw new ApiException(
                    "[{$e->getCode()}] {$e->getMessage()}",
                    (int) $e->getCode(),
                    null,
                    null
                );
            }

            $statusCode = $response->getStatusCode();

            switch($statusCode) {
                case 200:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\UpdateVrboListingPricing200Response',
                        $request,
                        $response,
                    );
                case 401:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 403:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 404:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 409:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 422:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 500:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 502:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 503:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 504:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
            }
            

            if ($statusCode < 200 || $statusCode > 299) {
                throw new ApiException(
                    sprintf(
                        '[%d] Error connecting to the API (%s)',
                        $statusCode,
                        (string) $request->getUri()
                    ),
                    $statusCode,
                    $response->getHeaders(),
                    (string) $response->getBody()
                );
            }

            return $this->handleResponseWithDataType(
                '\Repull\Model\UpdateVrboListingPricing200Response',
                $request,
                $response,
            );
        } catch (ApiException $e) {
            switch ($e->getCode()) {
                case 200:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\UpdateVrboListingPricing200Response',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 401:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 403:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 404:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 409:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 422:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 500:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 502:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 503:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 504:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
            }
        
            throw $e;
        }
    }

    /**
     * Operation updateVrboListingPricingAsync
     *
     * Update the Vrbo base pricing
     *
     * @param  string $id Repull listing id (numeric string) of a listing linked to Vrbo. (required)
     * @param  \Repull\Model\UpdateVrboListingPricingRequest $update_vrbo_listing_pricing_request (required)
     * @param  string|null $vrbo_listing_number The Vrbo listing number to work on, when the listing is linked to more than one Vrbo unit. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['updateVrboListingPricing'] to see the possible values for this operation
     *
     * @throws InvalidArgumentException
     * @return PromiseInterface
     */
    public function updateVrboListingPricingAsync(
        string $id,
        \Repull\Model\UpdateVrboListingPricingRequest $update_vrbo_listing_pricing_request,
        ?string $vrbo_listing_number = null,
        string $contentType = self::contentTypes['updateVrboListingPricing'][0]
    ): PromiseInterface
    {
        return $this->updateVrboListingPricingAsyncWithHttpInfo($id, $update_vrbo_listing_pricing_request, $vrbo_listing_number, $contentType)
            ->then(
                function ($response) {
                    return $response[0];
                }
            );
    }

    /**
     * Operation updateVrboListingPricingAsyncWithHttpInfo
     *
     * Update the Vrbo base pricing
     *
     * @param  string $id Repull listing id (numeric string) of a listing linked to Vrbo. (required)
     * @param  \Repull\Model\UpdateVrboListingPricingRequest $update_vrbo_listing_pricing_request (required)
     * @param  string|null $vrbo_listing_number The Vrbo listing number to work on, when the listing is linked to more than one Vrbo unit. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['updateVrboListingPricing'] to see the possible values for this operation
     *
     * @throws InvalidArgumentException
     * @return PromiseInterface
     */
    public function updateVrboListingPricingAsyncWithHttpInfo(
        string $id,
        \Repull\Model\UpdateVrboListingPricingRequest $update_vrbo_listing_pricing_request,
        ?string $vrbo_listing_number = null,
        string $contentType = self::contentTypes['updateVrboListingPricing'][0]
    ): PromiseInterface
    {
        $returnType = '\Repull\Model\UpdateVrboListingPricing200Response';
        $request = $this->updateVrboListingPricingRequest($id, $update_vrbo_listing_pricing_request, $vrbo_listing_number, $contentType);

        return $this->client
            ->sendAsync($request, $this->createHttpClientOption())
            ->then(
                function ($response) use ($returnType) {
                    if (in_array($returnType, ['\SplFileObject', '\Psr\Http\Message\StreamInterface'])) {
                        $content = $response->getBody(); //stream goes to serializer
                    } else {
                        $content = (string) $response->getBody();
                        if ($returnType !== 'string') {
                            $content = json_decode($content);
                        }
                    }

                    return [
                        ObjectSerializer::deserialize($content, $returnType, []),
                        $response->getStatusCode(),
                        $response->getHeaders()
                    ];
                },
                function ($exception) {
                    $response = $exception->getResponse();
                    $statusCode = $response->getStatusCode();
                    throw new ApiException(
                        sprintf(
                            '[%d] Error connecting to the API (%s)',
                            $statusCode,
                            $exception->getRequest()->getUri()
                        ),
                        $statusCode,
                        $response->getHeaders(),
                        (string) $response->getBody()
                    );
                }
            );
    }

    /**
     * Create request for operation 'updateVrboListingPricing'
     *
     * @param  string $id Repull listing id (numeric string) of a listing linked to Vrbo. (required)
     * @param  \Repull\Model\UpdateVrboListingPricingRequest $update_vrbo_listing_pricing_request (required)
     * @param  string|null $vrbo_listing_number The Vrbo listing number to work on, when the listing is linked to more than one Vrbo unit. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['updateVrboListingPricing'] to see the possible values for this operation
     *
     * @throws InvalidArgumentException
     * @return \GuzzleHttp\Psr7\Request
     */
    public function updateVrboListingPricingRequest(
        string $id,
        \Repull\Model\UpdateVrboListingPricingRequest $update_vrbo_listing_pricing_request,
        ?string $vrbo_listing_number = null,
        string $contentType = self::contentTypes['updateVrboListingPricing'][0]
    ): Request
    {

        // verify the required parameter 'id' is set
        if ($id === null || (is_array($id) && count($id) === 0)) {
            throw new InvalidArgumentException(
                'Missing the required parameter $id when calling updateVrboListingPricing'
            );
        }

        // verify the required parameter 'update_vrbo_listing_pricing_request' is set
        if ($update_vrbo_listing_pricing_request === null || (is_array($update_vrbo_listing_pricing_request) && count($update_vrbo_listing_pricing_request) === 0)) {
            throw new InvalidArgumentException(
                'Missing the required parameter $update_vrbo_listing_pricing_request when calling updateVrboListingPricing'
            );
        }



        $resourcePath = '/v1/channels/vrbo/listings/{id}/pricing';
        $formParams = [];
        $queryParams = [];
        $headerParams = [];
        $httpBody = '';
        $multipart = false;

        // query params
        $queryParams = array_merge($queryParams, ObjectSerializer::toQueryValue(
            $vrbo_listing_number,
            'vrboListingNumber', // param base name
            'string', // openApiType
            'form', // style
            true, // explode
            false, // required
            null // openApiFormat
        ) ?? []);


        // path params
        if ($id !== null) {
            $resourcePath = str_replace(
                '{id}',
                ObjectSerializer::toPathValue($id),
                $resourcePath
            );
        }


        $headers = $this->headerSelector->selectHeaders(
            ['application/json', ],
            $contentType,
            $multipart
        );

        // for model (json/xml)
        if (isset($update_vrbo_listing_pricing_request)) {
            if (stripos($headers['Content-Type'], 'application/json') !== false) {
                # if Content-Type contains "application/json", json_encode the body
                $httpBody = \GuzzleHttp\Utils::jsonEncode(ObjectSerializer::sanitizeForSerialization($update_vrbo_listing_pricing_request));
            } else {
                $httpBody = $update_vrbo_listing_pricing_request;
            }
        } elseif (count($formParams) > 0) {
            if ($multipart) {
                $multipartContents = [];
                foreach ($formParams as $formParamName => $formParamValue) {
                    $formParamValueItems = is_array($formParamValue) ? $formParamValue : [$formParamValue];
                    foreach ($formParamValueItems as $formParamValueItem) {
                        $multipartContents[] = [
                            'name' => $formParamName,
                            'contents' => $formParamValueItem
                        ];
                    }
                }
                // for HTTP post (form)
                $httpBody = new MultipartStream($multipartContents);

            } elseif (stripos($headers['Content-Type'], 'application/json') !== false) {
                # if Content-Type contains "application/json", json_encode the form parameters
                $httpBody = \GuzzleHttp\Utils::jsonEncode($formParams);
            } else {
                // for HTTP post (form)
                $httpBody = ObjectSerializer::buildQuery($formParams);
            }
        }

        // this endpoint requires Bearer (API Key) authentication (access token)
        if (!empty($this->config->getAccessToken())) {
            $headers['Authorization'] = 'Bearer ' . $this->config->getAccessToken();
        }

        $defaultHeaders = [];
        if ($this->config->getUserAgent()) {
            $defaultHeaders['User-Agent'] = $this->config->getUserAgent();
        }

        $headers = array_merge(
            $defaultHeaders,
            $headerParams,
            $headers
        );

        $operationHost = $this->config->getHost();
        $query = ObjectSerializer::buildQuery($queryParams);
        return new Request(
            'PUT',
            $operationHost . $resourcePath . ($query ? "?{$query}" : ''),
            $headers,
            $httpBody
        );
    }

    /**
     * Operation updateVrboListingProperty
     *
     * Update the Vrbo property details
     *
     * @param  string $id Repull listing id (numeric string) of a listing linked to Vrbo. (required)
     * @param  \Repull\Model\UpdateVrboListingPropertyRequest $update_vrbo_listing_property_request update_vrbo_listing_property_request (required)
     * @param  string|null $vrbo_listing_number The Vrbo listing number to work on, when the listing is linked to more than one Vrbo unit. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['updateVrboListingProperty'] to see the possible values for this operation
     *
     * @throws ApiException on non-2xx response or if the response body is not in the expected format
     * @throws InvalidArgumentException
     * @return \Repull\Model\UpdateVrboListingProperty200Response|\Repull\Model\Error
     */
    public function updateVrboListingProperty(
        string $id,
        \Repull\Model\UpdateVrboListingPropertyRequest $update_vrbo_listing_property_request,
        ?string $vrbo_listing_number = null,
        string $contentType = self::contentTypes['updateVrboListingProperty'][0]
    ): \Repull\Model\UpdateVrboListingProperty200Response|\Repull\Model\Error
    {
        list($response) = $this->updateVrboListingPropertyWithHttpInfo($id, $update_vrbo_listing_property_request, $vrbo_listing_number, $contentType);
        return $response;
    }

    /**
     * Operation updateVrboListingPropertyWithHttpInfo
     *
     * Update the Vrbo property details
     *
     * @param  string $id Repull listing id (numeric string) of a listing linked to Vrbo. (required)
     * @param  \Repull\Model\UpdateVrboListingPropertyRequest $update_vrbo_listing_property_request (required)
     * @param  string|null $vrbo_listing_number The Vrbo listing number to work on, when the listing is linked to more than one Vrbo unit. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['updateVrboListingProperty'] to see the possible values for this operation
     *
     * @throws ApiException on non-2xx response or if the response body is not in the expected format
     * @throws InvalidArgumentException
     * @return array of \Repull\Model\UpdateVrboListingProperty200Response|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error, HTTP status code, HTTP response headers (array of strings)
     */
    public function updateVrboListingPropertyWithHttpInfo(
        string $id,
        \Repull\Model\UpdateVrboListingPropertyRequest $update_vrbo_listing_property_request,
        ?string $vrbo_listing_number = null,
        string $contentType = self::contentTypes['updateVrboListingProperty'][0]
    ): array
    {
        $request = $this->updateVrboListingPropertyRequest($id, $update_vrbo_listing_property_request, $vrbo_listing_number, $contentType);

        try {
            $options = $this->createHttpClientOption();
            try {
                $response = $this->client->send($request, $options);
            } catch (RequestException $e) {
                throw new ApiException(
                    "[{$e->getCode()}] {$e->getMessage()}",
                    (int) $e->getCode(),
                    $e->getResponse() ? $e->getResponse()->getHeaders() : null,
                    $e->getResponse() ? (string) $e->getResponse()->getBody() : null
                );
            } catch (ConnectException $e) {
                throw new ApiException(
                    "[{$e->getCode()}] {$e->getMessage()}",
                    (int) $e->getCode(),
                    null,
                    null
                );
            }

            $statusCode = $response->getStatusCode();

            switch($statusCode) {
                case 200:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\UpdateVrboListingProperty200Response',
                        $request,
                        $response,
                    );
                case 401:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 403:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 404:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 409:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 422:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 500:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 502:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 503:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 504:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
            }
            

            if ($statusCode < 200 || $statusCode > 299) {
                throw new ApiException(
                    sprintf(
                        '[%d] Error connecting to the API (%s)',
                        $statusCode,
                        (string) $request->getUri()
                    ),
                    $statusCode,
                    $response->getHeaders(),
                    (string) $response->getBody()
                );
            }

            return $this->handleResponseWithDataType(
                '\Repull\Model\UpdateVrboListingProperty200Response',
                $request,
                $response,
            );
        } catch (ApiException $e) {
            switch ($e->getCode()) {
                case 200:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\UpdateVrboListingProperty200Response',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 401:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 403:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 404:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 409:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 422:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 500:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 502:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 503:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 504:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
            }
        
            throw $e;
        }
    }

    /**
     * Operation updateVrboListingPropertyAsync
     *
     * Update the Vrbo property details
     *
     * @param  string $id Repull listing id (numeric string) of a listing linked to Vrbo. (required)
     * @param  \Repull\Model\UpdateVrboListingPropertyRequest $update_vrbo_listing_property_request (required)
     * @param  string|null $vrbo_listing_number The Vrbo listing number to work on, when the listing is linked to more than one Vrbo unit. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['updateVrboListingProperty'] to see the possible values for this operation
     *
     * @throws InvalidArgumentException
     * @return PromiseInterface
     */
    public function updateVrboListingPropertyAsync(
        string $id,
        \Repull\Model\UpdateVrboListingPropertyRequest $update_vrbo_listing_property_request,
        ?string $vrbo_listing_number = null,
        string $contentType = self::contentTypes['updateVrboListingProperty'][0]
    ): PromiseInterface
    {
        return $this->updateVrboListingPropertyAsyncWithHttpInfo($id, $update_vrbo_listing_property_request, $vrbo_listing_number, $contentType)
            ->then(
                function ($response) {
                    return $response[0];
                }
            );
    }

    /**
     * Operation updateVrboListingPropertyAsyncWithHttpInfo
     *
     * Update the Vrbo property details
     *
     * @param  string $id Repull listing id (numeric string) of a listing linked to Vrbo. (required)
     * @param  \Repull\Model\UpdateVrboListingPropertyRequest $update_vrbo_listing_property_request (required)
     * @param  string|null $vrbo_listing_number The Vrbo listing number to work on, when the listing is linked to more than one Vrbo unit. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['updateVrboListingProperty'] to see the possible values for this operation
     *
     * @throws InvalidArgumentException
     * @return PromiseInterface
     */
    public function updateVrboListingPropertyAsyncWithHttpInfo(
        string $id,
        \Repull\Model\UpdateVrboListingPropertyRequest $update_vrbo_listing_property_request,
        ?string $vrbo_listing_number = null,
        string $contentType = self::contentTypes['updateVrboListingProperty'][0]
    ): PromiseInterface
    {
        $returnType = '\Repull\Model\UpdateVrboListingProperty200Response';
        $request = $this->updateVrboListingPropertyRequest($id, $update_vrbo_listing_property_request, $vrbo_listing_number, $contentType);

        return $this->client
            ->sendAsync($request, $this->createHttpClientOption())
            ->then(
                function ($response) use ($returnType) {
                    if (in_array($returnType, ['\SplFileObject', '\Psr\Http\Message\StreamInterface'])) {
                        $content = $response->getBody(); //stream goes to serializer
                    } else {
                        $content = (string) $response->getBody();
                        if ($returnType !== 'string') {
                            $content = json_decode($content);
                        }
                    }

                    return [
                        ObjectSerializer::deserialize($content, $returnType, []),
                        $response->getStatusCode(),
                        $response->getHeaders()
                    ];
                },
                function ($exception) {
                    $response = $exception->getResponse();
                    $statusCode = $response->getStatusCode();
                    throw new ApiException(
                        sprintf(
                            '[%d] Error connecting to the API (%s)',
                            $statusCode,
                            $exception->getRequest()->getUri()
                        ),
                        $statusCode,
                        $response->getHeaders(),
                        (string) $response->getBody()
                    );
                }
            );
    }

    /**
     * Create request for operation 'updateVrboListingProperty'
     *
     * @param  string $id Repull listing id (numeric string) of a listing linked to Vrbo. (required)
     * @param  \Repull\Model\UpdateVrboListingPropertyRequest $update_vrbo_listing_property_request (required)
     * @param  string|null $vrbo_listing_number The Vrbo listing number to work on, when the listing is linked to more than one Vrbo unit. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['updateVrboListingProperty'] to see the possible values for this operation
     *
     * @throws InvalidArgumentException
     * @return \GuzzleHttp\Psr7\Request
     */
    public function updateVrboListingPropertyRequest(
        string $id,
        \Repull\Model\UpdateVrboListingPropertyRequest $update_vrbo_listing_property_request,
        ?string $vrbo_listing_number = null,
        string $contentType = self::contentTypes['updateVrboListingProperty'][0]
    ): Request
    {

        // verify the required parameter 'id' is set
        if ($id === null || (is_array($id) && count($id) === 0)) {
            throw new InvalidArgumentException(
                'Missing the required parameter $id when calling updateVrboListingProperty'
            );
        }

        // verify the required parameter 'update_vrbo_listing_property_request' is set
        if ($update_vrbo_listing_property_request === null || (is_array($update_vrbo_listing_property_request) && count($update_vrbo_listing_property_request) === 0)) {
            throw new InvalidArgumentException(
                'Missing the required parameter $update_vrbo_listing_property_request when calling updateVrboListingProperty'
            );
        }



        $resourcePath = '/v1/channels/vrbo/listings/{id}/property';
        $formParams = [];
        $queryParams = [];
        $headerParams = [];
        $httpBody = '';
        $multipart = false;

        // query params
        $queryParams = array_merge($queryParams, ObjectSerializer::toQueryValue(
            $vrbo_listing_number,
            'vrboListingNumber', // param base name
            'string', // openApiType
            'form', // style
            true, // explode
            false, // required
            null // openApiFormat
        ) ?? []);


        // path params
        if ($id !== null) {
            $resourcePath = str_replace(
                '{id}',
                ObjectSerializer::toPathValue($id),
                $resourcePath
            );
        }


        $headers = $this->headerSelector->selectHeaders(
            ['application/json', ],
            $contentType,
            $multipart
        );

        // for model (json/xml)
        if (isset($update_vrbo_listing_property_request)) {
            if (stripos($headers['Content-Type'], 'application/json') !== false) {
                # if Content-Type contains "application/json", json_encode the body
                $httpBody = \GuzzleHttp\Utils::jsonEncode(ObjectSerializer::sanitizeForSerialization($update_vrbo_listing_property_request));
            } else {
                $httpBody = $update_vrbo_listing_property_request;
            }
        } elseif (count($formParams) > 0) {
            if ($multipart) {
                $multipartContents = [];
                foreach ($formParams as $formParamName => $formParamValue) {
                    $formParamValueItems = is_array($formParamValue) ? $formParamValue : [$formParamValue];
                    foreach ($formParamValueItems as $formParamValueItem) {
                        $multipartContents[] = [
                            'name' => $formParamName,
                            'contents' => $formParamValueItem
                        ];
                    }
                }
                // for HTTP post (form)
                $httpBody = new MultipartStream($multipartContents);

            } elseif (stripos($headers['Content-Type'], 'application/json') !== false) {
                # if Content-Type contains "application/json", json_encode the form parameters
                $httpBody = \GuzzleHttp\Utils::jsonEncode($formParams);
            } else {
                // for HTTP post (form)
                $httpBody = ObjectSerializer::buildQuery($formParams);
            }
        }

        // this endpoint requires Bearer (API Key) authentication (access token)
        if (!empty($this->config->getAccessToken())) {
            $headers['Authorization'] = 'Bearer ' . $this->config->getAccessToken();
        }

        $defaultHeaders = [];
        if ($this->config->getUserAgent()) {
            $defaultHeaders['User-Agent'] = $this->config->getUserAgent();
        }

        $headers = array_merge(
            $defaultHeaders,
            $headerParams,
            $headers
        );

        $operationHost = $this->config->getHost();
        $query = ObjectSerializer::buildQuery($queryParams);
        return new Request(
            'PUT',
            $operationHost . $resourcePath . ($query ? "?{$query}" : ''),
            $headers,
            $httpBody
        );
    }

    /**
     * Operation updateVrboListingProtection
     *
     * Update the Vrbo damage protection
     *
     * @param  string $id Repull listing id (numeric string) of a listing linked to Vrbo. (required)
     * @param  \Repull\Model\UpdateVrboListingProtectionRequest $update_vrbo_listing_protection_request update_vrbo_listing_protection_request (required)
     * @param  string|null $vrbo_listing_number The Vrbo listing number to work on, when the listing is linked to more than one Vrbo unit. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['updateVrboListingProtection'] to see the possible values for this operation
     *
     * @throws ApiException on non-2xx response or if the response body is not in the expected format
     * @throws InvalidArgumentException
     * @return \Repull\Model\UpdateVrboListingProtection200Response|\Repull\Model\Error
     */
    public function updateVrboListingProtection(
        string $id,
        \Repull\Model\UpdateVrboListingProtectionRequest $update_vrbo_listing_protection_request,
        ?string $vrbo_listing_number = null,
        string $contentType = self::contentTypes['updateVrboListingProtection'][0]
    ): \Repull\Model\UpdateVrboListingProtection200Response|\Repull\Model\Error
    {
        list($response) = $this->updateVrboListingProtectionWithHttpInfo($id, $update_vrbo_listing_protection_request, $vrbo_listing_number, $contentType);
        return $response;
    }

    /**
     * Operation updateVrboListingProtectionWithHttpInfo
     *
     * Update the Vrbo damage protection
     *
     * @param  string $id Repull listing id (numeric string) of a listing linked to Vrbo. (required)
     * @param  \Repull\Model\UpdateVrboListingProtectionRequest $update_vrbo_listing_protection_request (required)
     * @param  string|null $vrbo_listing_number The Vrbo listing number to work on, when the listing is linked to more than one Vrbo unit. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['updateVrboListingProtection'] to see the possible values for this operation
     *
     * @throws ApiException on non-2xx response or if the response body is not in the expected format
     * @throws InvalidArgumentException
     * @return array of \Repull\Model\UpdateVrboListingProtection200Response|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error, HTTP status code, HTTP response headers (array of strings)
     */
    public function updateVrboListingProtectionWithHttpInfo(
        string $id,
        \Repull\Model\UpdateVrboListingProtectionRequest $update_vrbo_listing_protection_request,
        ?string $vrbo_listing_number = null,
        string $contentType = self::contentTypes['updateVrboListingProtection'][0]
    ): array
    {
        $request = $this->updateVrboListingProtectionRequest($id, $update_vrbo_listing_protection_request, $vrbo_listing_number, $contentType);

        try {
            $options = $this->createHttpClientOption();
            try {
                $response = $this->client->send($request, $options);
            } catch (RequestException $e) {
                throw new ApiException(
                    "[{$e->getCode()}] {$e->getMessage()}",
                    (int) $e->getCode(),
                    $e->getResponse() ? $e->getResponse()->getHeaders() : null,
                    $e->getResponse() ? (string) $e->getResponse()->getBody() : null
                );
            } catch (ConnectException $e) {
                throw new ApiException(
                    "[{$e->getCode()}] {$e->getMessage()}",
                    (int) $e->getCode(),
                    null,
                    null
                );
            }

            $statusCode = $response->getStatusCode();

            switch($statusCode) {
                case 200:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\UpdateVrboListingProtection200Response',
                        $request,
                        $response,
                    );
                case 401:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 403:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 404:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 409:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 422:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 500:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 502:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 503:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 504:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
            }
            

            if ($statusCode < 200 || $statusCode > 299) {
                throw new ApiException(
                    sprintf(
                        '[%d] Error connecting to the API (%s)',
                        $statusCode,
                        (string) $request->getUri()
                    ),
                    $statusCode,
                    $response->getHeaders(),
                    (string) $response->getBody()
                );
            }

            return $this->handleResponseWithDataType(
                '\Repull\Model\UpdateVrboListingProtection200Response',
                $request,
                $response,
            );
        } catch (ApiException $e) {
            switch ($e->getCode()) {
                case 200:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\UpdateVrboListingProtection200Response',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 401:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 403:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 404:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 409:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 422:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 500:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 502:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 503:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 504:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
            }
        
            throw $e;
        }
    }

    /**
     * Operation updateVrboListingProtectionAsync
     *
     * Update the Vrbo damage protection
     *
     * @param  string $id Repull listing id (numeric string) of a listing linked to Vrbo. (required)
     * @param  \Repull\Model\UpdateVrboListingProtectionRequest $update_vrbo_listing_protection_request (required)
     * @param  string|null $vrbo_listing_number The Vrbo listing number to work on, when the listing is linked to more than one Vrbo unit. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['updateVrboListingProtection'] to see the possible values for this operation
     *
     * @throws InvalidArgumentException
     * @return PromiseInterface
     */
    public function updateVrboListingProtectionAsync(
        string $id,
        \Repull\Model\UpdateVrboListingProtectionRequest $update_vrbo_listing_protection_request,
        ?string $vrbo_listing_number = null,
        string $contentType = self::contentTypes['updateVrboListingProtection'][0]
    ): PromiseInterface
    {
        return $this->updateVrboListingProtectionAsyncWithHttpInfo($id, $update_vrbo_listing_protection_request, $vrbo_listing_number, $contentType)
            ->then(
                function ($response) {
                    return $response[0];
                }
            );
    }

    /**
     * Operation updateVrboListingProtectionAsyncWithHttpInfo
     *
     * Update the Vrbo damage protection
     *
     * @param  string $id Repull listing id (numeric string) of a listing linked to Vrbo. (required)
     * @param  \Repull\Model\UpdateVrboListingProtectionRequest $update_vrbo_listing_protection_request (required)
     * @param  string|null $vrbo_listing_number The Vrbo listing number to work on, when the listing is linked to more than one Vrbo unit. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['updateVrboListingProtection'] to see the possible values for this operation
     *
     * @throws InvalidArgumentException
     * @return PromiseInterface
     */
    public function updateVrboListingProtectionAsyncWithHttpInfo(
        string $id,
        \Repull\Model\UpdateVrboListingProtectionRequest $update_vrbo_listing_protection_request,
        ?string $vrbo_listing_number = null,
        string $contentType = self::contentTypes['updateVrboListingProtection'][0]
    ): PromiseInterface
    {
        $returnType = '\Repull\Model\UpdateVrboListingProtection200Response';
        $request = $this->updateVrboListingProtectionRequest($id, $update_vrbo_listing_protection_request, $vrbo_listing_number, $contentType);

        return $this->client
            ->sendAsync($request, $this->createHttpClientOption())
            ->then(
                function ($response) use ($returnType) {
                    if (in_array($returnType, ['\SplFileObject', '\Psr\Http\Message\StreamInterface'])) {
                        $content = $response->getBody(); //stream goes to serializer
                    } else {
                        $content = (string) $response->getBody();
                        if ($returnType !== 'string') {
                            $content = json_decode($content);
                        }
                    }

                    return [
                        ObjectSerializer::deserialize($content, $returnType, []),
                        $response->getStatusCode(),
                        $response->getHeaders()
                    ];
                },
                function ($exception) {
                    $response = $exception->getResponse();
                    $statusCode = $response->getStatusCode();
                    throw new ApiException(
                        sprintf(
                            '[%d] Error connecting to the API (%s)',
                            $statusCode,
                            $exception->getRequest()->getUri()
                        ),
                        $statusCode,
                        $response->getHeaders(),
                        (string) $response->getBody()
                    );
                }
            );
    }

    /**
     * Create request for operation 'updateVrboListingProtection'
     *
     * @param  string $id Repull listing id (numeric string) of a listing linked to Vrbo. (required)
     * @param  \Repull\Model\UpdateVrboListingProtectionRequest $update_vrbo_listing_protection_request (required)
     * @param  string|null $vrbo_listing_number The Vrbo listing number to work on, when the listing is linked to more than one Vrbo unit. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['updateVrboListingProtection'] to see the possible values for this operation
     *
     * @throws InvalidArgumentException
     * @return \GuzzleHttp\Psr7\Request
     */
    public function updateVrboListingProtectionRequest(
        string $id,
        \Repull\Model\UpdateVrboListingProtectionRequest $update_vrbo_listing_protection_request,
        ?string $vrbo_listing_number = null,
        string $contentType = self::contentTypes['updateVrboListingProtection'][0]
    ): Request
    {

        // verify the required parameter 'id' is set
        if ($id === null || (is_array($id) && count($id) === 0)) {
            throw new InvalidArgumentException(
                'Missing the required parameter $id when calling updateVrboListingProtection'
            );
        }

        // verify the required parameter 'update_vrbo_listing_protection_request' is set
        if ($update_vrbo_listing_protection_request === null || (is_array($update_vrbo_listing_protection_request) && count($update_vrbo_listing_protection_request) === 0)) {
            throw new InvalidArgumentException(
                'Missing the required parameter $update_vrbo_listing_protection_request when calling updateVrboListingProtection'
            );
        }



        $resourcePath = '/v1/channels/vrbo/listings/{id}/protection';
        $formParams = [];
        $queryParams = [];
        $headerParams = [];
        $httpBody = '';
        $multipart = false;

        // query params
        $queryParams = array_merge($queryParams, ObjectSerializer::toQueryValue(
            $vrbo_listing_number,
            'vrboListingNumber', // param base name
            'string', // openApiType
            'form', // style
            true, // explode
            false, // required
            null // openApiFormat
        ) ?? []);


        // path params
        if ($id !== null) {
            $resourcePath = str_replace(
                '{id}',
                ObjectSerializer::toPathValue($id),
                $resourcePath
            );
        }


        $headers = $this->headerSelector->selectHeaders(
            ['application/json', ],
            $contentType,
            $multipart
        );

        // for model (json/xml)
        if (isset($update_vrbo_listing_protection_request)) {
            if (stripos($headers['Content-Type'], 'application/json') !== false) {
                # if Content-Type contains "application/json", json_encode the body
                $httpBody = \GuzzleHttp\Utils::jsonEncode(ObjectSerializer::sanitizeForSerialization($update_vrbo_listing_protection_request));
            } else {
                $httpBody = $update_vrbo_listing_protection_request;
            }
        } elseif (count($formParams) > 0) {
            if ($multipart) {
                $multipartContents = [];
                foreach ($formParams as $formParamName => $formParamValue) {
                    $formParamValueItems = is_array($formParamValue) ? $formParamValue : [$formParamValue];
                    foreach ($formParamValueItems as $formParamValueItem) {
                        $multipartContents[] = [
                            'name' => $formParamName,
                            'contents' => $formParamValueItem
                        ];
                    }
                }
                // for HTTP post (form)
                $httpBody = new MultipartStream($multipartContents);

            } elseif (stripos($headers['Content-Type'], 'application/json') !== false) {
                # if Content-Type contains "application/json", json_encode the form parameters
                $httpBody = \GuzzleHttp\Utils::jsonEncode($formParams);
            } else {
                // for HTTP post (form)
                $httpBody = ObjectSerializer::buildQuery($formParams);
            }
        }

        // this endpoint requires Bearer (API Key) authentication (access token)
        if (!empty($this->config->getAccessToken())) {
            $headers['Authorization'] = 'Bearer ' . $this->config->getAccessToken();
        }

        $defaultHeaders = [];
        if ($this->config->getUserAgent()) {
            $defaultHeaders['User-Agent'] = $this->config->getUserAgent();
        }

        $headers = array_merge(
            $defaultHeaders,
            $headerParams,
            $headers
        );

        $operationHost = $this->config->getHost();
        $query = ObjectSerializer::buildQuery($queryParams);
        return new Request(
            'PUT',
            $operationHost . $resourcePath . ($query ? "?{$query}" : ''),
            $headers,
            $httpBody
        );
    }

    /**
     * Operation updateVrboListingRates
     *
     * Update the Vrbo rates and stay discounts
     *
     * @param  string $id Repull listing id (numeric string) of a listing linked to Vrbo. (required)
     * @param  \Repull\Model\UpdateVrboListingRatesRequest $update_vrbo_listing_rates_request update_vrbo_listing_rates_request (required)
     * @param  string|null $vrbo_listing_number The Vrbo listing number to work on, when the listing is linked to more than one Vrbo unit. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['updateVrboListingRates'] to see the possible values for this operation
     *
     * @throws ApiException on non-2xx response or if the response body is not in the expected format
     * @throws InvalidArgumentException
     * @return \Repull\Model\UpdateVrboListingRates200Response|\Repull\Model\Error
     */
    public function updateVrboListingRates(
        string $id,
        \Repull\Model\UpdateVrboListingRatesRequest $update_vrbo_listing_rates_request,
        ?string $vrbo_listing_number = null,
        string $contentType = self::contentTypes['updateVrboListingRates'][0]
    ): \Repull\Model\UpdateVrboListingRates200Response|\Repull\Model\Error
    {
        list($response) = $this->updateVrboListingRatesWithHttpInfo($id, $update_vrbo_listing_rates_request, $vrbo_listing_number, $contentType);
        return $response;
    }

    /**
     * Operation updateVrboListingRatesWithHttpInfo
     *
     * Update the Vrbo rates and stay discounts
     *
     * @param  string $id Repull listing id (numeric string) of a listing linked to Vrbo. (required)
     * @param  \Repull\Model\UpdateVrboListingRatesRequest $update_vrbo_listing_rates_request (required)
     * @param  string|null $vrbo_listing_number The Vrbo listing number to work on, when the listing is linked to more than one Vrbo unit. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['updateVrboListingRates'] to see the possible values for this operation
     *
     * @throws ApiException on non-2xx response or if the response body is not in the expected format
     * @throws InvalidArgumentException
     * @return array of \Repull\Model\UpdateVrboListingRates200Response|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error, HTTP status code, HTTP response headers (array of strings)
     */
    public function updateVrboListingRatesWithHttpInfo(
        string $id,
        \Repull\Model\UpdateVrboListingRatesRequest $update_vrbo_listing_rates_request,
        ?string $vrbo_listing_number = null,
        string $contentType = self::contentTypes['updateVrboListingRates'][0]
    ): array
    {
        $request = $this->updateVrboListingRatesRequest($id, $update_vrbo_listing_rates_request, $vrbo_listing_number, $contentType);

        try {
            $options = $this->createHttpClientOption();
            try {
                $response = $this->client->send($request, $options);
            } catch (RequestException $e) {
                throw new ApiException(
                    "[{$e->getCode()}] {$e->getMessage()}",
                    (int) $e->getCode(),
                    $e->getResponse() ? $e->getResponse()->getHeaders() : null,
                    $e->getResponse() ? (string) $e->getResponse()->getBody() : null
                );
            } catch (ConnectException $e) {
                throw new ApiException(
                    "[{$e->getCode()}] {$e->getMessage()}",
                    (int) $e->getCode(),
                    null,
                    null
                );
            }

            $statusCode = $response->getStatusCode();

            switch($statusCode) {
                case 200:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\UpdateVrboListingRates200Response',
                        $request,
                        $response,
                    );
                case 401:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 403:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 404:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 409:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 422:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 500:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 502:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 503:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 504:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
            }
            

            if ($statusCode < 200 || $statusCode > 299) {
                throw new ApiException(
                    sprintf(
                        '[%d] Error connecting to the API (%s)',
                        $statusCode,
                        (string) $request->getUri()
                    ),
                    $statusCode,
                    $response->getHeaders(),
                    (string) $response->getBody()
                );
            }

            return $this->handleResponseWithDataType(
                '\Repull\Model\UpdateVrboListingRates200Response',
                $request,
                $response,
            );
        } catch (ApiException $e) {
            switch ($e->getCode()) {
                case 200:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\UpdateVrboListingRates200Response',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 401:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 403:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 404:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 409:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 422:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 500:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 502:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 503:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 504:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
            }
        
            throw $e;
        }
    }

    /**
     * Operation updateVrboListingRatesAsync
     *
     * Update the Vrbo rates and stay discounts
     *
     * @param  string $id Repull listing id (numeric string) of a listing linked to Vrbo. (required)
     * @param  \Repull\Model\UpdateVrboListingRatesRequest $update_vrbo_listing_rates_request (required)
     * @param  string|null $vrbo_listing_number The Vrbo listing number to work on, when the listing is linked to more than one Vrbo unit. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['updateVrboListingRates'] to see the possible values for this operation
     *
     * @throws InvalidArgumentException
     * @return PromiseInterface
     */
    public function updateVrboListingRatesAsync(
        string $id,
        \Repull\Model\UpdateVrboListingRatesRequest $update_vrbo_listing_rates_request,
        ?string $vrbo_listing_number = null,
        string $contentType = self::contentTypes['updateVrboListingRates'][0]
    ): PromiseInterface
    {
        return $this->updateVrboListingRatesAsyncWithHttpInfo($id, $update_vrbo_listing_rates_request, $vrbo_listing_number, $contentType)
            ->then(
                function ($response) {
                    return $response[0];
                }
            );
    }

    /**
     * Operation updateVrboListingRatesAsyncWithHttpInfo
     *
     * Update the Vrbo rates and stay discounts
     *
     * @param  string $id Repull listing id (numeric string) of a listing linked to Vrbo. (required)
     * @param  \Repull\Model\UpdateVrboListingRatesRequest $update_vrbo_listing_rates_request (required)
     * @param  string|null $vrbo_listing_number The Vrbo listing number to work on, when the listing is linked to more than one Vrbo unit. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['updateVrboListingRates'] to see the possible values for this operation
     *
     * @throws InvalidArgumentException
     * @return PromiseInterface
     */
    public function updateVrboListingRatesAsyncWithHttpInfo(
        string $id,
        \Repull\Model\UpdateVrboListingRatesRequest $update_vrbo_listing_rates_request,
        ?string $vrbo_listing_number = null,
        string $contentType = self::contentTypes['updateVrboListingRates'][0]
    ): PromiseInterface
    {
        $returnType = '\Repull\Model\UpdateVrboListingRates200Response';
        $request = $this->updateVrboListingRatesRequest($id, $update_vrbo_listing_rates_request, $vrbo_listing_number, $contentType);

        return $this->client
            ->sendAsync($request, $this->createHttpClientOption())
            ->then(
                function ($response) use ($returnType) {
                    if (in_array($returnType, ['\SplFileObject', '\Psr\Http\Message\StreamInterface'])) {
                        $content = $response->getBody(); //stream goes to serializer
                    } else {
                        $content = (string) $response->getBody();
                        if ($returnType !== 'string') {
                            $content = json_decode($content);
                        }
                    }

                    return [
                        ObjectSerializer::deserialize($content, $returnType, []),
                        $response->getStatusCode(),
                        $response->getHeaders()
                    ];
                },
                function ($exception) {
                    $response = $exception->getResponse();
                    $statusCode = $response->getStatusCode();
                    throw new ApiException(
                        sprintf(
                            '[%d] Error connecting to the API (%s)',
                            $statusCode,
                            $exception->getRequest()->getUri()
                        ),
                        $statusCode,
                        $response->getHeaders(),
                        (string) $response->getBody()
                    );
                }
            );
    }

    /**
     * Create request for operation 'updateVrboListingRates'
     *
     * @param  string $id Repull listing id (numeric string) of a listing linked to Vrbo. (required)
     * @param  \Repull\Model\UpdateVrboListingRatesRequest $update_vrbo_listing_rates_request (required)
     * @param  string|null $vrbo_listing_number The Vrbo listing number to work on, when the listing is linked to more than one Vrbo unit. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['updateVrboListingRates'] to see the possible values for this operation
     *
     * @throws InvalidArgumentException
     * @return \GuzzleHttp\Psr7\Request
     */
    public function updateVrboListingRatesRequest(
        string $id,
        \Repull\Model\UpdateVrboListingRatesRequest $update_vrbo_listing_rates_request,
        ?string $vrbo_listing_number = null,
        string $contentType = self::contentTypes['updateVrboListingRates'][0]
    ): Request
    {

        // verify the required parameter 'id' is set
        if ($id === null || (is_array($id) && count($id) === 0)) {
            throw new InvalidArgumentException(
                'Missing the required parameter $id when calling updateVrboListingRates'
            );
        }

        // verify the required parameter 'update_vrbo_listing_rates_request' is set
        if ($update_vrbo_listing_rates_request === null || (is_array($update_vrbo_listing_rates_request) && count($update_vrbo_listing_rates_request) === 0)) {
            throw new InvalidArgumentException(
                'Missing the required parameter $update_vrbo_listing_rates_request when calling updateVrboListingRates'
            );
        }



        $resourcePath = '/v1/channels/vrbo/listings/{id}/rates';
        $formParams = [];
        $queryParams = [];
        $headerParams = [];
        $httpBody = '';
        $multipart = false;

        // query params
        $queryParams = array_merge($queryParams, ObjectSerializer::toQueryValue(
            $vrbo_listing_number,
            'vrboListingNumber', // param base name
            'string', // openApiType
            'form', // style
            true, // explode
            false, // required
            null // openApiFormat
        ) ?? []);


        // path params
        if ($id !== null) {
            $resourcePath = str_replace(
                '{id}',
                ObjectSerializer::toPathValue($id),
                $resourcePath
            );
        }


        $headers = $this->headerSelector->selectHeaders(
            ['application/json', ],
            $contentType,
            $multipart
        );

        // for model (json/xml)
        if (isset($update_vrbo_listing_rates_request)) {
            if (stripos($headers['Content-Type'], 'application/json') !== false) {
                # if Content-Type contains "application/json", json_encode the body
                $httpBody = \GuzzleHttp\Utils::jsonEncode(ObjectSerializer::sanitizeForSerialization($update_vrbo_listing_rates_request));
            } else {
                $httpBody = $update_vrbo_listing_rates_request;
            }
        } elseif (count($formParams) > 0) {
            if ($multipart) {
                $multipartContents = [];
                foreach ($formParams as $formParamName => $formParamValue) {
                    $formParamValueItems = is_array($formParamValue) ? $formParamValue : [$formParamValue];
                    foreach ($formParamValueItems as $formParamValueItem) {
                        $multipartContents[] = [
                            'name' => $formParamName,
                            'contents' => $formParamValueItem
                        ];
                    }
                }
                // for HTTP post (form)
                $httpBody = new MultipartStream($multipartContents);

            } elseif (stripos($headers['Content-Type'], 'application/json') !== false) {
                # if Content-Type contains "application/json", json_encode the form parameters
                $httpBody = \GuzzleHttp\Utils::jsonEncode($formParams);
            } else {
                // for HTTP post (form)
                $httpBody = ObjectSerializer::buildQuery($formParams);
            }
        }

        // this endpoint requires Bearer (API Key) authentication (access token)
        if (!empty($this->config->getAccessToken())) {
            $headers['Authorization'] = 'Bearer ' . $this->config->getAccessToken();
        }

        $defaultHeaders = [];
        if ($this->config->getUserAgent()) {
            $defaultHeaders['User-Agent'] = $this->config->getUserAgent();
        }

        $headers = array_merge(
            $defaultHeaders,
            $headerParams,
            $headers
        );

        $operationHost = $this->config->getHost();
        $query = ObjectSerializer::buildQuery($queryParams);
        return new Request(
            'PUT',
            $operationHost . $resourcePath . ($query ? "?{$query}" : ''),
            $headers,
            $httpBody
        );
    }

    /**
     * Operation updateVrboListingRegistration
     *
     * Update the Vrbo registration / licence
     *
     * @param  string $id Repull listing id (numeric string) of a listing linked to Vrbo. (required)
     * @param  \Repull\Model\UpdateVrboListingRegistrationRequest $update_vrbo_listing_registration_request update_vrbo_listing_registration_request (required)
     * @param  string|null $vrbo_listing_number The Vrbo listing number to work on, when the listing is linked to more than one Vrbo unit. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['updateVrboListingRegistration'] to see the possible values for this operation
     *
     * @throws ApiException on non-2xx response or if the response body is not in the expected format
     * @throws InvalidArgumentException
     * @return \Repull\Model\UpdateVrboListingRegistration200Response|\Repull\Model\Error
     */
    public function updateVrboListingRegistration(
        string $id,
        \Repull\Model\UpdateVrboListingRegistrationRequest $update_vrbo_listing_registration_request,
        ?string $vrbo_listing_number = null,
        string $contentType = self::contentTypes['updateVrboListingRegistration'][0]
    ): \Repull\Model\UpdateVrboListingRegistration200Response|\Repull\Model\Error
    {
        list($response) = $this->updateVrboListingRegistrationWithHttpInfo($id, $update_vrbo_listing_registration_request, $vrbo_listing_number, $contentType);
        return $response;
    }

    /**
     * Operation updateVrboListingRegistrationWithHttpInfo
     *
     * Update the Vrbo registration / licence
     *
     * @param  string $id Repull listing id (numeric string) of a listing linked to Vrbo. (required)
     * @param  \Repull\Model\UpdateVrboListingRegistrationRequest $update_vrbo_listing_registration_request (required)
     * @param  string|null $vrbo_listing_number The Vrbo listing number to work on, when the listing is linked to more than one Vrbo unit. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['updateVrboListingRegistration'] to see the possible values for this operation
     *
     * @throws ApiException on non-2xx response or if the response body is not in the expected format
     * @throws InvalidArgumentException
     * @return array of \Repull\Model\UpdateVrboListingRegistration200Response|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error, HTTP status code, HTTP response headers (array of strings)
     */
    public function updateVrboListingRegistrationWithHttpInfo(
        string $id,
        \Repull\Model\UpdateVrboListingRegistrationRequest $update_vrbo_listing_registration_request,
        ?string $vrbo_listing_number = null,
        string $contentType = self::contentTypes['updateVrboListingRegistration'][0]
    ): array
    {
        $request = $this->updateVrboListingRegistrationRequest($id, $update_vrbo_listing_registration_request, $vrbo_listing_number, $contentType);

        try {
            $options = $this->createHttpClientOption();
            try {
                $response = $this->client->send($request, $options);
            } catch (RequestException $e) {
                throw new ApiException(
                    "[{$e->getCode()}] {$e->getMessage()}",
                    (int) $e->getCode(),
                    $e->getResponse() ? $e->getResponse()->getHeaders() : null,
                    $e->getResponse() ? (string) $e->getResponse()->getBody() : null
                );
            } catch (ConnectException $e) {
                throw new ApiException(
                    "[{$e->getCode()}] {$e->getMessage()}",
                    (int) $e->getCode(),
                    null,
                    null
                );
            }

            $statusCode = $response->getStatusCode();

            switch($statusCode) {
                case 200:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\UpdateVrboListingRegistration200Response',
                        $request,
                        $response,
                    );
                case 401:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 403:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 404:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 409:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 422:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 500:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 502:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 503:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 504:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
            }
            

            if ($statusCode < 200 || $statusCode > 299) {
                throw new ApiException(
                    sprintf(
                        '[%d] Error connecting to the API (%s)',
                        $statusCode,
                        (string) $request->getUri()
                    ),
                    $statusCode,
                    $response->getHeaders(),
                    (string) $response->getBody()
                );
            }

            return $this->handleResponseWithDataType(
                '\Repull\Model\UpdateVrboListingRegistration200Response',
                $request,
                $response,
            );
        } catch (ApiException $e) {
            switch ($e->getCode()) {
                case 200:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\UpdateVrboListingRegistration200Response',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 401:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 403:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 404:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 409:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 422:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 500:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 502:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 503:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 504:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
            }
        
            throw $e;
        }
    }

    /**
     * Operation updateVrboListingRegistrationAsync
     *
     * Update the Vrbo registration / licence
     *
     * @param  string $id Repull listing id (numeric string) of a listing linked to Vrbo. (required)
     * @param  \Repull\Model\UpdateVrboListingRegistrationRequest $update_vrbo_listing_registration_request (required)
     * @param  string|null $vrbo_listing_number The Vrbo listing number to work on, when the listing is linked to more than one Vrbo unit. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['updateVrboListingRegistration'] to see the possible values for this operation
     *
     * @throws InvalidArgumentException
     * @return PromiseInterface
     */
    public function updateVrboListingRegistrationAsync(
        string $id,
        \Repull\Model\UpdateVrboListingRegistrationRequest $update_vrbo_listing_registration_request,
        ?string $vrbo_listing_number = null,
        string $contentType = self::contentTypes['updateVrboListingRegistration'][0]
    ): PromiseInterface
    {
        return $this->updateVrboListingRegistrationAsyncWithHttpInfo($id, $update_vrbo_listing_registration_request, $vrbo_listing_number, $contentType)
            ->then(
                function ($response) {
                    return $response[0];
                }
            );
    }

    /**
     * Operation updateVrboListingRegistrationAsyncWithHttpInfo
     *
     * Update the Vrbo registration / licence
     *
     * @param  string $id Repull listing id (numeric string) of a listing linked to Vrbo. (required)
     * @param  \Repull\Model\UpdateVrboListingRegistrationRequest $update_vrbo_listing_registration_request (required)
     * @param  string|null $vrbo_listing_number The Vrbo listing number to work on, when the listing is linked to more than one Vrbo unit. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['updateVrboListingRegistration'] to see the possible values for this operation
     *
     * @throws InvalidArgumentException
     * @return PromiseInterface
     */
    public function updateVrboListingRegistrationAsyncWithHttpInfo(
        string $id,
        \Repull\Model\UpdateVrboListingRegistrationRequest $update_vrbo_listing_registration_request,
        ?string $vrbo_listing_number = null,
        string $contentType = self::contentTypes['updateVrboListingRegistration'][0]
    ): PromiseInterface
    {
        $returnType = '\Repull\Model\UpdateVrboListingRegistration200Response';
        $request = $this->updateVrboListingRegistrationRequest($id, $update_vrbo_listing_registration_request, $vrbo_listing_number, $contentType);

        return $this->client
            ->sendAsync($request, $this->createHttpClientOption())
            ->then(
                function ($response) use ($returnType) {
                    if (in_array($returnType, ['\SplFileObject', '\Psr\Http\Message\StreamInterface'])) {
                        $content = $response->getBody(); //stream goes to serializer
                    } else {
                        $content = (string) $response->getBody();
                        if ($returnType !== 'string') {
                            $content = json_decode($content);
                        }
                    }

                    return [
                        ObjectSerializer::deserialize($content, $returnType, []),
                        $response->getStatusCode(),
                        $response->getHeaders()
                    ];
                },
                function ($exception) {
                    $response = $exception->getResponse();
                    $statusCode = $response->getStatusCode();
                    throw new ApiException(
                        sprintf(
                            '[%d] Error connecting to the API (%s)',
                            $statusCode,
                            $exception->getRequest()->getUri()
                        ),
                        $statusCode,
                        $response->getHeaders(),
                        (string) $response->getBody()
                    );
                }
            );
    }

    /**
     * Create request for operation 'updateVrboListingRegistration'
     *
     * @param  string $id Repull listing id (numeric string) of a listing linked to Vrbo. (required)
     * @param  \Repull\Model\UpdateVrboListingRegistrationRequest $update_vrbo_listing_registration_request (required)
     * @param  string|null $vrbo_listing_number The Vrbo listing number to work on, when the listing is linked to more than one Vrbo unit. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['updateVrboListingRegistration'] to see the possible values for this operation
     *
     * @throws InvalidArgumentException
     * @return \GuzzleHttp\Psr7\Request
     */
    public function updateVrboListingRegistrationRequest(
        string $id,
        \Repull\Model\UpdateVrboListingRegistrationRequest $update_vrbo_listing_registration_request,
        ?string $vrbo_listing_number = null,
        string $contentType = self::contentTypes['updateVrboListingRegistration'][0]
    ): Request
    {

        // verify the required parameter 'id' is set
        if ($id === null || (is_array($id) && count($id) === 0)) {
            throw new InvalidArgumentException(
                'Missing the required parameter $id when calling updateVrboListingRegistration'
            );
        }

        // verify the required parameter 'update_vrbo_listing_registration_request' is set
        if ($update_vrbo_listing_registration_request === null || (is_array($update_vrbo_listing_registration_request) && count($update_vrbo_listing_registration_request) === 0)) {
            throw new InvalidArgumentException(
                'Missing the required parameter $update_vrbo_listing_registration_request when calling updateVrboListingRegistration'
            );
        }



        $resourcePath = '/v1/channels/vrbo/listings/{id}/registration';
        $formParams = [];
        $queryParams = [];
        $headerParams = [];
        $httpBody = '';
        $multipart = false;

        // query params
        $queryParams = array_merge($queryParams, ObjectSerializer::toQueryValue(
            $vrbo_listing_number,
            'vrboListingNumber', // param base name
            'string', // openApiType
            'form', // style
            true, // explode
            false, // required
            null // openApiFormat
        ) ?? []);


        // path params
        if ($id !== null) {
            $resourcePath = str_replace(
                '{id}',
                ObjectSerializer::toPathValue($id),
                $resourcePath
            );
        }


        $headers = $this->headerSelector->selectHeaders(
            ['application/json', ],
            $contentType,
            $multipart
        );

        // for model (json/xml)
        if (isset($update_vrbo_listing_registration_request)) {
            if (stripos($headers['Content-Type'], 'application/json') !== false) {
                # if Content-Type contains "application/json", json_encode the body
                $httpBody = \GuzzleHttp\Utils::jsonEncode(ObjectSerializer::sanitizeForSerialization($update_vrbo_listing_registration_request));
            } else {
                $httpBody = $update_vrbo_listing_registration_request;
            }
        } elseif (count($formParams) > 0) {
            if ($multipart) {
                $multipartContents = [];
                foreach ($formParams as $formParamName => $formParamValue) {
                    $formParamValueItems = is_array($formParamValue) ? $formParamValue : [$formParamValue];
                    foreach ($formParamValueItems as $formParamValueItem) {
                        $multipartContents[] = [
                            'name' => $formParamName,
                            'contents' => $formParamValueItem
                        ];
                    }
                }
                // for HTTP post (form)
                $httpBody = new MultipartStream($multipartContents);

            } elseif (stripos($headers['Content-Type'], 'application/json') !== false) {
                # if Content-Type contains "application/json", json_encode the form parameters
                $httpBody = \GuzzleHttp\Utils::jsonEncode($formParams);
            } else {
                // for HTTP post (form)
                $httpBody = ObjectSerializer::buildQuery($formParams);
            }
        }

        // this endpoint requires Bearer (API Key) authentication (access token)
        if (!empty($this->config->getAccessToken())) {
            $headers['Authorization'] = 'Bearer ' . $this->config->getAccessToken();
        }

        $defaultHeaders = [];
        if ($this->config->getUserAgent()) {
            $defaultHeaders['User-Agent'] = $this->config->getUserAgent();
        }

        $headers = array_merge(
            $defaultHeaders,
            $headerParams,
            $headers
        );

        $operationHost = $this->config->getHost();
        $query = ObjectSerializer::buildQuery($queryParams);
        return new Request(
            'PUT',
            $operationHost . $resourcePath . ($query ? "?{$query}" : ''),
            $headers,
            $httpBody
        );
    }

    /**
     * Operation updateVrboListingRooms
     *
     * Update the Vrbo rooms and spaces
     *
     * @param  string $id Repull listing id (numeric string) of a listing linked to Vrbo. (required)
     * @param  \Repull\Model\UpdateVrboListingRoomsRequest $update_vrbo_listing_rooms_request update_vrbo_listing_rooms_request (required)
     * @param  string|null $vrbo_listing_number The Vrbo listing number to work on, when the listing is linked to more than one Vrbo unit. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['updateVrboListingRooms'] to see the possible values for this operation
     *
     * @throws ApiException on non-2xx response or if the response body is not in the expected format
     * @throws InvalidArgumentException
     * @return \Repull\Model\UpdateVrboListingRooms200Response|\Repull\Model\Error
     */
    public function updateVrboListingRooms(
        string $id,
        \Repull\Model\UpdateVrboListingRoomsRequest $update_vrbo_listing_rooms_request,
        ?string $vrbo_listing_number = null,
        string $contentType = self::contentTypes['updateVrboListingRooms'][0]
    ): \Repull\Model\UpdateVrboListingRooms200Response|\Repull\Model\Error
    {
        list($response) = $this->updateVrboListingRoomsWithHttpInfo($id, $update_vrbo_listing_rooms_request, $vrbo_listing_number, $contentType);
        return $response;
    }

    /**
     * Operation updateVrboListingRoomsWithHttpInfo
     *
     * Update the Vrbo rooms and spaces
     *
     * @param  string $id Repull listing id (numeric string) of a listing linked to Vrbo. (required)
     * @param  \Repull\Model\UpdateVrboListingRoomsRequest $update_vrbo_listing_rooms_request (required)
     * @param  string|null $vrbo_listing_number The Vrbo listing number to work on, when the listing is linked to more than one Vrbo unit. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['updateVrboListingRooms'] to see the possible values for this operation
     *
     * @throws ApiException on non-2xx response or if the response body is not in the expected format
     * @throws InvalidArgumentException
     * @return array of \Repull\Model\UpdateVrboListingRooms200Response|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error, HTTP status code, HTTP response headers (array of strings)
     */
    public function updateVrboListingRoomsWithHttpInfo(
        string $id,
        \Repull\Model\UpdateVrboListingRoomsRequest $update_vrbo_listing_rooms_request,
        ?string $vrbo_listing_number = null,
        string $contentType = self::contentTypes['updateVrboListingRooms'][0]
    ): array
    {
        $request = $this->updateVrboListingRoomsRequest($id, $update_vrbo_listing_rooms_request, $vrbo_listing_number, $contentType);

        try {
            $options = $this->createHttpClientOption();
            try {
                $response = $this->client->send($request, $options);
            } catch (RequestException $e) {
                throw new ApiException(
                    "[{$e->getCode()}] {$e->getMessage()}",
                    (int) $e->getCode(),
                    $e->getResponse() ? $e->getResponse()->getHeaders() : null,
                    $e->getResponse() ? (string) $e->getResponse()->getBody() : null
                );
            } catch (ConnectException $e) {
                throw new ApiException(
                    "[{$e->getCode()}] {$e->getMessage()}",
                    (int) $e->getCode(),
                    null,
                    null
                );
            }

            $statusCode = $response->getStatusCode();

            switch($statusCode) {
                case 200:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\UpdateVrboListingRooms200Response',
                        $request,
                        $response,
                    );
                case 401:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 403:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 404:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 409:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 422:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 500:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 502:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 503:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 504:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
            }
            

            if ($statusCode < 200 || $statusCode > 299) {
                throw new ApiException(
                    sprintf(
                        '[%d] Error connecting to the API (%s)',
                        $statusCode,
                        (string) $request->getUri()
                    ),
                    $statusCode,
                    $response->getHeaders(),
                    (string) $response->getBody()
                );
            }

            return $this->handleResponseWithDataType(
                '\Repull\Model\UpdateVrboListingRooms200Response',
                $request,
                $response,
            );
        } catch (ApiException $e) {
            switch ($e->getCode()) {
                case 200:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\UpdateVrboListingRooms200Response',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 401:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 403:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 404:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 409:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 422:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 500:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 502:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 503:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 504:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
            }
        
            throw $e;
        }
    }

    /**
     * Operation updateVrboListingRoomsAsync
     *
     * Update the Vrbo rooms and spaces
     *
     * @param  string $id Repull listing id (numeric string) of a listing linked to Vrbo. (required)
     * @param  \Repull\Model\UpdateVrboListingRoomsRequest $update_vrbo_listing_rooms_request (required)
     * @param  string|null $vrbo_listing_number The Vrbo listing number to work on, when the listing is linked to more than one Vrbo unit. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['updateVrboListingRooms'] to see the possible values for this operation
     *
     * @throws InvalidArgumentException
     * @return PromiseInterface
     */
    public function updateVrboListingRoomsAsync(
        string $id,
        \Repull\Model\UpdateVrboListingRoomsRequest $update_vrbo_listing_rooms_request,
        ?string $vrbo_listing_number = null,
        string $contentType = self::contentTypes['updateVrboListingRooms'][0]
    ): PromiseInterface
    {
        return $this->updateVrboListingRoomsAsyncWithHttpInfo($id, $update_vrbo_listing_rooms_request, $vrbo_listing_number, $contentType)
            ->then(
                function ($response) {
                    return $response[0];
                }
            );
    }

    /**
     * Operation updateVrboListingRoomsAsyncWithHttpInfo
     *
     * Update the Vrbo rooms and spaces
     *
     * @param  string $id Repull listing id (numeric string) of a listing linked to Vrbo. (required)
     * @param  \Repull\Model\UpdateVrboListingRoomsRequest $update_vrbo_listing_rooms_request (required)
     * @param  string|null $vrbo_listing_number The Vrbo listing number to work on, when the listing is linked to more than one Vrbo unit. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['updateVrboListingRooms'] to see the possible values for this operation
     *
     * @throws InvalidArgumentException
     * @return PromiseInterface
     */
    public function updateVrboListingRoomsAsyncWithHttpInfo(
        string $id,
        \Repull\Model\UpdateVrboListingRoomsRequest $update_vrbo_listing_rooms_request,
        ?string $vrbo_listing_number = null,
        string $contentType = self::contentTypes['updateVrboListingRooms'][0]
    ): PromiseInterface
    {
        $returnType = '\Repull\Model\UpdateVrboListingRooms200Response';
        $request = $this->updateVrboListingRoomsRequest($id, $update_vrbo_listing_rooms_request, $vrbo_listing_number, $contentType);

        return $this->client
            ->sendAsync($request, $this->createHttpClientOption())
            ->then(
                function ($response) use ($returnType) {
                    if (in_array($returnType, ['\SplFileObject', '\Psr\Http\Message\StreamInterface'])) {
                        $content = $response->getBody(); //stream goes to serializer
                    } else {
                        $content = (string) $response->getBody();
                        if ($returnType !== 'string') {
                            $content = json_decode($content);
                        }
                    }

                    return [
                        ObjectSerializer::deserialize($content, $returnType, []),
                        $response->getStatusCode(),
                        $response->getHeaders()
                    ];
                },
                function ($exception) {
                    $response = $exception->getResponse();
                    $statusCode = $response->getStatusCode();
                    throw new ApiException(
                        sprintf(
                            '[%d] Error connecting to the API (%s)',
                            $statusCode,
                            $exception->getRequest()->getUri()
                        ),
                        $statusCode,
                        $response->getHeaders(),
                        (string) $response->getBody()
                    );
                }
            );
    }

    /**
     * Create request for operation 'updateVrboListingRooms'
     *
     * @param  string $id Repull listing id (numeric string) of a listing linked to Vrbo. (required)
     * @param  \Repull\Model\UpdateVrboListingRoomsRequest $update_vrbo_listing_rooms_request (required)
     * @param  string|null $vrbo_listing_number The Vrbo listing number to work on, when the listing is linked to more than one Vrbo unit. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['updateVrboListingRooms'] to see the possible values for this operation
     *
     * @throws InvalidArgumentException
     * @return \GuzzleHttp\Psr7\Request
     */
    public function updateVrboListingRoomsRequest(
        string $id,
        \Repull\Model\UpdateVrboListingRoomsRequest $update_vrbo_listing_rooms_request,
        ?string $vrbo_listing_number = null,
        string $contentType = self::contentTypes['updateVrboListingRooms'][0]
    ): Request
    {

        // verify the required parameter 'id' is set
        if ($id === null || (is_array($id) && count($id) === 0)) {
            throw new InvalidArgumentException(
                'Missing the required parameter $id when calling updateVrboListingRooms'
            );
        }

        // verify the required parameter 'update_vrbo_listing_rooms_request' is set
        if ($update_vrbo_listing_rooms_request === null || (is_array($update_vrbo_listing_rooms_request) && count($update_vrbo_listing_rooms_request) === 0)) {
            throw new InvalidArgumentException(
                'Missing the required parameter $update_vrbo_listing_rooms_request when calling updateVrboListingRooms'
            );
        }



        $resourcePath = '/v1/channels/vrbo/listings/{id}/rooms';
        $formParams = [];
        $queryParams = [];
        $headerParams = [];
        $httpBody = '';
        $multipart = false;

        // query params
        $queryParams = array_merge($queryParams, ObjectSerializer::toQueryValue(
            $vrbo_listing_number,
            'vrboListingNumber', // param base name
            'string', // openApiType
            'form', // style
            true, // explode
            false, // required
            null // openApiFormat
        ) ?? []);


        // path params
        if ($id !== null) {
            $resourcePath = str_replace(
                '{id}',
                ObjectSerializer::toPathValue($id),
                $resourcePath
            );
        }


        $headers = $this->headerSelector->selectHeaders(
            ['application/json', ],
            $contentType,
            $multipart
        );

        // for model (json/xml)
        if (isset($update_vrbo_listing_rooms_request)) {
            if (stripos($headers['Content-Type'], 'application/json') !== false) {
                # if Content-Type contains "application/json", json_encode the body
                $httpBody = \GuzzleHttp\Utils::jsonEncode(ObjectSerializer::sanitizeForSerialization($update_vrbo_listing_rooms_request));
            } else {
                $httpBody = $update_vrbo_listing_rooms_request;
            }
        } elseif (count($formParams) > 0) {
            if ($multipart) {
                $multipartContents = [];
                foreach ($formParams as $formParamName => $formParamValue) {
                    $formParamValueItems = is_array($formParamValue) ? $formParamValue : [$formParamValue];
                    foreach ($formParamValueItems as $formParamValueItem) {
                        $multipartContents[] = [
                            'name' => $formParamName,
                            'contents' => $formParamValueItem
                        ];
                    }
                }
                // for HTTP post (form)
                $httpBody = new MultipartStream($multipartContents);

            } elseif (stripos($headers['Content-Type'], 'application/json') !== false) {
                # if Content-Type contains "application/json", json_encode the form parameters
                $httpBody = \GuzzleHttp\Utils::jsonEncode($formParams);
            } else {
                // for HTTP post (form)
                $httpBody = ObjectSerializer::buildQuery($formParams);
            }
        }

        // this endpoint requires Bearer (API Key) authentication (access token)
        if (!empty($this->config->getAccessToken())) {
            $headers['Authorization'] = 'Bearer ' . $this->config->getAccessToken();
        }

        $defaultHeaders = [];
        if ($this->config->getUserAgent()) {
            $defaultHeaders['User-Agent'] = $this->config->getUserAgent();
        }

        $headers = array_merge(
            $defaultHeaders,
            $headerParams,
            $headers
        );

        $operationHost = $this->config->getHost();
        $query = ObjectSerializer::buildQuery($queryParams);
        return new Request(
            'PUT',
            $operationHost . $resourcePath . ($query ? "?{$query}" : ''),
            $headers,
            $httpBody
        );
    }

    /**
     * Operation updateVrboListingStory
     *
     * Update the Vrbo host story
     *
     * @param  string $id Repull listing id (numeric string) of a listing linked to Vrbo. (required)
     * @param  \Repull\Model\UpdateVrboListingStoryRequest $update_vrbo_listing_story_request update_vrbo_listing_story_request (required)
     * @param  string|null $vrbo_listing_number The Vrbo listing number to work on, when the listing is linked to more than one Vrbo unit. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['updateVrboListingStory'] to see the possible values for this operation
     *
     * @throws ApiException on non-2xx response or if the response body is not in the expected format
     * @throws InvalidArgumentException
     * @return \Repull\Model\UpdateVrboListingStory200Response|\Repull\Model\Error
     */
    public function updateVrboListingStory(
        string $id,
        \Repull\Model\UpdateVrboListingStoryRequest $update_vrbo_listing_story_request,
        ?string $vrbo_listing_number = null,
        string $contentType = self::contentTypes['updateVrboListingStory'][0]
    ): \Repull\Model\UpdateVrboListingStory200Response|\Repull\Model\Error
    {
        list($response) = $this->updateVrboListingStoryWithHttpInfo($id, $update_vrbo_listing_story_request, $vrbo_listing_number, $contentType);
        return $response;
    }

    /**
     * Operation updateVrboListingStoryWithHttpInfo
     *
     * Update the Vrbo host story
     *
     * @param  string $id Repull listing id (numeric string) of a listing linked to Vrbo. (required)
     * @param  \Repull\Model\UpdateVrboListingStoryRequest $update_vrbo_listing_story_request (required)
     * @param  string|null $vrbo_listing_number The Vrbo listing number to work on, when the listing is linked to more than one Vrbo unit. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['updateVrboListingStory'] to see the possible values for this operation
     *
     * @throws ApiException on non-2xx response or if the response body is not in the expected format
     * @throws InvalidArgumentException
     * @return array of \Repull\Model\UpdateVrboListingStory200Response|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error, HTTP status code, HTTP response headers (array of strings)
     */
    public function updateVrboListingStoryWithHttpInfo(
        string $id,
        \Repull\Model\UpdateVrboListingStoryRequest $update_vrbo_listing_story_request,
        ?string $vrbo_listing_number = null,
        string $contentType = self::contentTypes['updateVrboListingStory'][0]
    ): array
    {
        $request = $this->updateVrboListingStoryRequest($id, $update_vrbo_listing_story_request, $vrbo_listing_number, $contentType);

        try {
            $options = $this->createHttpClientOption();
            try {
                $response = $this->client->send($request, $options);
            } catch (RequestException $e) {
                throw new ApiException(
                    "[{$e->getCode()}] {$e->getMessage()}",
                    (int) $e->getCode(),
                    $e->getResponse() ? $e->getResponse()->getHeaders() : null,
                    $e->getResponse() ? (string) $e->getResponse()->getBody() : null
                );
            } catch (ConnectException $e) {
                throw new ApiException(
                    "[{$e->getCode()}] {$e->getMessage()}",
                    (int) $e->getCode(),
                    null,
                    null
                );
            }

            $statusCode = $response->getStatusCode();

            switch($statusCode) {
                case 200:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\UpdateVrboListingStory200Response',
                        $request,
                        $response,
                    );
                case 401:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 403:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 404:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 409:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 422:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 500:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 502:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 503:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 504:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
            }
            

            if ($statusCode < 200 || $statusCode > 299) {
                throw new ApiException(
                    sprintf(
                        '[%d] Error connecting to the API (%s)',
                        $statusCode,
                        (string) $request->getUri()
                    ),
                    $statusCode,
                    $response->getHeaders(),
                    (string) $response->getBody()
                );
            }

            return $this->handleResponseWithDataType(
                '\Repull\Model\UpdateVrboListingStory200Response',
                $request,
                $response,
            );
        } catch (ApiException $e) {
            switch ($e->getCode()) {
                case 200:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\UpdateVrboListingStory200Response',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 401:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 403:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 404:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 409:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 422:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 500:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 502:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 503:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 504:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
            }
        
            throw $e;
        }
    }

    /**
     * Operation updateVrboListingStoryAsync
     *
     * Update the Vrbo host story
     *
     * @param  string $id Repull listing id (numeric string) of a listing linked to Vrbo. (required)
     * @param  \Repull\Model\UpdateVrboListingStoryRequest $update_vrbo_listing_story_request (required)
     * @param  string|null $vrbo_listing_number The Vrbo listing number to work on, when the listing is linked to more than one Vrbo unit. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['updateVrboListingStory'] to see the possible values for this operation
     *
     * @throws InvalidArgumentException
     * @return PromiseInterface
     */
    public function updateVrboListingStoryAsync(
        string $id,
        \Repull\Model\UpdateVrboListingStoryRequest $update_vrbo_listing_story_request,
        ?string $vrbo_listing_number = null,
        string $contentType = self::contentTypes['updateVrboListingStory'][0]
    ): PromiseInterface
    {
        return $this->updateVrboListingStoryAsyncWithHttpInfo($id, $update_vrbo_listing_story_request, $vrbo_listing_number, $contentType)
            ->then(
                function ($response) {
                    return $response[0];
                }
            );
    }

    /**
     * Operation updateVrboListingStoryAsyncWithHttpInfo
     *
     * Update the Vrbo host story
     *
     * @param  string $id Repull listing id (numeric string) of a listing linked to Vrbo. (required)
     * @param  \Repull\Model\UpdateVrboListingStoryRequest $update_vrbo_listing_story_request (required)
     * @param  string|null $vrbo_listing_number The Vrbo listing number to work on, when the listing is linked to more than one Vrbo unit. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['updateVrboListingStory'] to see the possible values for this operation
     *
     * @throws InvalidArgumentException
     * @return PromiseInterface
     */
    public function updateVrboListingStoryAsyncWithHttpInfo(
        string $id,
        \Repull\Model\UpdateVrboListingStoryRequest $update_vrbo_listing_story_request,
        ?string $vrbo_listing_number = null,
        string $contentType = self::contentTypes['updateVrboListingStory'][0]
    ): PromiseInterface
    {
        $returnType = '\Repull\Model\UpdateVrboListingStory200Response';
        $request = $this->updateVrboListingStoryRequest($id, $update_vrbo_listing_story_request, $vrbo_listing_number, $contentType);

        return $this->client
            ->sendAsync($request, $this->createHttpClientOption())
            ->then(
                function ($response) use ($returnType) {
                    if (in_array($returnType, ['\SplFileObject', '\Psr\Http\Message\StreamInterface'])) {
                        $content = $response->getBody(); //stream goes to serializer
                    } else {
                        $content = (string) $response->getBody();
                        if ($returnType !== 'string') {
                            $content = json_decode($content);
                        }
                    }

                    return [
                        ObjectSerializer::deserialize($content, $returnType, []),
                        $response->getStatusCode(),
                        $response->getHeaders()
                    ];
                },
                function ($exception) {
                    $response = $exception->getResponse();
                    $statusCode = $response->getStatusCode();
                    throw new ApiException(
                        sprintf(
                            '[%d] Error connecting to the API (%s)',
                            $statusCode,
                            $exception->getRequest()->getUri()
                        ),
                        $statusCode,
                        $response->getHeaders(),
                        (string) $response->getBody()
                    );
                }
            );
    }

    /**
     * Create request for operation 'updateVrboListingStory'
     *
     * @param  string $id Repull listing id (numeric string) of a listing linked to Vrbo. (required)
     * @param  \Repull\Model\UpdateVrboListingStoryRequest $update_vrbo_listing_story_request (required)
     * @param  string|null $vrbo_listing_number The Vrbo listing number to work on, when the listing is linked to more than one Vrbo unit. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['updateVrboListingStory'] to see the possible values for this operation
     *
     * @throws InvalidArgumentException
     * @return \GuzzleHttp\Psr7\Request
     */
    public function updateVrboListingStoryRequest(
        string $id,
        \Repull\Model\UpdateVrboListingStoryRequest $update_vrbo_listing_story_request,
        ?string $vrbo_listing_number = null,
        string $contentType = self::contentTypes['updateVrboListingStory'][0]
    ): Request
    {

        // verify the required parameter 'id' is set
        if ($id === null || (is_array($id) && count($id) === 0)) {
            throw new InvalidArgumentException(
                'Missing the required parameter $id when calling updateVrboListingStory'
            );
        }

        // verify the required parameter 'update_vrbo_listing_story_request' is set
        if ($update_vrbo_listing_story_request === null || (is_array($update_vrbo_listing_story_request) && count($update_vrbo_listing_story_request) === 0)) {
            throw new InvalidArgumentException(
                'Missing the required parameter $update_vrbo_listing_story_request when calling updateVrboListingStory'
            );
        }



        $resourcePath = '/v1/channels/vrbo/listings/{id}/story';
        $formParams = [];
        $queryParams = [];
        $headerParams = [];
        $httpBody = '';
        $multipart = false;

        // query params
        $queryParams = array_merge($queryParams, ObjectSerializer::toQueryValue(
            $vrbo_listing_number,
            'vrboListingNumber', // param base name
            'string', // openApiType
            'form', // style
            true, // explode
            false, // required
            null // openApiFormat
        ) ?? []);


        // path params
        if ($id !== null) {
            $resourcePath = str_replace(
                '{id}',
                ObjectSerializer::toPathValue($id),
                $resourcePath
            );
        }


        $headers = $this->headerSelector->selectHeaders(
            ['application/json', ],
            $contentType,
            $multipart
        );

        // for model (json/xml)
        if (isset($update_vrbo_listing_story_request)) {
            if (stripos($headers['Content-Type'], 'application/json') !== false) {
                # if Content-Type contains "application/json", json_encode the body
                $httpBody = \GuzzleHttp\Utils::jsonEncode(ObjectSerializer::sanitizeForSerialization($update_vrbo_listing_story_request));
            } else {
                $httpBody = $update_vrbo_listing_story_request;
            }
        } elseif (count($formParams) > 0) {
            if ($multipart) {
                $multipartContents = [];
                foreach ($formParams as $formParamName => $formParamValue) {
                    $formParamValueItems = is_array($formParamValue) ? $formParamValue : [$formParamValue];
                    foreach ($formParamValueItems as $formParamValueItem) {
                        $multipartContents[] = [
                            'name' => $formParamName,
                            'contents' => $formParamValueItem
                        ];
                    }
                }
                // for HTTP post (form)
                $httpBody = new MultipartStream($multipartContents);

            } elseif (stripos($headers['Content-Type'], 'application/json') !== false) {
                # if Content-Type contains "application/json", json_encode the form parameters
                $httpBody = \GuzzleHttp\Utils::jsonEncode($formParams);
            } else {
                // for HTTP post (form)
                $httpBody = ObjectSerializer::buildQuery($formParams);
            }
        }

        // this endpoint requires Bearer (API Key) authentication (access token)
        if (!empty($this->config->getAccessToken())) {
            $headers['Authorization'] = 'Bearer ' . $this->config->getAccessToken();
        }

        $defaultHeaders = [];
        if ($this->config->getUserAgent()) {
            $defaultHeaders['User-Agent'] = $this->config->getUserAgent();
        }

        $headers = array_merge(
            $defaultHeaders,
            $headerParams,
            $headers
        );

        $operationHost = $this->config->getHost();
        $query = ObjectSerializer::buildQuery($queryParams);
        return new Request(
            'PUT',
            $operationHost . $resourcePath . ($query ? "?{$query}" : ''),
            $headers,
            $httpBody
        );
    }

    /**
     * Operation updateVrboListingTaxes
     *
     * Update the Vrbo taxes
     *
     * @param  string $id Repull listing id (numeric string) of a listing linked to Vrbo. (required)
     * @param  \Repull\Model\UpdateVrboListingTaxesRequest $update_vrbo_listing_taxes_request update_vrbo_listing_taxes_request (required)
     * @param  string|null $vrbo_listing_number The Vrbo listing number to work on, when the listing is linked to more than one Vrbo unit. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['updateVrboListingTaxes'] to see the possible values for this operation
     *
     * @throws ApiException on non-2xx response or if the response body is not in the expected format
     * @throws InvalidArgumentException
     * @return \Repull\Model\UpdateVrboListingTaxes200Response|\Repull\Model\Error
     */
    public function updateVrboListingTaxes(
        string $id,
        \Repull\Model\UpdateVrboListingTaxesRequest $update_vrbo_listing_taxes_request,
        ?string $vrbo_listing_number = null,
        string $contentType = self::contentTypes['updateVrboListingTaxes'][0]
    ): \Repull\Model\UpdateVrboListingTaxes200Response|\Repull\Model\Error
    {
        list($response) = $this->updateVrboListingTaxesWithHttpInfo($id, $update_vrbo_listing_taxes_request, $vrbo_listing_number, $contentType);
        return $response;
    }

    /**
     * Operation updateVrboListingTaxesWithHttpInfo
     *
     * Update the Vrbo taxes
     *
     * @param  string $id Repull listing id (numeric string) of a listing linked to Vrbo. (required)
     * @param  \Repull\Model\UpdateVrboListingTaxesRequest $update_vrbo_listing_taxes_request (required)
     * @param  string|null $vrbo_listing_number The Vrbo listing number to work on, when the listing is linked to more than one Vrbo unit. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['updateVrboListingTaxes'] to see the possible values for this operation
     *
     * @throws ApiException on non-2xx response or if the response body is not in the expected format
     * @throws InvalidArgumentException
     * @return array of \Repull\Model\UpdateVrboListingTaxes200Response|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error, HTTP status code, HTTP response headers (array of strings)
     */
    public function updateVrboListingTaxesWithHttpInfo(
        string $id,
        \Repull\Model\UpdateVrboListingTaxesRequest $update_vrbo_listing_taxes_request,
        ?string $vrbo_listing_number = null,
        string $contentType = self::contentTypes['updateVrboListingTaxes'][0]
    ): array
    {
        $request = $this->updateVrboListingTaxesRequest($id, $update_vrbo_listing_taxes_request, $vrbo_listing_number, $contentType);

        try {
            $options = $this->createHttpClientOption();
            try {
                $response = $this->client->send($request, $options);
            } catch (RequestException $e) {
                throw new ApiException(
                    "[{$e->getCode()}] {$e->getMessage()}",
                    (int) $e->getCode(),
                    $e->getResponse() ? $e->getResponse()->getHeaders() : null,
                    $e->getResponse() ? (string) $e->getResponse()->getBody() : null
                );
            } catch (ConnectException $e) {
                throw new ApiException(
                    "[{$e->getCode()}] {$e->getMessage()}",
                    (int) $e->getCode(),
                    null,
                    null
                );
            }

            $statusCode = $response->getStatusCode();

            switch($statusCode) {
                case 200:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\UpdateVrboListingTaxes200Response',
                        $request,
                        $response,
                    );
                case 401:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 403:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 404:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 409:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 422:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 500:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 502:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 503:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 504:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
            }
            

            if ($statusCode < 200 || $statusCode > 299) {
                throw new ApiException(
                    sprintf(
                        '[%d] Error connecting to the API (%s)',
                        $statusCode,
                        (string) $request->getUri()
                    ),
                    $statusCode,
                    $response->getHeaders(),
                    (string) $response->getBody()
                );
            }

            return $this->handleResponseWithDataType(
                '\Repull\Model\UpdateVrboListingTaxes200Response',
                $request,
                $response,
            );
        } catch (ApiException $e) {
            switch ($e->getCode()) {
                case 200:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\UpdateVrboListingTaxes200Response',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 401:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 403:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 404:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 409:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 422:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 500:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 502:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 503:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 504:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
            }
        
            throw $e;
        }
    }

    /**
     * Operation updateVrboListingTaxesAsync
     *
     * Update the Vrbo taxes
     *
     * @param  string $id Repull listing id (numeric string) of a listing linked to Vrbo. (required)
     * @param  \Repull\Model\UpdateVrboListingTaxesRequest $update_vrbo_listing_taxes_request (required)
     * @param  string|null $vrbo_listing_number The Vrbo listing number to work on, when the listing is linked to more than one Vrbo unit. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['updateVrboListingTaxes'] to see the possible values for this operation
     *
     * @throws InvalidArgumentException
     * @return PromiseInterface
     */
    public function updateVrboListingTaxesAsync(
        string $id,
        \Repull\Model\UpdateVrboListingTaxesRequest $update_vrbo_listing_taxes_request,
        ?string $vrbo_listing_number = null,
        string $contentType = self::contentTypes['updateVrboListingTaxes'][0]
    ): PromiseInterface
    {
        return $this->updateVrboListingTaxesAsyncWithHttpInfo($id, $update_vrbo_listing_taxes_request, $vrbo_listing_number, $contentType)
            ->then(
                function ($response) {
                    return $response[0];
                }
            );
    }

    /**
     * Operation updateVrboListingTaxesAsyncWithHttpInfo
     *
     * Update the Vrbo taxes
     *
     * @param  string $id Repull listing id (numeric string) of a listing linked to Vrbo. (required)
     * @param  \Repull\Model\UpdateVrboListingTaxesRequest $update_vrbo_listing_taxes_request (required)
     * @param  string|null $vrbo_listing_number The Vrbo listing number to work on, when the listing is linked to more than one Vrbo unit. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['updateVrboListingTaxes'] to see the possible values for this operation
     *
     * @throws InvalidArgumentException
     * @return PromiseInterface
     */
    public function updateVrboListingTaxesAsyncWithHttpInfo(
        string $id,
        \Repull\Model\UpdateVrboListingTaxesRequest $update_vrbo_listing_taxes_request,
        ?string $vrbo_listing_number = null,
        string $contentType = self::contentTypes['updateVrboListingTaxes'][0]
    ): PromiseInterface
    {
        $returnType = '\Repull\Model\UpdateVrboListingTaxes200Response';
        $request = $this->updateVrboListingTaxesRequest($id, $update_vrbo_listing_taxes_request, $vrbo_listing_number, $contentType);

        return $this->client
            ->sendAsync($request, $this->createHttpClientOption())
            ->then(
                function ($response) use ($returnType) {
                    if (in_array($returnType, ['\SplFileObject', '\Psr\Http\Message\StreamInterface'])) {
                        $content = $response->getBody(); //stream goes to serializer
                    } else {
                        $content = (string) $response->getBody();
                        if ($returnType !== 'string') {
                            $content = json_decode($content);
                        }
                    }

                    return [
                        ObjectSerializer::deserialize($content, $returnType, []),
                        $response->getStatusCode(),
                        $response->getHeaders()
                    ];
                },
                function ($exception) {
                    $response = $exception->getResponse();
                    $statusCode = $response->getStatusCode();
                    throw new ApiException(
                        sprintf(
                            '[%d] Error connecting to the API (%s)',
                            $statusCode,
                            $exception->getRequest()->getUri()
                        ),
                        $statusCode,
                        $response->getHeaders(),
                        (string) $response->getBody()
                    );
                }
            );
    }

    /**
     * Create request for operation 'updateVrboListingTaxes'
     *
     * @param  string $id Repull listing id (numeric string) of a listing linked to Vrbo. (required)
     * @param  \Repull\Model\UpdateVrboListingTaxesRequest $update_vrbo_listing_taxes_request (required)
     * @param  string|null $vrbo_listing_number The Vrbo listing number to work on, when the listing is linked to more than one Vrbo unit. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['updateVrboListingTaxes'] to see the possible values for this operation
     *
     * @throws InvalidArgumentException
     * @return \GuzzleHttp\Psr7\Request
     */
    public function updateVrboListingTaxesRequest(
        string $id,
        \Repull\Model\UpdateVrboListingTaxesRequest $update_vrbo_listing_taxes_request,
        ?string $vrbo_listing_number = null,
        string $contentType = self::contentTypes['updateVrboListingTaxes'][0]
    ): Request
    {

        // verify the required parameter 'id' is set
        if ($id === null || (is_array($id) && count($id) === 0)) {
            throw new InvalidArgumentException(
                'Missing the required parameter $id when calling updateVrboListingTaxes'
            );
        }

        // verify the required parameter 'update_vrbo_listing_taxes_request' is set
        if ($update_vrbo_listing_taxes_request === null || (is_array($update_vrbo_listing_taxes_request) && count($update_vrbo_listing_taxes_request) === 0)) {
            throw new InvalidArgumentException(
                'Missing the required parameter $update_vrbo_listing_taxes_request when calling updateVrboListingTaxes'
            );
        }



        $resourcePath = '/v1/channels/vrbo/listings/{id}/taxes';
        $formParams = [];
        $queryParams = [];
        $headerParams = [];
        $httpBody = '';
        $multipart = false;

        // query params
        $queryParams = array_merge($queryParams, ObjectSerializer::toQueryValue(
            $vrbo_listing_number,
            'vrboListingNumber', // param base name
            'string', // openApiType
            'form', // style
            true, // explode
            false, // required
            null // openApiFormat
        ) ?? []);


        // path params
        if ($id !== null) {
            $resourcePath = str_replace(
                '{id}',
                ObjectSerializer::toPathValue($id),
                $resourcePath
            );
        }


        $headers = $this->headerSelector->selectHeaders(
            ['application/json', ],
            $contentType,
            $multipart
        );

        // for model (json/xml)
        if (isset($update_vrbo_listing_taxes_request)) {
            if (stripos($headers['Content-Type'], 'application/json') !== false) {
                # if Content-Type contains "application/json", json_encode the body
                $httpBody = \GuzzleHttp\Utils::jsonEncode(ObjectSerializer::sanitizeForSerialization($update_vrbo_listing_taxes_request));
            } else {
                $httpBody = $update_vrbo_listing_taxes_request;
            }
        } elseif (count($formParams) > 0) {
            if ($multipart) {
                $multipartContents = [];
                foreach ($formParams as $formParamName => $formParamValue) {
                    $formParamValueItems = is_array($formParamValue) ? $formParamValue : [$formParamValue];
                    foreach ($formParamValueItems as $formParamValueItem) {
                        $multipartContents[] = [
                            'name' => $formParamName,
                            'contents' => $formParamValueItem
                        ];
                    }
                }
                // for HTTP post (form)
                $httpBody = new MultipartStream($multipartContents);

            } elseif (stripos($headers['Content-Type'], 'application/json') !== false) {
                # if Content-Type contains "application/json", json_encode the form parameters
                $httpBody = \GuzzleHttp\Utils::jsonEncode($formParams);
            } else {
                // for HTTP post (form)
                $httpBody = ObjectSerializer::buildQuery($formParams);
            }
        }

        // this endpoint requires Bearer (API Key) authentication (access token)
        if (!empty($this->config->getAccessToken())) {
            $headers['Authorization'] = 'Bearer ' . $this->config->getAccessToken();
        }

        $defaultHeaders = [];
        if ($this->config->getUserAgent()) {
            $defaultHeaders['User-Agent'] = $this->config->getUserAgent();
        }

        $headers = array_merge(
            $defaultHeaders,
            $headerParams,
            $headers
        );

        $operationHost = $this->config->getHost();
        $query = ObjectSerializer::buildQuery($queryParams);
        return new Request(
            'PUT',
            $operationHost . $resourcePath . ($query ? "?{$query}" : ''),
            $headers,
            $httpBody
        );
    }

    /**
     * Operation updateVrboListingTranslations
     *
     * Update the Vrbo manual translations
     *
     * @param  string $id Repull listing id (numeric string) of a listing linked to Vrbo. (required)
     * @param  \Repull\Model\UpdateVrboListingTranslationsRequest $update_vrbo_listing_translations_request update_vrbo_listing_translations_request (required)
     * @param  string|null $vrbo_listing_number The Vrbo listing number to work on, when the listing is linked to more than one Vrbo unit. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['updateVrboListingTranslations'] to see the possible values for this operation
     *
     * @throws ApiException on non-2xx response or if the response body is not in the expected format
     * @throws InvalidArgumentException
     * @return \Repull\Model\UpdateVrboListingTranslations200Response|\Repull\Model\Error
     */
    public function updateVrboListingTranslations(
        string $id,
        \Repull\Model\UpdateVrboListingTranslationsRequest $update_vrbo_listing_translations_request,
        ?string $vrbo_listing_number = null,
        string $contentType = self::contentTypes['updateVrboListingTranslations'][0]
    ): \Repull\Model\UpdateVrboListingTranslations200Response|\Repull\Model\Error
    {
        list($response) = $this->updateVrboListingTranslationsWithHttpInfo($id, $update_vrbo_listing_translations_request, $vrbo_listing_number, $contentType);
        return $response;
    }

    /**
     * Operation updateVrboListingTranslationsWithHttpInfo
     *
     * Update the Vrbo manual translations
     *
     * @param  string $id Repull listing id (numeric string) of a listing linked to Vrbo. (required)
     * @param  \Repull\Model\UpdateVrboListingTranslationsRequest $update_vrbo_listing_translations_request (required)
     * @param  string|null $vrbo_listing_number The Vrbo listing number to work on, when the listing is linked to more than one Vrbo unit. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['updateVrboListingTranslations'] to see the possible values for this operation
     *
     * @throws ApiException on non-2xx response or if the response body is not in the expected format
     * @throws InvalidArgumentException
     * @return array of \Repull\Model\UpdateVrboListingTranslations200Response|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error, HTTP status code, HTTP response headers (array of strings)
     */
    public function updateVrboListingTranslationsWithHttpInfo(
        string $id,
        \Repull\Model\UpdateVrboListingTranslationsRequest $update_vrbo_listing_translations_request,
        ?string $vrbo_listing_number = null,
        string $contentType = self::contentTypes['updateVrboListingTranslations'][0]
    ): array
    {
        $request = $this->updateVrboListingTranslationsRequest($id, $update_vrbo_listing_translations_request, $vrbo_listing_number, $contentType);

        try {
            $options = $this->createHttpClientOption();
            try {
                $response = $this->client->send($request, $options);
            } catch (RequestException $e) {
                throw new ApiException(
                    "[{$e->getCode()}] {$e->getMessage()}",
                    (int) $e->getCode(),
                    $e->getResponse() ? $e->getResponse()->getHeaders() : null,
                    $e->getResponse() ? (string) $e->getResponse()->getBody() : null
                );
            } catch (ConnectException $e) {
                throw new ApiException(
                    "[{$e->getCode()}] {$e->getMessage()}",
                    (int) $e->getCode(),
                    null,
                    null
                );
            }

            $statusCode = $response->getStatusCode();

            switch($statusCode) {
                case 200:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\UpdateVrboListingTranslations200Response',
                        $request,
                        $response,
                    );
                case 401:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 403:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 404:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 409:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 422:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 500:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 502:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 503:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 504:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
            }
            

            if ($statusCode < 200 || $statusCode > 299) {
                throw new ApiException(
                    sprintf(
                        '[%d] Error connecting to the API (%s)',
                        $statusCode,
                        (string) $request->getUri()
                    ),
                    $statusCode,
                    $response->getHeaders(),
                    (string) $response->getBody()
                );
            }

            return $this->handleResponseWithDataType(
                '\Repull\Model\UpdateVrboListingTranslations200Response',
                $request,
                $response,
            );
        } catch (ApiException $e) {
            switch ($e->getCode()) {
                case 200:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\UpdateVrboListingTranslations200Response',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 401:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 403:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 404:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 409:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 422:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 500:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 502:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 503:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 504:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
            }
        
            throw $e;
        }
    }

    /**
     * Operation updateVrboListingTranslationsAsync
     *
     * Update the Vrbo manual translations
     *
     * @param  string $id Repull listing id (numeric string) of a listing linked to Vrbo. (required)
     * @param  \Repull\Model\UpdateVrboListingTranslationsRequest $update_vrbo_listing_translations_request (required)
     * @param  string|null $vrbo_listing_number The Vrbo listing number to work on, when the listing is linked to more than one Vrbo unit. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['updateVrboListingTranslations'] to see the possible values for this operation
     *
     * @throws InvalidArgumentException
     * @return PromiseInterface
     */
    public function updateVrboListingTranslationsAsync(
        string $id,
        \Repull\Model\UpdateVrboListingTranslationsRequest $update_vrbo_listing_translations_request,
        ?string $vrbo_listing_number = null,
        string $contentType = self::contentTypes['updateVrboListingTranslations'][0]
    ): PromiseInterface
    {
        return $this->updateVrboListingTranslationsAsyncWithHttpInfo($id, $update_vrbo_listing_translations_request, $vrbo_listing_number, $contentType)
            ->then(
                function ($response) {
                    return $response[0];
                }
            );
    }

    /**
     * Operation updateVrboListingTranslationsAsyncWithHttpInfo
     *
     * Update the Vrbo manual translations
     *
     * @param  string $id Repull listing id (numeric string) of a listing linked to Vrbo. (required)
     * @param  \Repull\Model\UpdateVrboListingTranslationsRequest $update_vrbo_listing_translations_request (required)
     * @param  string|null $vrbo_listing_number The Vrbo listing number to work on, when the listing is linked to more than one Vrbo unit. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['updateVrboListingTranslations'] to see the possible values for this operation
     *
     * @throws InvalidArgumentException
     * @return PromiseInterface
     */
    public function updateVrboListingTranslationsAsyncWithHttpInfo(
        string $id,
        \Repull\Model\UpdateVrboListingTranslationsRequest $update_vrbo_listing_translations_request,
        ?string $vrbo_listing_number = null,
        string $contentType = self::contentTypes['updateVrboListingTranslations'][0]
    ): PromiseInterface
    {
        $returnType = '\Repull\Model\UpdateVrboListingTranslations200Response';
        $request = $this->updateVrboListingTranslationsRequest($id, $update_vrbo_listing_translations_request, $vrbo_listing_number, $contentType);

        return $this->client
            ->sendAsync($request, $this->createHttpClientOption())
            ->then(
                function ($response) use ($returnType) {
                    if (in_array($returnType, ['\SplFileObject', '\Psr\Http\Message\StreamInterface'])) {
                        $content = $response->getBody(); //stream goes to serializer
                    } else {
                        $content = (string) $response->getBody();
                        if ($returnType !== 'string') {
                            $content = json_decode($content);
                        }
                    }

                    return [
                        ObjectSerializer::deserialize($content, $returnType, []),
                        $response->getStatusCode(),
                        $response->getHeaders()
                    ];
                },
                function ($exception) {
                    $response = $exception->getResponse();
                    $statusCode = $response->getStatusCode();
                    throw new ApiException(
                        sprintf(
                            '[%d] Error connecting to the API (%s)',
                            $statusCode,
                            $exception->getRequest()->getUri()
                        ),
                        $statusCode,
                        $response->getHeaders(),
                        (string) $response->getBody()
                    );
                }
            );
    }

    /**
     * Create request for operation 'updateVrboListingTranslations'
     *
     * @param  string $id Repull listing id (numeric string) of a listing linked to Vrbo. (required)
     * @param  \Repull\Model\UpdateVrboListingTranslationsRequest $update_vrbo_listing_translations_request (required)
     * @param  string|null $vrbo_listing_number The Vrbo listing number to work on, when the listing is linked to more than one Vrbo unit. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['updateVrboListingTranslations'] to see the possible values for this operation
     *
     * @throws InvalidArgumentException
     * @return \GuzzleHttp\Psr7\Request
     */
    public function updateVrboListingTranslationsRequest(
        string $id,
        \Repull\Model\UpdateVrboListingTranslationsRequest $update_vrbo_listing_translations_request,
        ?string $vrbo_listing_number = null,
        string $contentType = self::contentTypes['updateVrboListingTranslations'][0]
    ): Request
    {

        // verify the required parameter 'id' is set
        if ($id === null || (is_array($id) && count($id) === 0)) {
            throw new InvalidArgumentException(
                'Missing the required parameter $id when calling updateVrboListingTranslations'
            );
        }

        // verify the required parameter 'update_vrbo_listing_translations_request' is set
        if ($update_vrbo_listing_translations_request === null || (is_array($update_vrbo_listing_translations_request) && count($update_vrbo_listing_translations_request) === 0)) {
            throw new InvalidArgumentException(
                'Missing the required parameter $update_vrbo_listing_translations_request when calling updateVrboListingTranslations'
            );
        }



        $resourcePath = '/v1/channels/vrbo/listings/{id}/translations';
        $formParams = [];
        $queryParams = [];
        $headerParams = [];
        $httpBody = '';
        $multipart = false;

        // query params
        $queryParams = array_merge($queryParams, ObjectSerializer::toQueryValue(
            $vrbo_listing_number,
            'vrboListingNumber', // param base name
            'string', // openApiType
            'form', // style
            true, // explode
            false, // required
            null // openApiFormat
        ) ?? []);


        // path params
        if ($id !== null) {
            $resourcePath = str_replace(
                '{id}',
                ObjectSerializer::toPathValue($id),
                $resourcePath
            );
        }


        $headers = $this->headerSelector->selectHeaders(
            ['application/json', ],
            $contentType,
            $multipart
        );

        // for model (json/xml)
        if (isset($update_vrbo_listing_translations_request)) {
            if (stripos($headers['Content-Type'], 'application/json') !== false) {
                # if Content-Type contains "application/json", json_encode the body
                $httpBody = \GuzzleHttp\Utils::jsonEncode(ObjectSerializer::sanitizeForSerialization($update_vrbo_listing_translations_request));
            } else {
                $httpBody = $update_vrbo_listing_translations_request;
            }
        } elseif (count($formParams) > 0) {
            if ($multipart) {
                $multipartContents = [];
                foreach ($formParams as $formParamName => $formParamValue) {
                    $formParamValueItems = is_array($formParamValue) ? $formParamValue : [$formParamValue];
                    foreach ($formParamValueItems as $formParamValueItem) {
                        $multipartContents[] = [
                            'name' => $formParamName,
                            'contents' => $formParamValueItem
                        ];
                    }
                }
                // for HTTP post (form)
                $httpBody = new MultipartStream($multipartContents);

            } elseif (stripos($headers['Content-Type'], 'application/json') !== false) {
                # if Content-Type contains "application/json", json_encode the form parameters
                $httpBody = \GuzzleHttp\Utils::jsonEncode($formParams);
            } else {
                // for HTTP post (form)
                $httpBody = ObjectSerializer::buildQuery($formParams);
            }
        }

        // this endpoint requires Bearer (API Key) authentication (access token)
        if (!empty($this->config->getAccessToken())) {
            $headers['Authorization'] = 'Bearer ' . $this->config->getAccessToken();
        }

        $defaultHeaders = [];
        if ($this->config->getUserAgent()) {
            $defaultHeaders['User-Agent'] = $this->config->getUserAgent();
        }

        $headers = array_merge(
            $defaultHeaders,
            $headerParams,
            $headers
        );

        $operationHost = $this->config->getHost();
        $query = ObjectSerializer::buildQuery($queryParams);
        return new Request(
            'PUT',
            $operationHost . $resourcePath . ($query ? "?{$query}" : ''),
            $headers,
            $httpBody
        );
    }

    /**
     * Operation updateVrboListingWelcomeGuide
     *
     * Update the Vrbo welcome guide
     *
     * @param  string $id Repull listing id (numeric string) of a listing linked to Vrbo. (required)
     * @param  \Repull\Model\UpdateVrboListingWelcomeGuideRequest $update_vrbo_listing_welcome_guide_request update_vrbo_listing_welcome_guide_request (required)
     * @param  string|null $vrbo_listing_number The Vrbo listing number to work on, when the listing is linked to more than one Vrbo unit. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['updateVrboListingWelcomeGuide'] to see the possible values for this operation
     *
     * @throws ApiException on non-2xx response or if the response body is not in the expected format
     * @throws InvalidArgumentException
     * @return \Repull\Model\UpdateVrboListingWelcomeGuide200Response|\Repull\Model\Error
     */
    public function updateVrboListingWelcomeGuide(
        string $id,
        \Repull\Model\UpdateVrboListingWelcomeGuideRequest $update_vrbo_listing_welcome_guide_request,
        ?string $vrbo_listing_number = null,
        string $contentType = self::contentTypes['updateVrboListingWelcomeGuide'][0]
    ): \Repull\Model\UpdateVrboListingWelcomeGuide200Response|\Repull\Model\Error
    {
        list($response) = $this->updateVrboListingWelcomeGuideWithHttpInfo($id, $update_vrbo_listing_welcome_guide_request, $vrbo_listing_number, $contentType);
        return $response;
    }

    /**
     * Operation updateVrboListingWelcomeGuideWithHttpInfo
     *
     * Update the Vrbo welcome guide
     *
     * @param  string $id Repull listing id (numeric string) of a listing linked to Vrbo. (required)
     * @param  \Repull\Model\UpdateVrboListingWelcomeGuideRequest $update_vrbo_listing_welcome_guide_request (required)
     * @param  string|null $vrbo_listing_number The Vrbo listing number to work on, when the listing is linked to more than one Vrbo unit. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['updateVrboListingWelcomeGuide'] to see the possible values for this operation
     *
     * @throws ApiException on non-2xx response or if the response body is not in the expected format
     * @throws InvalidArgumentException
     * @return array of \Repull\Model\UpdateVrboListingWelcomeGuide200Response|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error, HTTP status code, HTTP response headers (array of strings)
     */
    public function updateVrboListingWelcomeGuideWithHttpInfo(
        string $id,
        \Repull\Model\UpdateVrboListingWelcomeGuideRequest $update_vrbo_listing_welcome_guide_request,
        ?string $vrbo_listing_number = null,
        string $contentType = self::contentTypes['updateVrboListingWelcomeGuide'][0]
    ): array
    {
        $request = $this->updateVrboListingWelcomeGuideRequest($id, $update_vrbo_listing_welcome_guide_request, $vrbo_listing_number, $contentType);

        try {
            $options = $this->createHttpClientOption();
            try {
                $response = $this->client->send($request, $options);
            } catch (RequestException $e) {
                throw new ApiException(
                    "[{$e->getCode()}] {$e->getMessage()}",
                    (int) $e->getCode(),
                    $e->getResponse() ? $e->getResponse()->getHeaders() : null,
                    $e->getResponse() ? (string) $e->getResponse()->getBody() : null
                );
            } catch (ConnectException $e) {
                throw new ApiException(
                    "[{$e->getCode()}] {$e->getMessage()}",
                    (int) $e->getCode(),
                    null,
                    null
                );
            }

            $statusCode = $response->getStatusCode();

            switch($statusCode) {
                case 200:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\UpdateVrboListingWelcomeGuide200Response',
                        $request,
                        $response,
                    );
                case 401:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 403:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 404:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 409:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 422:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 500:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 502:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 503:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 504:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
            }
            

            if ($statusCode < 200 || $statusCode > 299) {
                throw new ApiException(
                    sprintf(
                        '[%d] Error connecting to the API (%s)',
                        $statusCode,
                        (string) $request->getUri()
                    ),
                    $statusCode,
                    $response->getHeaders(),
                    (string) $response->getBody()
                );
            }

            return $this->handleResponseWithDataType(
                '\Repull\Model\UpdateVrboListingWelcomeGuide200Response',
                $request,
                $response,
            );
        } catch (ApiException $e) {
            switch ($e->getCode()) {
                case 200:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\UpdateVrboListingWelcomeGuide200Response',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 401:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 403:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 404:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 409:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 422:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 500:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 502:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 503:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 504:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
            }
        
            throw $e;
        }
    }

    /**
     * Operation updateVrboListingWelcomeGuideAsync
     *
     * Update the Vrbo welcome guide
     *
     * @param  string $id Repull listing id (numeric string) of a listing linked to Vrbo. (required)
     * @param  \Repull\Model\UpdateVrboListingWelcomeGuideRequest $update_vrbo_listing_welcome_guide_request (required)
     * @param  string|null $vrbo_listing_number The Vrbo listing number to work on, when the listing is linked to more than one Vrbo unit. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['updateVrboListingWelcomeGuide'] to see the possible values for this operation
     *
     * @throws InvalidArgumentException
     * @return PromiseInterface
     */
    public function updateVrboListingWelcomeGuideAsync(
        string $id,
        \Repull\Model\UpdateVrboListingWelcomeGuideRequest $update_vrbo_listing_welcome_guide_request,
        ?string $vrbo_listing_number = null,
        string $contentType = self::contentTypes['updateVrboListingWelcomeGuide'][0]
    ): PromiseInterface
    {
        return $this->updateVrboListingWelcomeGuideAsyncWithHttpInfo($id, $update_vrbo_listing_welcome_guide_request, $vrbo_listing_number, $contentType)
            ->then(
                function ($response) {
                    return $response[0];
                }
            );
    }

    /**
     * Operation updateVrboListingWelcomeGuideAsyncWithHttpInfo
     *
     * Update the Vrbo welcome guide
     *
     * @param  string $id Repull listing id (numeric string) of a listing linked to Vrbo. (required)
     * @param  \Repull\Model\UpdateVrboListingWelcomeGuideRequest $update_vrbo_listing_welcome_guide_request (required)
     * @param  string|null $vrbo_listing_number The Vrbo listing number to work on, when the listing is linked to more than one Vrbo unit. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['updateVrboListingWelcomeGuide'] to see the possible values for this operation
     *
     * @throws InvalidArgumentException
     * @return PromiseInterface
     */
    public function updateVrboListingWelcomeGuideAsyncWithHttpInfo(
        string $id,
        \Repull\Model\UpdateVrboListingWelcomeGuideRequest $update_vrbo_listing_welcome_guide_request,
        ?string $vrbo_listing_number = null,
        string $contentType = self::contentTypes['updateVrboListingWelcomeGuide'][0]
    ): PromiseInterface
    {
        $returnType = '\Repull\Model\UpdateVrboListingWelcomeGuide200Response';
        $request = $this->updateVrboListingWelcomeGuideRequest($id, $update_vrbo_listing_welcome_guide_request, $vrbo_listing_number, $contentType);

        return $this->client
            ->sendAsync($request, $this->createHttpClientOption())
            ->then(
                function ($response) use ($returnType) {
                    if (in_array($returnType, ['\SplFileObject', '\Psr\Http\Message\StreamInterface'])) {
                        $content = $response->getBody(); //stream goes to serializer
                    } else {
                        $content = (string) $response->getBody();
                        if ($returnType !== 'string') {
                            $content = json_decode($content);
                        }
                    }

                    return [
                        ObjectSerializer::deserialize($content, $returnType, []),
                        $response->getStatusCode(),
                        $response->getHeaders()
                    ];
                },
                function ($exception) {
                    $response = $exception->getResponse();
                    $statusCode = $response->getStatusCode();
                    throw new ApiException(
                        sprintf(
                            '[%d] Error connecting to the API (%s)',
                            $statusCode,
                            $exception->getRequest()->getUri()
                        ),
                        $statusCode,
                        $response->getHeaders(),
                        (string) $response->getBody()
                    );
                }
            );
    }

    /**
     * Create request for operation 'updateVrboListingWelcomeGuide'
     *
     * @param  string $id Repull listing id (numeric string) of a listing linked to Vrbo. (required)
     * @param  \Repull\Model\UpdateVrboListingWelcomeGuideRequest $update_vrbo_listing_welcome_guide_request (required)
     * @param  string|null $vrbo_listing_number The Vrbo listing number to work on, when the listing is linked to more than one Vrbo unit. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['updateVrboListingWelcomeGuide'] to see the possible values for this operation
     *
     * @throws InvalidArgumentException
     * @return \GuzzleHttp\Psr7\Request
     */
    public function updateVrboListingWelcomeGuideRequest(
        string $id,
        \Repull\Model\UpdateVrboListingWelcomeGuideRequest $update_vrbo_listing_welcome_guide_request,
        ?string $vrbo_listing_number = null,
        string $contentType = self::contentTypes['updateVrboListingWelcomeGuide'][0]
    ): Request
    {

        // verify the required parameter 'id' is set
        if ($id === null || (is_array($id) && count($id) === 0)) {
            throw new InvalidArgumentException(
                'Missing the required parameter $id when calling updateVrboListingWelcomeGuide'
            );
        }

        // verify the required parameter 'update_vrbo_listing_welcome_guide_request' is set
        if ($update_vrbo_listing_welcome_guide_request === null || (is_array($update_vrbo_listing_welcome_guide_request) && count($update_vrbo_listing_welcome_guide_request) === 0)) {
            throw new InvalidArgumentException(
                'Missing the required parameter $update_vrbo_listing_welcome_guide_request when calling updateVrboListingWelcomeGuide'
            );
        }



        $resourcePath = '/v1/channels/vrbo/listings/{id}/welcome-guide';
        $formParams = [];
        $queryParams = [];
        $headerParams = [];
        $httpBody = '';
        $multipart = false;

        // query params
        $queryParams = array_merge($queryParams, ObjectSerializer::toQueryValue(
            $vrbo_listing_number,
            'vrboListingNumber', // param base name
            'string', // openApiType
            'form', // style
            true, // explode
            false, // required
            null // openApiFormat
        ) ?? []);


        // path params
        if ($id !== null) {
            $resourcePath = str_replace(
                '{id}',
                ObjectSerializer::toPathValue($id),
                $resourcePath
            );
        }


        $headers = $this->headerSelector->selectHeaders(
            ['application/json', ],
            $contentType,
            $multipart
        );

        // for model (json/xml)
        if (isset($update_vrbo_listing_welcome_guide_request)) {
            if (stripos($headers['Content-Type'], 'application/json') !== false) {
                # if Content-Type contains "application/json", json_encode the body
                $httpBody = \GuzzleHttp\Utils::jsonEncode(ObjectSerializer::sanitizeForSerialization($update_vrbo_listing_welcome_guide_request));
            } else {
                $httpBody = $update_vrbo_listing_welcome_guide_request;
            }
        } elseif (count($formParams) > 0) {
            if ($multipart) {
                $multipartContents = [];
                foreach ($formParams as $formParamName => $formParamValue) {
                    $formParamValueItems = is_array($formParamValue) ? $formParamValue : [$formParamValue];
                    foreach ($formParamValueItems as $formParamValueItem) {
                        $multipartContents[] = [
                            'name' => $formParamName,
                            'contents' => $formParamValueItem
                        ];
                    }
                }
                // for HTTP post (form)
                $httpBody = new MultipartStream($multipartContents);

            } elseif (stripos($headers['Content-Type'], 'application/json') !== false) {
                # if Content-Type contains "application/json", json_encode the form parameters
                $httpBody = \GuzzleHttp\Utils::jsonEncode($formParams);
            } else {
                // for HTTP post (form)
                $httpBody = ObjectSerializer::buildQuery($formParams);
            }
        }

        // this endpoint requires Bearer (API Key) authentication (access token)
        if (!empty($this->config->getAccessToken())) {
            $headers['Authorization'] = 'Bearer ' . $this->config->getAccessToken();
        }

        $defaultHeaders = [];
        if ($this->config->getUserAgent()) {
            $defaultHeaders['User-Agent'] = $this->config->getUserAgent();
        }

        $headers = array_merge(
            $defaultHeaders,
            $headerParams,
            $headers
        );

        $operationHost = $this->config->getHost();
        $query = ObjectSerializer::buildQuery($queryParams);
        return new Request(
            'PUT',
            $operationHost . $resourcePath . ($query ? "?{$query}" : ''),
            $headers,
            $httpBody
        );
    }

    /**
     * Create http client option
     *
     * @throws \RuntimeException on file opening failure
     * @return array of http client options
     */
    protected function createHttpClientOption(): array
    {
        $options = [];
        if ($this->config->getDebug()) {
            $options[RequestOptions::DEBUG] = fopen($this->config->getDebugFile(), 'a');
            if (!$options[RequestOptions::DEBUG]) {
                throw new \RuntimeException('Failed to open the debug file: ' . $this->config->getDebugFile());
            }
        }

        return $options;
    }

    private function handleResponseWithDataType(
        string $dataType,
        RequestInterface $request,
        ResponseInterface $response,
    ): array {
        if (in_array($dataType, ['\SplFileObject', '\Psr\Http\Message\StreamInterface'])) {
            $content = $response->getBody(); //stream goes to serializer
        } else {
            $content = (string) $response->getBody();
            if ($dataType !== 'string') {
                try {
                    $content = json_decode($content, false, 512, JSON_THROW_ON_ERROR);
                } catch (\JsonException $exception) {
                    throw new ApiException(
                        sprintf(
                            'Error JSON decoding server response (%s)',
                            $request->getUri()
                        ),
                        $response->getStatusCode(),
                        $response->getHeaders(),
                        $content
                    );
                }
            }
        }

        return [
            ObjectSerializer::deserialize($content, $dataType, []),
            $response->getStatusCode(),
            $response->getHeaders()
        ];
    }

    private function responseWithinRangeCode(
        string $rangeCode,
        int $statusCode,
    ): bool {
        $left = (int) ($rangeCode[0].'00');
        $right = (int) ($rangeCode[0].'99');

        return $statusCode >= $left && $statusCode <= $right;
    }
}
