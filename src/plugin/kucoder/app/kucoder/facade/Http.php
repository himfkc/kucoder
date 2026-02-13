<?php
declare(strict_types=1);

namespace plugin\kucoder\app\kucoder\facade;

use plugin\kucoder\app\kucoder\lib\KcHttp;
use think\Facade;

/**
 * http请求 基于GuzzleHttp
 * Class Http
 * @method static mixed get(string $uri, array $options = [])
 * @method static mixed post(string $uri, array $data = [], array $options = [])
 * @method static mixed request(string $method, string $uri, array $options = [])
 */
class Http extends Facade
{
    protected static function getFacadeClass()
    {
        return KcHttp::class;
    }
}