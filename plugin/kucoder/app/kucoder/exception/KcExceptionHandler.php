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


namespace kucoder\exception;

use support\exception\PageNotFoundException;
use Throwable;
use Webman\Exception\BusinessException;
use Webman\Exception\ExceptionHandler;
use Webman\Http\Request;
use Webman\Http\Response;

class KcExceptionHandler extends ExceptionHandler
{
    public $dontReport = [
        BusinessException::class,
    ];

    /**
     * @inheritDoc
     */
    public function render(Request $request, Throwable $exception): Response
    {
        //404异常不走任何中间件 所以内置的跨域中间件无效 需要在这里单独处理跨域
        if ($exception instanceof PageNotFoundException) {
            kc_dump('404异常');
            if ($request->method() == 'OPTIONS') {
                kc_dump('404 预检');
                kc_dump('404 预检 expectsJson:', $request->expectsJson());
                kc_dump('404 预检  headers:', $request->header());
                $response = response('', 204);
                $response->withHeaders([
                    'Access-Control-Allow-Credentials' => 'true',
                    'Access-Control-Allow-Origin' => $request->header('origin', '*'),
                    'Access-Control-Allow-Methods' => $request->header('access-control-request-method', '*'),
                    'Access-Control-Allow-Headers' => $request->header('access-control-request-headers', '*'),
                ]);
                return $response;
            } else {
                kc_dump('404 非预检');
                kc_dump('404 非预检 expectsJson:', $request->expectsJson());
                kc_dump('404 非预检  headers:', $request->header());
                if ($request->expectsJson()) {
                    $response = new Response(200, ['Content-Type' => 'application/json'],
                        json_encode([
                            'code' => 404,
                            'msg' => '中间件异常：404 页面不存在',
                            'data' => []
                        ], JSON_UNESCAPED_UNICODE));
                    $response->withHeaders([
                        'Access-Control-Allow-Credentials' => 'true',
                        'Access-Control-Allow-Origin' => $request->header('origin', '*'),
                        'Access-Control-Allow-Methods' => $request->header('access-control-request-method', '*'),
                        'Access-Control-Allow-Headers' => $request->header('access-control-request-headers', '*'),
                    ]);
                    return $response;
                }
                return response($exception->getMessage(), 404);
            }
        }
        //非404异常 走内置的异常处理 然后再返回给中间件处理
        return parent::render($request, $exception);
    }
}