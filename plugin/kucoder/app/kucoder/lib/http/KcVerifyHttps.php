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


namespace kucoder\lib\http;

use InvalidArgumentException;

/**
 * HTTPS SSL/TLS 验证工具类
 */
class KcVerifyHttps
{
    /**
     * 系统默认 CA 证书路径
     */
    private static ?string $defaultCaBundle = null;

    /**
     * 应用 SSL 验证选项到 stream context
     *
     * @param array $options 验证选项
     *                       - verify: bool|string
     *                         false: 不验证
     *                         true: 使用系统默认 CA
     *                         string: 指定 CA 证书路径
     *                       - cert: string|array 客户端证书路径 [证书路径, 密码]
     *                       - ssl_key: string|array 私钥路径 [私钥路径, 密码]
     * @return array stream context 的 ssl 选项
     */
    public static function applyOptions(array $options): array
    {
        $sslOptions = [
            'verify_peer' => true,
            'verify_peer_name' => true,
            'allow_self_signed' => false,
        ];

        // 处理 verify 选项
        if (isset($options['verify'])) {
            if ($options['verify'] === false) {
                // 禁用 SSL 验证
                $sslOptions['verify_peer'] = false;
                $sslOptions['verify_peer_name'] = false;
                $sslOptions['allow_self_signed'] = true;
            } elseif ($options['verify'] === true) {
                // 使用系统默认 CA
                $caFile = self::getDefaultCaBundle();
                if ($caFile !== null) {
                    $sslOptions['cafile'] = $caFile;
                }
            } elseif (is_string($options['verify'])) {
                // 使用指定的 CA 证书
                if (!file_exists($options['verify'])) {
                    throw new InvalidArgumentException("SSL CA bundle not found: {$options['verify']}");
                }

                if (is_dir($options['verify'])) {
                    // 如果是目录，使用 capath
                    $sslOptions['capath'] = $options['verify'];
                } else {
                    // 如果是文件，使用 cafile
                    $sslOptions['cafile'] = $options['verify'];
                }
            }
        }

        // 处理客户端证书
        if (isset($options['cert'])) {
            $sslOptions = array_merge($sslOptions, self::parseCertOption($options['cert']));
        }

        // 处理私钥
        if (isset($options['ssl_key'])) {
            $sslOptions = array_merge($sslOptions, self::parseSslKeyOption($options['ssl_key']));
        }

        return $sslOptions;
    }

    /**
     * 解析证书选项
     *
     * @param string|array $cert 证书路径或 [路径, 密码]
     * @return array
     * @throws InvalidArgumentException
     */
    private static function parseCertOption(string|array $cert): array
    {
        $result = [];

        if (is_array($cert)) {
            $certPath = $cert[0];
            if (isset($cert[1])) {
                $result['passphrase'] = $cert[1];
            }
        } else {
            $certPath = $cert;
        }

        if (!file_exists($certPath)) {
            throw new InvalidArgumentException("SSL certificate not found: {$certPath}");
        }

        $result['local_cert'] = $certPath;

        // 根据扩展名设置证书类型
        $ext = strtolower(pathinfo($certPath, PATHINFO_EXTENSION));
        if (in_array($ext, ['p12', 'pfx'])) {
            $result['local_pk'] = $certPath; // P12 包含私钥
        }

        return $result;
    }

    /**
     * 解析私钥选项
     *
     * @param string|array $sslKey 私钥路径或 [路径, 密码]
     * @return array
     * @throws InvalidArgumentException
     */
    private static function parseSslKeyOption(string|array $sslKey): array
    {
        $result = [];

        if (is_array($sslKey)) {
            $keyPath = $sslKey[0];
            if (isset($sslKey[1])) {
                $result['passphrase'] = $sslKey[1];
            }
        } else {
            $keyPath = $sslKey;
        }

        if (!file_exists($keyPath)) {
            throw new InvalidArgumentException("SSL private key not found: {$keyPath}");
        }

        $result['local_pk'] = $keyPath;

        return $result;
    }

