// Layout
import Layout from '@/layout'

// 静态路由
export default [
    {
        path: '/example',
        component: () => import('@/views/yourPluginName/example'),
        hidden: true
    },
]