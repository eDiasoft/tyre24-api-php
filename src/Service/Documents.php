<?php

namespace eDiasoft\Tyre24\Service;

use eDiasoft\Tyre24\Service;
use eDiasoft\Tyre24\Tyre24Client;

class Documents extends Service
{
    public const ACCEPTABLE_DOCUMENT_TYPES = array(
        'DEBIT_AGREEMENT',
        'BUSINESS_REGISTRATION',
        'SELLER_INVOICE',
        'DELIVERY_NOTE',
        'TRAVEL_COUPON'
    );

    public function document(string $type, string $order, string $country = 'de', array $filter = [], int $limit = 10, int $offset = 0,  string $sort = '-filter')
    {
        return $this->httpClient->send(Tyre24Client::HTTP_GET, 'common/document', [
            'country'           => $country
        ],[
            'filter'   => $filter,
            'limit'     => $limit,
            'offset'    => $offset,
            'order'     => $order,
            'sort'      => $sort,
            'type'      => $type
        ]);
    }
}
