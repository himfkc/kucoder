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


namespace plugin\kucoder\app\kucoder\command;

use Exception;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class KcPluginCreate
 * @package plugin\kucoder\command
 * @note 创建插件命令
 */
#[AsCommand(name: 'kucoder-plugin:create', description: 'Create a kucoder plugin')]
class KcPluginCreate extends Command
{
    /**
     * @return void
     */
    protected function configure(): void
    {
        $this->addArgument('name', InputArgument::REQUIRED, 'App plugin name');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     * @throws Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $name = $input->getArgument('name');
        $output->writeln("Create App PluginService $name");

        if (str_contains($name, '/')) {
            $output->writeln('<error>Bad name, name must not contain character \'/\'</error>');
            return self::FAILURE;
        }

        // Create dir config/plugin/$name
        if (is_dir($plugin_config_path = base_path() . "/plugin/$name")) {
            $output->writeln("<error>Dir $plugin_config_path already exists</error>");
            return self::FAILURE;
        }

        $this->createAll($name);

        return self::SUCCESS;
    }

    /**
     * @param $name
     * @return void
     */
    protected function createAll($name): void
    {
        $base_path = base_path();
        $this->mkdir("$base_path/plugin/$name/app/controller", 0777, true);
        $this->mkdir("$base_path/plugin/$name/app/model", 0777, true);
        $this->mkdir("$base_path/plugin/$name/app/middleware", 0777, true);
        // $this->mkdir("$base_path/plugin/$name/app/view/index", 0777, true);
        $this->mkdir("$base_path/plugin/$name/config", 0777, true);
        $this->mkdir("$base_path/plugin/$name/public", 0777, true);
        $this->mkdir("$base_path/plugin/$name/api", 0777, true);
        $this->mkdir("$base_path/plugin/$name/api/install", 0777, true);
        $this->createFunctionsFile("$base_path/plugin/$name/app/functions.php");
        // $this->createControllerFile("$base_path/plugin/$name/app/controller/IndexController.php", $name);
        // $this->createViewFile("$base_path/plugin/$name/app/view/index/index.html");
        $this->createConfigFiles("$base_path/plugin/$name/config", $name);
        $this->createInstallClass("$base_path/plugin/$name/api/install", $name);
        $this->createSqlFile("$base_path/plugin/$name/api/install/install.sql");
        $this->createSqlFile("$base_path/plugin/$name/api/install/update.sql");
        $this->createSqlFile("$base_path/plugin/$name/api/install/uninstall.sql");
        $this->createPluginClass("$base_path/plugin/$name/api", $name);
        $this->mkdir("$base_path/plugin/$name/zinfo", 0777, true);
        $this->createZinfoFiles("$base_path/plugin/$name/zinfo", $name);
        $this->mkdir("$base_path/plugin/$name/vue", 0777, true);
        $this->createVueFiles("$base_path/plugin/$name/vue", $name);
    }

    protected function createZinfoFiles($base, $name): void
    {
        $this->mkdir($base . '/images', 0777, true);
        file_put_contents($base . '/intro.md', '');
        file_put_contents($base . '/logo.jpg', '');
        //info.php
        $content = <<<EOF
<?php
return [
    /**
     * 以下是插件信息 kucoder的每个插件都需要填写
     */
    //插件标识 全网唯一 仅限小写字母
    'name' => '$name',
    //插件名称 中文
    'title' => '',
    //插件版本号 如1.0.0
    'version' => '1.0.0',
    //插件依赖的最低kucoder版本
    'kucoder_version' => '1.0.0',
    //插件类型 0=辅助开发插件,1=完整独立系统,2=完整sass系统,3=物联网应用
    'type' => 0,
    //插件是否收费 付费类型:0=免费,1=收费
    'fee_type' => 0,
    //普通授权收费价格 如99/99.9 普通授权的购买者仅能用于自营项目 普通授权期限内免费升级维护
    'common_price' => 0,
    //高级授权收费价格 如999/999.9 高级授权的购买者能用于自营及外包项目 高级授权期限内免费升级维护
    'advance_price' => 0,
    //插件作者
    'author' => '',
    //插件作者ID 即kucoder的会员ID 登录kucoder后查看
    'author_id' => 1,
    //插件主页地址
    'homepage' => '',
    /*//插件文档地址
    'doc_url' => '',*/
    /*//是否创建插件目录 插件目录:0=不创建,1=创建
    'has_dir' => 1,
    //插件是否含有数据表 0=不含,1=包含
    'has_db' => 1,
    //插件是否含有vue模板文件 0=不含,1=包含 即vue-kc-admin的src/view目录是否创建有此插件的模板文件
    'has_view' => 1,*/
];
EOF;
        file_put_contents($base . '/info.php', $content);
        //  rely.json
        $content = <<<EOF
{
  "require": {},
  "require-dev": {},
  "dependencies": {},
  "devDependencies": {}
}
EOF;
        file_put_contents($base . '/rely.json', $content);

    }

