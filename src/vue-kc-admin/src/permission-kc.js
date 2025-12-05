import router from './router'
import { ElMessage } from 'element-plus'
import NProgress from 'nprogress'
import 'nprogress/nprogress.css'
import { getToken } from '@/utils/auth'
import { isHttp, isPathMatch } from '@/utils/validate'
import useUserStore from '@/store/modules/user'
import useSettingsStore from '@/store/modules/settings'
import usePermissionStore from '@/store/modules/permission'

NProgress.configure({ showSpinner: false })

const whiteList = ['/login', '/register', '/logout']

const isWhiteList = (path) => {
  return whiteList.some(pattern => isPathMatch(pattern, path))
}

let routeAdded = false
router.beforeEach((to, from, next) => {
  const userStore = useUserStore()
  NProgress.start()
  if (userStore.token) {
    to.meta.title && useSettingsStore().setTitle(to.meta.title)
    /* has token*/
    if (to.path === '/login') {
      next({ path: '/' })
      NProgress.done()
    } else if (isWhiteList(to.path)) {
      next()
    } else {

      console.log('是否含有此动态路由', router.hasRoute(to.name), to)
      if (!routeAdded) {
        console.log('动态路由还未添加上')
        // 动态添加可访问路由表
        userStore.userRoutes.forEach(route => {
          // 仅当路由有 component 或子路由时才添加
          /* if (route.component || (route.children && route.children.length > 0)) {
            router.addRoute(route)
          } */
          router.addRoute(route)
        })
        routeAdded = true
        // router.replace(router.currentRoute.value.fullPath)
        // 确保addRoutes已完成
        next({
          ...to,  // next({ ...to })的目的,是保证路由添加完了再进入页面 (可以理解为重进一次)
          replace: true // 重进一次, 不保留重复历史
        })
      } else {
        next()
        // next({ path: to.fullPath })  //会导致无限重定向
        // next({ ...to, replace: true })
      }



    }
  } else {
    // 没有token
    if (isWhiteList(to.path)) {
      // 在免登录白名单，直接进入
      next()
    } else {
      next(`/login?redirect=${to.fullPath}`) // 否则全部重定向到登录页
      NProgress.done()
    }
  }
})

router.afterEach(() => {
  NProgress.done()
})
