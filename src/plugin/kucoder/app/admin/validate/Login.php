<?php
declare(strict_types=1);

namespace plugin\kucoder\app\admin\validate;


class Login extends \think\Validate
{
    protected $rule = [
        'username|用户名' => 'require|alphaNum',
        'password|密码' => 'require|alphaNum|min:6',
        'code|验证码' => 'require|alphaNum',
        'uuid|uuid' => 'require',
    ];
}