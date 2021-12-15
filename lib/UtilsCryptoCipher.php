<?php

declare(strict_types=1);

namespace SimpleSAML\Module\totp;

use SimpleSAML\Utils\Crypto;

class UtilsCryptoCipher implements Cipher
{
    /**
     * @override
     *
     * @param mixed $moduleConfig
     */
    public function __construct($moduleConfig)
    {
    }

    /**
     * @override
     *
     * @param mixed $data
     */
    public function encrypt($data)
    {
        return base64_encode(Crypto::aesEncrypt($data));
    }

    /**
     * @override
     *
     * @param mixed $data
     */
    public function decrypt($data)
    {
        return Crypto::aesDecrypt(base64_decode($data, true));
    }
}
