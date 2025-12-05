import request from '@/utils/request'
import { parseStrEmpty } from "@/utils/ruoyi";
import { basePath } from '@/api/member/index'

// 查询用户列表
export function listMember(query) {
    return request({
        url: basePath + '/member/index',
        method: 'get',
        params: query
    })
}

// 查询用户详细
export function getMember(memberId) {
    return request({
        url: basePath + '/member/' + parseStrEmpty(memberId),
        method: 'get'
    })
}

// 新增用户
export function addMember(data) {
    return request({
        url: basePath + '/member/add',
        method: 'post',
        data: data
    })
}

// 修改用户
export function updateMember(data) {
    return request({
        url: basePath + '/member/edit',
        method: 'post',
        data: data
    })
}

// 删除用户
export function delMember(data) {
    return request({
        url: basePath + '/member/delete',
        method: 'post',
        data
    })
}

// 用户密码重置
export function resetMemberPwd(memberId, password) {
    const data = {
        memberId,
        password
    }
    return request({
        url: basePath + '/member/resetPwd',
        method: 'post',
        data: data
    })
}

// 用户状态修改
export function change(data) {
    return request({
        url: basePath + '/member/change',
        method: 'post',
        data: data
    })
}

// 查询用户个人信息
export function getMemberProfile() {
    return request({
        url: basePath + '/member/profile',
        method: 'get'
    })
}

// 修改用户个人信息
export function updateMemberProfile(data) {
    return request({
        url: basePath + '/member/profile',
        method: 'put',
        data: data
    })
}

// 用户密码重置
export function updateMemberPwd(oldPassword, newPassword) {
    const data = {
        oldPassword,
        newPassword
    }
    return request({
        url: basePath + '/member/profile/updatePwd',
        method: 'put',
        data: data
    })
}

// 用户头像上传
export function uploadAvatar(data) {
    return request({
        url: basePath + '/member/profile/avatar',
        method: 'post',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        data: data
    })
}

// 查询授权角色
export function getAuthRole(memberId) {
    return request({
        url: basePath + '/member/authRole/' + memberId,
        method: 'get'
    })
}

// 保存授权角色
export function updateAuthRole(data) {
    return request({
        url: basePath + '/member/authRole',
        method: 'put',
        params: data
    })
}
