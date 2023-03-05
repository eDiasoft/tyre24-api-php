<?php

namespace eDiasoft\Tyre24\Service;

use eDiasoft\Tyre24\Service;
use eDiasoft\Tyre24\Tyre24Client;

class Messages extends Service
{
    public function markAsRead(array $message_ids = [], $content_type = 'application/json')
    {
        return $this->httpClient->send(Tyre24Client::HTTP_PATCH, 'common/message/read', [
            'Content-Type'   => $content_type
        ],[
            'message_ids'   => $message_ids
        ]);
    }
}
