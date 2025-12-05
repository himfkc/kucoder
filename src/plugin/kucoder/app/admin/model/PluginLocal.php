<?php
declare(strict_types=1);

namespace plugin\kucoder\app\admin\model;

use plugin\kucoder\app\kucoder\model\Base;

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