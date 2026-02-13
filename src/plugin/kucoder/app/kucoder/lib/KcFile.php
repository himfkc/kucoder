<?php
declare(strict_types=1);

namespace plugin\kucoder\app\kucoder\lib;

use Exception;
use ZipArchive;

class KcFile
{
    /*
     * 删除目录及目录下的文件
     */
    public static function delDirWithFile($dir): bool
    {
        $files = array_diff(scandir($dir), array('.', '..'));
        foreach ($files as $file) {
            (is_dir("$dir/$file")) ? self::delDirWithFile("$dir/$file") : unlink("$dir/$file");
        }
        return rmdir($dir);
    }

    /**
     * 递归复制目录及目录下的文件
     */
    public static function copyDirWithFile(string $source, string $destination): bool
    {
        // 如果源不是目录，直接返回失败
        if (!is_dir($source)) {
            return false;
        }

        // 如果目标目录不存在，则尝试创建
        if (!is_dir($destination)) {
            if (!mkdir($destination, 0755, true)) {
                return false;
            }
        }

        // 打开源目录
        $dir = opendir($source);
        if (!$dir) {
            return false;
        }

        // 遍历目录中的每一项
        while (($file = readdir($dir)) !== false) {
            if ($file === '.' || $file === '..') {
                continue; // 跳过 . 和 ..
            }

            $srcPath = $source . DIRECTORY_SEPARATOR . $file;
            $destPath = $destination . DIRECTORY_SEPARATOR . $file;

            if (is_dir($srcPath)) {
                // 如果是目录，递归复制
                if (!self::copyDirwithFile($srcPath, $destPath)) {
                    closedir($dir);
                    return false;
                }
            } else {
                // 如果是文件，直接复制
                if (!copy($srcPath, $destPath)) {
                    closedir($dir);
                    return false;
                }
            }
        }

        closedir($dir);
        return true;
    }

    public static function dirHasFiles($dir): bool
    {
        if (!is_dir($dir)) return false;
        $files = glob($dir . '/*');
        return $files && count($files) > 0;
    }

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
            } catch (\Throwable $t) {
                throw new Exception('上传失败 ' . $t->getMessage());
            }
        }
        return $uploadedPath;
    }

    /**
     * @throws Exception
     */
    public static function extractZip(string $zipFile, string $pathTo): void
    {
        if (!file_exists($zipFile)) throw new Exception('ZIP文件不存在');
        $zip = new ZipArchive();
        if ($zip->open($zipFile) !== true) throw new Exception('无效的ZIP文件');
        if ($zip->extractTo($pathTo) !== true) throw new Exception('无法解压ZIP文件');
        $zip->close();
    }

}