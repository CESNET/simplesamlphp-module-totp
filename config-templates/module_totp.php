<?php
$config = [
    /**
     * Class used for storing generated tokens.
     * @var SimpleSAML\Module\totp\Storage
     */
    'storage' => '\\SimpleSAML\\Module\\totp\\Storage\\StoreStorage',
    /** Authentication source for the token generation page. */
    'authSource' => 'default-sp',
    /** User attribute with the user id. */
    'userIdAttribute' => 'uid',
    /** Issuer in the QR code. */
    'issuer' => 'SimpleSAMLphp dev',
    /** Digits used for TOTP. */
    //'digits' => 6,
    /** Period for TOTP. */
    //'period' => 30,
    /** Hash algorithm for TOTP. */
    //'algorithm' => 'sha1',
    /** Authentication context used for token generation page authentication. */
    //'authnContext' => 'https://refeds.org/profile/mfa',
];
