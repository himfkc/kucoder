<?php
declare(strict_types=1);

namespace plugin\kucoder\app\kucoder\lib;

use Exception;
use GuzzleHttp\Cookie\CookieJar;
use Psr\SimpleCache\InvalidArgumentException;
use ReflectionException;
use support\think\Cache;

class KcIdentity
{
    /**
     * 加密存储数据到cache
     * @throws ReflectionException
     * @throws InvalidArgumentException
     * @throws Exception
     */
    public static function set(int $id, string $app, string $data, int $expire): void
    {
        [$key, $opensslKey] = self::getKeys($id, $app, 'set');
        $openssl = new KcOpenSSL($opensslKey);
        $encryptData = $openssl->encrypt($data);
        Cache::set($key, $encryptData, $expire);
    }

    /**
     * 解密cache中存储的数据
     * @throws ReflectionException
     * @throws InvalidArgumentException
     * @throws Exception
     */
    public static function get(int $id, string $app): ?string
    {
        [$key, $opensslKey] = self::getKeys($id, $app);
        $data = Cache::get($key);
        if (!$data) return null;
        $openssl = new KcOpenSSL($opensslKey);
        $decryptData = $openssl->decrypt($data);
        return $decryptData;
    }

    public static function has(int $id, string $app): bool
    {
        [$key] = self::getKeys($id, $app);
        return Cache::has($key);
    }

    /**
     * 清除cache中存储的数据
     * @throws ReflectionException
     * @throws InvalidArgumentException
     * @throws Exception
     */
    public static function clear(int $id, string $app): void
    {
        [$key] = self::getKeys($id, $app);
        Cache::has($key) && Cache::delete($key);
    }

    /**
     * @throws Exception
     */
    public static function getKeys(int $id, string $app, $type = ''): array
    {
        $openssl_secret_key = config('plugin.kucoder.secret-key.openssl.secret_key') ?: '';
        $opensslKey = base64_decode($openssl_secret_key);
        if (!$opensslKey || strlen($opensslKey) !== 32) {
            if ($type === 'set') {
                $opensslKey = KcOpenSSL::generateKey();
                KcConfig::set(base_path('plugin/kucoder/config/secret-key.php'), 'openssl.secret_key', base64_encode($opensslKey));
            } else {
                throw new Exception('openssl_secret_key配置错误');
            }
        }
        $sessionType = config('session.type');
        $prefix = config("session.config.{$sessionType}.prefix");
        $prefix = str_replace(getenv('REDIS_PREFIX'), '', $prefix);
        $key = $prefix . $app . '_' . md5($opensslKey . md5((string)$id));
        return [$key, $opensslKey];
    }

    public static function getCookie(string $uri, int $id, string $app = 'admin'): ?CookieJar
    {
        $cookie = self::get($id, $app);
        if ($cookie) {
            $cookie = explode('=', $cookie);
            $domain = parse_url($uri, PHP_URL_HOST);
            return CookieJar::fromArray([$cookie[0] => $cookie[1]], $domain);
        }
        return null;
    }
}