<template>
   <div class="app-container config-container">
      <div class="config-header" v-if="!myPlugin">
         <div class="config-title">
            <el-icon class="title-icon">
               <Setting />
            </el-icon>
            <span>系统配置</span>
         </div>
         <div>
            <el-button type="primary" @click="handleRefreshCache">刷新缓存</el-button>
         </div>
      </div>

      <!-- 插件筛选 -->
      <div class="plugin-filter" v-if="!myPlugin">
         <el-menu mode="horizontal" :default-active="activePlugin" class="plugin-menu" @select="handlePluginSelect">
            <el-menu-item v-for="(plugin, i) in pluginList" :key="i" :index="plugin.name">
               {{ plugin.title }}
            </el-menu-item>
         </el-menu>
      </div>

      <div class="config-content">
         <!-- 左侧分组导航 -->
         <div class="config-sidebar">
            <div class="x ac gap-3 m-3 ml-7">
               <el-tooltip content="设置分组：系统管理/ 配置管理/ 配置分组" placement="top">
                  <div class="x ac gap-3">
                     <el-icon><icon-ep-operation /></el-icon>
                     配置分组
                  </div>
               </el-tooltip>
               <el-tooltip content="添加分组" placement="top">
                  <el-icon @click="handleAddGroup"><icon-ep-plus /></el-icon>
               </el-tooltip>
            </div>
            <el-menu :default-active="activeGroup" class="group-menu" @select="handleGroupSelect">
               <el-menu-item v-for="group in groupList" :key="group.id" :index="String(group.id)">
                  <el-icon>
                     <component :is="getGroupIcon(group.id)" />
                  </el-icon>
                  <span>{{ group.groupTitle }}</span>
               </el-menu-item>
            </el-menu>
         </div>

         <!-- 右侧配置表单 -->
         <div class="config-main">
            <div v-loading="loading" class="config-form-container">
               <div v-if="currentGroupConfig.title" class="group-header">
                  <div class="group-title">
                     <el-icon>
                        <component :is="getGroupIcon(Number(activeGroup))" />
                     </el-icon>
                     <span>{{ currentGroupConfig.title }}</span>
                  </div>
                  <div class="header-buttons">
                     <el-button type="primary" @click="handleAddConfig">添加配置项</el-button>
                     <el-button type="danger" @click="handleSaveGroup" :loading="saving">保存</el-button>
                  </div>
               </div>

               <!-- 空状态：没有标题但有数据，或者有标题但数据为空 -->
               <div
                  v-if="(currentGroupConfig.title || currentGroupConfig.items) && currentGroupConfig.items && currentGroupConfig.items.length === 0"
                  class="empty-config">
                  <el-empty description="该分组暂无配置项，点击上方按钮添加">
                     <el-button type="primary" @click="handleAddConfig">添加配置项</el-button>
                  </el-empty>
               </div>

               <el-form v-if="currentGroupConfig.items && currentGroupConfig.items.length > 0" ref="configFormRef"
                  :model="formData" label-width="200" class="config-form">
                  <el-form-item v-for="item in currentGroupConfig.items" :key="item.id" :label="item.title"
                     :prop="item.name">
                     <div class="form-item-content">
                        <div class="form-input-wrapper">
                           <template v-if="item.type === 'string' || item.type === 'input'">
                              <el-input v-model="formData[item.name]" :type="item.is_secret === 1 ? 'password' : 'text'"
                                 :placeholder="item.placeholder || '请输入' + item.title"
                                 :clearable="item.is_secret === 1 ? false : true" />
                           </template>
                           <template v-else-if="item.type === 'textarea'">
                              <el-input v-model="formData[item.name]" type="textarea" :rows="4"
                                 :placeholder="item.placeholder || '请输入' + item.title" />
                           </template>
                           <template v-else-if="item.type === 'number' || item.type === 'input-number'">
                              <el-input-number v-model="formData[item.name]" :min="0" controls-position="right" />
                           </template>
                           <template v-else-if="item.type === 'switch'">
                              <el-switch v-model="formData[item.name]" :active-value="1" :inactive-value="0" />
                           </template>
                           <template v-else-if="item.type === 'select'">
                              <el-select v-model="formData[item.name]" :placeholder="'请选择' + item.title"
                                 style="width: 100%" clearable filterable>
                                 <el-option v-for="(label, value) in item.options" :key="value" :label="label"
                                    :value="value" />
                              </el-select>
                           </template>

                           <template v-else-if="item.type === 'radio'">
                              <el-radio-group v-model="formData[item.name]" :key="'radio-' + item.name">
                                 <el-radio v-for="(label, value) in item.options" :key="String(value)"
                                    :value="String(value)">
                                    {{ label }}
                                 </el-radio>
                              </el-radio-group>
                           </template>

                           <template v-else-if="item.type === 'checkbox'">
                              <el-checkbox-group v-model="formData[item.name]" :key="'checkbox-' + item.name">
                                 <el-checkbox v-for="(label, value) in item.options" :key="String(value)"
                                    :value="String(value)">
                                    {{ label }}
                                 </el-checkbox>
                              </el-checkbox-group>
                           </template>

                           <template v-else-if="item.type === 'rich'">
                              <el-input v-model="formData[item.name]" type="textarea" :rows="8"
                                 :placeholder="item.placeholder || '请输入' + item.title" />
                           </template>
                           <template v-else-if="item.type === 'upload_img'">
                              <ImageUpload v-model="formData[item.name]" :limit="1" :file-size="5"
                                 :data="{ plugin: activePlugin }" />
                           </template>
                           <template v-else-if="item.type === 'upload_file'">
                              <FileUpload v-model="formData[item.name]" :limit="5" :file-size="50" text="点击上传"
                                 :data="{ plugin: activePlugin }" />
                           </template>
                        </div>
                        <el-button v-if="item.allow_del === 1" type="danger" circle size="small" class="delete-btn"
                           @click="handleDeleteConfig(item)">
                           <el-icon>
                              <Delete />
                           </el-icon>
                        </el-button>
                     </div>

                     <template v-if="item.tip" #label>
                        <span>{{ item.title }}</span>
                        <el-tooltip :content="item.tip" placement="top">
                           <el-icon class="label-tip">
                              <QuestionFilled />
                           </el-icon>
                        </el-tooltip>
                     </template>
                  </el-form-item>
               </el-form>

               <el-empty v-else description="暂无配置项" />
            </div>
         </div>
      </div>

      <!-- 添加配置项弹框 -->
      <el-dialog v-model="addDialogVisible" title="添加配置项" width="600px" append-to-body draggable>
         <el-form :model="addForm" label-width="100px">
            <el-form-item label="参数标题" required>
               <el-input v-model="addForm.title" placeholder="如：站点名称" />
            </el-form-item>
            <el-form-item label="参数名" required>
               <el-input v-model="addForm.name" placeholder="英文字符 如：site_name" />
            </el-form-item>
            <el-form-item label="配置类型" required>
               <el-select v-model="addForm.type" style="width: 100%">
                  <el-option label="文本框" value="input" />
                  <el-option label="文本域" value="textarea" />
                  <el-option label="数字" value="input-number" />
                  <el-option label="开关" value="switch" />
                  <el-option label="下拉选择" value="select" />
                  <el-option label="单选" value="radio" />
                  <el-option label="多选" value="checkbox" />
                  <el-option label="富文本" value="rich" />
                  <el-option label="上传图片" value="upload_img" />
                  <el-option label="上传文件" value="upload_file" />
               </el-select>
            </el-form-item>
            <el-form-item label="配置值">
               <el-input v-model="addForm.value" />
            </el-form-item>
            <el-form-item v-if="['select', 'radio', 'checkbox'].includes(addForm.type)" label="选项数据">
               <el-input v-model="addForm.dataText" type="textarea" :rows="4"
                  placeholder='请输入选项数据，JSON格式，如：{"1":"选项1","2":"选项2"}' />
            </el-form-item>
            <el-form-item label="提示说明">
               <el-input v-model="addForm.tip" placeholder="配置项的说明文字" />
            </el-form-item>
            <el-form-item label="敏感字段">
               <el-switch v-model="addForm.is_secret" :active-value="1" :inactive-value="0" />
               <span style="margin-left: 10px; color: #909399;">勾选后将以密码形式显示</span>
            </el-form-item>
            <el-form-item label="允许删除">
               <el-switch v-model="addForm.allow_del" :active-value="1" :inactive-value="0" />
               <span style="margin-left: 10px; color: #909399;">勾选后允许删除该配置项</span>
            </el-form-item>
            <el-form-item label="权重">
               <el-input-number v-model="addForm.weigh" :min="0" controls-position="right" />
            </el-form-item>
         </el-form>
         <template #footer>
            <el-button @click="addDialogVisible = false">取消</el-button>
            <el-button type="primary" @click="confirmAddConfig">确定</el-button>
         </template>
      </el-dialog>

      <!-- 添加配置分组弹框 -->
      <el-dialog v-model="addGroupDialog" title="添加配置分组" width="70%" append-to-body draggable>
         <ConfigGroupVue @changed="handleConfigGroupChanged" />
         <template #footer>
            <el-button @click="addGroupDialog = false">取消</el-button>
         </template>
      </el-dialog>
   </div>
