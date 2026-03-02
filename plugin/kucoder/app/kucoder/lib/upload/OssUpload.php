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
use kucoder\model\Upload;
use Throwable;

/**
 * OSS对象存储
 */
class OssUpload
{
    private string $driver = '';

    /**
     * @throws Exception|Throwable
     */
    public function __construct()
    {
        $kc_config = config_in_db('kucoder');

        // 验证配置
        if (!is_array($kc_config)) {
            throw new Exception('配置读取失败');
        }

        if ($kc_config['upload_mode'] === 'local') {
            throw new Exception('当前存储为本地存储');
        }

        // 获取 OSS 驱动类名
        $ossPlugin = match ($kc_config['oss_type']) {
            //阿里云oss对象存储
            'alioss' => \plugin\alioss\api\PluginAlioss::class ?? null,
            //腾讯云cos对象存储
            'txoss' => \plugin\txoss\api\PluginTxoss::class ?? null,
            //七牛云对象存储
            'qnoss' => \plugin\qnoss\api\PluginQnoss::class ?? null,
            //华为云对象存储
            'hwoss' => \plugin\hwoss\api\PluginHwoss::class ?? null,
            //默认
            default => null,
        };

        if (!$ossPlugin) {
            throw new Exception('未知的 OSS 类型: ' . ($kc_config['oss_type'] ?? '未指定'));
        }

        if (!class_exists($ossPlugin)) {
            throw new Exception('OSS 驱动类不存在: ' . $ossPlugin);
        }

        $this->driver = $ossPlugin;
    }

    /**
     * 上传文件
     * @param int $uid 后台用户id或前台会员id
     * @param string $app 后台或前台
     * @return array
     * @throws Throwable
     */
    public function upload(int $uid = 0, string $app = 'admin'): array
    {
        $request = request();
        if (!$request->isPost()) {
            throw new Exception('非法请求');
        }
        // 获取上传的文件
        $files = $request->file();
        if (!$files) {
            throw new Exception('请上传文件');
        }
        $uploaded = [];
        foreach ($files as $key => $spl_file) {
            //检查文件是否符合上传要求
            KcUpload::checkFile($spl_file);

            // 检查文件是否已存在
            $fileInfo = KcUpload::checkFileExist($spl_file);
            if ($fileInfo) {
                $uploaded[$key] = [
                    'name' => $fileInfo['name'],
                    'url' => $fileInfo['url'],
                ];
                continue;
            }
            // 获取远程对象名称
            $fileName = $spl_file->getUploadName();
            // 获取插件名作为路径前缀
            $plugin = $request->post('plugin', $request->plugin);
            $saveDir = $request->post('saveDir', '');
            // 添加路径前缀
            if ($saveDir) {
                $remoteObject = 'app/' . $plugin . '/upload/' . trim($saveDir, '/') . '/' . $fileName;
            } else {
                $remoteObject = 'app/' . $plugin . '/upload/' . date('Ymd') . '/' . $fileName;
            }
            // 使用本地临时文件路径上传
            $localFile = $spl_file->getRealPath();

            // 获取文件信息
            $fileSize = $spl_file->getSize();
            $fileExtension = strtolower($spl_file->getUploadExtension());
            $mineType = 'application/octet-stream';
            if (file_exists($localFile)) {
                $mineType = mime_content_type($localFile) ?? 'application/octet-stream';
            }

            // 调用底层驱动上传
            ($this->driver)::upload($localFile, $remoteObject, []);

            // 获取当前存储引擎类型
            $kc_config = config_in_db('kucoder');
            $storageMap = [
                'alioss' => 'alioss',
                'txoss' => 'txcos',
                'qnoss' => 'qnoss',
                'hwoss' => 'hwobs',
            ];
            $storage = $storageMap[$kc_config['oss_type']] ?? 'local';

            // 获取存储桶和域名信息(通过配置获取)
            $bucket = '';
            $domain = '';
            try {
                $ossPluginConfig = config_in_db($kc_config['oss_type']);
                if (is_array($ossPluginConfig)) {
                    $bucket = $ossPluginConfig['bucket'] ?? '';
                    $domain = $ossPluginConfig['domain'] ?? '';
                }
            } catch (\Throwable) {
                // 获取配置失败不影响上传流程
            }

            // 获取当前用户ID和IP
            $ip = $request->getRealIp();

            // 保存上传记录到统一上传表
            Upload::create([
                'storage' => $storage,
                'object_name' => $remoteObject,
                'file_name' => $fileName,
                'file_size' => $fileSize,
                'mine_type' => $mineType,
                'file_extension' => $fileExtension,
                'url' => $remoteObject,
                'bucket' => $bucket,
                'domain' => $domain,
                'user_id' => $uid,
                'app' => $app,
                'ip' => $ip,
                'uploaded' => 1,
                'status' => 1,
                'create_time' => time(),
                'update_time' => time(),
            ]);

            // 结果
            $uploaded[$key] = [
                'name' => $fileName,
                // 'url' => $url['data']['url'] ?? $remoteObject,
                'url' => $remoteObject,
            ];
        }
        return $uploaded;
    }

    /**
     * 魔术方法，动态调用底层驱动的方法
     * @param string $method 方法名
     * @param array $args 方法参数数组
     * @return mixed
     * @throws Exception
     */
    public function __call(string $method, array $args): mixed
    {
        $driver = $this->driver;

        if (!method_exists($driver, $method)) {
            throw new Exception('方法不存在: ' . $driver . '::' . $method);
        }

        return $driver::$method(...$args);
    }
}