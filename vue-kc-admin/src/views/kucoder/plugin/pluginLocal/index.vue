<template>
  <div class="app-container">
    <!-- 搜索 -->
    <el-form :model="queryParams" ref="queryRef" v-show="showSearch" :inline="true" label-width="68px">
      <el-form-item label="插件名称" prop="title">
        <el-input v-model="queryParams.title" placeholder="请输入插件名称" clearable @keyup.enter="handleQuery" size="small"
          style="width: 150px" />
      </el-form-item>
      <el-form-item label="状态" prop="status">
        <el-select v-model="queryParams.status" placeholder="插件状态" clearable size="small" style="width: 100px">
          <el-option v-for="dict in enumFieldData.status" :key="dict.value" :label="dict.label" :value="dict.value" />
        </el-select>
      </el-form-item>
      <!-- <el-form-item label="创建时间" style="width: 308px">
          <el-date-picker v-model="dateRange" value-format="YYYY-MM-DD" type="daterange" range-separator="-"
              start-placeholder="开始日期" end-placeholder="结束日期" size="small"></el-date-picker>
      </el-form-item> -->
      <el-form-item>
        <el-button type="primary" @click="handleQuery" size="small">
          <icon-ep-search class="icon" />搜索</el-button>
        <el-button @click="resetQuery" size="small">
          <icon-ep-refresh class="icon" />重置</el-button>
      </el-form-item>
    </el-form>

    <!-- 顶部按钮 -->
    <el-row :gutter="10" class="mb8">
      <el-col :span="1.5">
        <el-tooltip effect="dark" content="刷新" placement="top">
          <el-button size="small" type="info" circle plain @click="refresh">
            <icon-ep-refresh class=" w-4 " />
          </el-button>
        </el-tooltip>
      </el-col>
      <el-col :span="1.5">
        <el-button type="primary" size="small" @click="showImportLocalPlugin" v-auth:importLocalPlugin>
          <icon-ep-download class="icon" />导入本地调试插件</el-button>
      </el-col>
      <el-col :span="1.5">
        <kc-login ref="kcLoginRef" class="mb-2 ml-3" @success="kcLoginSuccess"></kc-login>
      </el-col>

      <right-toolbar v-model:showSearch="showSearch" @queryTable="getList"></right-toolbar>
    </el-row>

    <!-- 表格数据 -->
    <el-table :border="true" v-loading="loading" element-loading-text="数据加载中，请稍候..." :data="roleList" width="100%">
      <!-- <el-table-column type="selection" width="55" align="center" /> -->
      <!-- <el-table-column label="插件ID" prop="id" width="80" align="center" /> -->
      <el-table-column label="插件名" prop="title" :show-overflow-tooltip="true" width="180" align="center" />
      <el-table-column label="类型" prop="plugin_type" align="center" width="120">
        <template #default="scope">
          <dict-tag :options="enumFieldData.plugin_type" :value="scope.row.plugin_type" />
        </template>
      </el-table-column>

      <el-table-column label="简介" prop="desc" :show-overflow-tooltip="true" width="300" align="center" />
      <!-- <el-table-column label="图片" prop="img" align="center" /> -->
      <el-table-column label="标识" prop="name" :show-overflow-tooltip="true" width="80" align="center" />
      <el-table-column label="版本" prop="version" width="100" align="center">
        <template #default="scope">
          <el-tooltip v-if="scope.row.version_has_update" class="box-item" effect="dark"
            :content="`${scope.row.title} 有新版本 ${scope.row.version_latest}`" placement="top-start">
            <el-badge :is-dot="scope.row.version_has_update" :offset="[10, 5]">
              <el-tag size="small" type="info">{{ scope.row.version }}</el-tag>
            </el-badge>
          </el-tooltip>
          <el-tag v-else size="small" type="info">{{ scope.row.version }}</el-tag>
        </template>
      </el-table-column>
      <el-table-column label="作者" prop="author" width="100" align="center" :show-overflow-tooltip="true" />

      <!-- <el-table-column label="文档" prop="doc_id" align="center" /> -->

      <!-- <el-table-column label="过期时间" prop="expire_time" align="center" /> -->
      <!-- <el-table-column label="创建时间" align="center" prop="create_time" width="100" :show-overflow-tooltip="true"></el-table-column> -->

      <el-table-column label="主页" prop="homepage" width="80" align="center">
        <template #default="scope">
          <div class="x a">
            <el-link :href="scope.row.homepage" target="_blank" type="primary">
              <el-tag size="small" effect="dark">
                <!-- <icon-ep-house class="icon" /> -->
                主页
              </el-tag>
            </el-link>
            <!-- <el-link :href="scope.row.homepage" target="_blank" type="warning">
                <el-tag size="small" type="warning" effect="dark">
                    <icon-ep-fullScreen class="icon font-100" />
                    演示
                </el-tag>
            </el-link> -->
          </div>
        </template>
      </el-table-column>
      <el-table-column label="启用" align="center" width="100">
        <template #default="scope">
          <el-switch v-if="scope.row.install === 1" v-model="scope.row.status" :active-value="1" :inactive-value="0"
            @change="handleChange(scope.row)"></el-switch>
          <el-tag v-else-if="scope.row.download === 0" type="info">未加载</el-tag>
          <el-tag v-else type="info">未安装</el-tag>
        </template>
      </el-table-column>
      <el-table-column label="操作" align="center" class-name="">
        <template #default="scope">
          <div class="x c ac gap-3 ">
            <!-- 升级 -->
            <el-dropdown size="small" split-button trigger="click" :hide-on-click="false" type="info"
              v-if="scope.row.install === 1 && scope.row.version_has_update" v-auth:update>
              升级
              <template #dropdown>
                <el-dropdown-menu>
                  <el-dropdown-item v-for="(item, key) in scope.row.versions" :divided="key > 0">
                    <el-badge :is-dot="true" v-if="item.compare === 1">
                      <el-tag size="small" effect="light" type='success' @click="updatePlugin(scope.row, item)">版本{{
                        item.version }}</el-tag>
                    </el-badge>
                    <el-tag v-else size="small" effect="light" :type="item.compare === 0 ? 'primary' : 'info'"
                      @click="updatePlugin(scope.row, item)">版本{{ item.version }}</el-tag>
                  </el-dropdown-item>
                </el-dropdown-menu>
              </template>
            </el-dropdown>
            <!-- 安装 -->
            <el-tag type="primary" size="small" effect="dark" v-if="scope.row.install === 0" v-auth:install
              class="cursor-pointer" @click="installPlugin(scope.row)">安装</el-tag>
            <!-- 配置 -->
            <el-tag type="info" size="small" effect="dark" v-if="scope.row.install === 1 && scope.row.source === 2"
              class="cursor-pointer" @click="updatePlugin(scope.row)" v-auth:update>升级</el-tag>
            <el-tag type="info" size="small" effect="dark" v-if="scope.row.install === 1" class="cursor-pointer"
              @click="configPlugin(scope.row)">配置</el-tag>
            <el-tag type="danger" size="small" effect="dark" v-if="scope.row.install === 1" v-auth:uninstall
              class="cursor-pointer" @click="uninstallPlugin(scope.row)">卸载</el-tag>
          </div>

        </template>

      </el-table-column>
    </el-table>

    <pagination v-show="total > 0" :total="total" v-model:page="queryParams.pageNum"
      v-model:limit="queryParams.pageSize" @pagination="getList" />

    <!-- 卸载插件 -->
    <el-dialog :title="uninstallTitle" v-model="uninstallDialog" width="500px" append-to-body draggable>
      <div class="y c gap-4">
        <span>⚠️卸载将删除插件数据库、删除插件文件、删除插件菜单、移除插件依赖</span>
        <span>⚠️确保重要数据已备份再执行卸载操作❗ </span>
      </div>
      <template #footer>
        <div class="dialog-footer">
          <el-button @click="uninstallDialog = false">取 消</el-button>
          <el-button type="primary" @click="uninstallSubmit">确 定</el-button>
        </div>
      </template>
    </el-dialog>

    <!-- 升级插件 -->
    <el-dialog :title="updateTitle" v-model="updateDialog" width="500px" append-to-body draggable>
      <div class="y c gap-4">
        <span>⚠️禁止跨多个版本升级，推荐逐个版本升级</span>
        <span>⚠️确保重要数据已备份再执行升级 </span>
      </div>
      <template #footer>
        <div class="dialog-footer">
          <el-button @click="updateDialog = false">取 消</el-button>
          <el-button type="primary" @click="updateSubmit">确 定</el-button>
        </div>
      </template>
    </el-dialog>

    <!-- 导入本地调试插件 -->
    <el-dialog title="上传插件" v-model="importLocalPluginDialog" width="500px" append-to-body draggable center>
      <div class="m-t-5">
        <file-upload text="导入本地调试的zip插件" v-model="uploadedFiles" :limit="1" :fileType="['zip']" :data="extraData"
          @upload-after="uploadAfter" @close="importLocalPluginDialog = false">
        </file-upload>
      </div>
      <template #footer>
        <el-button type="info" @click="importLocalPluginDialog = false">取消</el-button>
        <el-button type="primary" @click="importSubmit">确认 开始导入</el-button>
      </template>
    </el-dialog>

    <!-- 配置插件 -->
    <el-dialog :title="configTitle" v-model="configDialog" width="70%" append-to-body draggable>
      <ConfigVue :my-plugin="configRow.name"></ConfigVue>
    </el-dialog>

    <!-- 微信校验 -->
    <el-dialog title="安装校验" v-model="weixinDialog" width="600px" append-to-body draggable center>
      <div class="body y c ac gap-5">
        <div class="x as gap-5">
          <img :src="qrcode" alt="">
          <div class="m-t-3 y gap-5">
            <p>微信扫码并关注公众号后 回复“安装插件”</p>
            <p>将得到的6位数字校验码 填入下方</p>
            <el-form-item label="校验码">
              <el-input v-model="wx_code" placeholder="请输入6位数字校验码" />
            </el-form-item>
          </div>
        </div>
      </div>
      <template #footer>
        <el-button type="info" @click="weixinDialog = false">取消</el-button>
        <el-button type="primary" @click="installSubmit">开始安装</el-button>
      </template>
    </el-dialog>
  </div>
