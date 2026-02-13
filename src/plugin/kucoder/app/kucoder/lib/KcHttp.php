<?php
declare(strict_types=1);

namespace plugin\kucoder\app\kucoder\lib;

use plugin\kucoder\app\kucoder\lib\http\KcGuzzleHttp;
use Psr\Http\Message\ResponseInterface;
use support\Container;

class KcHttp
{
    private KcGuzzleHttp $http;

    public function __construct()
    {
        $this->http = Container::instance('kucoder')->get('http');
    }

    public function get(string $uri, array $options = [])
    {
        return $this->http->get($uri, $options);
    }

    public function post(string $uri, array $data = [], array $options = [])
    {
        return $this->http->post($uri, $data, $options);
    }

    public function request(string $method, string $uri, array $options = []): ResponseInterface
    {
        return $this->http->request($method, $uri, $options);
    }
}