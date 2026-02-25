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


namespace plugin\kucoder\app\admin\model;

use kucoder\model\Base;

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
