<?php

declare(strict_types=1);

/**
 * Perun storage for module totp.
 */

namespace SimpleSAML\Module\totp\Storage;

use Jose\Component\Core\AlgorithmManager;
use Jose\Component\Core\JWKSet;
use Jose\Component\Signature\JWSBuilder;
use Jose\Component\Signature\Serializer\CompactSerializer;
use Jose\Easy\Build;
use SimpleSAML\Configuration;
use SimpleSAML\Logger;

class PerunStorage
{
    protected const CONFIG_FILE = 'module_totp.php';

    public function __construct()
    {
    }

    public function store($userId, $secret, $label = '')
    {
        $storage_config = Configuration::loadFromArray(
            Configuration::getOptionalConfig(self::CONFIG_FILE)->getArray('PerunStorage', [])
        );

        $token = [
            'type' => 'TOTP',
            'name' => empty($label) ? 'TOTP' : $label,
            'data' => $this->signTokenData([
                'secret' => $secret,
                'userId' => $userId,
            ]),
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $storage_config->getString('apiURL'));
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        $paramsJson = json_encode($token);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $paramsJson);
        $time = time();
        $jwkset = JWKSet::createFromJson(file_get_contents($storage_config->getString('OIDCKeyStore')));
        $jwk = $jwkset->get($storage_config->getString('OIDCKeyId', 'rsa1'));
        $id_token = Build::jws()
            ->exp($time + $storage_config->getInteger('OIDCTokenTimeout', 300))
            ->iat($time)
            ->nbf($time)
            ->alg($storage_config->getString('OIDCTokenAlg', 'RS256'))
            ->iss($storage_config->getString('OIDCIssuer'))
            ->aud($storage_config->getString('OIDCClientId'))
            ->sub($userId)
            ->claim('acr', 'https://refeds.org/profile/mfa')
            ->sign($jwk)
        ;
        curl_setopt(
            $ch,
            CURLOPT_HTTPHEADER,
            [
                'Content-Type: application/json',
                'Content-Length: ' . strlen($paramsJson),
                'Authorization: Bearer ' . $id_token,
            ]
        );
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        Logger::info(sprintf('Response from MFA API: %s', $response));
        curl_close($ch);
    }

    public static function getSignatureAlgorithm($className)
    {
        $classPath = sprintf('Jose\\Component\\Signature\\Algorithm\\%s', $className);
        if (!class_exists($classPath)) {
            throw new \Exception('Invalid algorithm specified: ' . $classPath);
        }

        return new $classPath();
    }

    private function signTokenData($data)
    {
        $signing_config = Configuration::loadFromArray(
            Configuration::getOptionalConfig(self::CONFIG_FILE)->getArray('Signing', [])
        );
        if ($signing_config->getBoolean('signingEnabled', false)) {
            $sign_jwkset = JWKSet::createFromJson(file_get_contents($signing_config->getString('signingKeystore')));
            $sign_jwk = $sign_jwkset->get($signing_config->getString('signingKeyId', 'rsa1'));

            $algorithmManager = new AlgorithmManager([]);

            $supported_sign_algs = $signing_config->getArray('supportedSignAlgs', ['RS256']);
            foreach ($supported_sign_algs as $alg) {
                $algorithmManager->add(self::getSignatureAlgorithm($alg));
            }

            $jwsBuilder = new JWSBuilder($algorithmManager);
            $data = json_encode($data);
            $jws = $jwsBuilder
                ->create()
                ->withPayload($data)
                ->addSignature($sign_jwk, [
                    'alg' => $signing_config->getString('signingAlg', 'RS256'),
                ])
                ->build()
            ;
            $serializer = new CompactSerializer();

            return $serializer->serialize($jws, 0);
        }

        return $data;
    }
}
