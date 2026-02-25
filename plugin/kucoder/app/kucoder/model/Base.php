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
use think\model\concern\SoftDelete;

/**
 * 基础模型
 */
class Base extends Model
{
    // 软删除
    use SoftDelete;
    protected string $deleteTime = 'delete_time';
    protected $defaultSoftDelete = NULL;

}