</template>

<script setup>
import { list, install, uninstall, update, importPlugin } from "@/api/kucoder/plugin/pluginLocal"
import { getQrcode, verifyWxCode } from '@/api/kucoder/system/install'
import { handleEnumField, kcLoading, kcAlert, kcMsg, cloneDeep, join_path } from "@/utils/kucoder"
import usePermissionStore from "@/store/modules/permission"
import useUserStore from "@/store/modules/user"
import { HMR } from "@/utils/hmr"
import ConfigVue from "@/views/kucoder/system/config/config/index.vue"

const userStore = useUserStore()
const permissionStore = usePermissionStore()
defineOptions({
  name: 'kucoder_plugin_pluginLocal'
})
const { proxy } = getCurrentInstance()
const roleList = ref([])
const loading = ref(true)
const showSearch = ref(true)
const total = ref(0)
const dateRange = ref([])
const enumFieldData = ref({})
const data = reactive({
  form: {},
  queryParams: {
    pageNum: 1,
    pageSize: 10,
    title: undefined,
    status: undefined,
    recycle: 0, // 是否查询回收站数据
  },
  rules: {
    title: [{ required: true, message: "插件名称不能为空", trigger: "blur" }],
  },
})
const { queryParams, form, rules } = toRefs(data)

// 登录kucoder
const kcLoginRef = useTemplateRef('kcLoginRef')
const kcLoginSuccess = (data) => {
  console.log('登录成功', data)
  console.log('userStore', userStore)
  getList();
}

