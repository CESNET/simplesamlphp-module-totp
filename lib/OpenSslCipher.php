<?php

declare(strict_types=1);

namespace SimpleSAML\Module\totp;

class OpenSslCipher implements Cipher
{
    /**
     * Table of pairs in the form of (cipherAlgorithm, hashAlgorithm). The newest one (with highest index) is used for
     * encryption, but the old ones can still be decrypted.
     */
    private const CIPHERS = [['aes-256-ecb', 'sha512']];

    /**
     * Length of the second key.
     */
    private const SECOND_KEY_LENGTH = 64;

    /**
     * The cipher index is padded to this length.
     */
    private const CIPHER_INDEX_PADDING = 3;

    /**
     * First key, used for encryption.
     */
    private $key32;

    /**
     * Second key, used for hash.
     */
    private $key64;

    /**
     * @override
     *
     * @param mixed $moduleConfig
     */
    public function __construct($moduleConfig)
    {
        $this->key32 = $moduleConfig->getString('key1');
        $this->key64 = $moduleConfig->getString('key2');
    }

    /**
     * @override
     *
     * @param mixed $data
     */
    public function encrypt($data)
    {
        $cipherIndex = count(self::CIPHERS) - 1;
        list($cipherAlgorithm, $hashAlgorithm) = self::CIPHERS[$cipherIndex];

        $first_key = base64_decode($this->key32, true);
        $second_key = base64_decode($this->key64, true);

        $first_encrypted = openssl_encrypt($data, $cipherAlgorithm, $first_key, OPENSSL_RAW_DATA);
        $second_encrypted = hash_hmac($hashAlgorithm, $first_encrypted, $second_key, true);
        $paddedCipherIndex = self::padNumber($cipherIndex, self::CIPHER_INDEX_PADDING);

        return base64_encode($paddedCipherIndex . $second_encrypted . $first_encrypted);
    }

    /**
     * @override
     *
     * @param mixed $data
     */
    public function decrypt($data)
    {
        $first_key = base64_decode($this->key32, true);
        $second_key = base64_decode($this->key64, true);
        $mix = base64_decode($data, true);

        $cipherIndex = intval(substr($mix, 0, self::CIPHER_INDEX_PADDING));
        list($cipherAlgorithm, $hashAlgorithm) = self::CIPHERS[$cipherIndex];
        $mix = substr($mix, self::CIPHER_INDEX_PADDING);

        $second_encrypted = substr($mix, 0, self::SECOND_KEY_LENGTH);
        $first_encrypted = substr($mix, self::SECOND_KEY_LENGTH);
        $data = openssl_decrypt($first_encrypted, $cipherAlgorithm, $first_key, OPENSSL_RAW_DATA);
        $second_encrypted_new = hash_hmac($hashAlgorithm, $first_encrypted, $second_key, true);

        if (hash_equals($second_encrypted, $second_encrypted_new)) {
            return $data;
        }

        return false;
    }

    /**
     * Pad a number to a specified number of characters by prepending zeros.
     *
     * @param mixed $number
     */
    private static function padNumber($number, int $pad_length)
    {
        $number = strval($number);

        return str_pad($number, $pad_length, '0', STR_PAD_LEFT);
    }
}
