<?php
return [
    //调试模式
    'debug' => getenv('APP_DEBUG') === 'true',
    //控制器后缀 不建议修改 否则插件代码要进行适配
    'controller_suffix' => 'Controller',
    //控制器复用 禁止开启
    'controller_reuse' => false,
    //成功响应状态码
    'success_code' => 1,
    //失败响应状态码
    'error_code' => 0,

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
