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

use kucoder\model\Upload;
use Throwable;
use Exception;

class LocalUpload
{
    /**
     * @throws Exception
     */
    public static function upload(int $uid=0,string $app='admin'): array
    {
        $request = request();
        if (!$request->isPost()) {
            throw new Exception('非法请求');
        }
        //上传到哪个插件
        $plugin = $request->post('plugin', $request->plugin);
        if (!$plugin) {
            throw new Exception('上传携带的plugin字段(即上传到哪个插件)不能为空');
        }
        //获取上传的文件
        $files = $request->file();
        // kc_dump('files', $files);
        if (!$files) {
            throw new Exception('请上传文件');
        }
        $uploaded = [];
        foreach ($files as $key => $spl_file) {
            //检查文件是否符合上传要求
            KcUpload::checkFile($spl_file);

            // 检查文件是否已上传
            $fileInfo = KcUpload::checkFileExist($spl_file);
            if($fileInfo){
                $uploaded[$key] = [
                    'name' => $fileInfo['name'],
                    'url' => $fileInfo['url'],
                    'savePath' => $fileInfo['savePath']
                ];
                continue;
            }

            // 在 move 之前保存文件信息（临时文件可能被清理）
            $fileName = $spl_file->getUploadName();
            $fileSize = $spl_file->getSize();
            $fileExtension = strtolower($spl_file->getUploadExtension());

            try {
                $saveDir = $request->post('saveDir', '');
                if (!$saveDir) {
                    $fileUrl = 'app/' . $plugin . '/upload/' . date('Ymd') . '/' . $fileName;
                    $fileSavePath = get_base_path('plugin/') . $plugin . '/public/upload/' . date('Ymd') . '/' . $fileName;
                } else {
                    $fileUrl = 'app/' . $plugin . '/upload/' . trim($saveDir, '/') . '/' . $fileName;
                    $fileSavePath = get_base_path('plugin/') . $plugin . '/public/upload/' . trim($saveDir, '/') . '/' . $fileName;
                }

                $spl_file->move($fileSavePath);
                $savePath = str_replace(get_base_path(), '', $fileSavePath);

                // 保存上传记录到统一上传表
                $filePath = $fileSavePath;
                if (file_exists($filePath)) {
                    $mineType = mime_content_type($filePath) ?? 'application/octet-stream';

                    // 获取当前用户ID和IP
                    $ip = $request->getRealIp();

                    Upload::create([
                        'storage' => 'local',
                        'object_name' => $savePath,
                        'file_name' => $fileName,
                        'file_size' => $fileSize,
                        'mine_type' => $mineType,
                        'file_extension' => $fileExtension,
                        'url' => $fileUrl,
                        'bucket' => '',
                        'domain' => '',
                        'user_id' => $uid,
                        'app' => $app,
                        'ip' => $ip,
                        'uploaded' => 1,
                        'status' => 1
                    ]);
                }

                $uploaded[$key] = ['name' => $fileName, 'url' => $fileUrl, 'savePath' => $savePath];
            } catch (Throwable $t) {
                throw new Exception('上传失败 ' . $t->getMessage());
            }
        }
        return $uploaded;
    }
}