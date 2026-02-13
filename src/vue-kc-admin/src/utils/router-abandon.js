
import Layout from '@/layout'

const views = import.meta.glob('@/views/**/*.vue')
console.log('glob views', views)

const slash = '/'

export function menuToRoute(menus) {
    let routes = []
    menus.forEach(menu => {
        // 按钮不加入路由
        if (menu.type !== 'button') {
            // 不是外链
            if (!menu.link_url) {
                let route = {
                    meta: {
                        title: menu.title,
                        // icon: menu.icon,
                        icon: 'dashboard',
                        noCache: !menu.keepalive,
                    },
                    hidden: menu.hidden
                }
                if (menu.path) {
                    route.path = route.name = slash + menu.plugin + '/' + menu.path
                }
                if (menu.children && menu.children?.length > 0) {
                    route.children = menuToRoute(menu.children)
                }
                if (menu.component && menu.type === 'menu') {
                    route.component = Layout
                    // route.component = views[`/src/views/${menu.component}/index.vue`]
                    route.children = []
                    const menuChildren = {
                        meta: {
                            title: menu.title,
                            // icon: menu.icon,
                            icon: 'dashboard',
                            noCache: !menu.keepalive,
                        },
                        hidden: menu.hidden,

                        path: slash + menu.plugin + '/' + menu.path,
                        name: slash + menu.plugin + '/' + menu.path,
                        component: views[`/src/views/${menu.component}/index.vue`]
                    }
                    route.children.push(menuChildren)
                }

                routes.push(route)
            } else {
                // 外链
                let route = {
                    path: '/' + menu.link_url,
                    meta: { title: menu.title, icon: menu.icon },
                }
                routes.push(route)
            }
        }
    })
    return routes
}