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

namespace kucoder\lib\upload;

use Exception;
use kucoder\interfaces\OssInterface;
use Throwable;

/**
 * OSS对象存储
 */
class OssUpload
{
    private string $driver = '';

    /**
     * @throws Exception
     */
    public function __construct()
    {
        try {
            $kc_config = config_in_db('kucoder');
            if ($kc_config['upload_mode'] === 'local') {
                throw new Exception('当前存储为本地存储');
            }
            $ossPlugin = match ($kc_config['oss_type']) {
                //阿里云oss对象存储
                'alioss' => fn() => \plugin\alioss\api\PluginAlioss::class ?? null,
                //腾讯云cos对象存储
                'txoss' => fn() => \plugin\txoss\api\PluginTxoss::class ?? null,
                //七牛云对象存储
                'qnoss' => fn() => \plugin\qnoss\api\PluginQnoss::class ?? null,
                //华为云对象存储
                'hwoss' => fn() => \plugin\hwoss\api\PluginHwoss::class ?? null,
                //默认
                default => fn() => null,
            };
            if (!class_exists($ossPlugin())) {
                throw new Exception($ossPlugin() . '不存在');
            }
            $this->driver = $ossPlugin();
        } catch (Throwable $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * 上传文件
     * @return array
     * @throws Exception
     */
    public function upload(): array
    {
        $request = request();
        if (!$request->isPost()) {
            throw new Exception('非法请求');
        }
        // 获取上传的文件
        $file = $request->file();
        if (!$file) {
            throw new Exception('请上传文件');
        }
        $uploaded = [];
        foreach ($file as $key => $spl_file) {
            if (!$spl_file->isValid()) {
                throw new Exception('上传的文件无效');
            }
            // 检查文件扩展名
            if (!in_array($spl_file->getUploadExtension(), config('plugin.kucoder.app.allow_upload_extensions'))) {
                throw new Exception('不允许上传此类文件');
            }
            // 检查文件大小
            if ($spl_file->getSize() > config('plugin.kucoder.app.allow_upload_size')) {
                throw new Exception('上传的文件过大');
            }
            // 获取远程对象名称
            $remoteObject = $spl_file->getUploadName();
            // 获取插件名作为路径前缀
            $plugin = $request->post('plugin', $request->plugin);
            $saveDir = $request->post('saveDir', '');
            // 添加路径前缀
            if ($saveDir) {
                $remoteObject = '/app/' . $plugin . '/upload/' . trim($saveDir, '/') . '/' . $remoteObject;
            } else {
                $remoteObject = '/app/' . $plugin . '/upload/' . date('Ymd') . '/' . $remoteObject;
            }
            // 使用本地临时文件路径上传
            $localFile = $spl_file->getRealPath();
            // 调用底层驱动上传
            $result = ($this->driver)::uploadFile($localFile, $remoteObject, []);
            // 获取实际访问 URL
            $url = ($this->driver)::getUrl($remoteObject, 0);
            // 结果
            $uploaded[$key] = [
                'name' => $spl_file->getUploadName(),
                'url' => $url['data']['url'] ?? $remoteObject,
            ];
        }
        return $uploaded;
    }

    /**
     * 魔术方法，动态调用底层驱动的方法
     * @param string $method 方法名
     * @param mixed $args 方法参数
     * @return mixed
     * @throws Exception
     */
    public function __call(string $method, mixed ...$args): mixed
    {
        return ($this->driver)::$method(...$args);
    }
}