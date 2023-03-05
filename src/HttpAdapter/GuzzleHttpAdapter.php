<?php

namespace eDiasoft\Tyre24\HttpAdapter;

use Composer\CaBundle\CaBundle;
use eDiasoft\Tyre24\Service;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\RequestOptions as GuzzleRequestOptions;
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

    public function send(string $httpMethod, string $url, array $headers = [], string $httpBody = '')
    {
        // TODO: Implement send() method.
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