</template>

<script setup>
import { getIndex, edit, refreshCache, add, deleteConfig } from "@/api/kucoder/system/config/config"
import { Setting, QuestionFilled, Tools, Document, ChatDotRound, Upload, Lock, Monitor, Delete } from '@element-plus/icons-vue'
import ConfigGroupVue from "@/views/kucoder/system/config/configGroup/index.vue"
import useUserStore from '@/store/modules/user'

const userStore = useUserStore()
defineOptions({
   name: 'kucoder_system_config_config'
})

const props = defineProps({
   myPlugin: {
      type: String,
      default: ''
   }
})

// 配置分组添加
const addGroupDialog = ref(false)
const handleAddGroup = () => {
   addGroupDialog.value = true
}
const handleConfigGroupChanged = () => {
   addGroupDialog.value = false
   getGroupList()
}

const { proxy } = getCurrentInstance()

const activePlugin = ref(props.myPlugin)
const activeGroup = ref('')
const loading = ref(false)
const saving = ref(false)
const refreshing = ref(false)
const allConfigData = ref({ configs: [], plugins: [], groups: [] })
const groupList = ref([])
const pluginList = ref([])
const currentGroupConfig = ref({ title: '', items: [] })
const formData = ref({})
const addDialogVisible = ref(false)
const addForm = ref({
   group_id: '',
   name: '',
   title: '',
   type: 'string',
   tip: '',
   value: '',
   config_data: null,
   dataText: '',
   is_secret: 0,
   allow_del: 1,
   hide: 0,
   weigh: 0
})

