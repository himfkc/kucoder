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


namespace plugin\kucoder\app\admin\controller\plugin;

use Exception;
use kucoder\constants\KcConst;
use kucoder\controller\AdminBase;
use kucoder\lib\KcFile;
use kucoder\lib\KcIdentity;
use kucoder\service\PluginService;
use kucoder\traits\HttpTrait;
use Psr\SimpleCache\InvalidArgumentException;
use ReflectionException;
use support\Response;
use support\think\Db;
use Throwable;

class PluginLocalController extends AdminBase
{
    use HttpTrait;

    protected string $modelClass = "\\plugin\\kucoder\\app\\admin\\model\\PluginLocal";
    protected array $allowAccessActions = ['index'];

    /**
     * @throws Exception
     * @throws Throwable
     */
    public function __construct()
    {
        parent::__construct();
        $this->httpUrl = getenv('KUCODER_API') . '/kapi/';
    }

    /**
     * @throws Exception
     */
    protected function index_before(): void
    {
        $this->auth->siteRight($siteRight);
        if (isset($siteRight['data']) && $siteRight['data']) {
            if (!password_verify(request()->host(true), $siteRight['data'])) {
                $this->throw($siteRight['msg']);
            }
        }
    }

    /**
     * @throws Exception|Throwable
     */
    protected function index_after(array|object $data): array
    {
        // kc_dump('插件数据1: ', $data);
        $marketPlugins = array_filter($data, fn($plugin) => $plugin['source'] === 1);
        if(!$marketPlugins) {
            return $data;
        }
        $pluginIds = array_column($marketPlugins, 'market_id', 'id');
        $uri = $this->httpUrl . 'market/version';
        $pluginIds['cookie'] = KcIdentity::getCookie($uri, $this->auth->getId());
        $res = $this->http_post($uri, $pluginIds);
        kc_dump('远程插件版本数据: ', $res);
        foreach ($data as &$plugin) {
            if ($plugin['source'] === 1 && isset($res['data'][$plugin['id']])) {
                $versions = $res['data'][$plugin['id']];
                $plugin['version_latest'] = reset($versions);
                $plugin['version_has_update'] = version_compare($plugin['version_latest'], $plugin['version'], '>');
                $geVersions = array_filter($versions, fn($v) => version_compare($v, $plugin['version'], '>='));
                $ltVersions = array_filter($versions, fn($v) => version_compare($v, $plugin['version'], '<'));
                $ltVersions = $ltVersions ? [reset($ltVersions)] : [];
                $plugin['versions'] = array_map(fn($v) => [
                    'version' => $v,
                    'compare' => version_compare($v, $plugin['version'])
                ], array_merge($geVersions, $ltVersions));
            }
        }
        // kc_dump('插件数据2: ', $data);
        return $data;
    }

    /**
     * @throws Throwable
     */
    public function install(): Response
    {
        $plugin = $this->request->post();
        try {
            $plugin['source'] === 1 && $this->download($plugin);
            PluginService::install($plugin['name'], $plugin['version']);
            //安装成功 更新安装状态
            $pluginLocal = $this->model->find($plugin['id']);
            $pluginLocal->install = 1;
            $pluginLocal->save();
            kc_dump(KcConst::KC_COMMAND . '安装完成，请重启后端webman');
            return $this->success('请重启后端webman');
        } catch (Throwable $t) {
            kc_dump(KcConst::KC_COMMAND . '安装未完成: ', $this->errorInfo($t));
            return $this->error('安装失败: ' . $t->getMessage(), [], $t->getCode());
        }
    }

    /**
     * 卸载插件
     * @return Response
     */
    public function uninstall(): Response
    {
        $plugin = $this->request->post();
        try {
            PluginService::uninstall($plugin['name'], $plugin['version']);
            //卸载成功 更新安装状态
            $pluginLocal = $this->model->find($plugin['id']);
            if ($pluginLocal->source === 1) {
                $pluginLocal->install = 0;
                $pluginLocal->save();
            } else {
                //本地调试插件 卸载删除数据库记录
                $pluginLocal->force()->delete();
            }
            kc_dump(KcConst::KC_COMMAND . '卸载完成');
            return $this->success('请重启后端webman');
        } catch (Throwable $t) {
            kc_dump(KcConst::KC_COMMAND . '卸载未完成: ', $this->errorInfo($t));
            return $this->error('卸载失败: ' . $t->getMessage(), [], $t->getCode());
        }
    }

    public function update(): Response
    {
        $plugin = $this->request->post();
        try {
            $plugin['source'] === 1 && $this->download($plugin);
            PluginService::update($plugin['name'], $plugin['version']);
            //升级成功 更新安装版本
            $pluginLocal = $this->model->find($plugin['id']);
            $pluginLocal->version = $plugin['version'];
            $pluginLocal->save();
            kc_dump(KcConst::KC_COMMAND . '升级完成');
            return $this->success('请重启后端webman');
        } catch (Throwable $t) {
            kc_dump(KcConst::KC_COMMAND . '升级未完成: ', $this->errorInfo($t));
            return $this->error('升级失败: ' . $t->getMessage(), [], $t->getCode());
        }
    }

    /**
     * @throws ReflectionException
     * @throws InvalidArgumentException
     * @throws Exception|Throwable
     */
    private function download(array $plugin): void
    {
        $pluginPath = base_path("plugin/{$plugin['name']}");
        $saveDir = base_path("plugin/kucoder/app/kucoder/zip/{$plugin['name']}");
        if (!is_dir($saveDir)) {
            mkdir($saveDir, 0755, true);
        }
        $savePath = $saveDir . '/' . $plugin['name'] . '_' . $plugin['version'] . '.zip';
        if (!is_file($savePath)) {
            kc_dump('开始下载插件');
            $uri = $this->httpUrl . 'market/download';
            $downOptions = ['save_file_path' => $savePath];
            $plugin['cookie'] = KcIdentity::getCookie($uri, $this->auth->getId(), $this->app);
            $plugin['site_host'] = request()->host(true);
            $this->http_download($uri, $plugin, $downOptions);
        }
        KcFile::extractZip($savePath, $pluginPath);
    }

    /**
     * 导入本地调试插件
     * @return Response
     * @throws Exception
     */
    public function importLocalPlugin(): Response
    {
        $this->auth->siteRight($siteRight);
        if (isset($siteRight['data']) && $siteRight['data']) {
            if (!password_verify(request()->host(true), $siteRight['data'])) {
                $this->throw($siteRight['msg']);
            }
        }
        Db::startTrans();
        try {
            $post = $this->request->post();
            $zipPath = isset($post['savePath']) ? base_path($post['savePath']) : '';
            $pluginName = str_contains($post['name'], '_') ? explode('_', $post['name'])[0] : '';
            if (!$pluginName) return $this->error('插件名不存在');
            $pluginPath = base_path("plugin/{$pluginName}");
            KcFile::extractZip($zipPath, $pluginPath);
            //写入更新数据库
            $plugin = $this->model->where('name', $pluginName)->find();
            $info = get_plugin_info($pluginName);
            $info['plugin_type'] = $info['type'];
            $info['source'] = 2;
            if ($plugin) {
                $this->model->save($info, ['id' => $plugin->id]);
            } else {
                $this->model->save($info, true);
            }
            unlink($zipPath);
            Db::commit();
            return $this->success('导入成功');
        } catch (Throwable $t) {
            Db::rollBack();
            kc_dump('导入本地调试插件异常: ', $this->errorInfo($t));
            return $this->error('导入本地调试插件失败: ' . $t->getMessage(), [], $t->getCode());
        }
    }


}