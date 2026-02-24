<?php
// return new Webman\Container;
/**
 * 使用PHP-DI容器
 */
$builder = new \DI\ContainerBuilder();
//使用include代替config() 防止加载不到配置文件
$dependence = include __DIR__ . '/dependence.php';
$builder->addDefinitions($dependence, []);
$builder->useAutowiring(true);
$builder->useAttributes(true);
//生产环境缓存
if (getenv('ENV') === 'production') {
    // 编译容器配置，提升性能
    $builder->enableCompilation(base_path('runtime') . '/phpdi/cache');
    // 生成代理类
    $builder->writeProxiesToFile(true, base_path('runtime') . '/phpdi/proxies');
}
return $builder->build();
