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


namespace plugin\kucoder\app\admin\controller;

use kucoder\controller\AdminBase;
use kucoder\lib\Captcha;
use kucoder\lib\KcHelper;
use kucoder\lib\KcIdentity;
use kucoder\service\LoginLogService;
use kucoder\service\MenuService;
use Psr\SimpleCache\InvalidArgumentException;
use support\exception\BusinessException;
use support\Request;
use support\Response;
use support\think\Cache;
use think\db\exception\DataNotFoundException;
use think\db\exception\DbException;
use think\db\exception\ModelNotFoundException;
use Throwable;

class LoginController extends AdminBase
{
    protected array $noNeedLogin = ['init', 'login', 'changeCaptcha', 'logout'];
    protected array $noNeedRight = ['getRouters', 'logout'];

    private static array $showFields = ['token', 'nickname', 'avatar', 'site_set'];

    /**
     * @return Response
     */
    public function changeCaptcha(): Response
    {
        try {
            return $this->ok('', ['captcha' => Captcha::create()]);
        } catch (InvalidArgumentException|\ReflectionException $e) {
            return $this->error($e->getMessage());
        }
    }

    /**
     * @throws Throwable
     * @throws DataNotFoundException
     * @throws BusinessException
     * @throws ModelNotFoundException
     * @throws DbException
     * @throws InvalidArgumentException
     */
    public function login(Request $request): Response
    {
        // kc_dump('login 请求方式' , $request->isPost());
        //判断是否是post请求
        if (!$request->isPost()) {
            return $this->error('请求方式错误');
        }
        //验证器
        $this->validate();
        //验证码
        $uuid = $request->post('uuid');
        $code = $request->post('code');
        if (!Captcha::check($uuid, $code)) {
            return $this->error('验证码错误');
        }
        //登录
        if (!$userInfo = $this->auth->login([$this, 'myLogin'])) {
            return $this->error('登录失败');
        }
        $cd = $userInfo;
        $userInfo = reserve_array_key($userInfo, self::$showFields);
        $userInfo['site_set'] = [
            'logo' => config_in_db('kucoder',null,'site_logo'),
            'site_name' => config_in_db('kucoder',null,'site_name') ?: 'Kucoder系统后台',
            // 'upload_path' => config('plugin.kucoder.app.upload_path'),  //上传接口默认/kucoder/upload
            'kucoder_api' => getenv('KUCODER_API'),
            'oss_domain' => KcHelper::getOssDomain(),
        ];
        //是否已登录kucoder
        $openssl_secret_key = get_env('openssl_secret_key') ?: '';
        $opensslKey = base64_decode($openssl_secret_key);
        if ($opensslKey && strlen($opensslKey) === 32) {
            if (KcIdentity::has($cd['user_id'], $this->app)) {
                $kcUser = Cache::get($this->app . ":kc_user_{$cd['nickname']}");
                $userInfo['kc_user'] = $kcUser;
            }
        }
        return $this->ok('登录成功', $userInfo);
    }

    /**
     * 自定义的protected/private登录方法(不能是public方法 否则暴露账户信息) 可替换默认的登录方法
     * @param string $modelClass
     * @return mixed
     * @throws Throwable
     */
    protected function myLogin(string $modelClass): object
    {
        kc_dump('执行自定义登录方法');
        //默认的用户名密码登录
        $username = request()->post('username');
        $password = request()->post('password');
        $user = (new $modelClass)->where('username', $username)->find();
        if (!$user) $this->throw('用户不存在');
        if (!password_verify($password, $user->password)){
            LoginLogService::add(0, 0,'用户名或密码错误');
            $this->throw('用户名或密码错误');
        }
        if (intval($user->status) !== 1){
            LoginLogService::add(0, 0,'用户已被禁用',$user->user_id);
            $this->throw('用户已被禁用');
        }
        $user->post_password = $password;
        return $user;
    }

    /**
     * @throws InvalidArgumentException|Throwable
     */
    public function logout(): Response
    {
        $this->auth->logout();
        return $this->ok('退出成功');
    }

    /**
     * @throws Throwable
     */
    public function getRouters(): Response
    {
        kc_dump('是否为超级管理员：' , $this->auth->getUserInfo()['is_super_admin']);
        $userId = $this->auth->getId();
        $menus = $this->auth->getUserMenus($userId);
        $btns = $this->auth->getUserBtns($userId);
        kc_dump('获取菜单:' , $menus);
        $roleMenus = array_map(function ($item) {
            return [
                'id' => $item['id'],
                'pid' => $item['pid'],
                'title' => $item['title'],
            ];
        }, $menus);
        $roleMenus = get_recursion_data($roleMenus);
        if($this->auth->getUserInfo()['is_super_admin']){
            kc_dump('获取所有菜单routes');
            $routes = MenuService::allMenusToRoutes($menus);
        }else{
            $routes = MenuService::menusToRoutes($menus);
        }
        return $this->ok('', [
            'routes' => $routes,
            'btns' => $btns,
            'roleMenus' => $roleMenus,
        ]);
    }

}