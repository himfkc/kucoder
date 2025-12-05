import request from '@/utils/request'
import { basePath } from '@/api/pluginName/index'

// 查询用户列表
export function list(query) {
    return request({
        url: basePath + '[/dir]/controllerName/index',
        method: 'get',
        params: query
    })
}

// 新增用户
export function add(data) {
    return request({
        url: basePath + '[/dir]/controllerName/add',
        method: 'post',
        data: data
    })
}

// 修改用户
export function edit(data) {
    return request({
        url: basePath + '[/dir]/controllerName/edit',
        method: 'post',
        data: data
    })
}

// 删除用户
export function del(data) {
    return request({
        url: basePath + '[/dir]/controllerName/delete',
        method: 'post',
        data
    })
}

// 用户状态修改
export function change(data) {
    return request({
        url: basePath + '[/dir]/controllerName/change',
        method: 'post',
        data: data
    })
}
