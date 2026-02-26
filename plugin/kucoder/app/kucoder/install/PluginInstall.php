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


namespace kucoder\install;

use Exception;
use kucoder\auth\AdminAuth;
use kucoder\constants\KcConst;
use kucoder\lib\KcFile;
use kucoder\lib\KcHelper;
use kucoder\lib\KcIdentity;
use kucoder\service\MenuService;
use kucoder\traits\HttpTrait;
use ReflectionException;
use support\think\Db;
use support\think\Cache;
use Throwable;
use Webman\Event\Event;
use plugin\kucoder\app\admin\model\ConfigGroup;
use plugin\kucoder\app\admin\model\Config;

class PluginInstall
{
    use HttpTrait;

    //插件默认使用主项目数据库配置
    protected static string $connection = '';

    /**
     * 安装
     * @param string $version
     * @return void
     * @throws Exception
     */
    protected static function install(string $version = ''): void
    {
        $plugin = static::getPluginName();
        Db::startTrans();
        try {
            //安装依赖
            static::handleDependencies($plugin, 'install');
            //安装数据库
            static::installSql($plugin);
            //安装菜单
            if ($menus = static::getMenus($plugin)) {
                kc_dump('开始安装插件菜单');
                MenuService::importPluginMenu($menus);
                Event::dispatch('adminMenu.deleteCache', ['key' => ["admin:menu:all"]]);
            }
            //安装vue文件
            static::installView($plugin);
            Db::commit();
        } catch (Throwable $t) {
            Db::rollback();
            throw new Exception($t->getMessage());
        }
    }

    /**
     * 卸载
     *
     * @param string $version
     * @return void
     * @throws Exception
     */
    protected static function uninstall(string $version = ''): void
    {
        $plugin = static::getPluginName();
        Db::startTrans();
        try {
            //移除依赖
            static::handleDependencies($plugin, 'uninstall');
            // 卸载数据库
            static::uninstallSql($plugin);
            // 删除菜单
            if (static::getMenus($plugin)) {
                kc_dump(KcConst::KC_COMMAND . '开始卸载插件菜单');
                MenuService::deletePluginMenu($plugin);
            } else {
                kc_dump(KcConst::KC_COMMAND . '无插件菜单,无需卸载');
            }
            // 删除vue文件
            static::uninstallView($plugin);
            //删除插件文件
            if (is_dir($pluginPath = base_path("plugin/{$plugin}"))) {
                kc_dump(KcConst::KC_COMMAND . '开始删除插件文件');
                KcFile::delDirWithFile($pluginPath);
            }
            Db::commit();
        } catch (Throwable $t) {
            Db::rollback();
            throw new Exception($t->getMessage());
        }
    }

    /**
     * 更新
     *
     * @param string $to_version
     * @param null $context
     * @return void
     * @throws ReflectionException
     * @throws Throwable
     * @throws \think\Exception
     */
    protected static function update(string $to_version = '', $context = null): void
    {
        $plugin = static::getPluginName();
        Db::startTrans();
        try {
            // 升级依赖
            static::updateDependencies($plugin);
            // 升级数据库
            static::updateSql($plugin);
            // 升级菜单
            static::updateMenuHandler($plugin);
            // 升级vue文件
            static::installView($plugin);
            Db::commit();
        } catch (Throwable $t) {
            Db::rollback();
            throw new Exception($t->getMessage());
        }
    }

    /**
     * @throws ReflectionException
     * @throws \think\Exception
     */
    protected static function updateMenuHandler(string $plugin): void
    {
        $old_menu_file = base_path() . "\\plugin\\{$plugin}\\config\\menu_bak.php";
        MenuService::export_plugin_menu($plugin, $old_menu_file);
        $old_menu = include $old_menu_file;
        $new_menu = static::getMenus($plugin);
        if ($old_menu !== $new_menu) {
            MenuService::del_plugin_menu($plugin);
            kc_dump(KcConst::KC_COMMAND . '开始升级插件菜单');
            MenuService::importPluginMenu($new_menu);
            Event::dispatch('adminMenu.deleteCache', ['key' => ["admin:menu:all"]]);
        } else {
            kc_dump(KcConst::KC_COMMAND . '插件菜单未发生变更,无需升级菜单');
        }
        $old_menu_file && unlink($old_menu_file);
    }

