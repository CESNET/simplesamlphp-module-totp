<?php

namespace SimpleSAML\Module\totp\Storage;

use SimpleSAML\Module\totp\Storage;
use SimpleSAML\Store;

class StoreStorage implements Storage
{
    public function store($userId, $secret, $label = '')
    {
        $store = Store::getInstance();
        $store->set('string', implode('_', ['totp', $userId, $label]), $secret);
    }
}
