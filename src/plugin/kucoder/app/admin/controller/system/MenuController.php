<?php
declare(strict_types=1);

namespace plugin\kucoder\app\admin\controller\system;

use plugin\kucoder\app\kucoder\controller\AdminBase;
use plugin\kucoder\app\kucoder\service\MenuService;
use ReflectionException;
use support\Response;
use think\db\exception\DbException;
use think\Exception;
use Throwable;
use Webman\Event\Event;


class MenuController extends AdminBase
{
    protected string $modelClass = "\\plugin\\kucoder\\app\\admin\\model\\Menu";

    /**
     * @return Response
     * @throws DbException
     * @throws Throwable
     */
    public function index(): Response
    {
        $recycle = $this->request->get('recycle', 0);
        $where = [];
        if ($recycle) {
            $where['recycle'] = true;
        }
        return $this->ok('', [
            'menus' => $this->auth->getRecursionUserMenus($this->auth->getId(), $where, !$recycle),
            'enumFieldData' => $this->getEnumFieldDataFromDb($this->model->getTable())
        ]);
    }

    /**
     * @throws ReflectionException
     */
    public function exportPluginMenu(string $pluginName=''): Response
    {
        $plugin = $pluginName ?: $this->request->input('pluginName');
        if (!$plugin) $this->throw('插件名称不能为空');
        $res = MenuService::export_plugin_menu($plugin);
        if (!$res) $this->throw('导出失败');
        return $this->ok('导出成功');
    }

    public function delPluginMenu(string $pluginName=''): Response
    {
        $plugin = $pluginName ?: $this->request->input('pluginName');
        if (!$plugin) $this->throw('插件名称不能为空');
        $res = MenuService::del_plugin_menu($plugin);
        if (!$res) $this->throw('删除失败');
        return $this->ok('删除成功');
    }

    protected function save_before(&$param): void
    {
        dump('save_before', $param);
        try {
            if (isset($param['path']) && $param['path'] && (!in_array($param['type'], ['link', 'button']))) {
                if (str_contains($param['path'], '/')) {
                    $pathArr = explode('/', $param['path']);
                } else {
                    $pathArr = [$param['path']];
                }
                array_walk($pathArr, function (&$item, $key) {
                    $item = ucfirst($item);
                });
                $param['name'] = ucfirst($param['plugin']) . implode('', $pathArr);
            }
        } catch (Throwable $t) {
            $this->errorInfo($t);
        }
    }

    protected function save_after($data, $action): void
    {
        $this->delete_menu_cache();
    }

    /**
     * @throws \Exception
     */
    protected function add_after($data): void
    {
        //如果是控制器菜单类型 添加curd子菜单
        if ($data['type'] === 'menu' && $data['insertGetId']) {
            dump('add_after $data', $data);
            try {
                $menuData = [
                    ['title' => '查询', 'path' => $data['path'] . '/index', 'pid' => $data['insertGetId'], 'type' => 'button', 'plugin' => $data['plugin'], 'create_uid' => $this->auth->getId()],
                    ['title' => '新增', 'path' => $data['path'] . '/add', 'pid' => $data['insertGetId'], 'type' => 'button', 'plugin' => $data['plugin'], 'create_uid' => $this->auth->getId()],
                    ['title' => '修改', 'path' => $data['path'] . '/edit', 'pid' => $data['insertGetId'], 'type' => 'button', 'plugin' => $data['plugin'], 'create_uid' => $this->auth->getId()],
                    ['title' => '删除', 'path' => $data['path'] . '/delete', 'pid' => $data['insertGetId'], 'type' => 'button', 'plugin' => $data['plugin'], 'create_uid' => $this->auth->getId()],
                    ['title' => '回收站', 'path' => $data['path'] . '/recycle', 'pid' => $data['insertGetId'], 'type' => 'button', 'plugin' => $data['plugin'], 'create_uid' => $this->auth->getId()]
                ];
                $this->model->replace(false)->saveAll($menuData);
            } catch (Throwable $t) {
                // $this->throw($this->errorInfo($t));
                $this->throw($t->getMessage());
            }
            $this->delete_menu_cache();
        }
    }

    protected function delete_after($data): void
    {
        $this->delete_menu_cache();
    }

    protected function change_after($data): void
    {
        $this->delete_menu_cache();
    }

    private function delete_menu_cache(): void
    {
        Event::dispatch('adminMenu.deleteCache', ['key' => ["{$this->app}:menu:all", "{$this->app}:menu:delete"]]);
    }

}