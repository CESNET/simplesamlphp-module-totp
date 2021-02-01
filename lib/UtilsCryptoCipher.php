<?php

namespace SimpleSAML\Module\totp;

use SimpleSAML\Utils\Crypto;

class UtilsCryptoCipher implements Cipher
{
    /**
     * @override
     */
    public function __construct($moduleConfig)
    {
    }

    /**
     * @override
     */
    public function encrypt($data)
    {
        return base64_encode(Crypto::aesEncrypt($data));
    }

    /**
     * @override
     */
    public function decrypt($data)
    {
        return Crypto::aesDecrypt(base64_decode($data, true));
    }
}
