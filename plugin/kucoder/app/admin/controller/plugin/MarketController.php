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


namespace plugin\kucoder\app\admin\controller\plugin;

use Exception;
use plugin\kucoder\app\admin\model\PluginLocal;
use kucoder\auth\AdminAuth;
use kucoder\lib\KcIdentity;
use kucoder\traits\HttpTrait;
use kucoder\traits\ResponseTrait;
use support\Response;
use Throwable;

class MarketController
{
    use ResponseTrait, HttpTrait;

    /**
     * @throws Exception|Throwable
     */
    public function __construct()
    {
        $this->httpUrl = config('plugin.kucoder.app.sys_url') . '/kapi/';
    }

    /**
     * @throws Exception
     */
    public function index(): Response
    {
        $uri = $this->httpUrl . 'market/index';
        $res = $this->http_get(uri:$uri, needLogin: false);
        return $this->ok('获取插件列表成功', $res['data']);
    }

    /**
     * @throws Exception
     */
    public function buy(): Response
    {
        $data = request()->post();
        $data['site_host'] = request()->host(true);
        kc_dump('购买插件数据: ', $data);
        $uri = $this->httpUrl . ($data['pay_type'] === 'cardkey' ? 'market/buyByCardKey' : 'market/buy');
        $data['cookie'] = KcIdentity::getCookie($uri, AdminAuth::getInstance()->getId(), 'admin');
        $res = $this->http_post($uri, $data);
        kc_dump('购买插件结果: ', $res);
        return $this->ok('购买结果', $res);
    }

    /**
     * @throws Exception|Throwable
     */
    public function payQuery(): Response
    {
        /*
         * param参数
         [
          "pay_code_url" => "weixin://wxpay/bizpayurl?pr=lIK3Pr0z3"
          "out_trade_no" => "2025101110235068e9bfb6423f96521"
          "order_id" => 30
          "token" => "api_token"
        ]
         */
        $param = request()->post();
        kc_dump('查询param: ', $param);
        $uri = $this->httpUrl . 'market/payQuery';
        $param['cookie'] = KcIdentity::getCookie($uri, AdminAuth::getInstance()->getId(), 'admin');
        $res = $this->http_post($uri, $param, true);
        $res = json_decode((string)$res->getBody(), true);
        kc_dump('查询支付结果: ', $res);
        if (isset($res['data']['pay_status']) && $res['data']['pay_status'] === 1) {
            //写入插件表
            try {
                $pluginLocal = new PluginLocal();
                $pluginLocal->save($res['data'], true);
            } catch (Throwable $t) {
                return $this->error('支付已成功 但保存订单失败：' . devMsg($t->getMessage()));
            }
            return $this->ok('支付成功 请到插件列表中查看安装', $res);
        }
        return $this->ok('支付结果', $res);
    }


}