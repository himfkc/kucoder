<?php
declare(strict_types=1);

namespace plugin\kucoder\app\admin\model;

use plugin\kucoder\app\kucoder\model\Base;

class Config extends Base
{
    protected $name = 'ku_config';

    protected $json = ['config_data'];

    /**
     * 关联配置分组
     */
    public function configGroup()
    {
        return $this->belongsTo(ConfigGroup::class, 'group_id', 'id');
    }
}
