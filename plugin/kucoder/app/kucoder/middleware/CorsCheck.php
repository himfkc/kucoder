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

namespace plugin\kucoder\app\kucoder\middleware;

use Exception;
use ReflectionException;
use Webman\Http\Response;
use Webman\Http\Request;
use Webman\MiddlewareInterface;

/**
 * 跨域请求检查中间件
 * Class CorsCheck
 * @package app\kucoder\middleware
 */
class CorsCheck implements MiddlewareInterface
{
    //允许的跨域domain
    private static array $allowCorsDomain = [];

    /**
     * 404请求默认不会触发任何中间件 所以404不会执行这里的process流程
     * @throws ReflectionException
     * @throws Exception
     */
    public function process(Request $request, callable $handler): Response
    {
        // kc_dump('插件kucoder的中间件执行了');
        $origin = $request->header('origin');
        // kc_dump('Cors origin: ' . $origin);
        if (empty($origin)) {
            //没有origin头，可能为同源请求或curl、Postman、guzzleHttp或者各大厂的支付回调
            $response = $handler($request);
            // kc_dump('无origin 中间件后置执行');
            return $this->handleResponse($response);
        }
        $host = parse_url($origin, PHP_URL_HOST);
        // kc_dump('Cors host: ' . $host);
        if (empty(self::$allowCorsDomain)) {
            self::$allowCorsDomain = explode(',', config('plugin.kucoder.app.allow_cors_domain'));
        }
        if (in_array($host, self::$allowCorsDomain) || in_array('*', self::$allowCorsDomain)) {
            if ($request->method() === 'OPTIONS') {
                // kc_dump('允许跨域 预检请求');
                $response = response('', 204);
            } else {
                // kc_dump('允许跨域 非预检请求');
                // kc_dump('Cors request header:', $request->header());
                // 继续执行请求
                // kc_dump('中间件前置执行');
                $response = $handler($request);
                // kc_dump('中间件后置执行');
                $response = $this->handleResponse($response);
            }
            //允许跨域请求
            $response->withHeaders($this->corsHeaders($request));
        } else {
            kc_dump('属于跨域请求');
            if(!is_dev_env()){
                if($request->expectsJson()){
                    return json(['msg' => '跨域错误', 'code' => 0]);
                }
                return response('生产环境跨域错误', 403);
            }
            if(config('plugin.kucoder.app.allow_cors_dev')){
                kc_dump('调试环境下允许所有跨域请求');
                //调试环境下允许所有跨域请求
                $response = $handler($request);
                $response = $this->handleResponse($response);
                $response->withHeaders($this->corsHeaders($request));
            }else{
                $response = response('调试环境跨域错误', 403);
            }
        }
        return $response;
    }

    private function handleResponse(Response $response): Response
    {
        $exception = $response->exception();
        if ($exception) {
            $data = is_dev_env() && is_app_debug() ? [
                'msg' => $exception->getMessage(),
                'code' => $exception->getCode(),
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
                'trace' => $exception->getTraceAsString(),
                'data' => []
            ] : [
                'msg' => $exception->getMessage(),
                'code' => $exception->getCode(),
                'data' => []
            ];
            kc_dump('cors异常信息:', $data);
            $response->withBody(json_encode($data, JSON_UNESCAPED_UNICODE));
        }
        return $response;
    }

    private function corsHeaders(Request $request): array
    {
        return [
            'Access-Control-Allow-Credentials' => 'true',
            'Access-Control-Allow-Origin' => $request->header('origin', ''),
            'Access-Control-Allow-Methods' => $request->header('access-control-request-method', '*'),
            'Access-Control-Allow-Headers' => $request->header('access-control-request-headers', '*'),
        ];
    }
}