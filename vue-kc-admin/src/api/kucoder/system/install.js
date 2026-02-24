import request from '@/utils/request'
import { pluginPath } from '@/api/kucoder/index'


export function envCheck(data, options = {}) {
    return request({
        url: pluginPath + '/kucoder/install/envCheck',
        method: 'post',
        data,
        ...options
    })
}

/* export function installRely(data = {}, options = {}) {
    return request({
        url: pluginPath + '/kucoder/install/installRely',
        method: 'post',
        data,
        ...options
    })
} */

export function initEnv(data, options = {}) {
    return request({
        url: pluginPath + '/kucoder/install/init',
        method: 'post',
        data,
        ...options
    })
}

export function install(data, options = {}) {
    return request({
        url: pluginPath + '/kucoder/install/install',
        method: 'post',
        data,
        ...options
    })
}

export function getQrcode(data, options = {}, method = 'post') {
    return request({
        url: pluginPath + '/kucoder/install/getQrcode',
        method,
        data,
        ...options
    })
}

export function verifyWxCode(data, options = {}, method = 'post') {
    return request({
        url: pluginPath + '/kucoder/install/verifyWxCode',
        method,
        data,
        ...options
    })
}