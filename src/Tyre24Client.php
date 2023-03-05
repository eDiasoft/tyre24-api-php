<?php

namespace eDiasoft\Tyre24;

use eDiasoft\Tyre24\Service\Articles;
use eDiasoft\Tyre24\Service\Authenticate;
use eDiasoft\Tyre24\Service\Documents;
use eDiasoft\Tyre24\Service\Messages;
use eDiasoft\Tyre24\Service\Orders;
use eDiasoft\Tyre24\Service\Shippings;

class Tyre24Client
{
    public const API_ENDPOINT = "https://api-b2b.alzura.com/";
    public const HTTP_GET = "GET";
    public const HTTP_POST = "POST";
    public const HTTP_DELETE = "DELETE";
    public const HTTP_PATCH = "PATCH";

    private Authenticate $authenticate;

    public Messages $messages;
    public Articles $articles;
    public Documents $documents;
    public Orders $orders;
    public Shippings $shippings;

    public function __construct(string $username, string $password, string $token = null)
    {
        $this->authenticate = new Authenticate($username, $password, $token);

        $this->setServices();
    }

    private function setServices()
    {
        $this->messages = new Messages($this->authenticate);
        $this->articles = new Articles($this->authenticate);
        $this->documents = new Documents($this->authenticate);
        $this->orders = new Orders($this->authenticate);
        $this->shippings = new Shippings($this->authenticate);
    }

    public static function authenticate(string $username, string $password, string $token = null): Tyre24Client
    {
        return new self($username, $password, $token);
    }
}
