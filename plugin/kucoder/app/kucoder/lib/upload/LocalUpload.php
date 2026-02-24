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


namespace plugin\kucoder\app\kucoder\lib\upload;

use Throwable;
use Exception;

class LocalUpload
{
    /**
     * @throws Exception
     */
    public static function upload(): array
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
        $file = $request->file();
        kc_dump('file', $file);
        if (!$file) {
            throw new Exception('请上传文件');
        }
        $uploadedPath = [];
        foreach ($file as $key => $spl_file) {
            if (!$spl_file->isValid()) {
                throw new Exception('上传的文件无效');
            }
            if (!in_array($spl_file->getUploadExtension(), config('plugin.kucoder.app.allow_upload_extensions'))) {
                throw new Exception('不允许上传此类文件');
            }
            if ($spl_file->getSize() > config('plugin.kucoder.app.allow_upload_size')) {
                throw new Exception('上传的文件过大');
            }
            try {
                $saveDir = $request->post('saveDir', '');
                if (!$saveDir) {
                    $fileUrl = '/app/'.$plugin . '/upload/' . date('Ymd') . '/' . $spl_file->getUploadName();
                    $fileSavePath = base_path('/plugin/') . $plugin . '/public/upload/' . date('Ymd') . '/' . $spl_file->getUploadName();
                } else {
                    $fileUrl = '/app/'.$plugin . '/upload/' . trim($saveDir, '/') . '/' . $spl_file->getUploadName();
                    $fileSavePath = base_path('/plugin/') . $plugin . '/public/upload/' . trim($saveDir, '/') . '/' . $spl_file->getUploadName();
                }
                $spl_file->move($fileSavePath);
                $savePath = str_replace(base_path(), '', $fileSavePath);
                $uploadedPath[$key] = ['name' => $spl_file->getUploadName(), 'url' => $fileUrl ?? '', 'savePath' => $savePath];
            } catch (Throwable $t) {
                throw new Exception('上传失败 ' . $t->getMessage());
            }
        }
        // return $uploadedPath;
        return [
            'success' => true,
            'message' => '上传成功',
            'data' => $uploadedPath
        ];
    }
}