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

use think\Facade;
use kucoder\lib\CodeStats as LibCodeStats;

class CodeStats extends Facade
{
    /**
     * 使用方法
     * CodeStats::start();
     * 执行程序...
     * CodeStats::end();
     * CodeStats::log();
     * 获取当前Facade对应类名（或者已经绑定的容器对象标识）
     * @return string
     */
    protected static function getFacadeClass(): string
    {
        return LibCodeStats::class;
    }
}