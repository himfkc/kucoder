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


namespace plugin\kucoder\app\kucoder\lib;

use Redis;
use RedisException;
use Throwable;
use Exception;
// use plugin\kucoder\app\kucoder\lib\KcConfig;

class KcScript
{
    /**
     * @throws RedisException
     */
    private static function redis(): Redis
    {
        $redis = new Redis();
        $redis->connect(getenv('REDIS_HOST') ?: '127.0.0.1', (int)getenv('REDIS_PORT') ?: 6379);
        //临时禁用保护模式
        $redis->config('SET', 'protected-mode', 'no');
        return $redis;
    }

    /**
     * @throws Exception
     */
    public static function content(string $url, bool $strict = false): string
    {
        $tempFile = tempnam(sys_get_temp_dir(), 'ku_script_content');
        try {
            if (!filter_var($url, FILTER_VALIDATE_URL)) {
                throw new Exception("无效的URL: " . $url);
            }
            if (!ini_get('allow_url_fopen')) {
                throw new Exception("allow_url_fopen 未启用，无法使用 file_get_contents 方式获取远程脚本");
            }
            $id = KcHelper::uuid4(true);
            $url .= (!str_contains($url, '?') ? '?' : '&') . 'id=' . $id;
            if ($strict) {
                $url .= '&strict=1';
                $redis = self::redis();
                $redis->setEx('kc_' . $id, 10, true);
            }
            $res = file_get_contents($url);
            if ($res === false) {
                throw new Exception("无法获取远程脚本: " . $url);
            }
            file_put_contents($tempFile, $res);
            // 执行PHP代码并捕获输出
            ob_start();
            include $tempFile;
            $output = ob_get_clean();
            // 清理临时文件
            unlink($tempFile);
            return $output;
        } catch (Throwable $t) {
            unlink($tempFile);
            throw new Exception($t->getMessage());
        }
    }

    /**
     * @throws Exception
     */
    public static function curl(string $url, array $param = []): string
    {
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            throw new Exception("无效的URL: " . $url);
        }
        if (!extension_loaded('curl')) {
            throw new Exception("PHP 没有安装 cURL 扩展");
        }
        $tempFile = tempnam(sys_get_temp_dir(), 'ku_script_curl');
        try {
            $isPost = false;
            if (!empty($param)) {
                if (!isset($param['method']) || strtolower($param['method']) == 'get') {
                    if (isset($param['method'])) unset($param['method']);
                    $url .= (!str_contains($url, '?') ? '?' : '&') . http_build_query($param);
                }
                if (isset($param['method']) && strtolower($param['method']) == 'post') {
                    unset($param['method']);
                    $isPost = true;
                }
            }
            $id = KcHelper::uuid4(true);
            $url .= (!str_contains($url, '?') ? '?' : '&') . 'id=' . $id;
            if (isset($param['strict']) && $param['strict']) {
                $url .= '&strict=1';
                $redis = self::redis();
                $redis->setEx('kc_' . $id, 10, true);
            }
            $ch = curl_init();
            $options = [
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_USERAGENT => 'PHP-Script',
                CURLOPT_TIMEOUT => 30,
            ];
            if ($isPost) {
                $options[CURLOPT_POST] = true;
                //传递一个数组到CURLOPT_POSTFIELDS，cURL会把数据编码成 multipart/form-data，
                //传递一个URL-encoded字符串到CURLOPT_POSTFIELDS时，数据会被编码成 application/x-www-form-urlencoded
                //传递二维数组给 CURLOPT_POSTFIELDS 时，cURL 无法正确处理，会导致数据丢失或格式错误
                $options[CURLOPT_POSTFIELDS] = $param;
            }
            curl_setopt_array($ch, $options);
            $res = curl_exec($ch);
            $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            if ($code !== 200 || $res === false) {
                throw new Exception("获取失败，HTTP 代码: " . $res);
            }
            file_put_contents($tempFile, $res);
            //执行并捕获输出
            ob_start();
            include $tempFile;
            $output = ob_get_clean();
            //清理
            unlink($tempFile);
            return $output;
        } catch (Throwable $t) {
            unlink($tempFile);
            throw new Exception($t->getMessage());
        }
    }

    /**
     * @throws Exception
     */
    public static function proc(string $url, array $options = []): string
    {
        //id strict
        $id = KcHelper::uuid4(true);
        $url .= (!str_contains($url, '?') ? '?' : '&') . 'id=' . $id;
        //请求选项 https://www.php.net/manual/zh/context.http.php
        $contextOptions = [
            'http' => [
                'method' => $options['method'] ?? 'GET',
                'header' => implode("\r\n", $options['header'] ?? []),
                'content' => $options['data'] ?? '',
                'timeout' => $options['timeout'] ?? 30,
                'ignore_errors' => true
            ],
            'ssl' => [
                'verify_peer' => $options['verify_ssl'] ?? false,
                'verify_peer_name' => $options['verify_ssl'] ?? false,
            ]
        ];
        $context = stream_context_create($contextOptions);
        $res = file_get_contents($url, false, $context);
        // 描述符：0=>标准输入, 1=>标准输出, 2=>标准错误
        $desc = [
            0 => ["pipe", "r"], // 标准输入
            1 => ["pipe", "w"], // 标准输出
            2 => ["pipe", "w"]  // 标准错误
        ];
        // 启动PHP进程 新进程无法访问当前变量
        $process = proc_open('php', $desc, $pipes);
        if (!is_resource($process)) {
            throw new Exception("无法启动 PHP 进程");
        }
        // 写入 PHP 进程的标准输入
        fwrite($pipes[0], $res);
        fclose($pipes[0]);
        // 读取输出
        $output = stream_get_contents($pipes[1]);
        fclose($pipes[1]);
        // 读取错误
        $errors = stream_get_contents($pipes[2]);
        fclose($pipes[2]);
        // 关闭进程
        $returnValue = proc_close($process);
        if ($returnValue !== 0) {
            throw new Exception("PHP 执行错误: " . $errors);
        }
        return $output;
    }
}