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


namespace plugin\kucoder\app\kucoder\service;

use plugin\kucoder\app\kucoder\auth\AdminAuth;
use plugin\kucoder\app\kucoder\auth\ApiAuth;
use plugin\kucoder\app\kucoder\auth\AppMiniAuth;
use plugin\kucoder\app\kucoder\model\LoginLog;

/**
 * 系统操作日志服务类
 * @package plugin\kucoder\app\kucoder\service
 * param $app_type 应用类型 0=后台admin应用,1=客户端api应用,2=客户端小程序app应用
 * param $status 操作结果 1=成功,0=失败
 * param $msg 操作结果信息
 * param $title 操作标题
 */
class LoginLogService
{
    public static function add(int $app_type = 0, int $status = 1, string $msg = '', ?int $uid = null): void
    {
        $request = request();
        $log = [
            //登录的插件
            'plugin' => $request->plugin,
            //登录的插件内应用
            'app' => $request->app,
            //应用类型 0=后台admin应用,1=客户端api应用,2=客户端小程序app应用
            'app_type' => $app_type,
            //登录操作ip
            'ip' => $request->getRealIp(),
            //登录操作结果 1=成功,0=失败
            'status' => $status,
            //登录操作结果信息
            'msg' => $msg,
        ];
        $log['uid'] = $uid ?: match ($app_type) {
            0 => AdminAuth::getInstance()->getId(),
            1 => ApiAuth::getInstance()->getId(),
            2 => AppMiniAuth::getInstance()->getId(),
            default => 0,
        };
        if(empty($log['uid'])){
            $log['uid'] = 0;
        }
        LoginLog::create($log);
    }
}