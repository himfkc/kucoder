import request from '@/utils/request'
import { basePath } from '@/api/kucoder/index'

// 查询登录日志列表
export function list(query) {
  return request({
    url: basePath + '/system/log/loginLog/index',
    method: 'get',
    params: query
  })
}

// 删除登录日志
export function delLoginLog(id) {
  return request({
    url: basePath + '/system/log/loginLog/delete',
    method: 'post',
    data: { id }
  })
}

// 清空登录日志
export function cleanLoginLog() {
  return request({
    url: basePath + '/system/log/loginLog/clean',
    method: 'post'
  })
}
