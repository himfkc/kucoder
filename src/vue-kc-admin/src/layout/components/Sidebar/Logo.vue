<template>
  <div class="sidebar-logo-container" :class="{ 'collapse': collapse }">
    <div class="logo-content">
      <transition name="fade">
        <!-- 导航折叠 -->
        <router-link v-if="collapse" key="collapse" class="x c ac logo-link h-20" :to="{ name: 'Index' }">
          <img v-if="logo" :src="logo" class="w-4 m-1" />
          <h1 v-else class="x ac  h-8 c-white">{{ title }}</h1>
        </router-link>
        <!-- 导航未折叠 -->
        <router-link v-else key="expand" class="x ac logo-link h-20" :to="{ name: 'Index' }">
          <img v-if="logo" :src="logo" class=" w-4 m-1" />
          <h1 class="x ac  h-8 c-white fs-15">{{ title }}</h1>
        </router-link>
      </transition>
    </div>
  </div>
</template>

<script setup>
import useSettingsStore from '@/store/modules/settings'
import variables from '@/assets/styles/variables.module.scss'
import useUserStore from '@/store/modules/user'
import { imgUrl } from '@/utils/kucoder';

defineProps({
  collapse: {
    type: Boolean,
    required: true
  }
})

const userStore = useUserStore()
// const logo = userStore.site_set.logo ? imgUrl(userStore.site_set.logo) : `${import.meta.env.VITE_DEPLOY_DIR.replace(/\/$/, '')}/logo.png`
const logo = `${import.meta.env.VITE_DEPLOY_DIR.replace(/\/$/, '')}/logo.png`
const title = computed(() => userStore.site_set.site_name)
const settingsStore = useSettingsStore()
const sideTheme = computed(() => settingsStore.sideTheme)

// 获取Logo背景色
const getLogoBackground = computed(() => {
  if (settingsStore.isDark) {
    return 'var(--sidebar-bg)'
  }
  return sideTheme.value === 'theme-dark' ? variables.menuBg : variables.menuLightBg
})

// 获取Logo文字颜色
const getLogoTextColor = computed(() => {
  if (settingsStore.isDark) {
    return 'var(--sidebar-text)'
  }
  return sideTheme.value === 'theme-dark' ? '#fff' : variables.menuLightText
})
</script>

<style lang="scss" scoped>
@use '@/assets/styles/variables.module.scss' as *;

.fade-enter-active {
  transition: opacity 1.5s;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}

.sidebar-logo-container {
  position: relative;
  width: 100%;
  height: 50px;
  line-height: 50px;
  background: v-bind(getLogoBackground);
  overflow: hidden;

  .logo-content {
    height: 100%;
  }

  .logo-link {
    display: flex;
    align-items: center;
    padding-left: 20px;
    height: 100%;
    text-decoration: none;
    color: v-bind(getLogoTextColor);

    &:hover {
      background-color: rgba(0, 0, 0, 0.06);
    }
  }

  & .sidebar-logo-link {
    height: 100%;
    width: 100%;

    & .sidebar-logo {
      width: 32px;
      height: 32px;
      vertical-align: middle;
      margin-right: 12px;
    }

    & .sidebar-title {
      display: inline-block;
      margin: 0;
      color: v-bind(getLogoTextColor);
      font-weight: 600;
      line-height: 50px;
      font-size: 14px;
      font-family: Avenir, Helvetica Neue, Arial, Helvetica, sans-serif;
      vertical-align: middle;
    }
  }

  &.collapse {
    .logo-link {
      padding-left: 0;
      justify-content: center;
    }

    .sidebar-logo {
      margin-right: 0px;
    }
  }
}
</style>