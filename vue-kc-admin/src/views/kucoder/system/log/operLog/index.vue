<template>
    <div class="app-container">
        <el-form :model="queryParams" ref="queryRef" :inline="true" v-show="showSearch" label-width="68px">
            <el-form-item label="操作IP" prop="ip">
                <el-input v-model="queryParams.ip" placeholder="请输入操作者IP" clearable size="small"
                    @keyup.enter="handleQuery" />
            </el-form-item>
            <!-- <el-form-item label="操作模块" prop="title">
                <el-input v-model="queryParams.title" placeholder="请输入操作模块" clearable style="width: 240px;"
                    @keyup.enter="handleQuery" />
            </el-form-item> -->
            <el-form-item label="插件" prop="plugin">
                <el-input v-model="queryParams.plugin" placeholder="请输入插件" clearable size="small"
                    @keyup.enter="handleQuery" />
            </el-form-item>
            <el-form-item label="状态" prop="status">
                <el-select v-model="queryParams.status" placeholder="操作状态" clearable size="small" style="width: 120px">
                    <el-option label="操作完成" :value="1" />
                    <el-option label="操作未完成" :value="0" />
                </el-select>
            </el-form-item>
            <el-form-item label="操作时间" style="width: 400px">
                <el-date-picker v-model="dateRange" value-format="YYYY-MM-DD HH:mm:ss" type="datetimerange"
                    range-separator="-" start-placeholder="开始日期" end-placeholder="结束日期"
                    :default-time="[new Date(2000, 1, 1, 0, 0, 0), new Date(2000, 1, 1, 23, 59, 59)]"
                    size="small"></el-date-picker>
            </el-form-item>
            <el-form-item>
                <el-button type="primary" icon="Search" @click="handleQuery" size="small"><icon-ep-search
                        class="icon" />搜索</el-button>
                <el-button icon="Refresh" @click="resetQuery" size="small"><icon-ep-refresh
                        class="icon" />重置</el-button>
            </el-form-item>
        </el-form>

        <el-row :gutter="10" class="mb8">
            <el-col :span="1.5">
                <el-button type="danger" plain :disabled="multiple" @click="handleDelete"
                    v-auth="['kucoder:system:log:operLog:delete']"><icon-ep-delete class="icon" />删除</el-button>
            </el-col>
            <!-- <el-col :span="1.5">
                <el-button type="danger" plain icon="Delete" @click="handleClean"
                    v-auth="['kucoder:system:log:operLog:clean']">清空</el-button>
            </el-col> -->
            <right-toolbar v-model:showSearch="showSearch" @queryTable="getList"></right-toolbar>
        </el-row>

        <el-table :border="true" ref="operLogRef" v-loading="loading" :data="operLogList"
            @selection-change="handleSelectionChange">
            <el-table-column type="selection" width="50" align="center" />
            <el-table-column label="日志编号" align="center" prop="id" width="80" />
            <!-- <el-table-column label="日志title" align="center" prop="title" :show-overflow-tooltip="true" /> -->
            <el-table-column label="插件" align="center" prop="plugin" width="100" :show-overflow-tooltip="true" />
            <el-table-column label="应用" align="center" prop="app" width="80" />
            <el-table-column label="应用类型" align="center" prop="app_type" width="90">
                <template #default="scope">
                    <el-tag v-if="scope.row.app_type === 0">后台</el-tag>
                    <el-tag v-else-if="scope.row.app_type === 1" type="success">API</el-tag>
                    <el-tag v-else-if="scope.row.app_type === 2" type="warning">小程序</el-tag>
                </template>
            </el-table-column>
            <el-table-column label="请求路径" align="center" prop="path" :show-overflow-tooltip="true" width="300" />
            <el-table-column label="控制器" align="center" prop="controller" width="120" :show-overflow-tooltip="true" />
            <el-table-column label="操作方法" align="center" prop="action" width="80" />
            <el-table-column label="用户ID" align="center" prop="uid" width="80" />
            <el-table-column label="操作IP" align="center" prop="ip" width="130" :show-overflow-tooltip="true" />
            <el-table-column label="状态" align="center" prop="status" width="80">
                <template #default="scope">
                    <el-tag v-if="scope.row.status === 1" type="success">操作完成</el-tag>
                    <el-tag v-else-if="scope.row.status === 0" type="danger">操作未完成</el-tag>
                </template>
            </el-table-column>
            <el-table-column label="操作时间" align="center" prop="create_time" width="180">
                <template #default="scope">
                    <span>{{ parseTime(scope.row.create_time) }}</span>
                </template>
            </el-table-column>
            <!-- <el-table-column label="操作" align="center" class-name="small-padding fixed-width" width="100">
                <template #default="scope">
                    <el-button link type="primary" icon="View" @click="handleView(scope.row)"
                        v-auth="['kucoder:system:log:operLog:query']">详细</el-button>
                </template>
            </el-table-column> -->
        </el-table>

        <pagination v-show="total > 0" :total="total" v-model:page="queryParams.pageNum"
            v-model:limit="queryParams.pageSize" @pagination="getList" />

        <!-- 操作日志详细 -->
        <el-dialog title="操作日志详细" v-model="open" width="800px" append-to-body>
            <el-form :model="form" label-width="100px">
                <el-row>
                    <el-col :span="12">
                        <el-form-item label="操作模块：">{{ form.title }}</el-form-item>
                    </el-col>
                    <el-col :span="12">
                        <el-form-item label="插件：">{{ form.plugin }}</el-form-item>
                    </el-col>
                    <el-col :span="12">
                        <el-form-item label="应用名：">{{ form.app }}</el-form-item>
                    </el-col>
                    <el-col :span="12">
                        <el-form-item label="应用类型：">
                            <el-tag v-if="form.app_type === 0">后台</el-tag>
                            <el-tag v-else-if="form.app_type === 1" type="success">API</el-tag>
                            <el-tag v-else-if="form.app_type === 2" type="warning">小程序</el-tag>
                        </el-form-item>
                    </el-col>
                    <el-col :span="12">
                        <el-form-item label="请求路径：">{{ form.path }}</el-form-item>
                    </el-col>
                    <el-col :span="12">
                        <el-form-item label="控制器：">{{ form.controller }}</el-form-item>
                    </el-col>
                    <el-col :span="12">
                        <el-form-item label="操作方法：">{{ form.action }}</el-form-item>
                    </el-col>
                    <el-col :span="12">
                        <el-form-item label="用户ID：">{{ form.uid }}</el-form-item>
                    </el-col>
                    <el-col :span="12">
                        <el-form-item label="操作IP：">{{ form.ip }}</el-form-item>
                    </el-col>
                    <el-col :span="12">
                        <el-form-item label="操作状态：">
                            <el-tag v-if="form.status === 1" type="success">正常</el-tag>
                            <el-tag v-else-if="form.status === 0" type="danger">异常</el-tag>
                        </el-form-item>
                    </el-col>
                    <el-col :span="24">
                        <el-form-item label="操作结果消息：">{{ form.msg }}</el-form-item>
                    </el-col>
                    <el-col :span="12">
                        <el-form-item label="操作时间：">{{ parseTime(form.create_time) }}</el-form-item>
                    </el-col>
                </el-row>
            </el-form>
            <template #footer>
                <div class="dialog-footer">
                    <el-button @click="open = false">关 闭</el-button>
                </div>
            </template>
        </el-dialog>
    </div>
