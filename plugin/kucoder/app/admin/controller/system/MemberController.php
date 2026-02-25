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


namespace plugin\kucoder\app\admin\controller\system;

use kucoder\controller\AdminBase;
use plugin\kucoder\app\admin\model\Member as KcMember;
use support\Request;
use support\Response;
use Throwable;

class MemberController extends AdminBase
{
    //允许访问父类的方法
    protected array $allowAccessActions = ['index', 'add', 'edit', 'delete'];

    //模型类
    protected string $modelClass = KcMember::class;

    //查询排除字段
    protected string|array $withoutField = 'password';

    /**
     * 获取会员详情
     * @param Request $request
     * @return Response
     */
    public function info(): Response
    {
        try {
            $request = $this->request;
            $id = (int)$request->get('id');

            if (!$id) {
                return $this->error('ID不能为空');
            }

            $member = KcMember::find($id);

            if (!$member) {
                return $this->error('数据不存在');
            }

            return $this->success('获取成功', $member);
        } catch (Throwable $e) {
            return $this->error('获取失败：' . $e->getMessage());
        }
    }

    /**
     * 新增前置处理 - 密码加密
     * @param array $data
     * @return void
     */
    protected function add_before(array &$data): void
    {
        if (isset($data['password']) && $data['password']) {
            if (strlen($data['password']) < 6) {
                $this->throw('密码长度不能小于6位');
            }
            $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        } else {
            $this->throw('密码不能为空');
        }
    }

    /**
     * 编辑前置处理 - 密码加密
     * @param array $data
     * @return void
     */
    protected function edit_before(array &$data): void
    {
        // 如果编辑时传了新密码，则加密密码
        if (isset($data['password']) && $data['password']) {
            if (strlen($data['password']) < 6) {
                $this->throw('密码长度不能小于6位');
            }
            $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        } else {
            // 如果没有传密码，则从编辑数据中移除，避免覆盖原有密码
            unset($data['password']);
        }
    }

    /**
     * 重置密码
     * @param Request $request
     * @return Response
     */
    public function resetPwd(Request $request): Response
    {
        try {
            $id = $request->post('m_id');
            if (!$id) {
                return $this->error('ID不能为空');
            }

            $password = $request->post('password');
            if (!$password) {
                return $this->error('请输入密码');
            }
            if (strlen($password) < 6) {
                return $this->error('密码长度不能小于6位');
            }

            $password = password_hash($password, PASSWORD_DEFAULT);
            KcMember::where('m_id', $id)->update([
                'password' => $password,
                'password_update_time' => date('Y-m-d H:i:s')
            ]);

            return $this->success('重置密码成功');
        } catch (Throwable $e) {
            return $this->error('重置密码失败：' . $e->getMessage());
        }
    }
}
