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
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use kucoder\constants\KcError;

class KcJWT
{
    public static function encode(array $data = []): string
    {
        $secret_key = config('plugin.kucoder.app.jwt.secret_key');
        $expire = config('plugin.kucoder.app.jwt.expire');
        $payload = [
            'iss' => 'https://kucoder.com',
            'aud' => 'https://kucoder.com',
            'iat' => time(),
            'exp' => time() + (int)$expire,
            'data' => $data,
        ];
        return JWT::encode($payload, $secret_key, 'HS256');
    }

    /**
     * @throws Exception
     */
    public static function decode(string $token): array
    {
        if (empty($token)) {
            throw new Exception('JWTToken为空');
        }
        try {
            $secret_key = config('plugin.kucoder.app.jwt.secret_key');
            $decode = (array)JWT::decode($token, new key($secret_key, 'HS256'));
            return isset($decode['data']) ? (array)$decode['data'] : [];
        } catch (\Throwable $t) {
            if ($t->getMessage() === 'Expired token') {
                throw new Exception('登录已过期,请重新登录', KcError::TokenExpired[0]);
            }
            throw new Exception('JWTToken解密异常:' . $t->getMessage());
        }
    }

}