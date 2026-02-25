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

class PortManager
{
    /**
     * @var int 端口号
     */
    private int $port;
    private string $host;

    public function __construct(int $port, string $host = 'localhost')
    {
        $this->port = $port;
        $this->host = $host;
    }

    /**
     * 检查端口是否被占用
     */
    public function isInUse(): bool
    {
        $socket = @fsockopen($this->host, $this->port, $errno, $errstr, 1);
        if ($socket) {
            fclose($socket);
            return true;
        }
        return false;
    }

    /**
     * 关闭端口
     */
    public function close(): array
    {
        if (!$this->isInUse()) {
            return ['success' => true, 'message' => "端口 {$this->port} 未被占用"];
        }
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            return $this->closeWindows();
        } else {
            return $this->closeUnix();
        }
    }

    private function closeWindows(): array
    {
        $result = ['killed_pids' => []];
        exec("netstat -ano | findstr :{$this->port}", $output);
        foreach ($output as $line) {
            if (preg_match('/LISTENING\s+(\d+)$/', $line, $matches)) {
                $pid = $matches[1];
                exec("taskkill /F /PID {$pid} 2>nul");
                $result['killed_pids'][] = $pid;
            }
        }
        $result['success'] = !empty($result['killed_pids']);
        $result['message'] = $result['success'] ? "成功关闭端口 {$this->port}" : "无法找到占用端口 {$this->port} 的进程";
        return $result;
    }

    private function closeUnix(): array
    {
        $result = ['killed_pids' => []];
        // 使用 lsof 查找进程
        exec("lsof -ti:{$this->port} 2>/dev/null", $output);
        foreach ($output as $pid) {
            if (is_numeric($pid)) {
                exec("kill -9 {$pid} 2>/dev/null");
                $result['killed_pids'][] = $pid;
            }
        }
        $result['success'] = !empty($result['killed_pids']);
        $result['message'] = $result['success'] ? "成功关闭端口 {$this->port}" : "无法找到占用端口 {$this->port} 的进程";
        return $result;
    }

    /**
     * 等待端口释放
     */
    public function waitForRelease($timeout = 10): bool
    {
        $start = time();
        while ($this->isInUse() && (time() - $start) < $timeout) {
            sleep(1);
        }
        return !$this->isInUse();
    }

    /*
     // 使用示例
        $portManager = new PortManager(port);
        $result = $portManager->close();
        if ($result['success']) {
            echo "端口 9527 关闭成功\n";
            if ($portManager->waitForRelease()) {
                echo "端口已完全释放\n";
            }
        } else {
            echo "关闭失败: " . $result['message'] . "\n";
        }
     */
}