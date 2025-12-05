<?php
declare(strict_types=1);


namespace plugin\kucoder\app\admin\controller\system;

use plugin\kucoder\app\kucoder\controller\AdminBase;
use plugin\kucoder\app\admin\model\Role;
use plugin\kucoder\app\admin\model\Dept;
use support\Response;
use think\db\exception\DataNotFoundException;
use think\db\exception\DbException;
use think\db\exception\ModelNotFoundException;

class UserController extends AdminBase
{
    protected string $modelClass = "\\plugin\\kucoder\\app\\admin\\model\\User";
    protected array $withJoin = ['dept' => ['dept_name']];
    // protected array $with = ['role' => 'role_id,role_name'];
    protected string|array $withoutField = 'password';

    /**
     * @throws DataNotFoundException
     * @throws ModelNotFoundException
     * @throws DbException
     */
    protected function index_after(&$items)
    {
        $roles = Role::field('role_id,role_name')->select();
        $depts = Dept::field('dept_id,dept_name,parent_id')->select();
        $depts = get_recursion_data($depts, 'dept_id', 'parent_id');
        return [
            'items' => $items,
            'roles' => $roles,
            'depts' => $depts
        ];
    }

    protected function add_before(&$data):void
    {
        //用户密码处理
        if (isset($data['password']) && $data['password']) {
            if (strlen($data['password']) < 6) {
                $this->throw('密码长度不能小于6位');
            }
            $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        } else {
            unset($data['password']);
        }
    }

    public function profile()
    {

    }



    /**
     * 重置密码
     */
    public function resetPwd():Response
    {
        $id = $this->request->post('userId');
        if (!$id) {
            return $this->error('参数错误');
        }
        $password = $this->request->post('password');
        if (!$password) {
            return $this->error('请输入密码');
        }
        if (strlen($password) < 6) {
            return $this->error('密码长度不能小于6位');
        }
        $password = password_hash($password, PASSWORD_DEFAULT);
        $this->model->where('user_id', $id)->update(['password' => $password]);
        return $this->success();
    }
}