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


namespace kucoder\facade;

use kucoder\lib\KcHttp;
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