import { createWebHistory, createRouter } from 'vue-router'
/* Layout */
import Layout from '@/layout'

import { ElMessage } from 'element-plus'
import NProgress from 'nprogress'
import 'nprogress/nprogress.css'
// import { getToken } from '@/utils/auth'
import { isHttp } from '@/utils/validate'
import useUserStore from '@/store/modules/user'
import useSettingsStore from '@/store/modules/settings'
import usePermissionStore from '@/store/modules/permission'
import { adminBasePath } from '@/api/adminRouteBasePath'
/**
 * Note: 路由配置项
 *
 * hidden: true                     // 当设置 true 的时候该路由不会再侧边栏出现 如401，login等页面，或者如一些编辑页面/edit/1
 * alwaysShow: true                 // 当你一个路由下面的 children 声明的路由大于1个时，自动会变成嵌套的模式--如组件页面
 *                                  // 只有一个时，会将那个子路由当做根路由显示在侧边栏--如引导页面
 *                                  // 若你想不管路由下面的 children 声明的个数都显示你的根路由
 *                                  // 你可以设置 alwaysShow: true，这样它就会忽略之前定义的规则，一直显示根路由
 * 
 * redirect: noRedirect             // 当设置 noRedirect 的时候该路由在面包屑导航中不可被点击
 * name:'router-name'               // 设定路由的名字，一定要填写不然使用<keep-alive>时会出现各种问题
 * query: '{"id": 1, "name": "ry"}' // 访问路由的默认传递参数
 * roles: ['admin', 'common']       // 访问路由的角色权限 弃用
 * permissions: ['a:a:a', 'b:b:b']  // 访问路由的菜单权限 弃用
 * meta : {
    noCache: true                   // 如果设置为true，则不会被 <keep-alive> 缓存(默认 false)
    title: 'title'                  // 设置该路由在侧边栏和面包屑中展示的名字
    icon: 'svg-name'                // 设置该路由的图标，对应路径src/assets/icons/svg
    breadcrumb: false               // 如果设置为false，则不会在breadcrumb面包屑中显示
    activeMenu: '/system/user'      // 当路由设置了该属性，则会高亮相对应的侧边栏。
  }
 */

// 路由白名单
const whiteList = ['Install', 'Login', 'Register', 'home', '401', '404', 'Admin']
// 公共路由 Route paths should start with a "/"
export const constantRoutes = [
  // 后台管理首页
  {
    path: adminBasePath,
    component: Layout,
    name: 'Admin',
    hidden:true,
    redirect: { name: 'Index' },  // redirect: adminBasePath + '/index',
    children: [
      {
        path: 'index',
        component: () => import('@/views/index.vue'),
        name: 'Index',
        meta: { title: '首页', icon: 'dashboard', affix: true }
      }
    ]
  },
  // 重定向
  /* {
    path: adminBasePath + '/redirect',
    component: Layout,
    hidden: true,
    name: 'Redirect',
    children: [
      {
        path: adminBasePath + '/redirect/:path(.*)',
        component: () => import('@/views/kucoder/redirect/index.vue')
      }
    ]
  }, */
  {
    path: adminBasePath + '/install',
    name: 'Install',
    component: () => import('@/views/kucoder/system/install/install'),
    hidden: true
  },
  {
    path: adminBasePath + '/login',
    name: 'Login',
    component: () => import('@/views/kucoder/login'),
    hidden: true
  },
  {
    path: adminBasePath + '/register',
    name: 'Register',
    component: () => import('@/views/kucoder/register'),
    hidden: true
  },
  {
    path: adminBasePath + '/logout',
    name: 'Logout',
    component: () => import('@/views/kucoder/logout'),
    hidden: true
  },
  {
    path: adminBasePath + '/user',
    component: Layout,
    hidden: true,
    redirect: 'noredirect',
    children: [
      {
        path: 'profile',
        component: () => import('@/views/kucoder/system/user/profile/index'),
        name: 'Profile',
        meta: { title: '个人中心', icon: 'user' }
      }
    ]
  },
  // 无访问权限
  {
    path: '/401',
    name: '401',
    component: () => import('@/views/kucoder/error/401'),
    hidden: true
  },
  {
    path: '/404',
    name: '404',
    component: () => import('@/views/kucoder/error/404'),
    hidden: true
  },
]

// 动态路由 不方便修改
export const dynamicRoutes = [
  /* {
    path: '/system/user-auth',
    component: Layout,
    hidden: true,
    permissions: ['system:user:edit'],
    children: [
      {
        path: 'role/:userId(\\d+)',
        component: () => import('@/views/system/user/authRole'),
        name: 'AuthRole',
        meta: { title: '分配角色', activeMenu: '/system/user' }
      }
    ]
  } */
]

// static目录下的所有静态路由文件
let allStaticRoutes = []
const staticRoutes = import.meta.glob('./static/*.js', { eager: true })
Object.keys(staticRoutes).forEach((file) => {
  allStaticRoutes = allStaticRoutes.concat(staticRoutes[file].default)
})

// 路由器实例
const router = createRouter({
  history: createWebHistory(import.meta.env.VITE_DEPLOY_DIR),
  routes: constantRoutes.concat(allStaticRoutes),
  scrollBehavior(to, from, savedPosition) {
    // 切换到新路由时，控制页面滚动行为
    // 返回 savedPosition，在按下 后退/前进 按钮时，就会像浏览器的原生表现那样
    if (savedPosition) {
      return savedPosition
    }
    return { top: 0 }
  },
})

/**
 * 路由守卫
 */
NProgress.configure({ showSpinner: false })
const isWhiteList = (name) => whiteList.includes(name)
let dynamicRoutesAdd = false
router.beforeEach(async (to, from) => {
  console.log('路由变化1:', from, to)
  const userStore = useUserStore()
  NProgress.start()
  if (userStore.token) {
    to.meta.title && useSettingsStore().setTitle(to.meta.title)
    if (to.name === 'Login') {
      return { name: 'Index' }
    } else if (isWhiteList(to.name)) {
      return true
    } else {
      // console.log('动态路由:', usePermissionStore().sidebarRouters, to)
      if (!dynamicRoutesAdd) {
        console.log('动态路由未添加，开始添加...')
        await usePermissionStore().generateRoutes()
          .then(resolveRoutes => {
            resolveRoutes.forEach(route => {
              if (!isHttp(route.path)) {
                router.addRoute(route) // 动态添加可访问路由表
              }
            })
            console.log('路由变化2:', from, to)
            dynamicRoutesAdd = true
            // return { ...to, replace: true }  // 这种方式会导致刷新后 路由不生效
            router.replace({ ...to, replace: true })
            NProgress.done()
          })
          .catch(({ msg, code }) => {
            console.log('路由初始化错误', msg, code)
            msg && ElMessage.error(msg)
            NProgress.done()
          })
      } else {
        return true // 如果动态路由已经添加过，直接进入
      }
    }
  } else {
    // 没有token
    if (isWhiteList(to.name)) {
      // 在免登录白名单，直接进入
      return true
    } else {
      // return {path:adminBasePath + `login?redirect=${to.fullPath}`}
      console.log('未登录未在白名单', to)
      //如果to.path以adminBasePath开头，则重定向到登录页
      if (to.path.startsWith(adminBasePath + '/')) {
        return { name: 'Login', redirect: to.fullPath } // 重定向到登录页
      }
      return { name: '404' }
    }
  }
})

router.afterEach(() => {
  NProgress.done()
})


export default router;
