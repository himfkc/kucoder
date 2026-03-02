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

use kucoder\facade\OSS;
use kucoder\model\Upload;
use kucoder\traits\ResponseTrait;
use Throwable;
use Exception;
use Webman\Http\UploadFile;

/**
 * kucoder上传类库 支持本地存储\OSS存储
 */
class KcUpload
{
    use ResponseTrait;

    /**
     * @throws Throwable
     */
    public static function getInstance(): string|null
    {
        $configs = config_in_db('kucoder');
        if ($configs['upload_mode'] === 'local') return LocalUpload::class;
        if ($configs['upload_mode'] === 'oss') return OSS::class;
        return null;
    }

    /**
     * $uid 用户id
     * $app 应用名称 后台:admin 前台:api APP或小程序:app_mini
     * @throws Exception
     */
    public static function upload(int $uid=0,string $app='admin'):array|null
    {
        try {
            $upload = self::getInstance();
            if ($upload) {
                return $upload::upload($uid,$app);
            }
        } catch (Throwable $t) {
            kc_dump('上传失败：',(new self)->errorInfo($t));
            throw new Exception('上传失败：' . $t->getMessage());
        }
        return null;
    }

    /**
     * @throws Exception
     */
    public static function checkFile(UploadFile $spl_file):void
    {
        // 检查文件有效性
        if (!$spl_file->isValid()) {
            throw new Exception('上传的文件无效');
        }
        // 先检查临时文件是否存在
        $realPath = $spl_file->getRealPath();
        if ($realPath && !file_exists($realPath)) {
            throw new Exception('上传的临时文件不存在');
        }
        // 检查文件扩展名
        if (!in_array($spl_file->getUploadExtension(), get_env('allow_upload_extensions'))) {
            throw new Exception('不允许上传此类文件');
        }
        // 检查文件大小
        if ($spl_file->getSize() > get_env('allow_upload_size')) {
            throw new Exception('上传的文件过大');
        }
    }

    /**
     * 检查要上传的文件是否已存在 若存在则返回已存储的文件信息
     * @param UploadFile $file
     * @return array|false
     */
    public static function checkFileExist(UploadFile $file):array|false
    {
        try {
            // 获取文件名、大小和扩展名
            $fileName = $file->getUploadName();
            $fileSize = $file->getSize();
            $fileExtension = strtolower($file->getUploadExtension());
            $fileMineType = $file->getUploadMimeType();
            // 如果文件名为空、大小为0或扩展名为空,直接返回false
            if ($fileName === '' || $fileExtension === '') {
                return false;
            }
            // 获取当前存储引擎
            $configs = config_in_db('kucoder');
            $storage = 'local';
            if ($configs['upload_mode'] === 'oss' && !empty($configs['oss_type'])) {
                // 将oss_type映射到storage字段
                $storageMap = [
                    'alioss' => 'alioss',
                    'txoss' => 'txcos',
                    'qnoss' => 'qnoss',
                    'hwoss' => 'hwobs',
                ];
                $storage = $storageMap[$configs['oss_type']] ?? 'local';
            }
            // 通过文件名、大小和扩展名查找
            $uploadModel = Upload::findByNameSizeExt($fileName, $fileSize, $fileExtension,$fileMineType, $storage);
            // 如果找到记录,返回文件信息
            if ($uploadModel && $uploadModel->uploaded == 1) {
                return [
                    'id' => $uploadModel->id,
                    'name' => $uploadModel->file_name,
                    'url' => $uploadModel->url,
                    'savePath' => $uploadModel->object_name,
                ];
            }
            return false;
        } catch (\Throwable $t) {
            // 任何异常都返回false,不影响上传流程
            return false;
        }
    }

}