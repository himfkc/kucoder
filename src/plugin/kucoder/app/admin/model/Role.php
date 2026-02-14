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


use plugin\kucoder\app\kucoder\model\Base;

class Role extends Base
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $name = 'ku_role';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $pk = 'role_id';

    /**
     * 指示是否自动维护时间戳
     *
     * @var bool
     */
    protected $autoWriteTimestamp = true;

    /**
     * @param string $value
     * @return string[]
     */
    public function getRulesAttr(string $value): array
    {
        return $value ? explode(',', $value) :[];
    }

    /**
     * @param array $value
     * @return string
     */
    public function setRulesAttr(array $value):string
    {
        return $value ? implode(',', $value) : '';
    }

}