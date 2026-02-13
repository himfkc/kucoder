import request from '@/utils/request'
import { basePath } from '@/api/kucoder/index'

export function list(query) {
    return request({
        url: basePath + '/plugin/pluginLocal/index',
        method: 'get',
        params: query
    })
}
export function install(data, options = {}) {
    return request({
        url: basePath + '/plugin/pluginLocal/install',
        method: 'post',
        data: data,
        ...options
    })
}
export function uninstall(data, options = {}) {
    return request({
        url: basePath + '/plugin/pluginLocal/uninstall',
        method: 'post',
        data: data,
        ...options
    })
}
export function update(data, options = {}) {
    return request({
        url: basePath + '/plugin/pluginLocal/update',
        method: 'post',
        data: data,
        ...options
    })
}
export function importPlugin(data, options = {}) {
    return request({
        url: basePath + '/plugin/pluginLocal/importLocalPlugin',
        method: 'post',
        data: data,
        ...options
    })
}

// 彻底删除
export function trueDel(data) {
    return request({
        url: basePath + '/plugin/pluginLocal/trueDel',
        method: 'post',
        data: data
    })
}


