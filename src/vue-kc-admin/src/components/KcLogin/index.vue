<template>
    <!-- 登录kucoder -->
    <div v-if="!userStore.kc.user.token" :span="12" class="x ac" :class="attrs.class">
        <el-tag type="danger" class="mr-2">你还未登录kucoder</el-tag>
        <el-tag effect="dark" type="primary" size="small" @click="handleLoginKc" class="cursor-pointer">去登录</el-tag>
    </div>
    <div v-else :span="12" class="x ac" :class="attrs.class">
        <el-tag type="success" class="mr-2">{{ userStore.kc.user.nickname }}你已登录kucoder</el-tag>
        <el-tag effect="dark" type="info" size="small" @click="handleLogoutKc" class="cursor-pointer">退出</el-tag>
    </div>

    <!-- 登录dialog -->
    <el-dialog title="登录到kucoder" v-model="kcLoginDialogVisible" width="400px" draggable class="p-5" center
        align-center>
        <el-form ref="kcLoginFormRef" :model="kcLoginForm" :rules="kcLoginFormRules" label-width="80px" class="y c">
            <!-- 用户名 -->
            <el-form-item label="用户名/邮箱" prop="username">
                <el-input v-model="kcLoginForm.username" placeholder="请输入用户名" />
            </el-form-item>
            <!-- 密码 -->
            <el-form-item label="密码" prop="password">
                <el-input v-model="kcLoginForm.password" placeholder="请输入密码" type="password" show-password />
            </el-form-item>
            <!-- 邮箱 -->
            <el-form-item label="邮箱" prop="email" v-if="register" required>
                <el-input v-model="kcLoginForm.email" placeholder="请输入邮箱" />
            </el-form-item>
            <!-- 验证码 -->
            <el-form-item label="验证码" prop="code" class="x c ac">
                <el-row :gutter="20" class="x ac">
                    <el-col :span="14">
                        <el-input v-model="kcLoginForm.code" placeholder="请输入验证码" />
                    </el-col>
                    <el-col :span="10">
                        <img :src="codeImg" @click="getCode" class="cursor-pointer h-30px w-120px" />
                    </el-col>
                </el-row>
            </el-form-item>
        </el-form>
        <template #footer>
            <el-button @click="cancelKcLogin">取消</el-button>
            <el-button type="primary" @click="kcLogin" v-if="!register">登录</el-button>
            <el-button type="info" @click="kcNoAccount" v-if="!register">没有账号</el-button>
            <el-button type="success" @click="kcRegister" v-if="register">注册</el-button>
        </template>
    </el-dialog>

    <!-- 站点授权 -->
    <kc-authorize v-model="kcAuthorizeDialogVisible"></kc-authorize>
</template>

<script setup>
import { loginKc, logoutKc, getCodeImg, registerKc } from "@/api/kucoder/plugin/kcLogin"
import useUserStore from '@/store/modules/user'
import { validEmail } from '@/utils/validate'
import { AUTH_E_CODE, KC_CODE_PREFIX } from '@/utils/constant'
import { kcMsg, kcAlert, kcLoading, kcConfirm } from "@/utils/kucoder"

/* 
const props = defineProps({
    modelValue: { type: Boolean, default: false },
})
const emits = defineEmits(['update:modelValue']) 
const kcLoginDialogVisible = computed(() => props.modelValue)
*/
defineOptions({
    inheritAttrs: false
})
const attrs = useAttrs()
const emits = defineEmits(['success'])
const { proxy } = getCurrentInstance();