// 插件配置
const configTitle = ref('')
const configDialog = ref(false)
const configRow = ref({})
const configPlugin = (plugin) => {
  console.log('配置插件参数');
  configTitle.value = '插件配置: ' + plugin.title
  configRow.value = plugin
  configDialog.value = true
}

// 上传本地插件
const importLocalPluginDialog = ref(false)
function showImportLocalPlugin() {
  importLocalPluginDialog.value = true
}
const uploadedFiles = ref([]) // 上传的文件
const extraData = ref({ plugin: 'kucoder', saveDir: 'zip' }) // 上传额外携带的数据
const uploadAfter = (res) => {
  console.log('uploadRes', res) //res.data = {file: {name: 'pay_1.0.0.zip', url: 'kucoder/upload/20251103/pay_1.0.0.zip',savePath:''}}
  zipFileObj.value = res.data.file
}
// 导入插件
const zipFileObj = ref({})
const importSubmit = () => {
  if (!zipFileObj.value) {
    kcAlert('请先上传插件', '请先上传插件', { type: 'warning' })
    return
  }
  importPlugin(zipFileObj.value, { timeout: 0 })
    .then(res => {
      console.log('importPlugin', res)
      kcAlert('导入成功', '导入成功', { type: 'success' })
      uploadedFiles.value = []
      importLocalPluginDialog.value = false
      getList()
    })
    .catch(err => {
      console.log('importPlugin', err)
      kcAlert(err.msg, '导入失败', { type: 'warning' })
    })
}

