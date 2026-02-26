<?php
declare(strict_types=1);

// +----------------------------------------------------------------------
// | Kucoder [ MAKE WEB FAST AND EASY ]
// +----------------------------------------------------------------------
// | Copyright (c) 2026~9999 https://kucoder.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: kucoder
// +----------------------------------------------------------------------


namespace kucoder\lib\sodium;

use Exception;
use kucoder\lib\KcConfig;
use SodiumException;
use Throwable;

/**
 * sodium对称加密类
 */
class KcSecretBox
{
    /**
     * sodium生成对称加密密钥
     */
    public static function generateKey(): string
    {
        return sodium_crypto_secretbox_keygen();
    }

    /**
     * sodium对称加密
     * @throws SodiumException
     * @throws Exception
     */
    public static function encrypt(string $message, string $key = ''): string
    {
        if (empty($key)) {
            $key = config('plugin.kucoder.kucoder.sodium.secretbox_key');
            if (empty($key)) {
                $key = self::generateKey();
                KcConfig::set(base_path('plugin/kucoder/config/app.php'), 'sodium.secretbox_key', $key);
            }
        }
        try {
            $nonce = random_bytes(SODIUM_CRYPTO_SECRETBOX_NONCEBYTES);
            $ciphertext = sodium_crypto_secretbox($message, $nonce, $key);
            // 加密完成后立即清除密钥
            sodium_memzero($key);
            return base64_encode($nonce . $ciphertext);
        } catch (Throwable $t) {
            sodium_memzero($key);
            throw new Exception($t->getMessage());
        }
    }

    /**
     * sodium对称解密
     * @throws SodiumException
     * @throws Exception
     */
    public static function decrypt(string $encryptedData, string $key = ''): string
    {
        try {
            if (empty($key)) {
                $key = config('plugin.kucoder.kucoder.sodium.secretbox_key');
                if (empty($key)) {
                    throw new Exception('sodium对称加密密钥未配置');
                }
            }
            $data = base64_decode($encryptedData);
            $nonce = substr($data, 0, SODIUM_CRYPTO_SECRETBOX_NONCEBYTES);
            $ciphertext = substr($data, SODIUM_CRYPTO_SECRETBOX_NONCEBYTES);
            $plaintext = sodium_crypto_secretbox_open($ciphertext, $nonce, $key);
            if ($plaintext === false) {
                throw new Exception('解密失败');
            }
            // 解密完成后立即清除密钥
            sodium_memzero($key);
            return $plaintext;
        } catch (Throwable $t) {
            sodium_memzero($key);
            throw new Exception($t->getMessage());
        }
    }
}