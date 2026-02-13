import request from '@/utils/request'
import { basePath } from '@/api/kucoder/index'

// 查询配置分组列表
export function list(query) {
  return request({
    url: basePath + '/system/config/configGroup/index',
    method: 'get',
    params: query
  })
}

// 新增配置分组
export function add(data) {
  return request({
    url: basePath + '/system/config/configGroup/add',
    method: 'post',
    data
  })
}

// 修改配置分组
export function update(data) {
  return request({
    url: basePath + '/system/config/configGroup/edit',
    method: 'post',
    data
  })
}

// 删除配置分组
export function del(data) {
  return request({
    url: basePath + '/system/config/configGroup/delete',
    method: 'post',
    data
  })
}

// 彻底删除配置分组
export function trueDel(data) {
  return request({
    url: basePath + '/system/config/configGroup/trueDel',
    method: 'post',
    data
  })
}