    /**
     * 获取系统默认的 CA 证书包路径
     *
     * @return string|null
     */
    public static function getDefaultCaBundle(): ?string
    {
        if (self::$defaultCaBundle !== null) {
            return self::$defaultCaBundle;
        }

        // 尝试常见的 CA 证书路径
        $caPaths = [
            // Debian/Ubuntu/Gentoo 等
            '/etc/ssl/certs/ca-certificates.crt',
            // Red Hat/CentOS/Fedora
            '/etc/pki/tls/certs/ca-bundle.crt',
            // Red Hat/CentOS/Fedora (旧路径)
            '/etc/ssl/certs/ca-bundle.crt',
            // OpenSUSE
            '/etc/ssl/ca-bundle.pem',
            // OpenELEC
            '/etc/pki/tls/cacert.pem',
            // Alpine Linux
            '/etc/ssl/cert.pem',
            // macOS (通过 Homebrew 安装)
            '/usr/local/etc/openssl/cert.pem',
            '/usr/local/etc/openssl@1.1/cert.pem',
            '/opt/homebrew/etc/openssl@1.1/cert.pem',
            '/opt/homebrew/etc/openssl@3/cert.pem',
            // macOS (系统)
            '/System/Library/OpenSSL/certs',
            // Windows
            'C:\\windows\\system32\\curl-ca-bundle.crt',
            'C:\\windows\\curl-ca-bundle.crt',
        ];

        foreach ($caPaths as $path) {
            if (file_exists($path) && is_readable($path)) {
                self::$defaultCaBundle = $path;
                return $path;
            }
        }

        // 尝试使用 openssl 获取
        $opensslCertFile = ini_get('openssl.cafile');
        if ($opensslCertFile && file_exists($opensslCertFile)) {
            self::$defaultCaBundle = $opensslCertFile;
            return $opensslCertFile;
        }

        $opensslCertDir = ini_get('openssl.capath');
        if ($opensslCertDir && is_dir($opensslCertDir)) {
            self::$defaultCaBundle = $opensslCertDir;
            return $opensslCertDir;
        }

        // 尝试通过 curl 获取
        if (function_exists('curl_version')) {
            $curlVersion = curl_version();
            if (isset($curlVersion['ssl_version']) && !empty($curlVersion['ssl_version'])) {
                // curl 已启用 SSL，使用 curl 的默认 CA
                // 但无法直接获取路径，返回 null
            }
        }

        return null;
    }

    /**
     * 检查系统是否支持 SSL/TLS
     *
     * @return bool
     */
    public static function supportsTls(): bool
    {
        return extension_loaded('openssl') ||
            (function_exists('curl_version') &&
                !empty(curl_version()['ssl_version']));
    }

    /**
     * 获取 SSL 版本信息
     *
     * @return array
     */
    public static function getSslInfo(): array
    {
        $info = [
            'openssl_enabled' => extension_loaded('openssl'),
            'curl_enabled' => function_exists('curl_version'),
        ];

        if ($info['curl_enabled']) {
            $curlVersion = curl_version();
            $info['curl_ssl_version'] = $curlVersion['ssl_version'] ?? 'unknown';
            $info['curl_version'] = $curlVersion['version'] ?? 'unknown';
        }

        if ($info['openssl_enabled']) {
            $info['openssl_version'] = OPENSSL_VERSION_TEXT;
        }

        $info['default_ca_bundle'] = self::getDefaultCaBundle();

        return $info;
    }

    /**
     * 验证证书文件是否有效
     *
     * @param string $certPath 证书路径
     * @return bool
     */
    public static function isValidCert(string $certPath): bool
    {
        if (!file_exists($certPath)) {
            return false;
        }

        $content = file_get_contents($certPath);
        if ($content === false) {
            return false;
        }

        // 检查是否是 PEM 格式
        if (str_contains($content, '-----BEGIN CERTIFICATE-----')) {
            return true;
        }

        // 检查是否是 DER 格式 (二进制)
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $certPath);
        finfo_close($finfo);

        return in_array($mimeType, [
            'application/x-x509-ca-cert',
            'application/pkix-cert',
            'application/octet-stream',
        ]);
    }
}

/* 使用示例
 <?php
use kucoder\lib\http\VerifyHttps;

// 1. 禁用 SSL 验证（调试环境）
$sslOptions = VerifyHttps::applyOptions(['verify' => false]);

// 2. 使用系统默认 CA（生产环境推荐）
$sslOptions = VerifyHttps::applyOptions(['verify' => true]);

// 3. 使用自定义 CA 证书
$sslOptions = VerifyHttps::applyOptions([
    'verify' => '/path/to/custom-ca-bundle.crt'
]);

// 4. 使用客户端证书（双向认证）
$sslOptions = VerifyHttps::applyOptions([
    'verify' => true,
    'cert' => '/path/to/client-cert.pem',
    'ssl_key' => '/path/to/client-key.pem',
]);

// 5. 使用带密码的客户端证书
$sslOptions = VerifyHttps::applyOptions([
    'verify' => true,
    'cert' => ['/path/to/client-cert.p12', 'password'],
    'ssl_key' => ['/path/to/client-key.pem', 'password'],
]);

// 应用到 stream context
$context = stream_context_create([
    'ssl' => $sslOptions
]);

// 获取 SSL 信息
$info = VerifyHttps::getSslInfo();
print_r($info);

 */
