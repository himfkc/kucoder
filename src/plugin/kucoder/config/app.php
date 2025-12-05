<?php

use support\Request;

return [
    //调试模式
    'debug' => getenv('APP_DEBUG') === 'true',
    //控制器后缀 不建议修改 否则插件代码要进行适配
    'controller_suffix' => 'Controller',
    //控制器复用 禁止开启
    'controller_reuse' => false,

    //允许跨域域名
    'allow_cors_domain' => 'localhost,127.0.0.1',
    //vue后台地址入口 默认/admin/
    'vue_admin_entry' => '/admin/',

    //jwt配置
    'jwt' => [
        //jwt加密密钥 系统自动生成
        'secret_key' => 'fkdsdsKUJHEUIH676$@(kEDHUf4kU83jTYlpy93Hn',
        //过期时间 默认3天
        'expire' => 3600 * 24 * 3,
    ],
    //cache过期时间 默认24小时
    'cache_expire_time' => 3600 * 24,

    // 验证码配置
    'click_captcha' => [
        // 模式:text=文字,icon=图标(若只有icon则适用于国际化站点)
        'mode' => ['text', 'icon'],
        // 长度
        'length' => 2,
        // 混淆点长度
        'confuse_length' => 2,
    ],

    //文件图片存储类型 1:仅本地存储 2:仅oss存储 3:本地和oss同时存储
    'upload_save_type' => 1,
    //允许上传的文件类型
    'allow_upload_extensions' => ['jpg', 'jpeg', 'png', 'gif', 'zip',
        'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'pdf', 'txt', 'mp3', 'mp4', 'avi', 'wmv', 'mkv', 'flv'],
    //允许上传的文件大小 50M
    'allow_upload_size' => 50 * 1024 * 1024,

    //成功响应状态码
    'success_code' => 1,
    //失败响应状态码
    'error_code' => 0,

    //kucoder系统级api 禁止修改
    // 'sys_no' => 'tYGjTth51hzMmLryvfyFMMHL2rnNpwsH37srHyjRWtU=',
    'sys_url' => 'http://kc.com',
    'sys_file_url' => '',
];