// 分组图标映射
const groupIconMap = {
   1: Monitor,
   2: ChatDotRound,
   3: Upload,
   4: Document,
   5: Tools,
   6: Lock
}

function getGroupIcon(groupId) {
   return groupIconMap[groupId] || Setting
}

// 获取所有配置数据
async function getGroupList() {
   try {
      loading.value = true
      const response = await getIndex()
      allConfigData.value = response.data || { configs: [], plugins: [], groups: [] }

      // 处理插件列表（后端返回的是包含 name 和 title 的数组）
      let plugins = allConfigData.value.plugins || []
      // 去重：根据 name 字段去重
      const uniquePlugins = []
      const seenNames = new Set()
      plugins.forEach(plugin => {
         if (!seenNames.has(plugin.name)) {
            seenNames.add(plugin.name)
            uniquePlugins.push(plugin)
         }
      })
      pluginList.value = uniquePlugins

      // 默认选中第一个插件（如果传入 myPlugin 且存在于列表中，则选中它）
      if (props.myPlugin) {
         activePlugin.value = props.myPlugin
      } else if (uniquePlugins.length > 0 && !activePlugin.value) {
         activePlugin.value = uniquePlugins[0].name
      }

      // 更新分组列表
      updateGroupList()
   } catch (error) {
      proxy.$modal.msgError('获取配置失败')
      console.error(error)
   } finally {
      loading.value = false
   }
}

