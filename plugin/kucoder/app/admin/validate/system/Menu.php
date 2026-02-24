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

class Menu extends Validate
{
    protected $rule = [
        'plugin|插件标识' => 'require|alpha',
        'title|菜单名称' => 'require',
        'path|路由path' => 'kcCheckPath',
        'link_url|外链地址' => 'requireIf:type,link|kcCheckUrl',
    ];

    //默认自定义的验证方法在value为空值时不执行验证 在这里添加必须验证的字段
    protected $must = ['path'];

    /**
     * 自定义验证规则  $value当前字段值 $rule规则参数 $data全部数据
     * @param $value
     * @param $rule
     * @param $data
     * @return bool|string
     */
    protected function kcCheckPath($value, $rule, $data): bool|string
    {
        if(in_array($data['type'],['menu','button']) && empty($value)){
            return '菜单控制器和按钮的名称不能为空';
        }
        //二级目录的path不能为空
        if ($data['pid'] !== 0 && $data['type'] == 'dir' && empty($value)) {
            return '子目录名称不能为空';
        }
        return true;
    }

    protected function kcCheckUrl($value, $rule, $data): bool|string
    {
        if ($data['type'] === 'link' && !filter_var($value, FILTER_VALIDATE_URL)) {
            return '外链地址格式不正确';
        }
        return true;
    }
}