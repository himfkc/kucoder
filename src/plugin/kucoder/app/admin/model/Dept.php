<?php
declare(strict_types=1);


namespace plugin\kucoder\app\admin\model;

use plugin\kucoder\app\kucoder\model\Base;

class Dept extends Base
{
    /**
     * @var string
     */
    protected $name = 'ku_dept';

    /**
     * @var string 主键
     */
    protected $pk = 'dept_id';

    /**
     * @var bool
     */
    protected $autoWriteTimestamp = true;
}