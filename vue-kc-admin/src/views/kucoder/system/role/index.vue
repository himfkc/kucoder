<template>
  <div class="app-container">
    <!-- 搜索 -->
    <el-form :model="queryParams" ref="queryRef" v-show="showSearch" :inline="true" label-width="68px">
      <el-form-item label="角色名称" prop="role_name">
        <el-input v-model="queryParams.role_name" placeholder="请输入角色名称" clearable @keyup.enter="handleQuery"
          size="small" style="width: 150px" />
      </el-form-item>
      <!-- <el-form-item label="权限字符" prop="role_key">
        <el-input v-model="queryParams.role_key" placeholder="请输入权限字符" clearable size="small"
          @keyup.enter="handleQuery" />
      </el-form-item> -->
      <el-form-item label="状态" prop="status">
        <el-select v-model="queryParams.status" placeholder="角色状态" clearable size="small" style="width: 100px">
          <el-option v-for="dict in enumFieldData.status" :key="dict.value" :label="dict.label" :value="dict.value" />
        </el-select>
      </el-form-item>
      <el-form-item label="创建时间" style="width: 308px">
        <el-date-picker v-model="dateRange" value-format="YYYY-MM-DD" type="daterange" range-separator="-"
          start-placeholder="开始日期" end-placeholder="结束日期" size="small"></el-date-picker>
      </el-form-item>
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
        <el-button type="primary" size="small" @click="handleAdd" v-auth:add>
          <icon-ep-plus class="icon" />新增</el-button>
      </el-col>
      <el-col :span="1.5">
        <el-button type="success" size="small" :disabled="single" @click="handleUpdate" v-auth:edit>
          <icon-ep-edit class="icon" />修改</el-button>
      </el-col>
      <el-col :span="1.5">
        <el-button type="danger" size="small" :disabled="multiple || queryParams.recycle == 1" @click="handleDelete"
          v-auth:delete>
          <icon-ep-delete class="icon" />删除</el-button>
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
        <el-button type="warning" size="small" @click="handleExport" v-auth="['system:role:export']">
          <icon-ep-download class="icon" />导出</el-button>
      </el-col> -->
      <right-toolbar v-model:showSearch="showSearch" @queryTable="getList"></right-toolbar>
    </el-row>

    <!-- 表格数据 -->
    <el-table :border="true" v-loading="loading" :data="roleList" @selection-change="handleSelectionChange">
      <el-table-column type="selection" width="55" align="center" />
      <el-table-column label="角色ID" prop="role_id" width="80" align="center" />
      <el-table-column label="角色名称" prop="role_name" :show-overflow-tooltip="true" width="150" />
      <!-- <el-table-column label="权限字符" prop="role_key" :show-overflow-tooltip="true" width="150" align="center" /> -->
      <el-table-column label="显示顺序" prop="sort" width="100" align="center" />
      <el-table-column label="状态" align="center" width="100">
        <template #default="scope">
          <el-switch v-if="scope.row.role_id != 1" v-model="scope.row.status" :active-value="1" :inactive-value="0"
            @change="handleChange(scope.row)"></el-switch>
        </template>
      </el-table-column>
      <el-table-column label="创建时间" align="center" prop="create_time" width="160"></el-table-column>
      <!-- <el-table-column label="操作" align="center" class-name="small-padding fixed-width"> -->
      <el-table-column label="操作" align="center" class-name="">
        <template #default="scope">
          <!-- 未删除 -->
          <div v-if="!scope.row.delete_time">
            <el-button link size="small" type="success" @click="handleUpdate(scope.row)" v-auth:edit
              v-if="scope.row.role_id != 1">
              <icon-ep-edit class="icon" />修改
            </el-button>
            <el-button link size="small" type="warning" @click="handleDataScope(scope.row)" v-auth:edit
              v-if="scope.row.role_id != 1">
              <icon-ep-circleCheck class="icon" />数据权限
            </el-button>
            <!-- <el-button size="small" type="info" @click="handleAuthUser(scope.row)" v-auth:edit
              v-if="scope.row.role_id != 1">
              <icon-ep-user class="icon" />分配用户
            </el-button> -->
            <el-button link size="small" type="danger" @click="handleDelete(scope.row)" v-auth:delete
              v-if="scope.row.role_id != 1">
              <icon-ep-delete class="icon" />删除
            </el-button>
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

    <!-- 添加或修改角色配置对话框 -->
    <el-dialog :title="title" v-model="open" width="500px" append-to-body>
      <el-form ref="roleRef" :model="form" :rules="rules" label-width="100px">
        <el-form-item label="角色名称" prop="role_name">
          <el-input v-model="form.role_name" placeholder="请输入角色名称" />
        </el-form-item>
        <!-- <el-form-item prop="role_key">
          <template #label>
            <span>
              <el-tooltip content="控制器中定义的权限字符，如：@PreAuthorize(`@ss.hasRole('admin')`)" placement="top">
                <el-icon><question-filled /></el-icon>
              </el-tooltip>
              权限字符
            </span>
          </template>
          <el-input v-model="form.role_key" placeholder="请输入权限字符" />
        </el-form-item> -->
        <el-form-item label="角色顺序" prop="sort">
          <el-input-number v-model="form.sort" controls-position="right" :min="1" />
        </el-form-item>
        <el-form-item label="状态">
          <el-radio-group v-model="form.status">
            <el-radio v-for="dict in enumFieldData.status" :key="dict.value" :value="dict.value">{{ dict.label
            }}</el-radio>
          </el-radio-group>
        </el-form-item>
        <el-form-item label="菜单权限">
          <el-checkbox v-model="menuExpand" @change="handleCheckedTreeExpand($event, 'menu')">展开/折叠</el-checkbox>
          <el-checkbox v-model="menuNodeAll" @change="handleCheckedTreeNodeAll($event, 'menu')">全选/全不选</el-checkbox>
          <el-checkbox v-model="form.menuCheckStrictly"
            @change="handleCheckedTreeConnect($event, 'menu')">父子联动</el-checkbox>
          <el-tree class="tree-border" :data="menuOptions" show-checkbox ref="menuRef" node-key="id"
            :check-strictly="!form.menuCheckStrictly" empty-text="加载中，请稍候"
            :props="{ label: 'title', children: 'children' }"></el-tree>
        </el-form-item>
        <el-form-item label="备注">
          <el-input v-model="form.remark" type="textarea" placeholder="请输入内容"></el-input>
        </el-form-item>
      </el-form>
      <template #footer>
        <div class="dialog-footer">
          <el-button type="primary" @click="submitForm">确 定</el-button>
          <el-button @click="cancel">取 消</el-button>
        </div>
      </template>
    </el-dialog>

    <!-- 分配角色数据权限对话框 -->
    <el-dialog :title="title" v-model="openDataScope" width="500px" append-to-body draggable>
      <el-form :model="form" label-width="80px">
        <el-form-item label="角色名称">
          <el-input v-model="form.role_name" :disabled="true" />
        </el-form-item>
        <!-- <el-form-item label="权限字符">
          <el-input v-model="form.role_key" :disabled="true" />
        </el-form-item> -->
        <el-form-item label="权限范围">
          <el-select v-model="form.dataScope" @change="dataScopeSelectChange">
            <el-option v-for="item in dataScopeOptions" :key="item.value" :label="item.label"
              :value="item.value"></el-option>
          </el-select>
        </el-form-item>
        <el-form-item label="数据权限" v-show="form.dataScope == 2">
          <el-checkbox v-model="deptExpand" @change="handleCheckedTreeExpand($event, 'dept')">展开/折叠</el-checkbox>
          <el-checkbox v-model="deptNodeAll" @change="handleCheckedTreeNodeAll($event, 'dept')">全选/全不选</el-checkbox>
          <el-checkbox v-model="form.deptCheckStrictly"
            @change="handleCheckedTreeConnect($event, 'dept')">父子联动</el-checkbox>
          <el-tree class="tree-border" :data="deptOptions" show-checkbox default-expand-all ref="deptRef" node-key="id"
            :check-strictly="!form.deptCheckStrictly" empty-text="加载中，请稍候"
            :props="{ label: 'label', children: 'children' }"></el-tree>
        </el-form-item>
      </el-form>
      <template #footer>
        <div class="dialog-footer">
          <el-button type="primary" @click="submitDataScope">确 定</el-button>
          <el-button @click="cancelDataScope">取 消</el-button>
        </div>
      </template>
    </el-dialog>
  </div>
