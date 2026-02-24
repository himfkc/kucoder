import request from '@/utils/request'
import { parseStrEmpty } from "@/utils/ruoyi";
import { basePath } from '@/api/kucoder/index'

// 查询用户列表
export function listUser(query) {
  return request({
    url: basePath + '/system/user/index',
    method: 'get',
    params: query
  })
}

// 查询用户详细
/* export function getUser(userId) {
  return request({
    url: basePath + '/system/user/' + parseStrEmpty(userId),
    method: 'get'
  })
} */

// 查看用户详情
export function getUserInfo(id='') {
  const data = id ? { id } : {}
  return request({
    url: basePath + '/system/user/info',
    method: 'post',
    data: data
  })
}

// 新增用户
export function addUser(data) {
  return request({
    url: basePath + '/system/user/add',
    method: 'post',
    data: data
  })
}

// 修改用户
export function updateUser(data) {
  return request({
    url: basePath + '/system/user/edit',
    method: 'post',
    data: data
  })
}

// 删除用户
export function delUser(data) {
  return request({
    url: basePath + '/system/user/delete',
    method: 'post',
    data
  })
}

// 用户密码重置
export function resetUserPwd(userId, password) {
  const data = {
    userId,
    password
  }
  return request({
    url: basePath + '/system/user/resetPwd',
    method: 'post',
    data: data
  })
}

// 用户状态修改
export function change(data) {
  return request({
    url: basePath + '/system/user/edit',
    method: 'post',
    data: {...data,edit_status:1}
  })
}

// 查询用户个人信息
/* export function getUserProfile() {
  return request({
    url: basePath + '/system/user/profile',
    method: 'get'
  })
} */

// 修改用户个人信息
export function updateUserProfile(data) {
  return request({
    url: basePath + '/system/user/updateProfile',
    method: 'post',
    data: data
  })
}

// 用户密码重置
export function updateUserPwd(oldPassword, newPassword,confirmPassword) {
  const data = {
    old_password:oldPassword,
    new_password:newPassword,
    confirm_password:confirmPassword
  }
  return request({
    url: basePath + '/system/user/updatePwd',
    method: 'post',
    data: data
  })
}

// 用户头像上传
export function uploadAvatar(data) {
  return request({
    url: basePath + '/system/user/uploadAvatar',
    method: 'post',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    data: data
  })
}

// 查询授权角色
export function getAuthRole(userId) {
  return request({
    url: basePath + '/system/user/authRole/' + userId,
    method: 'get'
  })
}

// 保存授权角色
export function updateAuthRole(data) {
  return request({
    url: basePath + '/system/user/authRole',
    method: 'put',
    params: data
  })
}

// 查询部门下拉树结构
export function deptTreeSelect() {
  return request({
    url: basePath + '/system/user/deptTree',
    method: 'get'
  })
}

// 彻底删除
export function trueDel(data) {
  return request({
    url: basePath + '/system/user/trueDel',
    method: 'post',
    data: data
  })
}


