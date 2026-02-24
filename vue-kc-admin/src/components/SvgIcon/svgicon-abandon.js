import * as ElementPlusIconsVue from '@element-plus/icons-vue'

/**
 * svg图标注册为全局组件 这种方法很差 kucoder已使用按需动态加载element-plus图标
 * 全局注册，但并没有被使用的组件无法在生产打包时被自动移除 (也叫“tree-shaking”)。
 * 如果你全局注册了一个组件，即使它并没有被实际使用，它仍然会出现在打包后的 JS 文件中
 * 使用unplugin-icons插件可以按需导入  参考 https://www.npmjs.com/package/unplugin-icons
 */
export default {
  install: (app) => {
    for (const key in ElementPlusIconsVue) {
      const componentConfig = ElementPlusIconsVue[key]
      app.component(componentConfig.name, componentConfig)
    }
  }
}
