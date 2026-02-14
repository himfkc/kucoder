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



namespace plugin\kucoder\app\kucoder\constants;


class KcError
{
    const TokenEmpty  = [400, '你还未登录'];
    const TokenError  = [401, 'token异常'];
    const TokenIpEmpty = [1000, 'token未传递ip'];
    const UnMatchedIp = [1001, 'token传递的ip与登录时ip不匹配'];
    const TokenExpired = [1002, 'token已过期,请重新登录'];
    const UAEmpty = [1003, 'UA异常'];
    const UnMatchedUA = [1004, 'UA不匹配'];
    const SessionEmptyId = [1005, '请求cookie不存在'];
    const SessionEmptyToken = [1006, 'session不存在token'];
    const SessionEmptyUserInfo = [1007, 'session不存在用户信息'];
    const SessionEmpty = [1008, 'session不存在'];



}