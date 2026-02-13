<template>
    <div class="app-container">
        <!-- 查询表单 -->
        <el-form :model="queryParams" ref="queryRef" :inline="true" v-show="showSearch" label-width="80px">
            <el-form-item label="用户名" prop="username">
                <el-input v-model="queryParams.username" placeholder="请输入用户名" clearable size="small"
                    @keyup.enter="handleQuery" />
            </el-form-item>
            <el-form-item label="昵称" prop="nickname">
                <el-input v-model="queryParams.nickname" placeholder="请输入昵称" clearable size="small"
                    @keyup.enter="handleQuery" />
            </el-form-item>
            <el-form-item label="手机号" prop="mobile">
                <el-input v-model="queryParams.mobile" placeholder="请输入手机号" clearable size="small"
                    @keyup.enter="handleQuery" />
            </el-form-item>
            <el-form-item label="状态" prop="status">
                <el-select v-model="queryParams.status" placeholder="请选择状态" clearable size="small" style="width: 120px">
                    <el-option label="停用" :value="0" />
                    <el-option label="正常" :value="1" />
                </el-select>
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
                <el-button type="primary" @click="handleAdd" v-auth:add size="small"><icon-ep-plus
                        class="icon" />新增</el-button>
            </el-col>
            <el-col :span="1.5">
                <el-button type="success" :disabled="single" @click="handleEdit" v-auth:edit size="small"><icon-ep-edit
                        class="icon" />修改</el-button>
            </el-col>
            <el-col :span="1.5">
                <el-button type="warning" :disabled="single" @click="handleResetPwd" v-auth:edit
                    size="small"><icon-ep-lock class="icon" />重置密码</el-button>
            </el-col>
            <el-col :span="1.5">
                <el-button type="danger" :disabled="multiple" @click="handleDelete" v-auth:delete
                    size="small"><icon-ep-delete class="icon" />删除</el-button>
            </el-col>
            <el-col :span="1.5">
                <el-button type="warning" @click="handleRecycle" v-auth:trueDel size="small"><icon-ep-delete
                        class="icon" />回收站</el-button>
            </el-col>
            <el-col :span="1.5">
                <el-button v-if="queryParams.recycle === 1" type="info" @click="refresh" size="small"><icon-ep-refresh
                        class="icon" />退出回收站</el-button>
            </el-col>
            <el-col :span="1.5">
