<template>
    <div class="main y c ac gap-5">
        <div class="y c ac gap-3 c-white">
            <h1 class="title">安装kucoder系统</h1>
        </div>

        <div v-if="step === 1" class="body y c ac gap-5">
            <p>环境检查</p>
            <el-table :data="envCheckData">
                <el-table-column label="要求" prop="need" align="center"></el-table-column>
                <el-table-column label="检测" prop="res" align="center" :show-overflow-tooltip="true">
                    <template #default="scope">
                        <span v-if="scope.row.check">{{ scope.row.res }} ✅</span>
                        <span v-else>{{ scope.row.res }} ❌</span>
                    </template>
                </el-table-column>
            </el-table>
            <el-button type="primary" @click="gotoNextStep">下一步</el-button>
        </div>

        <div v-if="step === 2" class="body y c ac gap-5">
            <p>数据库/Redis/后台</p>
            <el-form :model="form" :rules="rules" label-width="120px">
                <div class="x c gap-10">
                    <!-- db -->
                    <div>
                        <el-form-item label="数据库类型">
                            <el-select v-model="form.db_type" placeholder="请选择数据库类型">
                                <el-option label="mysql" value="mysql"></el-option>
                                <!-- <el-option label="postgreSQL" value="pgsql"></el-option> -->
                            </el-select>
                        </el-form-item>
                        <el-form-item label="数据库地址" prop="db_host">
                            <el-input v-model="form.db_host" placeholder="数据库地址 默认: 127.0.0.1" />
                        </el-form-item>
                        <el-form-item label="数据库端口" prop="db_port">
                            <el-input v-model="form.db_port" placeholder="数据库地址 默认: 3306" />
                        </el-form-item>
                        <el-form-item label="数据库名称" prop="db_name">
                            <el-input v-model="form.db_name" placeholder="数据库名称" />
                        </el-form-item>
                        <el-form-item label="数据库用户" prop="db_user">
                            <el-input v-model="form.db_user" placeholder="数据库用户" />
                        </el-form-item>
                        <el-form-item label="数据库密码" prop="db_password">
                            <el-input v-model="form.db_password" placeholder="数据库密码" show-password />
                        </el-form-item>
                        <el-form-item label="数据表前缀" prop="db_prefix">
                            <el-input v-model="form.db_prefix" placeholder="数据表前缀" />
                        </el-form-item>
                    </div>
                    <!-- redis -->
                    <div>
                        <el-form-item label="Redis地址">
                            <el-input v-model="form.redis_host" placeholder="Redis地址 默认: 127.0.0.1" />
                        </el-form-item>
                        <el-form-item label="Redis端口">
                            <el-input v-model="form.redis_port" placeholder="Redis端口 默认: 6379" />
                        </el-form-item>
                        <el-form-item label="Redis密码">
                            <el-input v-model="form.redis_password" placeholder="Redis密码" show-password />
                        </el-form-item>
                        <el-form-item label="Redis前缀">
                            <el-input v-model="form.redis_prefix" placeholder="Redis存储前缀" />
                        </el-form-item>
                        <el-form-item label="后台登录用户名" prop="admin_username">
                            <el-input v-model="form.admin_username" placeholder="登录用户名" />
                        </el-form-item>
                        <el-form-item label="后台登录密码" prop="admin_password">
                            <el-input v-model="form.admin_password" placeholder="登录密码" show-password />
                        </el-form-item>
                    </div>
                </div>
            </el-form>
            <div class="x c ac gap-5">
                <el-button type="info" @click="gotoPreviousStep">上一步</el-button>
                <el-button type="primary" @click="gotoNextStep">下一步</el-button>
            </div>
        </div>

        <!-- <div v-if="step === 3" class="body y c ac gap-20">
            <div>安装后端composer依赖及前端pnpm依赖</div>
            <el-button type="primary" @click="installRelySubmit">点击安装依赖</el-button>
            <div class="x c ac gap-5">
                <el-button type="info" @click="gotoPreviousStep">上一步</el-button>
                <el-button type="primary" @click="gotoNextStep">下一步或已手动安装 跳过</el-button>
            </div>
        </div> -->
        <div v-if="step === 3" class="body y c ac gap-5">
            <div>微信扫码</div>
            <div class="x as gap-5">
                <img :src="qrcode" alt="">
                <div class="m-t-3 y gap-5">
                    <p>微信扫码并关注公众号后 回复“安装系统”</p>
                    <p>将得到的6位数字校验码 填入下方</p>
                    <el-form-item label="校验码">
                        <el-input v-model="wx_code" placeholder="请输入6位数字校验码" />
                    </el-form-item>
                </div>
            </div>
            <div class="x c ac gap-5">
                <el-button type="info" @click="gotoPreviousStep">上一步</el-button>
                <el-button type="primary" @click="init" :disabled="envInited">校验并初始化</el-button>
                <el-button type="primary" @click="installSubmit" :disabled="!envInited">开始安装</el-button>
            </div>
        </div>

    </div>
</template>

<script setup>
import { kcMsg, kcLoading, kcAlert} from '@/utils/kucoder'
import { envCheck, getQrcode, verifyWxCode, install, initEnv } from '@/api/kucoder/system/install'
import { HMR } from "@/utils/hmr"

