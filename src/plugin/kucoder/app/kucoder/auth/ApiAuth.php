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

namespace plugin\kucoder\app\kucoder\auth;

use plugin\kucoder\app\kucoder\constants\KcConst;
use plugin\kucoder\app\kucoder\interfaces\AuthInterface;
use support\exception\BusinessException;
use Throwable;

class ApiAuth extends RBAC
{
    protected static array $config = ['app' => KcConst::API_APP,];
    protected const TOKEN = KcConst::API_TOKEN;
    protected const USERINFO = KcConst::API_USERINFO;
    private static ?self $instance = null;

    private function __construct($config = [])
    {
        self::$config = array_merge(self::$config, $config);
        parent::__construct(self::$config);
    }

    private function __clone(): void
    {
        // 禁止克隆
    }

    public static function getInstance(array $config = []): self
    {
        if (static::$instance === null) {
            static::$instance = new static($config);
        }
        return static::$instance;
    }

    /**
     * 浏览器api接口鉴权
     * @throws BusinessException
     * @throws Throwable
     */
    public function checkRight(?string $plugin = null): void
    {

    }

}