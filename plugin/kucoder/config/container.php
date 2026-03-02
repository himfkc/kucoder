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
return $builder->build();
