<?php
declare(strict_types=1);

namespace plugin\kucoder\app\admin\validate;


class Login extends \think\Validate
{
    protected $rule = [
        'username|用户名' => 'require',
        'password|密码' => 'require|min:6',
        'code|验证码' => 'require',
        'uuid|uuid' => 'require',
    ];
}