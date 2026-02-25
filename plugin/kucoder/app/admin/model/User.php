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

class User extends Base
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $name = 'ku_user';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $pk = 'user_id';

    /**
     * 指示是否自动维护时间戳
     *
     * @var bool
     */
    protected $autoWriteTimestamp = true;

    public function dept(): \think\model\relation\HasOne
    {
        return $this->hasOne(Dept::class, 'dept_id', 'dept_id');
    }

    public function getRoleIdsAttr(string $value,$data): array
    {
        return $value ? explode(',', $value) : [];
    }

    public function setRoleIdsAttr(array $value): string
    {
        // kc_dump('要更新的role_Ids为：',$value);
        return $value ? implode(',', $value) : '';
    }
}