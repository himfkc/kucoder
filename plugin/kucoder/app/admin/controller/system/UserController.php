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

use plugin\kucoder\app\admin\model\User;
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
    protected array $orderBy = ['user_id' => 'asc'];

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

    protected function info_before(&$data):void
    {
        $id = isset($data['id']) ? $data['id'] : '';
        $user_id = isset($data['user_id']) ? $data['user_id'] : '';
        if(!$id && !$user_id){
            $data['user_id'] = $this->auth->getId();
        }
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

    public function updateProfile(): Response
    {
        $uid = $this->auth->getId();
        $data = $this->request->post();
        //验证手机号
        if (isset($data['mobile']) && $data['mobile']) {
            if(!preg_match('/^1[3-9]\d{9}$/',$data['mobile'])){
                return $this->error('手机号格式错误');
            }
        }
        //验证邮箱
        if (isset($data['email']) && $data['email']) {
            if(!preg_match('/^[a-zA-Z0-9_-]+@[a-zA-Z0-9_-]+(\.[a-zA-Z0-9_-]+)+$/',$data['email'])){
                return $this->error('邮箱格式错误');
            }
        }
        $data['user_id'] = $uid;
        $this->model->where('user_id', $uid)->update($data);
        return $this->success();
    }

    /**
     * 修改密码
     */
    public function updatePwd(): Response
    {
        $oldPassword = $this->request->post('old_password');
        $newPassword = $this->request->post('new_password');
        $confirmPassword = $this->request->post('confirm_password');

        // 验证参数
        if (empty($oldPassword) || empty($newPassword) || empty($confirmPassword)) {
            return $this->error('参数不能为空');
        }

        // 验证新密码长度
        if (strlen($newPassword) < 6) {
            return $this->error('新密码长度不能少于6位');
        }

        // 验证两次密码是否一致
        if ($newPassword !== $confirmPassword) {
            return $this->error('两次密码输入不一致');
        }

        $uid = $this->auth->getId();
        // 获取当前用户信息
        $user = User::where('user_id', $uid)->find();
        if (!$user) {
            return $this->error('用户不存在');
        }

        // 验证旧密码
        if (!password_verify($oldPassword, $user->password)) {
            return $this->error('旧密码错误');
        }

        // 加密新密码
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

        // 更新密码
        $user->password = $hashedPassword;
        $result = $user->save();
        if ($result) {
            return $this->success('密码修改成功，请重新登录');
        }

        return $this->error('密码修改失败');
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

    /**
     * 上传头像
     */
     public function uploadAvatar():Response 
     {
        try{
           $uploadedPath = \plugin\kucoder\app\kucoder\lib\KcFile::upload();
           $url = $uploadedPath['avatarfile']['url'];
           $model = $this->model->find($this->auth->getId());
           $model->avatar = $url;
           $model->save(); 
           return $this->ok('',$uploadedPath);
        }catch(\Throwable $t){
            return $this->error('上传头像失败:'.$t->getMessage());
        }
        
     }


}