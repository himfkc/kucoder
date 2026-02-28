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

use Exception;
use plugin\kucoder\app\admin\model\User;
use kucoder\constants\KcConst;
use kucoder\constants\KcError;
use kucoder\lib\KcJWT;
use kucoder\lib\sodium\KcBox;
use kucoder\service\LoginLogService;
use kucoder\traits\HttpTrait;
use kucoder\traits\ResponseTrait;
use Psr\SimpleCache\InvalidArgumentException;
use ReflectionException;
use support\exception\BusinessException;
use support\think\Cache;
use support\think\Db;
use think\db\exception\DataNotFoundException;
use think\db\exception\DbException;
use think\db\exception\ModelNotFoundException;
use Throwable;
use Workerman\Protocols\Http\Session;

/**
 * RBAC权限控制类
 */
class RBAC
{
    use ResponseTrait, HttpTrait;

    /**
     * @var string 应用标识
     */
    protected string $app = 'admin';

    /**
     * @var int|float 缓存时间 默认1天
     */
    protected int|float $keeptime = 60 * 60 * 24;

    /**
     * 用户表 用户字段=>数据表实际的用户字段
     */
    protected array $userField = [
        'id' => 'user_id',
        'username' => 'username',
        'nickname' => 'nickname',
        'avatar' => 'avatar',
        'mobile' => 'mobile',
        'email' => 'email',
        'status' => 'status',
        'last_login_time' => 'last_login_time',
        'role_ids' => 'role_ids', // 用户角色id
    ];

    /**
     * 角色表字段
     * @var array|string[]
     */
    protected array $roleField = [
        //角色表主键
        'id' => 'role_id',
        //角色表菜单
        'menu' => 'rules',
    ];

    /**
     * 用户角色表字段
     * @var array|string[]
     */
    protected array $userRoleField = [
        //用户主键
        'user_id' => 'user_id',
        //角色菜单
        'role_id' => 'role_id',
    ];

    /**
     * 菜单表字段
     * @var array|string[]
     */
    protected array $menuField = [
        //角色表主键
        'id' => 'id',
        //菜单名字段
        'name' => 'title',
        //父级id字段
        'pid' => 'pid',
        //菜单路径
        'path' => 'path',
    ];

    /**
     * 数据表名
     */
    protected array $table = [
        //用户表
        'user' => 'ku_user',
        //角色表
        'role' => 'ku_role',
        //菜单表
        'menu' => 'ku_menu',
    ];

    /**
     * 菜单条件
     */
    protected array $where = [
        'delete_time' => null
    ];

