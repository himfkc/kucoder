<?php
declare(strict_types=1);


namespace plugin\kucoder\app\admin\validate\system;


use think\Validate;

class Role extends Validate
{
    protected $rule = [
        'role_name|角色名称' => 'require',
        'rules|角色权限' => 'require',
    ];
}