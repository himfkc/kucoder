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


namespace kucoder\lib;

use Exception;

class KcConfig
{
    /**
     * @throws Exception
     */
    public static function set(string $file, string $key, mixed $value): void
    {
        if (!file_exists($file)) {
            throw new Exception("配置文件不存在: {$file}");
        }
        $content = file_get_contents($file);
        if ($content === false) {
            throw new Exception("无法读取配置文件: {$file}");
        }
        // 解析PHP文件内容为数组
        $config = include $file;
        if (!is_array($config)) {
            throw new Exception("配置文件需返回数组 格式错误: {$file}");
        }
        // 使用点分隔符设置多维数组值
        self::setNestedValue($config, $key, $value);
        // 生成新的配置文件内容
        $newContent = "<?php\n\nreturn " . self::arrayToCode($config) . ";\n";
        // 写入文件
        if (file_put_contents($file, $newContent) === false) {
            throw new Exception("无法写入配置文件: {$file}");
        }
        // 清除缓存
        if (function_exists('opcache_invalidate')) {
            opcache_invalidate($file, true);
        }
        clearstatcache(true, $file);
    }

    /**
     * 设置多维数组的值
     */
    private static function setNestedValue(array &$array, string $key, mixed $value): void
    {
        $keys = explode('.', $key);
        foreach ($keys as $i => $segment) {
            // 如果是最后一个键，设置值
            if ($i === count($keys) - 1) {
                $array[$segment] = $value;
            } else {
                // 如果中间键不存在，创建空数组
                if (!isset($array[$segment]) || !is_array($array[$segment])) {
                    $array[$segment] = [];
                }
                $array = &$array[$segment];
            }
        }
    }

    /**
     * 将数组转换为PHP代码字符串
     */
    private static function arrayToCode(array $array, int $indentLevel = 1): string
    {
        $indent = str_repeat('    ', $indentLevel);
        $entries = [];
        foreach ($array as $key => $value) {
            $formattedKey = self::formatKey($key);
            $formattedValue = self::formatValue($value, $indentLevel + 1);
            $entries[] = "{$indent}{$formattedKey} => {$formattedValue}";
        }
        $innerContent = implode(",\n", $entries);
        $outerIndent = str_repeat('    ', $indentLevel - 1);
        if (empty($entries)) {
            return "[]";
        }
        return "[\n{$innerContent}\n{$outerIndent}]";
    }

    /**
     * 格式化键名
     */
    private static function formatKey($key): string|int
    {
        if (is_int($key)) {
            return $key;
        }
        // 检查是否是有效的变量名
        if (preg_match('/^[a-zA-Z_][a-zA-Z0-9_]*$/', $key)) {
            return "'{$key}'";
        }
        // 包含特殊字符的键名
        return var_export($key, true);
    }

    /**
     * 格式化值
     */
    private static function formatValue($value, int $indentLevel = 1): string
    {
        if (is_array($value)) {
            return self::arrayToCode($value, $indentLevel);
        }
        if (is_string($value)) {
            return "'" . addcslashes($value, "'\\") . "'";
        }
        if (is_bool($value)) {
            return $value ? 'true' : 'false';
        }
        if (is_null($value)) {
            return 'null';
        }
        if (is_float($value)) {
            // 保持浮点数的精度
            return str_replace(',', '.', (string)$value);
        }
        return var_export($value, true);
    }
}