    /**
     * RBAC constructor.
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->app = $config['app'] ?: KcConst::ADMIN_APP;
        $this->userField = array_merge($this->userField, $config['userField'] ?? []);
        $this->roleField = array_merge($this->roleField, $config['roleField'] ?? []);
        $this->menuField = array_merge($this->menuField, $config['menuField'] ?? []);
        $this->userRoleField = array_merge($this->userRoleField, $config['userRoleField'] ?? []);
        $this->table = array_merge($this->table, $config['table'] ?? []);
    }

    /**
     * 根据角色id获取角色信息
     * @param int $roleId
     * @param string $field
     * @return array
     * @throws Exception
     */
    protected function getMenuIdsArrByRoleId(int $roleId, string $field = '*'): array
    {
        try {
            $key = $this->app . ':role:' . $roleId;
            return Cache::tag($this->app)
                ->remember($key, function () use ($roleId) {
                    $menuIds = Db::name($this->table['role'])
                        ->where($this->roleField['id'], $roleId)
                        ->value($this->roleField['menu']);
                    return explode(',', $menuIds);
                }, (int)$this->keeptime);
        } catch (Throwable $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * 获取用户信息
     * @param int $userId
     * @param bool $isCache
     * @return array
     * @throws Exception
     */
    protected function userInfo(int $userId, bool $isCache = true): mixed
    {
        try {
            $key = "{$this->app}:userInfo:{$userId}";
            if (!$isCache) {
                delete_cache($key);
            }
            return Cache::remember($key, function () use ($userId) {
                $baseQuery = Db::name($this->table['user'])
                    ->where($this->userField['id'], $userId)
                    ->field(array_values($this->userField));
                if ($this->app === 'admin') {
                    $baseQuery->filter(function ($user) {
                        $user['is_super_admin'] = false;
                        $user['roleIdsArr'] = explode(',', trim($user[$this->userField['role_ids']], ','));
                        foreach ($user['roleIdsArr'] as $roleId) {
                            $menuIdsArr = $this->getMenuIdsArrByRoleId((int)$roleId);
                            if (in_array('*', $menuIdsArr)) {
                                $user['is_super_admin'] = true;
                                break;
                            }
                        }
                        return $user;
                    });
                }
                return $baseQuery->find();
            }, (int)$this->keeptime);
        } catch (Throwable $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * 检查权限
     * @param int $userId
     * @param string $path
     * @param string $plugin
     * @return bool
     * @throws Exception
     */
    protected function checkAuth(int $userId, string $path, string $plugin): bool
    {
        try {
            $userInfo = $this->userInfo($userId);
            if ($userInfo['is_super_admin']) return true;

            $allMenus = $this->getAllMenus();
            $pluginMenus = array_filter($allMenus, fn($menu) => $menu['plugin'] === $plugin);
            $menuPaths = array_column($pluginMenus, $this->menuField['path'], $this->menuField['id']);
            $pathMenuId = array_search($path, $menuPaths);
            foreach ($userInfo['roleIdsArr'] as $roleId) {
                $menuIdsArr = $this->getMenuIdsArrByRoleId((int)$roleId);
                if (in_array($pathMenuId, $menuIdsArr)) {
                    return true;
                }
            }
            return false;
        } catch (Throwable $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * 获取所有菜单
     * @param array $where
     * @return array
     * @throws ReflectionException
     */
    public function getAllMenus(array $where = []): array
    {
        // kc_dump("getAllMenus where", $where);
        if (!isset($where['recycle'])) {
            //全部菜单
            $where = array_merge($this->where, $where);
            $keySymbol = 'all';
        } else {
            //软删除菜单
            unset($where['recycle']);
            $where = 'delete_time is not null';
            $keySymbol = 'delete';
        }
        $key = "{$this->app}:menu:{$keySymbol}";
        return Cache::tag($this->app)
            ->remember($key, function () use ($where) {
                return Db::name($this->table['menu'])
                    ->where($where)
                    ->order('sort asc,id asc')
                    ->select()
                    ->toArray();
            }, (int)$this->keeptime);
    }

    /**
     * 获取所有递归菜单
     * @return array
     * @throws ReflectionException
     */
    public function getRecursionAllMenus(): array
    {
        return Cache::tag($this->app)
            ->remember("{$this->app}:recursionMenu", function () {
                return get_recursion_data($this->getAllMenus(), $this->menuField['id'], $this->menuField['pid']);
            }, (int)$this->keeptime);
    }

    /**
     * 获取用户菜单
     * @param int $userId
     * @param array $where
     * @return array
     * @throws Exception
     */
    public function getUserMenus(int $userId, array $where = []): array
    {
        try {
            $userInfo = $this->userInfo($userId);
            if ($userInfo['is_super_admin']) {
                return $this->getAllMenus($where);
            }
            $userMenuIds = [];
            foreach ($userInfo['roleIdsArr'] as $roleId) {
                $menuIdsArr = $this->getMenuIdsArrByRoleId((int)$roleId);
                $userMenuIds = array_merge($userMenuIds, $menuIdsArr);
            }
            $userMenu = array_flip(array_unique($userMenuIds));
            $allMenu = array_column($this->getAllMenus($where), null, $this->menuField['id']);
            return array_intersect_key($allMenu, $userMenu);
        } catch (Throwable $e) {
            throw new Exception((string)$e);
        }
    }

    /**
     * 获取用户递归菜单
     * @param int $userId
     * @param array $where
     * @param bool $recursion
     * @return array
     * @throws Exception
     */
    public function getRecursionUserMenus(int $userId, array $where = [], bool $recursion = true): array
    {
        if ($recursion) {
            return get_recursion_data($this->getUserMenus($userId, $where), $this->menuField['id'], $this->menuField['pid']);
        } else {
            return $this->getUserMenus($userId, $where);
        }
    }

    /**
     * @throws Exception
     */
    public function getUserBtns(int $userId): array
    {
        $userMenus = $this->getUserMenus($userId);
        $userBtns = array_filter($userMenus, function ($menu) {
            return $menu['type'] === 'button';
        });
        array_walk($userBtns, fn(&$btn) => $btn['path'] = $btn['plugin'] . '/' . $btn['path']);
        return array_column($userBtns, 'path');
    }

    /**
     * @throws Exception
     */
    public function siteRight(&$siteRight = []): void
    {
        kc_dump('host: ' . request()->host(true));
        kc_dump('getLocalIp: ' . request()->getLocalIp());
        kc_dump('getRemoteIp: ' . request()->getRemoteIp());
        kc_dump('getRealIp: ' . request()->getRealIp());
        $url = getenv('KUCODER_API') . '/kapi/site/right';
        $result = $this->http_post($url, [
            'site_host' => request()->host(true),
            'box_public_key' => get_env('sodium_box_public_key'),
        ], needLogin: false);
        if (isset($result['data']['res']) && $res = $result['data']['res']) {
            kc_dump('site right result:', $res);
            $my_box_private_key = base64_decode(get_env('sodium_box_private_key'));
            $kc_box_public_key = base64_decode(get_env('kc_box_public_key'));
            $kcBox = new KcBox($my_box_private_key);
            $decrypted = $kcBox->decrypt($res, $kc_box_public_key);
            kc_dump('解密后的decrypted : ' . $decrypted);
            $siteRight['data'] = $decrypted;
            $siteRight['msg'] = $result['data']['msg'];
        }
    }

    /**
     * @throws DataNotFoundException
     * @throws BusinessException
     * @throws ModelNotFoundException
     * @throws DbException
     * @throws Throwable
     */
    public function login(?callable $callback = null, ?string $modelClass = null): array
    {
        //登录日志app
        $app_type = match ($this->app) {
            KcConst::ADMIN_APP => 0,
            KcConst::API_APP => 1,
            KcConst::APP_MINI_APP => 2,
            default => 0,
        };
        //默认是管理员登录
        if (!$modelClass) {
            $modelClass = User::class;
        }
        if ($callback && is_callable($callback)) {
            //用户自定义的protected/private登录方法 需返回登录成功后的管理员/会员 对象
            $ro = new \ReflectionObject($callback[0]);
            $method = $ro->getMethod($callback[1]);
            $user = $method->invokeArgs($callback[0], [$modelClass]);
        } else {
            //默认的登录方法
            $username = request()->post('username');
            $password = request()->post('password');
            $user = (new $modelClass)
                ->where($this->userField['username'], $username)
                ->whereOr($this->userField['email'], $username)
                ->whereOr($this->userField['mobile'], $username)
                ->find();
            if (!$user) {
                $this->throw('用户不存在');
            }
            if (!password_verify($password, $user->password)) {
                LoginLogService::add($app_type, 0, '用户名或密码错误');
                $this->throw('用户名或密码错误');
            }
            if (intval($user->status) !== 1) {
                LoginLogService::add($app_type, 0, '用户被禁用', $user->{$this->userField['id']});
                $this->throw('用户已被禁用');
            }
            $user->post_password = $password;
        }


        //客户端ip 当项目使用代理(例如nginx)时，使用$request->getRemoteIp()及getLocalIp()得到的往往是代理服务器IP(类似127.0.0.1 192.168.x.x)并非客户端真实IP
        $ip = request()->getRealIp();
        //访问设备信息
        $ua = $this->getUa();
        // kc_dump('ua result:', $ua);
        //生成token
        $token = KcJWT::encode([
            'uid' => $user->{$this->userField['id']},
            'ip' => $ip,
            'os_name' => $ua['os_name'],
            'os_version' => $ua['os_version'],
            'browser_name' => $ua['browser_name'],
            'browser_version' => $ua['browser_version'],
            'device_type' => $ua['device_type'],
        ]);
        $userInfo = $this->userInfo((int)($user->{$this->userField['id']}));
        //更新登录
        $user->last_login_time = date('Y-m-d H:i:s');
        $user->last_login_ip = $ip;
        // 检查是否需要重新哈希
        kc_dump('isset($user->post_password)', isset($user->post_password));
        if (isset($user->post_password) && password_needs_rehash($user->password, PASSWORD_DEFAULT)) {
            kc_dump('需要重新哈希密码');
            $user->password = password_hash($user->post_password, PASSWORD_DEFAULT);
            kc_dump('重新哈希后的密码: ' . $user->password);
        }
        $user->save();
        //登录日志
        LoginLogService::add($app_type, 1, '', $user->{$this->userField['id']});

        kc_dump('RBAC登录的authType: ' . $this->getAuthType());
        if ($this->getAuthType() === 'cookie') {
            // session
            $session = request()->session();
            $session->set($this->userField['id'], $user->{$this->userField['id']});
            $session->set(static::TOKEN, $token);
            // cookie方式下的象征性token 非真实的token
            $userInfo['token'] = static::TOKEN;
        } elseif ($this->getAuthType() === 'token') {
            // JWTToken
            $token_id = bin2hex(pack('d', microtime(true)) . random_bytes(8));
            $data[$this->userField['id']] = $user->{$this->userField['id']};
            $data[static::TOKEN] = $token;
            $cacheDefault = $this->get_cache_default();
            //token过期时间 默认与session过期时间一致 默认7天
            $tokenExpireTime = config('session.lifetime');
            Cache::set($cacheDefault . '_token_' . $token_id, $data, $tokenExpireTime);
            $userInfo['token'] = $token_id;
        }
        return $userInfo;
    }

    /**
     * @throws InvalidArgumentException
     * @throws Exception
     * @throws Throwable
     */
    public function logout(): void
    {
        // 清除缓存
        $sd = $this->getAuthData();
        if (isset($sd['uid']) && $uid = $sd['uid']) {
            delete_cache(static::$config['app'] . ':userInfo:' . $uid);
        }
        if ($this->getAuthType() === 'cookie') {
            //清除session
            $session = request()->session();
            // $session->forget([$this->userField['id'], static::TOKEN]);
            $session->flush();
        } else {
            // 清除token
            $token_id = get_header_token();
            $cacheDefault = $this->get_cache_default();
            Cache::delete($cacheDefault . '_token_' . $token_id);
        }
    }

    private function get_cache_default(): string
    {
        return config('think-cache.default');
    }

    /**
     * @throws BusinessException
     * @throws Throwable
     */
    public function checkLogin(?string $requestIp = null): void
    {
        $sd = $this->getAuthData();
        // kc_dump('checkLogin-' . static::$config['app'], $sd);
        if (!isset($sd[static::TOKEN]) || !$sd[static::TOKEN]) {
            $this->throw('请先登录');
        }
        //是否禁用
        if ($this->userInfo($sd['uid'])['status'] === 0) {
            $this->throw('用户已被禁用');
        }
        if (!isset($sd['decodedToken'])) {
            $sd['decodedToken'] = KcJWT::decode($sd[static::TOKEN]);
        }
        $this->checkTokenValid($sd['decodedToken'], $requestIp);
    }

    /**
     * @throws Exception
     * @throws Throwable
     */
    protected function getAuthData(bool $userInfoCache = true): array
    {
        if ($this->getAuthType() === 'cookie') {
            //使用redis session + cookie + jwtToken方式
            // kc_dump('当前请求path:' . request()->path());
            // kc_dump('当前请求cookie:', request()->cookie());
            $session = request()->session();
            $sd = $session->all();
            // kc_dump(static::$config['app'].'-sd:', $sd);
            $sd['uid'] = isset($sd[$this->userField['id']]) ? $sd[$this->userField['id']] : null;
        } else {
            //使用jwtToken方式
            $token_id = get_header_token();
            if (!$token_id) return [];
            $cacheDefault = $this->get_cache_default();
            $sd = Cache::get($cacheDefault . '_token_' . $token_id);
            if (!isset($sd[static::TOKEN])) return [];
            $sd['decodedToken'] = KcJWT::decode($sd[static::TOKEN]);
            isset($sd['decodedToken']['uid']) && $sd['uid'] = $sd['decodedToken']['uid'];
        }
        return $sd;
    }

    private function checkTokenValid(array $data = [], ?string $requestIp = null): void
    {
        // kc_dump('token decode:', $data);
        if (!$data || !isset($data['uid'])) {
            $this->throw('请先登录');
        }
        if (!isset($data['ip']) || !$data['ip']) {
            $this->throw('请求异常', KcError::TokenIpEmpty[0]);
        }
        if (!$requestIp) {
            $requestIp = request()->getRealIp();
        }
        // kc_dump('请求ip: ' . $requestIp . ' token中ip: ' . $data['ip']);
        if ($requestIp !== $data['ip']) {
            $this->throw('请求异常', KcError::UnMatchedIp[0]);
        }
        //校验user-agent
        $ua = $this->getUA();
        // kc_dump('请求ua:', $ua);
        if (!is_dev_env()) {
            if ((!isset($data['os_name']) || $ua['os_name'] !== $data['os_name'])
                || (!isset($data['os_version']) || $ua['os_version'] !== $data['os_version'])
                || (!isset($data['browser_name']) || $ua['browser_name'] !== $data['browser_name'])
                || (!isset($data['browser_version']) || $ua['browser_version'] !== $data['browser_version'])
                || (!isset($data['device_type']) || $ua['device_type'] !== $data['device_type'])
            ) {
                $this->throw('请求异常', KcError::UnMatchedUA[0]);
            }
        }
    }

    /**
     * 获取管理员id或会员id
     * @return int|null
     * @throws Exception
     * @throws Throwable
     */
    public function getId(): int|null
    {
        $sd = $this->getAuthData();
        // kc_dump('getId sd:', $sd);
        return (isset($sd['uid']) && $sd['uid']) ? (int)$sd['uid'] : null;
    }

    /**
     * @throws Throwable
     */
    public function getUserInfo(bool $isCache = true): array
    {
        return $this->userInfo($this->getId(), $isCache);
    }

    /**
     * @throws Exception
     */
    private function getAuthType(): string
    {
        $authType = request()->header('x-auth-type');
        kc_dump('RBAC getAuthType: ' . $authType);
        if (!$authType) {
            throw new Exception('验证类型未定义');
        }
        if (!in_array($authType, ['cookie', 'token'])) {
            throw new Exception('验证类型错误');
        }
        return $authType;
    }

    private function getUA(): array
    {
        $bd = new \foroco\BrowserDetection();
        // $_SERVER['HTTP_USER_AGENT'] webman中无效 因为Workerman与HTTP协议相关的变量函数无法直接使用
        $userAgent = request()->header('user-agent');
        $ua = $bd->getAll($userAgent);
        if (!$ua) $this->throw('请求异常', KcError::UAEmpty[0]);
        return $ua;
    }

    /**
     * @throws Exception
     * @throws Throwable
     */
    public function isLogin(): bool
    {
        $sd = $this->getAuthData();
        if (!$sd) return false;
        if (!isset($sd['uid']) || !$sd['uid']) return false;
        if (!isset($sd[static::TOKEN]) || !$sd[static::TOKEN]) return false;
        return true;
    }

    /**
     * @throws Exception
     * @throws Throwable
     */
    public function __get($name)
    {
        $userInfo = $this->getUserInfo();
        if (isset($userInfo[$name])) {
            return $userInfo[$name];
        }
        return null;
    }

}