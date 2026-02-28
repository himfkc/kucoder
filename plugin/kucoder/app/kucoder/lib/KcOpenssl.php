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


namespace kucoder\lib;

use Exception;

/**
 * 高强度加密解密类
 * 支持 AES-256-GCM, ChaCha20-Poly1305, AES-256-CCM 三种认证加密算法
 */
class KcOpenssl
{
    /**
     * 支持的加密算法列表
     */
    private const SUPPORTED_CIPHERS = [
        1 => 'aes-256-gcm',      // 选项1
        2 => 'chacha20-poly1305', // 选项2
        3 => 'aes-256-ccm'       // 选项3
    ];

    /**
     * 算法配置
     */
    private const CIPHER_CONFIGS = [
        'aes-256-gcm' => [
            'key_length' => 32,
            'iv_length' => 16,
            'tag_length' => 16
        ],
        'chacha20-poly1305' => [
            'key_length' => 32,
            'iv_length' => 12,
            'tag_length' => 16
        ],
        'aes-256-ccm' => [
            'key_length' => 32,
            'iv_length' => 12,
            'tag_length' => 16
        ]
    ];

    /**
     * 默认加密算法（对应选项1）
     */
    private const DEFAULT_CIPHER_OPTION = 1;

    private string $selectedCipher;

    /**
     * 构造函数
     *
     * @param string $key 加密密钥
     * @param int $cipher 加密算法选项：1=AES-256-GCM, 2=ChaCha20-Poly1305, 3=AES-256-CCM
     * @throws Exception 如果密钥长度不符合要求或算法不支持
     */
    public function __construct(private string $key = '', int $cipher = self::DEFAULT_CIPHER_OPTION)
    {
        if (empty($key)) {
            $openssl_secret_key = get_env('openssl_secret_key') ?: '';
            $opensslKey = base64_decode($openssl_secret_key);
            if (!$opensslKey || strlen($opensslKey) !== 32) {
                $opensslKey = self::generateKey($cipher);
                KcConfig::set(base_path('plugin/kucoder/config/secret-key.php'), 'openssl.secret_key', base64_encode($opensslKey));
            }
            $this->key = $opensslKey;
        }
        // 验证并设置加密算法
        $this->setCipher($cipher);
        // 验证密钥长度
        $config = self::CIPHER_CONFIGS[$this->selectedCipher];
        $requiredLength = $config['key_length'];
        if (strlen($this->key) < $requiredLength) {
            throw new Exception("加密密钥长度不足，算法 {$this->selectedCipher} 需要 {$requiredLength} 字节，当前长度：" . strlen($this->key));
        }
    }

    /**
     * 设置加密算法
     *
     * @param int $cipherOption 算法选项：1,2,3
     * @throws Exception
     */
    private function setCipher(int $cipherOption): void
    {
        if (!isset(self::SUPPORTED_CIPHERS[$cipherOption])) {
            $supported = implode(', ', array_keys(self::SUPPORTED_CIPHERS));
            throw new Exception("不支持的加密算法选项: {$cipherOption}。支持的选项: {$supported}");
        }
        $this->selectedCipher = self::SUPPORTED_CIPHERS[$cipherOption];
        // 验证系统是否支持该算法
        if (!in_array($this->selectedCipher, openssl_get_cipher_methods(), true)) {
            throw new Exception("当前系统不支持加密算法: {$this->selectedCipher}，请检查 OpenSSL 版本");
        }
    }

    /**
     * 生成安全随机密钥
     *
     * @param int $cipher 加密算法选项：1=AES-256-GCM, 2=ChaCha20-Poly1305, 3=AES-256-CCM
     * @return string
     * @throws Exception
     */
    public static function generateKey(int $cipher = self::DEFAULT_CIPHER_OPTION): string
    {
        if (!isset(self::SUPPORTED_CIPHERS[$cipher])) {
            throw new Exception("不支持的加密算法选项: {$cipher}");
        }
        $cipherName = self::SUPPORTED_CIPHERS[$cipher];
        $keyLength = self::CIPHER_CONFIGS[$cipherName]['key_length'];
        $key = openssl_random_pseudo_bytes($keyLength);
        if ($key === false) {
            throw new Exception('生成随机密钥失败');
        }
        return $key;
    }

