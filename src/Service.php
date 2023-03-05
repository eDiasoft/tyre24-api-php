<?php

namespace eDiasoft\Tyre24;

use eDiasoft\Tyre24\HttpAdapter\HttpAdapterInterface;
use eDiasoft\Tyre24\HttpAdapter\HttpAdapterPicker;
use eDiasoft\Tyre24\Service\Authenticate;

class Service
{
    private $apiVersion = '1.1';
    protected HttpAdapterInterface $httpClient;
    protected Authenticate $authenticate;
    public function __construct(Authenticate $authenticate)
    {
        $this->httpClient = (new HttpAdapterPicker())->pickHttpAdapter($this);
        $this->authenticate = $authenticate;
    }

    public function setApiVersion(string $apiVersion)
    {
        $this->apiVersion = $apiVersion;

        return $this;
    }
    public function getApiVersion()
    {
        return $this->apiVersion;
    }

    public function authenticate(): Authenticate
    {
        return $this->authenticate;
    }
}
