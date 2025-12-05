<?php

return [
    '@' => [
        // 给主项目及所有插件增加全局中间件
        plugin\kucoder\app\kucoder\middleware\CorsCheck::class
    ]
];