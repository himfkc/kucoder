<?php
declare(strict_types=1);

// +----------------------------------------------------------------------
// | Kucoder [ MAKE WEB FAST AND EASY ]
// +----------------------------------------------------------------------
// | Copyright (c) 2026~9999 https://kucoder.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: kucoder
// +----------------------------------------------------------------------


namespace kucoder\lib;

use kucoder\lib\http\KcGuzzleHttp;
use Psr\Http\Message\ResponseInterface;
use support\Container;

class KcHttp
{
    private KcGuzzleHttp $http;

    public function __construct()
    {
        $this->http = Container::instance('kucoder')->get('http');
        kc_dump('$this->http get_class:', get_class($this->http));
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