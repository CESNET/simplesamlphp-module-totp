<?php

namespace SimpleSAML\Module\totp;

interface Storage
{
    public function store($userId, $secret, $label = '');
}
