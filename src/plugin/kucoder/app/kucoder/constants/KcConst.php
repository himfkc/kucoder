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



namespace plugin\kucoder\app\kucoder\constants;


class KcConst
{
    const VUE_KC_ADMIN = '/vue-kc-admin/';
    const VUE_KC_ADMIN_SRC = '/vue-kc-admin/src/';

    const ADMIN_APP = 'admin';
    const ADMIN_TOKEN = 'admin_token';
    const ADMIN_USERINFO = 'admin_userInfo';
    const API_APP = 'api';
    const API_TOKEN = 'api_token';
    const API_USERINFO = 'api_userInfo';
    const APP_MINI_APP = 'app_mini';
    const APP_MINI_TOKEN = 'app_mini_token';
    const APP_MINI_USERINFO = 'app_mini_userInfo';

    //RBAC api鉴权参数配置
    const API_AUTH_CONFIG = [
        'app' => 'api',
        'table' => [
            //会员表
            'user' => 'ku_member',
            //会员角色表
            'role' => 'ku_member_role',
            //会员菜单表
            'menu' => 'ku_member_menu',
        ],
        'userField' => [
            'id' => 'm_id',
            'username' => 'username',
            'is_developer' => 'is_developer'
        ],
        'menuField' => [
            //菜单表主键
            'id' => 'id',
            //菜单名字段
            'name' => 'title',
            //父级id字段
            'pid' => 'pid',
            //菜单路径
            'path' => 'path',
        ],
    ];

    const KC_COMMAND = 'KuCoder > ';
    const ALLOWED_COMMANDS = ['php', 'composer', 'pnpm', 'npm', 'yarn', 'git'];
    const INVALID_COMMAND = '无效的命令';
    const INVALID_WORK_DIR = '无效的工作目录';
    const INVALID_COMMAND_PARAMS = '命令参数必须是数组或包含空格的字符串';
}