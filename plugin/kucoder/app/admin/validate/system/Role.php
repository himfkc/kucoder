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



namespace plugin\kucoder\app\admin\validate\system;


use think\Validate;

class Role extends Validate
{
    protected $rule = [
        'role_name|角色名称' => 'require',
        'rules|角色权限' => 'require',
    ];
}