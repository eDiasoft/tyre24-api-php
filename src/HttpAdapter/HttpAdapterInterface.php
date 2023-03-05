<?php

namespace eDiasoft\Tyre24\HttpAdapter;

interface HttpAdapterInterface
{
    public function send(string $httpMethod, string $url, array $headers = [], array $queries = [], string $httpBody = '');

    public function sendGetToken(string $username, string $password);
}
