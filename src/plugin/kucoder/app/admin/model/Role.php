<?php
declare(strict_types=1);

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