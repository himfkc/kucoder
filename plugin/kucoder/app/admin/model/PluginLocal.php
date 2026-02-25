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

class PluginLocal extends Base
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $name = 'ku_plugin_local';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $pk = 'id';

    /**
     * 指示是否自动维护时间戳
     *
     * @var bool
     */
    protected $autoWriteTimestamp = true;
}