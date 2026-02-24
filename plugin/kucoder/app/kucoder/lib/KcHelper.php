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

use Exception;
use InvalidArgumentException;
use plugin\kucoder\app\kucoder\constants\KcConst;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use support\Response;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;
use Throwable;

class KcHelper
{
    public static function isLocal(): bool
    {
        $host = request()->host(true);
        kc_dump('请求host', $host);
        kc_dump('sys_host', parse_url(config('plugin.kucoder.app.sys_url'), PHP_URL_HOST));
        if ('localhost' === $host) $host = '127.0.0.1';
        return $host === parse_url(config('plugin.kucoder.app.sys_url'), PHP_URL_HOST);
    }

    /**
     * 生成UUID
     * @param bool $withoutDashes 是否不包含连字符
     * @return string
     */
    public static function uuid(bool $withoutDashes = false): string
    {
        $uuid = sprintf(
            '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000, // UUID版本 4
            mt_rand(0, 0x3fff) | 0x8000, // UUID 变体
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff)
        );
        return $withoutDashes ? str_replace('-', '', $uuid) : $uuid;
    }

    public static function uuid4(bool $withoutDashes = false, bool $asString = true): string
    {
        $uuid = $asString ? Uuid::uuid4()->toString() : Uuid::uuid4()->getBytes();
        return $withoutDashes ? str_replace('-', '', $uuid) : $uuid;
    }

    public static function uuid5(
        UuidInterface|string $ns, string $name, bool $withoutDashes = false): string
    {
        $uuid = Uuid::uuid5($ns, $name)->toString();
        return $withoutDashes ? str_replace('-', '', $uuid) : $uuid;
    }

    /**
     * @throws Exception
     */
    public static function isValidUuid(string $uuidString): bool
    {
        try {
            if (!str_contains($uuidString, '-')) {
                // 检查长度是否为32位
                if (strlen($uuidString) !== 32 || !ctype_xdigit($uuidString)) {
                    return false;
                }
                // 重新插入连字符以验证
                $uuidWithDashes = substr($uuidString, 0, 8) . '-' .
                    substr($uuidString, 8, 4) . '-' .
                    substr($uuidString, 12, 4) . '-' .
                    substr($uuidString, 16, 4) . '-' .
                    substr($uuidString, 20, 12);
                return Uuid::isValid($uuidWithDashes);
            }
            return Uuid::isValid($uuidString);
        } catch (Throwable $t) {
            throw new Exception($t->getMessage());
        }
    }

    /**
     * 生成指定长度和类型的随机字符串
     *
     * @param int $length 字符串长度（部分类型如md5/sha1会忽略此参数，使用固定长度）
     * @param string $type 生成类型：
     *                     - 'alpha'：仅字母（大小写）
     *                     - 'alpha_lower'：仅小写字母
     *                     - 'alpha_upper'：仅大写字母
     *                     - 'alnum'：字母+数字
     *                     - 'numeric'：仅数字
     *                     - 'special'：包含特殊字符（字母+数字+符号）
     *                     - 'md5'：32位md5哈希（忽略长度参数）
     *                     - 'sha1'：40位sha1哈希（忽略长度参数）
     * @return string 生成的随机字符串
     * @throws InvalidArgumentException|\Random\RandomException 当类型不支持或长度无效时抛出异常
     */
    public static function random(string $type = 'alnum', int $length = 12): string
    {
        // 验证长度有效性（特殊类型除外）
        if (!in_array($type, ['md5', 'sha1']) && ($length < 1 || $length > 10000)) {
            throw new InvalidArgumentException("长度必须为1-10000之间的整数");
        }

        // 定义字符集
        $charsets = [
            'alpha_lower' => 'abcdefghijklmnopqrstuvwxyz',
            'alpha_upper' => 'ABCDEFGHIJKLMNOPQRSTUVWXYZ',
            'alpha' => 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ',
            'numeric' => '0123456789',
            'alnum' => 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789',
            'special' => 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()_+-=[]{}|;:,.<>?',
        ];

        switch ($type) {
            case 'md5':
                // md5哈希（固定32位）
                return md5(uniqid((string)mt_rand(), true));
            case 'sha1':
                // sha1哈希（固定40位）
                return sha1(uniqid((string)mt_rand(), true));
            case 'alpha_lower':
            case 'alpha_upper':
            case 'alpha':
            case 'numeric':
            case 'alnum':
            case 'special':
                $charset = $charsets[$type];
                $charsetLength = strlen($charset);
                $result = '';
                // 生成指定长度的字符串
                for ($i = 0; $i < $length; $i++) {
                    // 使用更安全的随机数生成（PHP7+）
                    $randomIndex = random_int(0, $charsetLength - 1);
                    $result .= $charset[$randomIndex];
                }
                return $result;
            default:
                throw new InvalidArgumentException("不支持的类型：{$type}，支持的类型有：" . implode(', ', array_keys($charsets)) . ', md5, sha1');
        }
    }

    /**
     * @throws Exception
     */
    public static function setCookie(Response $response, array $cookieData = [], string $name = '', string $value = '', int $lifetime = 0,
                                     string   $path = '/', string $domain = '',
                                     bool     $secure = false, bool $httponly = true, string $samesite = 'lax'): Response
    {

        $session = request()->session();
        $sessionId = $session->getId();
        /** $cookieParams
         * return [
         * 'lifetime' => static::$cookieLifetime,
         * 'path' => static::$cookiePath,
         * 'domain' => static::$domain,
         * 'secure' => static::$secure,
         * 'httponly' => static::$httpOnly,
         * 'samesite' => static::$sameSite,
         * ];
         */
        $cookieParams = $session->getCookieParams();
        $cookies = [
            'name' => $name ?: config('session.session_name'),
            'value' => $value ?: $sessionId,
            'path' => $path ?: $cookieParams['path'],
            'lifetime' => $lifetime ?: $cookieParams['lifetime'],
            'domain' => $domain ?: $cookieParams['domain'],
            'secure' => $secure ?? $cookieParams['secure'],
            'httponly' => $httponly,
            'samesite' => $samesite,
        ];
        kc_dump('response设置cookies:', $cookies);
        return $response->withoutHeader('Set-Cookie')->cookie(name: $cookies['name'], value: $cookies['value'], maxAge: $cookies['lifetime'],
            path: $cookies['path'], domain: $cookies['domain'], secure: $cookies['secure'],
            httpOnly: $cookies['httponly'], sameSite: $cookies['samesite']);
    }

    /**
     * @throws Exception
     */
    public static function setConfig(string $file, string $key, mixed $value): void
    {
        $content = file_get_contents($file);
        $value = match (gettype($value)) {
            'string' => "'" . $value . "'",
            'array' => '[' . implode(',', $value) . ']',
            default => $value
        };
        $newContent = preg_replace(
            "/('{$key}'\\s*=>\\s*)([^,]+)/",
            '${1}' . $value,
            $content
        );
        file_put_contents($file, $newContent);
        // 清除缓存
        if (function_exists('opcache_invalidate')) {
            opcache_invalidate($file, true);
        }
        clearstatcache(true, $file);
    }

    /**
     * @throws Exception
     */
    public static function imageToBase64($imagePath): array
    {
        // 检查文件是否存在
        if (!file_exists($imagePath)) {
            throw new Exception("文件不存在: " . $imagePath);
        }
        // 获取图片MIME类型
        $mimeType = mime_content_type($imagePath);
        // 读取图片内容并编码
        $imageData = file_get_contents($imagePath);
        $base64 = base64_encode($imageData);
        return [
            'mime_type' => $mimeType,
            'base64' => $base64,
            'base64_url' => 'data:' . $mimeType . ';base64,' . $base64
        ];
    }

    /*    public static function setEnv(string $env_file, array $newData): void
        {
            $env_arr = file($env_file, FILE_IGNORE_NEW_LINES);
            $new_lines = [];
            foreach ($env_arr as $line) {
                $trimmedLine = trim($line);
                if (str_starts_with($trimmedLine, '#') || empty($trimmedLine)) {
                    $new_lines[] = $line;
                    continue; // 跳过注释行和空行
                }
                if (str_contains($trimmedLine, '=')) {
                    [$key, $value] = explode('=', $trimmedLine, 2);
                    $key = trim($key);
                    $value = trim($value);
                    if (array_key_exists($key, $newData)) {
                        $new_lines[] = $key . '=' . $newData[$key];
                    } else {
                        $new_lines[] = $line;
                    }
                } else {
                    $new_lines[] = $line;
                }
            }
            file_put_contents($env_file, implode("\n", $new_lines));
        }*/

    public static function runCommand(string|array $command, string $workDir = '', bool $realtimeOutput = false, bool $output = false, ?int $timeout = null): string|bool
    {
        if (is_string($command)) {
            if (!str_contains($command, ' ')) {
                throw new InvalidArgumentException(KcConst::INVALID_COMMAND_PARAMS);
            }
            $command = explode(' ', $command);
        }
        $command = array_map('trim', $command);
        if (!in_array($command[0], KcConst::ALLOWED_COMMANDS)) {
            throw new InvalidArgumentException(KcConst::INVALID_COMMAND . "：{$command[0]}");
        }
        if (!$workDir) {
            $workDir = base_path();
        }
        if (!is_dir($workDir)) {
            throw new InvalidArgumentException(KcConst::INVALID_WORK_DIR . "：{$workDir}");
        }
        $process = new Process($command);
        $process->setTimeout($timeout);
        $process->setWorkingDirectory($workDir);
        try {
            if (!$realtimeOutput) {
                $process->run();
            } else {
                $process->run(function ($type, $buffer) {
                    echo KcConst::KC_COMMAND . $buffer;
                    if (ob_get_level() > 0) {
                        ob_flush();
                    }
                    flush();
                });
            }
            if (!$process->isSuccessful()) {
                throw new ProcessFailedException($process);
            }
            return $output ? $process->getOutput() : true;
        } catch (ProcessFailedException $e) {
            $errorOutput = $process->getErrorOutput();
            kc_dump('command error:', $errorOutput);
            throw new ProcessFailedException($process);
        }
    }

    public static function generateRelyBackup(string $plugin, string $dependencies_file, string $dependencies_file_bak = ''): void
    {
        $composer = json_decode(file_get_contents(base_path('composer.json')), true);
        $packagePath = kc_path(base_path(), KcConst::VUE_KC_ADMIN, '/package.json');
        $package = json_decode(file_get_contents($packagePath), true);
        $backup = [
            'require' => $composer['require'],
            'require-dev' => isset($composer['require-dev']) ? $composer['require-dev'] : [],
            'dependencies' => $package['dependencies'],
            'devDependencies' => $package['devDependencies'],
        ];
        $backup = json_encode($backup, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        file_put_contents(base_path("/plugin/{$plugin}/zinfo/rely_kucoder.json"), $backup);
        copy($dependencies_file, $dependencies_file_bak ?: $dependencies_file . '.bak');
    }

    public static function checkRelyBackup(string $plugin, string $c): bool
    {
        $backup = json_decode(file_get_contents(base_path("/plugin/{$plugin}/zinfo/rely_kucoder.json")), true);
        $rely = explode(' ', $c)[2];
        if (str_starts_with($c, 'composer') && (isset($backup['require'][$rely]) || isset($backup['require-dev'][$rely]))) {
            return false;
        } else if (str_starts_with($c, 'pnpm') && (isset($backup['dependencies'][$rely]) || isset($backup['devDependencies'][$rely]))) {
            return false;
        }
        return true;
    }


}