    /**
     * @throws Throwable
     */
    protected static function updateDependencies(string $plugin): void
    {
        $old_rely_file = base_path() . "\\plugin\\{$plugin}\\zinfo\\rely.json.bak";
        $old_rely = json_decode(file_get_contents($old_rely_file), true);
        $new_rely_file = base_path() . "\\plugin\\{$plugin}\\zinfo\\rely.json";
        $new_rely = json_decode(file_get_contents($new_rely_file), true);
        if ($old_rely !== $new_rely) {
            static::handleDependencies($plugin, 'update');
        } else {
            kc_dump(KcConst::KC_COMMAND . '插件依赖文件未发生变更,无需升级依赖');
        }
    }

    /**
     * 获取菜单
     *
     * @param string $pluginName
     * @return array
     */
    protected static function getMenus(string $pluginName = ''): array
    {
        $menu_file = base_path() . "\\plugin\\{$pluginName}\\config\\menu.php";
        if (!is_file($menu_file)) return [];
        $menus = include $menu_file;
        return $menus ?: [];
    }

    /**
     * 安装模板
     *
     * @param string $pluginName
     * @return void
     * @throws Exception
     */
    protected static function installView(string $pluginName): void
    {
        $vue_src = base_path() . "\\plugin\\{$pluginName}\\vue\\src";
        $vue_api = $vue_src . "\\api\\$pluginName";
        $vue_views = $vue_src . "\\views\\$pluginName";
        if (KcFile::dirHasFiles($vue_api) || KcFile::dirHasFiles($vue_views)) {
            kc_dump(KcConst::KC_COMMAND . '开始安装插件vue文件');
            $dest_dir = base_path() . KcConst::VUE_KC_ADMIN_SRC;
            KcFile::copyDirWithFile($vue_src, $dest_dir);
        } else {
            kc_dump(KcConst::KC_COMMAND . '插件vue文件不存在,无需安装');
        }
    }

    protected static function uninstallView(string $plugin): void
    {
        $dest_dir = base_path() . KcConst::VUE_KC_ADMIN_SRC;
        if ($od = opendir($dest_dir)) {
            kc_dump(KcConst::KC_COMMAND . '开始卸载插件vue文件');
            while (false !== ($file = readdir($od))) {
                if ($file == '.' || $file == '..') continue;
                if (is_dir($dest_dir . $file . '/' . $plugin)) {
                    KcFile::delDirWithFile($dest_dir . $file . '/' . $plugin);
                }
            }
            closedir($od);
        }
    }

    /**
     * @throws Exception
     * @throws Throwable
     */
    protected static function handleDependencies(string $pluginName, string $type): void
    {
        $dependencies_file = base_path() . "\\plugin\\{$pluginName}\\zinfo\\rely.json";
        if (is_file($dependencies_file)) {
            $rely = file_get_contents($dependencies_file);
            $instance = new self();
            $uri = config('plugin.kucoder.app.sys_url') . '/kapi/pins/' . (in_array($type, ['install', 'update']) ? 'installRely' : 'uninstallRely');
            $vuePath = kc_path(base_path(), KcConst::VUE_KC_ADMIN);
            $cookie = KcIdentity::getCookie($uri, AdminAuth::getInstance()->getId(), 'admin');
            $res = $instance->http_post($uri, ['rely' => $rely, 'basePath' => base_path(), 'vuePath' => $vuePath,'cookie' => $cookie]);
            // kc_dump('rely response:', $res);
            $command = $res['data']['command'];
            if (is_array($command) && $command) {
                if ($type === 'install') {
                    kc_dump(KcConst::KC_COMMAND . '开始安装插件依赖');
                    KcHelper::generateRelyBackup($pluginName, $dependencies_file);
                } else if ($type === 'update') {
                    kc_dump(KcConst::KC_COMMAND . '开始升级插件依赖');
                    copy($dependencies_file, $dependencies_file . '.bak');
                } else {
                    kc_dump(KcConst::KC_COMMAND . '开始卸载插件依赖');
                }
                foreach ($command as $type => $cmd) {
                    if (is_array($cmd) && $cmd) {
                        foreach ($cmd as $c) {
                            if (in_array($type, ['install', 'update'])) {
                                kc_dump($c[2]);
                                KcHelper::runCommand($c[0], $c[1], true);
                            } else {
                                if (KcHelper::checkRelyBackup($pluginName, $c[0])) {
                                    kc_dump($c[2]);
                                    KcHelper::runCommand($c[0], $c[1], true);
                                }
                            }
                        }
                    }
                }
            }
        }
    }

