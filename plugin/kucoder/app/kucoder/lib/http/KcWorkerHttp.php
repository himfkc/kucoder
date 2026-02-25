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

use Workerman\Http\Client;
use Workerman\Coroutine;
use Throwable;

/**
 * Workerman Http请求类
 * 基于workerman/http-client组件
 * 真正的异步请求，支持回调函数
 */
class KcWorkerHttp
{
    private static ?Client $httpInstance = null;

    //请求参数
    private array $options = [
        'headers' => [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json, text/plain, */*',
        ],
        // SSL 验证选项
        'verify' => true,  // 默认启用 SSL 验证
    ];

    /**
     * 实例化Workerman Http Client
     */
    protected function http(): Client
    {
        if (!self::$httpInstance) {
            $config = [
                'max_conn_per_addr' => 128,  // 每个域名最多维持多少并发连接
                'keepalive_timeout' => 15,   // 连接多长时间不通讯就关闭
                'connect_timeout'   => 5,    // 连接超时时间
                'timeout'           => 10,   // 请求发出后等待响应的超时时间
            ];

            // 调试环境下不验证SSL证书
            if (function_exists('is_dev_env') && is_dev_env()) {
                $config['verify'] = false;
                $this->options['verify'] = false;
            }

            // 使用 KcVerifyHttps 获取 SSL 上下文配置
            if ($this->options['verify'] !== false) {
                try {
                    $sslOptions = KcVerifyHttps::applyOptions(['verify' => $this->options['verify']]);
                    $config['context'] = [
                        'ssl' => $sslOptions
                    ];
                } catch (Throwable $t) {
                    // SSL 配置失败，记录日志但不阻止请求
                    error_log('KcWorkerHttp SSL config error: ' . $t->getMessage());
                }
            }

            self::$httpInstance = new Client($config);
        }
        return self::$httpInstance;
    }

    /**
     * 设置http请求参数
     */
    protected function setHttpOptions(array $options): void
    {
        $this->options = array_merge($this->options, $options);

        // 如果 verify 选项改变，需要重新创建 Client
        if (isset($options['verify']) && self::$httpInstance !== null) {
            // 清除实例，下次请求时会重新创建
            self::$httpInstance = null;
        }
    }

    /**
     * 判断是否开启协程
     */
    protected function isCoroutine(): bool
    {
        return Coroutine::isCoroutine();
    }

    /**
     * 执行回调
     */
    protected function executeCallback(?callable $callback, ...$args): void
    {
        if ($callback !== null) {
            try {
                $callback(...$args);
            } catch (Throwable $t) {
                // 回调执行异常，可以选择记录日志
                error_log('KcWorkerHttp callback error: ' . $t->getMessage());
            }
        }
    }

    /**
     * get请求 - 真正的异步
     *
     * 非协程环境：使用workerman/http-client的回调
     * 协程环境：同步发起请求，然后执行回调
     *
     * @param string $uri 请求地址
     * @param array $options 请求选项 ['headers'=>[], 'verify'=>true/false/string, ...]
     * @param callable|null $success_callback 成功回调 function(Response $response)
     * @param callable|null $error_callback 失败回调 function(Throwable $exception)
     */
    public function get(string $uri, array $options = [], ?callable $success_callback = null, ?callable $error_callback = null): void
    {
        $this->setHttpOptions($options);

        if ($this->isCoroutine()) {
            // 协程环境：同步发起请求，然后执行回调
            try {
                $res = $this->http()->get($uri);
                $this->executeCallback($success_callback, $res);
            } catch (Throwable $t) {
                $this->executeCallback($error_callback, $t);
            }
        } else {
            // 非协程环境：真正的异步，使用回调
            $this->http()->get($uri, $success_callback, $error_callback);
        }
    }

    /**
     * post请求 - 真正的异步
     *
     * @param string $uri 请求地址
     * @param array $data 请求数据
     * @param array $options 请求选项 ['headers'=>[], 'verify'=>true/false/string, ...]
     * @param callable|null $success_callback 成功回调 function(Response $response)
     * @param callable|null $error_callback 失败回调 function(Throwable $exception)
     */
    public function post(string $uri, array $data = [], array $options = [], ?callable $success_callback = null, ?callable $error_callback = null): void
    {
        $this->setHttpOptions($options);

        // 处理请求数据格式
        if ($data) {
            $contentType = $this->options['headers']['Content-Type'] ?? '';
            if (str_contains($contentType, 'application/json')) {
                $postData = json_encode($data);
            } elseif (str_contains($contentType, 'application/x-www-form-urlencoded')) {
                $postData = http_build_query($data);
            } else {
                $postData = $data;
            }
        } else {
            $postData = [];
        }

        if ($this->isCoroutine()) {
            // 协程环境：同步发起请求，然后执行回调
            try {
                $res = $this->http()->post($uri, $postData);
                $this->executeCallback($success_callback, $res);
            } catch (Throwable $t) {
                $this->executeCallback($error_callback, $t);
            }
        } else {
            // 非协程环境：真正的异步，使用回调
            $this->http()->post($uri, $postData, $success_callback, $error_callback);
        }
    }

    /**
     * 自定义请求 - 真正的异步
     *
     * @param string $method HTTP方法
     * @param string $uri 请求地址
     * @param array $options 请求选项 ['headers'=>[], 'data'=>..., 'json'=>..., 'form_params'=>..., 'verify'=>...]
     * @param callable|null $success_callback 成功回调 function(Response $response)
     * @param callable|null $error_callback 失败回调 function(Throwable $exception)
     */
    public function request(string $method, string $uri, array $options = [], ?callable $success_callback = null, ?callable $error_callback = null): void
    {
        $this->setHttpOptions($options);

        // 构建request选项
        $requestOptions = [
            'method' => strtoupper($method),
        ];

        // 添加headers
        if (!empty($this->options['headers'])) {
            $requestOptions['headers'] = $this->options['headers'];
        }

        // 处理JSON数据
        if (isset($this->options['json'])) {
            $requestOptions['data'] = json_encode($this->options['json']);
            $requestOptions['headers']['Content-Type'] = 'application/json';
        }

        // 处理form_params
        if (isset($this->options['form_params'])) {
            $requestOptions['data'] = http_build_query($this->options['form_params']);
            $requestOptions['headers']['Content-Type'] = 'application/x-www-form-urlencoded';
        }

        // 处理普通data
        if (isset($this->options['data'])) {
            $requestOptions['data'] = $this->options['data'];
        }

        if ($this->isCoroutine()) {
            // 协程环境：同步发起请求，然后执行回调
            try {
                $res = $this->http()->request($uri, $requestOptions);
                $this->executeCallback($success_callback, $res);
            } catch (Throwable $t) {
                $this->executeCallback($error_callback, $t);
            }
        } else {
            // 非协程环境：真正的异步，使用回调
            $requestOptions['success'] = $success_callback;
            $requestOptions['error'] = $error_callback;
            $this->http()->request($uri, $requestOptions);
        }
    }

    /**
     * 设置 SSL 验证选项
     *
     * @param bool|string $verify
     *   - false: 禁用 SSL 验证
     *   - true: 使用系统默认 CA
     *   - string: 指定 CA 证书路径
     * @return self
     */
    public function setVerify(bool|string $verify): self
    {
        $this->setHttpOptions(['verify' => $verify]);
        return $this;
    }

    /**
     * 设置客户端证书（双向认证）
     *
     * @param string|array $cert 证书路径或 [路径, 密码]
     * @return self
     */
    public function setCert(string|array $cert): self
    {
        $this->setHttpOptions(['cert' => $cert]);
        return $this;
    }

    /**
     * 设置私钥
     *
     * @param string|array $sslKey 私钥路径或 [路径, 密码]
     * @return self
     */
    public function setSslKey(string|array $sslKey): self
    {
        $this->setHttpOptions(['ssl_key' => $sslKey]);
        return $this;
    }
}
