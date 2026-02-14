<?php

// +----------------------------------------------------------------------
// | Kucoder [ MAKE WEB FAST AND EASY ]
// +----------------------------------------------------------------------
// | Copyright (c) 2026~9999 https://kucoder.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: kucoder
// +----------------------------------------------------------------------


namespace plugin\kucoder\app\kucoder\lib\http;

interface HttpInterface
{
    public function get(string $uri, array $options = [],?callable $success_callback = null,?callable $error_callback = null);

    public function post(string $uri, array $data = [], array $options = [],?callable $success_callback = null,?callable $error_callback = null);

    public function request(string $method, string $uri, array $options = [],?callable $success_callback = null,?callable $error_callback = null);
}