// 切换插件
function handlePluginSelect(plugin) {
   activePlugin.value = plugin
   // 切换插件后，重置选中的分组
   const filteredGroups = getFilteredGroups()
   if (filteredGroups.length > 0) {
      activeGroup.value = String(filteredGroups[0].id)
   } else {
      activeGroup.value = ''
   }
   loadGroupConfig()
}

// 获取过滤后的分组列表
function getFilteredGroups() {
   // 只显示当前插件下的分组（根据分组本身的 plugin 字段过滤）
   return allConfigData.value.groups
      .filter(g => g.plugin === activePlugin.value)
      .map(group => ({
         id: group.id,
         groupTitle: group.title,
         plugin: group.plugin
      }))
}

// 更新分组列表
function updateGroupList() {
   groupList.value = getFilteredGroups()
   if (groupList.value.length > 0 && !activeGroup.value) {
      activeGroup.value = String(groupList.value[0].id)
   }
}

// 加载当前分组配置
async function loadGroupConfig() {
   try {
      loading.value = true

      // 先更新分组列表
      updateGroupList()

      // 根据插件和分组过滤配置项
      let filteredConfigs = allConfigData.value.configs.filter(config => {
         // 排除隐藏的配置项（hide为1时隐藏）
         if (config.hide === 1) {
            return false
         }
         // 如果没有选中分组，只匹配插件
         if (!activeGroup.value) {
            return config.plugin === activePlugin.value
         }
         // 有选中分组时，同时匹配分组和插件
         const matchGroup = config.group_id === parseInt(activeGroup.value)
         const matchPlugin = config.plugin === activePlugin.value
         return matchGroup && matchPlugin
      })

      // 处理配置项的选项数据
      filteredConfigs = filteredConfigs.map(config => {
         const item = { ...config }
         if (['select', 'radio', 'checkbox'].includes(item.type) && item.config_data) {
            try {


               // 兼容后端返回的字符串或对象
               if (typeof item.config_data === 'string') {
                  item.options = JSON.parse(item.config_data)
               } else if (typeof item.config_data === 'object') {
                  // 使用 JSON.stringify + JSON.parse 来深拷贝并确保 key 是字符串
                  item.options = JSON.parse(JSON.stringify(item.config_data))
               } else {
                  item.options = {}
               }

               // 确保选项是对象类型
               if (item.options && typeof item.options === 'object' && !Array.isArray(item.options)) {
                  // 确保所有选项的 key 都是字符串类型，避免类型匹配问题
                  const normalizedOptions = {}
                  Object.keys(item.options).forEach(key => {
                     normalizedOptions[String(key)] = item.options[key]
                  })
                  item.options = normalizedOptions

               } else {
                  item.options = {}
               }
            } catch (e) {
               console.error('解析选项数据失败:', item.config_data, e)
               item.options = {}
            }
         }
         return item
      })

      // 获取当前分组信息
      const group = allConfigData.value.groups.find(g => g.id === parseInt(activeGroup.value))

      // 获取当前插件信息
      const plugin = pluginList.value.find(p => p.name === activePlugin.value)

      currentGroupConfig.value = {
         title: group ? group.title : (plugin ? plugin.title : ''),
         items: filteredConfigs
      }

      // 初始化表单数据（不等待 nextTick，直接设置）
      formData.value = {}
      if (currentGroupConfig.value.items) {
         currentGroupConfig.value.items.forEach(item => {
            let value = item.value


            if (item.type === 'checkbox') {
               try {
                  value = typeof value === 'string' ? JSON.parse(value) : value
                  if (!Array.isArray(value)) value = []
               } catch {
                  value = []
               }
            } else if (item.type === 'switch') {
               value = parseInt(value) || 0
            } else if (item.type === 'number' || item.type === 'input-number') {
               value = parseFloat(value) || 0
            } else if (item.type === 'select' || item.type === 'radio') {
               // 转换为字符串以匹配选项的 key 类型
               value = value !== null && value !== undefined ? String(value) : ''
            }


            formData.value[item.name] = value
         })
      }


   } catch (error) {
      proxy.$modal.msgError('获取配置失败')
      console.error(error)
   } finally {
      loading.value = false
   }
}

