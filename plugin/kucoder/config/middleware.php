<?php

return [
    '@' => [
        // 超全局中间件 适用于主项目及所有插件
        kucoder\middleware\CorsCheck::class
    ]
];