import request from '@/utils/request'
import { basePath } from '@/api/kucoder/index'

// 获取所有配置数据（包含插件、分组和配置项）
export function getIndex() {
  return request({
    url: basePath + '/system/config/config/index',
    method: 'get'
  })
}

// 刷新参数缓存
export function refreshCache() {
  return request({
    url: basePath + '/system/config/config/refreshCache',
    method: 'post'
  })
}

// 编辑保存指定插件的配置
export function edit(plugin, groupId, data) {
  return request({
    url: basePath + '/system/config/config/edit',
    method: 'post',
    data: { plugin, group_id: groupId, ...data }
  })
}

// 新增配置项
export function add(data) {
  return request({
    url: basePath + '/system/config/config/add',
    method: 'post',
    data
  })
}

// 删除配置项
export function deleteConfig(data) {
  return request({
    url: basePath + '/system/config/config/delete',
    method: 'post',
    data
  })
}
