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


namespace kucoder\traits;

use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Cookie\CookieJarInterface;
use GuzzleHttp\Psr7;
use GuzzleHttp\Psr7\MultipartStream;
use kucoder\constants\KcError;
use Throwable;

trait HttpTrait
{
    use ResponseTrait;

    protected Client $http;
    protected string $httpUrl = '';
    protected static ?Client $httpInstance = null;
    private array $options = [
        'headers' => [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json, text/plain, */*',
        ],
        'verify' => true,  //验证https证书
    ];
    private array $downOptions = [
        //保存的文件地址 必填
        'save_file_path' => '',
        //debug模式
        'debug' => false,
        //sink直接保存文件 不需要手动处理文件  stream流式下载 需手动处理流
        'down_type' => 'sink',
        //保存的文件地址字符串file 或 资源resource
        'sink_type' => 'file',
        //下载请求方式 默认post
        'method' => 'post',
    ];
    protected array $errorPrefix = [
        'msg' => 'kucoder:',
        'code' => 888
    ];
    protected string $noLoginMsg = 'http: 你还未登录kucoder，请重新登录';

    protected function setHttpOptions(array $options): void
    {
        $this->options = array_merge($this->options, $options);
    }

    /**
     * @throws Exception
     */
    protected function http_get(string $uri, array $data = [], bool $allResponse = false, bool $needLogin = true, bool $cookie = true)
    {
        if ($needLogin) {
            $cookie ? $this->cookieHandle($data) : $this->tokenHandle($data);
        }
        $this->httpsVerify();
        // kc_dump('get this->options:', $this->options);
        try {
            kc_dump('准备发送get请求: ',$uri, $data);
            $res = $this->http()->get($uri, $this->options);
            kc_dump('http_get res:', $res);
        } catch (Throwable $t) {
            throw new Exception(
                $this->errorPrefix['msg'] . 'get_' . devMsg($t->getMessage()),
                $this->errorPrefix['code'] . $t->getCode()
            );
        }
        return $this->handleResponse($res, $allResponse);
    }

    /**
     * @throws Exception
     */
    protected function http_post(string $uri, array $data = [], bool $allResponse = false, bool $needLogin = true, bool $cookie = true)
    {
        if ($needLogin) {
            $cookie ? $this->cookieHandle($data) : $this->tokenHandle($data);
        }
        $this->httpsVerify();
        $data['site_host'] = request()->host(true);
        // $data['site_host'] = 'x.y.taobao.com.cn';
        if ($data) {
            $this->options['json'] = $data;
        }
        kc_dump('post this->options:', $this->options);
        try {
            $res = $this->http()->post($uri, $this->options);
        } catch (Throwable $t) {
            throw new Exception(
                $this->errorPrefix['msg'] . 'post_' . devMsg($t->getMessage()),
                $this->errorPrefix['code'] . $t->getCode()
            );
        }
        return $this->handleResponse($res, $allResponse);
    }

    /**
     * @throws Exception
     */
    protected function http_upload(string $uri, array $data = [], array $uploadFields = [], bool $allResponse = false, bool $needLogin = true, bool $cookie = true)
    {
        if ($needLogin) {
            if ($cookie) {
                $this->cookieHandle($data);
            } else if (isset($data['token']) || isset($data['token'])) {
                $token = $data['token'] ?? $data['token'];
                $this->options['headers']['Authorization'] = 'Bearer ' . $token;
            }
        }
        $this->httpsVerify();

        $multipart = [];
        $data['site_host'] = request()->host(true);
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $value = json_encode($value, JSON_UNESCAPED_UNICODE);
            }
            if (in_array($key, $uploadFields)) {
                $value = Psr7\Utils::tryFopen(public_path() . $value, 'rb');
            }
            $multipart[] = [
                'name' => $key,
                'contents' => $value
            ];
        }
        $boundary = '--MyCustomBoundary12345';
        $multipartStream = new MultipartStream($multipart, $boundary);
        $this->options['headers']['Content-Type'] = 'multipart/form-data;boundary=' . $boundary;
        $this->options['body'] = $multipartStream;
        // kc_dump('upload this->options:', $this->options);
        try {
            $res = $this->http()->post($uri, $this->options);
        } catch (Throwable $t) {
            throw new Exception(
                $this->errorPrefix['msg'] . 'upload_' . devMsg($t->getMessage()),
                $this->errorPrefix['code'] . $t->getCode()
            );
        }
        return $this->handleResponse($res, $allResponse);
    }