    /**
     * 安装SQL
     *
     * @param string $pluginName
     * @return void
     * @throws Exception
     */
    protected static function installSql(string $pluginName): void
    {
        $sqlFile = base_path() . "\\plugin\\{$pluginName}\\api\\install\\install.sql";
        if (!is_file($sqlFile)) {
            throw new Exception("插件安装文件不存在：{$sqlFile}");
        }
        if (filesize($sqlFile) > 0) {
            kc_dump(KcConst::KC_COMMAND . '开始安装插件数据库');
            static::executeSql($sqlFile);
            // 清除config配置参数缓存
            $installSql = file_get_contents($sqlFile);
            if(str_contains($installSql,"INSERT INTO `__prefix__ku_config`")){
                Cache::delete(KcConst::ADMIN_APP . ':config:configs');
            }
            if(str_contains($installSql,"INSERT INTO `__prefix__ku_config_group`")){
                Cache::delete(KcConst::ADMIN_APP . ':config:groups');
            }
        }
    }

    /**
     * 卸载SQL
     *
     * @param string $pluginName
     * @return void
     * @throws Exception
     */
    protected static function uninstallSql(string $pluginName): void
    {
        // 如果卸载数据库文件存在责直接使用
        $uninstallSqlFile = base_path() . "\\plugin\\{$pluginName}\\api\\install\\uninstall.sql";
        // kc_dump('检查卸载数据库文件大小:', is_file($uninstallSqlFile) ? filesize($uninstallSqlFile) : ' 文件不存在');
        if (is_file($uninstallSqlFile) && filesize($uninstallSqlFile) > 0) {
            kc_dump(KcConst::KC_COMMAND . 'uninstall.sql存在');
            static::executeSql($uninstallSqlFile);
            return;
        }
        // 否则根据install.sql生成卸载数据库文件uninstall.sql
        $installSqlFile = base_path() . "\\plugin\\{$pluginName}\\api\\install\\install.sql";
        if (!is_file($installSqlFile)) {
            return;
        }
        $installSql = file_get_contents($installSqlFile);
        preg_match_all('/CREATE TABLE `(.+?)`/si', $installSql, $matches);
        $dropSql = '';
        foreach ($matches[1] as $table) {
            $dropSql .= "DROP TABLE IF EXISTS `$table`;\n";
        }
        file_put_contents($uninstallSqlFile, $dropSql);
        kc_dump(KcConst::KC_COMMAND . '开始卸载插件数据库');
        static::executeSql($uninstallSqlFile);
        unlink($uninstallSqlFile);
        // 清除插件参数
        if(str_contains($installSql,"INSERT INTO `__prefix__ku_config`")){
            kc_dump(KcConst::KC_COMMAND . '开始删除插件config参数');
            // Config::where('plugin','=',$pluginName)->delete(true); //不生效 待查原因
            Config::destroy(function($query) use($pluginName){
                $query->where('plugin','=',$pluginName);
            },true);
            Cache::delete(KcConst::ADMIN_APP . ':config:configs');
        }
        // 清除插件参数分组
        if(str_contains($installSql,"INSERT INTO `__prefix__ku_config_group`")){
            ConfigGroup::destroy(function($query) use($pluginName){
                $query->where('plugin','=',$pluginName);
            },true);
            Cache::delete(KcConst::ADMIN_APP . ':config:groups');
        }
    }

    /**
     * @throws Exception
     */
    protected static function updateSql(string $plugin): void
    {
        $sqlFile = base_path() . "\\plugin\\{$plugin}\\api\\install\\update.sql";
        if (is_file($sqlFile) && filesize($sqlFile) > 0) {
            kc_dump(KcConst::KC_COMMAND . '开始升级插件数据库');
            static::executeSql($sqlFile);
        }
    }

    /**
     * 导入数据库
     *
     * @param $sqlFile
     * @return void
     * @throws Exception
     */
    protected static function executeSql(string $sqlFile): void
    {
        if (!$sqlFile || !is_file($sqlFile)) {
            return;
        }
        $sqlFileArr = explode(';', file_get_contents($sqlFile));
        if ($sqlFileArr) {
            foreach ($sqlFileArr as $sql) {
                if ($sql = trim($sql)) {
                    try {
                        // 替换表前缀
                        $sql = str_replace('__prefix__', get_table_prefix(), $sql);
                        // 执行SQL
                        if (static::$connection) {
                            Db::connect(static::$connection)->execute($sql);
                        } else {
                            Db::execute($sql);
                        }
                    } catch (Throwable $e) {
                        // throw new Exception($e->getMessage());
                    }
                }
            }
        }
        unset($sqlFileArr);
    }

    private static function getPluginName(): string
    {
        $path = explode('\\', static::class);
        return $path[1];
    }

}

