import request from '@/utils/request'
import { basePath } from '@/api/kucoder/index'

// 查询部门列表
export function listDept(query) {
  return request({
    url: basePath + '/system/dept/index',
    method: 'get',
    params: query
  })
}

// 查询部门列表（排除节点）
export function listDeptExcludeChild(deptId) {
  return request({
    url: basePath + '/system/dept/list/exclude/' + deptId,
    method: 'get'
  })
}

// 查询部门详细
export function getDept(deptId) {
  return request({
    url: basePath + '/system/dept/' + deptId,
    method: 'get'
  })
}

// 新增部门
export function addDept(data) {
  return request({
    url: basePath + '/system/dept/add',
    method: 'post',
    data: data
  })
}

// 修改部门
export function updateDept(data) {
  return request({
    url: basePath + '/system/dept/edit',
    method: 'post',
    data: data
  })
}

// 删除部门
export function delDept(data) {
  return request({
    url: basePath + '/system/dept/delete',
    method: 'post',
    data: data
  })
}

export function change(data) {
  return request({
    url: basePath + '/system/dept/edit',
    method: 'post',
    data: {...data,edit_status:1}
  })
}

// 彻底删除
export function trueDel(data) {
  return request({
    url: basePath + '/system/dept/trueDel',
    method: 'post',
    data: data
  })
}