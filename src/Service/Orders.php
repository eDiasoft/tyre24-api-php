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

    public function setOrderStatus(string $order, string $country, int $statusId)
    {
        return $this->httpClient->send(Tyre24Client::HTTP_PATCH, 'seller/order/' . $order . '/status', [
            'Content-Type'      => 'application/json',
            'country'           => $country
        ], httpBody: json_encode([
            'status_id' => $statusId
        ]));
    }

    public function setTracking(string $order, string $country, int $shopCompanyId, array $parcelNumbers)
    {
        return $this->httpClient->send(Tyre24Client::HTTP_PATCH, 'seller/order/' . $order . '/tracking', [
            'Content-Type'      => 'application/json',
            'country'           => $country
        ], httpBody: json_encode([
            'shipping_company_id' => $shopCompanyId,
            'parcel_numbers'   => $parcelNumbers
        ]));
    }

    public function uploadInvoice(string $order, string $country, string $invoice)
    {
        return $this->httpClient->send(Tyre24Client::HTTP_PATCH, 'seller/order/' . $order . '/invoicepdf', [
            'Content-Type'      => 'application/json',
            'country'           => $country
        ], httpBody: json_encode([
            'pdf' => $invoice,
        ]));
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
