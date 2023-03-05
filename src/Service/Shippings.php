<?php

namespace eDiasoft\Tyre24\Service;

use eDiasoft\Tyre24\Service;
use eDiasoft\Tyre24\Tyre24Client;

class Shippings extends Service
{
    public function shippingCompanies(string $country = 'de', array $filter = [])
    {
        return $this->httpClient->send(Tyre24Client::HTTP_GET, 'common/shipping-companies', [
            'country'   => $country
        ],[
            'filter'   => $filter
        ]);
    }
}
