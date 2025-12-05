<?php
declare(strict_types=1);

namespace plugin\kucoder\app\admin\controller;

use plugin\kucoder\app\kucoder\controller\AdminBase;
use plugin\kucoder\app\kucoder\lib\Captcha;
use plugin\kucoder\app\kucoder\lib\KcIdentity;
use plugin\kucoder\app\kucoder\service\MenuService;
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

    private static array $showFields = ['token','nickname','avatar','site_set'];

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
        // dump('login 请求方式' , $request->isPost());
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
        $userInfo = reserve_array_key($userInfo, self::$showFields);
        $userInfo['site_set'] = [
            'logo' => '/favicon.ico',
            'site_name' => 'Kucoder快速开发框架',
            // 'upload_path' => config('plugin.kucoder.app.upload_path'),
            'sys_url' => config('plugin.kucoder.app.sys_url'),
            'sys_file_url' => config('plugin.kucoder.app.sys_file_url') ?: config('plugin.kucoder.app.sys_url'),
        ];
        //是否已登录kucoder
        $openssl_secret_key = config('plugin.kucoder.app.openssl_secret_key') ?: '';
        $opensslKey = base64_decode($openssl_secret_key);
        if ($opensslKey && strlen($opensslKey) === 32){
            if(KcIdentity::has($this->auth->getId(),$this->app)) {
                $kcUser = Cache::get($this->app . ":kc_user_{$this->auth->nickname}");
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
    protected function myLogin(string $modelClass):object
    {
        //默认的用户名密码登录
        $username = request()->post('username');
        $password = request()->post('password');
        $user = (new $modelClass)->where('username', $username)->find();
        if (!$user) $this->throw('用户不存在');
        if (!password_verify($password, $user->password)) $this->throw('用户名或密码错误');
        if (intval($user->status) !== 1) $this->throw('用户已被禁用');
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
        $userId = $this->auth->getId();
        $menus = $this->auth->getUserMenus($userId);
        $btns = $this->auth->getUserBtns($userId);

        $roleMenus = array_map(function ($item) {
            return [
                'id' => $item['id'],
                'pid' => $item['pid'],
                'title' => $item['title'],
            ];
        }, $menus);
        $roleMenus = get_recursion_data($roleMenus);

        return $this->ok('', [
            'routes' => MenuService::menusToRoutes($menus),
            'btns' => $btns,
            'roleMenus' => $roleMenus,
        ]);
    }

}