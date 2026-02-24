<template>
    <div class="app-container">
        <!-- 搜索 -->
        <el-form :model="queryParams" ref="queryRef" v-show="showSearch" :inline="true" label-width="68px">
            <el-form-item label="插件名称" prop="title">
                <el-input v-model="queryParams.title" placeholder="请输入插件名称" clearable @keyup.enter="handleQuery"
                    size="small" style="width: 150px" />
            </el-form-item>
            <el-form-item label="状态" prop="status">
                <el-select v-model="queryParams.status" placeholder="插件状态" clearable size="small" style="width: 100px">
                    <el-option v-for="dict in enumFieldData.status" :key="dict.value" :label="dict.label"
                        :value="dict.value" />
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
                <el-button type="primary" size="small" @click="handleAdd" v-auth:add>
                    <icon-ep-plus class="icon" />新增</el-button>
            </el-col>
            <el-col :span="1.5">
                <el-button type="success" size="small" :disabled="single" @click="handleUpdate" v-auth:edit>
                    <icon-ep-edit class="icon" />修改</el-button>
            </el-col>
            <el-col :span="1.5">
                <el-button type="danger" size="small" :disabled="multiple || queryParams.recycle == 1"
                    @click="handleDelete" v-auth:delete>
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
            <right-toolbar v-model:showSearch="showSearch" @queryTable="getList"></right-toolbar>
        </el-row>

        <!-- 表格数据 -->
        <el-table :border="true" v-loading="loading" :data="roleList" @selection-change="handleSelectionChange">
            <el-table-column type="selection" width="55" align="center" />
            <el-table-column label="插件ID" prop="id" width="80" align="center" />
            <el-table-column label="插件名" prop="title" :show-overflow-tooltip="true" width="150" align="center" />
            <el-table-column label="插件版本" prop="version" align="center" />
            <!-- <el-table-column label="权限字符" prop="role_key" :show-overflow-tooltip="true" width="150" align="center" /> -->
            <!-- <el-table-column label="显示顺序" prop="sort" width="100" align="center" /> -->
            <el-table-column label="简介" prop="desc" :show-overflow-tooltip="true" width="150" align="center" />
            <el-table-column label="图片" prop="img" align="center" />
            <el-table-column label="类型" prop="type" align="center" />
            <el-table-column label="作者" prop="author" align="center" />
            <el-table-column label="主页" prop="homepage" align="center" />
            <el-table-column label="文档" prop="doc_id" align="center" />

            <el-table-column label="过期时间" prop="expire_time" align="center" />

            <el-table-column label="状态" align="center" width="100">
                <template #default="scope">
                    <el-switch v-if="scope.row.id != 1" v-model="scope.row.status" :active-value="1" :inactive-value="0"
                        @change="handleChange(scope.row)"></el-switch>
                </template>
            </el-table-column>
            <el-table-column label="安装时间" align="center" prop="create_time" width="160"></el-table-column>
            <el-table-column label="操作" align="center" class-name="">
                <template #default="scope">
                    <!-- 未删除 -->
                    <div v-if="!scope.row.delete_time">
                        <el-button link size="small" type="success" @click="handleUpdate(scope.row)" v-auth:edit
                            v-if="scope.row.id != 1">
                            <icon-ep-edit class="icon" />修改
                        </el-button>
                        <el-button link size="small" type="danger" @click="handleDelete(scope.row)" v-auth:delete
                            v-if="scope.row.id != 1">
                            <icon-ep-delete class="icon" />删除
                        </el-button>
                    </div>
                    <!-- 回收站 -->
                    <div class="" v-else>
                        <el-button link size="small" type="info" @click="handleRestore(scope.row)" v-auth:trueDel
                            v-if="scope.row.id != 1">
                            <icon-ep-refreshLeft class="icon" />恢复
                        </el-button>
                        <el-button link size="small" type="danger" @click="handleDeleteTrue(scope.row)" v-auth:trueDel
                            v-if="scope.row.id != 1">
                            <icon-ep-delete class="icon" />彻底删除
                        </el-button>
                    </div>
                </template>

            </el-table-column>
        </el-table>

        <pagination v-show="total > 0" :total="total" v-model:page="queryParams.pageNum"
            v-model:limit="queryParams.pageSize" @pagination="getList" />

        <!-- 添加或修改插件配置对话框 -->
        <el-dialog :title="title" v-model="open" width="500px" append-to-body draggable>
            <el-form ref="formRef" :model="form" :rules="rules" label-width="100px">
                <el-form-item label="插件名称" prop="title">
                    <el-input v-model="form.title" placeholder="请输入插件名称" />
                </el-form-item>
                <el-form-item label="状态">
                    <el-radio-group v-model="form.status">
                        <el-radio v-for="dict in enumFieldData.status" :key="dict.value" :value="dict.value">{{
                            dict.label
                        }}</el-radio>
                    </el-radio-group>
                </el-form-item>
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

