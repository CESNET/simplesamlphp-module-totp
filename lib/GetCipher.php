<?php

declare(strict_types=1);

namespace SimpleSAML\Module\totp;

class GetCipher
{
    /**
     * Get the desired class implementing the \SimpleSAML\Module\totp\Cipher interface.
     *
     * @param mixed $moduleConfig
     */
    public static function getInstance($moduleConfig)
    {
        $cipherClass = $moduleConfig->getString('cipher', '\\SimpleSAML\\Module\\totp\\OpenSslCipher');

        return new $cipherClass($moduleConfig);
    }
}
