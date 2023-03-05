<?php

namespace eDiasoft\Tyre24\Service;

use eDiasoft\Tyre24\Exceptions\AuthenticationException;
use eDiasoft\Tyre24\Service;

class Authenticate extends Service
{
    private ?string $username;
    private ?string $password;
    private ?string $token = null;
    public function __construct(string $username, string $password, string $token = null)
    {
        parent::__construct($this);

        $this->username = $username;
        $this->password = $password;
        $this->token = $token ?? $this->retrievingToken();
    }

    private function retrievingToken(): string
    {
        if($this->username && $this->password)
        {
            $data = $this->httpClient->sendGetToken($this->username, $this->password);

            return $data->data->token;
        }

        throw new AuthenticationException('Either username or password are empty.');
    }

    public function token()
    {
        return $this->token;
    }
}
