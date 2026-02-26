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


namespace kucoder\traits;

use support\exception\BusinessException;
use support\Response;
use Exception;
use support\think\Model;
use think\Collection;

trait ResponseTrait
{
    public function ok(string $msg = '', array|Model|Collection|null $data = [], ?int $code = null): Response
    {
        if (!$code) {
            $code = config('plugin.kucoder.app.success_code');
        }
        $responseData = [
            'msg' => $msg,
            'data' => $data,
            'code' => $code
        ];
        return json($responseData);
    }

    public function error(string $msg = '', array|Model|Collection|null $data = [], ?int $code = null): Response
    {
        if (!$code) {
            $code = config('plugin.kucoder.app.error_code');
        }
        $msg = $this->uniqueError($msg);
        $responseData = [
            'msg' => $msg,
            'data' => $data,
            'code' => $code
        ];
        return json($responseData);
    }

    public function success(string $msg = '', array|Model|Collection|null $data = [], ?int $code = null): Response
    {
        return $this->ok($msg, $data, $code);
    }

    public function throw(string $msg='', ?int $code = null,?bool $checkMsg = false): void
    {
        if (!$code) {
            $code = config('plugin.kucoder.app.error_code');
        }
        $msg = $checkMsg ? $this->uniqueError($msg) : $msg;
        throw new BusinessException($msg, $code);
    }

    public function errorInfo(\Throwable $t): bool|string
    {
        return json_encode([
            'msg' => $t->getMessage(),
            'code' => $t->getCode(),
            'file' => $t->getFile(),
            'line' => $t->getLine(),
            'trace' => $t->getTraceAsString(),
        ], JSON_UNESCAPED_UNICODE);
    }

    public function uniqueError(string $msg = ''): string
    {
        if ($msg && str_starts_with($msg, 'SQLSTATE[23000]') && str_contains($msg, '1062 Duplicate entry')) {
            $fieldVal = preg_replace("/^SQLSTATE\[23000\](.*)Duplicate entry '(.*?)'(.*)/", '$2', $msg);
            $msg = "数据冲突 数据库中已存在字段值为{$fieldVal}的冲突数据";
        }
        return $msg;
    }
}