/** 查询插件列表 */
function getList() {
  console.log('getList')
  console.log('import.meta.hot', import.meta.hot)
  console.log('HMR.getStatus', HMR.getStatus())
  loading.value = true
  list(proxy.addDateRange(queryParams.value, dateRange.value, 'create_time'))
    .then(({ res }) => {
      roleList.value = res.list
      total.value = res.total
      enumFieldData.value = handleEnumField(res.enumFieldData || {})
      loading.value = false
    })
    .catch(err => {
      console.warn(err)
      loading.value = false
    })
}

// 刷新
function refresh() {
  queryParams.value.pageNum = 1
  queryParams.value.recycle = 0 // 重置回收站查询
  getList()
}

// 安装插件
const installRow = ref({})
const weixinDialog = ref(false)
const qrcode = ref('')
const wx_code = ref('')
function installPlugin(row) {
  installRow.value = cloneDeep(row)
  getQrcode().then(res => {
    console.log('weixin', res)
    qrcode.value = res.data.qrcode
  })
  weixinDialog.value = true
}
async function checkWxCode() {
  if (!wx_code.value || !/^[0-9]{6}$/.test(wx_code.value)) {
    kcMsg('校验码不正确')
    return false
  }
  return await verifyWxCode({ wx_code: wx_code.value })
    .then(res => {
      weixinDialog.value = false
      return true
    })
    .catch(err => {
      // kcAlert(err.msg)
      return false
    })
}
async function installSubmit() {
  /*const isCheckedWxCode = await checkWxCode()
  if (!isCheckedWxCode) {
    return
  }*/
  await HMR.disable()
  const loading = kcLoading('正在安装中...请勿操作 执行步骤可在后端控制台查看')
  install(installRow.value, { timeout: 0, headers: { showErrMsg: false } })
    .then(async ({ res, msg, code }) => {
      enableHmr(msg, '安装成功', loading)
    })
    .catch(async (err) => {
      console.log(err)
      await HMR.enable()
      loading.close()
      kcAlert(err.msg, '安装失败', { type: 'warning' })
    })
}

// 卸载插件
const uninstallTitle = ref('')
const uninstallDialog = ref(false)
const uninstallRow = ref({})
function uninstallPlugin(row) {
  uninstallTitle.value = '确认卸载插件：' + row.title + ' ?'
  uninstallRow.value = cloneDeep(row)
  uninstallDialog.value = true
}
async function uninstallSubmit() {
  await HMR.disable()
  uninstallDialog.value = false
  const loading = kcLoading('正在卸载中...请勿操作 执行步骤可在后端控制台查看')
  uninstall(uninstallRow.value, { timeout: 0, headers: { showErrMsg: false } })
    .then(async ({ res, msg, code }) => {
      enableHmr(msg, '卸载完成', loading)
    })
    .catch(async (err) => {
      console.log(err)
      await HMR.enable()
      loading.close()
      kcAlert(err.msg, '卸载失败', { type: 'warning' })
    })
}