    protected function createVueFiles($base, $name): void
    {
        $this->mkdir($base . "/src/api/{$name}", 0777, true);
        $this->mkdir($base . "/src/views/{$name}", 0777, true);
        // $this->mkdir($base . "/src/addon/{$name}", 0777, true);
    }

    protected function createPluginClass($base, $name): void
    {
        $class= 'Plugin'.ucfirst($name);
        $content = <<<EOF
<?php
declare(strict_types=1);

namespace plugin\\$name\\api;

/**
 * 插件对外提供的接口类
 */
class $class
{

}
EOF;
        file_put_contents($base . '/' . $class . '.php', $content);
    }

    /**
     * @param $path
     * @return void
     */
    protected function mkdir($path): void
    {
        if (is_dir($path)) {
            return;
        }
        echo "Create $path\r\n";
        mkdir($path, 0777, true);
    }

    /**
     * @param $path
     * @param $name
     * @return void
     */
    protected function createControllerFile($path, $name): void
    {
        $content = <<<EOF
<?php

namespace plugin\\$name\\app\\controller;

use support\\Request;

class IndexController
{

    public function index()
    {
        return view('index/index', ['name' => '$name']);
    }

}

EOF;
        file_put_contents($path, $content);

    }

    /**
     * @param $path
     * @return void
     */
    protected function createViewFile($path): void
    {
        $content = <<<EOF
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="shortcut icon" href="/favicon.ico"/>
    <title>webman app plugin</title>

</head>
<body>
hello <?=htmlspecialchars(\$name)?>
</body>
</html>


EOF;
        file_put_contents($path, $content);

    }


    /**
     * @param $file
     * @return void
     */
    protected function createFunctionsFile($file): void
    {
        $content = <<<EOF
<?php
/**
 * Here is your custom functions.
 */



EOF;
        file_put_contents($file, $content);
    }

    /**
     * @param $base
     * @param $name
     * @return void
     */
    protected function createInstallClass($base, $name): void
    {
        $content = <<<EOF
<?php

namespace plugin\\$name\\api;

use Exception;
use plugin\kucoder\app\kucoder\install\PluginInstall;

class Install extends PluginInstall
{
    //插件默认使用主项目数据库配置
    protected static string ?\$connection = '';
    
    /**
     * 安装插件
     * @param string \$version
     * @return void
     * @throws Exception
     */
    public static function install(string \$version = ''): void
    {
        //调用系统安装逻辑 已包含安装插件数据库、导入插件菜单、导入插件vue文件、安装插件依赖
        parent::install(\$version);

        //插件额外的安装逻辑

    }

    /**
     * 卸载插件
     * @param string \$version
     * @return void
     * @throws Throwable
     */
    public static function uninstall(string \$version=''): void
    {
        //调用系统卸载逻辑 已包含卸载插件数据库、卸载插件菜单、卸载插件vue文件、卸载插件依赖
        parent::uninstall(\$version);

        //插件额外的卸载逻辑

    }

    /**
     * 升级插件
     * @param string \$to_version
     * @param null \$context
     * @return void
     * @throws Throwable
     * @throws ReflectionException
     * @throws \think\Exception
     */
    public static function update(string \$to_version='', \$context = null): void
    {
        //调用系统升级逻辑
        parent::update(\$to_version);

        //插件额外的升级逻辑

    }

}
EOF;

        file_put_contents("$base/Install.php", $content);

    }