<el-button v-if="queryParams.recycle === 1" type="danger" size="small"
    @click="handleDeleteTrue" v-auth:trueDel><icon-ep-delete class="icon" />彻底删除</el-button>
            </el-col>
            <right-toolbar v-model:showSearch="showSearch" @queryTable="getList"></right-toolbar>
        </el-row>

        <!-- 数据表格 -->
        <el-table :border="true" ref="memberRef" v-loading="loading" :data="memberList"
            @selection-change="handleSelectionChange">
            <el-table-column type="selection" width="50" align="center" />
            <el-table-column label="会员ID" align="center" prop="m_id" width="100" />
            <el-table-column label="用户名" align="center" prop="username" width="120" :show-overflow-tooltip="true" />
            <el-table-column label="昵称" align="center" prop="nickname" width="120" :show-overflow-tooltip="true" />
            <el-table-column label="头像" align="center" prop="avatar" width="80">
                <template #default="scope">
                    <el-image v-if="scope.row.avatar" :src="imgUrl(scope.row.avatar)"
                        :preview-src-list="[imgUrl(scope.row.avatar)]" :preview-teleported="true"
                        style="width: 40px; height: 40px;" />
                    <span v-else>-</span>
                </template>
            </el-table-column>
            <el-table-column label="邮箱" align="center" prop="email" width="150" :show-overflow-tooltip="true" />
            <el-table-column label="手机号" align="center" prop="mobile" width="120" />
            <el-table-column label="性别" align="center" prop="sex" width="80">
                <template #default="scope">
                    <span v-if="scope.row.sex === 0">男</span>
                    <span v-else-if="scope.row.sex === 1">女</span>
                    <span v-else>未知</span>
                </template>
            </el-table-column>
            <el-table-column label="状态" align="center" prop="status" width="80">
                <template #default="scope">
                    <el-tag v-if="scope.row.status === 0" type="danger">停用</el-tag>
                    <el-tag v-else-if="scope.row.status === 1" type="success">正常</el-tag>
                </template>
            </el-table-column>
            <el-table-column label="最后登录IP" align="center" prop="login_ip" width="130" :show-overflow-tooltip="true" />
            <el-table-column label="最后登录时间" align="center" prop="last_login_time" width="180">
                <template #default="scope">
                    <span>{{ parseTime(scope.row.last_login_time) }}</span>
                </template>
            </el-table-column>
            <el-table-column label="创建时间" align="center" prop="create_time" width="180">
                <template #default="scope">
                    <span>{{ parseTime(scope.row.create_time) }}</span>
                </template>
            </el-table-column>
            <el-table-column label="操作" align="center" class-name="small-padding fixed-width" width="280">
                <template #default="scope">
                    <!-- 未删除 -->
                    <div v-if="!scope.row.delete_time">
                        <el-button link size="small" type="primary" @click="handleView(scope.row)"
                            v-auth:info><icon-ep-view class="icon" />查看</el-button>
                        <el-button link size="small" type="primary" @click="handleEdit(scope.row)"
                            v-auth:edit><icon-ep-edit class="icon" />修改</el-button>
                        <el-button link size="small" type="warning" @click="handleResetPwd(scope.row)" v-auth:edit>
                            <icon-ep-lock class="icon" />重置密码
                        </el-button>
                        <el-button link size="small" type="danger" @click="handleDelete(scope.row)"
                            v-auth:delete><icon-ep-delete class="icon" />删除</el-button>
                    </div>
                    <!-- 回收站 -->
                    <div v-else>
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

        <!-- 分页 -->
        <pagination v-show="total > 0" :total="total" v-model:page="queryParams.pageNum"
            v-model:limit="queryParams.pageSize" @pagination="getList" />

        <!-- 查看详情对话框 -->
        <el-dialog title="会员详情" v-model="openView" width="800px" append-to-body>
            <el-form :model="form" label-width="120px">
                <el-row>
                    <el-col :span="24">
                        <el-form-item label="会员ID：">{{ form.m_id }}</el-form-item>
                    </el-col>
                    <el-col :span="24">
                        <el-form-item label="用户名：">{{ form.username }}</el-form-item>
                    </el-col>
                    <el-col :span="24">
                        <el-form-item label="昵称：">{{ form.nickname }}</el-form-item>
                    </el-col>
                    <el-col :span="24">
                        <el-form-item label="头像：" v-if="form.avatar">
                            <el-image :src="imgUrl(form.avatar)" :preview-src-list="[imgUrl(form.avatar)]"
                                :preview-teleported="true" style="width: 100px; height: 100px;" />
                        </el-form-item>
                    </el-col>
                    <el-col :span="24">
                        <el-form-item label="邮箱：">{{ form.email || '-' }}</el-form-item>
                    </el-col>
                    <el-col :span="24">
                        <el-form-item label="手机号：">{{ form.mobile || '-' }}</el-form-item>
                    </el-col>
                    <el-col :span="24">
                        <el-form-item label="性别：">
                            <span v-if="form.sex === 0">男</span>
                            <span v-else-if="form.sex === 1">女</span>
                            <span v-else>未知</span>
                        </el-form-item>
                    </el-col>
                    <el-col :span="24">
                        <el-form-item label="状态：">
                            <el-tag v-if="form.status === 0" type="danger">停用</el-tag>
                            <el-tag v-else-if="form.status === 1" type="success">正常</el-tag>
                        </el-form-item>
                    </el-col>
                    <el-col :span="24">
                        <el-form-item label="最后登录IP：">{{ form.login_ip || '-' }}</el-form-item>
                    </el-col>
                    <el-col :span="24">
                        <el-form-item label="最后登录时间：">{{ parseTime(form.last_login_time) || '-' }}</el-form-item>
                    </el-col>
                    <el-col :span="24">
                        <el-form-item label="密码更新时间：">{{ parseTime(form.password_update_time) || '-' }}</el-form-item>
                    </el-col>
                    <el-col :span="24">
                        <el-form-item label="创建时间：">{{ parseTime(form.create_time) }}</el-form-item>
                    </el-col>
                    <el-col :span="24">
                        <el-form-item label="更新时间：">{{ parseTime(form.update_time) }}</el-form-item>
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

        <!-- 新增/修改对话框 -->
        <el-dialog :title="title" v-model="openEdit" width="800px" append-to-body>
            <el-form ref="editRef" :model="form" :rules="rules" label-width="120px">
                <el-row>
                    <el-col :span="12">
                        <el-form-item label="用户名" prop="username">
                            <el-input v-model="form.username" placeholder="请输入用户名" :disabled="form.m_id" />
                        </el-form-item>
                    </el-col>
                    <el-col :span="12">
                        <el-form-item label="昵称" prop="nickname">
                            <el-input v-model="form.nickname" placeholder="请输入昵称" />
                        </el-form-item>
                    </el-col>
                    <el-col :span="12">
                        <el-form-item label="邮箱" prop="email">
                            <el-input v-model="form.email" placeholder="请输入邮箱" />
                        </el-form-item>
                    </el-col>
                    <el-col :span="12">
                        <el-form-item label="手机号" prop="mobile">
                            <el-input v-model="form.mobile" placeholder="请输入手机号" maxlength="11" />
                        </el-form-item>
                    </el-col>
                    <!-- <el-col :span="12">
                        <el-form-item label="密码" prop="password"
                            :rules="form.m_id ? [] : [{ required: true, message: '密码不能为空', trigger: 'blur' }]">
                            <el-input v-model="form.password" type="password" placeholder="请输入密码(至少6位)" show-password />
                        </el-form-item>
                    </el-col> -->
                    <el-col :span="12">
                        <el-form-item label="性别" prop="sex">
                            <el-select v-model="form.sex" placeholder="请选择性别" style="width: 100%">
                                <el-option label="男" :value="0" />
                                <el-option label="女" :value="1" />
                                <el-option label="未知" :value="2" />
                            </el-select>
                        </el-form-item>
                    </el-col>
                    <!-- <el-col :span="12">
                        <el-form-item label="头像" prop="avatar">
                            <image-upload v-model="form.avatar" :limit="1" />
                        </el-form-item>
                    </el-col> -->
                    <el-col :span="12">
                        <el-form-item label="状态" prop="status">
                            <el-select v-model="form.status" placeholder="请选择状态" style="width: 100%">
                                <el-option label="停用" :value="0" />
                                <el-option label="正常" :value="1" />
                            </el-select>
                        </el-form-item>
                    </el-col>
                    <el-col :span="24">
                        <el-form-item label="备注" prop="remark">
                            <el-input v-model="form.remark" type="textarea" :rows="3" placeholder="请输入备注" />
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

        <!-- 重置密码对话框 -->
        <el-dialog title="重置密码" v-model="openResetPwd" width="500px" append-to-body>
            <el-form ref="resetPwdRef" :model="resetPwdForm" :rules="resetPwdRules" label-width="100px">
                <el-row>
                    <el-col :span="24">
                        <el-form-item label="会员ID：">{{ resetPwdForm.m_id }}</el-form-item>
                    </el-col>
                    <el-col :span="24">
                        <el-form-item label="用户名：">{{ resetPwdForm.username }}</el-form-item>
                    </el-col>
                    <el-col :span="24">
                        <el-form-item label="昵称：">{{ resetPwdForm.nickname }}</el-form-item>
                    </el-col>
                    <el-col :span="24">
                        <el-form-item label="新密码" prop="password">
                            <el-input v-model="resetPwdForm.password" type="password" placeholder="请输入新密码(至少6位)"
                                show-password />
                        </el-form-item>
                    </el-col>
                </el-row>
            </el-form>
            <template #footer>
                <div class="dialog-footer">
                    <el-button @click="openResetPwd = false">取 消</el-button>
                    <el-button type="primary" @click="submitResetPwd">确 定</el-button>
                </div>
            </template>
        </el-dialog>
    </div>