// 登录到插件市场
const userStore = useUserStore()
const kcLoginDialogVisible = ref(false)
const kcLoginFormRef = useTemplateRef('kcLoginFormRef')
const codeImg = ref("")
const kcLoginForm = ref({
    username: '',
    password: '',
    email: '',
    code: undefined,
    uuid: undefined,
})
const kcLoginFormRules = ref({
    username: [{ required: true, message: "用户名不能为空", trigger: "blur" }, { min: 2, message: "用户名称长度必须大于2 ", trigger: "blur" }],
    password: [{ required: true, message: "密码不能为空", trigger: "blur" }, { min: 6, message: "会员密码长度必须大于等于6位", trigger: "blur" }, { pattern: /^[^<>"'|\\]+$/, message: "不能包含非法字符：< > \" ' \\\ |", trigger: "blur" }],
    code: [{ required: true, message: "验证码不能为空", trigger: "blur" }],
})
function cancelKcLogin() {
    kcLoginDialogVisible.value = false
    register.value = false
    // emits('update:modelValue', false)
    codeImg.value = ""
    kcLoginForm.value = {
        username: '',
        password: '',
        email: '',
        code: undefined,
        uuid: undefined,
    }
}

const register = ref(false)
function kcNoAccount() {
    register.value = true
}
function kcRegister() {
    if (!kcLoginForm.value.email || !validEmail(kcLoginForm.value.email)) {
        kcMsg('邮箱格式不正确'); return
    }
    kcLoginFormRef.value.validate(valid => {
        if (valid) {
            console.log('kcLoginForm', kcLoginForm.value)
            const loading = kcLoading('注册中...')
            registerKc(kcLoginForm.value, { headers: { showErrMsg: false } })
                .then((res) => {
                    loading.close()
                    console.log('kcRegister res', res)
                    kcAlert(res.data.msg)
                    cancelKcLogin()
                })
                .catch(err => {
                    loading.close()
                    console.log('err', err)
                    loginErrHandle(err)
                })
        }
    })
}

function getCode() {
    getCodeImg().then(({ res, code, msg }) => {
        console.log('验证码response', res)
        codeImg.value = res.captcha.img_base64;
        kcLoginForm.value.uuid = res.captcha.uuid;
    })
}

function kcLogin() {
    kcLoginFormRef.value.validate(valid => {
        if (valid) {
            console.log('kcLoginForm', kcLoginForm.value)
            loginKc(kcLoginForm.value, { headers: { showErrMsg: false } })
                .then(({ res, code, msg }) => {
                    console.log('kcLogin res', res)
                    userStore.kc.site_set = res.data.site_set
                    delete res.data.site_set
                    userStore.kc.user = res.data
                    kcLoginDialogVisible.value = false
                    emits('success', res.data)
                    proxy.$modal.msgSuccess("登录成功")
                })
                .catch(err => {
                    console.log('err', err)
                    loginErrHandle(err)
                })
        }
    })
}

// 站点授权
const kcAuthorizeDialogVisible = ref(false)
function loginErrHandle(err) {
    const code = err.code.toString()
    console.log('err2', code.startsWith(KC_CODE_PREFIX))
    if (code.startsWith(KC_CODE_PREFIX)) {
        const kcCode = code.substring(3)
        if (AUTH_E_CODE.includes(Number(kcCode))) {
            kcLoginDialogVisible.value = false
            kcConfirm(err.msg, '系统提示', {
                type: 'warning',
                confirmButtonText: '去授权',
            }).then(confirm => {
                console.log('action', confirm)
                kcAuthorizeDialogVisible.value = true
            }).catch(cancel => {
                console.log('action', cancel)
                kcAuthorizeDialogVisible.value = false
            })
        } else {
            kcAlert(err.msg)
        }
    } else {
        console.log('err3', err)
        kcAlert(err.msg)
    }
}

function kcLoginMesBox() {
    return ElMessageBox.confirm('你还未登录kucoder 请登录', '系统提示', {
        confirmButtonText: '登录',
        cancelButtonText: '取消',
        type: 'warning',
    })
        .then(() => {
            getCode()
            kcLoginDialogVisible.value = true
        })
        .catch(() => { })
}
function handleLoginKc() {
    getCode()
    kcLoginDialogVisible.value = true
}

function handleLogoutKc() {
    return ElMessageBox.confirm('确定退出kucoder系统吗', '系统提示', {
        confirmButtonText: '退出',
        cancelButtonText: '取消',
        type: 'warning',
    })
        .then(() => {
            logoutKc({ kcToken: userStore.kc.user.token })
                .then(({ res, code, msg }) => {
                    userStore.kc.user = {}
                    userStore.kc.site_set = {}
                    kcLoginForm.value = {
                        username: '',
                        password: '',
                        code: undefined,
                        uuid: undefined,
                    }
                })
        })
        .catch(() => { })
}

getCodeImg().then(({ res, code, msg }) => {
    console.log('response', res)
    codeImg.value = "data:image/gif;base64," + res.captcha.img_base64;
    kcLoginForm.value.uuid = res.captcha.uuid;
})

defineExpose({ kcLoginMesBox })
</script>

<style lang="scss" scoped></style>