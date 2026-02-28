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


namespace plugin\kucoder\app\admin\controller\system\config;

use Exception;
use plugin\kucoder\app\admin\model\Config;
use plugin\kucoder\app\admin\model\ConfigGroup;
use kucoder\constants\KcConst;
use kucoder\controller\AdminBase;
use kucoder\lib\KcHelper;
use Psr\SimpleCache\InvalidArgumentException;
use support\Response;
use support\think\Cache;

/**
 * 系统配置控制器
 */
class ConfigController extends AdminBase
{
    protected string $modelClass = Config::class;

    protected array $allowAccessActions = [];

    /**
     * 获取所有配置项
     */
    public function index(): Response
    {
        $model = $this->model;
        //所有参数配置项
        $configs = Cache::remember(KcConst::ADMIN_APP .':config:configs', function () use ($model) {
            return $model->select()->toArray();
        }, get_env('cache_expire_time'));
        //配置参数所属的所有插件
        $plugins = array_values(array_unique(array_column($configs, 'plugin')));
        $systemPlugins = $this->getSystemPlugins();
        $plugins = array_map(function ($plugin) use ($systemPlugins) {
            if($plugin === 'kucoder'){
                $systemPlugins['kucoder'] = [
                    'name' => 'kucoder',
                    'title' => '主系统',
                ];
            }
            return $systemPlugins[$plugin] ?? [
                'name' => $plugin,
                'title' => $plugin,
            ];
        }, $plugins);
        //所有配置分组
        $configGroupClass = ConfigGroup::class;
        $groups = Cache::remember(KcConst::ADMIN_APP.':config:groups', function () use ($configGroupClass) {
            return (new $configGroupClass)->select()->toArray();
        }, get_env('cache_expire_time'));
        $data = compact('configs', 'plugins', 'groups');
        return $this->success('获取成功', $data);
    }

    /**
     * 获取每个系统插件的name与title
     */
    private function getSystemPlugins(): array
    {
        $plugins = [];
        $pluginDir = base_path('plugin');
        if (!is_dir($pluginDir)) {
            return $plugins;
        }
        $dirHandle = opendir($pluginDir);
        while (($plugin = readdir($dirHandle)) !== false) {
            // 跳过 . 和 .. 目录
            if ($plugin === '.' || $plugin === '..') {
                continue;
            }
            $pluginPath = $pluginDir . DIRECTORY_SEPARATOR . $plugin;
            if (!is_dir($pluginPath)) {
                continue;
            }
            $infofile = $pluginPath . DIRECTORY_SEPARATOR . 'zinfo/info.php';
            if (!file_exists($infofile)) {
                continue;
            }
            // info.php 返回的是 PHP 数组，不是 JSON
            $info = include $infofile;
            if (!$info || !isset($info['title'])) {
                continue;
            }
            $plugins[$plugin] = [
                'name' => $plugin,
                'title' => $info['title'] ?: $plugin,
            ];
        }
        closedir($dirHandle);
        return $plugins;
    }



    /**
     * 添加配置项
     */
    public function add(): Response
    {
        $request = $this->request;
        $data = $request->post();

        if (empty($data['name'])) {
            return $this->error('配置名称不能为空');
        }

        if (empty($data['title'])) {
            return $this->error('配置标题不能为空');
        }

        if (empty($data['type'])) {
            return $this->error('配置类型不能为空');
        }

        $config = new Config();
        $config->group_id = isset($data['group_id']) ? $data['group_id'] : null;
        $config->plugin = $data['plugin'] ?? 'kucoder';
        $config->name = $data['name'];
        $config->title = $data['title'];
        $config->type = $data['type'];
        $config->value = $data['value'] ?? '';
        $config->config_data = $data['config_data'] ?? null;
        $config->is_secret = $data['is_secret'] ?? 0;
        $config->validate = $data['validate'] ?? '';
        $config->extend = $data['extend'] ?? '';
        $config->allow_del = $data['allow_del'] ?? 1;
        $config->weigh = $data['weigh'] ?? 0;
        $config->save();

        // 清除配置和分组的缓存
        Cache::delete(KcConst::ADMIN_APP . ':config:configs');
        Cache::delete(KcConst::ADMIN_APP . ':config:groups');
        return $this->success();
    }

    /**
     * 编辑保存指定插件的配置
     * @throws Exception
     * @throws InvalidArgumentException
     */
    public function edit(): Response
    {
        $request = $this->request;
        $plugin = $request->post('plugin');
        $groupId = $request->post('group_id');
        $configs = $request->post();

        if (empty($plugin)) {
            return $this->error('插件不能为空');
        }

        // 移除plugin和group_id字段，保留配置数据
        unset($configs['plugin'], $configs['group_id']);

        if (empty($configs)) {
            return $this->success();
        }

        foreach ($configs as $name => $value) {
            // 构建查询条件：必须匹配 plugin，可选匹配 group_id
            $query = Config::where('name', $name)
                ->where('plugin', $plugin)
                ->where('delete_time', null);

            // 如果提供了 group_id，则同时匹配分组
            if (!empty($groupId)) {
                $query->where('group_id', $groupId);
            }

            $config = $query->find();
            if ($config) {
                // 如果值是数组，进行JSON编码
                if (is_array($value)) {
                    $config->value = json_encode($value, JSON_UNESCAPED_UNICODE);
                } else {
                    $config->value = $value;
                }
                $config->save();
            }
        }

        // 清除缓存
        Cache::delete(KcConst::ADMIN_APP . ':config:configs');
        return $this->success('保存成功');
    }

    /**
     * 删除配置项
     */
    public function delete(): Response
    {
        $request = $this->request;
        $id = $request->post('id');
        if (empty($id)) {
            return $this->error('配置ID不能为空');
        }
        $config = Config::withTrashed()->find($id);
        if (!$config) {
            return $this->error('配置项不存在');
        }
        // 检查是否允许删除
        if ($config->allow_del == 0) {
            return $this->error('该配置项不允许删除');
        }
        $config->force()->delete();
        Cache::delete(KcConst::ADMIN_APP . ':config:configs');
        return $this->success();
    }

    /**
     * 刷新缓存
     */
    public function refreshCache(): Response
    {
        // 清除配置缓存
        Cache::delete(KcConst::ADMIN_APP . ':config:configs');
        Cache::delete(KcConst::ADMIN_APP . ':config:groups');
        return $this->success('缓存已刷新');
    }
}
