<?php

return [
    'openssl' => [
        'secret_key' => 'F9s6zvTK5KDmlS71RJiqvV3rYMZIMG1+fv96udxwRmI='
    ],
    'sodium' => [
        "secretbox" => [
            "key" => "OKu4OtlsmtGVEmNu/VQRdZv+efJuhlv8djCNTpM6IdU="
        ],
        "box" => [
            "public_key" => "OBQWVM4QkilaGAAX217GeeoHoA1mpF3ubBy4sLY9PgQ=",
            "private_key" => "H804gjV5iAqJ2+qxZOogSvwVuRMFvQDzeqEIBo5Mg7s="
        ],
        "sign" => [
            "public_key" => "jfcgg2XvwEMOJpCbiZKBQNqfc7/3LHdQkAPx9zJ7Ic4=",
            "private_key" => "UiCSJo1HTkfeRy4VcOqgz2dCPtx7T+36hMHlm1GluPSN9yCDZe/AQw4mkJuJkoFA2p9zv/csd1CQA/H3Mnshzg=="
        ]
    ],
    //系统密钥 严禁修改 否则系统无法正常使用
    'kc' => [
        'appid' => 'bf0e2d2a19a64df5870692ae918102b8',
        'appsecret' => 'bedee8366b064176b8c258c8485a1539',
        'box_public_key' => 'OBQWVM4QkilaGAAX217GeeoHoA1mpF3ubBy4sLY9PgQ=',
        'sign_public_key' => 'jfcgg2XvwEMOJpCbiZKBQNqfc7/3LHdQkAPx9zJ7Ic4='
    ]
];
