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


namespace plugin\kucoder\app\kucoder\controller;

use Exception;
use plugin\kucoder\app\admin\model\Config;
use plugin\kucoder\app\admin\model\Role;
use plugin\kucoder\app\admin\model\User;
use plugin\kucoder\app\kucoder\lib\KcScript;
use plugin\kucoder\app\kucoder\service\PluginService;
use plugin\kucoder\app\kucoder\traits\HttpTrait;
use support\Request;
use support\Response;
use support\think\Db;
use Throwable;
use PDO;

class InstallController extends AdminBase
{
    use HttpTrait;

    /**
     * 不需要登录
     * @var array
     */
    protected array $noNeedLogin = ['envCheck', 'getQrcode', 'verifyWxCode', 'install', 'init'];
    protected string $validateClass = "\\plugin\\kucoder\\app\\admin\\validate\\Install";

    /**
     * @throws Exception
     */
    public function envCheck(): Response
    {
        // 已安装kucoder
        if(file_exists(base_path('plugin/kucoder/api/install/installed.lock'))){
            return $this->error('你已安装kucoder系统，勿重复安装');
        }
        // 删除缓存
        $redisPrefix = getenv('REDIS_PREFIX') ?: 'kucoder:';
        delete_prefix_cache($redisPrefix);
        
        $uri = config('plugin.kucoder.app.sys_url') . '/ks/install/envCheck';
        $data = KcScript::content($uri, true);
        $data = json_decode($data, true);
        if (isset($data['code']) && $data['code'] === 0) {
            return $this->error($data['msg'], $data['data'] ?? []);
        }
        return $this->ok('', $data);
    }


    /**
     * @throws Throwable
     */
    public function getQrcode(Request $request): Response
    {
        $uri = config('plugin.kucoder.app.sys_url') . '/kapi/kins/getQrcode';
        // $this->setHttpOptions(['verify'=>false]);
        $res = $this->http_post($uri, needLogin: false);
        return $this->ok('', $res['data']);
    }

    public function verifyWxCode(): Response
    {
        try {
            $data = $this->request->post();
            //校验码
            $uri = config('plugin.kucoder.app.sys_url') . '/kapi/kins/checkWxCode';
            $this->http_post($uri, ['wx_code' => $data['wx_code']], needLogin: false);
            unset($data['wx_code']);
            return $this->ok('', ['msg' => '验证成功']);
        } catch (Throwable $e) {
            return $this->error($e->getMessage());
        }
    }

    /**
     * @throws Exception
     */
    public function install(): Response
    {
        try {
            $data = $this->request->post();
            PluginService::install('kucoder');
            $this->initDb($data);
            return $this->ok('', ['msg' => '安装成功','vue_admin_entry'=>config_app('kucoder','vue_admin_entry')]);
        } catch (Throwable $e) {
            return $this->error($e->getMessage());
        }
    }

    private function initDb(array $data): void
    {
        kc_dump('开始初始化数据库');
        Db::startTrans();
        try {
            //角色表
            $role = new Role();
            $role->role_name = '超级管理员';
            $role->rules = ['*'];
            $role->create_uid = 1;
            $role->update_uid = 1;
            $role->save();

            //管理员
            $user = new User();
            $user->username = $data['admin_username'];
            $user->password = password_hash($data['admin_password'], PASSWORD_BCRYPT);
            $user->role_ids = [1];
            $user->save();

            //更新vue_admin_entry
            $vue_admin_entry = config_app('kucoder', 'vue_admin_entry');
            $config = Config::where(['plugin' => 'kucoder', 'name' => 'vue_admin_entry'])->find();
            if ($config) {
                $config->value = $vue_admin_entry;
                $config->save();
            }
            
            Db::commit();
        } catch (Throwable $e) {
            Db::rollback();
            kc_dump('初始化数据库异常：',$e->getMessage());
            throw new Exception($e->getMessage());
        }
    }

    /**
     * @throws Exception
     */
    private function stat(array $data): void
    {
        $uri = config('plugin.kucoder.app.sys_url') . '/kapi/kins/stat';
        $sysId = config('plugin.kucoder.app.secret-key.kc');
        //appid、appsecret仅是安装序号 非密码
        $d = [
            'id1' => $sysId['appid'],
            'id2' => $sysId['appsecret'],
            'php' => phpversion(),
            'db' => $data['db_type']
        ];
        $this->http_post($uri, $d, needLogin: false);
    }

