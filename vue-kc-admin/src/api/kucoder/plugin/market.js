import request from '@/utils/request'
import { basePath } from '@/api/kucoder/index'

// 查询菜单列表
export function index(query) {
    return request({
        url: basePath + '/plugin/market/index',
        method: 'get',
        params: query,
    })
}

// 购买插件
export function buy(data) {
    return request({
        url: basePath + '/plugin/market/buy',
        method: 'post',
        data
    })
}

export function payQuery(data, headers = {}) {
    return request({
        url: basePath + '/plugin/market/payQuery',
        method: 'post',
        data,
        headers
    })
}