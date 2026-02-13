<?php
declare(strict_types=1);

namespace plugin\kucoder\app\kucoder\model;

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