import AutoImport from 'unplugin-auto-import/vite'
import { ElementPlusResolver } from 'unplugin-vue-components/resolvers'

export default function createAutoImport() {
    return AutoImport({
        imports: [
            'vue',
            'vue-router',
            'pinia'
        ],
        dts: false, //ts时使用
        resolvers: [
            // Auto import functions from Element Plus, e.g. ElMessage, ElMessageBox... (with style)
            // 自动导入 Element Plus 相关函数，如：ElMessage, ElMessageBox... (带样式)
            ElementPlusResolver()
        ],
    })
}
