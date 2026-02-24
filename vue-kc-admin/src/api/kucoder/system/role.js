import request from '@/utils/request'
import { basePath } from '@/api/kucoder/index'

// 查询角色列表
export function listRole(data) {
  return request({
    url: basePath + '/system/role/index',
    method: 'get',
    params: data
  })
}
// 新增角色
export function addRole(data) {
  return request({
    url: basePath + '/system/role/add',
    method: 'post',
    data: data
  })
}
// 修改角色
export function updateRole(data) {
  return request({
    url: basePath + '/system/role/edit',
    method: 'post',
    data: data
  })
}
// 删除角色
export function delRole(data) {
  return request({
    url: basePath + '/system/role/delete',
    method: 'post',
    data: data
  })
}

// 查询角色详细
export function getRole(roleId) {
  return request({
    url: basePath + '/system/role/' + roleId,
    method: 'get'
  })
}
// 角色数据权限
export function dataScope(data) {
  return request({
    url: basePath + '/system/role/dataScope',
    method: 'put',
    data: data
  })
}

// 角色状态修改
export function change(data) {
  return request({
    url: basePath + '/system/role/edit',
    method: 'post',
    data: {...data,edit_status:1}
  })
}

// 查询角色已授权用户列表
export function allocatedUserList(query) {
  return request({
    url: basePath + '/system/role/authUser/allocatedList',
    method: 'get',
    params: query
  })
}

// 查询角色未授权用户列表
export function unallocatedUserList(query) {
  return request({
    url: basePath + '/system/role/authUser/unallocatedList',
    method: 'get',
    params: query
  })
}

// 取消用户授权角色
export function authUserCancel(data) {
  return request({
    url: basePath + '/system/role/authUser/cancel',
    method: 'put',
    data: data
  })
}

// 批量取消用户授权角色
export function authUserCancelAll(data) {
  return request({
    url: basePath + '/system/role/authUser/cancelAll',
    method: 'put',
    params: data
  })
}

// 授权用户选择
export function authUserSelectAll(data) {
  return request({
    url: basePath + '/system/role/authUser/selectAll',
    method: 'put',
    params: data
  })
}

// 根据角色ID查询部门树结构
export function deptTreeSelect(roleId) {
  return request({
    url: basePath + '/system/role/deptTree/' + roleId,
    method: 'get'
  })
}

// 彻底删除
export function trueDel(data) {
  return request({
    url: basePath + '/system/role/trueDel',
    method: 'post',
    data: data
  })
}
