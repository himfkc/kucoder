<template>
  <div v-if="!item.hidden">
    <template v-if="isEmpty(item.children)">
      <app-link :to="item.path">
        <el-menu-item :index="item.path" :class="{ 'submenu-title-noDropdown': !isNest }">
          <svg-icon :icon-class="item.meta.icon" />
          <template v-if="item.meta" #title>
            <span class="menu-title">{{ item.meta.title }}</span>
          </template>
        </el-menu-item>
      </app-link>
    </template>

    <el-sub-menu v-else ref="subMenu" :index="item.path" teleported>
      <template v-if="item.meta" #title>
        <svg-icon :icon-class="item.meta?.icon" />
        <span class="menu-title">{{ item.meta.title }}</span>
      </template>

      <sidebar-item v-for="(child, index) in item.children" :key="child.path + index" :is-nest="true" :item="child"
        :base-path="child.path" class="nest-menu" />
    </el-sub-menu>
  </div>
</template>

<script setup>
import { isExternal, isEmpty } from '@/utils/validate'
import AppLink from './Link'
import { getNormalPath } from '@/utils/ruoyi'

const props = defineProps({
  // route object
  item: {
    type: Object,
    required: true
  },
  isNest: {
    type: Boolean,
    default: false
  },
  basePath: {
    type: String,
    default: ''
  }
})

// const onlyOneChild = ref({})

/* function hasOneShowingChild(children = [], parent) {
  if (!children) {
    children = []
  }
  const showingChildren = children.filter(item => {
    if (item.hidden) {
      return false
    }
    onlyOneChild.value = item
    return true
  })

  // When there is only one child router, the child router is displayed by default
  if (showingChildren.length === 1) {
    return true
  }

  // Show parent if there are no child router to display
  if (showingChildren.length === 0) {
    onlyOneChild.value = { ...parent, path: '', noShowingChildren: true }
    return true
  }

  return false
}

function resolvePath(routePath, routeQuery) {
  if (isExternal(routePath)) {
    return routePath
  }
  if (isExternal(props.basePath)) {
    return props.basePath
  }
  if (routeQuery) {
    let query = JSON.parse(routeQuery)
    return { path: getNormalPath(props.basePath + '/' + routePath), query: query }
  }
  return getNormalPath(props.basePath + '/' + routePath)
}

function hasTitle(title) {
  if (title.length > 5) {
    return title
  } else {
    return ""
  }
} */
</script>
