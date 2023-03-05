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
        $this->messages = new Messages();
        $this->articles = new Articles();
        $this->documents = new Documents();
        $this->orders = new Orders();
        $this->shippings = new Shippings();
    }

//    public function getOrders()
//    {
//        $this->handler_stack->push($this->handleAuthorizationHeader());
//
//        try {
//            $content = $this->guzzle->get('latestorders', [
//                'query' => [
//                    'country' => 'de'
//                ]
//            ])->getBody()->getContents();
//
//            return new Orders(json_decode($content, true)['data']);
//        }catch (ClientException $e) {
//            dd($e->getResponse()->getHeaders());
//        }
//    }
//
//    private function retriveNewAuthenticateToken()
//    {
//        $result = $this->guzzle->get('login', [
//            'auth'  => [
//                env('TYRE24_' . strtoupper($this->shop->name) . '_USER'),
//                env('TYRE24_' . strtoupper($this->shop->name) . '_PASSWORD')
//            ]
//        ]);
//
//        $data = json_decode($result->getBody()->getContents());
//
//        return Token::create([
//            'shop_id'           => $this->shop->id,
//            'tokenable_id'      => $this->platform->id,
//            'tokenable_type'    => Platform::class,
//            'key'               => $data->data->token,
//            'valid_until'       => Carbon::parse($data->data->expire_date)
//        ]);
//    }
//
//    private function handleAuthorizationHeader()
//    {
//        return function (callable $handler)
//        {
//            return function (RequestInterface $request, array $options) use ($handler)
//            {
//                if($this->token)
//                {
//                    $request = $request->withHeader('X-AUTH-TOKEN', $this->token->key);
//                }
//
//                return $handler($request, $options);
//            };
//        };
//    }

    public static function authenticate(string $username, string $password, string $token = null): Tyre24Client
    {
        return new self($username, $password, $token);
    }
}
