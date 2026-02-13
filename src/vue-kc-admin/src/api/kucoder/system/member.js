import request from '@/utils/request'
import { basePath } from '@/api/kucoder/index'

// 查询会员列表
export function list(query) {
  return request({
    url: basePath + '/system/member/index',
    method: 'get',
    params: query
  })
}

// 获取会员详情
export function getInfo(id) {
  return request({
    url: basePath + '/system/member/info',
    method: 'get',
    params: { id }
  })
}

// 新增会员
export function add(data) {
  return request({
    url: basePath + '/system/member/add',
    method: 'post',
    data
  })
}

// 修改会员
export function update(data) {
  return request({
    url: basePath + '/system/member/edit',
    method: 'post',
    data
  })
}

// 删除会员
export function delMember(data) {
  return request({
    url: basePath + '/system/member/delete',
    method: 'post',
    data
  })
}

// 重置密码
export function resetPwd(data) {
  return request({
    url: basePath + '/system/member/resetPwd',
    method: 'post',
    data
  })
}

// 彻底删除
export function trueDel(data) {
  return request({
    url: basePath + '/system/member/trueDel',
    method: 'post',
    data: data
  })
}