<script setup>
import { addRole, change, delRole, listRole, updateRole, trueDel, } from "@/api/kucoder/system/role"
// import { roleMenuTreeselect, treeselect as menuTreeselect } from "@/api/kucoder/system/menu"
import { handleEnumField, clone } from "@/utils/kucoder"
import usePermissionStore from "@/store/modules/permission"

defineOptions({
    name: 'kucoder_plugin_plugin'
})

const { proxy } = getCurrentInstance()

const roleList = ref([])
const open = ref(false)
const loading = ref(true)
const showSearch = ref(true)
const single = ref(true)
const multiple = ref(true)
const total = ref(0)
const title = ref("")
const dateRange = ref([])

const menuExpand = ref(false)
const menuNodeAll = ref(false)
const deptExpand = ref(true)
const deptNodeAll = ref(false)
const menuRef = ref(null)

// const permissionStore = usePermissionStore()
const enumFieldData = ref({})

const data = reactive({
    form: {},
    queryParams: {
        pageNum: 1,
        pageSize: 10,
        title: undefined,
        // role_key: undefined,
        status: undefined,
        recycle: 0, // 是否查询回收站数据
    },
    rules: {
        title: [{ required: true, message: "插件名称不能为空", trigger: "blur" }],
        // role_key: [{ required: true, message: "权限字符不能为空", trigger: "blur" }],
        // sort: [{ required: true, message: "插件顺序不能为空", trigger: "blur" }]
        // rules: [{ required: true, message: "插件菜单权限不能为空", trigger: "blur" }]
    },
})

const { queryParams, form, rules } = toRefs(data)

/** 查询插件列表 */
function getList() {
    loading.value = true
    listRole(proxy.addDateRange(queryParams.value, dateRange.value, 'create_time'))
        .then(response => {
            roleList.value = response.data
            total.value = response.total
            enumFieldData.value = handleEnumField(response.enumFieldData || {})
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
    const ids = row.id || ids.value
    proxy.$modal.confirm('是否确认恢复插件编号为"' + ids + '"的数据项?').then(function () {
        return updateRole({ id: ids, delete_restore: 1 })
    }).then(() => {
        getList()
        proxy.$modal.msgSuccess("恢复成功")
    }).catch(() => { })
}
/**回收站彻底删除按钮操作 */
function handleDeleteTrue(row) {
    const ids = row.id || ids.value
    proxy.$modal.confirm('是否确认彻底删除插件编号为"' + ids + '"的数据项?').then(function () {
        return trueDel({ id: ids })
    }).then(() => {
        getList()
        proxy.$modal.msgSuccess("彻底删除成功")
    }).catch(() => { })
}
/** 删除按钮操作 */
function handleDelete(row) {
    const ids = row.id || ids.value
    proxy.$modal.confirm('是否确认删除插件编号为"' + ids + '"的数据项?').then(function () {
        return delRole({ id: ids })
    }).then(() => {
        getList()
        proxy.$modal.msgSuccess("删除成功")
    }).catch(() => { })
}

/** 插件状态修改 */
function handleChange(row) {
    if (queryParams.value.recycle == 1) {
        proxy.$modal.msgWarning("回收站数据不能修改状态")
        row.status = Number(!row.status)
        return false;
    }
    proxy.$modal.confirm('确认要执行操作吗?').then(function () {
        return change({ id: row.id, status: row.status })
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
        id: undefined,
        title: undefined,
        // role_key: undefined,
        sort: 999,
        status: 1,
        rules: [],
        deptIds: [],
        menuCheckStrictly: true,
        deptCheckStrictly: true,
        remark: undefined
    }
    proxy.resetForm("formRef")
}

/** 添加插件 */
function handleAdd() {
    reset()
    // getMenuTreeselect()
    open.value = true
    title.value = "添加插件"
}

/** 修改插件 */
function handleUpdate(row) {
    console.log('修改插件', row)
    form.value = clone(row)
    open.value = true
    /* nextTick(() => {
        console.log('menuRef.value', menuRef.value)
        menuRef.value.setCheckedKeys(row.rules)
        form.value.menuCheckStrictly = true  //父子联动
    }) */
    title.value = "修改插件"
}

/** 提交按钮 */
function submitForm() {
    proxy.$refs["formRef"].validate(valid => {
        if (valid) {
            if (form.value.id != undefined) {
                // form.value.rules = getMenuAllCheckedKeys()
                // return false
                updateRole(form.value).then(response => {
                    proxy.$modal.msgSuccess("修改成功")
                    open.value = false
                    getList()
                })
            } else {
                // form.value.rules = getMenuAllCheckedKeys()
                // return false
                addRole(form.value).then(response => {
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

getList()
</script>