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


namespace kucoder\controller;

use Exception;
use kucoder\lib\KcHelper;
use PDOException;
use plugin\kucoder\app\admin\model\Config;
use plugin\kucoder\app\admin\model\Role;
use plugin\kucoder\app\admin\model\User;
use kucoder\lib\KcScript;
use kucoder\service\PluginService;
use kucoder\traits\HttpTrait;
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
        if (file_exists(base_path('plugin/kucoder/api/install/installed.lock'))) {
            return $this->error('你已安装kucoder系统，勿重复安装');
        }

        // 删除缓存
        $redisPrefix = getenv('REDIS_PREFIX') ?: 'kucoder:';
        delete_prefix_cache($redisPrefix);

        // 检查env是否存在
        $kucoder_api = getenv('KUCODER_API');
        if (!file_exists(base_path('.env'))) {
            // copy(base_path('.env.example'), base_path('.env'));
            $fp = fopen(base_path('.env'), 'w');
            fwrite($fp, file_get_contents(base_path('.env.example')));
            fflush($fp);
            fclose($fp);
            $kucoder_api = getenv('KUCODER_API');
        }

        $uri = $kucoder_api . '/ks/install/envCheck';
        $data = KcScript::content($uri, true);
        $data = json_decode($data, true);
        if (isset($data['code']) && $data['code'] === 0) {
            return $this->error($data['msg'], $data['data'] ?? []);
        }
        return $this->ok('', $data);
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
            $createDb = $this->createDatabase($data['db_name'], $data['db_host'], $data['db_user'], $data['db_password']);
            if (!$createDb['success']) {
                return $this->error($createDb['message']);
            }

            //env
            $newData = array_merge(get_env(),$data);
            $uri = getenv('KUCODER_API') . '/ks/install/envSet';
            $params = [
                'env_file' => base_path('.env'),
                'newData' => json_encode($newData, JSON_UNESCAPED_UNICODE),
                'strict' => true,
                'method' => 'POST'
            ];
            KcScript::curl($uri, $params);

            //初始化配置文件
            // KcHelper::initConfig();

            return $this->success('初始化环境成功');
        } catch (Throwable $e) {
            return $this->error('初始化环境失败：' . $e->getMessage());
        }
    }


    /**
     * @throws Throwable
     */
    public function getQrcode(Request $request): Response
    {
        $uri = getenv('KUCODER_API') . '/ks/install/getQrcode';
        $res = $this->http_post($uri, needLogin: false);
        return $this->ok('', $res['data']);
    }

    public function verifyWxCode(): Response
    {
        try {
            $data = $this->request->post();
            //校验码
            $uri = getenv('KUCODER_API') . '/ks/install/checkWxCode';
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
            $this->stat($data);
            return $this->ok('', ['msg' => '安装成功', 'vue_admin_entry' => getenv('VUE_ADMIN_ENTRY')]);
        } catch (Throwable $e) {
            return $this->error($e->getMessage());
        }
    }

    /**
     * @throws Exception
     */
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
            $config = Config::where(['plugin' => 'kucoder', 'name' => 'vue_admin_entry'])->find();
            if ($config) {
                $config->value = getenv('VUE_ADMIN_ENTRY');
                $config->save();
            }

            Db::commit();
        } catch (Throwable $e) {
            Db::rollback();
            kc_dump('初始化数据库异常：', $e->getMessage());
            throw new Exception($e->getMessage());
        }
    }

    /**
     * @throws Exception
     */
    private function stat(array $data): void
    {
        $uri = getenv('KUCODER_API') . '/ks/install/stat';
        $d = [
            //appid、appsecret仅是安装序号 非密码
            'id1' => get_env('kc_appid'),
            'id2' => get_env('kc_appsecret'),
            'php' => phpversion(),
            'db' => $data['db_type']
        ];
        $this->http_post($uri, $d, needLogin: false);
    }

    /**
     * 创建数据库
     * @param string $db_name 数据库名称
     * @param string $host 数据库主机名 (默认: localhost)
     * @param string $username 数据库用户名 (默认: root)
     * @param string $password 数据库密码 (默认: 空密码)
     * @return array 返回操作结果，包含状态、消息和PDO对象（如果成功）
     */
    private function createDatabase(string $db_name = '', string $host = 'localhost', string $username = 'root', string $password = ''): array
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

}