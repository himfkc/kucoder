<template>
  <div class="app-container">
    <el-row :gutter="20">
      <splitpanes :horizontal="appStore.device === 'mobile'" class="default-theme">
        <!--部门数据-->
        <pane size="16" v-if="showDept">
          <el-col>
            <div class="head-container">
              <el-input v-model="dept_name" placeholder="请输入部门名称" clearable prefix-icon="Search"
                style="margin-bottom: 20px" />
            </div>
            <div class="head-container">
              <el-tree :data="deptOptions" :props="{ label: 'dept_name', children: 'children' }"
                :expand-on-click-node="false" :filter-node-method="filterNode" ref="deptTreeRef" node-key="id"
                highlight-current default-expand-all @node-click="handleNodeClick" />
            </div>
          </el-col>
        </pane>
        <!--用户数据-->
        <pane size="84">
          <el-col>
            <!-- 顶部搜索 -->
            <el-form :model="queryParams" ref="queryRef" :inline="true" v-show="showSearch" label-width="68px">
              <el-form-item label="用户名称" prop="username">
                <el-input v-model="queryParams.username" placeholder="请输入用户名称" clearable @keyup.enter="handleQuery"
                  size="small" />
              </el-form-item>
              <el-form-item label="手机号码" prop="mobile">
                <el-input v-model="queryParams.mobile" placeholder="请输入手机号码" clearable @keyup.enter="handleQuery"
                  size="small" />
              </el-form-item>
              <el-form-item label="状态" prop="status">
                <el-select v-model="queryParams.status" placeholder="用户状态" clearable style="width: 120px" size="small">
                  <el-option v-for="dict in enumFieldData.status" :key="dict.value" :label="dict.label"
                    :value="dict.value" />
                </el-select>
              </el-form-item>
              <el-form-item label="创建时间" style="width: 308px">
                <el-date-picker v-model="dateRange" value-format="YYYY-MM-DD" type="daterange" range-separator="-"
                  start-placeholder="开始日期" end-placeholder="结束日期" size="small"></el-date-picker>
              </el-form-item>
              <el-form-item>
                <!-- <el-button size="small" type="primary" icon="Search" @click="handleQuery">搜索</el-button>
                <el-button size="small" icon="Refresh" @click="resetQuery">重置</el-button> -->
                <el-button type="primary" @click="handleQuery" size="small">
                  <icon-ep-search class="icon" />搜索</el-button>
                <el-button @click="resetQuery" size="small">
                  <icon-ep-refresh class="icon" />重置</el-button>
              </el-form-item>
            </el-form>

            <el-row :gutter="10" class="mb8">
              <el-col :span="1.5">
                <el-tooltip effect="dark" content="部门" placement="top">
                  <el-button size="small" type="info" circle plain @click="handleShowDept">
                    <icon-ep-user class=" w-4 " />
                  </el-button>
                </el-tooltip>
              </el-col>
              <el-col :span="1.5">
                <el-tooltip effect="dark" content="刷新" placement="top">
                  <el-button size="small" type="info" circle plain @click="refresh">
                    <icon-ep-refresh class=" w-4 " />
                  </el-button>
                </el-tooltip>
              </el-col>
              <el-col :span="1.5">
                <el-button type="primary" size="small" @click="handleAdd" v-auth:add><icon-ep-plus
                    class="icon" />新增</el-button>
              </el-col>
              <el-col :span="1.5">
                <el-button type="success" size="small" :disabled="single" @click="handleUpdate"
                  v-auth:edit><icon-ep-edit class="icon" />修改</el-button>
              </el-col>
              <el-col :span="1.5">
                <el-button type="danger" size="small" :disabled="multiple" @click="handleDelete"
                  v-auth:delete><icon-ep-delete class="icon" />删除</el-button>
              </el-col>
              <el-col :span="1.5">
                <el-button size="small" type="warning" @click="handleRecycle" v-auth:trueDel>
                  <icon-ep-delete class="icon" />回收站</el-button>
              </el-col>
              <el-col :span="1.5">
                <el-button v-if="queryParams.recycle == 1" type="info" size="small" @click="refresh"><icon-ep-refresh
                    class="icon" />退出回收站</el-button>
              </el-col>
              <el-col :span="1.5">
                <el-button v-if="queryParams.recycle == 1" type="danger" size="small" @click="handleDeleteTrue"
                  v-auth:trueDel><icon-ep-delete class="icon" />彻底删除</el-button>
              </el-col>
              <!-- <el-col :span="1.5">
                <el-button type="info" size="small" @click="handleImport" v-auth:import><icon-ep-upload
                    class="icon" />导入</el-button>
              </el-col>
              <el-col :span="1.5">
                <el-button type="warning" size="small" @click="handleExport" v-auth:export><icon-ep-download
                    class="icon" />导出</el-button>
              </el-col> -->
              <right-toolbar v-model:showSearch="showSearch" @queryTable="getList" :columns="columns"></right-toolbar>
            </el-row>

            <el-table :border="true" v-loading="loading" :data="userList" @selection-change="handleSelectionChange">
              <el-table-column type="selection" width="50" align="center" />
              <el-table-column label="用户编号" align="center" key="user_id" prop="user_id" v-if="columns[0].visible" />
              <el-table-column label="用户名称" align="center" key="username" prop="username" v-if="columns[1].visible"
                :show-overflow-tooltip="true" />
              <el-table-column label="用户昵称" align="center" key="nickname" prop="nickname" v-if="columns[2].visible"
                :show-overflow-tooltip="true" />
              <el-table-column label="用户角色" align="center" key="nickname" prop="role_ids" v-if="columns[7].visible"
                :show-overflow-tooltip="true">
                <template #default="scope">
                  <el-tag v-for="role_id in scope.row.role_ids" :key="role_id" size="small" type="info" class="mr5">{{
                    roleObj[role_id] }}</el-tag>
                </template>
              </el-table-column>
              <el-table-column label="部门" align="center" key="dept_name" prop="dept.dept_name" v-if="columns[3].visible"
                :show-overflow-tooltip="true" />
              <el-table-column label="手机号码" align="center" key="mobile" prop="mobile" v-if="columns[4].visible"
                width="120" />
              <el-table-column label="状态" align="center" key="status" v-if="columns[5].visible">
                <template #default="scope">
                  <el-switch v-model="scope.row.status" :active-value="1" :inactive-value="0"
                    @change="handleStatusChange(scope.row)"></el-switch>
                </template>
              </el-table-column>
              <el-table-column label="创建时间" align="center" prop="create_time" v-if="columns[6].visible" width="160">
              </el-table-column>
              <el-table-column label="操作" align="center" width="400" class-name="">
                <template #default="scope">
                  <!-- 未删除 -->
                  <div v-if="!scope.row.delete_time">
                    <el-button link size="small" type="primary" @click="handleView(scope.row)" v-auth:info><icon-ep-view
                        class="icon" />查看</el-button>
                    <el-button v-if="scope.row.user_id != 1" link size="small" type="success"
                      @click="handleUpdate(scope.row)" v-auth:edit><icon-ep-edit class="icon" />修改</el-button>
                    <el-button v-if="scope.row.user_id != 1" link size="small" type="info"
                      @click="handleResetPwd(scope.row)" v-auth:resetPwd><icon-ep-key class="icon" />重置密码</el-button>
                    <!-- <el-button v-if="scope.row.user_id != 1" size="small" type="warning"
                    @click="handleAuthRole(scope.row)" v-auth="['system:user:edit']"><icon-ep-CircleCheck
                      class="icon" />分配角色</el-button> -->
                    <el-button v-if="scope.row.user_id != 1" link size="small" type="danger"
                      @click="handleDelete(scope.row)" v-auth:delete><icon-ep-delete class="icon" />删除</el-button>
                  </div>
                  <!-- 回收站 -->
                  <div class="" v-else>
                    <el-button link size="small" type="info" @click="handleRestore(scope.row)" v-auth:trueDel
                      v-if="scope.row.role_id != 1">
                      <icon-ep-refreshLeft class="icon" />恢复
                    </el-button>
                    <el-button link size="small" type="danger" @click="handleDeleteTrue(scope.row)" v-auth:trueDel
                      v-if="scope.row.role_id != 1">
                      <icon-ep-delete class="icon" />彻底删除
                    </el-button>
                  </div>
                </template>
              </el-table-column>
            </el-table>
            <pagination v-show="total > 0" :total="total" v-model:page="queryParams.pageNum"
              v-model:limit="queryParams.pageSize" @pagination="getList" />
          </el-col>
        </pane>
      </splitpanes>
    </el-row>

    <!-- 添加或修改用户配置对话框 -->
    <el-dialog :title="title" v-model="open" width="600px" append-to-body draggable>
      <el-form :model="form" :rules="rules" ref="userRef" label-width="80px">
        <el-row>
          <el-col :span="12">
            <el-form-item v-if="form.user_id == undefined" label="用户名" prop="username">
              <el-input v-model="form.username" placeholder="请输入用户名" maxlength="30" />
            </el-form-item>
          </el-col>
          <el-col :span="12">
            <el-form-item v-if="form.user_id == undefined" label="密码" prop="password">
              <el-input v-model="form.password" placeholder="请输入用户密码" type="password" maxlength="20" show-password />
            </el-form-item>
          </el-col>
        </el-row>
        <el-row>
          <el-col :span="24">
            <el-form-item label="角色" prop="role_ids">
              <el-select v-model="form.role_ids" multiple placeholder="请选择">
                <el-option v-for="item in roleOptions" :key="item.role_id" :label="item.role_name" :value="item.role_id"
                  :disabled="item.status == 1"></el-option>
              </el-select>
            </el-form-item>
          </el-col>
        </el-row>
        <el-row>
          <el-col :span="12">
            <el-form-item label="用户昵称" prop="nickname">
              <el-input v-model="form.nickname" placeholder="请输入用户昵称" maxlength="30" />
            </el-form-item>
          </el-col>

          <el-col :span="12">
            <el-form-item label="部门" prop="dept_id">
              <el-tree-select v-model="form.dept_id" :data="enabledDeptOptions"
                :props="{ label: 'dept_name', children: 'children' }" value-key="dept_id" placeholder="请选择归属部门"
                check-strictly />
            </el-form-item>
          </el-col>
        </el-row>
        <el-row>
          <el-col :span="12">
            <el-form-item label="手机" prop="mobile">
              <el-input v-model="form.mobile" placeholder="请输入手机号码" maxlength="11" />
            </el-form-item>
          </el-col>
          <el-col :span="12">
            <el-form-item label="邮箱" prop="email">
              <el-input v-model="form.email" placeholder="请输入邮箱" maxlength="50" />
            </el-form-item>
          </el-col>
        </el-row>

        <el-row>
          <el-col :span="12">
            <el-form-item label="性别">
              <el-select v-model="form.sex" placeholder="请选择">
                <el-option v-for="dict in enumFieldData.sex" :key="dict.value" :label="dict.label"
                  :value="dict.value"></el-option>
              </el-select>
            </el-form-item>
          </el-col>
          <el-col :span="12">
            <el-form-item label="状态">
              <el-radio-group v-model="form.status">
                <el-radio v-for="dict in enumFieldData.status" :key="Number(dict.value)" :value="Number(dict.value)">
                  {{ dict.label }}
                </el-radio>
              </el-radio-group>
            </el-form-item>
          </el-col>
        </el-row>
        <el-row>
          <!-- <el-col :span="12">
            <el-form-item label="岗位">
              <el-select v-model="form.postIds" multiple placeholder="请选择">
                <el-option v-for="item in postOptions" :key="item.postId" :label="item.postName" :value="item.postId"
                  :disabled="item.status == 1"></el-option>
              </el-select>
            </el-form-item>
          </el-col> -->

        </el-row>
        <el-row>
          <el-col :span="24">
            <el-form-item label="备注">
              <el-input v-model="form.remark" type="textarea" placeholder="请输入内容"></el-input>
            </el-form-item>
          </el-col>
        </el-row>
      </el-form>
      <template #footer>
        <div class="dialog-footer">
          <el-button type="primary" @click="submitForm">确 定</el-button>
          <el-button @click="cancel">取 消</el-button>
        </div>
      </template>
    </el-dialog>

    <!-- 用户导入对话框 -->
    <el-dialog :title="upload.title" v-model="upload.open" width="400px" append-to-body>
      <el-upload ref="uploadRef" :limit="1" accept=".xlsx, .xls" :headers="upload.headers"
        :action="upload.url + '?updateSupport=' + upload.updateSupport" :disabled="upload.isUploading"
        :on-progress="handleFileUploadProgress" :on-success="handleFileSuccess" :auto-upload="false" drag>
        <!-- <el-icon class="el-icon--upload"><upload-filled /></el-icon> -->
        <el-icon class="el-icon--upload"><file-upload /></el-icon>
        <div class="el-upload__text">将文件拖到此处，或<em>点击上传</em></div>
        <template #tip>
          <div class="el-upload__tip text-center">
            <div class="el-upload__tip">
              <el-checkbox v-model="upload.updateSupport" />是否更新已经存在的用户数据
            </div>
            <span>仅允许导入xls、xlsx格式文件。</span>
            <el-link type="primary" :underline="false" style="font-size: 12px; vertical-align: baseline"
              @click="importTemplate">下载模板</el-link>
          </div>
        </template>
      </el-upload>
      <template #footer>
        <div class="dialog-footer">
          <el-button type="primary" @click="submitFileForm">确 定</el-button>
          <el-button @click="upload.open = false">取 消</el-button>
        </div>
      </template>
    </el-dialog>

    <!-- 查看详情对话框 -->
    <el-dialog title="用户详情" v-model="openView" width="800px" append-to-body>
      <el-form :model="form" label-width="120px">
        <el-row>
          <el-col :span="24">
            <el-form-item label="用户ID：">{{ form.user_id }}</el-form-item>
          </el-col>
          <el-col :span="24">
            <el-form-item label="用户名：">{{ form.username }}</el-form-item>
          </el-col>
          <el-col :span="24">
            <el-form-item label="用户昵称：">{{ form.nickname || '-' }}</el-form-item>
          </el-col>
          <el-col :span="24">
            <el-form-item label="用户角色：">
              <el-tag v-for="role_id in form.role_ids" :key="role_id" size="small" type="info" class="mr5">
                {{ roleObj[role_id] }}
              </el-tag>
            </el-form-item>
          </el-col>
          <el-col :span="24">
            <el-form-item label="所属部门：">{{ form.dept?.dept_name || '-' }}</el-form-item>
          </el-col>
          <el-col :span="24">
            <el-form-item label="手机号码：">{{ form.mobile || '-' }}</el-form-item>
          </el-col>
          <el-col :span="24">
            <el-form-item label="邮箱：">{{ form.email || '-' }}</el-form-item>
          </el-col>
          <el-col :span="24">
            <el-form-item label="性别：">
              <el-tag v-if="form.sex === 0" type="primary">男</el-tag>
              <el-tag v-else-if="form.sex === 1" type="danger">女</el-tag>
              <el-tag v-else type="info">未知</el-tag>
            </el-form-item>
          </el-col>
          <el-col :span="24">
            <el-form-item label="用户类型：">
              <el-tag v-if="form.user_type === 0" type="success">系统用户</el-tag>
              <el-tag v-else type="warning">注册用户</el-tag>
            </el-form-item>
          </el-col>
          <el-col :span="24">
            <el-form-item label="状态：">
              <el-tag v-if="form.status === 1" type="success">正常</el-tag>
              <el-tag v-else type="danger">停用</el-tag>
            </el-form-item>
          </el-col>
          <el-col :span="24">
            <el-form-item label="最后登录IP：">{{ form.login_ip || '-' }}</el-form-item>
          </el-col>
          <el-col :span="24">
            <el-form-item label="最后登录时间：">{{ form.last_login_time || '-' }}</el-form-item>
          </el-col>
          <el-col :span="24">
            <el-form-item label="密码更新时间：">{{ form.pwd_update_date || '-' }}</el-form-item>
          </el-col>
          <el-col :span="24">
            <el-form-item label="创建时间：">{{ form.create_time || '-' }}</el-form-item>
          </el-col>
          <el-col :span="24">
            <el-form-item label="备注：">{{ form.remark || '-' }}</el-form-item>
          </el-col>
        </el-row>
      </el-form>
      <template #footer>
        <div class="dialog-footer">
          <el-button @click="openView = false">关 闭</el-button>
        </div>
      </template>
    </el-dialog>
  </div>
