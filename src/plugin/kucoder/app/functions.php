<?php

use plugin\kucoder\app\admin\model\Config;
use support\exception\BusinessException;
use support\think\Cache;
use think\facade\Db;
use support\Log;

/**
 * Here is your custom functions.
 */
if (!function_exists('in_action')) {
    function in_action(string|array $noNeed): bool
    {
        if (is_string($noNeed)) {
            $noNeed = explode(',', $noNeed);
        }
        if (in_array('*', $noNeed)) return true;
        // var_dump(request()->action);
        // var_dump('-------------------------------------');
        // var_dump($noNeed);
        if (in_array(request()->action, $noNeed)) return true;
        return false;
    }
}

if (!function_exists('get_site_config')) {
    /**
     * 获取站点配置
     * @throws Throwable
     */
    function get_site_config(string $name = ''): array|null
    {
        if ($name) {
            $siteConfig = get_site_config();
            $siteConfig = array_column($siteConfig, 'value', 'name');
            return isset($siteConfig[$name]) ? $siteConfig[$name] : null;
        } else {
            $key = 'site_config';
            $expire = 60 * 60 * 24;
            return Cache::remember($key, function () {
                return Db::name('config')->select()->toArray();
            }, $expire);
        }
    }
}

if (!function_exists('get_recursion_data')) {
    /**
     * 递归获取数据
     * @param array $data
     * @param int $pid
     * @param int $level
     * @param bool $self
     * @return array
     */
    function get_recursion_data(array|object $data = [], string $idField = 'id', string $pidField = 'pid', int $pid = 0, int $level = 0, bool $self = false): array
    {
        $recursionData = [];
        $selfData = [];
        foreach ($data as $k => $v) {
            if ($v[$idField] == $pid && $self && $level == 0) {
                $selfData[0] = $v;
            }
            if ($v[$pidField] == $pid) {
                $item = $v;
                unset($data[$k]);   //减少后续递归消耗
                $level++;
                $item['children'] = get_recursion_data($data, $idField, $pidField, $item[$idField], $level);
                $recursionData[] = $item;
            }
        }
        if (!$self) {
            return $recursionData;
        } else {
            $selfData[0]['children'] = $recursionData;
            return $selfData;
        }
    }
}

if(!function_exists('reserve_array_key')){
    /**
     * 恢复数组的键值
     * @param array $array 一维关联数组
     * @param array $fieldsToKeep 要保留的键名列表（索引数组）
     * @return array 索引数组
     */
    function reserve_array_key(array $array,array $fieldsToKeep = []): array
    {
        return array_intersect_key($array, array_flip($fieldsToKeep));
    }
}

if (!function_exists('_2dArray_filter_field')) {
    /**
     * 过滤二维数组的字段
     * @param array $array 二维数组
     * @param array $fieldsToKeep 保留的字段
     * @param array $fieldsToRemove 移除的字段
     * @return array 过滤后的二维数组
     */
    function _2dArray_filter_field(array $array, array $fieldsToKeep = [], array $fieldsToRemove = []): array
    {
        return array_map(function ($item) use ($fieldsToKeep, $fieldsToRemove) {
            if (!empty($fieldsToKeep)) {
                $item = array_intersect_key($item, array_flip($fieldsToKeep));
            }
            if (!empty($fieldsToRemove)) {
                $item = array_diff_key($item, array_flip($fieldsToRemove));
            }
            return $item;
        }, $array);
    }
}

if (!function_exists('_2dTreeArray_filter_field')) {
    /**
     * 递归过滤二维数组字段
     * @param array $array
     * @param array $fieldsToKeep
     * @param array $fieldsToRemove
     * @return array
     */
    function _2dTreeArray_filter_field(array $array, array $fieldsToKeep = [], array $fieldsToRemove = []): array
    {
        return array_map(function ($item) use ($fieldsToKeep, $fieldsToRemove) {
            // 只保留指定的字段
            $filteredItem = [];
            if (!empty($fieldsToKeep)) {
                foreach ($fieldsToKeep as $field) {
                    if (array_key_exists($field, $item)) {
                        $filteredItem[$field] = $item[$field];
                    }
                }
            }
            //要移除的字段
            if (!empty($fieldsToRemove)) {
                foreach ($fieldsToRemove as $field) {
                    if (array_key_exists($field, $item)) {
                        unset($item[$field]);
                    }
                }
                // 合并剩余字段
                $filteredItem = array_merge($filteredItem, $item);
            }
            // 如果有 children，递归处理
            if (isset($item['children']) && is_array($item['children'])) {
                $filteredItem['children'] = _2dTreeArray_filter_field($item['children'], $fieldsToKeep, $fieldsToRemove);
            }
            return $filteredItem;
        }, $array);
    }
}


