<?php
declare(strict_types=1);

namespace plugin\kucoder\app\kucoder\facade;

use plugin\kucoder\app\kucoder\lib\KcHttpAsync;
use think\Facade;

/**
 * http异步请求 基于workerman/http_client 支持协程
 * Class HttpAsync
 * @method static mixed get(string $uri, array $options = [], ?callable $success_callback = null, ?callable $error_callback = null)
 * @method static mixed post(string $uri, array $data = [], array $options = [], ?callable $success_callback = null, ?callable $error_callback = null)
 * @method static mixed request(string $method, string $uri, array $options = [], ?callable $success_callback = null, ?callable $error_callback = null)
 */
class HttpAsync extends Facade
{
    protected static function getFacadeClass()
    {
        return KcHttpAsync::class;
    }
}