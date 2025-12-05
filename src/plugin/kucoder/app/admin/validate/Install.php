<?php
declare(strict_types=1);

namespace plugin\kucoder\app\admin\validate;

use plugin\kucoder\app\kucoder\validate\BaseValidate;

class Install extends BaseValidate
{
    protected $rule = [
        'db_name|数据库名' => 'require|alphaDash',
        'db_user|数据库用户名' => 'require|alphaDash|min:4',
        'db_password|数据库密码' => 'require|alphaDash|min:4',
        'admin_username|登录用户名' => 'require|alphaDash|min:4',
        'admin_password|登录密码' => 'require|alphaDash|min:6',
    ];
}