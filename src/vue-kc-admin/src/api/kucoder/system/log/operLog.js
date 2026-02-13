import request from '@/utils/request'
import { basePath } from '@/api/kucoder/index'

// 查询操作日志列表
export function list(query) {
  return request({
    url: basePath + '/system/log/operLog/index',
    method: 'get',
    params: query
  })
}

// 删除操作日志
export function delOperLog(id) {
  return request({
    url: basePath + '/system/log/operLog/delete',
    method: 'post',
    data: { id }
  })
}

// 清空操作日志
export function cleanOperLog() {
  return request({
    url: basePath + '/system/log/operLog/clean',
    method: 'post'
  })
}
