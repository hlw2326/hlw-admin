<?php

declare(strict_types=1);

/**
 * +----------------------------------------------------------------------
 * | HlwAdmin Plugin for HlwAdmin
 * +----------------------------------------------------------------------
 * | 版权所有 2014~2026 HlwAdmin [ hlwadmin.top ]
 * +----------------------------------------------------------------------
 * | 官方网站: https://hlwadmin.top
 * +----------------------------------------------------------------------
 */

$decode = static function (string $name): string {
    $value = trim((string) env($name, ''));
    if ($value === '') {
        return '';
    }
    return base64_decode($value, true) ?: '';
};

return [
    // 默认加密驱动，可选 aes、rsa、sm4、hmac
    'type' => env('CRYPT_TYPE', 'aes'),
    // AES 对称加密配置
    'aes'  => [
        'key'    => env('CRYPT_AES_KEY', ''),
        'cipher' => env('CRYPT_AES_CIPHER', 'aes-256-gcm'),
    ],
    // 国密 SM4 对称加密配置
    'sm4'  => [
        'key' => env('CRYPT_SM4_KEY', ''),
    ],
    // HMAC 签名配置
    'hmac' => [
        'key'  => env('CRYPT_HMAC_KEY', ''),
        'algo' => env('CRYPT_HMAC_ALGO', 'sha256'),
    ],
    // RSA 密钥配置：CRYPT_RSA_PUBLIC / CRYPT_RSA_PRIVATE 使用 Base64 编码后的完整 PEM 文本
    'rsa'  => [
        'public_key'  => $decode('CRYPT_RSA_PUBLIC'),
        'private_key' => $decode('CRYPT_RSA_PRIVATE'),
        'passphrase'  => env('CRYPT_RSA_PASSPHRASE', ''),
        'openssl_cnf' => env('CRYPT_OPENSSL_CNF', ''),
    ],
    // API 信封加密与验签配置
    'api'  => [
        'sign_secret'   => env('CRYPT_API_SIGN_SECRET', ''),
        'time_tolerance' => env('CRYPT_API_TIME_TOLERANCE', 60000),
        'nonce_expire'   => env('CRYPT_API_NONCE_EXPIRE', 120),
    ],
];
