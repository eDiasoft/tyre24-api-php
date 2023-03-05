<?php

namespace eDiasoft\Tyre24\Service;

use eDiasoft\Tyre24\Service;
use eDiasoft\Tyre24\Tyre24Client;

class Orders extends Service
{
    public function statuses(string $language = 'de_DE', array $filter = [])
    {
        return $this->httpClient->send(Tyre24Client::HTTP_GET, 'common/order-status', [
            'Accept-Language'   => $language
        ],[
            'filter'   => $filter
        ]);
    }

    public function order(string $country, string $order)
    {
        return $this->httpClient->send(Tyre24Client::HTTP_GET, 'common/order/' . $order, [
            'country'   => $country
        ]);
    }

    public function latestOrders(string $country = 'de', int $counter = 0, bool $demo = false, bool $no_tagging = true, string $order_role = null, int $tracking_number = 0)
    {
        return $this->httpClient->send(Tyre24Client::HTTP_GET, 'common/latestorders', [
            'country'   => $country
        ],[
            'counter'   => $counter,
            'demo'      => $demo,
            'no_tagging'    => $no_tagging,
            'order_role'    => $order_role,
            'tracking_number'   => $tracking_number
        ]);
    }
}
