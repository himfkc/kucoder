<template>
    <div class="app-container">
        <!-- 查询表单 -->
        <el-form :model="queryParams" ref="queryRef" :inline="true" v-show="showSearch" label-width="80px">
            <el-form-item label="分组名称" prop="title">
                <el-input v-model="queryParams.title" placeholder="请输入分组名称" clearable size="small"
                    @keyup.enter="handleQuery" />
            </el-form-item>
            <el-form-item>
                <el-button type="primary" @click="handleQuery" size="small"><icon-ep-search
                        class="icon" />搜索</el-button>
                <el-button @click="resetQuery" size="small"><icon-ep-refresh class="icon" />重置</el-button>
            </el-form-item>
        </el-form>

        <!-- 操作按钮 -->
        <el-row :gutter="10" class="mb8">
            <el-col :span="1.5">
                <el-tooltip effect="dark" content="刷新" placement="top">
                    <el-button size="small" type="info" circle plain @click="refresh">
                        <icon-ep-refresh class="w-4" />
                    </el-button>
                </el-tooltip>
            </el-col>
            <el-col :span="1.5">
                <el-button type="primary" @click="handleAdd" v-auth:add size="small"><icon-ep-plus
                        class="icon" />新增</el-button>
            </el-col>
            <el-col :span="1.5">
                <el-button type="success" :disabled="single" @click="handleEdit" v-auth:edit size="small"><icon-ep-edit
                        class="icon" />修改</el-button>
            </el-col>
            <el-col :span="1.5">
                <el-button type="danger" :disabled="multiple || queryParams.recycle == 1" @click="handleDelete"
                    v-auth:delete size="small"><icon-ep-delete class="icon" />删除</el-button>
            </el-col>
            <el-col :span="1.5">
                <el-button type="warning" @click="handleRecycle" v-auth:trueDel size="small"><icon-ep-delete
                        class="icon" />回收站</el-button>
            </el-col>
            <el-col :span="1.5">
                <el-button v-if="queryParams.recycle == 1" type="info" size="small" @click="refresh"><icon-ep-refresh
                        class="icon" />退出回收站</el-button>
            </el-col>
            <el-col :span="1.5">
<el-button v-if="queryParams.recycle == 1" type="danger" size="small"
    @click="handleDeleteTrue" v-auth:trueDel><icon-ep-delete class="icon" />彻底删除</el-button>
            </el-col>
            <right-toolbar v-model:showSearch="showSearch" @queryTable="getList"></right-toolbar>
        </el-row>

        <!-- 数据表格 -->
        <el-table :border="true" ref="configGroupRef" v-loading="loading" :data="configGroupList"
            @selection-change="handleSelectionChange">
            <el-table-column type="selection" width="50" align="center" />
            <el-table-column label="ID" align="center" prop="id" width="80" />
            <el-table-column label="所属插件" align="center" prop="plugin" min-width="120">
                <template #default="scope">
                    <el-tag type="primary" size="small">{{ scope.row.plugin || '-' }}</el-tag>
                </template>
            </el-table-column>
            <el-table-column label="分组名称" align="center" prop="title" min-width="200" />
            <el-table-column label="分组标识" align="center" prop="name" min-width="150" />
            <el-table-column label="分组图标" align="center" prop="icon" min-width="150">
                <template #default="scope">
                    <el-icon v-if="scope.row.icon" :size="20">
                        <component :is="getIcon(scope.row.icon)" />
                    </el-icon>
                    <span v-else class="text-gray-400">-</span>
                </template>
            </el-table-column>

            <el-table-column label="操作" align="center" class-name="small-padding fixed-width" width="180">
                <template #default="scope">
                    <!-- 未删除 -->
                    <div v-if="!scope.row.delete_time">
                        <el-button link size="small" type="primary" @click="handleEdit(scope.row)"
                            v-auth:edit><icon-ep-edit class="icon" />修改</el-button>
                        <el-button link size="small" type="danger" @click="handleDelete(scope.row)"
                            v-auth:delete><icon-ep-delete class="icon" />删除</el-button>
                    </div>
                    <!-- 回收站 -->
                    <div v-else>
<el-button link size="small" type="info" @click="handleRestore(scope.row)"
    v-auth:trueDel><icon-ep-refreshLeft class="icon" />恢复</el-button>
<el-button link size="small" type="danger" @click="handleDeleteTrue(scope.row)"
    v-auth:trueDel><icon-ep-delete class="icon" />彻底删除</el-button>
                    </div>
                </template>
            </el-table-column>
        </el-table>

        <!-- 分页 -->
        <pagination v-show="total > 0" :total="total" v-model:page="queryParams.pageNum"
            v-model:limit="queryParams.pageSize" @pagination="getList" />

        <!-- 新增/修改对话框 -->
        <el-dialog :title="title" v-model="openEdit" width="600px" append-to-body>
            <el-form ref="editRef" :model="form" :rules="rules" label-width="100px">
                <el-row>
                    <el-col :span="24">
                        <el-form-item label="分组名称" prop="title">
                            <el-input v-model="form.title" placeholder="请输入分组名称" />
                        </el-form-item>
                    </el-col>
                </el-row>
                <el-row>
                    <el-col :span="24">
                        <el-form-item label="分组标识" prop="name">
                            <el-input v-model="form.name" placeholder="请输入分组标识（英文，如：base）" />
                        </el-form-item>
                    </el-col>
                </el-row>
                <el-row>
                    <el-col :span="24">
                        <el-form-item label="分组图标" prop="icon">
                            <el-select v-model="form.icon" placeholder="请选择分组图标" clearable style="width: 100%">
                                <el-option label="设置" value="Setting" />
                                <el-option label="文档" value="Document" />
                                <el-option label="聊天" value="ChatDotRound" />
                                <el-option label="上传" value="Upload" />
                                <el-option label="工具" value="Tools" />
                                <el-option label="锁" value="Lock" />
                                <el-option label="监控" value="Monitor" />
                            </el-select>
                        </el-form-item>
                    </el-col>
                </el-row>
                <el-row>
                    <el-col :span="24">
                        <el-form-item label="所属插件" prop="plugin">
                            <el-input v-model="form.plugin" placeholder="请输入所属插件（如：kucoder）" />
                        </el-form-item>
                    </el-col>
                </el-row>
            </el-form>
            <template #footer>
                <div class="dialog-footer">
                    <el-button @click="openEdit = false">取 消</el-button>
                    <el-button type="primary" @click="submitEdit">确 定</el-button>
                </div>
            </template>
        </el-dialog>
    </div>