if (!function_exists('delete_cache')) {
    /**
     * 清除缓存
     * @throws \Psr\SimpleCache\InvalidArgumentException
     * @throws Exception
     */
    function delete_cache(string $key = '', string $tag = ''): bool
    {
        dump('执行删除缓存key', $key);
        if (!$key && !$tag) {
            throw  new \Exception('请指定要删除的缓存key或者tag');
        }
        return $key ? Cache::delete($key) : Cache::tag($tag)->clear();
    }
}

if (!function_exists('get_database')) {
    /**
     * @return string
     */
    function get_database(): string
    {
        $default = config('think-orm.default');
        return config("think-orm.connections.{$default}.database");
    }
}

if (!function_exists('get_table_prefix')) {
    function get_table_prefix(): string
    {
        $default = config('think-orm.default');
        return config("think-orm.connections.{$default}.prefix");
    }
}

if (!function_exists('index_data_cache')) {
    /**
     * @return bool
     */
    function index_data_cache(): bool
    {
        return config('plugin.kucoder.app.index_data_cache');
    }
}

if (!function_exists('kc_log')) {
    function kc_log(string $message, string $level = 'info', array $context = []): void
    {
        Log::log($level, $message, $context);
    }
}

if (!function_exists('plugin_status')) {
    /**
     * 检查插件是否启用
     * @param string $pluginName
     * @return bool
     */
    function plugin_status(string $pluginName): bool
    {
        $plugin = Db::name('ku_plugin_local')->where('name', $pluginName)->field('status')->find();
        if (!$plugin) {
            return false;
        }
        return (bool)$plugin['status'];
    }
}

if (!function_exists('get_pluginName')) {
    function get_pluginName(): string
    {
        return request()->plugin ?: '';
    }
}

if(!function_exists('dev_env')){
    function dev_env(): bool
    {
        return getenv('ENV') === 'development';
    }
}

if(!function_exists('app_debug')){
    function app_debug(): bool
    {
        return getenv('APP_DEBUG') === 'true';
    }
}

if (!function_exists('config_app')) {
    /**
     * 获取插件app配置 config_app：plugin_config_app简写
     * config_app() 获取当前访问路径所属插件的app配置
     * config_app('','version') 获取当前访问路径所属插件的app.version配置
     * config_app('插件名') 获取指定插件的app配置
     * config_app('插件名','version') 获取指定插件的app.version配置
     * @throws Exception
     */
    function config_app(string $pluginName = '', ?string $key = null): mixed
    {
        $pluginName = $pluginName ?: get_pluginName();
        $config = config("plugin.{$pluginName}.app");
        if ($key) {
            return $config[$key] ?? null;
        }
        return $config;
    }
}

if (!function_exists('config_in_db')) {
    /**
     * 获取sys_config表的插件配置
     * @param string|null $plugin
     * @param string|null $group
     * @param string|null $name
     * @param bool $simple
     * @return mixed
     * @throws Throwable
     */
    function config_in_db(?string $plugin = null, ?string $group = null, ?string $name = null, $simple = true): mixed
    {
        $config = Cache::remember('config_in_db', function () {
            return Config::select()->toArray();
        }, config('plugin.kucoder.app.cache_expire_time'));

        if ($plugin) {
            $item = array_filter($config, fn($item) => $item['plugin'] === $plugin);
            $item = $simple ? array_column($item, 'value', 'name') : $item;
            return array_values($item);
        }
        if ($group) {
            $item = array_filter($config, fn($item) => $item['group'] === $group && (!$plugin || $item['plugin'] === $plugin));
            $item = $simple ? array_column($item, 'value', 'name') : $item;
            return array_values($item);
        }
        if ($name) {
            $item = array_filter($config, fn($item) => $item['name'] === $name && (!$group || $item['group'] === $group) && (!$plugin || $item['plugin'] === $plugin));
            $item = $simple ? array_column($item, 'value', 'name') : $item;
            return $item['name'] ?? null;
        }

        return $config;
    }
}

