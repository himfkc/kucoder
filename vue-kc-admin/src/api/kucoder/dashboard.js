import request from '@/utils/request'
import { basePath } from '@/api/kucoder/index'

// 获取首页统计数据
export function getDashboardStats() {
    return request({
        url: basePath + '/dashboard/stats',
        method: 'get'
    })
}

// 获取访问趋势数据
export function getVisitTrend(data) {
    return request({
        url: basePath + '/dashboard/visitTrend',
        method: 'get',
        params: data
    })
}

// 获取用户增长数据
export function getUserGrowth(data) {
    return request({
        url: basePath + '/dashboard/userGrowth',
        method: 'get',
        params: data
    })
}

// 获取订单数据
export function getOrderData(data) {
    return request({
        url: basePath + '/dashboard/orderData',
        method: 'get',
        params: data
    })
}

// 获取系统状态
export function getSystemStatus() {
    return request({
        url: basePath + '/dashboard/systemStatus',
        method: 'get'
    })
}

// 获取快捷操作列表
export function getQuickActions() {
    return request({
        url: basePath + '/dashboard/quickActions',
        method: 'get'
    })
}

// 获取最新动态
export function getRecentActivities() {
    return request({
        url: basePath + '/dashboard/recentActivities',
        method: 'get'
    })
}

// 获取待办事项
export function getTodoList() {
    return request({
        url: basePath + '/dashboard/todoList',
        method: 'get'
    })
}
