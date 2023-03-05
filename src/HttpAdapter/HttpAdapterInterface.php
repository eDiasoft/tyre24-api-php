<?php

namespace eDiasoft\Tyre24\HttpAdapter;

interface HttpAdapterInterface
{
    public function send(string $httpMethod, string $url, array $headers = [], string $httpBody = '');
}
