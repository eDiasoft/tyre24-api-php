<?php

namespace eDiasoft\Tyre24\Service;

use eDiasoft\Tyre24\Exceptions\AuthenticationException;
use eDiasoft\Tyre24\Service;

class Authenticate extends Service
{
    private ?string $username;
    private ?string $password;
    private ?string $token;
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
            return $this->httpClient->send('GET', 'common/login');
        }

        throw new AuthenticationException('Either username or password are empty.');
    }

    public function token()
    {
        return $this->token;
    }
}
