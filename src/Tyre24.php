<?php

namespace app\Tyre24\src;

use App\Models\Platform;
use App\Models\Shop;
use App\Models\Token;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\HandlerStack;
use Illuminate\Support\Carbon;
use Psr\Http\Message\RequestInterface;

class Tyre24
{
    const END_POINT = "https://api-b2b.alzura.com/common/";

    private Shop $shop;
    private Platform $platform;
    private ?Token $token;
    private Client $guzzle;
    private HandlerStack $handler_stack;

    public function __construct(Shop $shop, Platform $platform, Token $token = null)
    {
        $this->shop = $shop;
        $this->platform = $platform;
        $this->token = $token;

        $this->handler_stack = HandlerStack::create();

        $this->guzzle = guzzle([
            'handler'           => $this->handler_stack,
            'base_uri'          => (env('APP_ENV') == 'production')? self::END_POINT : 'http://cms.zeus.local/tyre24/',
            'headers'           => [
                'Accept' => 'application/vnd.saitowag.api+json;version=' . env('TYRE24_API_VERSION')
            ],
        ]);

        if(!$token)
        {
            $this->token = $this->retriveNewAuthenticateToken();
        }
    }

    public function getOrders()
    {
        $this->handler_stack->push($this->handleAuthorizationHeader());

        try {
            $content = $this->guzzle->get('latestorders', [
                'query' => [
                    'country' => 'de'
                ]
            ])->getBody()->getContents();

            return new Orders(json_decode($content, true)['data']);
        }catch (ClientException $e) {
            dd($e->getResponse()->getHeaders());
        }
    }

    private function retriveNewAuthenticateToken()
    {
        $result = $this->guzzle->get('login', [
            'auth'  => [
                env('TYRE24_' . strtoupper($this->shop->name) . '_USER'),
                env('TYRE24_' . strtoupper($this->shop->name) . '_PASSWORD')
            ]
        ]);

        $data = json_decode($result->getBody()->getContents());

        return Token::create([
            'shop_id'           => $this->shop->id,
            'tokenable_id'      => $this->platform->id,
            'tokenable_type'    => Platform::class,
            'key'               => $data->data->token,
            'valid_until'       => Carbon::parse($data->data->expire_date)
        ]);
    }

    private function handleAuthorizationHeader()
    {
        return function (callable $handler)
        {
            return function (RequestInterface $request, array $options) use ($handler)
            {
                if($this->token)
                {
                    $request = $request->withHeader('X-AUTH-TOKEN', $this->token->key);
                }

                return $handler($request, $options);
            };
        };
    }

    public static function authenticate(Shop $shop, Platform $platform): Tyre24
    {
        $token = $shop->tokens()->where(function ($query) use ($platform){
            $query->where('tokenable_type', Platform::class)->where('tokenable_id', $platform->id);
        })->where('valid_until', '>', now()->format('Y-m-d'))->first();

        return new self($shop, $platform, $token);
    }
}
