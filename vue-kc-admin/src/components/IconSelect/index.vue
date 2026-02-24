<template>
  <div class="icon-body">
    <div class="icon-tabs">
      <el-tabs v-model="activeTab" @tab-change="handleTabChange">
        <el-tab-pane label="本地SVG" name="local"></el-tab-pane>
        <el-tab-pane label="Element Plus" name="element"></el-tab-pane>
      </el-tabs>
    </div>
    <el-input v-model="iconName" class="icon-search" clearable placeholder="请输入图标名称" @clear="filterIcons"
      @input="filterIcons">
      <template #suffix><i class="el-icon-search el-input__icon" /></template>
    </el-input>
    <div class="icon-list">
      <div class="list-container">
        <div v-for="(item, index) in iconList" class="icon-item-wrapper" :key="index" @click="selectedIcon(item)">
          <div :class="['icon-item', { active: activeIcon === item }]">
            <svg-icon v-if="activeTab === 'local'" :icon-class="item" class="icon" style="height: 16px;width: 16px;" />
            <el-icon v-else-if="activeTab === 'element'" :size="16">
              <component :is="ElementPlusIconsVue[item]" />
            </el-icon>
            <span>{{ item }}</span>
          </div>
        </div>
        <div v-if="iconList.length === 0" class="empty-tip">暂无匹配图标</div>
      </div>
    </div>
  </div>
</template>

<script setup>
import icons from './requireIcons'
import * as ElementPlusIconsVue from '@element-plus/icons-vue'

const props = defineProps({
  activeIcon: {
    type: String
  }
})

const iconName = ref('')
const iconList = ref(icons.local)
const activeTab = ref('local')
const emit = defineEmits(['selected'])

function handleTabChange(tabName) {
  iconList.value = icons[tabName]
  filterIcons()
}

function filterIcons() {
  const currentIcons = icons[activeTab.value]
  iconList.value = currentIcons
  if (iconName.value) {
    iconList.value = currentIcons.filter(item => item.toLowerCase().indexOf(iconName.value.toLowerCase()) !== -1)
  }
}

function selectedIcon(name) {
  const prefixMap = {
    local: '',
    element: 'ep:'
  }
  const finalIcon = prefixMap[activeTab.value] + name
  emit('selected', finalIcon)
  document.body.click()
}

function reset() {
  iconName.value = ''
  activeTab.value = 'local'
  iconList.value = icons.local
}

defineExpose({
  reset
})
</script>

<style lang='scss' scoped>
.icon-body {
  width: 100%;
  padding: 10px;

  .icon-tabs {
    margin-bottom: 10px;

    :deep(.el-tabs__nav-wrap::after) {
      background-color: transparent;
    }

    :deep(.el-tabs__item) {
      padding: 0 15px;
      font-size: 13px;
    }
  }

  .icon-search {
    position: relative;
    margin-bottom: 10px;
  }

  .icon-list {
    height: 200px;
    overflow: auto;

    .list-container {
      display: flex;
      flex-wrap: wrap;

      .icon-item-wrapper {
        width: calc(100% / 3);
        height: 35px;
        line-height: 35px;
        cursor: pointer;
        display: flex;

        .icon-item {
          display: flex;
          max-width: 100%;
          height: 100%;
          padding: 0 5px;
          align-items: center;

          &:hover {
            background: #ececec;
            border-radius: 5px;
          }

          .icon-wrapper {
            position: relative;
            flex-shrink: 0;
            width: 16px;
            height: 16px;

            .icon {
              font-size: 16px;
              width: 16px;
              height: 16px;
            }

            .icon-error {
              position: absolute;
              top: 50%;
              left: 50%;
              transform: translate(-50%, -50%);
              font-size: 10px;
              color: #999;
              background: #f5f5f5;
              border-radius: 50%;
              width: 14px;
              height: 14px;
              line-height: 14px;
              text-align: center;
            }
          }

          .icon {
            flex-shrink: 0;
            font-size: 16px;
            height: 16px;
            width: 16px;
          }

          span {
            display: inline-block;
            vertical-align: -0.15em;
            fill: currentColor;
            padding-left: 5px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            font-size: 12px;
          }
        }

        .icon-item.active {
          background: #ececec;
          border-radius: 5px;
        }
      }

      .empty-tip {
        width: 100%;
        text-align: center;
        padding: 30px 0;
        color: #999;
        font-size: 13px;
      }
    }
  }
}
</style>