<?php

namespace eDiasoft\Tyre24\HttpAdapter;

use Composer\CaBundle\CaBundle;

use eDiasoft\Tyre24\Exceptions\ApiException;
use eDiasoft\Tyre24\Exceptions\CurlConnectTimeoutException;
use eDiasoft\Tyre24\Service;
use eDiasoft\Tyre24\Tyre24Client;

final class CurlHttpAdapter implements HttpAdapterInterface
{
    public const DEFAULT_TIMEOUT = 10;
    public const DEFAULT_CONNECT_TIMEOUT = 2;
    public const HTTP_NO_CONTENT = 204;
    public const MAX_RETRIES = 5;
    public const DELAY_INCREASE_MS = 1000;
    protected Service $service;
    public function __construct(Service $service)
    {
        $this->service = $service;
    }

    public function send(string $httpMethod, string $url, array $headers = [], string $httpBody = '')
    {
        for ($i = 0; $i <= self::MAX_RETRIES; $i++)
        {
            usleep($i * self::DELAY_INCREASE_MS);

            try {
                return $this->attemptRequest($httpMethod, $url, $headers, $httpBody);
            } catch (CurlConnectTimeoutException $e) {
                //
            }
        }

        throw new CurlConnectTimeoutException("Unable to connect to Tyre24. Maximum number of retries (". self::MAX_RETRIES .") reached.");
    }

    protected function attemptRequest($httpMethod, $url, $headers, $httpBody)
    {
        $curl = curl_init(Tyre24Client::API_ENDPOINT . $url);
        $headers["Accept"] = "application/vnd.saitowag.api+json;version=" . $this->service->getApiVersion();

        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $this->parseHeaders($headers));
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, self::DEFAULT_CONNECT_TIMEOUT);
        curl_setopt($curl, CURLOPT_TIMEOUT, self::DEFAULT_TIMEOUT);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($curl, CURLOPT_CAINFO, CaBundle::getBundledCaBundlePath());

        switch ($httpMethod) {
            case Tyre24Client::HTTP_POST:
                curl_setopt($curl, CURLOPT_POST, true);
                curl_setopt($curl, CURLOPT_POSTFIELDS,  $httpBody);

                break;
            case Tyre24Client::HTTP_GET:
                break;
            case Tyre24Client::HTTP_PATCH:
                curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'PATCH');
                curl_setopt($curl, CURLOPT_POSTFIELDS, $httpBody);

                break;
            case Tyre24Client::HTTP_DELETE:
                curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'DELETE');
                curl_setopt($curl, CURLOPT_POSTFIELDS,  $httpBody);

                break;
            default:
                throw new \InvalidArgumentException("Invalid http method: ". $httpMethod);
        }

        $startTime = microtime(true);
        $response = curl_exec($curl);
        $endTime = microtime(true);

        if ($response === false)
        {
            $executionTime = $endTime - $startTime;
            $curlErrorNumber = curl_errno($curl);
            $curlErrorMessage = "Curl error: " . curl_error($curl);

            if ($this->isConnectTimeoutError($curlErrorNumber, $executionTime))
            {
                throw new CurlConnectTimeoutException("Unable to connect to Tyre24. " . $curlErrorMessage);
            }

            throw new ApiException($curlErrorMessage);
        }

        $statusCode = curl_getinfo($curl, CURLINFO_RESPONSE_CODE);
        curl_close($curl);

        return $this->parseResponseBody($response, $statusCode, $httpBody);
    }

    protected function parseResponseBody($response, $statusCode, $httpBody)
    {
        if (empty($response))
        {
            if ($statusCode === self::HTTP_NO_CONTENT)
            {
                return null;
            }

            throw new ApiException("No response body found.");
        }

        $body = @json_decode($response);

        // GUARDS
        if (json_last_error() !== JSON_ERROR_NONE)
        {
            throw new ApiException("Unable to decode Tyre24 response: '{$response}'.");
        }

        if (isset($body->error))
        {
            throw new ApiException($body->error->message);
        }

        if ($statusCode >= 400)
        {
            $message = "Error executing API call ({$body->status}: {$body->title}): {$body->detail}";

            $field = null;

            if (! empty($body->field))
            {
                $field = $body->field;
            }

            if (isset($body->_links, $body->_links->documentation))
            {
                $message .= ". Documentation: {$body->_links->documentation->href}";
            }

            if ($httpBody)
            {
                $message .= ". Request body: {$httpBody}";
            }

            throw new ApiException($message, $statusCode, $field);
        }

        return $body;
    }

    protected function parseHeaders($headers)
    {
        $result = [];

        foreach ($headers as $key => $value)
        {
            $result[] = $key .': ' . $value;
        }

        return $result;
    }
}
