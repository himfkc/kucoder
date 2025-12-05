<?php
declare(strict_types=1);


namespace plugin\kucoder\app\admin\validate\system;


class Dept extends \think\Validate
{
    protected $rule = [
        'dept_name|部门名称' => 'require'
    ];

}