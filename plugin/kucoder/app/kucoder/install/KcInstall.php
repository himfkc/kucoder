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


namespace plugin\kucoder\app\kucoder\install;

use Exception;
use plugin\kucoder\app\kucoder\auth\AdminAuth;
use plugin\kucoder\app\kucoder\constants\KcConst;
use plugin\kucoder\app\kucoder\lib\KcFile;
use plugin\kucoder\app\kucoder\lib\KcHelper;
use plugin\kucoder\app\kucoder\lib\KcIdentity;
use plugin\kucoder\app\kucoder\service\MenuService;
use plugin\kucoder\app\kucoder\traits\HttpTrait;
use plugin\kucoder\app\kucoder\traits\ResponseTrait;
use ReflectionException;
use support\think\Db;
use Throwable;
use Webman\Event\Event;

class KcInstall
{
    use HttpTrait,ResponseTrait;

    //kucoder默认使用主项目数据库配置
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
            // static::handleDependencies($plugin, 'install');
            //安装数据库
            static::installSql($plugin);
            //安装菜单
            if ($menus = static::getMenus($plugin)) {
                kc_dump(KcConst::KC_COMMAND . '开始安装kucoder菜单');
                MenuService::importPluginMenu($menus);
                Event::dispatch('adminMenu.deleteCache', ['key' => ["admin:menu:all"]]);
            }
            //安装vue文件
            // static::installView($plugin);
            Db::commit();
        } catch (Throwable $t) {
            Db::rollback();
            kc_dump('kucoder安装异常：',(new self)->errorInfo($t));
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
                kc_dump(KcConst::KC_COMMAND . '开始卸载kucoder菜单');
                MenuService::deletePluginMenu($plugin);
            } else {
                kc_dump(KcConst::KC_COMMAND . '无kucoder菜单,无需卸载');
            }
            // 删除vue文件
            static::uninstallView($plugin);
            //删除kucoder文件
            if (is_dir($pluginPath = base_path("plugin/{$plugin}"))) {
                kc_dump(KcConst::KC_COMMAND . '开始删除kucoder文件');
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
            kc_dump(KcConst::KC_COMMAND . '开始升级kucoder菜单');
            MenuService::importPluginMenu($new_menu);
            Event::dispatch('adminMenu.deleteCache', ['key' => ["admin:menu:all"]]);
        } else {
            kc_dump(KcConst::KC_COMMAND . 'kucoder菜单未发生变更,无需升级菜单');
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
            kc_dump(KcConst::KC_COMMAND . 'kucoder依赖文件未发生变更,无需升级依赖');
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
            kc_dump(KcConst::KC_COMMAND . '开始安装kucodervue文件');
            $dest_dir = base_path() . KcConst::VUE_KC_ADMIN_SRC;
            KcFile::copyDirWithFile($vue_src, $dest_dir);
        } else {
            kc_dump(KcConst::KC_COMMAND . 'kucodervue文件不存在,无需安装');
        }
    }

    protected static function uninstallView(string $plugin): void
    {
        $dest_dir = base_path() . KcConst::VUE_KC_ADMIN_SRC;
        if ($od = opendir($dest_dir)) {
            kc_dump(KcConst::KC_COMMAND . '开始卸载kucodervue文件');
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
        $composer = json_decode(file_get_contents(base_path('composer.json')), true);
        $packagePath = kc_path(base_path(), KcConst::VUE_KC_ADMIN, '/package.json');
        $package = json_decode(file_get_contents($packagePath), true);
        $rely = [
            'require' => $composer['require'],
            'require-dev' => isset($composer['require-dev']) ? $composer['require-dev'] : [],
            'dependencies' => $package['dependencies'],
            'devDependencies' => $package['devDependencies'],
        ];
        $rely = json_encode($rely, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        $uri = config('plugin.kucoder.app.sys_url') . '/kapi/pins/' . (in_array($type, ['install', 'update']) ? 'installRely' : 'uninstallRely');
        $vuePath = kc_path(base_path(), KcConst::VUE_KC_ADMIN);
        $data = ['rely' => $rely, 'basePath' => base_path(), 'vuePath' => $vuePath];
        if ($type === 'update') {
            $cookie = KcIdentity::getCookie($uri, AdminAuth::getInstance()->getId(), 'admin');
            $data['cookie'] = $cookie;
        }
        $instance = new self();
        $res = $instance->http_post($uri, $data, cookie: $type === 'update');
        // kc_dump('rely response:', $res);
        $command = $res['data']['command'];
        if (is_array($command) && $command) {
            if ($type === 'install') {
                kc_dump(KcConst::KC_COMMAND . '开始安装kucoder依赖');
                // KcHelper::generateRelyBackup($pluginName, $dependencies_file);
            } else if ($type === 'update') {
                kc_dump(KcConst::KC_COMMAND . '开始升级kucoder依赖');
                // copy($dependencies_file, $dependencies_file . '.bak');
            } else {
                kc_dump(KcConst::KC_COMMAND . '开始卸载kucoder依赖');
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
            throw new Exception("kucoder安装文件不存在：{$sqlFile}");
        }
        if (filesize($sqlFile) > 0) {
            kc_dump(KcConst::KC_COMMAND . '开始安装kucoder数据库');
            static::executeSql($sqlFile);
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
        kc_dump(KcConst::KC_COMMAND . '开始卸载kucoder数据库');
        static::executeSql($uninstallSqlFile);
        unlink($uninstallSqlFile);
    }

    /**
     * @throws Exception
     */
    protected static function updateSql(string $plugin): void
    {
        $sqlFile = base_path() . "\\plugin\\{$plugin}\\api\\install\\update.sql";
        if (is_file($sqlFile) && filesize($sqlFile) > 0) {
            kc_dump(KcConst::KC_COMMAND . '开始升级kucoder数据库');
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
                        kc_dump('数据库sql执行异常:',$e->getMessage());
                        throw new Exception($e->getMessage());
                    }
                }
            }
        }
        unset($sqlFileArr);
    }

    private static function getPluginName(): string
    {
        $path = explode('\\', static::class);
        kc_dump('当前插件类：', $path[1]);
        return $path[1];
    }

}