</template>

<script setup>
import { list, getInfo, add, update, delMember, resetPwd, trueDel } from "@/api/kucoder/system/member"
import { imgUrl } from "@/utils/kucoder"

defineOptions({
    name: "kucoder_system_member",
})

const { proxy } = getCurrentInstance()

const memberList = ref([])
const loading = ref(true)
const showSearch = ref(true)
const ids = ref([])
const single = ref(true)
const multiple = ref(true)
const total = ref(0)
const openView = ref(false)
const openEdit = ref(false)
const openResetPwd = ref(false)
const title = ref("")

const data = reactive({
    form: {},
    resetPwdForm: {},
    queryParams: {
        pageNum: 1,
        pageSize: 10,
        username: undefined,
        nickname: undefined,
        mobile: undefined,
        status: undefined,
        recycle: undefined
    },
    rules: {
        username: [
            { required: true, message: "用户名不能为空", trigger: "blur" }
        ],
        nickname: [
            { required: true, message: "昵称不能为空", trigger: "blur" }
        ],
        password: [
            { min: 6, message: "密码长度不能小于6位", trigger: "blur" }
        ],
        status: [
            { required: true, message: "状态不能为空", trigger: "change" }
        ]
    },
    resetPwdRules: {
        password: [
            { required: true, message: "新密码不能为空", trigger: "blur" },
            { min: 6, message: "密码长度不能小于6位", trigger: "blur" }
        ]
    }
})