    /**
     * 创建数据库
     *  @param string $db_name 数据库名称
     * @param string $host 数据库主机名 (默认: localhost)
     * @param string $username 数据库用户名 (默认: root)
     * @param string $password 数据库密码 (默认: 空密码)
     * @return array 返回操作结果，包含状态、消息和PDO对象（如果成功）
     */
    private function createDatabase(string $db_name= '',string $host = 'localhost', string $username = 'root', string $password = ''):array 
    {
        try {
            // 1. 首先连接到MySQL服务器（不指定具体数据库）
            $dsn = "mysql:host={$host};charset=utf8mb4";
            $pdo = new PDO($dsn, $username, $password);
            
            // 设置PDO错误模式为异常
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            // 2. 检查数据库是否已存在
            // 解决方案1：使用PDO的quote方法安全地添加引号（推荐）
            $quoted_db_name = $pdo->quote($db_name);
            $stmt = $pdo->query("SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = {$quoted_db_name}");
            
            // 解决方案2：也可以使用占位符和预处理语句（更安全）
            /*
            $stmt = $pdo->prepare("SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = ?");
            $stmt->execute([$db_name]);
            */

            $databaseExists = $stmt->fetch();
            
            if ($databaseExists) {
                return [
                    'success' => true,
                    'message' => "数据库{$db_name}已存在",
                    'exists' => true,
                    'pdo' => $pdo
                ];
            }
            
            // 3. 创建数据库，使用utf8mb4_general_ci编码
            $sql = "CREATE DATABASE `{$db_name}` 
                    CHARACTER SET utf8mb4 
                    COLLATE utf8mb4_general_ci";
            
            $pdo->exec($sql);
            
            return [
                'success' => true,
                'message' => "数据库{$db_name}创建成功！编码：utf8mb4_general_ci",
                'exists' => false,
                'pdo' => $pdo
            ];
            
        } catch (PDOException $e) {
            return [
                'success' => false,
                'message' => '数据库创建失败：' . $e->getMessage(),
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * @throws Exception
     */
    public function init(): Response
    {
        try {
            $data = $this->request->post();
            $this->validate(param: $data);

            //创建数据库
            $createDb = $this->createDatabase($data['db_name'],$data['db_host'],$data['db_user'],$data['db_password']);
            if(!$createDb['success']){
                return $this->error($createDb['message']);
            }

            //env
            $newData = [];
            $envData = getenv();
            foreach ($data as $k => $v) {
                if (isset($envData[strtoupper($k)])) {
                    $newData[trim(strtoupper($k))] = trim($v);
                }
            }
            kc_dump('env的newData:',$newData);
            $uri = config('plugin.kucoder.app.sys_url') . '/ks/install/envSet';
            $params = [
                'env_file' => base_path('.env'),
                'newData' => json_encode($newData, JSON_UNESCAPED_UNICODE),
                'strict' => true,
                'method' => 'POST'
            ];
            KcScript::curl($uri, $params);

            //配置文件
            if (is_file($rc_file = config_path('redis.php'))) {
                $redis_config = <<<'EOF'
<?php
/**
 * This file is part of webman.
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the MIT-LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @author    walkor<walkor@workerman.net>
 * @copyright walkor<walkor@workerman.net>
 * @link      http://www.workerman.net/
 * @license   http://www.opensource.org/licenses/mit-license.php MIT License
 */

return [
    'default' => [
        'password' => getenv('REDIS_PASSWORD'),
        'host' => getenv('REDIS_HOST'),
        'port' => (int)getenv('REDIS_PORT'),
        'database' => getenv('REDIS_DATABASE'),
        'pool' => [
            'max_connections' => 5,
            'min_connections' => 1,
            'wait_timeout' => 3,
            'idle_timeout' => 60,
            'heartbeat_interval' => 50,
        ],
    ]
];
EOF;
                file_put_contents($rc_file, $redis_config);
            }
            if (is_file($sc_file = config_path('session.php'))) {
                $session_config = <<<'EOF'
<?php
/**
 * This file is part of webman.
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the MIT-LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @author    walkor<walkor@workerman.net>
 * @copyright walkor<walkor@workerman.net>
 * @link      http://www.workerman.net/
 * @license   http://www.opensource.org/licenses/mit-license.php MIT License
 */

use Webman\Session\FileSessionHandler;
use Webman\Session\RedisSessionHandler;
use Webman\Session\RedisClusterSessionHandler;

return [

    'type' => 'redis', // file or redis or redis_cluster

    /*
     * handler配置
     * type使用file时，handler可选 FileSessionHandler::class
     * type使用redis时，handler可选 RedisSessionHandler::class
     * type使用redis_cluster时，handler可选 RedisClusterSessionHandler::class
     */
    'handler' => RedisSessionHandler::class,

    'config' => [
        'file' => [
            'save_path' => runtime_path() . '/sessions',
        ],
        'redis' => [
            'host' => getenv('REDIS_HOST'),
            'port' => (int)getenv('REDIS_PORT'),
            'auth' => getenv('REDIS_PASSWORD'),
            'timeout' => 2,
            'database' => getenv('REDIS_DATABASE'),
            'prefix' => getenv('REDIS_PREFIX') . 'redis_session_',
        ],
        'redis_cluster' => [
            'host' => ['127.0.0.1:7000', '127.0.0.1:7001', '127.0.0.1:7002'],
            'timeout' => 2,
            'auth' => '',
            'prefix' => 'redis_session_',
        ]
    ],
    // 存储session_id的cookie名
    'session_name' => 'PHPSID',
    // 是否自动刷新session，默认关闭
    'auto_update_timestamp' => false,
    // session过期时间
    'lifetime' => 7 * 24 * 60 * 60,
    // 存储session_id的cookie过期时间
    'cookie_lifetime' => 365 * 24 * 60 * 60,
    // 存储session_id的cookie路径
    'cookie_path' => '/',
    // 存储session_id的cookie域名
    'domain' => '',
    // 是否开启httpOnly，默认开启
    'http_only' => true,
    // 仅在https下开启session，默认关闭
    'secure' => false,
    // 用于防止CSRF攻击和用户追踪，可选值strict/lax/non
    'same_site' => 'lax',
    // 回收session的几率
    'gc_probability' => [1, 1000],

];

EOF;
                file_put_contents($sc_file, $session_config);
            }
            if (is_file($orm_file = config_path('think-orm.php'))) {
                $orm_config = <<<'EOF'
<?php
return [
    'default' => 'mysql',
    'connections' => [
        'mysql' => [
            // 数据库类型
            'type' => 'mysql',
            // 服务器地址
            'hostname' => getenv('DB_HOST'),
            // 数据库名
            'database' => getenv('DB_NAME'),
            // 数据库用户名
            'username' => getenv('DB_USER'),
            // 数据库密码
            'password' => getenv('DB_PASSWORD'),
            // 数据库连接端口
            'hostport' => getenv('DB_PORT'),
            // 数据库连接参数
            'params' => [
                // 连接超时3秒
                \PDO::ATTR_TIMEOUT => 3,
            ],
            // 数据库编码默认采用utf8
            'charset' => getenv('DB_CHARSET'),
            // 数据库表前缀
            'prefix' => getenv('DB_PREFIX'),
            // 断线重连
            'break_reconnect' => true,
            // 自定义分页类
            'bootstrap' => '',
            // 连接池配置
            'pool' => [
                'max_connections' => 5, // 最大连接数
                'min_connections' => 1, // 最小连接数
                'wait_timeout' => 3,    // 从连接池获取连接等待超时时间
                'idle_timeout' => 60,   // 连接最大空闲时间，超过该时间会被回收
                'heartbeat_interval' => 50, // 心跳检测间隔，需要小于60秒
            ],
            // 全局自动时间戳
            'auto_timestamp' => getenv('DB_AUTO_TIMESTAMP') === 'true',
            // 字段缓存 对模型及Db都有效
            'fields_cache' => getenv('DB_FIELDS_CACHE') === 'true',
        ],
    ],
];
EOF;
                file_put_contents($orm_file, $orm_config);
            }
            if (is_file($cc_file = config_path('think-cache.php'))) {
                $cache_config = <<<'EOF'
<?php
return [
    // 默认缓存驱动
    'default' => 'redis',
    // 缓存连接方式配置
    'stores' => [
        // redis缓存
        'redis' => [
            // 驱动方式
            'type' => 'redis',
            // 服务器地址
            'host' => getenv('REDIS_HOST'),
            // redis端口 官方默认没有这个选项
            'port' => (int)getenv('REDIS_PORT'),
            // 缓存前缀
            'prefix' => getenv('REDIS_PREFIX'),
            // 默认缓存有效期 0表示永久缓存
            'expire' => 0,
            // Thinkphp官方没有这个参数，由于生成的tag键默认不过期，如果tag键数量很大，避免长时间占用内存，可以设置一个超过其他缓存的过期时间，0为不设置
            'tag_expire' => 86400 * 30,
            // 缓存标签前缀
            'tag_prefix' => 'tag:',
            // 连接池配置
            'pool' => [
                'max_connections' => 5, // 最大连接数
                'min_connections' => 1, // 最小连接数
                'wait_timeout' => 3,    // 从连接池获取连接等待超时时间
                'idle_timeout' => 60,   // 连接最大空闲时间，超过该时间会被回收
                'heartbeat_interval' => 50, // 心跳检测间隔，需要小于60秒
            ],
        ],
        // 文件缓存
        'file' => [
            // 驱动方式
            'type' => 'file',
            // 设置不同的缓存保存目录
            'path' => runtime_path() . '/file/',
        ],
    ],
];
EOF;
                file_put_contents($cc_file, $cache_config);
            }

            //初始化secret-key
            $uri = config('plugin.kucoder.app.sys_url') . '/ks/install/initSecretKey';
            $params = [
                'method' => 'POST',
                'secret_key_file' => base_path('plugin/kucoder/config/secret-key.php'),
                'strict' => true,
            ];
            KcScript::curl($uri, $params);
            return $this->success('初始化环境成功');
        } catch (Throwable $e) {
            return $this->error('初始化环境失败：' . $e->getMessage());
        }
    }
}