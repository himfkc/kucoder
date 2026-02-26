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