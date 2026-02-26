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

namespace kucoder\auth;

use kucoder\constants\KcConst;
use support\exception\BusinessException;
use Throwable;

class AdminAuth extends RBAC
{
    protected static array $config = ['app' => KcConst::ADMIN_APP,];
    protected const TOKEN = KcConst::ADMIN_TOKEN;
    protected const USERINFO = KcConst::ADMIN_USERINFO;
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
     * 后台鉴权
     * @throws BusinessException
     * @throws Throwable
     */
    public function checkRight(?string $plugin = null): void
    {
        $uid = $this->getId();
        $path = request()->path();
        $plugin = request()->plugin ?: $plugin;
        $app = request()->app;
        $admin_base_path = request()->route_base_path ?: "/app/{$plugin}/{$app}/";
        $menuPath = str_replace($admin_base_path, '', $path);
        if (!$this->checkAuth($uid, $menuPath, $plugin)) {
            $this->throw("访问{$path} 权限不足");
        }
    }
}