<?php
declare(strict_types=1);

namespace kucoder\controller;


/**
 * 测试控制器
 */
class TestController
{
    /**
     * 测试接口
     */
    public function index()
    {
        kc_dump('测试base_path:', [
            'base_path' => base_path(),
            'get_base_path' => get_base_path(),
            "base_path('/plugin/')" => base_path('/plugin/'),
            "base_path('\plugin\')" => base_path("\\plugin\\"),
            "get_base_path('/plugin/')" => get_base_path('/plugin/'),
        ]);
    }
}