</template>

<script setup>
import useUserStore from '@/store/modules/user'
import useAppStore from '@/store/modules/app'
import { change, listUser, resetUserPwd, delUser, updateUser, addUser, trueDel, getUserInfo } from "@/api/kucoder/system/user"
import { Splitpanes, Pane } from "splitpanes"
import "splitpanes/dist/splitpanes.css"
import { handleEnumField, clone, array_column } from "@/utils/kucoder"

defineOptions({
  name: 'kucoder_system_user'
})

const appStore = useAppStore()
const { proxy } = getCurrentInstance()
const showDept = ref(false)
const deptTreeRef = useTemplateRef('deptTreeRef')
function handleShowDept() {
  showDept.value = !showDept.value
}

const userList = ref([])
const open = ref(false)
const openView = ref(false)
const loading = ref(true)
const showSearch = ref(true)
const ids = ref([])
const single = ref(true)
const multiple = ref(true)
const total = ref(0)
const title = ref("")
const dateRange = ref([])
const dept_name = ref("")
const deptOptions = ref([])
const enabledDeptOptions = ref([])
const initPassword = ref(undefined)
const postOptions = ref([])
const roleOptions = ref([])
const roleObj = ref({})

/*** 用户导入参数 */
const upload = reactive({
  // 是否显示弹出层（用户导入）
  open: false,
  // 弹出层标题（用户导入）
  title: "",
  // 是否禁用上传
  isUploading: false,
  // 是否更新已经存在的用户数据
  updateSupport: 0,
  // 设置上传的请求头部
  headers: { Authorization: "Bearer " + useUserStore().token },
  // 上传的地址
  url: import.meta.env.VITE_APP_BASE_API + "/system/user/importData"
})
// 列显隐信息
const columns = ref([
  { key: 0, label: `用户编号`, visible: true },
  { key: 1, label: `用户名称`, visible: true },
  { key: 2, label: `用户昵称`, visible: true },
  { key: 7, label: `角色`, visible: true },
  { key: 3, label: `部门`, visible: true },
  { key: 4, label: `手机号码`, visible: true },
  { key: 5, label: `状态`, visible: true },
  { key: 6, label: `创建时间`, visible: true },

])

