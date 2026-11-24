<?php
/**
 * TiketApi
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
 * TiketApi Class Doc Comment
 *
 * @package  Repull
 * @author   OpenAPI Generator team
 * @link     https://openapi-generator.tech
 */
class TiketApi
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
        'getTiketAvailability' => [
            'application/json',
        ],
        'getTiketContent' => [
            'application/json',
        ],
        'getTiketListingPricing' => [
            'application/json',
        ],
        'getTiketProperty' => [
            'application/json',
        ],
        'listTiketProperties' => [
            'application/json',
        ],
        'listTiketPropertyRooms' => [
            'application/json',
        ],
        'listTiketReservations' => [
            'application/json',
        ],
        'mapTiketListings' => [
            'application/json',
        ],
        'syncTiket' => [
            'application/json',
        ],
        'tiketSetup' => [
            'application/json',
        ],
        'updateTiketAvailability' => [
            'application/json',
        ],
        'updateTiketListingPricing' => [
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
     * Operation getTiketAvailability
     *
     * Read tiket.com rates, availability and restrictions
     *
     * @param  string $property_id tiket.com Hotel ID (shown next to the property name in the tiket.com extranet; &#x60;propertyId&#x60; on &#x60;GET /v1/channels/tiket/properties&#x60;). (required)
     * @param  \DateTime|null $start_date First night, YYYY-MM-DD. Default today. (optional)
     * @param  \DateTime|null $end_date Last night, YYYY-MM-DD, inclusive. Default &#x60;start_date&#x60; + &#x60;number_of_days&#x60; - 1. (optional)
     * @param  int|null $number_of_days Window length when &#x60;end_date&#x60; is omitted. (optional, default to 31)
     * @param  string|null $room_type_code Only this tiket.com room type. (optional)
     * @param  string|null $rate_plan_code Only this tiket.com rate plan. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['getTiketAvailability'] to see the possible values for this operation
     *
     * @throws ApiException on non-2xx response or if the response body is not in the expected format
     * @throws InvalidArgumentException
     * @return \Repull\Model\TiketAvailabilityResponse|\Repull\Model\Error
     */
    public function getTiketAvailability(
        string $property_id,
        ?\DateTime $start_date = null,
        ?\DateTime $end_date = null,
        ?int $number_of_days = 31,
        ?string $room_type_code = null,
        ?string $rate_plan_code = null,
        string $contentType = self::contentTypes['getTiketAvailability'][0]
    ): \Repull\Model\TiketAvailabilityResponse|\Repull\Model\Error
    {
        list($response) = $this->getTiketAvailabilityWithHttpInfo($property_id, $start_date, $end_date, $number_of_days, $room_type_code, $rate_plan_code, $contentType);
        return $response;
    }

    /**
     * Operation getTiketAvailabilityWithHttpInfo
     *
     * Read tiket.com rates, availability and restrictions
     *
     * @param  string $property_id tiket.com Hotel ID (shown next to the property name in the tiket.com extranet; &#x60;propertyId&#x60; on &#x60;GET /v1/channels/tiket/properties&#x60;). (required)
     * @param  \DateTime|null $start_date First night, YYYY-MM-DD. Default today. (optional)
     * @param  \DateTime|null $end_date Last night, YYYY-MM-DD, inclusive. Default &#x60;start_date&#x60; + &#x60;number_of_days&#x60; - 1. (optional)
     * @param  int|null $number_of_days Window length when &#x60;end_date&#x60; is omitted. (optional, default to 31)
     * @param  string|null $room_type_code Only this tiket.com room type. (optional)
     * @param  string|null $rate_plan_code Only this tiket.com rate plan. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['getTiketAvailability'] to see the possible values for this operation
     *
     * @throws ApiException on non-2xx response or if the response body is not in the expected format
     * @throws InvalidArgumentException
     * @return array of \Repull\Model\TiketAvailabilityResponse|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error, HTTP status code, HTTP response headers (array of strings)
     */
    public function getTiketAvailabilityWithHttpInfo(
        string $property_id,
        ?\DateTime $start_date = null,
        ?\DateTime $end_date = null,
        ?int $number_of_days = 31,
        ?string $room_type_code = null,
        ?string $rate_plan_code = null,
        string $contentType = self::contentTypes['getTiketAvailability'][0]
    ): array
    {
        $request = $this->getTiketAvailabilityRequest($property_id, $start_date, $end_date, $number_of_days, $room_type_code, $rate_plan_code, $contentType);

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
                        '\Repull\Model\TiketAvailabilityResponse',
                        $request,
                        $response,
                    );
                case 400:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
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
                '\Repull\Model\TiketAvailabilityResponse',
                $request,
                $response,
            );
        } catch (ApiException $e) {
            switch ($e->getCode()) {
                case 200:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\TiketAvailabilityResponse',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 400:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
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
            }
        
            throw $e;
        }
    }

    /**
     * Operation getTiketAvailabilityAsync
     *
     * Read tiket.com rates, availability and restrictions
     *
     * @param  string $property_id tiket.com Hotel ID (shown next to the property name in the tiket.com extranet; &#x60;propertyId&#x60; on &#x60;GET /v1/channels/tiket/properties&#x60;). (required)
     * @param  \DateTime|null $start_date First night, YYYY-MM-DD. Default today. (optional)
     * @param  \DateTime|null $end_date Last night, YYYY-MM-DD, inclusive. Default &#x60;start_date&#x60; + &#x60;number_of_days&#x60; - 1. (optional)
     * @param  int|null $number_of_days Window length when &#x60;end_date&#x60; is omitted. (optional, default to 31)
     * @param  string|null $room_type_code Only this tiket.com room type. (optional)
     * @param  string|null $rate_plan_code Only this tiket.com rate plan. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['getTiketAvailability'] to see the possible values for this operation
     *
     * @throws InvalidArgumentException
     * @return PromiseInterface
     */
    public function getTiketAvailabilityAsync(
        string $property_id,
        ?\DateTime $start_date = null,
        ?\DateTime $end_date = null,
        ?int $number_of_days = 31,
        ?string $room_type_code = null,
        ?string $rate_plan_code = null,
        string $contentType = self::contentTypes['getTiketAvailability'][0]
    ): PromiseInterface
    {
        return $this->getTiketAvailabilityAsyncWithHttpInfo($property_id, $start_date, $end_date, $number_of_days, $room_type_code, $rate_plan_code, $contentType)
            ->then(
                function ($response) {
                    return $response[0];
                }
            );
    }

    /**
     * Operation getTiketAvailabilityAsyncWithHttpInfo
     *
     * Read tiket.com rates, availability and restrictions
     *
     * @param  string $property_id tiket.com Hotel ID (shown next to the property name in the tiket.com extranet; &#x60;propertyId&#x60; on &#x60;GET /v1/channels/tiket/properties&#x60;). (required)
     * @param  \DateTime|null $start_date First night, YYYY-MM-DD. Default today. (optional)
     * @param  \DateTime|null $end_date Last night, YYYY-MM-DD, inclusive. Default &#x60;start_date&#x60; + &#x60;number_of_days&#x60; - 1. (optional)
     * @param  int|null $number_of_days Window length when &#x60;end_date&#x60; is omitted. (optional, default to 31)
     * @param  string|null $room_type_code Only this tiket.com room type. (optional)
     * @param  string|null $rate_plan_code Only this tiket.com rate plan. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['getTiketAvailability'] to see the possible values for this operation
     *
     * @throws InvalidArgumentException
     * @return PromiseInterface
     */
    public function getTiketAvailabilityAsyncWithHttpInfo(
        string $property_id,
        ?\DateTime $start_date = null,
        ?\DateTime $end_date = null,
        ?int $number_of_days = 31,
        ?string $room_type_code = null,
        ?string $rate_plan_code = null,
        string $contentType = self::contentTypes['getTiketAvailability'][0]
    ): PromiseInterface
    {
        $returnType = '\Repull\Model\TiketAvailabilityResponse';
        $request = $this->getTiketAvailabilityRequest($property_id, $start_date, $end_date, $number_of_days, $room_type_code, $rate_plan_code, $contentType);

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
     * Create request for operation 'getTiketAvailability'
     *
     * @param  string $property_id tiket.com Hotel ID (shown next to the property name in the tiket.com extranet; &#x60;propertyId&#x60; on &#x60;GET /v1/channels/tiket/properties&#x60;). (required)
     * @param  \DateTime|null $start_date First night, YYYY-MM-DD. Default today. (optional)
     * @param  \DateTime|null $end_date Last night, YYYY-MM-DD, inclusive. Default &#x60;start_date&#x60; + &#x60;number_of_days&#x60; - 1. (optional)
     * @param  int|null $number_of_days Window length when &#x60;end_date&#x60; is omitted. (optional, default to 31)
     * @param  string|null $room_type_code Only this tiket.com room type. (optional)
     * @param  string|null $rate_plan_code Only this tiket.com rate plan. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['getTiketAvailability'] to see the possible values for this operation
     *
     * @throws InvalidArgumentException
     * @return \GuzzleHttp\Psr7\Request
     */
    public function getTiketAvailabilityRequest(
        string $property_id,
        ?\DateTime $start_date = null,
        ?\DateTime $end_date = null,
        ?int $number_of_days = 31,
        ?string $room_type_code = null,
        ?string $rate_plan_code = null,
        string $contentType = self::contentTypes['getTiketAvailability'][0]
    ): Request
    {

        // verify the required parameter 'property_id' is set
        if ($property_id === null || (is_array($property_id) && count($property_id) === 0)) {
            throw new InvalidArgumentException(
                'Missing the required parameter $property_id when calling getTiketAvailability'
            );
        }



        if ($number_of_days !== null && $number_of_days > 366) {
            throw new InvalidArgumentException('invalid value for "$number_of_days" when calling TiketApi.getTiketAvailability, must be smaller than or equal to 366.');
        }
        if ($number_of_days !== null && $number_of_days < 1) {
            throw new InvalidArgumentException('invalid value for "$number_of_days" when calling TiketApi.getTiketAvailability, must be bigger than or equal to 1.');
        }
        



        $resourcePath = '/v1/channels/tiket/availability';
        $formParams = [];
        $queryParams = [];
        $headerParams = [];
        $httpBody = '';
        $multipart = false;

        // query params
        $queryParams = array_merge($queryParams, ObjectSerializer::toQueryValue(
            $property_id,
            'property_id', // param base name
            'string', // openApiType
            'form', // style
            true, // explode
            true, // required
            null // openApiFormat
        ) ?? []);
        // query params
        $queryParams = array_merge($queryParams, ObjectSerializer::toQueryValue(
            $start_date,
            'start_date', // param base name
            'string', // openApiType
            'form', // style
            true, // explode
            false, // required
            'date' // openApiFormat
        ) ?? []);
        // query params
        $queryParams = array_merge($queryParams, ObjectSerializer::toQueryValue(
            $end_date,
            'end_date', // param base name
            'string', // openApiType
            'form', // style
            true, // explode
            false, // required
            'date' // openApiFormat
        ) ?? []);
        // query params
        $queryParams = array_merge($queryParams, ObjectSerializer::toQueryValue(
            $number_of_days,
            'number_of_days', // param base name
            'integer', // openApiType
            'form', // style
            true, // explode
            false, // required
            null // openApiFormat
        ) ?? []);
        // query params
        $queryParams = array_merge($queryParams, ObjectSerializer::toQueryValue(
            $room_type_code,
            'room_type_code', // param base name
            'string', // openApiType
            'form', // style
            true, // explode
            false, // required
            null // openApiFormat
        ) ?? []);
        // query params
        $queryParams = array_merge($queryParams, ObjectSerializer::toQueryValue(
            $rate_plan_code,
            'rate_plan_code', // param base name
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
     * Operation getTiketContent
     *
     * Read the content Repull sent tiket.com
     *
     * @param  string|null $property_id tiket.com Hotel ID (shown next to the property name in the tiket.com extranet; &#x60;propertyId&#x60; on &#x60;GET /v1/channels/tiket/properties&#x60;). Required unless &#x60;partner_hotel_code&#x60; is sent. (optional)
     * @param  string|null $partner_hotel_code Repull&#39;s code for a property Repull created (&#x60;partnerHotelCode&#x60;). (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['getTiketContent'] to see the possible values for this operation
     *
     * @throws ApiException on non-2xx response or if the response body is not in the expected format
     * @throws InvalidArgumentException
     * @return \Repull\Model\TiketContentResponse|\Repull\Model\Error
     */
    public function getTiketContent(
        ?string $property_id = null,
        ?string $partner_hotel_code = null,
        string $contentType = self::contentTypes['getTiketContent'][0]
    ): \Repull\Model\TiketContentResponse|\Repull\Model\Error
    {
        list($response) = $this->getTiketContentWithHttpInfo($property_id, $partner_hotel_code, $contentType);
        return $response;
    }

    /**
     * Operation getTiketContentWithHttpInfo
     *
     * Read the content Repull sent tiket.com
     *
     * @param  string|null $property_id tiket.com Hotel ID (shown next to the property name in the tiket.com extranet; &#x60;propertyId&#x60; on &#x60;GET /v1/channels/tiket/properties&#x60;). Required unless &#x60;partner_hotel_code&#x60; is sent. (optional)
     * @param  string|null $partner_hotel_code Repull&#39;s code for a property Repull created (&#x60;partnerHotelCode&#x60;). (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['getTiketContent'] to see the possible values for this operation
     *
     * @throws ApiException on non-2xx response or if the response body is not in the expected format
     * @throws InvalidArgumentException
     * @return array of \Repull\Model\TiketContentResponse|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error, HTTP status code, HTTP response headers (array of strings)
     */
    public function getTiketContentWithHttpInfo(
        ?string $property_id = null,
        ?string $partner_hotel_code = null,
        string $contentType = self::contentTypes['getTiketContent'][0]
    ): array
    {
        $request = $this->getTiketContentRequest($property_id, $partner_hotel_code, $contentType);

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
                        '\Repull\Model\TiketContentResponse',
                        $request,
                        $response,
                    );
                case 400:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
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
                '\Repull\Model\TiketContentResponse',
                $request,
                $response,
            );
        } catch (ApiException $e) {
            switch ($e->getCode()) {
                case 200:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\TiketContentResponse',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 400:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
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
            }
        
            throw $e;
        }
    }

    /**
     * Operation getTiketContentAsync
     *
     * Read the content Repull sent tiket.com
     *
     * @param  string|null $property_id tiket.com Hotel ID (shown next to the property name in the tiket.com extranet; &#x60;propertyId&#x60; on &#x60;GET /v1/channels/tiket/properties&#x60;). Required unless &#x60;partner_hotel_code&#x60; is sent. (optional)
     * @param  string|null $partner_hotel_code Repull&#39;s code for a property Repull created (&#x60;partnerHotelCode&#x60;). (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['getTiketContent'] to see the possible values for this operation
     *
     * @throws InvalidArgumentException
     * @return PromiseInterface
     */
    public function getTiketContentAsync(
        ?string $property_id = null,
        ?string $partner_hotel_code = null,
        string $contentType = self::contentTypes['getTiketContent'][0]
    ): PromiseInterface
    {
        return $this->getTiketContentAsyncWithHttpInfo($property_id, $partner_hotel_code, $contentType)
            ->then(
                function ($response) {
                    return $response[0];
                }
            );
    }

    /**
     * Operation getTiketContentAsyncWithHttpInfo
     *
     * Read the content Repull sent tiket.com
     *
     * @param  string|null $property_id tiket.com Hotel ID (shown next to the property name in the tiket.com extranet; &#x60;propertyId&#x60; on &#x60;GET /v1/channels/tiket/properties&#x60;). Required unless &#x60;partner_hotel_code&#x60; is sent. (optional)
     * @param  string|null $partner_hotel_code Repull&#39;s code for a property Repull created (&#x60;partnerHotelCode&#x60;). (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['getTiketContent'] to see the possible values for this operation
     *
     * @throws InvalidArgumentException
     * @return PromiseInterface
     */
    public function getTiketContentAsyncWithHttpInfo(
        ?string $property_id = null,
        ?string $partner_hotel_code = null,
        string $contentType = self::contentTypes['getTiketContent'][0]
    ): PromiseInterface
    {
        $returnType = '\Repull\Model\TiketContentResponse';
        $request = $this->getTiketContentRequest($property_id, $partner_hotel_code, $contentType);

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
     * Create request for operation 'getTiketContent'
     *
     * @param  string|null $property_id tiket.com Hotel ID (shown next to the property name in the tiket.com extranet; &#x60;propertyId&#x60; on &#x60;GET /v1/channels/tiket/properties&#x60;). Required unless &#x60;partner_hotel_code&#x60; is sent. (optional)
     * @param  string|null $partner_hotel_code Repull&#39;s code for a property Repull created (&#x60;partnerHotelCode&#x60;). (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['getTiketContent'] to see the possible values for this operation
     *
     * @throws InvalidArgumentException
     * @return \GuzzleHttp\Psr7\Request
     */
    public function getTiketContentRequest(
        ?string $property_id = null,
        ?string $partner_hotel_code = null,
        string $contentType = self::contentTypes['getTiketContent'][0]
    ): Request
    {




        $resourcePath = '/v1/channels/tiket/content';
        $formParams = [];
        $queryParams = [];
        $headerParams = [];
        $httpBody = '';
        $multipart = false;

        // query params
        $queryParams = array_merge($queryParams, ObjectSerializer::toQueryValue(
            $property_id,
            'property_id', // param base name
            'string', // openApiType
            'form', // style
            true, // explode
            false, // required
            null // openApiFormat
        ) ?? []);
        // query params
        $queryParams = array_merge($queryParams, ObjectSerializer::toQueryValue(
            $partner_hotel_code,
            'partner_hotel_code', // param base name
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
     * Operation getTiketListingPricing
     *
     * Read tiket.com rates, availability and restrictions for a listing
     *
     * @param  int $id Repull listing id. (required)
     * @param  string|null $hotel_id tiket.com Hotel ID — only needed when the listing is mapped on more than one tiket.com property. (optional)
     * @param  \DateTime|null $start_date First night, YYYY-MM-DD. Default today. (optional)
     * @param  \DateTime|null $end_date Last night, YYYY-MM-DD, inclusive. (optional)
     * @param  int|null $number_of_days Window length when &#x60;end_date&#x60; is omitted. (optional, default to 31)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['getTiketListingPricing'] to see the possible values for this operation
     *
     * @throws ApiException on non-2xx response or if the response body is not in the expected format
     * @throws InvalidArgumentException
     * @return \Repull\Model\TiketListingPricingResponse|\Repull\Model\Error
     */
    public function getTiketListingPricing(
        int $id,
        ?string $hotel_id = null,
        ?\DateTime $start_date = null,
        ?\DateTime $end_date = null,
        ?int $number_of_days = 31,
        string $contentType = self::contentTypes['getTiketListingPricing'][0]
    ): \Repull\Model\TiketListingPricingResponse|\Repull\Model\Error
    {
        list($response) = $this->getTiketListingPricingWithHttpInfo($id, $hotel_id, $start_date, $end_date, $number_of_days, $contentType);
        return $response;
    }

    /**
     * Operation getTiketListingPricingWithHttpInfo
     *
     * Read tiket.com rates, availability and restrictions for a listing
     *
     * @param  int $id Repull listing id. (required)
     * @param  string|null $hotel_id tiket.com Hotel ID — only needed when the listing is mapped on more than one tiket.com property. (optional)
     * @param  \DateTime|null $start_date First night, YYYY-MM-DD. Default today. (optional)
     * @param  \DateTime|null $end_date Last night, YYYY-MM-DD, inclusive. (optional)
     * @param  int|null $number_of_days Window length when &#x60;end_date&#x60; is omitted. (optional, default to 31)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['getTiketListingPricing'] to see the possible values for this operation
     *
     * @throws ApiException on non-2xx response or if the response body is not in the expected format
     * @throws InvalidArgumentException
     * @return array of \Repull\Model\TiketListingPricingResponse|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error, HTTP status code, HTTP response headers (array of strings)
     */
    public function getTiketListingPricingWithHttpInfo(
        int $id,
        ?string $hotel_id = null,
        ?\DateTime $start_date = null,
        ?\DateTime $end_date = null,
        ?int $number_of_days = 31,
        string $contentType = self::contentTypes['getTiketListingPricing'][0]
    ): array
    {
        $request = $this->getTiketListingPricingRequest($id, $hotel_id, $start_date, $end_date, $number_of_days, $contentType);

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
                        '\Repull\Model\TiketListingPricingResponse',
                        $request,
                        $response,
                    );
                case 400:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
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
                '\Repull\Model\TiketListingPricingResponse',
                $request,
                $response,
            );
        } catch (ApiException $e) {
            switch ($e->getCode()) {
                case 200:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\TiketListingPricingResponse',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 400:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
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
            }
        
            throw $e;
        }
    }

    /**
     * Operation getTiketListingPricingAsync
     *
     * Read tiket.com rates, availability and restrictions for a listing
     *
     * @param  int $id Repull listing id. (required)
     * @param  string|null $hotel_id tiket.com Hotel ID — only needed when the listing is mapped on more than one tiket.com property. (optional)
     * @param  \DateTime|null $start_date First night, YYYY-MM-DD. Default today. (optional)
     * @param  \DateTime|null $end_date Last night, YYYY-MM-DD, inclusive. (optional)
     * @param  int|null $number_of_days Window length when &#x60;end_date&#x60; is omitted. (optional, default to 31)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['getTiketListingPricing'] to see the possible values for this operation
     *
     * @throws InvalidArgumentException
     * @return PromiseInterface
     */
    public function getTiketListingPricingAsync(
        int $id,
        ?string $hotel_id = null,
        ?\DateTime $start_date = null,
        ?\DateTime $end_date = null,
        ?int $number_of_days = 31,
        string $contentType = self::contentTypes['getTiketListingPricing'][0]
    ): PromiseInterface
    {
        return $this->getTiketListingPricingAsyncWithHttpInfo($id, $hotel_id, $start_date, $end_date, $number_of_days, $contentType)
            ->then(
                function ($response) {
                    return $response[0];
                }
            );
    }

    /**
     * Operation getTiketListingPricingAsyncWithHttpInfo
     *
     * Read tiket.com rates, availability and restrictions for a listing
     *
     * @param  int $id Repull listing id. (required)
     * @param  string|null $hotel_id tiket.com Hotel ID — only needed when the listing is mapped on more than one tiket.com property. (optional)
     * @param  \DateTime|null $start_date First night, YYYY-MM-DD. Default today. (optional)
     * @param  \DateTime|null $end_date Last night, YYYY-MM-DD, inclusive. (optional)
     * @param  int|null $number_of_days Window length when &#x60;end_date&#x60; is omitted. (optional, default to 31)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['getTiketListingPricing'] to see the possible values for this operation
     *
     * @throws InvalidArgumentException
     * @return PromiseInterface
     */
    public function getTiketListingPricingAsyncWithHttpInfo(
        int $id,
        ?string $hotel_id = null,
        ?\DateTime $start_date = null,
        ?\DateTime $end_date = null,
        ?int $number_of_days = 31,
        string $contentType = self::contentTypes['getTiketListingPricing'][0]
    ): PromiseInterface
    {
        $returnType = '\Repull\Model\TiketListingPricingResponse';
        $request = $this->getTiketListingPricingRequest($id, $hotel_id, $start_date, $end_date, $number_of_days, $contentType);

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
     * Create request for operation 'getTiketListingPricing'
     *
     * @param  int $id Repull listing id. (required)
     * @param  string|null $hotel_id tiket.com Hotel ID — only needed when the listing is mapped on more than one tiket.com property. (optional)
     * @param  \DateTime|null $start_date First night, YYYY-MM-DD. Default today. (optional)
     * @param  \DateTime|null $end_date Last night, YYYY-MM-DD, inclusive. (optional)
     * @param  int|null $number_of_days Window length when &#x60;end_date&#x60; is omitted. (optional, default to 31)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['getTiketListingPricing'] to see the possible values for this operation
     *
     * @throws InvalidArgumentException
     * @return \GuzzleHttp\Psr7\Request
     */
    public function getTiketListingPricingRequest(
        int $id,
        ?string $hotel_id = null,
        ?\DateTime $start_date = null,
        ?\DateTime $end_date = null,
        ?int $number_of_days = 31,
        string $contentType = self::contentTypes['getTiketListingPricing'][0]
    ): Request
    {

        // verify the required parameter 'id' is set
        if ($id === null || (is_array($id) && count($id) === 0)) {
            throw new InvalidArgumentException(
                'Missing the required parameter $id when calling getTiketListingPricing'
            );
        }




        if ($number_of_days !== null && $number_of_days > 366) {
            throw new InvalidArgumentException('invalid value for "$number_of_days" when calling TiketApi.getTiketListingPricing, must be smaller than or equal to 366.');
        }
        if ($number_of_days !== null && $number_of_days < 1) {
            throw new InvalidArgumentException('invalid value for "$number_of_days" when calling TiketApi.getTiketListingPricing, must be bigger than or equal to 1.');
        }
        

        $resourcePath = '/v1/channels/tiket/listings/{id}/pricing';
        $formParams = [];
        $queryParams = [];
        $headerParams = [];
        $httpBody = '';
        $multipart = false;

        // query params
        $queryParams = array_merge($queryParams, ObjectSerializer::toQueryValue(
            $hotel_id,
            'hotel_id', // param base name
            'string', // openApiType
            'form', // style
            true, // explode
            false, // required
            null // openApiFormat
        ) ?? []);
        // query params
        $queryParams = array_merge($queryParams, ObjectSerializer::toQueryValue(
            $start_date,
            'start_date', // param base name
            'string', // openApiType
            'form', // style
            true, // explode
            false, // required
            'date' // openApiFormat
        ) ?? []);
        // query params
        $queryParams = array_merge($queryParams, ObjectSerializer::toQueryValue(
            $end_date,
            'end_date', // param base name
            'string', // openApiType
            'form', // style
            true, // explode
            false, // required
            'date' // openApiFormat
        ) ?? []);
        // query params
        $queryParams = array_merge($queryParams, ObjectSerializer::toQueryValue(
            $number_of_days,
            'number_of_days', // param base name
            'integer', // openApiType
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
     * Operation getTiketProperty
     *
     * Get a tiket.com property
     *
     * @param  string $id tiket.com Hotel ID. (required)
     * @param  string|null $status &#x60;active&#x60; (default) hides inactive listings: they are left out of &#x60;listings&#x60;, and a room mapped to one comes back with &#x60;listingId: null&#x60; and &#x60;listingHidden: true&#x60;. &#x60;inactive&#x60; shows only inactive listings and &#x60;all&#x60; shows both. &#x60;mappingStatus&#x60; always counts every mapped room. (optional, default to 'active')
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['getTiketProperty'] to see the possible values for this operation
     *
     * @throws ApiException on non-2xx response or if the response body is not in the expected format
     * @throws InvalidArgumentException
     * @return \Repull\Model\TiketProperty|\Repull\Model\Error
     */
    public function getTiketProperty(
        string $id,
        ?string $status = 'active',
        string $contentType = self::contentTypes['getTiketProperty'][0]
    ): \Repull\Model\TiketProperty|\Repull\Model\Error
    {
        list($response) = $this->getTiketPropertyWithHttpInfo($id, $status, $contentType);
        return $response;
    }

    /**
     * Operation getTiketPropertyWithHttpInfo
     *
     * Get a tiket.com property
     *
     * @param  string $id tiket.com Hotel ID. (required)
     * @param  string|null $status &#x60;active&#x60; (default) hides inactive listings: they are left out of &#x60;listings&#x60;, and a room mapped to one comes back with &#x60;listingId: null&#x60; and &#x60;listingHidden: true&#x60;. &#x60;inactive&#x60; shows only inactive listings and &#x60;all&#x60; shows both. &#x60;mappingStatus&#x60; always counts every mapped room. (optional, default to 'active')
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['getTiketProperty'] to see the possible values for this operation
     *
     * @throws ApiException on non-2xx response or if the response body is not in the expected format
     * @throws InvalidArgumentException
     * @return array of \Repull\Model\TiketProperty|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error, HTTP status code, HTTP response headers (array of strings)
     */
    public function getTiketPropertyWithHttpInfo(
        string $id,
        ?string $status = 'active',
        string $contentType = self::contentTypes['getTiketProperty'][0]
    ): array
    {
        $request = $this->getTiketPropertyRequest($id, $status, $contentType);

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
                        '\Repull\Model\TiketProperty',
                        $request,
                        $response,
                    );
                case 400:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 401:
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
                '\Repull\Model\TiketProperty',
                $request,
                $response,
            );
        } catch (ApiException $e) {
            switch ($e->getCode()) {
                case 200:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\TiketProperty',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 400:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
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
                case 404:
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
     * Operation getTiketPropertyAsync
     *
     * Get a tiket.com property
     *
     * @param  string $id tiket.com Hotel ID. (required)
     * @param  string|null $status &#x60;active&#x60; (default) hides inactive listings: they are left out of &#x60;listings&#x60;, and a room mapped to one comes back with &#x60;listingId: null&#x60; and &#x60;listingHidden: true&#x60;. &#x60;inactive&#x60; shows only inactive listings and &#x60;all&#x60; shows both. &#x60;mappingStatus&#x60; always counts every mapped room. (optional, default to 'active')
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['getTiketProperty'] to see the possible values for this operation
     *
     * @throws InvalidArgumentException
     * @return PromiseInterface
     */
    public function getTiketPropertyAsync(
        string $id,
        ?string $status = 'active',
        string $contentType = self::contentTypes['getTiketProperty'][0]
    ): PromiseInterface
    {
        return $this->getTiketPropertyAsyncWithHttpInfo($id, $status, $contentType)
            ->then(
                function ($response) {
                    return $response[0];
                }
            );
    }

    /**
     * Operation getTiketPropertyAsyncWithHttpInfo
     *
     * Get a tiket.com property
     *
     * @param  string $id tiket.com Hotel ID. (required)
     * @param  string|null $status &#x60;active&#x60; (default) hides inactive listings: they are left out of &#x60;listings&#x60;, and a room mapped to one comes back with &#x60;listingId: null&#x60; and &#x60;listingHidden: true&#x60;. &#x60;inactive&#x60; shows only inactive listings and &#x60;all&#x60; shows both. &#x60;mappingStatus&#x60; always counts every mapped room. (optional, default to 'active')
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['getTiketProperty'] to see the possible values for this operation
     *
     * @throws InvalidArgumentException
     * @return PromiseInterface
     */
    public function getTiketPropertyAsyncWithHttpInfo(
        string $id,
        ?string $status = 'active',
        string $contentType = self::contentTypes['getTiketProperty'][0]
    ): PromiseInterface
    {
        $returnType = '\Repull\Model\TiketProperty';
        $request = $this->getTiketPropertyRequest($id, $status, $contentType);

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
     * Create request for operation 'getTiketProperty'
     *
     * @param  string $id tiket.com Hotel ID. (required)
     * @param  string|null $status &#x60;active&#x60; (default) hides inactive listings: they are left out of &#x60;listings&#x60;, and a room mapped to one comes back with &#x60;listingId: null&#x60; and &#x60;listingHidden: true&#x60;. &#x60;inactive&#x60; shows only inactive listings and &#x60;all&#x60; shows both. &#x60;mappingStatus&#x60; always counts every mapped room. (optional, default to 'active')
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['getTiketProperty'] to see the possible values for this operation
     *
     * @throws InvalidArgumentException
     * @return \GuzzleHttp\Psr7\Request
     */
    public function getTiketPropertyRequest(
        string $id,
        ?string $status = 'active',
        string $contentType = self::contentTypes['getTiketProperty'][0]
    ): Request
    {

        // verify the required parameter 'id' is set
        if ($id === null || (is_array($id) && count($id) === 0)) {
            throw new InvalidArgumentException(
                'Missing the required parameter $id when calling getTiketProperty'
            );
        }



        $resourcePath = '/v1/channels/tiket/properties/{id}';
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
     * Operation listTiketProperties
     *
     * List tiket.com properties
     *
     * @param  string|null $status &#x60;active&#x60; (default) hides inactive listings: they are left out of &#x60;listings&#x60;, and a room mapped to one comes back with &#x60;listingId: null&#x60; and &#x60;listingHidden: true&#x60;. &#x60;inactive&#x60; shows only inactive listings and &#x60;all&#x60; shows both. &#x60;mappingStatus&#x60; always counts every mapped room. (optional, default to 'active')
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['listTiketProperties'] to see the possible values for this operation
     *
     * @throws ApiException on non-2xx response or if the response body is not in the expected format
     * @throws InvalidArgumentException
     * @return \Repull\Model\TiketPropertyListResponse|\Repull\Model\Error
     */
    public function listTiketProperties(
        ?string $status = 'active',
        string $contentType = self::contentTypes['listTiketProperties'][0]
    ): \Repull\Model\TiketPropertyListResponse|\Repull\Model\Error
    {
        list($response) = $this->listTiketPropertiesWithHttpInfo($status, $contentType);
        return $response;
    }

    /**
     * Operation listTiketPropertiesWithHttpInfo
     *
     * List tiket.com properties
     *
     * @param  string|null $status &#x60;active&#x60; (default) hides inactive listings: they are left out of &#x60;listings&#x60;, and a room mapped to one comes back with &#x60;listingId: null&#x60; and &#x60;listingHidden: true&#x60;. &#x60;inactive&#x60; shows only inactive listings and &#x60;all&#x60; shows both. &#x60;mappingStatus&#x60; always counts every mapped room. (optional, default to 'active')
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['listTiketProperties'] to see the possible values for this operation
     *
     * @throws ApiException on non-2xx response or if the response body is not in the expected format
     * @throws InvalidArgumentException
     * @return array of \Repull\Model\TiketPropertyListResponse|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error, HTTP status code, HTTP response headers (array of strings)
     */
    public function listTiketPropertiesWithHttpInfo(
        ?string $status = 'active',
        string $contentType = self::contentTypes['listTiketProperties'][0]
    ): array
    {
        $request = $this->listTiketPropertiesRequest($status, $contentType);

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
                        '\Repull\Model\TiketPropertyListResponse',
                        $request,
                        $response,
                    );
                case 400:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
                        $request,
                        $response,
                    );
                case 401:
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
                '\Repull\Model\TiketPropertyListResponse',
                $request,
                $response,
            );
        } catch (ApiException $e) {
            switch ($e->getCode()) {
                case 200:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\TiketPropertyListResponse',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 400:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
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
                case 500:
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
     * Operation listTiketPropertiesAsync
     *
     * List tiket.com properties
     *
     * @param  string|null $status &#x60;active&#x60; (default) hides inactive listings: they are left out of &#x60;listings&#x60;, and a room mapped to one comes back with &#x60;listingId: null&#x60; and &#x60;listingHidden: true&#x60;. &#x60;inactive&#x60; shows only inactive listings and &#x60;all&#x60; shows both. &#x60;mappingStatus&#x60; always counts every mapped room. (optional, default to 'active')
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['listTiketProperties'] to see the possible values for this operation
     *
     * @throws InvalidArgumentException
     * @return PromiseInterface
     */
    public function listTiketPropertiesAsync(
        ?string $status = 'active',
        string $contentType = self::contentTypes['listTiketProperties'][0]
    ): PromiseInterface
    {
        return $this->listTiketPropertiesAsyncWithHttpInfo($status, $contentType)
            ->then(
                function ($response) {
                    return $response[0];
                }
            );
    }

    /**
     * Operation listTiketPropertiesAsyncWithHttpInfo
     *
     * List tiket.com properties
     *
     * @param  string|null $status &#x60;active&#x60; (default) hides inactive listings: they are left out of &#x60;listings&#x60;, and a room mapped to one comes back with &#x60;listingId: null&#x60; and &#x60;listingHidden: true&#x60;. &#x60;inactive&#x60; shows only inactive listings and &#x60;all&#x60; shows both. &#x60;mappingStatus&#x60; always counts every mapped room. (optional, default to 'active')
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['listTiketProperties'] to see the possible values for this operation
     *
     * @throws InvalidArgumentException
     * @return PromiseInterface
     */
    public function listTiketPropertiesAsyncWithHttpInfo(
        ?string $status = 'active',
        string $contentType = self::contentTypes['listTiketProperties'][0]
    ): PromiseInterface
    {
        $returnType = '\Repull\Model\TiketPropertyListResponse';
        $request = $this->listTiketPropertiesRequest($status, $contentType);

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
     * Create request for operation 'listTiketProperties'
     *
     * @param  string|null $status &#x60;active&#x60; (default) hides inactive listings: they are left out of &#x60;listings&#x60;, and a room mapped to one comes back with &#x60;listingId: null&#x60; and &#x60;listingHidden: true&#x60;. &#x60;inactive&#x60; shows only inactive listings and &#x60;all&#x60; shows both. &#x60;mappingStatus&#x60; always counts every mapped room. (optional, default to 'active')
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['listTiketProperties'] to see the possible values for this operation
     *
     * @throws InvalidArgumentException
     * @return \GuzzleHttp\Psr7\Request
     */
    public function listTiketPropertiesRequest(
        ?string $status = 'active',
        string $contentType = self::contentTypes['listTiketProperties'][0]
    ): Request
    {



        $resourcePath = '/v1/channels/tiket/properties';
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
     * Operation listTiketPropertyRooms
     *
     * List a tiket.com property&#39;s room types
     *
     * @param  string $id tiket.com Hotel ID. (required)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['listTiketPropertyRooms'] to see the possible values for this operation
     *
     * @throws ApiException on non-2xx response or if the response body is not in the expected format
     * @throws InvalidArgumentException
     * @return \Repull\Model\TiketPropertyRoomsResponse|\Repull\Model\Error
     */
    public function listTiketPropertyRooms(
        string $id,
        string $contentType = self::contentTypes['listTiketPropertyRooms'][0]
    ): \Repull\Model\TiketPropertyRoomsResponse|\Repull\Model\Error
    {
        list($response) = $this->listTiketPropertyRoomsWithHttpInfo($id, $contentType);
        return $response;
    }

    /**
     * Operation listTiketPropertyRoomsWithHttpInfo
     *
     * List a tiket.com property&#39;s room types
     *
     * @param  string $id tiket.com Hotel ID. (required)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['listTiketPropertyRooms'] to see the possible values for this operation
     *
     * @throws ApiException on non-2xx response or if the response body is not in the expected format
     * @throws InvalidArgumentException
     * @return array of \Repull\Model\TiketPropertyRoomsResponse|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error, HTTP status code, HTTP response headers (array of strings)
     */
    public function listTiketPropertyRoomsWithHttpInfo(
        string $id,
        string $contentType = self::contentTypes['listTiketPropertyRooms'][0]
    ): array
    {
        $request = $this->listTiketPropertyRoomsRequest($id, $contentType);

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
                        '\Repull\Model\TiketPropertyRoomsResponse',
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
                '\Repull\Model\TiketPropertyRoomsResponse',
                $request,
                $response,
            );
        } catch (ApiException $e) {
            switch ($e->getCode()) {
                case 200:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\TiketPropertyRoomsResponse',
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
            }
        
            throw $e;
        }
    }

    /**
     * Operation listTiketPropertyRoomsAsync
     *
     * List a tiket.com property&#39;s room types
     *
     * @param  string $id tiket.com Hotel ID. (required)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['listTiketPropertyRooms'] to see the possible values for this operation
     *
     * @throws InvalidArgumentException
     * @return PromiseInterface
     */
    public function listTiketPropertyRoomsAsync(
        string $id,
        string $contentType = self::contentTypes['listTiketPropertyRooms'][0]
    ): PromiseInterface
    {
        return $this->listTiketPropertyRoomsAsyncWithHttpInfo($id, $contentType)
            ->then(
                function ($response) {
                    return $response[0];
                }
            );
    }

    /**
     * Operation listTiketPropertyRoomsAsyncWithHttpInfo
     *
     * List a tiket.com property&#39;s room types
     *
     * @param  string $id tiket.com Hotel ID. (required)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['listTiketPropertyRooms'] to see the possible values for this operation
     *
     * @throws InvalidArgumentException
     * @return PromiseInterface
     */
    public function listTiketPropertyRoomsAsyncWithHttpInfo(
        string $id,
        string $contentType = self::contentTypes['listTiketPropertyRooms'][0]
    ): PromiseInterface
    {
        $returnType = '\Repull\Model\TiketPropertyRoomsResponse';
        $request = $this->listTiketPropertyRoomsRequest($id, $contentType);

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
     * Create request for operation 'listTiketPropertyRooms'
     *
     * @param  string $id tiket.com Hotel ID. (required)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['listTiketPropertyRooms'] to see the possible values for this operation
     *
     * @throws InvalidArgumentException
     * @return \GuzzleHttp\Psr7\Request
     */
    public function listTiketPropertyRoomsRequest(
        string $id,
        string $contentType = self::contentTypes['listTiketPropertyRooms'][0]
    ): Request
    {

        // verify the required parameter 'id' is set
        if ($id === null || (is_array($id) && count($id) === 0)) {
            throw new InvalidArgumentException(
                'Missing the required parameter $id when calling listTiketPropertyRooms'
            );
        }


        $resourcePath = '/v1/channels/tiket/properties/{id}/rooms';
        $formParams = [];
        $queryParams = [];
        $headerParams = [];
        $httpBody = '';
        $multipart = false;



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
     * Operation listTiketReservations
     *
     * List tiket.com reservations as tiket.com sent them
     *
     * @param  string|null $property_id tiket.com Hotel ID (shown next to the property name in the tiket.com extranet; &#x60;propertyId&#x60; on &#x60;GET /v1/channels/tiket/properties&#x60;). (optional)
     * @param  string|null $status Only &#x60;confirmed&#x60; or only &#x60;cancelled&#x60; bookings. (optional)
     * @param  \DateTime|null $check_in_from Check-in on or after, YYYY-MM-DD. (optional)
     * @param  \DateTime|null $check_in_to Check-in on or before, YYYY-MM-DD. (optional)
     * @param  int|null $limit Page size. (optional, default to 50)
     * @param  int|null $offset Rows to skip. (optional, default to 0)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['listTiketReservations'] to see the possible values for this operation
     *
     * @throws ApiException on non-2xx response or if the response body is not in the expected format
     * @throws InvalidArgumentException
     * @return \Repull\Model\TiketReservationListResponse|\Repull\Model\Error
     */
    public function listTiketReservations(
        ?string $property_id = null,
        ?string $status = null,
        ?\DateTime $check_in_from = null,
        ?\DateTime $check_in_to = null,
        ?int $limit = 50,
        ?int $offset = 0,
        string $contentType = self::contentTypes['listTiketReservations'][0]
    ): \Repull\Model\TiketReservationListResponse|\Repull\Model\Error
    {
        list($response) = $this->listTiketReservationsWithHttpInfo($property_id, $status, $check_in_from, $check_in_to, $limit, $offset, $contentType);
        return $response;
    }

    /**
     * Operation listTiketReservationsWithHttpInfo
     *
     * List tiket.com reservations as tiket.com sent them
     *
     * @param  string|null $property_id tiket.com Hotel ID (shown next to the property name in the tiket.com extranet; &#x60;propertyId&#x60; on &#x60;GET /v1/channels/tiket/properties&#x60;). (optional)
     * @param  string|null $status Only &#x60;confirmed&#x60; or only &#x60;cancelled&#x60; bookings. (optional)
     * @param  \DateTime|null $check_in_from Check-in on or after, YYYY-MM-DD. (optional)
     * @param  \DateTime|null $check_in_to Check-in on or before, YYYY-MM-DD. (optional)
     * @param  int|null $limit Page size. (optional, default to 50)
     * @param  int|null $offset Rows to skip. (optional, default to 0)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['listTiketReservations'] to see the possible values for this operation
     *
     * @throws ApiException on non-2xx response or if the response body is not in the expected format
     * @throws InvalidArgumentException
     * @return array of \Repull\Model\TiketReservationListResponse|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error, HTTP status code, HTTP response headers (array of strings)
     */
    public function listTiketReservationsWithHttpInfo(
        ?string $property_id = null,
        ?string $status = null,
        ?\DateTime $check_in_from = null,
        ?\DateTime $check_in_to = null,
        ?int $limit = 50,
        ?int $offset = 0,
        string $contentType = self::contentTypes['listTiketReservations'][0]
    ): array
    {
        $request = $this->listTiketReservationsRequest($property_id, $status, $check_in_from, $check_in_to, $limit, $offset, $contentType);

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
                        '\Repull\Model\TiketReservationListResponse',
                        $request,
                        $response,
                    );
                case 400:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
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
                '\Repull\Model\TiketReservationListResponse',
                $request,
                $response,
            );
        } catch (ApiException $e) {
            switch ($e->getCode()) {
                case 200:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\TiketReservationListResponse',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 400:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
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
            }
        
            throw $e;
        }
    }

    /**
     * Operation listTiketReservationsAsync
     *
     * List tiket.com reservations as tiket.com sent them
     *
     * @param  string|null $property_id tiket.com Hotel ID (shown next to the property name in the tiket.com extranet; &#x60;propertyId&#x60; on &#x60;GET /v1/channels/tiket/properties&#x60;). (optional)
     * @param  string|null $status Only &#x60;confirmed&#x60; or only &#x60;cancelled&#x60; bookings. (optional)
     * @param  \DateTime|null $check_in_from Check-in on or after, YYYY-MM-DD. (optional)
     * @param  \DateTime|null $check_in_to Check-in on or before, YYYY-MM-DD. (optional)
     * @param  int|null $limit Page size. (optional, default to 50)
     * @param  int|null $offset Rows to skip. (optional, default to 0)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['listTiketReservations'] to see the possible values for this operation
     *
     * @throws InvalidArgumentException
     * @return PromiseInterface
     */
    public function listTiketReservationsAsync(
        ?string $property_id = null,
        ?string $status = null,
        ?\DateTime $check_in_from = null,
        ?\DateTime $check_in_to = null,
        ?int $limit = 50,
        ?int $offset = 0,
        string $contentType = self::contentTypes['listTiketReservations'][0]
    ): PromiseInterface
    {
        return $this->listTiketReservationsAsyncWithHttpInfo($property_id, $status, $check_in_from, $check_in_to, $limit, $offset, $contentType)
            ->then(
                function ($response) {
                    return $response[0];
                }
            );
    }

    /**
     * Operation listTiketReservationsAsyncWithHttpInfo
     *
     * List tiket.com reservations as tiket.com sent them
     *
     * @param  string|null $property_id tiket.com Hotel ID (shown next to the property name in the tiket.com extranet; &#x60;propertyId&#x60; on &#x60;GET /v1/channels/tiket/properties&#x60;). (optional)
     * @param  string|null $status Only &#x60;confirmed&#x60; or only &#x60;cancelled&#x60; bookings. (optional)
     * @param  \DateTime|null $check_in_from Check-in on or after, YYYY-MM-DD. (optional)
     * @param  \DateTime|null $check_in_to Check-in on or before, YYYY-MM-DD. (optional)
     * @param  int|null $limit Page size. (optional, default to 50)
     * @param  int|null $offset Rows to skip. (optional, default to 0)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['listTiketReservations'] to see the possible values for this operation
     *
     * @throws InvalidArgumentException
     * @return PromiseInterface
     */
    public function listTiketReservationsAsyncWithHttpInfo(
        ?string $property_id = null,
        ?string $status = null,
        ?\DateTime $check_in_from = null,
        ?\DateTime $check_in_to = null,
        ?int $limit = 50,
        ?int $offset = 0,
        string $contentType = self::contentTypes['listTiketReservations'][0]
    ): PromiseInterface
    {
        $returnType = '\Repull\Model\TiketReservationListResponse';
        $request = $this->listTiketReservationsRequest($property_id, $status, $check_in_from, $check_in_to, $limit, $offset, $contentType);

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
     * Create request for operation 'listTiketReservations'
     *
     * @param  string|null $property_id tiket.com Hotel ID (shown next to the property name in the tiket.com extranet; &#x60;propertyId&#x60; on &#x60;GET /v1/channels/tiket/properties&#x60;). (optional)
     * @param  string|null $status Only &#x60;confirmed&#x60; or only &#x60;cancelled&#x60; bookings. (optional)
     * @param  \DateTime|null $check_in_from Check-in on or after, YYYY-MM-DD. (optional)
     * @param  \DateTime|null $check_in_to Check-in on or before, YYYY-MM-DD. (optional)
     * @param  int|null $limit Page size. (optional, default to 50)
     * @param  int|null $offset Rows to skip. (optional, default to 0)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['listTiketReservations'] to see the possible values for this operation
     *
     * @throws InvalidArgumentException
     * @return \GuzzleHttp\Psr7\Request
     */
    public function listTiketReservationsRequest(
        ?string $property_id = null,
        ?string $status = null,
        ?\DateTime $check_in_from = null,
        ?\DateTime $check_in_to = null,
        ?int $limit = 50,
        ?int $offset = 0,
        string $contentType = self::contentTypes['listTiketReservations'][0]
    ): Request
    {





        if ($limit !== null && $limit > 200) {
            throw new InvalidArgumentException('invalid value for "$limit" when calling TiketApi.listTiketReservations, must be smaller than or equal to 200.');
        }
        if ($limit !== null && $limit < 1) {
            throw new InvalidArgumentException('invalid value for "$limit" when calling TiketApi.listTiketReservations, must be bigger than or equal to 1.');
        }
        
        if ($offset !== null && $offset < 0) {
            throw new InvalidArgumentException('invalid value for "$offset" when calling TiketApi.listTiketReservations, must be bigger than or equal to 0.');
        }
        

        $resourcePath = '/v1/channels/tiket/reservations';
        $formParams = [];
        $queryParams = [];
        $headerParams = [];
        $httpBody = '';
        $multipart = false;

        // query params
        $queryParams = array_merge($queryParams, ObjectSerializer::toQueryValue(
            $property_id,
            'property_id', // param base name
            'string', // openApiType
            'form', // style
            true, // explode
            false, // required
            null // openApiFormat
        ) ?? []);
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
        // query params
        $queryParams = array_merge($queryParams, ObjectSerializer::toQueryValue(
            $check_in_from,
            'check_in_from', // param base name
            'string', // openApiType
            'form', // style
            true, // explode
            false, // required
            'date' // openApiFormat
        ) ?? []);
        // query params
        $queryParams = array_merge($queryParams, ObjectSerializer::toQueryValue(
            $check_in_to,
            'check_in_to', // param base name
            'string', // openApiType
            'form', // style
            true, // explode
            false, // required
            'date' // openApiFormat
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
            $offset,
            'offset', // param base name
            'integer', // openApiType
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
     * Operation mapTiketListings
     *
     * Map tiket.com room types to listings
     *
     * @param  \Repull\Model\TiketMapListingsRequest $tiket_map_listings_request tiket_map_listings_request (required)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['mapTiketListings'] to see the possible values for this operation
     *
     * @throws ApiException on non-2xx response or if the response body is not in the expected format
     * @throws InvalidArgumentException
     * @return \Repull\Model\TiketMapListingsResponse|\Repull\Model\Error
     */
    public function mapTiketListings(
        \Repull\Model\TiketMapListingsRequest $tiket_map_listings_request,
        string $contentType = self::contentTypes['mapTiketListings'][0]
    ): \Repull\Model\TiketMapListingsResponse|\Repull\Model\Error
    {
        list($response) = $this->mapTiketListingsWithHttpInfo($tiket_map_listings_request, $contentType);
        return $response;
    }

    /**
     * Operation mapTiketListingsWithHttpInfo
     *
     * Map tiket.com room types to listings
     *
     * @param  \Repull\Model\TiketMapListingsRequest $tiket_map_listings_request (required)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['mapTiketListings'] to see the possible values for this operation
     *
     * @throws ApiException on non-2xx response or if the response body is not in the expected format
     * @throws InvalidArgumentException
     * @return array of \Repull\Model\TiketMapListingsResponse|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error, HTTP status code, HTTP response headers (array of strings)
     */
    public function mapTiketListingsWithHttpInfo(
        \Repull\Model\TiketMapListingsRequest $tiket_map_listings_request,
        string $contentType = self::contentTypes['mapTiketListings'][0]
    ): array
    {
        $request = $this->mapTiketListingsRequest($tiket_map_listings_request, $contentType);

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
                        '\Repull\Model\TiketMapListingsResponse',
                        $request,
                        $response,
                    );
                case 400:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
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
                case 422:
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
                '\Repull\Model\TiketMapListingsResponse',
                $request,
                $response,
            );
        } catch (ApiException $e) {
            switch ($e->getCode()) {
                case 200:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\TiketMapListingsResponse',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 400:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
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
                case 422:
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
            }
        
            throw $e;
        }
    }

    /**
     * Operation mapTiketListingsAsync
     *
     * Map tiket.com room types to listings
     *
     * @param  \Repull\Model\TiketMapListingsRequest $tiket_map_listings_request (required)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['mapTiketListings'] to see the possible values for this operation
     *
     * @throws InvalidArgumentException
     * @return PromiseInterface
     */
    public function mapTiketListingsAsync(
        \Repull\Model\TiketMapListingsRequest $tiket_map_listings_request,
        string $contentType = self::contentTypes['mapTiketListings'][0]
    ): PromiseInterface
    {
        return $this->mapTiketListingsAsyncWithHttpInfo($tiket_map_listings_request, $contentType)
            ->then(
                function ($response) {
                    return $response[0];
                }
            );
    }

    /**
     * Operation mapTiketListingsAsyncWithHttpInfo
     *
     * Map tiket.com room types to listings
     *
     * @param  \Repull\Model\TiketMapListingsRequest $tiket_map_listings_request (required)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['mapTiketListings'] to see the possible values for this operation
     *
     * @throws InvalidArgumentException
     * @return PromiseInterface
     */
    public function mapTiketListingsAsyncWithHttpInfo(
        \Repull\Model\TiketMapListingsRequest $tiket_map_listings_request,
        string $contentType = self::contentTypes['mapTiketListings'][0]
    ): PromiseInterface
    {
        $returnType = '\Repull\Model\TiketMapListingsResponse';
        $request = $this->mapTiketListingsRequest($tiket_map_listings_request, $contentType);

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
     * Create request for operation 'mapTiketListings'
     *
     * @param  \Repull\Model\TiketMapListingsRequest $tiket_map_listings_request (required)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['mapTiketListings'] to see the possible values for this operation
     *
     * @throws InvalidArgumentException
     * @return \GuzzleHttp\Psr7\Request
     */
    public function mapTiketListingsRequest(
        \Repull\Model\TiketMapListingsRequest $tiket_map_listings_request,
        string $contentType = self::contentTypes['mapTiketListings'][0]
    ): Request
    {

        // verify the required parameter 'tiket_map_listings_request' is set
        if ($tiket_map_listings_request === null || (is_array($tiket_map_listings_request) && count($tiket_map_listings_request) === 0)) {
            throw new InvalidArgumentException(
                'Missing the required parameter $tiket_map_listings_request when calling mapTiketListings'
            );
        }


        $resourcePath = '/v1/channels/tiket/listings/map';
        $formParams = [];
        $queryParams = [];
        $headerParams = [];
        $httpBody = '';
        $multipart = false;





        $headers = $this->headerSelector->selectHeaders(
            ['application/json', ],
            $contentType,
            $multipart
        );

        // for model (json/xml)
        if (isset($tiket_map_listings_request)) {
            if (stripos($headers['Content-Type'], 'application/json') !== false) {
                # if Content-Type contains "application/json", json_encode the body
                $httpBody = \GuzzleHttp\Utils::jsonEncode(ObjectSerializer::sanitizeForSerialization($tiket_map_listings_request));
            } else {
                $httpBody = $tiket_map_listings_request;
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
     * Operation syncTiket
     *
     * Send every mapped listing&#39;s calendar to tiket.com
     *
     * @param  \Repull\Model\SyncTiketRequest|null $sync_tiket_request sync_tiket_request (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['syncTiket'] to see the possible values for this operation
     *
     * @throws ApiException on non-2xx response or if the response body is not in the expected format
     * @throws InvalidArgumentException
     * @return \Repull\Model\TiketSyncResponse|\Repull\Model\Error
     */
    public function syncTiket(
        ?\Repull\Model\SyncTiketRequest $sync_tiket_request = null,
        string $contentType = self::contentTypes['syncTiket'][0]
    ): \Repull\Model\TiketSyncResponse|\Repull\Model\Error
    {
        list($response) = $this->syncTiketWithHttpInfo($sync_tiket_request, $contentType);
        return $response;
    }

    /**
     * Operation syncTiketWithHttpInfo
     *
     * Send every mapped listing&#39;s calendar to tiket.com
     *
     * @param  \Repull\Model\SyncTiketRequest|null $sync_tiket_request (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['syncTiket'] to see the possible values for this operation
     *
     * @throws ApiException on non-2xx response or if the response body is not in the expected format
     * @throws InvalidArgumentException
     * @return array of \Repull\Model\TiketSyncResponse|\Repull\Model\Error|\Repull\Model\Error, HTTP status code, HTTP response headers (array of strings)
     */
    public function syncTiketWithHttpInfo(
        ?\Repull\Model\SyncTiketRequest $sync_tiket_request = null,
        string $contentType = self::contentTypes['syncTiket'][0]
    ): array
    {
        $request = $this->syncTiketRequest($sync_tiket_request, $contentType);

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
                        '\Repull\Model\TiketSyncResponse',
                        $request,
                        $response,
                    );
                case 401:
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
                '\Repull\Model\TiketSyncResponse',
                $request,
                $response,
            );
        } catch (ApiException $e) {
            switch ($e->getCode()) {
                case 200:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\TiketSyncResponse',
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
                case 404:
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
     * Operation syncTiketAsync
     *
     * Send every mapped listing&#39;s calendar to tiket.com
     *
     * @param  \Repull\Model\SyncTiketRequest|null $sync_tiket_request (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['syncTiket'] to see the possible values for this operation
     *
     * @throws InvalidArgumentException
     * @return PromiseInterface
     */
    public function syncTiketAsync(
        ?\Repull\Model\SyncTiketRequest $sync_tiket_request = null,
        string $contentType = self::contentTypes['syncTiket'][0]
    ): PromiseInterface
    {
        return $this->syncTiketAsyncWithHttpInfo($sync_tiket_request, $contentType)
            ->then(
                function ($response) {
                    return $response[0];
                }
            );
    }

    /**
     * Operation syncTiketAsyncWithHttpInfo
     *
     * Send every mapped listing&#39;s calendar to tiket.com
     *
     * @param  \Repull\Model\SyncTiketRequest|null $sync_tiket_request (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['syncTiket'] to see the possible values for this operation
     *
     * @throws InvalidArgumentException
     * @return PromiseInterface
     */
    public function syncTiketAsyncWithHttpInfo(
        ?\Repull\Model\SyncTiketRequest $sync_tiket_request = null,
        string $contentType = self::contentTypes['syncTiket'][0]
    ): PromiseInterface
    {
        $returnType = '\Repull\Model\TiketSyncResponse';
        $request = $this->syncTiketRequest($sync_tiket_request, $contentType);

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
     * Create request for operation 'syncTiket'
     *
     * @param  \Repull\Model\SyncTiketRequest|null $sync_tiket_request (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['syncTiket'] to see the possible values for this operation
     *
     * @throws InvalidArgumentException
     * @return \GuzzleHttp\Psr7\Request
     */
    public function syncTiketRequest(
        ?\Repull\Model\SyncTiketRequest $sync_tiket_request = null,
        string $contentType = self::contentTypes['syncTiket'][0]
    ): Request
    {



        $resourcePath = '/v1/channels/tiket/sync';
        $formParams = [];
        $queryParams = [];
        $headerParams = [];
        $httpBody = '';
        $multipart = false;





        $headers = $this->headerSelector->selectHeaders(
            ['application/json', ],
            $contentType,
            $multipart
        );

        // for model (json/xml)
        if (isset($sync_tiket_request)) {
            if (stripos($headers['Content-Type'], 'application/json') !== false) {
                # if Content-Type contains "application/json", json_encode the body
                $httpBody = \GuzzleHttp\Utils::jsonEncode(ObjectSerializer::sanitizeForSerialization($sync_tiket_request));
            } else {
                $httpBody = $sync_tiket_request;
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
     * Operation tiketSetup
     *
     * Set up a tiket.com property
     *
     * @param  \Repull\Model\TiketSetupRequest $tiket_setup_request tiket_setup_request (required)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['tiketSetup'] to see the possible values for this operation
     *
     * @throws ApiException on non-2xx response or if the response body is not in the expected format
     * @throws InvalidArgumentException
     * @return \Repull\Model\TiketSetupResponse|\Repull\Model\Error
     */
    public function tiketSetup(
        \Repull\Model\TiketSetupRequest $tiket_setup_request,
        string $contentType = self::contentTypes['tiketSetup'][0]
    ): \Repull\Model\TiketSetupResponse|\Repull\Model\Error
    {
        list($response) = $this->tiketSetupWithHttpInfo($tiket_setup_request, $contentType);
        return $response;
    }

    /**
     * Operation tiketSetupWithHttpInfo
     *
     * Set up a tiket.com property
     *
     * @param  \Repull\Model\TiketSetupRequest $tiket_setup_request (required)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['tiketSetup'] to see the possible values for this operation
     *
     * @throws ApiException on non-2xx response or if the response body is not in the expected format
     * @throws InvalidArgumentException
     * @return array of \Repull\Model\TiketSetupResponse|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error, HTTP status code, HTTP response headers (array of strings)
     */
    public function tiketSetupWithHttpInfo(
        \Repull\Model\TiketSetupRequest $tiket_setup_request,
        string $contentType = self::contentTypes['tiketSetup'][0]
    ): array
    {
        $request = $this->tiketSetupRequest($tiket_setup_request, $contentType);

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
                        '\Repull\Model\TiketSetupResponse',
                        $request,
                        $response,
                    );
                case 400:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
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
                case 422:
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
                '\Repull\Model\TiketSetupResponse',
                $request,
                $response,
            );
        } catch (ApiException $e) {
            switch ($e->getCode()) {
                case 200:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\TiketSetupResponse',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 400:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
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
                case 422:
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
            }
        
            throw $e;
        }
    }

    /**
     * Operation tiketSetupAsync
     *
     * Set up a tiket.com property
     *
     * @param  \Repull\Model\TiketSetupRequest $tiket_setup_request (required)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['tiketSetup'] to see the possible values for this operation
     *
     * @throws InvalidArgumentException
     * @return PromiseInterface
     */
    public function tiketSetupAsync(
        \Repull\Model\TiketSetupRequest $tiket_setup_request,
        string $contentType = self::contentTypes['tiketSetup'][0]
    ): PromiseInterface
    {
        return $this->tiketSetupAsyncWithHttpInfo($tiket_setup_request, $contentType)
            ->then(
                function ($response) {
                    return $response[0];
                }
            );
    }

    /**
     * Operation tiketSetupAsyncWithHttpInfo
     *
     * Set up a tiket.com property
     *
     * @param  \Repull\Model\TiketSetupRequest $tiket_setup_request (required)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['tiketSetup'] to see the possible values for this operation
     *
     * @throws InvalidArgumentException
     * @return PromiseInterface
     */
    public function tiketSetupAsyncWithHttpInfo(
        \Repull\Model\TiketSetupRequest $tiket_setup_request,
        string $contentType = self::contentTypes['tiketSetup'][0]
    ): PromiseInterface
    {
        $returnType = '\Repull\Model\TiketSetupResponse';
        $request = $this->tiketSetupRequest($tiket_setup_request, $contentType);

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
     * Create request for operation 'tiketSetup'
     *
     * @param  \Repull\Model\TiketSetupRequest $tiket_setup_request (required)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['tiketSetup'] to see the possible values for this operation
     *
     * @throws InvalidArgumentException
     * @return \GuzzleHttp\Psr7\Request
     */
    public function tiketSetupRequest(
        \Repull\Model\TiketSetupRequest $tiket_setup_request,
        string $contentType = self::contentTypes['tiketSetup'][0]
    ): Request
    {

        // verify the required parameter 'tiket_setup_request' is set
        if ($tiket_setup_request === null || (is_array($tiket_setup_request) && count($tiket_setup_request) === 0)) {
            throw new InvalidArgumentException(
                'Missing the required parameter $tiket_setup_request when calling tiketSetup'
            );
        }


        $resourcePath = '/v1/channels/tiket/setup';
        $formParams = [];
        $queryParams = [];
        $headerParams = [];
        $httpBody = '';
        $multipart = false;





        $headers = $this->headerSelector->selectHeaders(
            ['application/json', ],
            $contentType,
            $multipart
        );

        // for model (json/xml)
        if (isset($tiket_setup_request)) {
            if (stripos($headers['Content-Type'], 'application/json') !== false) {
                # if Content-Type contains "application/json", json_encode the body
                $httpBody = \GuzzleHttp\Utils::jsonEncode(ObjectSerializer::sanitizeForSerialization($tiket_setup_request));
            } else {
                $httpBody = $tiket_setup_request;
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
     * Operation updateTiketAvailability
     *
     * Write tiket.com rates, availability or restrictions
     *
     * @param  \Repull\Model\TiketAvailabilityUpdateRequest $tiket_availability_update_request tiket_availability_update_request (required)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['updateTiketAvailability'] to see the possible values for this operation
     *
     * @throws ApiException on non-2xx response or if the response body is not in the expected format
     * @throws InvalidArgumentException
     * @return \Repull\Model\TiketAriWriteResponse|\Repull\Model\Error
     */
    public function updateTiketAvailability(
        \Repull\Model\TiketAvailabilityUpdateRequest $tiket_availability_update_request,
        string $contentType = self::contentTypes['updateTiketAvailability'][0]
    ): \Repull\Model\TiketAriWriteResponse|\Repull\Model\Error
    {
        list($response) = $this->updateTiketAvailabilityWithHttpInfo($tiket_availability_update_request, $contentType);
        return $response;
    }

    /**
     * Operation updateTiketAvailabilityWithHttpInfo
     *
     * Write tiket.com rates, availability or restrictions
     *
     * @param  \Repull\Model\TiketAvailabilityUpdateRequest $tiket_availability_update_request (required)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['updateTiketAvailability'] to see the possible values for this operation
     *
     * @throws ApiException on non-2xx response or if the response body is not in the expected format
     * @throws InvalidArgumentException
     * @return array of \Repull\Model\TiketAriWriteResponse|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error, HTTP status code, HTTP response headers (array of strings)
     */
    public function updateTiketAvailabilityWithHttpInfo(
        \Repull\Model\TiketAvailabilityUpdateRequest $tiket_availability_update_request,
        string $contentType = self::contentTypes['updateTiketAvailability'][0]
    ): array
    {
        $request = $this->updateTiketAvailabilityRequest($tiket_availability_update_request, $contentType);

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
                        '\Repull\Model\TiketAriWriteResponse',
                        $request,
                        $response,
                    );
                case 400:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
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
                case 422:
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
                '\Repull\Model\TiketAriWriteResponse',
                $request,
                $response,
            );
        } catch (ApiException $e) {
            switch ($e->getCode()) {
                case 200:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\TiketAriWriteResponse',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 400:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
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
                case 422:
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
            }
        
            throw $e;
        }
    }

    /**
     * Operation updateTiketAvailabilityAsync
     *
     * Write tiket.com rates, availability or restrictions
     *
     * @param  \Repull\Model\TiketAvailabilityUpdateRequest $tiket_availability_update_request (required)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['updateTiketAvailability'] to see the possible values for this operation
     *
     * @throws InvalidArgumentException
     * @return PromiseInterface
     */
    public function updateTiketAvailabilityAsync(
        \Repull\Model\TiketAvailabilityUpdateRequest $tiket_availability_update_request,
        string $contentType = self::contentTypes['updateTiketAvailability'][0]
    ): PromiseInterface
    {
        return $this->updateTiketAvailabilityAsyncWithHttpInfo($tiket_availability_update_request, $contentType)
            ->then(
                function ($response) {
                    return $response[0];
                }
            );
    }

    /**
     * Operation updateTiketAvailabilityAsyncWithHttpInfo
     *
     * Write tiket.com rates, availability or restrictions
     *
     * @param  \Repull\Model\TiketAvailabilityUpdateRequest $tiket_availability_update_request (required)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['updateTiketAvailability'] to see the possible values for this operation
     *
     * @throws InvalidArgumentException
     * @return PromiseInterface
     */
    public function updateTiketAvailabilityAsyncWithHttpInfo(
        \Repull\Model\TiketAvailabilityUpdateRequest $tiket_availability_update_request,
        string $contentType = self::contentTypes['updateTiketAvailability'][0]
    ): PromiseInterface
    {
        $returnType = '\Repull\Model\TiketAriWriteResponse';
        $request = $this->updateTiketAvailabilityRequest($tiket_availability_update_request, $contentType);

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
     * Create request for operation 'updateTiketAvailability'
     *
     * @param  \Repull\Model\TiketAvailabilityUpdateRequest $tiket_availability_update_request (required)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['updateTiketAvailability'] to see the possible values for this operation
     *
     * @throws InvalidArgumentException
     * @return \GuzzleHttp\Psr7\Request
     */
    public function updateTiketAvailabilityRequest(
        \Repull\Model\TiketAvailabilityUpdateRequest $tiket_availability_update_request,
        string $contentType = self::contentTypes['updateTiketAvailability'][0]
    ): Request
    {

        // verify the required parameter 'tiket_availability_update_request' is set
        if ($tiket_availability_update_request === null || (is_array($tiket_availability_update_request) && count($tiket_availability_update_request) === 0)) {
            throw new InvalidArgumentException(
                'Missing the required parameter $tiket_availability_update_request when calling updateTiketAvailability'
            );
        }


        $resourcePath = '/v1/channels/tiket/availability';
        $formParams = [];
        $queryParams = [];
        $headerParams = [];
        $httpBody = '';
        $multipart = false;





        $headers = $this->headerSelector->selectHeaders(
            ['application/json', ],
            $contentType,
            $multipart
        );

        // for model (json/xml)
        if (isset($tiket_availability_update_request)) {
            if (stripos($headers['Content-Type'], 'application/json') !== false) {
                # if Content-Type contains "application/json", json_encode the body
                $httpBody = \GuzzleHttp\Utils::jsonEncode(ObjectSerializer::sanitizeForSerialization($tiket_availability_update_request));
            } else {
                $httpBody = $tiket_availability_update_request;
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
     * Operation updateTiketListingPricing
     *
     * Write tiket.com rates, availability or restrictions for a listing
     *
     * @param  int $id Repull listing id. (required)
     * @param  \Repull\Model\TiketListingPricingUpdateRequest $tiket_listing_pricing_update_request tiket_listing_pricing_update_request (required)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['updateTiketListingPricing'] to see the possible values for this operation
     *
     * @throws ApiException on non-2xx response or if the response body is not in the expected format
     * @throws InvalidArgumentException
     * @return \Repull\Model\TiketAriWriteResponse|\Repull\Model\Error
     */
    public function updateTiketListingPricing(
        int $id,
        \Repull\Model\TiketListingPricingUpdateRequest $tiket_listing_pricing_update_request,
        string $contentType = self::contentTypes['updateTiketListingPricing'][0]
    ): \Repull\Model\TiketAriWriteResponse|\Repull\Model\Error
    {
        list($response) = $this->updateTiketListingPricingWithHttpInfo($id, $tiket_listing_pricing_update_request, $contentType);
        return $response;
    }

    /**
     * Operation updateTiketListingPricingWithHttpInfo
     *
     * Write tiket.com rates, availability or restrictions for a listing
     *
     * @param  int $id Repull listing id. (required)
     * @param  \Repull\Model\TiketListingPricingUpdateRequest $tiket_listing_pricing_update_request (required)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['updateTiketListingPricing'] to see the possible values for this operation
     *
     * @throws ApiException on non-2xx response or if the response body is not in the expected format
     * @throws InvalidArgumentException
     * @return array of \Repull\Model\TiketAriWriteResponse|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error, HTTP status code, HTTP response headers (array of strings)
     */
    public function updateTiketListingPricingWithHttpInfo(
        int $id,
        \Repull\Model\TiketListingPricingUpdateRequest $tiket_listing_pricing_update_request,
        string $contentType = self::contentTypes['updateTiketListingPricing'][0]
    ): array
    {
        $request = $this->updateTiketListingPricingRequest($id, $tiket_listing_pricing_update_request, $contentType);

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
                        '\Repull\Model\TiketAriWriteResponse',
                        $request,
                        $response,
                    );
                case 400:
                    return $this->handleResponseWithDataType(
                        '\Repull\Model\Error',
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
                '\Repull\Model\TiketAriWriteResponse',
                $request,
                $response,
            );
        } catch (ApiException $e) {
            switch ($e->getCode()) {
                case 200:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\TiketAriWriteResponse',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 400:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Error',
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
            }
        
            throw $e;
        }
    }

    /**
     * Operation updateTiketListingPricingAsync
     *
     * Write tiket.com rates, availability or restrictions for a listing
     *
     * @param  int $id Repull listing id. (required)
     * @param  \Repull\Model\TiketListingPricingUpdateRequest $tiket_listing_pricing_update_request (required)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['updateTiketListingPricing'] to see the possible values for this operation
     *
     * @throws InvalidArgumentException
     * @return PromiseInterface
     */
    public function updateTiketListingPricingAsync(
        int $id,
        \Repull\Model\TiketListingPricingUpdateRequest $tiket_listing_pricing_update_request,
        string $contentType = self::contentTypes['updateTiketListingPricing'][0]
    ): PromiseInterface
    {
        return $this->updateTiketListingPricingAsyncWithHttpInfo($id, $tiket_listing_pricing_update_request, $contentType)
            ->then(
                function ($response) {
                    return $response[0];
                }
            );
    }

    /**
     * Operation updateTiketListingPricingAsyncWithHttpInfo
     *
     * Write tiket.com rates, availability or restrictions for a listing
     *
     * @param  int $id Repull listing id. (required)
     * @param  \Repull\Model\TiketListingPricingUpdateRequest $tiket_listing_pricing_update_request (required)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['updateTiketListingPricing'] to see the possible values for this operation
     *
     * @throws InvalidArgumentException
     * @return PromiseInterface
     */
    public function updateTiketListingPricingAsyncWithHttpInfo(
        int $id,
        \Repull\Model\TiketListingPricingUpdateRequest $tiket_listing_pricing_update_request,
        string $contentType = self::contentTypes['updateTiketListingPricing'][0]
    ): PromiseInterface
    {
        $returnType = '\Repull\Model\TiketAriWriteResponse';
        $request = $this->updateTiketListingPricingRequest($id, $tiket_listing_pricing_update_request, $contentType);

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
     * Create request for operation 'updateTiketListingPricing'
     *
     * @param  int $id Repull listing id. (required)
     * @param  \Repull\Model\TiketListingPricingUpdateRequest $tiket_listing_pricing_update_request (required)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['updateTiketListingPricing'] to see the possible values for this operation
     *
     * @throws InvalidArgumentException
     * @return \GuzzleHttp\Psr7\Request
     */
    public function updateTiketListingPricingRequest(
        int $id,
        \Repull\Model\TiketListingPricingUpdateRequest $tiket_listing_pricing_update_request,
        string $contentType = self::contentTypes['updateTiketListingPricing'][0]
    ): Request
    {

        // verify the required parameter 'id' is set
        if ($id === null || (is_array($id) && count($id) === 0)) {
            throw new InvalidArgumentException(
                'Missing the required parameter $id when calling updateTiketListingPricing'
            );
        }

        // verify the required parameter 'tiket_listing_pricing_update_request' is set
        if ($tiket_listing_pricing_update_request === null || (is_array($tiket_listing_pricing_update_request) && count($tiket_listing_pricing_update_request) === 0)) {
            throw new InvalidArgumentException(
                'Missing the required parameter $tiket_listing_pricing_update_request when calling updateTiketListingPricing'
            );
        }


        $resourcePath = '/v1/channels/tiket/listings/{id}/pricing';
        $formParams = [];
        $queryParams = [];
        $headerParams = [];
        $httpBody = '';
        $multipart = false;



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
        if (isset($tiket_listing_pricing_update_request)) {
            if (stripos($headers['Content-Type'], 'application/json') !== false) {
                # if Content-Type contains "application/json", json_encode the body
                $httpBody = \GuzzleHttp\Utils::jsonEncode(ObjectSerializer::sanitizeForSerialization($tiket_listing_pricing_update_request));
            } else {
                $httpBody = $tiket_listing_pricing_update_request;
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
