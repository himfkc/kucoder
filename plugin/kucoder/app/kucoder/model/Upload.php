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


namespace kucoder\model;

use support\think\Model;

/**
 * 统一上传记录表模型
 */
class Upload extends Base
{
    protected $name = 'ku_upload';

    /**
     * 根据文件名、大小和扩展名查找记录
     * @param string $fileName 文件名
     * @param int $fileSize 文件大小
     * @param string $fileExtension 文件扩展名
     * @param string|null $storage 存储引擎
     * @return Model|null
     */
    public static function findByNameSizeExt(string $fileName, int $fileSize, string $fileExtension,string $fileMineType, ?string $storage = null): ?Upload
    {
        $where = [
            'file_name' => $fileName,
            'file_size' => $fileSize,
            'file_extension' => $fileExtension,
            'mine_type' => $fileMineType,
            'status' => 1,
        ];
        if ($storage !== null) {
            $where['storage'] = $storage;
        }
        return self::where($where)->find();
    }
}
