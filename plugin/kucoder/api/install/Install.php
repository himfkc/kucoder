<?php
declare(strict_types=1);

namespace plugin\kucoder\api\install;

use Exception;
use plugin\kucoder\app\kucoder\install\KcInstall;
use ReflectionException;
use Throwable;

class Install extends KcInstall
{
    //插件默认使用主项目数据库配置
    protected static string $connection = '';

    /**
     * 安装插件
     * @param string $version
     * @return void
     * @throws Exception
     */
    public static function install(string $version = ''): void
    {
        //调用父类安装逻辑 已包含安装插件数据库、导入插件菜单、导入vue文件、安装依赖
        parent::install($version);

        //插件额外的安装逻辑

    }

    /**
     * 升级插件
     * @param string $to_version
     * @param null $context
     * @return void
     * @throws Throwable
     * @throws ReflectionException
     * @throws \think\Exception
     */
    public static function update(string $to_version='', $context = null): void
    {
        //调用父类升级逻辑
        parent::update($to_version);

        //插件额外的升级逻辑

    }


}