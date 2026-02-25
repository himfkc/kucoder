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


namespace kucoder\controller;

use Exception;
use kucoder\service\OperLogService;
use kucoder\traits\ResponseTrait;
use support\Container;
use support\Request;
use support\Response;
use webman\App;

class Base
{
    use ResponseTrait;

    /**
     * @var Request
     */
    protected Request $request;

    /*
     * 身份验证方式 可在请求或子类重写
     */
    protected string $authType = '';

    /**
     * 操作日志
     */
    protected OperLogService $oLog;

    /**
     * @throws Exception
     */
    public function __construct()
    {
        $this->request = App::request();
        kc_dump('请求路径:' . $this->request->path() . '  ' . $this->request->uri());
        $header_auth_type = $this->request->header('x-auth-type', '');
        $this->request->setHeader('x-auth-type', $header_auth_type ?: $this->authType ?: 'cookie');
        $this->oLog = Container::instance('kucoder')->get('oLog');
    }

    protected function checkLoginAndRight(): void
    {
        if (!in_action($this->noNeedLogin)) {
            //登录验证
            $this->auth->checkLogin();
            //鉴权验证
            if (!in_action($this->noNeedRight)) {
                $this->auth->checkRight($this->pluginName);
            }
        }
        if ($this->modelClass) {
            if (!class_exists($this->modelClass)) {
                $this->throw("模型不存在:{$this->modelClass}");
            }
            $this->model = new $this->modelClass;
        }
    }

    public function __call(string $method, array $args): Response
    {
        //访问不存在的方法或受保护的方法时抛出错误
        return $this->error("要访问的方法:{$method} 不存在");
    }
}