// 切换分组
function handleGroupSelect(groupId) {
   activeGroup.value = groupId
   loadGroupConfig()
}

// 添加配置项
function handleAddConfig() {
   addForm.value = {
      group_id: activeGroup.value,
      plugin: activePlugin.value,
      name: '',
      title: '',
      type: 'input',
      tip: '',
      value: '',
      config_data: null,
      dataText: '',
      is_secret: 0,
      allow_del: 1,
      weigh: 0
   }
   addDialogVisible.value = true
}

// 确认添加配置项
async function confirmAddConfig() {
   try {
      if (!addForm.value.name) {
         proxy.$modal.msgWarning('配置名称不能为空')
         return
      }
      if (!addForm.value.title) {
         proxy.$modal.msgWarning('配置标题不能为空')
         return
      }

      // 如果是下拉选择，解析data字段
      if (['select', 'radio', 'checkbox'].includes(addForm.value.type) && addForm.value.dataText) {
         try {
            addForm.value.config_data = JSON.parse(addForm.value.dataText)
         } catch {
            proxy.$modal.msgWarning('选项数据格式错误，请输入正确的JSON格式')
            return
         }
      } else {
         addForm.value.config_data = null
      }

      await add(addForm.value)
      proxy.$modal.msgSuccess('添加成功')
      addDialogVisible.value = false
      // 重新获取所有配置数据并加载当前分组
      await getGroupList()
      await loadGroupConfig()
   } catch (error) {
      proxy.$modal.msgError('添加失败')
      console.error(error)
   }
}

// 删除配置项
async function handleDeleteConfig(item) {
   try {
      await proxy.$modal.confirm(`确定要删除配置项"${item.title}"吗？`)
      await deleteConfig({ id: item.id })
      proxy.$modal.msgSuccess('删除成功')
      // 重新获取所有配置数据并加载当前分组
      await getGroupList()
      await loadGroupConfig()
   } catch (error) {
      if (error !== 'cancel') {
         proxy.$modal.msgError('删除失败')
         console.error(error)
      }
   }
}

// 保存当前分组配置
async function handleSaveGroup() {
   let newSiteName = ''
   try {
      saving.value = true
      const saveData = {}
      if (currentGroupConfig.value.items) {
         currentGroupConfig.value.items.forEach(item => {
            let value = formData.value[item.name]
            // 处理数组类型（checkbox）
            if (item.type === 'checkbox') {
               value = JSON.stringify(value || [])
            } else if (item.type === 'switch') {
               value = value ? 1 : 0
            }
            saveData[item.name] = value

            // 系统名称更改
            if (item.plugin === 'kucoder' && item.name === 'site_name') {
               let site_name = userStore.site_set.site_name
               if (site_name !== formData.value[item.name]) {
                  newSiteName = formData.value[item.name]
               }
            }
         })
      }
      await edit(activePlugin.value, activeGroup.value, saveData)
      proxy.$modal.msgSuccess('保存成功')
      if (newSiteName) {
         userStore.site_set.site_name = newSiteName
      }
      // 重新加载当前分组配置以更新显示
      await getGroupList()
      await loadGroupConfig()
   } catch (error) {
      proxy.$modal.msgError('保存失败')
      console.error(error)
   } finally {
      saving.value = false
      newSiteName = ''
   }
}