    /**
     * @param string $uri
     * @param array $data
     * @param array $downOptions
     * @param bool $needLogin
     * @param bool $cookie
     * @return bool
     * @throws Exception
     */
    protected function http_download(string $uri, array $data = [], array $downOptions = [], bool $needLogin = true, bool $cookie = true): bool
    {
        if ($needLogin) {
            $cookie ? $this->cookieHandle($data) : $this->tokenHandle($data);
        }
        $this->httpsVerify();
        $data['site_host'] = request()->host(true);
        if ($data) {
            $this->options['json'] = $data;
        }
        $options = array_merge($this->downOptions, $downOptions);
        $this->options['debug'] = $options['debug'];
        if (!$options['save_file_path']) {
            $this->throw('文件保存地址不能为空');
        }
        try {
            //fp文件资源resource
            $fp = '';
            //sink 直接指定响应体的输出目标（文件路径、资源句柄或 StreamInterface 对象），Guzzle 会自动将响应内容写入该目标，无需手动处理流
            if ($options['down_type'] === 'sink') {
                $this->options['stream'] = false;  //sink和stream不能同时使用
                if ($options['sink_type'] === 'file') {
                    // 直接指定文件路径
                    $this->options['sink'] = $options['save_file_path'];
                } else if ($options['sink_type'] === 'resource') {
                    $fp = fopen($options['save_file_path'], 'w+');
                    $this->options['sink'] = $fp;
                }
            }
            //stream 将响应体以流（StreamInterface）的形式返回，不自动保存到文件，需要手动通过 $response->getBody() 获取流并处理（如分块写入文件）
            if ($options['down_type'] === 'stream') {
                $this->options['stream'] = true;
                $fp = fopen($options['save_file_path'], 'w+');
                if (!$fp) {
                    $this->throw("无法创建stream文件: " . $options['save_file_path']);
                }
            }
            //下载请求
            $res = $this->http()->request($options['method'], $uri, $this->options);
            // kc_dump('download response: ', $res);
            if ($res->getStatusCode() !== 200) {
                $this->throw($this->errorPrefix['msg'] . '200状态码错误_' . $res->getReasonPhrase());
            }
            $resHeaders = $res->getHeaders();
            //手动处理stream
            if (isset($resHeaders['Content-Disposition'])) {
                //stream需手动获取流并写入文件 适合需要自定义流式处理逻辑（如进度监控、数据过滤、断点续传等）
                if ($options['down_type'] === 'stream') {
                    $stream = $res->getBody();
                    // kc_dump('stream: ', $stream);
                    // 确保流可读
                    if (!$stream->isReadable()) {
                        $this->throw("响应体不可读");
                    }
                    while (!$stream->eof()) {
                        fwrite($fp, $stream->read(10240)); //读取10KB数据并写入到文件
                    }
                }
            }
            //响应为json
            if (str_contains($resHeaders['Content-Type'][0], 'application/json')) {
                $body = json_decode((string)$res->getBody(), true);
                //sink类型 body=null 处理异常
                if ($body && $body['code'] !== 1) {
                    $this->throw(
                        $this->errorPrefix['msg'] . 'download_' . devMsg($body['msg']),
                        $this->errorPrefix['code'] . $body['code']
                    );
                }
            }
            //关闭资源
            if (is_resource($fp)) fclose($fp);
        } catch (Throwable $t) {
            // 清理可能损坏的文件
            if (file_exists($options['save_file_path'])) {
                unlink($options['save_file_path']);
            }
            //关闭资源
            if ($fp && is_resource($fp)) fclose($fp);
            throw new Exception($t->getMessage(), $t->getCode());
            // throw new Exception($this->errorInfo($t));
        }

        return true;
    }

    /**
     * @throws Exception
     */
    private function cookieHandle(array &$data): void
    {
        if (!isset($data['cookie']) || !$data['cookie'] instanceof CookieJarInterface) {
            $this->throw($this->noLoginMsg, $this->errorPrefix['code'] . KcError::TokenEmpty[0]);
        }
        $this->options['cookies'] = $data['cookie'];
        unset($data['cookie']);
    }

    private function tokenHandle(array &$data): void
    {
        if (isset($data['token']) && !empty($data['token'])) {
            $token = $data['token'];
            unset($data['token']);
            $this->options['headers']['Authorization'] = 'Bearer ' . $token;
        } else {
            $this->throw($this->noLoginMsg, $this->errorPrefix['code'] . KcError::TokenEmpty[0]);
        }
    }

    /**
     * @throws Exception
     */
    protected function handleResponse($res, bool $allResponse = false)
    {
        if ($res->getStatusCode() !== 200) {
            throw new Exception($this->errorPrefix['msg'] . '200状态码错误_' . $res->getReasonPhrase());
        }
        $body = json_decode((string)$res->getBody(), true);
        // kc_dump('http body:', $body);
        if ($body && is_array($body) && $body['code'] !== 1) {
            throw new Exception(
                $this->errorPrefix['msg'] . $body['msg'],
                $this->errorPrefix['code'] . $body['code']
            );
        }
        if ($allResponse) return $res;
        return $body;
    }

    private function httpsVerify(): void
    {
        //验证服务器的https证书
        $this->options['verify'] = $this->options['verify'] ? base_path('plugin/kucoder/app/kucoder/lib/http/cacert.pem') : false;
    }

    protected function http(): ?Client
    {
        if (!self::$httpInstance) {
            self::$httpInstance = new Client([
                'timeout' => 10,              // 总超时时间 10 秒
                'connect_timeout' => 5,        // 连接超时时间 5 秒
                'http_errors' => true,         // 启用 HTTP 错误
            ]);
        }
        return $this->http = self::$httpInstance;
    }


}