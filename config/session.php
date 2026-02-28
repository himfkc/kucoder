<?php
/**
 * This file is part of webman.
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the MIT-LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @author    walkor<walkor@workerman.net>
 * @copyright walkor<walkor@workerman.net>
 * @link      http://www.workerman.net/
 * @license   http://www.opensource.org/licenses/mit-license.php MIT License
 */

use Webman\Session\FileSessionHandler;
use Webman\Session\RedisSessionHandler;
use Webman\Session\RedisClusterSessionHandler;

return [

    'type' => 'redis', // file or redis or redis_cluster

    /*
     * handler配置
     * type使用file时，handler可选 FileSessionHandler::class
     * type使用redis时，handler可选 RedisSessionHandler::class
     * type使用redis_cluster时，handler可选 RedisClusterSessionHandler::class
     */
    'handler' => RedisSessionHandler::class,

    'config' => [
        'file' => [
            'save_path' => runtime_path() . '/sessions',
        ],
        'redis' => [
            'host' => getenv('REDIS_HOST') ?: '127.0.0.1',
            'port' => (int)getenv('REDIS_PORT') ?: 6379,
            'auth' => getenv('REDIS_PASSWORD'),
            'timeout' => 2,
            'database' => (int)getenv('REDIS_DATABASE'),
            'prefix' => getenv('REDIS_PREFIX') . 'redis_session_',
        ],
        'redis_cluster' => [
            'host' => ['127.0.0.1:7000', '127.0.0.1:7001', '127.0.0.1:7002'],
            'timeout' => 2,
            'auth' => '',
            'prefix' => 'redis_session_',
        ]
    ],
    // 存储session_id的cookie名
    'session_name' => 'PHPSID',
    // 是否自动刷新session，默认关闭
    'auto_update_timestamp' => false,
    // session过期时间
    'lifetime' => 7 * 24 * 60 * 60,
    // 存储session_id的cookie过期时间
    'cookie_lifetime' => 365 * 24 * 60 * 60,
    // 存储session_id的cookie路径
    'cookie_path' => '/',
    // 存储session_id的cookie域名
    'domain' => '',
    // 是否开启httpOnly，默认开启
    'http_only' => true,
    // 仅在https下开启session，默认关闭
    'secure' => false,
    // 用于防止CSRF攻击和用户追踪，可选值strict/lax/non
    'same_site' => 'lax',
    // 回收session的几率
    'gc_probability' => [1, 1000],

];