// 刷新缓存
async function handleRefreshCache() {
   try {
      refreshing.value = true
      await refreshCache()
      proxy.$modal.msgSuccess('刷新缓存成功')
      // 重新加载所有配置数据
      await getGroupList()
      await loadGroupConfig()
   } catch (error) {
      proxy.$modal.msgError('刷新缓存失败')
      console.error(error)
   } finally {
      refreshing.value = false
   }
}

onMounted(() => {
   getGroupList().then(() => {
      loadGroupConfig()
   })
})
</script>

<style scoped lang="scss">
.config-container {
   height: calc(100vh - 120px);
   display: flex;
   flex-direction: column;
   background: #f5f7fa;

   .config-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 16px 20px;
      background: #fff;
      border-bottom: 1px solid #e4e7ed;
      margin: -20px -20px 20px -20px;

      .config-title {
         display: flex;
         align-items: center;
         gap: 10px;
         font-size: 18px;
         font-weight: 600;
         color: #303133;

         .title-icon {
            font-size: 22px;
            color: #409eff;
         }
      }
   }

   .plugin-filter {
      display: flex;
      align-items: center;
      padding: 12px 20px;
      background: #fff;
      border-bottom: 1px solid #e4e7ed;
      margin-bottom: 20px;
      gap: 12px;

      .plugin-filter-title {
         font-size: 14px;
         color: #606266;
         white-space: nowrap;
      }

      .plugin-menu {
         flex: 1;
         border-bottom: none;
         overflow-x: auto;

         .el-menu-item {
            border-bottom: 2px solid transparent;

            &.is-active {
               border-bottom-color: #409eff;
            }
         }
      }
   }

   .config-content {
      flex: 1;
      display: flex;
      gap: 20px;
      overflow: hidden;

      .config-sidebar {
         width: 240px;
         background: #fff;
         border-radius: 8px;
         overflow: hidden;

         .group-menu {
            border: none;
            height: 100%;

            .el-menu-item {
               height: 48px;
               line-height: 48px;
               margin: 0 10px;
               border-radius: 6px;

               &:hover {
                  background-color: #f5f7fa;
               }

               &.is-active {
                  background-color: #ecf5ff;
                  color: #409eff;
               }

               .el-icon {
                  margin-right: 8px;
                  font-size: 18px;
               }
            }
         }
      }

      .config-main {
         flex: 1;
         background: #fff;
         border-radius: 8px;
         display: flex;
         flex-direction: column;
         overflow: hidden;

         .config-form-container {
            flex: 1;
            overflow-y: auto;
            padding: 20px;

            .group-header {
               display: flex;
               justify-content: space-between;
               align-items: center;
               padding-bottom: 20px;
               border-bottom: 1px solid #e4e7ed;
               margin-bottom: 20px;

               .group-title {
                  display: flex;
                  align-items: center;
                  gap: 10px;
                  font-size: 16px;
                  font-weight: 600;
                  color: #303133;

                  .el-icon {
                     font-size: 20px;
                     color: #409eff;
                  }
               }

               .header-buttons {
                  display: flex;
                  gap: 10px;
               }
            }

            .empty-config {
               padding: 40px 0;
            }

            .config-form {
               .label-tip {
                  margin-left: 4px;
                  color: #909399;
                  cursor: help;
               }

               .form-item-content {
                  display: flex;
                  align-items: flex-start;
                  gap: 10px;

                  .form-input-wrapper {
                     flex: 1;
                     width: 500px;
                  }

                  .delete-btn {
                     margin-top: 4px;
                  }
               }

               .array-editor {
                  width: 100%;

                  .array-item {
                     display: flex;
                     align-items: center;
                     gap: 10px;
                     margin-bottom: 10px;

                     .array-key,
                     .array-value {
                        flex: 1;
                     }

                     .array-separator {
                        color: #909399;
                     }
                  }
               }
            }
         }
      }
   }
}
</style>
