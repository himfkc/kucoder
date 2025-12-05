<template>
  <!-- https://element-plus.org/zh-CN/component/config-provider.html#config-provider-attributes -->
  <el-config-provider :size="size" :z-index="zIndex" :locale="zhCn">
    <!-- <app /> -->
    <router-view />
  </el-config-provider>
</template>

<script setup>
import { ElConfigProvider } from 'element-plus'
import zhCn from 'element-plus/es/locale/lang/zh-cn'
import Cookies from 'js-cookie'
import useSettingsStore from '@/store/modules/settings'
import { handleThemeStyle } from '@/utils/theme'


const zIndex = ref(Cookies.get('zIndex') || 3000)
const size = ref(Cookies.get('size') || 'default')

onMounted(() => {
  nextTick(() => {
    // 初始化主题样式
    handleThemeStyle(useSettingsStore().theme)
  })
})


</script>
