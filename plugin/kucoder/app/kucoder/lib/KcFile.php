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


namespace kucoder\lib;

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
    public static function extractZip(string $zipFile, string $pathTo): void
    {
        if (!file_exists($zipFile)) throw new Exception('ZIP文件不存在');
        $zip = new ZipArchive();
        if ($zip->open($zipFile) !== true) throw new Exception('无效的ZIP文件');
        if ($zip->extractTo($pathTo) !== true) throw new Exception('无法解压ZIP文件');
        $zip->close();
    }

}