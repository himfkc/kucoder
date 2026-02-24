<template>
   <div class="app-container">
      <el-form :model="queryParams" ref="queryRef" :inline="true" v-show="showSearch">
         <el-form-item label="部门名称" prop="dept_name">
            <el-input v-model="queryParams.dept_name" placeholder="请输入部门名称" clearable size="small" style="width: 150px"
               @keyup.enter="handleQuery" />
         </el-form-item>
         <el-form-item label="状态" prop="status">
            <el-select v-model="queryParams.status" placeholder="部门状态" clearable style="width: 100px" size="small">
               <el-option v-for="dict in enumFieldData.status" :key="dict.value" :label="dict.label"
                  :value="dict.value" />
            </el-select>
         </el-form-item>
         <el-form-item>
            <el-button type="primary" size="small" @click="handleQuery">
               <icon-ep-search class="icon" />搜索</el-button>
            <el-button size="small" @click="resetQuery">
               <icon-ep-refresh class="icon" />重置</el-button>
         </el-form-item>
      </el-form>

      <el-row :gutter="10" class="mb8">
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
         <el-col :span="1.5">
            <el-button type="info" size="small" @click="toggleExpandAll"><icon-ep-sort class="icon" />展开/折叠</el-button>
         </el-col>
         <right-toolbar v-model:showSearch="showSearch" @queryTable="getList"></right-toolbar>
      </el-row>

      <el-table :border="true" v-if="refreshTable" v-loading="loading" :data="deptList" row-key="dept_id"
         :default-expand-all="isExpandAll" :tree-props="{ children: 'children', hasChildren: 'hasChildren' }">
         <el-table-column prop="dept_name" label="部门名称" width="260"></el-table-column>
         <el-table-column prop="sort" label="排序" align="center" width="200"></el-table-column>
         <el-table-column prop="leader" label="负责人" align="center" width="200"></el-table-column>
         <el-table-column prop="phone" label="联系电话" align="center" width="200"></el-table-column>
         <el-table-column prop="email" label="邮箱" align="center" width="200"></el-table-column>
         <el-table-column prop="status" label="状态" align="center" width="100">
            <template #default="scope">
               <!-- <dict-tag :options="sys_normal_disable" :value="scope.row.status" /> -->
               <el-switch v-model="scope.row.status" :active-value="1" :inactive-value="0"
                  @change="handleChange(scope.row)">
               </el-switch>
            </template>
         </el-table-column>
         <el-table-column label="创建时间" align="center" prop="create_time" width="200"></el-table-column>
         <el-table-column label="操作" align="center" class-name="">
            <template #default="scope">
               <!-- 未删除 -->
               <div v-if="!scope.row.delete_time">
                  <el-button link type="primary" size="small" @click="handleAdd(scope.row)" v-auth:add>
                     <icon-ep-plus class="icon" />新增
                  </el-button>
                  <el-button link type="success" size="small" @click="handleUpdate(scope.row)" v-auth:edit>
                     <icon-ep-edit class="icon" />修改
                  </el-button>
                  <el-button v-if="scope.row.parent_id != 0" link type="danger" size="small"
                     @click="handleDelete(scope.row)" v-auth:delete>
                     <icon-ep-delete class="icon" />删除
                  </el-button>
               </div>
               <!-- 回收站 -->
               <div class="" v-else>
                  <el-button link size="small" type="info" @click="handleRestore(scope.row)" v-auth:trueDel>
                     <icon-ep-refreshLeft class="icon" />恢复
                  </el-button>
                  <el-button link size="small" type="danger" @click="handleDeleteTrue(scope.row)" v-auth:trueDel>
                     <icon-ep-delete class="icon" />彻底删除
                  </el-button>
               </div>
            </template>
         </el-table-column>
      </el-table>

      <!-- 添加或修改部门对话框 -->
      <el-dialog :title="title" v-model="open" width="600px" append-to-body draggable>
         <el-form ref="deptRef" :model="form" :rules="rules" label-width="80px">
            <el-row>
               <el-col :span="24" v-if="form.parent_id !== 0">
                  <el-form-item label="上级部门" prop="parent_id">
                     <el-tree-select v-model="form.parent_id" :data="deptOptions"
                        :props="{ value: 'dept_id', label: 'dept_name', children: 'children' }" value-key="dept_id"
                        placeholder="选择上级部门" check-strictly />
                  </el-form-item>
               </el-col>
               <el-col :span="12">
                  <el-form-item label="部门名称" prop="dept_name">
                     <el-input v-model="form.dept_name" placeholder="请输入部门名称" />
                  </el-form-item>
               </el-col>
               <el-col :span="12">
                  <el-form-item label="显示排序" prop="sort">
                     <el-input-number v-model="form.sort" controls-position="right" :min="0" />
                  </el-form-item>
               </el-col>
               <el-col :span="12">
                  <el-form-item label="负责人" prop="leader">
                     <el-input v-model="form.leader" placeholder="请输入负责人" maxlength="20" />
                  </el-form-item>
               </el-col>
               <el-col :span="12">
                  <el-form-item label="联系电话" prop="phone">
                     <el-input v-model="form.phone" placeholder="请输入联系电话" maxlength="11" />
                  </el-form-item>
               </el-col>
               <el-col :span="12">
                  <el-form-item label="邮箱" prop="email">
                     <el-input v-model="form.email" placeholder="请输入邮箱" maxlength="50" />
                  </el-form-item>
               </el-col>
               <el-col :span="12">
                  <el-form-item label="部门状态">
                     <el-radio-group v-model="form.status">
                        <el-radio v-for="dict in enumFieldData.status" :key="dict.value" :value="dict.value">{{
                           dict.label
                        }}</el-radio>
                     </el-radio-group>
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
   </div>