    /**
     * 加密数据
     *
     * @param string $plaintext 明文数据
     * @return string base64编码的加密数据（格式：IV + Tag + Ciphertext）
     * @throws Exception
     */
    public function encrypt(string $plaintext): string
    {
        $config = self::CIPHER_CONFIGS[$this->selectedCipher];
        $iv = random_bytes($config['iv_length']);
        $tag = '';

        // 根据算法类型调整参数
        if ($this->selectedCipher === 'aes-256-ccm') {
            // CCM 模式需要特殊处理
            $ciphertext = openssl_encrypt(
                $plaintext,
                $this->selectedCipher,
                $this->getCipherKey(),
                OPENSSL_RAW_DATA,
                $iv,
                $tag,
                '', // AAD
                $config['tag_length']
            );
        } else {
            // GCM 和 ChaCha20-Poly1305
            $ciphertext = openssl_encrypt(
                $plaintext,
                $this->selectedCipher,
                $this->getCipherKey(),
                OPENSSL_RAW_DATA,
                $iv,
                $tag
            );
        }
        if ($ciphertext === false) {
            throw new Exception('加密失败: ' . openssl_error_string());
        }
        if (strlen($tag) !== $config['tag_length']) {
            throw new Exception("认证标签生成失败：期望长度 {$config['tag_length']}，实际长度 " . strlen($tag));
        }
        // 返回格式：IV + Tag + Ciphertext
        $encryptedData = $iv . $tag . $ciphertext;
        return base64_encode($encryptedData);
    }

    /**
     * 解密数据
     *
     * @param string $encryptedData base64编码的加密数据
     * @return string 解密后的明文数据
     * @throws Exception
     */
    public function decrypt(string $encryptedData): string
    {
        $config = self::CIPHER_CONFIGS[$this->selectedCipher];
        $data = base64_decode($encryptedData, true);
        if ($data === false) {
            throw new Exception('Base64 解码失败');
        }
        $minLength = $config['iv_length'] + $config['tag_length'];
        if (strlen($data) < $minLength) {
            throw new Exception('密文格式错误或被篡改：长度不足');
        }
        // 解析数据包
        $iv = substr($data, 0, $config['iv_length']);
        $tag = substr($data, $config['iv_length'], $config['tag_length']);
        $ciphertext = substr($data, $config['iv_length'] + $config['tag_length']);
        // 根据算法类型调整解密参数
        if ($this->selectedCipher === 'aes-256-ccm') {
            $plaintext = openssl_decrypt(
                $ciphertext,
                $this->selectedCipher,
                $this->getCipherKey(),
                OPENSSL_RAW_DATA,
                $iv,
                $tag,
                '' // AAD
            );
        } else {
            $plaintext = openssl_decrypt(
                $ciphertext,
                $this->selectedCipher,
                $this->getCipherKey(),
                OPENSSL_RAW_DATA,
                $iv,
                $tag
            );
        }
        if ($plaintext === false) {
            throw new Exception('解密失败: 数据可能被篡改或认证失败 - ' . openssl_error_string());
        }
        return $plaintext;
    }

    /**
     * 获取当前使用的加密算法名称
     *
     * @return string
     */
    public function getCipherName(): string
    {
        return $this->selectedCipher;
    }

    /**
     * 获取当前使用的加密算法选项
     *
     * @return int
     */
    public function getCipherOption(): int
    {
        return array_search($this->selectedCipher, self::SUPPORTED_CIPHERS);
    }

    /**
     * 获取支持的加密算法选项列表
     *
     * @return array
     */
    public static function getSupportedCipherOptions(): array
    {
        return self::SUPPORTED_CIPHERS;
    }

    /**
     * 验证加密算法是否可用
     *
     * @param string $cipher 加密算法名称
     * @return bool
     */
    public static function isCipherSupported(string $cipher): bool
    {
        return in_array($cipher, openssl_get_cipher_methods(), true);
    }

    /**
     * 检测服务器支持的 Cipher
     */
    public static function checkAvailableCiphers(): array
    {
        $all = openssl_get_cipher_methods();
        $secure = [];
        $insecure = [];
        $securePatterns = ['/aes-.*gcm/', '/aes-.*ccm/', '/chacha20/'];
        foreach ($all as $cipher) {
            $isSecure = false;
            foreach ($securePatterns as $pattern) {
                if (preg_match($pattern, $cipher)) {
                    $isSecure = true;
                    break;
                }
            }
            if ($isSecure) {
                $secure[] = $cipher;
            } else {
                $insecure[] = $cipher;
            }
        }
        return ['secureCiphers' => $secure, 'insecureCiphers' => $insecure];
    }

    /**
     * 获取适合当前算法的密钥
     *
     * @return string
     */
    private function getCipherKey(): string
    {
        $requiredLength = self::CIPHER_CONFIGS[$this->selectedCipher]['key_length'];
        if (strlen($this->key) === $requiredLength) {
            return $this->key;
        }
        // 如果密钥长度不符合要求，使用 HKDF 派生合适长度的密钥
        return hash_hkdf('sha256', $this->key, $requiredLength, $this->selectedCipher);
    }
}