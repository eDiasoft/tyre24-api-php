<?php

namespace eDiasoft\Tyre24\Service;

use eDiasoft\Tyre24\Service;
use eDiasoft\Tyre24\Tyre24Client;

class Articles extends Service
{
    public function markAsRead(array $filter = [], string $accept_language = 'de_DE', string $country = 'de')
    {
        return $this->httpClient->send(Tyre24Client::HTTP_GET, 'common/article-types', [
            'Accept-Language'   => $accept_language,
            'country'           => $country
        ],[
            'filter'   => $filter
        ]);
    }
}