// 升级插件
const updateTitle = ref('')
const updateDialog = ref(false)
const updateRow = ref({})
function updatePlugin(row, item = {}) {
  if (row.source === 1) {
    if (item.compare === 0) {
      kcAlert('⚠️要升级的版本正在使用中，无需升级!', '', { type: 'warning' })
      return
    }
    if (Number(item.compare) === -1) {
      kcAlert(`⚠️要升级的版本${item.version}低于当前版本${row.version}，属于降级操作，请谨慎操作，切记做好数据备份!`,
        '降级插件', {
        showCancelButton: true,
        confirmButtonText: '执意降级',
        cancelButtonText: '取消',
        type: 'warning'
      })
        .then(async (action) => {
          if (action === 'confirm') {
            kcAlert(`⚠️先卸载当前版本${row.version}, 再安装低版本${item.version}，切记做好数据备份!`,
              '降级插件', {
              showCancelButton: true,
              confirmButtonText: '确认',
              cancelButtonText: '取消',
              type: 'warning'
            })
          }
        })
        .catch(async (action) => {
          console.log('取消 cancel', action)
        })
    }
    if (Number(item.compare) === 1) {
      // updateTitle.value = '确认升级插件：' + row.title + ' 到新版本 ?'
      updateTitle.value = `确认升级插件：${row.title} 到版本${item.version} ?`
      updateRow.value = cloneDeep(row)
      updateRow.value.version = item.version
      updateDialog.value = true
    }
  }
  // 本地调试插件升级
  if (row.source === 2) {
    kcAlert(`⚠️本地插件升级，需提前导入新版本的本地插件!`, '',
      { type: 'warning', confirmButtonText: '确认已导入 开始升级', })
      .then(() => {
        updateTitle.value = `确认升级插件：${row.title} 到版本${row.version} ?`
        updateRow.value = cloneDeep(row)
        updateDialog.value = true
      })
  }

}
async function updateSubmit() {
  await HMR.disable()
  updateDialog.value = false
  const loading = kcLoading('正在升级中...请勿操作 执行步骤可在后端控制台查看')
  update(updateRow.value, { timeout: 0, headers: { showErrMsg: false } })
    .then(async ({ res, msg, code }) => {
      enableHmr(msg, '升级完成', loading)
    })
    .catch(async (err) => {
      console.log(err)
      await HMR.enable()
      loading.close()
      kcAlert(err.msg, '升级失败', { type: 'warning' })
    })
}
// 启用hmr
async function enableHmr(msg, title, loading) {
  await refreshRoute()
  await HMR.enable()
  setTimeout(() => {
    loading.close()
    kcAlert(msg, title, { showClose: false })
      .then(() => {
        window.location.reload()
      })
      .catch(err => {
        loading.close()
      })
  }, 1000)
}

// 初始化启用hmr
HMR.getStatus().then(res => {
  console.log('hmr', res, typeof res)
  if (res === false) {
    HMR.enable()
  }
})
// 刷新路由
async function refreshRoute() {
  permissionStore.routes = [];
  await permissionStore.generateRoutes()
    .then(routes => {
      console.log("更新menu后的新路由", routes);
    });
}

/** 搜索按钮操作 */
function handleQuery() {
  queryParams.value.pageNum = 1
  getList()
}

/** 重置按钮操作 */
function resetQuery() {
  dateRange.value = []
  proxy.resetForm("queryRef")
  handleQuery()
}

function handleChange(row) {
  proxy.$modal.confirm('确认要执行操作吗?').then(function () {
    return change({ id: row.id, status: row.status })
  }).then(() => {
    proxy.$modal.msgSuccess("操作成功")
  }).catch(function (err) {
    proxy.$modal.msgWarning(err)
    row.status = !row.status
  })
}

getList()
</script>