<?php
declare(strict_types=1);

namespace plugin\kucoder\app\kucoder\model;

use plugin\kucoder\app\admin\model\Member;
use plugin\kucoder\app\admin\model\User;
use think\model\relation\BelongsTo;

class Log extends Base
{
    protected $name = 'ku_log';

    /**
     * 关联管理员
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'uid');
    }

    /**
     * 关联会员
     * @return BelongsTo
     */
    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class, 'm_id', 'uid');
    }
}