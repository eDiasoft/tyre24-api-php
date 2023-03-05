<?php

namespace eDiasoft\Tyre24;

use eDiasoft\Tyre24\HttpAdapter\HttpAdapterInterface;
use eDiasoft\Tyre24\HttpAdapter\HttpAdapterPicker;
use eDiasoft\Tyre24\Service\Authenticate;

class Service
{
    private $apiVersion = '1.1';
    protected HttpAdapterInterface $httpClient;

    public function __construct(Authenticate $authenticate)
    {
        $this->httpClient = (new HttpAdapterPicker())->pickHttpAdapter($this);
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
}