</template>

<script setup>
import { addRole, change, dataScope, delRole, getRole, listRole, updateRole, deptTreeSelect, trueDel } from "@/api/kucoder/system/role"
import { roleMenuTreeselect, treeselect as menuTreeselect } from "@/api/kucoder/system/menu"
import { handleEnumField, clone } from "@/utils/kucoder"
import usePermissionStore from "@/store/modules/permission"

defineOptions({
  name: 'kucoder_system_role'
})

const router = useRouter()
const { proxy } = getCurrentInstance()

const roleList = ref([])
const open = ref(false)
const loading = ref(true)
const showSearch = ref(true)
const ids = ref([])
const single = ref(true)
const multiple = ref(true)
const total = ref(0)
const title = ref("")
const dateRange = ref([])

const menuExpand = ref(false)
const menuNodeAll = ref(false)
const deptExpand = ref(true)
const deptNodeAll = ref(false)
const deptOptions = ref([])
const openDataScope = ref(false)
const menuRef = ref(null)
const deptRef = ref(null)

const permissionStore = usePermissionStore()
const menuOptions = ref(permissionStore.roleMenus)
const enumFieldData = ref({})

/** 数据范围选项*/
const dataScopeOptions = ref([
  { value: "1", label: "全部数据权限" },
  { value: "2", label: "自定数据权限" },
  { value: "3", label: "本部门数据权限" },
  { value: "4", label: "本部门及以下数据权限" },
  { value: "5", label: "仅本人数据权限" }
])

