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


namespace plugin\kucoder\app\admin\controller\system\log;

use kucoder\controller\AdminBase;
use kucoder\model\LoginLog;
use support\Response;

/**
 * 登录日志控制器类
 */
class LoginLogController extends AdminBase
{
    protected string $modelClass = "\\plugin\\kucoder\\app\\kucoder\\model\\LoginLog";
    protected array $allowAccessActions = ['index', 'delete'];

    // 默认排序：按创建时间倒序
    protected array $orderBy = ['create_time' => 'desc'];

    /**
     * 登录日志列表
     * @return Response
     */
    public function index(): Response
    {
        return parent::index();
    }

    /**
     * 删除登录日志
     * @return Response
     */
    public function delete(): Response
    {
        return parent::delete();
    }

    /**
     * 清空登录日志
     * @return Response
     */
    public function clean(): Response
    {
        try {
            $this->checkActionAccess();

            // 清空所有登录日志（非软删除，真正删除）
            $count = LoginLog::count();
            LoginLog::where('1=1')->delete();

            $this->_log(1, '清空登录日志，共删除 ' . $count . ' 条记录');

            return $this->success('清空成功', [
                'count' => $count
            ]);
        } catch (\Throwable $t) {
            return $this->error('清空失败：' . $t->getMessage());
        }
    }
}
