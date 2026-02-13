<?php
declare(strict_types=1);

namespace plugin\kucoder\app\kucoder\facade;

use think\Facade;
use plugin\kucoder\app\kucoder\lib\CodeStats as LibCodeStats;

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