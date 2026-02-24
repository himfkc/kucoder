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