    /**
     * @param string $file
     * @return void
     */
    protected function createSqlFile(string $file): void
    {
        file_put_contents($file, '');
    }

    /**
     * @param $base
     * @param $name
     * @return void
     */
    protected function createConfigFiles($base, $name): void
    {
        // app.php
        $content = <<<EOF
<?php

use support\\Request;

return [
    /**
     * 插件配置文件
     */
    //调试模式
    'debug' => true,
    //控制器后缀 不建议修改
    'controller_suffix' => 'Controller',
    //控制器复用 禁止开启
    'controller_reuse' => false,
    //插件版本号
    'version' => '1.0.0',
    
];

EOF;
        file_put_contents("$base/app.php", $content);

        // menu.php
        $content = <<<EOF
<?php

return [];

EOF;
        file_put_contents("$base/menu.php", $content);

        // autoload.php
        $content = <<<EOF
<?php
return [
    'files' => [
        base_path() . '/plugin/$name/app/functions.php',
    ]
];
EOF;
        file_put_contents("$base/autoload.php", $content);

        // container.php
        $content = <<<EOF
<?php
return new Webman\\Container;

EOF;
        file_put_contents("$base/container.php", $content);

        //dependence.php
        $content = <<<EOF
<?php
//container中使用config('dependence')无效的话 可直接include 此文件
return [];
EOF;
        file_put_contents("$base/denpendence.php", $content);


        // database.php
        $content = <<<EOF
<?php
return  [];

EOF;
        file_put_contents("$base/database.php", $content);

        // exception.php
        $content = <<<EOF
<?php

return [
    '' => plugin\\kucoder\\app\\kucoder\\exception\\KcExceptionHandler::class,
];

EOF;
        file_put_contents("$base/exception.php", $content);

        // log.php
        $content = <<<EOF
<?php

return [
    'default' => [
        'handlers' => [
            [
                'class' => Monolog\\Handler\\RotatingFileHandler::class,
                'constructor' => [
                    runtime_path() . '/logs/$name.log',
                    7,
                    Monolog\\Logger::DEBUG,
                ],
                'formatter' => [
                    'class' => Monolog\\Formatter\\LineFormatter::class,
                    'constructor' => [null, 'Y-m-d H:i:s', true],
                ],
            ]
        ],
    ],
];

EOF;
        file_put_contents("$base/log.php", $content);

        // middleware.php
        $content = <<<EOF
<?php

return [
    '' => [
        
    ]
];

EOF;
        file_put_contents("$base/middleware.php", $content);

        // process.php
        $content = <<<EOF
<?php
return [];

EOF;
        file_put_contents("$base/process.php", $content);

        // redis.php
        $content = <<<EOF
<?php
return [
    'default' => [
        'host' => '127.0.0.1',
        'password' => null,
        'port' => 6379,
        'database' => 0,
    ],
];

EOF;
        file_put_contents("$base/redis.php", $content);

        // route.php
        $content = <<<EOF
<?php

use Webman\\Route;


EOF;
        file_put_contents("$base/route.php", $content);

        // static.php
        $content = <<<EOF
<?php

return [
    'enable' => true,
    'middleware' => [],    // Static file Middleware
];

EOF;
        file_put_contents("$base/static.php", $content);

        // translation.php
        $content = <<<EOF
<?php

return [
    // Default language
    'locale' => 'zh_CN',
    // Fallback language
    'fallback_locale' => ['zh_CN', 'en'],
    // Folder where language files are stored
    'path' => base_path() . "/plugin/$name/resource/translations",
];

EOF;
        file_put_contents("$base/translation.php", $content);

        // view.php
        $content = <<<EOF
<?php

use support\\view\\Raw;
use support\\view\\Twig;
use support\\view\\Blade;
use support\\view\\ThinkPHP;

return [
    'handler' => Raw::class
];

EOF;
        file_put_contents("$base/view.php", $content);

        // thinkorm.php
        $content = <<<EOF
<?php

return [];

EOF;
        file_put_contents("$base/thinkorm.php", $content);

    }
}