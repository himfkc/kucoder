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

class CodeStats
{
    private float $startTime;
    private float $endTime;
    private int $startMemory;
    private int $endMemory;
    //峰值内存
    private int $peakMemory;

    /**
     * 统计代码的开始执行时间及当前使用内存
     * @return void
     */
    public function start(): void
    {
        $this->startTime = microtime(true);
        $this->startMemory = memory_get_usage();
    }

    /**
     * 统计代码的结束执行时间及当前使用内存
     * @return void
     */
    public function end(): void
    {
        $this->endTime = microtime(true);
        $this->endMemory = memory_get_usage();
        $this->peakMemory = memory_get_peak_usage();
    }

    /**
     * 获取代码执行时间和内存使用情况
     * @return array
     */
    public function stats(): array
    {
        $execution_time = $this->endTime - $this->startTime;
        $memory_used = $this->endMemory - $this->startMemory;
        return [
            // 'time_sec' => $execution_time.'秒',
            'time_ms' => round($execution_time * 1000, 2).'毫秒',
            // 'memory_bytes' => $memory_used.'字节',
            // 'memory_kb' => round($memory_used / 1024, 2).'KB',
            'memory_mb' => round($memory_used / (1024 * 1024), 4).'MB',
            // 'peak_memory_bytes' => $this->peakMemory.'字节',
            // 'peak_memory_kb' => round($this->peakMemory / 1024, 2).'KB',
            'peak_memory_mb' => round($this->peakMemory / (1024 * 1024), 4).'MB',
        ];
    }

    public function log(): void
    {
       kc_log('统计结果:'.var_export($this->stats(), true));
    }

}