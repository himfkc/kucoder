import request from '@/utils/request'
import { basePath } from '@/api/kucoder/index'

// 登录方法
export function login(data = {}) {
  return request({
    url: basePath + '/login/login',
    headers: {
      isToken: false,
      repeatSubmit: false
    },
    method: 'post',
    data: data
  })
}

// 注册方法
export function register(data) {
  return request({
    url: basePath + '/login/register',
    headers: {
      isToken: false
    },
    method: 'post',
    data: data
  })
}

// 获取用户详细信息
export function getInfo() {
  return request({
    url: basePath + '/login/getInfo',
    method: 'get'
  })
}

// 退出方法
export function logout() {
  return request({
    url: basePath + '/login/logout',
    method: 'post'
  })
}

// 获取验证码
export function getCodeImg() {
  return request({
    url: basePath + '/login/changeCaptcha',
    headers: {
      isToken: false
    },
    method: 'get',
    timeout: 20000
  })
}

// 获取路由
export const getRouters = () => {
  return request({
    url: basePath + '/login/getRouters',
    method: 'get'
  })
}