const data = reactive({
  form: {},
  queryParams: {
    pageNum: 1,
    pageSize: 10,
    role_name: undefined,
    // role_key: undefined,
    status: undefined,
    recycle: 0, // 是否查询回收站数据
  },
  rules: {
    role_name: [{ required: true, message: "角色名称不能为空", trigger: "blur" }],
    // role_key: [{ required: true, message: "权限字符不能为空", trigger: "blur" }],
    // sort: [{ required: true, message: "角色顺序不能为空", trigger: "blur" }]
    // rules: [{ required: true, message: "角色菜单权限不能为空", trigger: "blur" }]
  },
})

const { queryParams, form, rules } = toRefs(data)

/** 查询角色列表 */
function getList() {
  loading.value = true
  listRole(proxy.addDateRange(queryParams.value, dateRange.value, 'create_time'))
    .then(({ data, msg, code }) => {
      roleList.value = data.list
      total.value = data.total
      enumFieldData.value = handleEnumField(data.enumFieldData || {})
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
/** 回收站按钮操作 */
function handleRecycle() {
  queryParams.value.recycle = 1; // 设置查询回收站数据
  getList(); // 重新查询菜单列表
}
/** 回收站恢复按钮操作 */
function handleRestore(row) {
  const role_ids = row.role_id || ids.value
  proxy.$modal.confirm('是否确认恢复角色编号为"' + role_ids + '"的数据项?').then(function () {
    return updateRole({ id: role_ids, delete_restore: 1 })
  }).then(() => {
    getList()
    proxy.$modal.msgSuccess("恢复成功")
  }).catch(() => { })
}
/**回收站彻底删除按钮操作 */
function handleDeleteTrue(row) {
  const role_ids = row.role_id || ids.value
  proxy.$modal.confirm('是否确认彻底删除角色编号为"' + role_ids + '"的数据项?').then(function () {
    return trueDel({ id: role_ids })
  }).then(() => {
    getList()
    proxy.$modal.msgSuccess("彻底删除成功")
  }).catch(() => { })
}
/** 删除按钮操作 */
function handleDelete(row) {
  const role_ids = row.role_id || ids.value
  proxy.$modal.confirm('是否确认删除角色编号为"' + role_ids + '"的数据项?').then(function () {
    return delRole({ id: role_ids })
  }).then(() => {
    getList()
    proxy.$modal.msgSuccess("删除成功")
  }).catch(() => { })
}

/** 角色状态修改 */
function handleChange(row) {
  if (queryParams.value.recycle == 1) {
    proxy.$modal.msgWarning("回收站数据不能修改状态")
    row.status = Number(!row.status)
    return false;
  }
  proxy.$modal.confirm('确认要执行操作吗?').then(function () {
    return change({ id: row.role_id, status: row.status })
  }).then(() => {
    proxy.$modal.msgSuccess("操作成功")
  }).catch(function (err) {
    proxy.$modal.msgWarning(err)
    row.status = !row.status
  })
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



/** 导出按钮操作 */
/* function handleExport() {
  proxy.download("system/role/export", {
    ...queryParams.value,
  }, `role_${new Date().getTime()}.xlsx`)
} */

/** 多选框选中数据 */
function handleSelectionChange(selection) {
  ids.value = selection.map(item => item.role_id)
  single.value = selection.length != 1
  multiple.value = !selection.length
}



/** 更多操作 */
function handleCommand(command, row) {
  switch (command) {
    case "handleDataScope":
      handleDataScope(row)
      break
    case "handleAuthUser":
      // handleAuthUser(row)
      break
    default:
      break
  }
}

/** 分配用户 */
/* function handleAuthUser(row) {
  router.push("/system/role-auth/user/" + row.role_id)
} */

/** 查询菜单树结构 */
/* function getMenuTreeselect() {
  menuTreeselect().then(res => {
    menuOptions.value = res.data
  })
} */

/** 所有部门节点数据 */
function getDeptAllCheckedKeys() {
  // 目前被选中的部门节点
  let checkedKeys = deptRef.value.getCheckedKeys()
  // 半选中的部门节点
  let halfCheckedKeys = deptRef.value.getHalfCheckedKeys()
  checkedKeys.unshift.apply(checkedKeys, halfCheckedKeys)
  return checkedKeys
}

/** 重置新增的表单以及其他数据  */
function reset() {
  if (menuRef.value != undefined) {
    menuRef.value.setCheckedKeys([])
  }
  menuExpand.value = false
  menuNodeAll.value = false
  deptExpand.value = true
  deptNodeAll.value = false
  form.value = {
    role_id: undefined,
    role_name: undefined,
    // role_key: undefined,
    sort: 999,
    status: 1,
    rules: [],
    deptIds: [],
    menuCheckStrictly: true,
    deptCheckStrictly: true,
    remark: undefined
  }
  proxy.resetForm("roleRef")
}

/** 添加角色 */
function handleAdd() {
  reset()
  // getMenuTreeselect()
  open.value = true
  title.value = "添加角色"
}

/** 修改角色 */
function handleUpdate(row) {
  console.log('修改角色', row)
  // reset()
  // const role_id = row.role_id || ids.value
  /* const roleMenu = getRoleMenuTreeselect(role_id)
  getRole(role_id).then(res => {
    form.value = res.data
    form.value.sort = Number(form.value.sort)
    open.value = true
    nextTick(() => {
      roleMenu.then((res) => {
        let checkedKeys = res.checkedKeys
        checkedKeys.forEach((v) => {
          nextTick(() => {
            menuRef.value.setChecked(v, true, false)
          })
        })
      })
    })
  }) */

  form.value = clone(row)


  open.value = true
  nextTick(() => {
    console.log('menuRef.value', menuRef.value)
    menuRef.value.setCheckedKeys(row.rules)
    form.value.menuCheckStrictly = true  //父子联动
  })
  title.value = "修改角色"
}

/** 根据角色ID查询菜单树结构 */
/* function getRoleMenuTreeselect(role_id) {
  return roleMenuTreeselect(role_id).then(({data,msg,code}) => {
    menuOptions.value = data.menus
    return data
  })
} */

/** 根据角色ID查询部门树结构 */
/* function getDeptTree(role_id) {
  return deptTreeSelect(role_id).then(({data,msg,code}) => {
    deptOptions.value = data.depts
    return data
  })
} */

/** 树权限（展开/折叠）*/
function handleCheckedTreeExpand(value, type) {
  if (type == "menu") {
    let treeList = menuOptions.value
    for (let i = 0; i < treeList.length; i++) {
      menuRef.value.store.nodesMap[treeList[i].id].expanded = value
    }
  } else if (type == "dept") {
    let treeList = deptOptions.value
    for (let i = 0; i < treeList.length; i++) {
      deptRef.value.store.nodesMap[treeList[i].id].expanded = value
    }
  }
}

/** 树权限（全选/全不选） */
function handleCheckedTreeNodeAll(value, type) {
  if (type == "menu") {
    menuRef.value.setCheckedNodes(value ? menuOptions.value : [])
  } else if (type == "dept") {
    deptRef.value.setCheckedNodes(value ? deptOptions.value : [])
  }
}

/** 树权限（父子联动） */
function handleCheckedTreeConnect(value, type) {
  if (type == "menu") {
    form.value.menuCheckStrictly = value ? true : false
  } else if (type == "dept") {
    form.value.deptCheckStrictly = value ? true : false
  }
}

/** 所有菜单节点数据 */
function getMenuAllCheckedKeys() {
  // 目前被选中的菜单节点
  let checkedKeys = menuRef.value.getCheckedKeys()
  // 半选中的菜单节点
  let halfCheckedKeys = menuRef.value.getHalfCheckedKeys()
  checkedKeys.unshift.apply(checkedKeys, halfCheckedKeys)
  console.log('所有选中节点', checkedKeys)
  return checkedKeys
}

/** 提交按钮 */
function submitForm() {
  proxy.$refs["roleRef"].validate(valid => {
    if (valid) {
      if (form.value.role_id != undefined) {
        form.value.rules = getMenuAllCheckedKeys()
        // return false
        updateRole(form.value).then(res => {
          proxy.$modal.msgSuccess("修改成功")
          open.value = false
          getList()
        })
      } else {
        form.value.rules = getMenuAllCheckedKeys()
        // return false
        addRole(form.value).then(res => {
          proxy.$modal.msgSuccess("新增成功")
          open.value = false
          getList()
        })
      }
    }
  })
}

/** 取消按钮 */
function cancel() {
  open.value = false
  reset()
}

/** 选择角色权限范围触发 */
function dataScopeSelectChange(value) {
  if (value !== "2") {
    deptRef.value.setCheckedKeys([])
  }
}

/** 分配数据权限操作 */
function handleDataScope(row) {
  reset()
  const deptTreeSelect = getDeptTree(row.role_id)
  getRole(row.role_id).then(res => {
    form.value = res.data
    openDataScope.value = true
    nextTick(() => {
      deptTreeSelect.then(res => {
        nextTick(() => {
          if (deptRef.value) {
            deptRef.value.setCheckedKeys(res.checkedKeys)
          }
        })
      })
    })
  })
  title.value = "分配数据权限"
}

/** 提交按钮（数据权限） */
function submitDataScope() {
  if (form.value.role_id != undefined) {
    form.value.deptIds = getDeptAllCheckedKeys()
    dataScope(form.value).then(res => {
      proxy.$modal.msgSuccess("修改成功")
      openDataScope.value = false
      getList()
    })
  }
}

/** 取消按钮（数据权限）*/
function cancelDataScope() {
  openDataScope.value = false
  reset()
}

getList()
</script>
