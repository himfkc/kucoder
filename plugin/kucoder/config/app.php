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
    //调试环境下允许所有跨域访问
    'allow_cors_dev' => true,
    //vue后台地址入口 默认/admin/ 后台访问方式：域名/vue部署子目录/vue后台地址入口
    'vue_admin_entry' => '/admin/',
    //jwt配置
    'jwt' => [
        //jwt加密密钥 系统自动生成
        'secret_key' => 'kus#%sKUkdefdez29g6$@(kEDHUf4kU83jTYlpy93Hn',
        //过期时间 默认3天
        'expire' => 3600 * 24 * 3,
    ],
    // 验证码配置
    'click_captcha' => [
        // 模式:text=文字,icon=图标(若只有icon则适用于国际化站点)
        'mode' => ['text', 'icon'],
        // 长度
        'length' => 2,
        // 混淆点长度
        'confuse_length' => 2,
    ],
    //成功响应状态码
    'success_code' => 1,
    //失败响应状态码
    'error_code' => 0,
    //cache过期时间 24小时
    'cache_expire_time' => 3600 * 24,

    //上传需要登录
    'upload_need_login' => true,
    //文件图片存储类型 1:仅本地存储 2:仅oss存储 3:本地和oss同时存储
    'upload_save_type' => 1,
    //允许上传的文件类型
    'allow_upload_extensions' => ['jpg', 'jpeg', 'png', 'gif', 'zip',
        'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'pdf', 'txt', 'mp3', 'mp4', 'avi', 'wmv', 'mkv', 'flv'],
    //允许上传的文件大小 50M
    'allow_upload_size' => 50 * 1024 * 1024,


    //kucoder系统级api 勿修改
    'sys_url' => 'https://api.kucoder.com',
    'sys_file_url' => '',


    /**
     * 以下是插件开发配置项 参考
     */
    //插件标识 全网唯一
    'name' => 'kucoder',
    //插件名称
    'title' => 'Kucoder快速开发框架',
    //插件版本号
    'version' => '1.0.0',
    //插件依赖的最低kucoder版本
    'kucoder_version' => '1.0.0',
    //插件类型 0=辅助开发插件,1=完整独立系统,2=完整sass系统,3=物联网应用
    'type' => 1,
    //插件是否收费 付费类型:0=免费,1=收费
    'fee_type' => 0,
    //普通授权收费价格 如99.9 普通授权的购买者仅能用于自营项目 更新维护期限为一年
    'common_price' => 0,
    //高级授权收费价格 如999.9 高级授权的购买者能用于自营及外包项目 更新维护期限为10年
    'advance_price' => 0,
    //插件主页地址
    'homepage' => '',
    //插件文档地址
    'doc_url' => '',
    //是否创建插件目录 插件目录:0=不创建,1=创建
    'has_dir' => 1,
    //插件是否含有数据表 0=不含,1=包含
    'has_db' => 1,
    //插件是否含有vue模板 0=不含,1=包含
    'has_view' => 1,
];
