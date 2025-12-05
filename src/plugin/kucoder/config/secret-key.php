<?php

return [
    'openssl' => [
        'secret_key' => 'CnaBrAcOoSe+OnAPiW7A/Q8/KvFSThU16xpzTZU0FAM='
    ],
    'sodium' => [
        'secretbox' => [
            'key' => 'Vn9+c56KPh9iJajuFXt0lOBwpm/rqExNSKGAKv/yWoY='
        ],
        'box' => [
            'public_key' => 'jPHM+W1JuX+eUdeK7jCTJEl95XcbYyNOmvV/X3pwSSI=',
            'private_key' => 'yTdfx7iryUbpyEAKjdSLdWVJFyU5/M29fgAdEIX3DVY='
        ],
        'sign' => [
            'public_key' => 'He7C06OCxTRuO0ztEs4dQZ2MfMRzJKkX/ZHTtdTISZQ=',
            'private_key' => 'X72CGiTXSzrfL+h/Y/b7HJstT6A5XbQfcYA0JDy+aRR7b/IAB75GOC+FZkaiT6IrtFOfXuLdrQHERUIuaQIg9Q=='
        ]
    ],
    //系统密钥 严禁修改 否则系统无法正常使用
    'kc' => [
        'appid' => 'bf0e2d2a19a64df5870692ae918102b8',
        'appsecret' => 'bedee8366b064176b8c258c8485a1539',
        'box_public_key' => 'bzIso+a8RHfechLEjb8Eawz3fLi8t/yad/erEEaoGzI=',
        'sign_public_key' => 'RjcQ449FsQilFjorHWkxvqLe1qd3++eBd1GwKmFG0FI='
    ]
];
