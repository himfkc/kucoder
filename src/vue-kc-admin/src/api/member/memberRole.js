import request from '@/utils/request'
import { basePath } from '@/api/member/index'

// 查询用户列表
export function list(query) {
    return request({
        url: basePath + '/memberRole/index',
        method: 'get',
        params: query
    })
}

// 新增用户
export function add(data) {
    return request({
        url: basePath + '/memberRole/add',
        method: 'post',
        data: data
    })
}

// 修改用户
export function edit(data) {
    return request({
        url: basePath + '/memberRole/edit',
        method: 'post',
        data: data
    })
}

// 删除用户
export function del(data) {
    return request({
        url: basePath + '/memberRole/delete',
        method: 'post',
        data
    })
}

// 用户状态修改
export function change(data) {
    return request({
        url: basePath + '/memberRole/change',
        method: 'post',
        data: data
    })
}
