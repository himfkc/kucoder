<?php

return [
    'openssl' => [
        'secret_key' => ''
    ],
    'sodium' => [
        "secretbox" => [
            "key" => ""
        ],
        "box" => [
            "public_key" => "",
            "private_key" => ""
        ],
        "sign" => [
            "public_key" => "",
            "private_key" => ""
        ]
    ],
    //系统密钥 严禁修改 否则系统无法正常使用
    'kc' => [
        'appid' => '',
        'appsecret' => '',
        'box_public_key' => '',
        'sign_public_key' => ''
    ]
];
