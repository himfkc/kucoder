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
use SodiumException;

/**
 * 使用 Sodium 的数字签名类
 * 基于 Ed25519 椭圆曲线数字签名算法
 */
class KcSign
{
    /**
     * 签名密钥对
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
            $this->keyPair = sodium_crypto_sign_keypair_from_secretkey_and_publickey(
                $secretKey,
                sodium_crypto_sign_publickey_from_secretkey($secretKey)
            );
        } else {
            // 生成新密钥对
            $this->keyPair = sodium_crypto_sign_keypair();
        }
    }

    /**
     * 生成新的签名密钥对
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
     * 生成签名
     *
     * @param string $message 要签名的消息
     * @return string base64编码的签名
     * @throws Exception
     */
    public function sign(string $message): string
    {
        if (empty($message)) {
            throw new Exception('签名消息不能为空');
        }

        try {
            // 生成签名
            $signature = sodium_crypto_sign_detached($message, $this->keyPair);

            if ($signature === false) {
                throw new Exception('签名生成失败');
            }

            return base64_encode($signature);

        } finally {
            // 清理敏感数据
            sodium_memzero($message);
        }
    }

    /**
     * 验证签名
     *
     * @param string $message 原始消息
     * @param string $signature base64编码的签名
     * @param string $publicKey 签名者的公钥
     * @return bool 验证结果
     * @throws Exception
     */
    public static function verify(string $message, string $signature, string $publicKey): bool
    {
        if (empty($message)) {
            throw new Exception('验证消息不能为空');
        }

        self::validatePublicKey($publicKey);

        $decodedSignature = base64_decode($signature, true);
        if ($decodedSignature === false) {
            throw new Exception('签名格式错误：Base64 解码失败');
        }

        if (strlen($decodedSignature) !== SODIUM_CRYPTO_SIGN_BYTES) {
            throw new Exception('签名格式错误：长度不正确');
        }

        // 验证签名
        $isValid = sodium_crypto_sign_verify_detached($decodedSignature, $message, $publicKey);

        // 清理内存
        sodium_memzero($message);

        return $isValid;
    }

    /**
     * 使用当前实例的公钥验证签名
     *
     * @param string $message 原始消息
     * @param string $signature base64编码的签名
     * @return bool 验证结果
     * @throws Exception
     */
    public function verifyWithOwnKey(string $message, string $signature): bool
    {
        return self::verify($message, $signature, $this->getPublicKey());
    }

    /**
     * 获取公钥
     *
     * @return string
     * @throws SodiumException
     */
    public function getPublicKey(): string
    {
        return sodium_crypto_sign_publickey($this->keyPair);
    }

    /**
     * 获取私钥（谨慎使用！）
     *
     * @return string
     * @throws SodiumException
     */
    public function getSecretKey(): string
    {
        return sodium_crypto_sign_secretkey($this->keyPair);
    }

    /**
     * 安全地获取密钥对（用于存储）
     *
     * @return array ['public_key' => string, 'secret_key' => string]
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
     * 安全地序列化密钥对（用于安全存储）
     *
     * @return string base64编码的序列化密钥对
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
     * 验证公钥格式
     *
     * @param string $publicKey
     * @throws Exception
     */
    private static function validatePublicKey(string $publicKey): void
    {
        if (strlen($publicKey) !== SODIUM_CRYPTO_SIGN_PUBLICKEYBYTES) {
            throw new Exception('无效的公钥：长度不正确，期望 ' . SODIUM_CRYPTO_SIGN_PUBLICKEYBYTES . ' 字节');
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
        if (strlen($secretKey) !== SODIUM_CRYPTO_SIGN_SECRETKEYBYTES) {
            throw new Exception('无效的私钥：长度不正确，期望 ' . SODIUM_CRYPTO_SIGN_SECRETKEYBYTES . ' 字节');
        }
    }

    /**
     * 为消息生成签名并返回包含消息和签名的包
     *
     * @param string $message 要签名的消息
     * @return array ['message' => string, 'signature' => string, 'public_key' => string]
     * @throws Exception
     */
    public function signPackage(string $message): array
    {
        $signature = $this->sign($message);

        return [
            'message' => $message,
            'signature' => $signature,
            'public_key' => base64_encode($this->getPublicKey()),
            'timestamp' => time()
        ];
    }

    /**
     * 验证签名包
     *
     * @param array $package 包含 message, signature, public_key 的数组
     * @return bool 验证结果
     * @throws Exception
     */
    public static function verifyPackage(array $package): bool
    {
        if (!isset($package['message']) || !isset($package['signature']) || !isset($package['public_key'])) {
            throw new Exception('签名包格式错误：缺少必要字段');
        }

        $publicKey = base64_decode($package['public_key'], true);
        if ($publicKey === false) {
            throw new Exception('公钥格式错误：Base64 解码失败');
        }

        return self::verify($package['message'], $package['signature'], $publicKey);
    }

    /**
     * 批量验证多个签名
     *
     * @param array $signatures 签名数组，每个元素包含 message, signature, public_key
     * @return array 验证结果数组
     */
    public static function verifyBatch(array $signatures): array
    {
        $results = [];

        foreach ($signatures as $index => $signatureData) {
            try {
                $isValid = self::verify(
                    $signatureData['message'],
                    $signatureData['signature'],
                    $signatureData['public_key']
                );
                $results[$index] = [
                    'valid' => $isValid,
                    'error' => null
                ];
            } catch (Exception $e) {
                $results[$index] = [
                    'valid' => false,
                    'error' => $e->getMessage()
                ];
            }
        }

        return $results;
    }

    /**
     * 销毁时清理内存中的敏感数据
     */
    public function __destruct()
    {
        if (isset($this->keyPair)) {
            sodium_memzero($this->keyPair);
        }
    }
}