const { queryParams, form, rules, resetPwdForm, resetPwdRules } = toRefs(data)

/** 查询会员列表 */
function getList() {
    loading.value = true
    list(queryParams.value).then(response => {
        memberList.value = response.data.list
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
    queryParams.value.recycle = undefined
    getList()
}

/** 刷新 */
function refresh() {
    queryParams.value.pageNum = 1
    queryParams.value.recycle = undefined
    getList()
}

/** 多选框选中数据 */
function handleSelectionChange(selection) {
    ids.value = selection.map(item => item.m_id)
    single.value = !selection.length
    multiple.value = !selection.length
}

/** 查看详情操作 */
function handleView(row) {
    const id = row.m_id
    getInfo(id).then(response => {
        form.value = response.data
        openView.value = true
    })
}

/** 新增按钮操作 */
function handleAdd() {
    form.value = {}
    title.value = "新增会员"
    openEdit.value = true
}

/** 修改按钮操作 */
function handleEdit(row) {
    const id = row.m_id || ids.value[0]
    getInfo(id).then(response => {
        form.value = response.data
        form.value.password = '' // 编辑时不显示密码
        title.value = "修改会员"
        openEdit.value = true
    })
}

/** 提交新增/修改 */
function submitEdit() {
    proxy.$refs.editRef.validate(valid => {
        if (valid) {
            if (form.value.m_id) {
                update(form.value).then(response => {
                    proxy.$modal.msgSuccess("修改成功")
                    openEdit.value = false
                    getList()
                })
            } else {
                add(form.value).then(response => {
                    proxy.$modal.msgSuccess("新增成功")
                    openEdit.value = false
                    getList()
                })
            }
        }
    })
}

/** 重置密码按钮操作 */
function handleResetPwd(row) {
    resetPwdForm.value = {
        m_id: row.m_id,
        username: row.username,
        nickname: row.nickname,
        password: ''
    }
    openResetPwd.value = true
}

/** 提交重置密码 */
function submitResetPwd() {
    proxy.$refs.resetPwdRef.validate(valid => {
        if (valid) {
            resetPwd(resetPwdForm.value).then(response => {
                proxy.$modal.msgSuccess("重置密码成功")
                openResetPwd.value = false
            })
        }
    })
}

/** 删除按钮操作 */
function handleDelete(row) {
    const deleteIds = row.m_id || ids.value
    proxy.$modal.confirm('是否确认删除编号为"' + deleteIds + '"的数据项?').then(function () {
        return delMember({ id: deleteIds })
    }).then(() => {
        getList()
        proxy.$modal.msgSuccess("删除成功")
    }).catch(() => { })
}

/** 回收站按钮操作 */
function handleRecycle() {
    queryParams.value.recycle = 1
    queryParams.value.pageNum = 1
    getList()
}

/** 恢复按钮操作 */
function handleRestore(row) {
    const restoreIds = row.m_id || ids.value
    proxy.$modal.confirm('是否确认恢复编号为"' + restoreIds + '"的数据项?').then(function () {
        return update({ m_id: restoreIds, delete_restore: 1 })
    }).then(() => {
        getList()
        proxy.$modal.msgSuccess("恢复成功")
    }).catch(() => { })
}

/** 彻底删除按钮操作 */
function handleDeleteTrue(row) {
    const deleteIds = row.m_id || ids.value
    proxy.$modal.confirm('是否确认彻底删除编号为"' + deleteIds + '"的数据项?此操作不可恢复!').then(function () {
        return trueDel({ id: deleteIds })
    }).then(() => {
        getList()
        proxy.$modal.msgSuccess("彻底删除成功")
    }).catch(() => { })
}

getList()
</script>
