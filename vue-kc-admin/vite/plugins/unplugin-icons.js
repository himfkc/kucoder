// https://www.npmjs.com/package/unplugin-icons
import Icons from 'unplugin-icons/vite'

export default function createIcons() {
    return Icons({
        autoInstall: false,
        // compiler: "vue3", // 编译方式
        // scale: 1, // 缩放
        // defaultClass: '', // 默认类名
        // defaultStyle: '', // 默认样式
        // jsx: 'react' // jsx支持
    })
}