</template>

<script setup>
import { list, delOperLog, cleanOperLog } from "@/api/kucoder/system/log/operLog"

defineOptions({
    name: "kucoder_system_log_operLog",
})

const { proxy } = getCurrentInstance()

const operLogList = ref([])
const open = ref(false)
const loading = ref(true)
const showSearch = ref(true)
const ids = ref([])
const single = ref(true)
const multiple = ref(true)
const total = ref(0)
const dateRange = ref([])

const data = reactive({
    form: {},
    queryParams: {
        pageNum: 1,
        pageSize: 10,
        ip: undefined,
        title: undefined,
        plugin: undefined,
        status: undefined
    }
})

const { queryParams, form } = toRefs(data)

/** 查询操作日志列表 */
function getList() {
    loading.value = true
    list(proxy.addDateRange(queryParams.value, dateRange.value)).then(response => {
        operLogList.value = response.data.list
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
    dateRange.value = []
    proxy.resetForm("queryRef")
    queryParams.value.pageNum = 1
    getList()
}

/** 多选框选中数据 */
function handleSelectionChange(selection) {
    ids.value = selection.map(item => item.id)
    multiple.value = !selection.length
}

/** 详细按钮操作 */
function handleView(row) {
    open.value = true
    form.value = row
}

/** 删除按钮操作 */
function handleDelete(row) {
    const logIds = row.id || ids.value
    proxy.$modal.confirm('是否确认删除日志编号为"' + logIds + '"的数据项?').then(function () {
        return delOperLog(logIds)
    }).then(() => {
        getList()
        proxy.$modal.msgSuccess("删除成功")
    }).catch(() => { })
}

/** 清空按钮操作 */
function handleClean() {
    proxy.$modal.confirm("是否确认清空所有操作日志数据项?").then(function () {
        return cleanOperLog()
    }).then(() => {
        getList()
        proxy.$modal.msgSuccess("清空成功")
    }).catch(() => { })
}

getList()
</script>
