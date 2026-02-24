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


namespace plugin\kucoder\app\kucoder\service;

use Exception;

class PluginService
{
    /**
     * @throws Exception
     */
    public static function install(string $pluginName, string $version = ''): void
    {
        $pluginInstallClass = self::getPluginInstallClass($pluginName);
        $installedLock = base_path("plugin/{$pluginName}/api/install/installed.lock");
        if (file_exists($installedLock)) {
            throw new Exception("插件已安装");
        }
        try {
            $pluginInstallClass::install($version);
            file_put_contents($installedLock, '');
        } catch (\Throwable $t) {
            throw new Exception("插件安装失败：{$t->getMessage()}");
        }
    }

    /**
     * @throws Exception
     */
    public static function uninstall(string $pluginName, string $version = ''): void
    {
        $pluginInstallClass = self::getPluginInstallClass($pluginName);
        try {
            $pluginInstallClass::uninstall($version);
            $installedLock = base_path("plugin/{$pluginName}/api/install/installed.lock");
            if (file_exists($installedLock)) {
                unlink($installedLock);
            }
        } catch (\Throwable $t) {
            throw new Exception("插件卸载失败：{$t->getMessage()}");
        }
    }

    /**
     * @throws Exception
     */
    public static function update(string $pluginName, string $version = ''): void
    {
        $pluginInstallClass = self::getPluginInstallClass($pluginName);
        try {
            $pluginInstallClass::update($version);
        } catch (\Throwable $t) {
            throw new Exception("插件升级失败：{$t->getMessage()}");
        }
    }

    /**
     * @throws Exception
     */
    private static function getPluginInstallClass(string $pluginName): string
    {
        $pluginInstallClass = "\\plugin\\{$pluginName}\\api\\install\\Install";
        if (!class_exists($pluginInstallClass)) {
            throw new Exception("{$pluginInstallClass}.php文件不存在");
        }
        return $pluginInstallClass;
    }

}