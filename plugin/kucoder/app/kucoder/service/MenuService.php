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


namespace kucoder\service;

use plugin\kucoder\app\admin\model\Menu as MenuModel;
use kucoder\auth\AdminAuth;
use ReflectionException;
use support\think\Cache;
use Webman\Event\Event;

class MenuService
{
    private static function getInstance()
    {
        static $instance;
        if (!$instance) {
            $instance = new MenuModel();
        }
        return $instance;
    }

    /**
     * 导入菜单
     * @param array $menus
     * @param int $level
     * @return void
     */
    public static function importPluginMenu(array $menus, int $level = 0): void
    {
        foreach ($menus as $menu) {
            if (isset($menu['children']) && $menu['children']) {
                $children = $menu['children'];
                unset($menu['children']);
                if (!isset($menu['pid'])) {
                    $menu['pid'] = 0;
                }
                $save = MenuModel::create($menu);
                foreach ($children as &$child) {
                    $child['pid'] = $save->id;
                }
                self::importPluginMenu($children, ++$level);
            } else {
                MenuModel::create($menu);
            }
        }
    }

    /**
     * 删除菜单
     * @param string $plugin
     * @return bool
     */
    public static function deletePluginMenu(string $plugin): bool
    {
        $del = MenuModel::destroy(function ($query) use ($plugin) {
            $query->where('plugin', $plugin);
        }, true);
        if ($del) {
            Event::dispatch('adminMenu.deleteCache', ['key' => ["admin:menu:all", "admin:menu:allRoutes"]]);
            return true;
        }
        return false;
    }

    /**
     * @throws ReflectionException
     */
    public static function export_plugin_menu(string $pluginName = '', string $filePath = ''): bool
    {
        $pluginName = $pluginName ?: request()->plugin;
        $menuFile = $filePath ?: base_path() . '/plugin/' . $pluginName . '/config/menu.php';
        $menuPids = MenuModel::where(['plugin' => $pluginName, 'pid' => 0])->column('id');
        $allMenus = AdminAuth::getInstance()->getAllMenus();
        $pluginMenus = array_filter($allMenus, fn($menu) => $menu['plugin'] == $pluginName);
        $fieldsToRemove = ['create_uid', 'update_uid', 'create_time', 'update_time', 'delete_time'];
        $pluginMenus = _2dArray_filter_field($pluginMenus, [], $fieldsToRemove);
        $allMenuTree = [];
        foreach($menuPids as $menuPid) {
            $menuTree = get_recursion_data($pluginMenus, 'id', 'pid', $menuPid, 0, true);
            $menuTree = _2dTreeArray_filter_field($menuTree, [], ['id', 'pid']);
            $allMenuTree= array_merge($allMenuTree,$menuTree);
        }
        $menuTreeExport = var_export($allMenuTree, true);
        if (!$menuTreeExport) {
           $menuTreeExport = var_export([], true); 
        } 
        $fp = fopen($menuFile, 'w+');
        $content = <<<EOF
<?php

return $menuTreeExport;
EOF;
        fwrite($fp, $content);
        fclose($fp);
        return true;
    }

    public static function del_plugin_menu(string $pluginName): bool
    {
        $auth = AdminAuth::getInstance();
        $allMenus = $auth->getAllMenus();
        $pluginMenus = array_filter($allMenus, fn($menu) => $menu['plugin'] == $pluginName);
        if (!$pluginMenus) return true;
        $pluginMenusIds = array_column($pluginMenus, 'id');
        //真实删除 非软删除
        $del = MenuModel::destroy($pluginMenusIds, true);
        if ($del) {
            Event::dispatch('adminMenu.deleteCache', ['key' => ["admin:menu:all", "admin:menu:delete", "admin:menu:allRoutes"]]);
            return true;
        }
        return false;
    }

    public static function allMenusToRoutes(array $data, string $cache_key = 'admin:menu:allRoutes'): array
    {
        $allRoutes = Cache::get($cache_key);
        if (!$allRoutes) {
            $allRoutes = self::menusToRoutes($data);
            Cache::set($cache_key, $allRoutes, 24*3600);
        }
        return $allRoutes;
    }

    public static function menusToRoutes(array $data, string $idField = 'id', string $pidField = 'pid', int $pid = 0, int $level = 0, bool $self = false): array
    {
        $recursionData = [];
        $selfData = [];
        //vue后台地址入口 默认/admin
        $vue_admin_entry = getenv('VUE_ADMIN_ENTRY');
        $adminBasePath = str_ends_with($vue_admin_entry,'/') ? $vue_admin_entry : $vue_admin_entry.'/';
        foreach ($data as $k => $v) {
            $r = [];
            if ($v['type'] !== 'button') {
                if ($v['type'] !== 'link') {
                    $r = [
                        'name' => $v['name'],
                        'hidden' => !$v['show'],
                        'meta' => [
                            'title' => $v['title'],
                            'icon' => $v['icon'],
                            'noCache' => !$v['keepalive']
                        ]
                    ];
                    if ($v['type'] === 'dir') {
                        //控制器目录
                        if ($v['pid'] === 0) {
                            //防止多个顶级目录联动
                            if (!$v['path']) {
                                $v['path'] = $v['plugin'];
                            }
                            $r['path'] = $adminBasePath . $v['path'];
                        } else {
                            // 非顶级目录，只取 path 的最后一部分，避免嵌套路由重复
                            if (str_contains($v['path'], '/')) {
                                $pathArr = explode('/', $v['path']);
                                $r['path'] = $pathArr[count($pathArr) - 1];
                            } else {
                                $r['path'] = $v['path'];
                            }
                        }
                        if ($v['pid'] === 0) {
                            $r['component'] = 'Layout';
                        } else {
                            $r['component'] = 'ParentView';
                        }
                        $r['redirect'] = 'noRedirect';
                    } elseif ($v['type'] === 'menu') {
                        //菜单控制器
                        if (str_contains($v['path'], '/')) {
                            $pathArr = explode('/', $v['path']);
                            $r['path'] = $pathArr[count($pathArr) - 1];
                        } else {
                            $r['path'] = $v['path'];
                        }
                        //默认组件路径在views/下
                        if (str_starts_with($v['component'], $v['plugin'] . '/')) {
                            $r['component'] = $v['component'];
                        } else {
                            $r['component'] = $v['plugin'] . '/' . $v['component'];
                        }
                    }
                } else {
                    $r = [
                        'hidden' => !$v['show'],
                        'path' => '/' . $v['link_url'],
                        'meta' => [
                            'title' => $v['title'],
                            'icon' => $v['icon'],
                        ]
                    ];
                }

            }

            if ($v[$idField] == $pid && $self && $level == 0) {
                $selfData = $r;
            }
            if ($v[$pidField] == $pid && $v['type'] !== 'button') {
                // $item = $r;
                $itemId = $v[$idField];
                unset($data[$k]);   //减少后续递归消耗
                $level++;
                $r['children'] = self::menusToRoutes($data, $idField, $pidField, $itemId, $level);
                $recursionData[] = $r;
            }
        }
        if (!$self) {
            return $recursionData;
        } else {
            $selfData['children'] = $recursionData;
            return $selfData;
        }
    }
}