// Vite 的官方插件，用于支持Vue3项目的开发 处理Vue单文件组件SFC，使其能够在Vite项目中正常工作
import vue from '@vitejs/plugin-vue'

// 自动导入ref、reactive、computed等vue vue-route pinia函数
import createAutoImport from './unplugin-auto-import'

// 组件自动加载
import createVueComponents from './unplugin-vue-components'

// svg图标转组件
import createSvgIcon from './vite-plugin-svg-icons'

// 压缩
// import createCompression from './vite-plugin-compression'

// unplugin-icons图标
import createIcons from './unplugin-icons'

// unocss
import createUnocss from './unocss'

// vue-devtools
import vueDevTools from 'vite-plugin-vue-devtools'

// hmr热更新
import { hmr } from './hmr'

export default function createVitePlugins(env, isBuild = false) {
    const vitePlugins = [vue()]
    // 自动导入import
    vitePlugins.push(createAutoImport())

    // 使用unplugin-vue-components
    vitePlugins.push(createVueComponents())

    // vite-plugin-svg-icons
    vitePlugins.push(createSvgIcon(isBuild))

    // 压缩vite-plugin-compression
    // isBuild && vitePlugins.push(...createCompression(env))

    // 使用unplugin-icons
    vitePlugins.push(createIcons())

    // 使用unocss
    vitePlugins.push(createUnocss())

    // 使用vue-devtools
    vitePlugins.push(vueDevTools())

    // 使用hmr
    !isBuild && vitePlugins.push(hmr(env))

    return vitePlugins
}
