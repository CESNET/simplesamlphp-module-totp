<?php

declare(strict_types=1);

$config = [
    /**
     * Class used for storing generated tokens.
     *
     * @var SimpleSAML\Module\totp\Storage
     */
    'storage' => '\\SimpleSAML\\Module\\totp\\Storage\\StoreStorage',
    /**
     * Authentication source for the token generation page.
     */
    'authSource' => 'default-sp',
    /**
     * User attribute with the user id.
     */
    'userIdAttribute' => 'uid',
    /**
     * Issuer in the QR code.
     */
    'issuer' => 'SimpleSAMLphp dev',
    /**
     * Digits used for TOTP.
     */
    'digits' => 6,
    /**
     * Period for TOTP.
     */
    'period' => 30,
    /**
     * Hash algorithm for TOTP.
     */
    'algorithm' => 'sha1',
    /**
     * Authentication context used for token generation page authentication.
     */
    'authnContext' => 'https://refeds.org/profile/mfa',
    /**
     * Encrypting class
     */
    'cipher' => '\\SimpleSAML\\Module\\totp\\OpenSslCipher',
    /**
     * URL to return to, for example if using authswitcher
     */
    'skip_redirect_url' => 'https://.../',

    'PerunStorage' => [
        'apiURL' => 'https://id.muni.cz/mfaapi/token',
        'OIDCKeyStore' => '/var/oidc-keystore.jwks',
        'OIDCKeyId' => 'rsa1',
        'OIDCTokenTimeout' => 300,
        'OIDCTokenAlg' => 'RS256',
        'OIDCIssuer' => 'https://oidc.muni.cz/oidc/',
        'OIDCClientId' => 'd574aeba-b2d0-4234-bcf0-53ec30b17ba4',
        'database.dsn' => '',
        'database.username' => '',
        'database.password' => '',
    ],

    'Signing' => [
        'signingEnabled' => true,
        'signingKeystore' => '/var/oidc-keystore.jwks',
        'signingAlg' => 'RS256',
        'signingKeyId' => 'rsa1',
        'supportedSignAlgs' => ['RS256'],
    ],
];
