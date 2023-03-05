<?php

namespace eDiasoft\Tyre24\HttpAdapter;

use Composer\CaBundle\CaBundle;
use eDiasoft\Tyre24\Exceptions\ApiException;
use eDiasoft\Tyre24\Service;
use eDiasoft\Tyre24\Tyre24Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\RequestOptions as GuzzleRequestOptions;
use Psr\Http\Message\ResponseInterface;

class GuzzleHttpAdapter implements HttpAdapterInterface
{
    public const DEFAULT_TIMEOUT = 10;
    public const DEFAULT_CONNECT_TIMEOUT = 2;
    public const HTTP_NO_CONTENT = 204;

    protected ClientInterface $httpClient;
    protected Service $service;

    public function __construct(ClientInterface $httpClient, Service $service)
    {
        $this->httpClient = $httpClient;
        $this->service = $service;
    }

    public function send(string $httpMethod, string $url, array $headers = [], array $queries = [], string $httpBody = '')
    {
        $headers["Accept"] = "application/vnd.saitowag.api+json;version=" . $this->service->getApiVersion();
        $headers["X-AUTH-TOKEN"] = $this->service->authenticate()->token();

        $request = new Request($httpMethod, Tyre24Client::API_ENDPOINT . $url, $headers, $httpBody);

        try {
            $response = $this->httpClient->send($request, [
                'http_errors' => false,
                'query'         => $queries
            ]);
        } catch (GuzzleException $e) {
            throw new ApiException($e->getMessage(), $e->getCode());
        }

        return $this->parseResponseBody($response);
    }

    public function sendGetToken(string $username, string $password)
    {
        $headers = array(
            'Accept'    => "application/vnd.saitowag.api+json;version=" . $this->service->getApiVersion()
        );

        $request = new Request('GET', Tyre24Client::API_ENDPOINT . 'common/login', $headers);

        try {
            $response = $this->httpClient->send($request, [
                'http_errors' => false,
                'auth'  => [
                    $username,
                    $password
                ]
            ]);
        } catch (GuzzleException $e) {
            throw new ApiException($e->getMessage(), $e->getCode());
        }

        return $this->parseResponseBody($response);
    }

    private function parseResponseBody(ResponseInterface $response)
    {
        $body = (string) $response->getBody();

        if (empty($body))
        {
            if ($response->getStatusCode() === self::HTTP_NO_CONTENT)
            {
                return null;
            }

            throw new ApiException("No response body found.");
        }

        $object = @json_decode($body);

        if (json_last_error() !== JSON_ERROR_NONE)
        {
            throw new ApiException("Unable to decode Tyre24 response: '{$body}'.");
        }

        return $object;
    }

    public static function createDefault(Service $service)
    {
        $retryMiddlewareFactory = new GuzzleRetryMiddlewareFactory;
        $handlerStack = HandlerStack::create();
        $handlerStack->push($retryMiddlewareFactory->retry());

        $client = new Client([
            GuzzleRequestOptions::VERIFY => CaBundle::getBundledCaBundlePath(),
            GuzzleRequestOptions::TIMEOUT => self::DEFAULT_TIMEOUT,
            GuzzleRequestOptions::CONNECT_TIMEOUT => self::DEFAULT_CONNECT_TIMEOUT,
            'handler' => $handlerStack,
        ]);

        return new GuzzleHttpAdapter($client, $service);
    }
}
