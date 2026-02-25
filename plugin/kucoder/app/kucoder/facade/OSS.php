<?php
declare(strict_types=1);

namespace kucoder\facade;

use kucoder\lib\upload\OssUpload;
use think\Facade;

class OSS extends Facade
{
    protected static function getFacadeClass(): string
    {
        return OssUpload::class;
    }
}