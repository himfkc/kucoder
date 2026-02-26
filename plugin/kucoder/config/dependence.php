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

use Psr\Container\ContainerInterface;

return [
    // 基本值定义
    // 'database.host' => 'localhost',
    // 'database.name' => 'myapp',

    // 接口绑定
    // LoggerInterface::class => DI\create(FileLogger::class),

    // 带参数的类
    /*
     Database::class => DI\create()->constructor(
            DI\get('database.host'),
            DI\get('database.name')
        ),
    */

    // 工厂函数
    /*
     Cache::class => DI\factory(function($container) {
        $config = $container->get('cache.config');
        return new RedisCache($config);
    }),
    */

    //自定义接口方法
    /*
     * config/dependence.php 中使用了new来实例化Mailer类，这个在本示例没有任何问题，
     * 但是想象下如果Mailer类依赖了其它类的话或者Mailer类内部使用了注解注入，使用new初始化将不会依赖自动注入。
     * 解决办法是利用自定义接口注入，通过Container::get(类名) 或者 Container::make(类名, [构造函数参数])方法来初始化类。
    app\service\MailerInterface::class => function(\Psr\Container\ContainerInterface $container) {
        return $container->make(app\service\Mailer::class, ['smtp_host' => '192.168.1.11', 'smtp_port' => 25]);
    }
    */

    // 别名
    // 'db' => DI\get(Database::class),

    /* 自定义接口方法
     \kucoder\lib\http\HttpInterface::class =>
        function (Psr\Container\ContainerInterface $container) {
            $http_client_type = config('plugin.kucoder.app.http_client_type');
            $httpClass = match ($http_client_type) {
                'guzzleHttp' => KcGuzzleHttp::class,
                'workerman' => KcWorkerHttp::class,
            };
            //使用new $httpClass()初始化类 如果$httpClass类依赖了其它类的话或者$httpClass类内部使用了注解注入，使用new初始化将不会依赖自动注入。
            return $container->make($httpClass, []);
        },
    */
    'http' => function (ContainerInterface $container) {
        return $container->make(\kucoder\lib\http\KcGuzzleHttp::class, []);
    },
    'http_async' => function (ContainerInterface $container) {
        return $container->make(\kucoder\lib\http\KcWorkerHttp::class, []);
    },
    'oLog' => \DI\factory(function ($container) {
        return $container->make(\kucoder\service\OperLogService::class, []);
    }),
];