</template>

<script setup>
import { list, add, update, del, trueDel } from "@/api/kucoder/system/config/configGroup"
import { Setting, Document, ChatDotRound, Upload, Tools, Lock, Monitor } from '@element-plus/icons-vue'

defineOptions({
    name: "kucoder_system_config_configGroup",
})
const emits = defineEmits(["changed"])

const { proxy } = getCurrentInstance()

const configGroupList = ref([])
const loading = ref(true)
const showSearch = ref(true)
const ids = ref([])
const single = ref(true)
const multiple = ref(true)
const total = ref(0)
const openEdit = ref(false)
const title = ref("")

// 图标映射
const iconMap = {
    Setting,
    Document,
    ChatDotRound,
    Upload,
    Tools,
    Lock,
    Monitor
}

// 获取图标组件
function getIcon(iconName) {
    return iconMap[iconName] || Setting
}

const data = reactive({
    form: {},
    queryParams: {
        pageNum: 1,
        pageSize: 10,
        title: undefined,
        recycle: 0 // 是否查询回收站数据
    },
    rules: {
        title: [
            { required: true, message: "分组名称不能为空", trigger: "blur" }
        ],
        name: [
            { required: true, message: "分组标识不能为空", trigger: "blur" }
        ],
        plugin: [
            { required: true, message: "所属插件不能为空", trigger: "blur" }
        ]
    }
})

const { queryParams, form, rules } = toRefs(data)

/** 查询配置分组列表 */
function getList() {
    loading.value = true
    list(queryParams.value).then(response => {
        configGroupList.value = response.data.list
        total.value = response.data.total
        loading.value = false
    }).catch(() => {
        loading.value = false
    })
}

/** 搜索按钮操作 */
function handleQuery() {
    queryParams.value.pageNum = 1
    getList()
}

/** 重置按钮操作 */
function resetQuery() {
    proxy.resetForm("queryRef")
    queryParams.value.pageNum = 1
    getList()
}

// 刷新
function refresh() {
    queryParams.value.pageNum = 1
    queryParams.value.recycle = 0 // 重置回收站查询
    getList()
}

/** 回收站按钮操作 */
function handleRecycle() {
    queryParams.value.recycle = 1 // 设置查询回收站数据
    getList() // 重新查询列表
}

/** 回收站恢复按钮操作 */
function handleRestore(row) {
    const restoreIds = row.id || ids.value
    proxy.$modal.confirm('是否确认恢复编号为"' + restoreIds + '"的数据项?').then(function () {
        return update({ id: restoreIds, delete_restore: 1 })
    }).then(() => {
        getList()
        proxy.$modal.msgSuccess("恢复成功")
    }).catch(() => { })
}

/** 回收站彻底删除按钮操作 */
function handleDeleteTrue(row) {
    const deleteIds = row.id || ids.value
    proxy.$modal.confirm('是否确认彻底删除编号为"' + deleteIds + '"的数据项?').then(function () {
        return trueDel({ id: deleteIds })
    }).then(() => {
        getList()
        proxy.$modal.msgSuccess("彻底删除成功")
    }).catch(() => { })
}

/** 多选框选中数据 */
function handleSelectionChange(selection) {
    ids.value = selection.map(item => item.id)
    single.value = selection.length != 1
    multiple.value = !selection.length
}

/** 新增按钮操作 */
function handleAdd() {
    reset()
    openEdit.value = true
    title.value = "添加配置分组"
}

/** 修改按钮操作 */
function handleEdit(row) {
    const id = row.id || ids.value[0]
    form.value = row
    openEdit.value = true
    title.value = "修改配置分组"
}

/** 表单重置 */
function reset() {
    form.value = {
        id: undefined,
        title: undefined,
        name: undefined,
        icon: undefined,
        plugin: undefined
    }
    proxy.resetForm("editRef")
}

/** 提交按钮 */
function submitEdit() {
    proxy.$refs.editRef.validate(valid => {
        if (valid) {
            if (form.value.id != undefined) {
                update(form.value).then(() => {
                    emits("changed")
                    proxy.$modal.msgSuccess("修改成功")
                    openEdit.value = false
                    getList()
                })
            } else {
                add(form.value).then(() => {
                    emits("changed")
                    proxy.$modal.msgSuccess("新增成功")
                    openEdit.value = false
                    getList()
                })
            }
        }
    })
}

/** 删除按钮操作 */
function handleDelete(row) {
    const deleteIds = row.id || ids.value
    proxy.$modal.confirm('是否确认删除编号为"' + deleteIds + '"的数据项？').then(function () {
        return del({ id: deleteIds })
    }).then(() => {
        emits("changed")
        getList()
        proxy.$modal.msgSuccess("删除成功")
    }).catch(() => { })
}

getList()
</script>

<style scoped>
.text-gray-400 {
    color: #909399;
}
</style>
