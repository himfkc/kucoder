import request from '@/utils/request'
import { basePath } from '@/api/develop/index'

// 查询列表
export function indexList(data) {
    return request({
        url: basePath + '/develop/index',
        method: 'get',
        params: data
    })
}
// 新增
export function add(data) {
    return request({
        url: basePath + '/develop/add',
        method: 'post',
        data: data
    })
}
// 修改
export function edit(data = {}) {
    return request({
        url: basePath + '/develop/edit',
        method: 'post',
        data: data
    })
}
// 删除
export function del(data) {
    return request({
        url: basePath + '/develop/delete',
        method: 'post',
        data: data
    })
}
// 字段修改
export function change(data) {
    return request({
        url: basePath + '/develop/change',
        method: 'post',
        data: data
    })
}

export function updatePluginStatus(data) {
    return request({
        url: basePath + '/develop/updatePluginStatus',
        method: 'post',
        data: data
    })
}

// 发布到插件市场
export function publish(data) {
    return request({
        url: basePath + '/develop/publishToRemote',
        method: 'post',
        data: data
    })
}

export function rePublish(data) {
    return request({
        url: basePath + '/develop/rePublish',
        method: 'post',
        data: data
    })
}

export function pluginPack(data) {
    return request({
        url: basePath + '/develop/package',
        method: 'post',
        data: data
    })
}

// 撤销发布
export function cancelPublish(data) {
    return request({
        url: basePath + '/develop/cancelPublish',
        method: 'post',
        data: data
    })
}