const data = reactive({
  form: {},
  queryParams: {
    pageNum: 1,
    pageSize: 10,
    username: undefined,
    mobile: undefined,
    status: undefined,
    dept_id: undefined,
    recycle: 0
  },
  rules: {
    username: [{ required: true, message: "用户名称不能为空", trigger: "blur" }, { min: 2, message: "用户名称长度必须大于2 ", trigger: "blur" }],
    // nickname: [{ required: true, message: "用户昵称不能为空", trigger: "blur" }],
    password: [{ required: true, message: "用户密码不能为空", trigger: "blur" }, { min: 6, message: "会员密码长度必须大于等于6位", trigger: "blur" }, { pattern: /^[^<>"'|\\]+$/, message: "不能包含非法字符：< > \" ' \\\ |", trigger: "blur" }],
    email: [{ type: "email", message: "请输入正确的邮箱地址", trigger: ["blur", "change"] }],
    mobile: [{ pattern: /^1[3|4|5|6|7|8|9][0-9]\d{8}$/, message: "请输入正确的手机号码", trigger: "blur" }],
    role_ids: [{ required: true, message: "角色不能为空", trigger: "blur" }],
  }
})

const { queryParams, form, rules } = toRefs(data)

const enumFieldData = ref({})
/** 查询用户列表 */
function getList() {
  loading.value = true
  listUser(proxy.addDateRange(queryParams.value, dateRange.value, 'create_time'))
    .then(({ data, msg, code }) => {
      loading.value = false
      userList.value = data.list.items
      enumFieldData.value = handleEnumField(data.enumFieldData || {})
      total.value = data.total

      // 角色
      roleOptions.value = data.list.roles || []
      roleObj.value = array_column(data.list.roles, 'role_name', 'role_id')
      console.log('roleObj.value', roleObj.value)
      // 部门
      // deptOptions.value = filterDisabledDept(JSON.parse(JSON.stringify(res.data.depts || [])))
      deptOptions.value = JSON.parse(JSON.stringify(data.list.depts || []))
      console.log('deptOtions.value', deptOptions.value)
      enabledDeptOptions.value = data.list.depts || []
      console.log('enabledDeptOptions', enabledDeptOptions.value)
    })
    .catch(err => {
      console.warn(err)
      loading.value = false
    })
}

/** 回收站按钮操作 */
function handleRecycle() {
  queryParams.value.recycle = 1; // 设置查询回收站数据
  getList(); // 重新查询菜单列表
}
// 刷新
function refresh() {
  queryParams.value.pageNum = 1
  queryParams.value.recycle = 0 // 重置回收站查询
  getList()
}
/** 回收站恢复按钮操作 */
function handleRestore(row) {
  const ids = row.user_id || ids.value
  proxy.$modal.confirm('是否确认恢复用户编号为"' + ids + '"的数据项?').then(function () {
    return updateUser({ id: ids, delete_restore: 1 })
  }).then(() => {
    getList()
    proxy.$modal.msgSuccess("恢复成功")
  }).catch(() => { })
}
/** 删除按钮操作 */
function handleDelete(row) {
  const ids = row.user_id || ids.value
  proxy.$modal.confirm('是否确认删除用户编号为"' + ids + '"的数据项？').then(function () {
    return delUser({ id: ids })
  }).then(() => {
    getList()
    proxy.$modal.msgSuccess("删除成功")
  }).catch(() => { })
}
/**回收站彻底删除按钮操作 */
function handleDeleteTrue(row) {
  const ids = row.user_id || ids.value
  proxy.$modal.confirm('是否确认彻底删除用户编号为"' + ids + '"的数据项?').then(function () {
    return trueDel({ id: ids })
  }).then(() => {
    getList()
    proxy.$modal.msgSuccess("彻底删除成功")
  }).catch(() => { })
}

/** 通过条件过滤节点  */
const filterNode = (value, data) => {
  if (!value) return true
  return data.label.indexOf(value) !== -1
}

/** 根据名称筛选部门树 */
watch(dept_name, val => {
  // proxy.$refs["deptTreeRef"].filter(val)
  if (deptTreeRef.value) {
    deptTreeRef.value.filter(val)
  }
})



/** 查询部门下拉树结构 */
/* function getDeptTree() {
  deptTreeSelect().then(response => {
    deptOptions.value = response.data
    enabledDeptOptions.value = filterDisabledDept(JSON.parse(JSON.stringify(response.data)))
  })
} */

/** 过滤禁用的部门 */
function filterDisabledDept(deptList) {
  return deptList.filter(dept => {
    if (dept.disabled) {
      return false
    }
    if (dept.children && dept.children.length) {
      dept.children = filterDisabledDept(dept.children)
    }
    return true
  })
}

/** 节点单击事件 */
function handleNodeClick(data) {
  queryParams.value.dept_id = data.dept_id
  handleQuery()
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
  queryParams.value.dept_id = undefined
  // proxy.$refs.deptTreeRef.setCurrentKey(null)
  if (deptTreeRef.value) {
    deptTreeRef.value.setCurrentKey(null)
  }

  handleQuery()
}



/** 查看详情操作 */
function handleView(row) {
  const id = row.user_id
  getUserInfo(id).then(response => {
    form.value = response.data
    openView.value = true
  })
}

/** 导出按钮操作 */
function handleExport() {
  proxy.download("system/user/export", {
    ...queryParams.value,
  }, `user_${new Date().getTime()}.xlsx`)
}

/** 用户状态修改  */
function handleStatusChange(row) {
  if (queryParams.value.recycle == 1) {
    proxy.$modal.msgWarning("回收站数据不能修改状态")
    row.status = Number(!row.status)
    return false;
  }
  proxy.$modal.confirm('确认要执行操作吗?').then(function () {
    return change({ id: row.user_id, status: row.status })
  }).then(() => {
    proxy.$modal.msgSuccess("操作成功")
  }).catch(function (err) {
    proxy.$modal.msgWarning(err)
    row.status = !row.status
  })
}

/** 更多操作 */
function handleCommand(command, row) {
  switch (command) {
    case "handleResetPwd":
      handleResetPwd(row)
      break
    case "handleAuthRole":
      // handleAuthRole(row)
      break
    default:
      break
  }
}

/** 跳转角色分配 */
/* function handleAuthRole(row) {
  const user_id = row.user_id
  router.push("/system/user-auth/role/" + user_id)
} */

/** 重置密码按钮操作 */
function handleResetPwd(row) {
  ElMessageBox.prompt('请输入"' + row.username + '"的新密码', "提示", {
    confirmButtonText: "确定",
    cancelButtonText: "取消",
    closeOnClickModal: false,
    inputPattern: /^.{6,20}$/,
    inputErrorMessage: "用户密码长度必须介于 6 和 20 之间",
    inputValidator: (value) => {
      if (/<|>|"|'|\||\\/.test(value)) {
        return "不能包含非法字符：< > \" ' \\\ |"
      }
    },
  }).then(({ value }) => {
    resetUserPwd(row.user_id, value).then(() => {
      proxy.$modal.msgSuccess("修改成功")
    })
  }).catch(() => { })
}

/** 选择条数  */
function handleSelectionChange(selection) {
  ids.value = selection.map(item => item.user_id)
  single.value = selection.length != 1
  multiple.value = !selection.length
}

/** 导入按钮操作 */
function handleImport() {
  upload.title = "用户导入"
  upload.open = true
}

/** 下载模板操作 */
function importTemplate() {
  proxy.download("system/user/importTemplate", {
  }, `user_template_${new Date().getTime()}.xlsx`)
}

/**文件上传中处理 */
const handleFileUploadProgress = (event, file, fileList) => {
  upload.isUploading = true
}

/** 文件上传成功处理 */
const handleFileSuccess = (response, file, fileList) => {
  upload.open = false
  upload.isUploading = false
  proxy.$refs["uploadRef"].handleRemove(file)
  proxy.$alert("<div style='overflow: auto;overflow-x: hidden;max-height: 70vh;padding: 10px 20px 0;'>" + response.msg + "</div>", "导入结果", { dangerouslyUseHTMLString: true })
  getList()
}

/** 提交上传文件 */
function submitFileForm() {
  proxy.$refs["uploadRef"].submit()
}

/** 重置操作表单 */
function reset() {
  form.value = {
    user_id: undefined,
    dept_id: undefined,
    username: undefined,
    nickname: undefined,
    password: undefined,
    mobile: undefined,
    email: undefined,
    sex: undefined,
    status: 1,
    remark: undefined,
    postIds: [],
    role_ids: []
  }
  proxy.resetForm("userRef")
}

/** 取消按钮 */
function cancel() {
  open.value = false
  reset()
}

/** 新增按钮操作 */
function handleAdd() {
  reset()
  open.value = true
  title.value = "添加用户"
}

/** 修改按钮操作 */
function handleUpdate(row) {
  form.value = clone(row)
  open.value = true
  title.value = "修改用户"
}

/** 提交按钮 */
function submitForm() {
  proxy.$refs["userRef"].validate(valid => {
    if (valid) {
      if (form.value.user_id != undefined) {
        updateUser(form.value).then(() => {
          proxy.$modal.msgSuccess("修改成功")
          open.value = false
          getList()
        })
      } else {
        addUser(form.value).then(() => {
          proxy.$modal.msgSuccess("新增成功")
          open.value = false
          getList()
        })
      }
    }
  })
}

// getDeptTree()
getList()
</script>
