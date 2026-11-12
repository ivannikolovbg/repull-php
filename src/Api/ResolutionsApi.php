<?php
/**
 * ResolutionsApi
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
 * ResolutionsApi Class Doc Comment
 *
 * @package  Repull
 * @author   OpenAPI Generator team
 * @link     https://openapi-generator.tech
 */
class ResolutionsApi
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
        'getAirbnbResolution' => [
            'application/json',
        ],
        'listAirbnbResolutions' => [
            'application/json',
        ],
        'refreshAirbnbResolutions' => [
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
     * Operation getAirbnbResolution
     *
     * Get an Airbnb Resolution Center case
     *
     * @param  string $id The Airbnb resolution id. (required)
     * @param  string|null $x_schema Apply a custom or built-in schema to transform the response. Built-in: &#x60;native&#x60; (default), &#x60;calry&#x60;, &#x60;calry-v1&#x60;. Custom: any schema name created via &#x60;POST /v1/schema/custom&#x60;. Unknown / inactive schema names fall back to &#x60;native&#x60;. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['getAirbnbResolution'] to see the possible values for this operation
     *
     * @throws ApiException on non-2xx response or if the response body is not in the expected format
     * @throws InvalidArgumentException
     * @return \Repull\Model\Resolution|\Repull\Model\Error
     */
    public function getAirbnbResolution(
        string $id,
        ?string $x_schema = null,
        string $contentType = self::contentTypes['getAirbnbResolution'][0]
    ): \Repull\Model\Resolution|\Repull\Model\Error
    {
        list($response) = $this->getAirbnbResolutionWithHttpInfo($id, $x_schema, $contentType);
        return $response;
    }

    /**
     * Operation getAirbnbResolutionWithHttpInfo
     *
     * Get an Airbnb Resolution Center case
     *
     * @param  string $id The Airbnb resolution id. (required)
     * @param  string|null $x_schema Apply a custom or built-in schema to transform the response. Built-in: &#x60;native&#x60; (default), &#x60;calry&#x60;, &#x60;calry-v1&#x60;. Custom: any schema name created via &#x60;POST /v1/schema/custom&#x60;. Unknown / inactive schema names fall back to &#x60;native&#x60;. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['getAirbnbResolution'] to see the possible values for this operation
     *
     * @throws ApiException on non-2xx response or if the response body is not in the expected format
     * @throws InvalidArgumentException
     * @return array of \Repull\Model\Resolution|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error, HTTP status code, HTTP response headers (array of strings)
     */
    public function getAirbnbResolutionWithHttpInfo(
        string $id,
        ?string $x_schema = null,
        string $contentType = self::contentTypes['getAirbnbResolution'][0]
    ): array
    {
        $request = $this->getAirbnbResolutionRequest($id, $x_schema, $contentType);

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
                        '\Repull\Model\Resolution',
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
                '\Repull\Model\Resolution',
                $request,
                $response,
            );
        } catch (ApiException $e) {
            switch ($e->getCode()) {
                case 200:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\Resolution',
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
     * Operation getAirbnbResolutionAsync
     *
     * Get an Airbnb Resolution Center case
     *
     * @param  string $id The Airbnb resolution id. (required)
     * @param  string|null $x_schema Apply a custom or built-in schema to transform the response. Built-in: &#x60;native&#x60; (default), &#x60;calry&#x60;, &#x60;calry-v1&#x60;. Custom: any schema name created via &#x60;POST /v1/schema/custom&#x60;. Unknown / inactive schema names fall back to &#x60;native&#x60;. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['getAirbnbResolution'] to see the possible values for this operation
     *
     * @throws InvalidArgumentException
     * @return PromiseInterface
     */
    public function getAirbnbResolutionAsync(
        string $id,
        ?string $x_schema = null,
        string $contentType = self::contentTypes['getAirbnbResolution'][0]
    ): PromiseInterface
    {
        return $this->getAirbnbResolutionAsyncWithHttpInfo($id, $x_schema, $contentType)
            ->then(
                function ($response) {
                    return $response[0];
                }
            );
    }

    /**
     * Operation getAirbnbResolutionAsyncWithHttpInfo
     *
     * Get an Airbnb Resolution Center case
     *
     * @param  string $id The Airbnb resolution id. (required)
     * @param  string|null $x_schema Apply a custom or built-in schema to transform the response. Built-in: &#x60;native&#x60; (default), &#x60;calry&#x60;, &#x60;calry-v1&#x60;. Custom: any schema name created via &#x60;POST /v1/schema/custom&#x60;. Unknown / inactive schema names fall back to &#x60;native&#x60;. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['getAirbnbResolution'] to see the possible values for this operation
     *
     * @throws InvalidArgumentException
     * @return PromiseInterface
     */
    public function getAirbnbResolutionAsyncWithHttpInfo(
        string $id,
        ?string $x_schema = null,
        string $contentType = self::contentTypes['getAirbnbResolution'][0]
    ): PromiseInterface
    {
        $returnType = '\Repull\Model\Resolution';
        $request = $this->getAirbnbResolutionRequest($id, $x_schema, $contentType);

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
     * Create request for operation 'getAirbnbResolution'
     *
     * @param  string $id The Airbnb resolution id. (required)
     * @param  string|null $x_schema Apply a custom or built-in schema to transform the response. Built-in: &#x60;native&#x60; (default), &#x60;calry&#x60;, &#x60;calry-v1&#x60;. Custom: any schema name created via &#x60;POST /v1/schema/custom&#x60;. Unknown / inactive schema names fall back to &#x60;native&#x60;. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['getAirbnbResolution'] to see the possible values for this operation
     *
     * @throws InvalidArgumentException
     * @return \GuzzleHttp\Psr7\Request
     */
    public function getAirbnbResolutionRequest(
        string $id,
        ?string $x_schema = null,
        string $contentType = self::contentTypes['getAirbnbResolution'][0]
    ): Request
    {

        // verify the required parameter 'id' is set
        if ($id === null || (is_array($id) && count($id) === 0)) {
            throw new InvalidArgumentException(
                'Missing the required parameter $id when calling getAirbnbResolution'
            );
        }



        $resourcePath = '/v1/channels/airbnb/resolutions/{id}';
        $formParams = [];
        $queryParams = [];
        $headerParams = [];
        $httpBody = '';
        $multipart = false;


        // header params
        if ($x_schema !== null) {
            $headerParams['X-Schema'] = ObjectSerializer::toHeaderValue($x_schema);
        }

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
     * Operation listAirbnbResolutions
     *
     * List Airbnb Resolution Center cases
     *
     * @param  string|null $x_schema Apply a custom or built-in schema to transform the response. Built-in: &#x60;native&#x60; (default), &#x60;calry&#x60;, &#x60;calry-v1&#x60;. Custom: any schema name created via &#x60;POST /v1/schema/custom&#x60;. Unknown / inactive schema names fall back to &#x60;native&#x60;. (optional)
     * @param  string|null $status Filter by lifecycle: &#x60;open&#x60; (still needs action) or &#x60;closed&#x60; (settled). Omit for all. (optional)
     * @param  int|null $limit Page size (default 50, max 200). (optional)
     * @param  string|null $cursor Opaque pagination cursor from a previous &#x60;pagination.nextCursor&#x60;. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['listAirbnbResolutions'] to see the possible values for this operation
     *
     * @throws ApiException on non-2xx response or if the response body is not in the expected format
     * @throws InvalidArgumentException
     * @return \Repull\Model\ListAirbnbResolutions200Response|\Repull\Model\Error
     */
    public function listAirbnbResolutions(
        ?string $x_schema = null,
        ?string $status = null,
        ?int $limit = null,
        ?string $cursor = null,
        string $contentType = self::contentTypes['listAirbnbResolutions'][0]
    ): \Repull\Model\ListAirbnbResolutions200Response|\Repull\Model\Error
    {
        list($response) = $this->listAirbnbResolutionsWithHttpInfo($x_schema, $status, $limit, $cursor, $contentType);
        return $response;
    }

    /**
     * Operation listAirbnbResolutionsWithHttpInfo
     *
     * List Airbnb Resolution Center cases
     *
     * @param  string|null $x_schema Apply a custom or built-in schema to transform the response. Built-in: &#x60;native&#x60; (default), &#x60;calry&#x60;, &#x60;calry-v1&#x60;. Custom: any schema name created via &#x60;POST /v1/schema/custom&#x60;. Unknown / inactive schema names fall back to &#x60;native&#x60;. (optional)
     * @param  string|null $status Filter by lifecycle: &#x60;open&#x60; (still needs action) or &#x60;closed&#x60; (settled). Omit for all. (optional)
     * @param  int|null $limit Page size (default 50, max 200). (optional)
     * @param  string|null $cursor Opaque pagination cursor from a previous &#x60;pagination.nextCursor&#x60;. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['listAirbnbResolutions'] to see the possible values for this operation
     *
     * @throws ApiException on non-2xx response or if the response body is not in the expected format
     * @throws InvalidArgumentException
     * @return array of \Repull\Model\ListAirbnbResolutions200Response|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error, HTTP status code, HTTP response headers (array of strings)
     */
    public function listAirbnbResolutionsWithHttpInfo(
        ?string $x_schema = null,
        ?string $status = null,
        ?int $limit = null,
        ?string $cursor = null,
        string $contentType = self::contentTypes['listAirbnbResolutions'][0]
    ): array
    {
        $request = $this->listAirbnbResolutionsRequest($x_schema, $status, $limit, $cursor, $contentType);

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
                        '\Repull\Model\ListAirbnbResolutions200Response',
                        $request,
                        $response,
                    );
                case 401:
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
                '\Repull\Model\ListAirbnbResolutions200Response',
                $request,
                $response,
            );
        } catch (ApiException $e) {
            switch ($e->getCode()) {
                case 200:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\ListAirbnbResolutions200Response',
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
            }
        
            throw $e;
        }
    }

    /**
     * Operation listAirbnbResolutionsAsync
     *
     * List Airbnb Resolution Center cases
     *
     * @param  string|null $x_schema Apply a custom or built-in schema to transform the response. Built-in: &#x60;native&#x60; (default), &#x60;calry&#x60;, &#x60;calry-v1&#x60;. Custom: any schema name created via &#x60;POST /v1/schema/custom&#x60;. Unknown / inactive schema names fall back to &#x60;native&#x60;. (optional)
     * @param  string|null $status Filter by lifecycle: &#x60;open&#x60; (still needs action) or &#x60;closed&#x60; (settled). Omit for all. (optional)
     * @param  int|null $limit Page size (default 50, max 200). (optional)
     * @param  string|null $cursor Opaque pagination cursor from a previous &#x60;pagination.nextCursor&#x60;. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['listAirbnbResolutions'] to see the possible values for this operation
     *
     * @throws InvalidArgumentException
     * @return PromiseInterface
     */
    public function listAirbnbResolutionsAsync(
        ?string $x_schema = null,
        ?string $status = null,
        ?int $limit = null,
        ?string $cursor = null,
        string $contentType = self::contentTypes['listAirbnbResolutions'][0]
    ): PromiseInterface
    {
        return $this->listAirbnbResolutionsAsyncWithHttpInfo($x_schema, $status, $limit, $cursor, $contentType)
            ->then(
                function ($response) {
                    return $response[0];
                }
            );
    }

    /**
     * Operation listAirbnbResolutionsAsyncWithHttpInfo
     *
     * List Airbnb Resolution Center cases
     *
     * @param  string|null $x_schema Apply a custom or built-in schema to transform the response. Built-in: &#x60;native&#x60; (default), &#x60;calry&#x60;, &#x60;calry-v1&#x60;. Custom: any schema name created via &#x60;POST /v1/schema/custom&#x60;. Unknown / inactive schema names fall back to &#x60;native&#x60;. (optional)
     * @param  string|null $status Filter by lifecycle: &#x60;open&#x60; (still needs action) or &#x60;closed&#x60; (settled). Omit for all. (optional)
     * @param  int|null $limit Page size (default 50, max 200). (optional)
     * @param  string|null $cursor Opaque pagination cursor from a previous &#x60;pagination.nextCursor&#x60;. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['listAirbnbResolutions'] to see the possible values for this operation
     *
     * @throws InvalidArgumentException
     * @return PromiseInterface
     */
    public function listAirbnbResolutionsAsyncWithHttpInfo(
        ?string $x_schema = null,
        ?string $status = null,
        ?int $limit = null,
        ?string $cursor = null,
        string $contentType = self::contentTypes['listAirbnbResolutions'][0]
    ): PromiseInterface
    {
        $returnType = '\Repull\Model\ListAirbnbResolutions200Response';
        $request = $this->listAirbnbResolutionsRequest($x_schema, $status, $limit, $cursor, $contentType);

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
     * Create request for operation 'listAirbnbResolutions'
     *
     * @param  string|null $x_schema Apply a custom or built-in schema to transform the response. Built-in: &#x60;native&#x60; (default), &#x60;calry&#x60;, &#x60;calry-v1&#x60;. Custom: any schema name created via &#x60;POST /v1/schema/custom&#x60;. Unknown / inactive schema names fall back to &#x60;native&#x60;. (optional)
     * @param  string|null $status Filter by lifecycle: &#x60;open&#x60; (still needs action) or &#x60;closed&#x60; (settled). Omit for all. (optional)
     * @param  int|null $limit Page size (default 50, max 200). (optional)
     * @param  string|null $cursor Opaque pagination cursor from a previous &#x60;pagination.nextCursor&#x60;. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['listAirbnbResolutions'] to see the possible values for this operation
     *
     * @throws InvalidArgumentException
     * @return \GuzzleHttp\Psr7\Request
     */
    public function listAirbnbResolutionsRequest(
        ?string $x_schema = null,
        ?string $status = null,
        ?int $limit = null,
        ?string $cursor = null,
        string $contentType = self::contentTypes['listAirbnbResolutions'][0]
    ): Request
    {



        if ($limit !== null && $limit > 200) {
            throw new InvalidArgumentException('invalid value for "$limit" when calling ResolutionsApi.listAirbnbResolutions, must be smaller than or equal to 200.');
        }
        if ($limit !== null && $limit < 1) {
            throw new InvalidArgumentException('invalid value for "$limit" when calling ResolutionsApi.listAirbnbResolutions, must be bigger than or equal to 1.');
        }
        


        $resourcePath = '/v1/channels/airbnb/resolutions';
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
            false // required
        ) ?? []);
        // query params
        $queryParams = array_merge($queryParams, ObjectSerializer::toQueryValue(
            $limit,
            'limit', // param base name
            'integer', // openApiType
            'form', // style
            true, // explode
            false // required
        ) ?? []);
        // query params
        $queryParams = array_merge($queryParams, ObjectSerializer::toQueryValue(
            $cursor,
            'cursor', // param base name
            'string', // openApiType
            'form', // style
            true, // explode
            false // required
        ) ?? []);

        // header params
        if ($x_schema !== null) {
            $headerParams['X-Schema'] = ObjectSerializer::toHeaderValue($x_schema);
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
     * Operation refreshAirbnbResolutions
     *
     * Refresh Airbnb resolutions from Airbnb
     *
     * @param  string|null $x_schema Apply a custom or built-in schema to transform the response. Built-in: &#x60;native&#x60; (default), &#x60;calry&#x60;, &#x60;calry-v1&#x60;. Custom: any schema name created via &#x60;POST /v1/schema/custom&#x60;. Unknown / inactive schema names fall back to &#x60;native&#x60;. (optional)
     * @param  \Repull\Model\RefreshAirbnbResolutionsRequest|null $refresh_airbnb_resolutions_request refresh_airbnb_resolutions_request (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['refreshAirbnbResolutions'] to see the possible values for this operation
     *
     * @throws ApiException on non-2xx response or if the response body is not in the expected format
     * @throws InvalidArgumentException
     * @return \Repull\Model\RefreshAirbnbResolutions200Response|\Repull\Model\Error
     */
    public function refreshAirbnbResolutions(
        ?string $x_schema = null,
        ?\Repull\Model\RefreshAirbnbResolutionsRequest $refresh_airbnb_resolutions_request = null,
        string $contentType = self::contentTypes['refreshAirbnbResolutions'][0]
    ): \Repull\Model\RefreshAirbnbResolutions200Response|\Repull\Model\Error
    {
        list($response) = $this->refreshAirbnbResolutionsWithHttpInfo($x_schema, $refresh_airbnb_resolutions_request, $contentType);
        return $response;
    }

    /**
     * Operation refreshAirbnbResolutionsWithHttpInfo
     *
     * Refresh Airbnb resolutions from Airbnb
     *
     * @param  string|null $x_schema Apply a custom or built-in schema to transform the response. Built-in: &#x60;native&#x60; (default), &#x60;calry&#x60;, &#x60;calry-v1&#x60;. Custom: any schema name created via &#x60;POST /v1/schema/custom&#x60;. Unknown / inactive schema names fall back to &#x60;native&#x60;. (optional)
     * @param  \Repull\Model\RefreshAirbnbResolutionsRequest|null $refresh_airbnb_resolutions_request (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['refreshAirbnbResolutions'] to see the possible values for this operation
     *
     * @throws ApiException on non-2xx response or if the response body is not in the expected format
     * @throws InvalidArgumentException
     * @return array of \Repull\Model\RefreshAirbnbResolutions200Response|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error|\Repull\Model\Error, HTTP status code, HTTP response headers (array of strings)
     */
    public function refreshAirbnbResolutionsWithHttpInfo(
        ?string $x_schema = null,
        ?\Repull\Model\RefreshAirbnbResolutionsRequest $refresh_airbnb_resolutions_request = null,
        string $contentType = self::contentTypes['refreshAirbnbResolutions'][0]
    ): array
    {
        $request = $this->refreshAirbnbResolutionsRequest($x_schema, $refresh_airbnb_resolutions_request, $contentType);

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
                        '\Repull\Model\RefreshAirbnbResolutions200Response',
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
                '\Repull\Model\RefreshAirbnbResolutions200Response',
                $request,
                $response,
            );
        } catch (ApiException $e) {
            switch ($e->getCode()) {
                case 200:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Repull\Model\RefreshAirbnbResolutions200Response',
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
            }
        
            throw $e;
        }
    }

    /**
     * Operation refreshAirbnbResolutionsAsync
     *
     * Refresh Airbnb resolutions from Airbnb
     *
     * @param  string|null $x_schema Apply a custom or built-in schema to transform the response. Built-in: &#x60;native&#x60; (default), &#x60;calry&#x60;, &#x60;calry-v1&#x60;. Custom: any schema name created via &#x60;POST /v1/schema/custom&#x60;. Unknown / inactive schema names fall back to &#x60;native&#x60;. (optional)
     * @param  \Repull\Model\RefreshAirbnbResolutionsRequest|null $refresh_airbnb_resolutions_request (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['refreshAirbnbResolutions'] to see the possible values for this operation
     *
     * @throws InvalidArgumentException
     * @return PromiseInterface
     */
    public function refreshAirbnbResolutionsAsync(
        ?string $x_schema = null,
        ?\Repull\Model\RefreshAirbnbResolutionsRequest $refresh_airbnb_resolutions_request = null,
        string $contentType = self::contentTypes['refreshAirbnbResolutions'][0]
    ): PromiseInterface
    {
        return $this->refreshAirbnbResolutionsAsyncWithHttpInfo($x_schema, $refresh_airbnb_resolutions_request, $contentType)
            ->then(
                function ($response) {
                    return $response[0];
                }
            );
    }

    /**
     * Operation refreshAirbnbResolutionsAsyncWithHttpInfo
     *
     * Refresh Airbnb resolutions from Airbnb
     *
     * @param  string|null $x_schema Apply a custom or built-in schema to transform the response. Built-in: &#x60;native&#x60; (default), &#x60;calry&#x60;, &#x60;calry-v1&#x60;. Custom: any schema name created via &#x60;POST /v1/schema/custom&#x60;. Unknown / inactive schema names fall back to &#x60;native&#x60;. (optional)
     * @param  \Repull\Model\RefreshAirbnbResolutionsRequest|null $refresh_airbnb_resolutions_request (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['refreshAirbnbResolutions'] to see the possible values for this operation
     *
     * @throws InvalidArgumentException
     * @return PromiseInterface
     */
    public function refreshAirbnbResolutionsAsyncWithHttpInfo(
        ?string $x_schema = null,
        ?\Repull\Model\RefreshAirbnbResolutionsRequest $refresh_airbnb_resolutions_request = null,
        string $contentType = self::contentTypes['refreshAirbnbResolutions'][0]
    ): PromiseInterface
    {
        $returnType = '\Repull\Model\RefreshAirbnbResolutions200Response';
        $request = $this->refreshAirbnbResolutionsRequest($x_schema, $refresh_airbnb_resolutions_request, $contentType);

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
     * Create request for operation 'refreshAirbnbResolutions'
     *
     * @param  string|null $x_schema Apply a custom or built-in schema to transform the response. Built-in: &#x60;native&#x60; (default), &#x60;calry&#x60;, &#x60;calry-v1&#x60;. Custom: any schema name created via &#x60;POST /v1/schema/custom&#x60;. Unknown / inactive schema names fall back to &#x60;native&#x60;. (optional)
     * @param  \Repull\Model\RefreshAirbnbResolutionsRequest|null $refresh_airbnb_resolutions_request (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['refreshAirbnbResolutions'] to see the possible values for this operation
     *
     * @throws InvalidArgumentException
     * @return \GuzzleHttp\Psr7\Request
     */
    public function refreshAirbnbResolutionsRequest(
        ?string $x_schema = null,
        ?\Repull\Model\RefreshAirbnbResolutionsRequest $refresh_airbnb_resolutions_request = null,
        string $contentType = self::contentTypes['refreshAirbnbResolutions'][0]
    ): Request
    {




        $resourcePath = '/v1/channels/airbnb/resolutions';
        $formParams = [];
        $queryParams = [];
        $headerParams = [];
        $httpBody = '';
        $multipart = false;


        // header params
        if ($x_schema !== null) {
            $headerParams['X-Schema'] = ObjectSerializer::toHeaderValue($x_schema);
        }



        $headers = $this->headerSelector->selectHeaders(
            ['application/json', ],
            $contentType,
            $multipart
        );

        // for model (json/xml)
        if (isset($refresh_airbnb_resolutions_request)) {
            if (stripos($headers['Content-Type'], 'application/json') !== false) {
                # if Content-Type contains "application/json", json_encode the body
                $httpBody = \GuzzleHttp\Utils::jsonEncode(ObjectSerializer::sanitizeForSerialization($refresh_airbnb_resolutions_request));
            } else {
                $httpBody = $refresh_airbnb_resolutions_request;
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
