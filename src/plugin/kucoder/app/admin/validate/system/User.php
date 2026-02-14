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

class User extends Validate
{
    protected $rule = [
        'username|用户名' => 'require|alphaNum|min:2|unique:ku_user,username',
        'password|密码' => 'require|alphaNum|min:6',
        'role_ids|角色' => 'require',
        'email|邮箱' => 'checkEmail',
        'mobile|手机号' => 'checkMobile',
    ];

    protected $scene = [
        'add' => ['username', 'password', 'role_ids','email','mobile'],
        'edit' => ['role_ids','email','mobile']
    ];

    protected function checkEmail($value): bool|string
    {
        if($value && !filter_var($value,FILTER_VALIDATE_EMAIL)){
            return '邮箱格式不正确';
        }
        return true;
    }

    protected function checkMobile($value):bool|string
    {
        if($value && !preg_match('/^1[3-9]\d{9}$/',$value)){
            return '手机号码格式不正确';
        }
        return true;
    }
}