const step = ref(1)
const envCheckData = ref([])
const envInited = ref(false)

const init = async () => {
    if (!await checkWxCode()){
        // kcMsg('校验码不正确')
        return;
    } 
    await HMR.disable()
    const loading = kcLoading('正在初始化环境...请勿操作')
    initEnv(form.value)
        .then(async (res) => {
            console.log('init', res)
            loading.close()
            kcMsg('初始化环境成功')
            setTimeout(() => {
                envInited.value = true
            }, 2000)
            await HMR.enable()
        })
        .catch(async (err) => {
            console.log('init', err)
            loading.close()
            await HMR.enable()
        })
}
const wx_code = ref(null)
async function checkWxCode() {
    if (!wx_code.value || !/^[0-9]{6}$/.test(wx_code.value)) {
        kcMsg('校验码格式不正确')
        return false
    }
    return await verifyWxCode({ wx_code: wx_code.value })
        .then(res => {
            return true
        })
        .catch(err => {
            return false
        })
}
const installSubmit = async () => {
    step.value = null;
    const loading = kcLoading('正在安装kucoder中...请勿操作 耗时较长 执行步骤可在后端控制台查看')
    install(form.value, { timeout: 0 })
        .then(async (res) => {
            console.log('install', res)
            kcMsg('安装成功')
            loading.close()
            kcAlert(getLoginPath(res.data.vue_admin_entry), '安装成功，后台登录地址如下',
                { showClose: false, confirmButtonText: '点击跳转到登录页面' }
            ).then(async () => { 
                await HMR.enable()
                window.location.href = getLoginPath(res.data.vue_admin_entry)
            })
        })
        .catch(async (err) => {
            console.log('install', err)
            kcAlert(err.msg)
            loading.close()
            await HMR.enable()
        })
}

function getLoginPath(adminBasePath) {
    const currentUrl = window.location.href
    const loginUrl  = currentUrl.replace('/admin',adminBasePath).replace('/install','/login')
    return loginUrl
}

const form = ref({
    db_type: 'mysql',
    db_host: '127.0.0.1',
    db_port: '3306',
    db_name: '',
    db_user: '',
    db_password: '',
    db_prefix: 'kc_',
    redis_host: '127.0.0.1',
    redis_port: '6379',
    redis_prefix:'kucoder:',
    redis_password: '',
    admin_username: '',
    admin_password: '',
})
const rules = ref({
    db_name: [
        { required: true, message: '请输入数据库名称', trigger: 'blur' },
    ],
    db_user: [
        { required: true, message: '请输入数据库用户', trigger: 'blur' },
    ],
    db_password: [
        { required: true, message: '请输入数据库用户密码', trigger: 'blur' },
    ],
    admin_username: [
        { required: true, message: '请输入后台登录用户名', trigger: 'blur' },
    ],
    admin_password: [
        { required: true, message: '请输入后台登录用户密码', trigger: 'blur' },
    ],
})

const gotoNextStep = () => {
    if (step.value === 1) {
        // 环境检测
        const $noCheckData = envCheckData.value.filter(item => !item.check)
        if ($noCheckData.length > 0) {
            kcMsg($noCheckData[0].res)
            return
        }
    }
    if (step.value === 2) {
        // 数据库配置
        if (!form.value.db_name || !form.value.db_user || !form.value.db_password) {
            kcMsg('数据库 名称/用户/密码不能为空')
            return
        }
        if (!form.value.admin_username || !form.value.admin_password) {
            kcMsg('后台登录 用户名/密码不能为空')
            return
        }
    }
    step.value++
}

const gotoPreviousStep = () => {
    step.value--
}

const qrcode = ref('')
watch(() => step.value, (val) => {
    if (val === 3) {
        getQrcode().then(res => {
            console.log('getQrcode', res)
            qrcode.value = res.data.qrcode
        }).catch(err => {
            kcAlert(err.msg)
        })
    }
})

onMounted(async () => {
    console.log('HMR状态：',await HMR.getStatus());
    if(!await HMR.getStatus()){
        await HMR.enable()
    }
    envCheck().then(res => {
        console.log('envCheck', res)
        envCheckData.value = res.data
    }).catch(err => {
        kcAlert(err.msg)
    })
})
</script>

<style lang="scss" scoped>
.main {
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100%;
    /* fallback for old browsers */
    background: #373b44;
    /* Chrome 10-25, Safari 5.1-6 */
    background: -webkit-linear-gradient(to right, #4286f4, #373b44);
    /* W3C, IE 10+/ Edge, Firefox 16+, Chrome 26+, Opera 12+, Safari 7+ */
    background: linear-gradient(to right, #4286f4, #373b44);
}

.title {
    margin: 0px auto 30px auto;
    // text-align: center;
    // color: #707070;
    font-weight: bold;
    font-size: 20px;
}

.body {
    border-radius: 6px;
    background: #fbfbfb;
    width: 60%;
    padding: 25px 25px 5px 25px;
    z-index: 1;

    .el-input {
        height: 40px;

        input {
            height: 40px;
        }
    }

    .input-icon {
        height: 39px;
        width: 14px;
        margin-left: 0px;
    }
}
</style>