<?php

declare(strict_types=1);

namespace SimpleSAML\Module\totp\Auth\Process;

use Jose\Component\Core\AlgorithmManager;
use Jose\Component\Core\JWKSet;
use Jose\Component\Signature\JWSVerifier;
use Jose\Component\Signature\Serializer\CompactSerializer;
use Jose\Component\Signature\Serializer\JWSSerializerManager;
use SimpleSAML\Auth\ProcessingFilter;
use SimpleSAML\Configuration;
use SimpleSAML\Logger;
use SimpleSAML\Module\totp\GetCipher;
use SimpleSAML\Module\totp\Storage\PerunStorage;

class DecryptSecrets extends ProcessingFilter
{
    private const MODULE_CONFIG_FILE = 'module_totp.php';

    private $signing_enabled = false;

    private $user_id_attribute = 'uid';

    private $signing_keystore = null;

    private $signing_key_id = 'rsa1';

    private $supported_sign_algs = ['RS256'];

    private $secret_attr = 'totp_secret';

    /**
     * Initialize the filter.
     *
     * @param array $config  Configuration information about this filter.
     * @param mixed $reserved  For future use
     */
    public function __construct(array $config, $reserved)
    {
        parent::__construct($config, $reserved);

        $config = Configuration::getOptionalConfig(self::MODULE_CONFIG_FILE);
        $signing_config = Configuration::loadFromArray(
            Configuration::getOptionalConfig(self::MODULE_CONFIG_FILE)->getArray('Signing', [])
        );
        $this->signing_enabled = $signing_config->getBoolean('signingEnabled', $this->signing_enabled);
        $this->signing_keystore = $signing_config->getString('signingKeystore', $this->signing_keystore);
        $this->signing_key_id = $signing_config->getString('signingKeyId', $this->signing_key_id);
        $this->supported_sign_algs = $signing_config->getArray('supportedSignAlgs', $this->supported_sign_algs);
        $this->user_id_attribute = $config->getString('userIdAttribute', $this->user_id_attribute);
        $this->secret_attr = $config->getString('secretAttr', $this->secret_attr);
    }

    /**
     * Apply DecryptSecrets filter
     *
     * @param array $state  The current state
     */
    public function process(&$state)
    {
        $attributes = &$state['Attributes'];

        if (! empty($attributes['mfaTokens'])) {
            foreach ($attributes['mfaTokens'] as $mfaToken) {
                $token = json_decode($mfaToken, true);
                if ($token['type'] === 'TOTP') {
                    $secret = $this->decryptTokenData($token['data'], $attributes);
                    if ($secret) {
                        $attributes[$this->secret_attr][] = $secret;
                    }
                }
            }
        }
    }

    private function decryptTokenData($tokenData, $attributes)
    {
        # isset($tokenData['payload']) condition for backward compatibility (unsigned tokens will be skipped)
        if ($this->signing_enabled && ! isset($tokenData['secret'])) {
            $userId = $attributes[$this->user_id_attribute][0];
            $sign_jwkset = JWKSet::createFromJson(file_get_contents($this->signing_keystore));
            $sign_jwk = $sign_jwkset->get($this->signing_key_id);

            $algorithmManager = new AlgorithmManager([]);

            foreach ($this->supported_sign_algs as $alg) {
                $algorithmManager->add(PerunStorage::getSignatureAlgorithm($alg));
            }

            $jwsVerifier = new JWSVerifier($algorithmManager);

            $serializerManager = new JWSSerializerManager([new CompactSerializer()]);

            $jws = $serializerManager->unserialize($tokenData);
            if (! $jwsVerifier->verifyWithKey($jws, $sign_jwk, 0)) {
                Logger::debug('SIGNED SECRET NOT VERIFIED');
                return null;
            }
            $payload = $jws->getPayload();
            if ($payload) {
                $payload = json_decode($payload, true);
                if ($payload['userId'] !== $userId) {
                    Logger::debug('SIGNED SECRET HAS WRONG USER ID');
                    return null;
                }
                $cipher = GetCipher::getInstance(Configuration::getOptionalConfig(self::MODULE_CONFIG_FILE));
                return $cipher->decrypt($payload['secret']);
            }
        }
        # !isset($tokenData['payload']) condition for backward compatibility (signed tokens will be skipped)
        elseif (! $this->signing_enabled && isset($tokenData['secret'])) {
            $cipher = GetCipher::getInstance(Configuration::getOptionalConfig(self::MODULE_CONFIG_FILE));
            return $cipher->decrypt($tokenData['secret']);
        }
        return null;
    }
}
