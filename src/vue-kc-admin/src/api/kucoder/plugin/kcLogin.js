import request from '@/utils/request'
import { basePath } from '@/api/kucoder/index'

// 登录kucoder
export function loginKc(data) {
    return request({
        url: basePath + '/plugin/kcLogin/login',
        method: 'post',
        data: data
    })
}

export function logoutKc(data) {
    return request({
        url: basePath + '/plugin/kcLogin/logout',
        method: 'post',
        data: data
    })
}

// 登录验证码
export function getCodeImg() {
    return request({
        url: basePath + '/plugin/kcLogin/getCodeImg',
        method: 'post',
    })
}