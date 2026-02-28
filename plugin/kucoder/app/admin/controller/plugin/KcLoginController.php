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
use kucoder\controller\AdminBase;
use kucoder\lib\KcHelper;
use kucoder\lib\KcIdentity;
use kucoder\traits\HttpTrait;
use support\Response;
use support\think\Cache;
use Throwable;

class KcLoginController extends AdminBase
{
    use HttpTrait;

    protected array $noNeedRight = ['login','getCodeImg','logout'];

    public function __construct()
    {
        parent::__construct();
        $this->httpUrl = getenv('KUCODER_API') . '/app/member/api/';
    }

    /**
     * @throws Exception
     * @throws Throwable
     */
    public function login(): Response
    {
        $type = $this->request->post('type', 'login');
        $uri = $this->httpUrl . ($type === 'register' ? 'login/register' : 'login/login');
        $data = $this->request->post();
        $data['site_host'] = request()->host(true);
        $data['source'] = 'kucoder_admin';
        $res = $this->http_post($uri, $data, allResponse: true, needLogin: false);
        if ($res->hasHeader('Set-Cookie')) {
            $setCookie = $res->getHeader('Set-Cookie');
            $cookiePart = explode(';', $setCookie[0]);
            $expire = (int)explode('=', $cookiePart[1])[1];
            KcIdentity::set($this->auth->getId(),$this->app,$cookiePart[0],$expire-100);
        }
        $body = json_decode((string)$res->getBody(), true);
        if ($body['code'] !== 0) {
            $nickname = $this->auth->nickname;
            unset($body['data']['site_set']);
            Cache::set($this->app . ":kc_user_{$nickname}", $body['data']);
            //kc_public_key
            if(isset($body['data']['kc_public_key'])){
                $kc_box_public_key = $body['data']['kc_public_key']['kc_box_public_key'];
                $kc_sign_public_key = $body['data']['kc_public_key']['kc_sign_public_key'];
                if(get_env('kc_box_public_key') !== $kc_box_public_key){
                    if($kc_box_public_key && $kc_sign_public_key){
                        $checkBox = strlen($kc_box_public_key) === SODIUM_CRYPTO_BOX_PUBLICKEYBYTES;
                        $checkSign = strlen($kc_sign_public_key) === SODIUM_CRYPTO_SIGN_PUBLICKEYBYTES;
                        if ($checkBox && $checkSign){
                            kc_dump('更新kucoder_public_key');
                            KcHelper::setEnv($body['data']['kc_public_key']);
                        }
                    }
                }
            }
            return $this->ok('登录成功', $body);
        }
        return $this->error($body['msg']);
    }

    /**
     * @throws Exception
     */
    public function getCodeImg(): Response
    {
        $uri = $this->httpUrl . 'login/changeCaptcha';
        $res = $this->http_get(uri:$uri, needLogin: false);
        return $this->ok('获取验证码成功', $res['data']);
    }

    /**
     * @throws Exception
     * @throws Throwable
     */
    public function logout(): Response
    {
        kc_dump('准备退出插件市场');
        $uri = $this->httpUrl . 'login/logout';
        $post = $this->request->post();
        $post['cookie'] = KcIdentity::getCookie($uri, $this->auth->getId(), $this->app);
        $res = $this->http_post($uri, $post);
        kc_dump('logout res', $res);
        if($res['code'] !== 0){
            KcIdentity::clear($this->auth->getId(),$this->app);
            Cache::delete($this->app . ":kc_user_{$this->auth->nickname}");
            return $this->ok('登出成功', $res['data']);
        }
        return $this->error($res['msg']);
    }
}