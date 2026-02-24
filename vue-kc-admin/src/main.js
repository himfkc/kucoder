import { createApp } from 'vue'

import App from './App'
// 状态管理
import pinia from './store'
// 路由
import router from './router'
// 自定义指令
import directive from './directive'
// 插件
import plugins from './plugins'
// svg图标
import 'virtual:svg-icons-register'
// unocss
import 'virtual:uno.css'
// element-plus
import 'element-plus/dist/index.css'
import 'element-plus/theme-chalk/dark/css-vars.css'
import '@/assets/styles/index.scss'

const app = createApp(App)

app.use(router)
app.use(pinia)
app.use(plugins)
directive(app)

app.mount('#app')
