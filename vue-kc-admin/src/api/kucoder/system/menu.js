import request from '@/utils/request'
import { basePath } from '@/api/kucoder/index'

// 查询菜单列表
export function listMenu(query) {
  return request({
    url: basePath + '/system/menu/index',
    method: 'get',
    params: query
  })
}

// 新增菜单
export function addMenu(data) {
  return request({
    url: basePath + '/system/menu/add',
    method: 'post',
    data: data
  })
}

// 修改菜单
export function updateMenu(data) {
  return request({
    url: basePath + '/system/menu/edit',
    method: 'post',
    data: data
  })
}

//更改字段状态 在请求携带的data数据中添加edit_status字段
export function change(data) {
  return request({
    url: basePath + '/system/menu/edit',
    method: 'post',
    data: {...data,edit_status:1}
  })
}

// 删除菜单
export function delMenu(data) {
  return request({
    url: basePath + '/system/menu/delete',
    method: 'post',
    data: data
  })
}



// 导出菜单
export function exportPluginMenu(query) {
  return request({
    url: basePath + '/system/menu/exportPluginMenu',
    method: 'get',
    params: query
  })
}





// 查询菜单详细  nouse
export function getMenu(menuId) {
  return request({
    url: basePath + '/system/menu/' + menuId,
    method: 'get'
  })
}

// 查询菜单下拉树结构
export function treeselect() {
  return request({
    url: basePath + '/system/menu/treeselect',
    method: 'get'
  })
}

// 根据角色ID查询菜单下拉树结构
export function roleMenuTreeselect(roleId) {
  return request({
    url: basePath + '/system/menu/roleMenuTreeselect/' + roleId,
    method: 'get'
  })
}

// 彻底删除
export function trueDel(data) {
  return request({
    url: basePath + '/system/menu/trueDel',
    method: 'post',
    data: data
  })
}