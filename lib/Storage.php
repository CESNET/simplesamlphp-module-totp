<?php

declare(strict_types=1);

namespace SimpleSAML\Module\totp;

interface Storage
{
    public function store($userId, $secret, $label = '');
}
