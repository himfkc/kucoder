<?php
declare(strict_types=1);

namespace plugin\kucoder\app\kucoder\lib;

use plugin\kucoder\app\kucoder\lib\http\HttpInterface;
use plugin\kucoder\app\kucoder\lib\http\KcWorkerHttp;
use Psr\Http\Message\ResponseInterface;
use support\Container;

class KcHttpAsync
{
    private KcWorkerHttp $http;

    public function __construct()
    {
        $this->http = Container::instance('kucoder')->get('http_async');
    }

    public function get(string $uri, array $options = [], ?callable $success_callback = null, ?callable $error_callback = null): void
    {
        $this->http->get($uri, $options, $success_callback, $error_callback);
    }

    public function post(string $uri, array $data = [], array $options = [], ?callable $success_callback = null, ?callable $error_callback = null): void
    {
        $this->http->post($uri, $data, $options, $success_callback, $error_callback);
    }

    public function request(string $method, string $uri, array $options = [], ?callable $success_callback = null, ?callable $error_callback = null): void
    {
        $this->http->request($method, $uri, $options, $success_callback, $error_callback);
    }
}