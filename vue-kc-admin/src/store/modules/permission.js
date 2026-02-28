import auth from '@/plugins/auth'
import router, { constantRoutes, dynamicRoutes } from '@/router'
import { getRouters } from '@/api/kucoder/login'
import Layout from '@/layout/index'
import ParentView from '@/components/ParentView'
import InnerLink from '@/layout/components/InnerLink'
// import { ElMessage } from 'element-plus'

import { defineStore } from 'pinia'

// 匹配views里面所有的.vue文件
const modules = import.meta.glob('./../../views/**/*.vue')

const usePermissionStore = defineStore(
  'permission',
  {
    state: () => ({
      btns: [],       //按钮鉴权
      roleMenus: [],  //角色管理的菜单权限
      source_routes: [], // 原始数据 后端返回的JSON对象，未处理
      routes: [],   // Vue Router主路由 处理后的数据  经过filterAsyncRouter处理后的路由，包含真正的Vue组件
      topbarRouters: [],  // 顶部导航
      sidebarRouters: []  // 侧边栏 + 顶部搜索 菜单
    }),
    getters: {
      btnsSet(state) {
        return new Set(state.btns)
      }
    },
    actions: {
      setRoutes(routes) {
        this.routes = constantRoutes.concat(routes)
      },
      setTopbarRoutes(routes) {
        this.topbarRouters = routes
      },
      setSidebarRouters(routes) {
        this.sidebarRouters = routes
      },
      generateRoutes() {
        return new Promise((resolve, reject) => {
          
          if (!this.routes.length) {
            console.log('没有路由了，重新请求路由数据')
            // 向后端请求路由数据
            getRouters()
              .then(({ res, msg, code }) => {
                this.source_routes = res.routes
                this.btns = res.btns
                this.roleMenus = res.roleMenus

                const rdata = JSON.parse(JSON.stringify(res.routes))
                const sdata = JSON.parse(JSON.stringify(res.routes))

                const sidebarRoutes = filterAsyncRouter(sdata)
                // rdata 被处理后变成 rewriteRoutes(会调用 filterChildren 扁平化子路由)
                const rewriteRoutes = filterAsyncRouter(rdata, false, true)

                /* const asyncRoutes = filterDynamicRoutes(dynamicRoutes)
                asyncRoutes.forEach(route => { router.addRoute(route) }) */

                // Vue Router 主路由 处理后的数据  经过 filterAsyncRouter 处理后的路由，包含真正的 Vue 组件
                this.setRoutes(rewriteRoutes)
                // 侧边栏 + 顶部搜索 菜单
                this.setSidebarRouters(constantRoutes.concat(sidebarRoutes))
                // 顶部导航
                this.setTopbarRoutes(sidebarRoutes)

                resolve(rewriteRoutes)
              })
              .catch(({ msg, code }) => {
                console.warning('获取路由失败', msg, code)
                // ElMessage.error(msg)
                reject({ msg, code })
              }
              )
          } else {
            console.log('已经有路由了，直接返回', this.source_routes)
            // 如果已经有路由了，直接返回
            const rdata = JSON.parse(JSON.stringify(this.source_routes))
            const sdata = JSON.parse(JSON.stringify(this.source_routes))

            const sidebarRoutes = filterAsyncRouter(sdata)
            const rewriteRoutes = filterAsyncRouter(rdata, false, true)

            /* const asyncRoutes = filterDynamicRoutes(dynamicRoutes)
            asyncRoutes.forEach(route => { router.addRoute(route) }) */

            this.setRoutes(rewriteRoutes)
            this.setSidebarRouters(constantRoutes.concat(sidebarRoutes))
            this.setTopbarRoutes(sidebarRoutes)
            resolve(rewriteRoutes)
          }
        })
      }
    },


    // pinia持久化存储
    persist: true
  })

// 遍历后台传来的路由字符串，转换为组件对象
function filterAsyncRouter(asyncRouterMap, lastRouter = false, type = false) {
  return asyncRouterMap.filter(route => {
    if (type && route.children) {
      route.children = filterChildren(route.children)
    }
    if (route.component) {
      // Layout ParentView 组件特殊处理
      if (route.component === 'Layout') {
        route.component = Layout
      } else if (route.component === 'ParentView') {
        route.component = ParentView
      } else if (route.component === 'InnerLink') {
        route.component = InnerLink
      } else {
        route.component = loadView(route.component)
      }
    }
    if (route.children != null && route.children && route.children.length) {
      route.children = filterAsyncRouter(route.children, route, type)
    } else {
      delete route['children']
      delete route['redirect']
    }
    return true
  })
}

function filterChildren(childrenMap, lastRouter = false) {
  var children = []
  childrenMap.forEach(el => {
    el.path = lastRouter ? lastRouter.path + '/' + el.path : el.path
    if (el.children && el.children.length && el.component === 'ParentView') {
      children = children.concat(filterChildren(el.children, el))
    } else {
      children.push(el)
    }
  })
  return children
}

// 动态路由遍历，验证是否具备权限
/* export function filterDynamicRoutes(routes) {
  const res = []
  routes.forEach(route => {
    if (route.permissions) {
      if (auth.hasPermiOr(route.permissions)) {
        res.push(route)
      }
    } else if (route.roles) {
      if (auth.hasRoleOr(route.roles)) {
        res.push(route)
      }
    }
  })
  return res
} */

export const loadView = (view) => {
  let res
  for (const path in modules) {
    const dir = path.split('views/')[1].split('/index.vue')[0]
    if (dir === view) {
      res = () => modules[path]()
    }
  }
  return res
}

export default usePermissionStore
