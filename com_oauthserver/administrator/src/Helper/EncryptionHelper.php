<?php
/**
 * @package         Joomla.Administrator
 * @subpackage      com_oauthserver
 *
 * @copyright   (c) 2024. Webmasterskaya. <https://webmasterskaya.xyz>
 * @license         MIT; see LICENSE.txt
 **/

namespace Webmasterskaya\Component\OauthServer\Administrator\Helper;

class EncryptionHelper
{
    public static function generatingKeys(string $alg = 'sha512', int $length = 4096, int $type = OPENSSL_KEYTYPE_RSA, ?string $passphrase = null): array
    {
        $config = [
            "digest_alg"       => $alg,
            "private_key_bits" => $length,
            "private_key_type" => $type,
            "encrypt_key"      => !empty($passphrase)
        ];

        $private_key = '';
        $key         = openssl_pkey_new($config);

        openssl_pkey_export($key, $private_key, $passphrase);

        $public_key = openssl_pkey_get_details($key);

        return [$private_key, $public_key['key']];
    }
}