if (!function_exists('is_json')) {
    function is_json($string): bool
    {
        json_decode($string);
        return (json_last_error() == JSON_ERROR_NONE);
    }
}

if (!function_exists('get_header_token')) {
    function get_header_token(): string
    {
        $authorization = request()->header('Authorization');
        if ($authorization && str_starts_with($authorization, 'Bearer')
            && preg_match('/Bearer\s(\S+)/', $authorization, $matches)) {
            $token = $matches[1];
        }
        return $token ?? '';
    }
}

if (!function_exists('enable_coroutine')) {
    function enable_coroutine(string $worker = 'webman'): bool
    {
        return (bool)config("process.{$worker}")['eventLoop'];
    }
}

if(!function_exists('kc_path')){
    /**
     * 将多个路径部分连接成一个完整的路径
     * 统一使用 `/` 作为路径分隔符，跨平台兼容
     *
     * @param mixed ...$parts 路径部分
     * @return string 连接后的路径
     */
    function kc_path(...$parts): string
    {
        $path = '';
        foreach ($parts as $part) {
            if (is_array($part)) {
                $part = kc_path(...$part);
            }
            $part = (string) $part;
            if ($part === '') {
                continue;
            }
            if ($path === '') {
                $path = $part;
            } else {
                // 标准化分隔符并连接
                $path = rtrim(str_replace('\\', '/', $path), '/') . '/' .
                    ltrim(str_replace('\\', '/', $part), '/');
            }
        }
        return $path;
    }
}

if (!function_exists('get_plugin_info')) {
    /**
     * @throws Exception
     */
    function get_plugin_info(string $pluginName=''): array
    {
        $pluginName = $pluginName ?: get_pluginName();
        $pluginPath = base_path("plugin/{$pluginName}");
        if(!is_dir($pluginPath)){
            throw new Exception("Plugin {$pluginName} not exists");
        }
        return include base_path("plugin/{$pluginName}/zinfo/info.php");
    }
}

if (!function_exists('generate_order_no')) {
    /**
     * 生成订单号
     * @param string $payType
     * @return string
     * @throws \Random\RandomException
     */
    function generate_order_no(string $payType = 'wechat'): string
    {
        //唯一订单号 微信小程序支付的订单号out_trade_no的最大长度限制为32位string(32)，且只能包含数字、大小写字母、下划线、减号和星号。这一规则适用于微信、抖音、快手和QQ小程序。
        if ($payType === 'wechat') {
            // time().uniqid().mt_rand(0, 9999)  time()目前10位 uniqid()返回13位 加上mt_rand最多返回4位 共27位 小于32位
            // date('YmdHis')固定 14 位数字（字符串） uniqid()返回13位  加上random_int返回4位 共31位 小于32位
            return date('YmdHis') . uniqid() . random_int(1000, 9999);
        }
        return date('YmdHis') . uniqid();
    }
}

if (!function_exists('devMsg')) {
    /**
     * @throws Exception
     */
    function devMsg(string $msg = ''): string
    {
        return config_app(pluginName: 'kucoder', key: 'debug') ? $msg : '';
    }
}

if (!function_exists('throw_err')) {
    /**
     * @throws Exception
     */
    function throw_err(string $msg = '', ?int $code = 500, ?bool $checkMsg = false): void
    {
        if (!$code) {
            $code = config('plugin.kucoder.app.error_code');
        }
        if ($checkMsg && $msg) {
            if (str_starts_with($msg, 'SQLSTATE[23000]') && str_contains($msg, '1062 Duplicate entry')) {
                $fieldVal = preg_replace("/^SQLSTATE\[23000\](.*)Duplicate entry '(.*?)'(.*)/", '$2', $msg);
                $msg = "数据冲突 数据库中已存在字段值为{$fieldVal}的冲突数据";
            }
        }
        dump('throw_err:', $msg, $code);
        // throw new Exception($msg, $code);
        throw new BusinessException($msg, $code);
    }
}
