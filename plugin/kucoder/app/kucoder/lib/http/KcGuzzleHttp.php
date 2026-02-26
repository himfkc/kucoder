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


namespace kucoder\lib\http;

use Exception;
use GuzzleHttp\Client;
use Psr\Http\Message\ResponseInterface;
use Throwable;

/**
 * GuzzleHttp请求类
 */
class KcGuzzleHttp
{
    private static ?Client $httpInstance = null;

    //请求参数 参考 https://guzzle-cn.readthedocs.io/zh-cn/latest/request-options.html
    private array $options = [
        //请求头
        'headers' => [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json, text/plain, */*',
        ],
        //自定义参数 返回完整的响应对象
        'originResponse' => false,
    ];

    /**
     * 实例化GuzzleHttp
     */
    protected function http(): ?Client
    {
        if (!self::$httpInstance) {
            self::$httpInstance = new Client([
                'timeout' => 10,              // 总超时时间 10 秒
                'connect_timeout' => 5,        // 连接超时时间 5 秒
                'http_errors' => true,         // 启用 HTTP 错误
                // 允许更多的并发连接
                'handler' => new \GuzzleHttp\HandlerStack(
                    new \GuzzleHttp\Handler\CurlMultiHandler()
                ),
            ]);
        }
        return self::$httpInstance;
    }

    /**
     * 设置http请求参数
     * @param array $options
     */
    protected function setHttpOptions(array $options): void
    {
        $this->options = array_merge($this->options, $options);
        //调试环境下不验证服务器的https证书 设置成true启用SSL证书验证，默认使用操作系统提供的CA包。
        //设置成 false 禁用证书验证(这是不安全的！) 等同于curl的curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        if (is_dev_env()) {
            $this->options['verify'] = false;
        }
    }

    /**
     * get请求
     * @throws Exception
     */
    public function get(string $uri, array $options = []): array|ResponseInterface
    {
        $this->setHttpOptions($options);
        try {
            $res = $this->http()->get($uri, $this->options);
        } catch (Throwable $t) {
            throw new Exception($t->getMessage(), $t->getCode());
        }
        if ($this->options['originResponse']) {
            return $res;
        }
        return $this->handleResponse($res);
    }

    /**
     * post请求
     * @throws Exception
     */
    public function post(string $uri, array $data = [], array $options = []): array|ResponseInterface
    {
        $this->setHttpOptions($options);
        if ($data) {
            $contentType = $this->options['headers']['Content-Type'] ?? '';
            if (str_contains($contentType, 'application/json')) {
                $this->options['json'] = $data;
            } elseif (str_contains($contentType, 'application/x-www-form-urlencoded')) {
                $this->options['form_params'] = $data;
            } else {
                $this->options['body'] = $data;  // 默认
            }
        }
        try {
            $res = $this->http()->post($uri, $this->options);
        } catch (Throwable $t) {
            throw new Exception($t->getMessage(), $t->getCode());
        }
        if ($this->options['originResponse']) {
            return $res;
        }
        return $this->handleResponse($res);
    }

    /**
     * 响应处理
     * @throws Exception
     */
    protected function handleResponse($res): array
    {
        if ($res->getStatusCode() !== 200) {
            throw new Exception('200状态码错误_' . $res->getReasonPhrase());
        }
        return json_decode((string)$res->getBody(), true);
    }


    /**
     * 自定义请求
     * @throws Exception
     */
    public function request(string $method, string $uri, array $options = []): ResponseInterface
    {
        $this->setHttpOptions($options);
        try {
            $res = $this->http()->request($method, $uri, $this->options);
        } catch (Throwable $t) {
            throw new Exception($t->getMessage(), $t->getCode());
        }
        return $res;
    }
}