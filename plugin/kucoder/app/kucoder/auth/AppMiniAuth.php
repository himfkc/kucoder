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

use plugin\kucoder\app\kucoder\auth\RBAC;
use plugin\kucoder\app\kucoder\constants\KcConst;
use support\exception\BusinessException;
use Throwable;

/**
 * AppMiniAuth 移动APP及小程序鉴权
 */
class AppMiniAuth extends RBAC
{
    protected static array $config = ['app' => KcConst::APP_MINI_APP,];
    protected const TOKEN = KcConst::APP_MINI_TOKEN;
    protected const USERINFO = KcConst::APP_MINI_USERINFO;
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
     * App或者小程序接口鉴权
     * @throws BusinessException
     * @throws Throwable
     */
    public function checkRight(?string $plugin = null): void
    {

    }
}