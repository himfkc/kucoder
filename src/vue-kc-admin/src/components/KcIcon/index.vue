<!-- 支持的图标来源
    本地 SVG：src/assets/icons/svg/ 目录下的文件
    Element Plus：ep:Search, ep:User 等（使用 @element-plus/icons-vue）
    Iconify：fa:user, fa:home, mdi:home 等
-->
<template>
    <svg-icon v-if="isLocalSvg" :icon-class="iconName" :class="className" :color="color" />
    <el-icon v-else-if="isElementPlus && elementIconComponent" :size="parsedSize" :class="className" class="my_icon">
        <component :is="elementIconComponent" />
    </el-icon>
    <el-icon v-else-if="isElementPlus" :size="parsedSize" :class="className" class="my_icon">
        <icon-ep-warning />
    </el-icon>
    <Icon v-else-if="isIconify" :icon="iconName" :class="className" :width="parsedSize" :height="parsedSize"
        class="my_icon" />
</template>


<script setup>
import { Icon } from '@iconify/vue'
import * as ElementPlusIconsVue from '@element-plus/icons-vue'

const props = defineProps({
    // 图标名称
    icon: {
        type: String,
        required: true
    },
    // 图标来源：local-本地SVG，iconify-Iconify图标，auto-自动识别
    type: {
        type: String,
        default: 'auto',
        validator: (value) => ['local', 'iconify', 'auto'].includes(value)
    },
    // 图标尺寸
    size: {
        type: [String, Number],
        default: ''
    },
    // 颜色（仅本地SVG支持）
    color: {
        type: String,
        default: ''
    },
    // 自定义类名
    className: {
        type: String,
        default: ''
    }
})

// 判断是否为本地 SVG 图标
const isLocalSvg = computed(() => {
    if (props.type === 'local') return true
    if (props.type === 'iconify') return false
    // auto 模式：没有前缀的视为本地 SVG
    return !props.icon.includes(':')
})

// 判断是否为 Element Plus 图标
const isElementPlus = computed(() => {
    if (props.type === 'local') return false
    if (props.type === 'iconify') return false
    // auto 模式：以 ep: 开头的视为 Element Plus 图标
    return props.icon.startsWith('ep:')
})

// 判断是否为 Iconify 图标
const isIconify = computed(() => {
    if (props.type === 'iconify') return true
    if (props.type === 'local') return false
    // auto 模式：有前缀且不是 ep: 的视为 Iconify 图标
    return props.icon.includes(':') && !props.icon.startsWith('ep:')
})

// Element Plus 图标组件
const elementIconComponent = computed(() => {
    if (!isElementPlus.value) return null
    // 移除 ep: 前缀
    let iconKey = props.icon.substring(3)

    // 直接尝试使用原始名称（因为 requireIcons.js 中已经是 PascalCase）
    let componentName = iconKey

    // 如果找不到，尝试首字母大写
    if (!ElementPlusIconsVue[componentName]) {
        componentName = iconKey.charAt(0).toUpperCase() + iconKey.slice(1)
    }

    // 如果还是找不到，打印警告
    if (!ElementPlusIconsVue[componentName]) {
        console.warn(`KcIcon: Element Plus 图标 "${props.icon}" 未找到，组件名称: ${componentName}`)
        return null
    }
    return ElementPlusIconsVue[componentName]
})

// 图标名称（本地 SVG 去除前缀）
const iconName = computed(() => {
    if (isLocalSvg.value) {
        return props.icon
    }
    return props.icon
})

// 解析尺寸
const parsedSize = computed(() => {
    return typeof props.size === 'number' ? props.size + 'px' : props.size
})
</script>

<style lang="scss" scoped>
// 继承 SvgIcon 的样式
:deep(.svg-icon) {
    width: 1em;
    height: 1em;
    position: relative;
    fill: currentColor;
    vertical-align: -2px;
}

// Iconify 图标样式
.iconify {
    display: inline-block;
    vertical-align: -2px;
}
</style>
