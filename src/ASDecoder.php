<?php

namespace AppleSign;

use AppleSign\Apple\JWT;
use AppleSign\Apple\JWK;
use Exception;

/**
 * Decode Sign In with Apple identity token, and produce an ASPayload for
 * utilizing in backend auth flows to verify validity of provided user creds.
 *
 * @package  AppleSignIn\ASDecoder
 * @author   Griffin Ledingham <gcledingham@gmail.com>
 * @license  http://opensource.org/licenses/BSD-3-Clause 3-clause BSD
 * @link     https://github.com/GriffinLedingham/php-apple-signin
 */
class ASDecoder
{
    /**
     * Parse a provided Sign In with Apple identity token.
     *
     * @param string $identityToken
     * @return object|null
     * @throws Exception
     */
    public static function getAppleSignInPayload(string $identityToken)
    {
        $identityPayload = self::decodeIdentityToken($identityToken);

        return new ASPayload($identityPayload);
    }

    /**
     * Decode the Apple encoded JWT using Apple's public key for the signing.
     *
     * @param string $identityToken
     * @return object
     */
    public static function decodeIdentityToken(string $identityToken)
    {
        $payload = JWT::decode($identityToken);
        return $payload;
    }

    /**
     * Fetch Apple's public key from the auth/keys REST API to use to decode
     * the Sign In JWT.
     *
     * @param $kid
     * @return array
     * @throws Exception
     */
    public static function fetchPublicKey($kid): array
    {
        $publicKeys = file_get_contents('https://appleid.apple.com/auth/keys');
        $decodedPublicKeys = json_decode($publicKeys, true);
        if (!isset($decodedPublicKeys['keys']) || count($decodedPublicKeys['keys']) < 1) {
            throw new Exception('Invalid key format.');
        }
        foreach ($decodedPublicKeys['keys'] as $value) {
            if ($value['kid'] === $kid) {
                $parsedKeyData = $value;
            }
        }
        if (!isset($parsedKeyData)) {
            throw new Exception('kid not found.');
        }
        $parsedPublicKey = JWK::parseKey($parsedKeyData);
        $publicKeyDetails = openssl_pkey_get_details($parsedPublicKey);

        if (!isset($publicKeyDetails['key'])) {
            throw new Exception('Invalid public key details.');
        }

        return [
            'publicKey' => $publicKeyDetails['key'],
            'alg' => $parsedKeyData['alg']
        ];
    }
}
