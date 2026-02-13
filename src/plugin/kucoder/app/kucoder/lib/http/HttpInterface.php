<?php

namespace plugin\kucoder\app\kucoder\lib\http;

interface HttpInterface
{
    public function get(string $uri, array $options = [],?callable $success_callback = null,?callable $error_callback = null);

    public function post(string $uri, array $data = [], array $options = [],?callable $success_callback = null,?callable $error_callback = null);

    public function request(string $method, string $uri, array $options = [],?callable $success_callback = null,?callable $error_callback = null);
}