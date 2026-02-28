<template>
  <el-breadcrumb class="app-breadcrumb" separator="/">
    <transition-group name="breadcrumb">
      <el-breadcrumb-item v-for="(item, index) in levelList" :key="item.path">
        <span v-if="item.redirect === 'noRedirect' || index == levelList.length - 1" class="no-redirect">{{
          item.meta.title }}</span>
        <a v-else @click.prevent="handleLink(item)">{{ item.meta.title }}</a>
      </el-breadcrumb-item>
    </transition-group>
  </el-breadcrumb>
</template>

<script setup>
import usePermissionStore from '@/store/modules/permission'
import { adminBasePath } from '@/api/adminRouteBasePath'

const route = useRoute()
const router = useRouter()
const permissionStore = usePermissionStore()
const levelList = ref([])

//  meta.breadcrumb如果设置为false，则不会在breadcrumb面包屑中显示(默认 true)
function getBreadcrumb() {
  // only show routes with meta.title
  let matched = []
  // console.log('getBreadcrumb route', route)
  const routeMathed = route.matched || []
  for (let i = 0; i < routeMathed.length; i++) {
    const item = routeMathed[i]
    if (item.meta && item.meta.title) {
      matched.push(item)
    }
  }
  // 判断是否为首页
  if (!isDashboard(matched[0])) {
    matched = [{ path: adminBasePath + "/index", meta: { title: "首页" } }].concat(matched)
  }
  levelList.value = matched.filter(item => item.meta && item.meta.title && item.meta.breadcrumb !== false)
}

/* function findPathNum(str, char = "/") {
  let index = str.indexOf(char)
  let num = 0
  while (index !== -1) {
    num++
    index = str.indexOf(char, index + 1)
  }
  return num
}
function getMatched(pathList, routeList, matched) {
  console.log('getMatched pathList', pathList, 'routeList', routeList)
  let data = routeList.find(item => item.path == pathList[0] || (item.name += '').toLowerCase() == pathList[0])
  if (data) {
    matched.push(data)
    if (data.children && pathList.length) {
      pathList.shift()
      getMatched(pathList, data.children, matched)
    }
  }
} */
function isDashboard(route) {
  const name = route && route.name
  if (!name) {
    return false
  }
  return name.trim() === 'Index'
}
function handleLink(item) {
  const { redirect, path } = item
  if (redirect) {
    router.push(redirect)
    return
  }
  router.push(path)
}

watchEffect(() => {
  // if you go to the redirect page, do not update the breadcrumbs
  if (route.path.startsWith(adminBasePath + '/redirect/')) {
    return
  }
  getBreadcrumb()
})
getBreadcrumb()
</script>

<style lang='scss' scoped>
.app-breadcrumb.el-breadcrumb {
  display: inline-block;
  font-size: 14px;
  line-height: 50px;
  margin-left: 8px;

  .no-redirect {
    color: #97a8be;
    cursor: text;
  }
}
</style>