<?php

namespace SimpleSAML\Module\totp;

interface Cipher
{
    /**
     * The constructor
     *
     * @param $moduleConfig - module's configuration
     */
    public function __construct($moduleConfig);

    /**
     * Encrypt the data
     *
     * @return string
     */
    public function encrypt($data);

    /**
     * Decrypt the data
     *
     * @return might return false if data is currupted, string otherwise
     */
    public function decrypt($data);
}