</template>

<script setup name="Dept">
import { listDept, delDept, addDept, updateDept, change, trueDel } from "@/api/kucoder/system/dept"
import { handleEnumField, clone } from "@/utils/kucoder"

const { proxy } = getCurrentInstance()

defineOptions({
   name: 'kucoder_system_dept'
})

const deptList = ref([])
const open = ref(false)
const loading = ref(true)
const showSearch = ref(true)
const title = ref("")
const deptOptions = ref([])
const isExpandAll = ref(true)
const refreshTable = ref(true)

const enumFieldData = ref({})

const data = reactive({
   form: {},
   queryParams: {
      dept_name: undefined,
      status: undefined,
      recycle: 0
   },
   rules: {
      parent_id: [{ required: true, message: "上级部门不能为空", trigger: "blur" }],
      dept_name: [{ required: true, message: "部门名称不能为空", trigger: "blur" }],
      sort: [{ required: true, message: "显示排序不能为空", trigger: "blur" }],
      email: [{ type: "email", message: "请输入正确的邮箱地址", trigger: ["blur", "change"] }],
      phone: [{ pattern: /^1[3|4|5|6|7|8|9][0-9]\d{8}$/, message: "请输入正确的手机号码", trigger: "blur" }]
   },
})

const { queryParams, form, rules } = toRefs(data)

/** 查询部门列表 */
function getList() {
   loading.value = true
   listDept(queryParams.value).then(({ data, msg, code }) => {
      deptList.value = proxy.handleTree(data.list, "dept_id", 'parent_id')
      enumFieldData.value = handleEnumField(data.enumFieldData)
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
   const ids = row.dept_id
   proxy.$modal.confirm('是否确认恢复角色编号为"' + ids + '"的数据项?').then(function () {
      return updateDept({ id: ids, delete_restore: 1 })
   }).then(() => {
      getList()
      proxy.$modal.msgSuccess("恢复成功")
   }).catch(() => { })
}
/**回收站彻底删除按钮操作 */
function handleDeleteTrue(row) {
   const ids = row.dept_id
   proxy.$modal.confirm('是否确认彻底删除角色编号为"' + ids + '"的数据项?').then(function () {
      return trueDel({ id: ids })
   }).then(() => {
      getList()
      proxy.$modal.msgSuccess("彻底删除成功")
   }).catch(() => { })
}
/** 删除按钮操作 */
function handleDelete(row) {
   proxy.$modal.confirm('是否确认删除名称为"' + row.dept_name + '"的数据项?').then(function () {
      return delDept({ id: row.dept_id })
   }).then(() => {
      getList()
      proxy.$modal.msgSuccess("删除成功")
   }).catch(() => { })
}

/** 状态修改 */
function handleChange(row) {
   if (queryParams.value.recycle == 1) {
      proxy.$modal.msgWarning("回收站数据不能修改状态")
      row.status = Number(!row.status)
      return false;
   }

   proxy.$modal.confirm('确认要执行操作吗?').then(function () {
      return change({ dept_id: row.dept_id, status: row.status })
   }).then(() => {
      proxy.$modal.msgSuccess("操作成功")
   }).catch(function (err) {
      proxy.$modal.msgWarning(err)
      row.status = !row.status
   })
}

/** 取消按钮 */
function cancel() {
   open.value = false
   reset()
}

/** 表单重置 */
function reset() {
   form.value = {
      dept_id: undefined,
      parent_id: undefined,
      dept_name: undefined,
      sort: 999,
      leader: undefined,
      phone: undefined,
      email: undefined,
      status: 1
   }
   proxy.resetForm("deptRef")
}

/** 搜索按钮操作 */
function handleQuery() {
   getList()
}

/** 重置按钮操作 */
function resetQuery() {
   proxy.resetForm("queryRef")
   handleQuery()
}

/** 新增按钮操作 */
function handleAdd(row) {
   reset()
   listDept().then(response => {
      deptOptions.value = proxy.handleTree(response.data, "dept_id")
   })
   if (row != undefined) {
      form.value.parent_id = row.dept_id
   }
   open.value = true
   title.value = "添加部门"
}

/** 展开/折叠操作 */
function toggleExpandAll() {
   refreshTable.value = false
   isExpandAll.value = !isExpandAll.value
   nextTick(() => {
      refreshTable.value = true
   })
}

/** 修改按钮操作 */
function handleUpdate(row) {
   /* reset()
   listDeptExcludeChild(row.dept_id).then(response => {
      deptOptions.value = proxy.handleTree(response.data, "dept_id")
   })
   getDept(row.dept_id).then(response => {
      form.value = response.data
      open.value = true
      title.value = "修改部门"
   }) */
   form.value = clone(row)
   open.value = true
   title.value = "修改部门"
}

/** 提交按钮 */
function submitForm() {
   proxy.$refs["deptRef"].validate(valid => {
      if (valid) {
         if (form.value.dept_id != undefined) {
            updateDept(form.value).then(response => {
               proxy.$modal.msgSuccess("修改成功")
               open.value = false
               getList()
            })
         } else {
            addDept(form.value).then(response => {
               proxy.$modal.msgSuccess("新增成功")
               open.value = false
               getList()
            })
         }
      }
   })
}



getList()
</script>
