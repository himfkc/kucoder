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


namespace plugin\kucoder\app\kucoder\lib\sodium;

use Exception;
use SodiumException;

/**
 * 使用 Sodium 的非对称加解密类
 * 基于 Curve25519 椭圆曲线密码学
 */
class KcBox
{
    /**
     * 密钥对结构
     */
    private string $keyPair;

    /**
     * 构造函数
     *
     * @param string|null $secretKey 可选的私钥，如果为空则生成新密钥对
     * @throws Exception
     */
    public function __construct(?string $secretKey = null)
    {
        if (!extension_loaded('sodium')) {
            throw new Exception('Sodium 扩展未加载');
        }

        if ($secretKey !== null) {
            // 从现有私钥生成密钥对
            $this->validateSecretKey($secretKey);
            $this->keyPair = sodium_crypto_box_keypair_from_secretkey_and_publickey(
                $secretKey,
                sodium_crypto_box_publickey_from_secretkey($secretKey)
            );
        } else {
            // 生成新密钥对
            $this->keyPair = sodium_crypto_box_keypair();
        }
    }

    /**
     * 生成新的密钥对
     *
     * @return self
     * @throws Exception
     */
    public static function generate(): self
    {
        return new self();
    }

    /**
     * 从私钥创建实例
     *
     * @param string $secretKey 私钥
     * @return self
     * @throws Exception
     */
    public static function fromSecretKey(string $secretKey): self
    {
        return new self($secretKey);
    }

    /**
     * 加密数据（使用接收方的公钥）
     *
     * @param string $plaintext 明文数据
     * @param string $recipientPublicKey 接收方的公钥
     * @return string base64编码的加密数据
     * @throws Exception
     */
    public function encrypt(string $plaintext, string $recipientPublicKey): string
    {
        $this->validatePublicKey($recipientPublicKey);

        // 生成随机 nonce
        $nonce = random_bytes(SODIUM_CRYPTO_BOX_NONCEBYTES);

        try {
            // 加密数据
            $ciphertext = sodium_crypto_box(
                $plaintext,
                $nonce,
                sodium_crypto_box_keypair_from_secretkey_and_publickey(
                    $this->getSecretKey(),
                    $recipientPublicKey
                )
            );

            if ($ciphertext === false) {
                throw new Exception('加密失败');
            }

            // 返回格式：nonce + ciphertext
            $encryptedData = $nonce . $ciphertext;
            return base64_encode($encryptedData);

        } finally {
            // 清理敏感数据
            sodium_memzero($plaintext);
        }
    }

    /**
     * 解密数据（使用自己的私钥）
     *
     * @param string $encryptedData base64编码的加密数据
     * @param string $senderPublicKey 发送方的公钥
     * @return string 解密后的明文数据
     * @throws Exception
     */
    public function decrypt(string $encryptedData, string $senderPublicKey): string
    {
        $this->validatePublicKey($senderPublicKey);

        $data = base64_decode($encryptedData, true);
        if ($data === false) {
            throw new Exception('Base64 解码失败');
        }

        // 检查数据长度
        $minLength = SODIUM_CRYPTO_BOX_NONCEBYTES;
        if (strlen($data) < $minLength) {
            throw new Exception('加密数据格式错误：长度不足');
        }

        // 提取 nonce 和密文
        $nonce = substr($data, 0, SODIUM_CRYPTO_BOX_NONCEBYTES);
        $ciphertext = substr($data, SODIUM_CRYPTO_BOX_NONCEBYTES);

        // 创建密钥对用于解密
        $keyPair = sodium_crypto_box_keypair_from_secretkey_and_publickey(
            $this->getSecretKey(),
            $senderPublicKey
        );

        try {
            // 解密数据
            $plaintext = sodium_crypto_box_open($ciphertext, $nonce, $keyPair);

            if ($plaintext === false) {
                throw new Exception('解密失败：数据可能被篡改或认证失败');
            }

            return $plaintext;

        } finally {
            // 清理密钥对
            sodium_memzero($keyPair);
        }
    }

    /**
     * 获取公钥
     *
     * @return string
     * @throws SodiumException
     */
    public function getPublicKey(): string
    {
        return sodium_crypto_box_publickey($this->keyPair);
    }

    /**
     * 获取私钥（谨慎使用！）
     *
     * @return string
     * @throws SodiumException
     */
    public function getSecretKey(): string
    {
        return sodium_crypto_box_secretkey($this->keyPair);
    }

    /**
     * 安全地获取密钥对（用于存储）
     *
     * @return array ['public_key' => string, 'secret_key' => string]
     * @throws SodiumException
     */
    public function getKeyPair(): array
    {
        return [
            'public_key' => $this->getPublicKey(),
            'secret_key' => $this->getSecretKey()
        ];
    }

    /**
     * 从密钥对创建实例
     *
     * @param array $keyPair 包含 public_key 和 secret_key 的数组
     * @return self
     * @throws Exception
     */
    public static function fromKeyPair(array $keyPair): self
    {
        if (!isset($keyPair['secret_key']) || !is_string($keyPair['secret_key'])) {
            throw new Exception('无效的密钥对：缺少或无效的私钥');
        }

        return new self($keyPair['secret_key']);
    }

    /**
     * 验证公钥格式
     *
     * @param string $publicKey
     * @throws Exception
     */
    private function validatePublicKey(string $publicKey): void
    {
        if (strlen($publicKey) !== SODIUM_CRYPTO_BOX_PUBLICKEYBYTES) {
            throw new Exception('无效的公钥：长度不正确');
        }
    }

    /**
     * 验证私钥格式
     *
     * @param string $secretKey
     * @throws Exception
     */
    private function validateSecretKey(string $secretKey): void
    {
        if (strlen($secretKey) !== SODIUM_CRYPTO_BOX_SECRETKEYBYTES) {
            throw new Exception('无效的私钥：长度不正确');
        }
    }

    /**
     * 安全地序列化密钥对（用于安全存储）
     *
     * @return string base64编码的序列化密钥对
     * @throws SodiumException
     */
    public function serializeKeyPair(): string
    {
        $keyPair = $this->getKeyPair();
        $serialized = json_encode($keyPair);
        return base64_encode($serialized);
    }

    /**
     * 从序列化数据恢复实例
     *
     * @param string $serializedData base64编码的序列化密钥对
     * @return self
     * @throws Exception
     */
    public static function fromSerialized(string $serializedData): self
    {
        $decoded = base64_decode($serializedData, true);
        if ($decoded === false) {
            throw new Exception('Base64 解码失败');
        }

        $keyPair = json_decode($decoded, true);
        if (!is_array($keyPair) || !isset($keyPair['secret_key'])) {
            throw new Exception('无效的序列化数据');
        }

        return self::fromKeyPair($keyPair);
    }

    /**
     * 销毁时清理内存中的敏感数据
     * @throws SodiumException
     */
    public function __destruct()
    {
        if (isset($this->keyPair)) {
            sodium_memzero($this->keyPair);
        }
    }
}