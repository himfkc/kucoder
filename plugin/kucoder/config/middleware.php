<?php

return [
    '@' => [
        // 超全局中间件 适用于主项目及所有插件
        plugin\kucoder\app\kucoder\middleware\CorsCheck::class
    ]
];