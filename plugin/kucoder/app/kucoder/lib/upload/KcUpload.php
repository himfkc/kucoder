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
use Throwable;
use Exception;

/**
 * kucoder上传类库 支持本地存储\OSS存储
 */
class KcUpload
{
    /**
     * @throws Throwable
     */
    public static function getInstance(): string|null
    {
        $configs = config_in_db('kucoder');
        kc_dump('kc_config:',$configs);
        if ($configs['upload_mode'] === 'local') {
            return LocalUpload::class;
        }
        if ($configs['upload_mode'] === 'oss') {
            return OSS::class;
        }
        return null;
    }

    /**
     * @throws Exception
     */
    public static function upload():array|null
    {
        try {
            $upload = self::getInstance();
            if ($upload) {
                return $upload::upload();
            }
        } catch (Throwable $t) {
            throw new Exception('上传失败：' . $t->getMessage());
        }
        return null;
    }

}