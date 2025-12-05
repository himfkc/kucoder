import Components from 'unplugin-vue-components/vite'
import { ElementPlusResolver } from 'unplugin-vue-components/resolvers'
import IconsResolver from 'unplugin-icons/resolver'

export default function createVueComponents() {
    return Components({
        // dirs 指定组件所在位置，默认为 src/components 可以让我们使用自己定义组件的时候免去 import 的麻烦
        dirs: ['src/components/'],

        // 配置需要将哪些后缀类型的文件进行自动按需引入
        extensions: ['vue'],

        // resolvers for custom components 处理自定义组件
        resolvers: [
            // Auto register Element Plus components
            // 自动导入 Element Plus 组件
            ElementPlusResolver(),

            // Auto register icon components
            // 自动注册图标组件
            IconsResolver({
                prefix: 'icon', // 自动引入的Icon组件统一前缀，默认为 i，设置false为不需要前缀
                // enabledCollections: ['mdi'], // 使用element-plus 这是可选的，默认启用 Iconify 支持的所有集合
            }),
        ],

    })
}