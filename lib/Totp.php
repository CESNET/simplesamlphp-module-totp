<?php

namespace SimpleSAML\Module\totp;

use Exception;
use RobThree\Auth\Providers\Qr\BaconQrCodeProvider;
use RobThree\Auth\TwoFactorAuth;
use SimpleSAML\Auth\Simple;
use SimpleSAML\Configuration;

class Totp
{
    private const ISSUER = 'issuer';

    private const DIGITS = 'digits';

    private const PERIOD = 'period';

    private const ALGORITHM = 'algorithm';

    private const AUTH_SOURCE = 'authSource';

    private const USER_ID_ATTRIBUTE = 'userIdAttribute';

    private const CONFIG_FILE = 'module_totp.php';

    private const AUTHN_CONTEXT = 'authnContext';

    private const STORAGE = 'storage';

    private const DEFAULTS = [
        self::ISSUER => 'SimpleSAMLphp dev',
        self::DIGITS => 6,
        self::PERIOD => 30,
        self::ALGORITHM => 'sha1',
        self::USER_ID_ATTRIBUTE => 'uid',
        self::AUTHN_CONTEXT => 'https://refeds.org/profile/mfa',
        self::STORAGE => '\\SimpleSAML\\Module\\totp\\Storage\\StoreStorage',
    ];

    private $tfa;

    private $config;

    public function __construct()
    {
        $this->config = self::getConfig();
        $this->tfa = new TwoFactorAuth(
            $this->getString(self::ISSUER),
            $this->getString(self::DIGITS),
            $this->getString(self::PERIOD),
            $this->getString(self::ALGORITHM),
            new BaconQrCodeProvider()
        );
    }

    public function getQRCodeImageAsDataUri($label, $secret)
    {
        return $this->tfa->getQRCodeImageAsDataUri($label, $secret);
    }

    public function getUserId()
    {
        $authSource = $this->config->getString(self::AUTH_SOURCE);
        $as = new Simple($authSource);
        $authnContext = $this->getString(self::AUTHN_CONTEXT);
        $as->requireAuth([
            'ForceAuthn' => true,
            'saml:AuthnContextClassRef' => $authnContext,
        ]);
        $attributes = $as->getAttributes();
        $userIdAttribute = $this->getString(self::USER_ID_ATTRIBUTE);
        if (empty($attributes[$userIdAttribute])) {
            throw new Exception('Missing attribute ' . $userIdAttribute);
        }
        return $attributes[$userIdAttribute][0];
    }

    public function createSecret()
    {
        return $this->tfa->createSecret();
    }

    public function storeSecret($userId, $secret, $label = '')
    {
        $className = $this->getString(self::STORAGE);
        $storage = new $className();
        $cipher = GetCipher::getInstance($this->config);
        $secret = $cipher->encrypt($secret);
        $storage->store($userId, $secret, $label);
    }

    public static function decryptSecret($secret)
    {
        $cipher = GetCipher::getInstance(self::getConfig());
        return $cipher->decrypt($secret);
    }

    public function verifyCode($secret, $code)
    {
        return $this->tfa->verifyCode($secret, $code);
    }

    private static function getConfig()
    {
        return Configuration::getOptionalConfig(self::CONFIG_FILE);
    }

    private function getString($name)
    {
        return $this->config->getString($name, self::DEFAULTS[$name]);
    }
}
