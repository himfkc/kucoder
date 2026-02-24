<template>
    <div class="app-container">
        <div class="header"></div>
        <div class="x w">
            <div v-for="item in pluginList" :key="item.id" class="y m-r-5 m-b-10 plugin" @click="pluginClick(item)">
                <div class="">
                    <el-image fit="contain" :src="imgUrl(item.img, marketFileDomain)" />
                </div>
                <div class="y gap-y-6 p-x-1">
                    <div class="color-gray-500 fs-14">{{ item.title }}</div>
                    <div class="x sb c-gray ">
                        <div class="x">
                            <icon-ep-download class="icon" /><span>{{ item.download }}</span>
                        </div>
                        <div>
                            <div v-if="item.fee_type == 0" class="color-green-500 fs-14">免费</div>
                            <div v-else class="x">
                                <el-tag type="info" size="small" class="mr-1 ml-1" v-if="item.common_fee">
                                    ￥{{ item.common_fee }}</el-tag>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- 插件弹框 -->
        <div class="dialog">
            <el-dialog v-model="dialogVisible" title="插件详情" width="80%" :fullscreen="fullscreen">
                <template #header>
                    <div class="x sb">
                        <div>插件详情</div>
                        <div class="">
                            <el-tooltip effect="dark" :content="fullscreenText" placement="top">
                                <icon-ep-fullScreen class="icon color-gray m-b-3 p-b-1" @click="fullscreenClick"
                                    style="outline:none" />
                            </el-tooltip>
                        </div>
                    </div>
                </template>
                <div class="x gap-x-10">
                    <div class="w-68%">
                        <div class="x">
                            <!-- 图片轮播  -->
                            <div class="imgs w-50%">
                                <el-carousel arrow="always" :interval="2000">
                                    <el-carousel-item v-for="(item, key) in strToArray(plugin.plugin_imgs)" :key="key">
                                        <el-image fit="cover" :src="imgUrl(item, marketFileDomain)"
                                            :preview-src-list="getImgList(plugin.plugin_imgs)"
                                            :preview-teleported="true" show-progress hide-on-click-modal />
                                    </el-carousel-item>
                                </el-carousel>
                            </div>
                            <!-- 插件信息 -->
                            <div class="infos w-75% m-l-5">
                                <el-descriptions :column="2" size="large" border>
                                    <el-descriptions-item>
                                        <template #label>应用</template>
                                        <el-tag size="small">{{ plugin.title }}</el-tag>
                                    </el-descriptions-item>
                                    <el-descriptions-item>
                                        <template #label>标识</template>
                                        <el-tag size="small">{{ plugin.name }}</el-tag>
                                    </el-descriptions-item>
                                    <el-descriptions-item>
                                        <template #label>分类</template>
                                        <!-- <el-tag size="small">{{ plugin.type }}</el-tag> -->
                                        <dict-tag :options="enumFieldData.plugin_type" :value="plugin.plugin_type" />
                                    </el-descriptions-item>
                                    <el-descriptions-item>
                                        <template #label>最新版本</template>
                                        <el-tag size="small" type="info">{{ plugin.version }}</el-tag>
                                    </el-descriptions-item>
                                    <el-descriptions-item>
                                        <template #label>作者</template>
                                        <el-tag size="small" type="info">{{ plugin.author }}</el-tag>
                                    </el-descriptions-item>
                                    <el-descriptions-item>
                                        <template #label>主页文档</template>
                                        <el-tooltip content="跳转到应用主页" placement="top" v-if="plugin.homepage">
                                            <el-tag size="small" @click="goto(plugin.homepage)" class="cursor-pointer">
                                                <div class="x c ac">
                                                    <icon-ep-link class="icon" />点击跳转到应用主页
                                                </div>
                                            </el-tag>
                                        </el-tooltip>
                                    </el-descriptions-item>
                                    <el-descriptions-item>
                                        <template #label>下载</template>
                                        <el-tag size="small" type="info">{{ plugin.download }}</el-tag>
                                    </el-descriptions-item>
                                    <el-descriptions-item>
                                        <template #label>价格</template>
                                        <el-tag type="info" size="small" class="mr-1 ml-1">
                                            ￥{{ plugin.common_fee }}元/普通
                                        </el-tag>
                                        <el-tag type="info" size="small" class="mr-1 ml-1">
                                            ￥{{ plugin.advance_fee }}元/高级
                                        </el-tag>
                                    </el-descriptions-item>
                                    <el-descriptions-item>
                                        <template #label>kucoder版本</template>
                                        <el-tag size="small" type="info">{{ plugin.kucoder_version }}</el-tag>
                                    </el-descriptions-item>
                                    <el-descriptions-item>
                                        <template #label>最近更新</template>
                                        <el-tag size="small" type="info">{{ plugin.update_time }}</el-tag>
                                    </el-descriptions-item>

                                </el-descriptions>
                            </div>
                        </div>
                        <div class="content">
                            <div class="fs-18 font-bold mb-10">应用介绍</div>
                            <div v-html="DOMPurify.sanitize(plugin.intro)"></div>
                        </div>
                    </div>
                    <div class="w-30%">
                        <!-- 登录kucoder -->
                        <kc-login ref="kcLoginRef" class="mb-5" @success="kcLoginSuccess"></kc-login>

                        <!-- 授权信息 -->
                        <div class="y gap-y-15">
                            <!-- 普通授权 -->
                            <div class="common" v-if="plugin.fee_type == 1">
                                <div class="x sb pb-5">
                                    <el-text>普通授权 <span class="color-red-500">￥{{ plugin.common_fee }}</span></el-text>
                                    <el-button type="info" size="small"
                                        @click="buyPlugin(plugin, 'common')">购买普通授权</el-button>
                                </div>

                                <div class="y gap-y-3">
                                    <el-row class="x ac">
                                        <icon-fa-check-circle class="icon color-lime-500" />{{
                                            numberToChinese(common_year) }}年内免费升级更新
                                        <el-tooltip content="到期后仍可使用，但不能下载及升级最新版本" placement="top">
                                            <icon-fa-question-circle class="icon" />
                                        </el-tooltip>
                                    </el-row>
                                    <el-row class="x ac">
                                        <icon-fa-check-circle class="icon color-lime-500" />{{
                                            numberToChinese(common_year) }}年内免费技术支持
                                        <el-tooltip content="不含二次开发" placement="top">
                                            <icon-fa-question-circle class="icon" />
                                        </el-tooltip>
                                    </el-row>
                                    <el-row class="x ac">
                                        <icon-fa-times-circle class="icon color-red-400" />仅限用于自营项目，禁止用于外包项目
                                        <el-tooltip content="外包项目指非购买者主体的其它主体项目" placement="top">
                                            <icon-fa-question-circle class="icon" />
                                        </el-tooltip>
                                    </el-row>
                                    <el-row class="x ac">
                                        <icon-fa-times-circle class="icon color-red-400" />因代码可复制，不支持七天无理由退款
                                    </el-row>
                                    <el-row class="x ac">
                                        <icon-fa-times-circle class="icon color-red-400" />禁止转售或分享插件源码
                                        <el-tooltip content="对于违反的行为，保留追究法律责任的权利" placement="top">
                                            <icon-fa-question-circle class="icon" />
                                        </el-tooltip>
                                    </el-row>
                                    <el-row class="x ac">
                                        <icon-fa-exclamation-circle class="icon color-orange-500" />仅限在kucoder系统内可以使用
                                        <el-tooltip content="对于违反的行为，保留追究法律责任的权利" placement="top">
                                            <icon-fa-question-circle class="icon" />
                                        </el-tooltip>
                                    </el-row>
                                    <el-row class="x ac">
                                        <icon-fa-exclamation-circle class="icon color-orange-500" />禁止用于任何形式的违法用途
                                        <el-tooltip content="禁止将插件用于任何形式的违法用途，否则将依法追究相关责任，一经发现，永久封禁账号，并向公安机关举报"
                                            placement="top">
                                            <icon-fa-question-circle class="icon" />
                                        </el-tooltip>
                                    </el-row>
                                </div>
                            </div>
                            <!-- 高级授权 -->
                            <div class="advanced" v-if="plugin.fee_type == 1">
                                <div class="x sb pb-5">
                                    <el-text>高级授权 <span class="color-red-500">￥{{ plugin.advance_fee }}</span></el-text>
                                    <el-button type="success" size="small"
                                        @click="buyPlugin(plugin, 'advance')">购买高级授权</el-button>
                                </div>
                                <div class="y gap-y-3">
                                    <el-row class="x ac">
                                        <icon-fa-check-circle class="icon color-lime-500" />{{
                                            numberToChinese(advance_year) }}年内免费升级更新
                                        <el-tooltip content="到期后仍可使用，但不能下载及升级最新版本" placement="top">
                                            <icon-fa-question-circle class="icon" />
                                        </el-tooltip>
                                    </el-row>
                                    <el-row class="x ac">
                                        <icon-fa-check-circle class="icon color-lime-500" />{{
                                            numberToChinese(advance_year) }}年内免费技术支持
                                        <el-tooltip content="不含二次开发" placement="top">
                                            <icon-fa-question-circle class="icon" />
                                        </el-tooltip>
                                    </el-row>
                                    <el-row class="x ac">
                                        <icon-fa-check-circle class="icon color-lime-500" />项目性质不限，可用于自营、外包项目
                                    </el-row>
                                    <el-row class="x ac">
                                        <icon-fa-check-circle class="icon color-lime-500" />源码未加密，可学习可商用
                                    </el-row>
                                    <el-row class="x ac">
                                        <icon-fa-check-circle class="icon color-lime-500" />支持私有化部署
                                    </el-row>
                                    <el-row class="x ac">
                                        <icon-fa-check-circle class="icon color-lime-500" />正版授权，放心使用
                                    </el-row>
                                    <el-row class="x ac">
                                        <icon-fa-check-circle class="icon color-lime-500" />可二次开发
                                    </el-row>
                                    <el-row class="x ac">
                                        <icon-fa-times-circle class="icon color-red-400" />因代码可复制，不支持七天无理由退款
                                    </el-row>
                                    <el-row class="x ac">
                                        <icon-fa-times-circle class="icon color-red-400" />禁止转售或分享插件源码
                                        <el-tooltip content="对于违反的行为，保留追究法律责任的权利" placement="top">
                                            <icon-fa-question-circle class="icon" />
                                        </el-tooltip>
                                    </el-row>
                                    <el-row class="x ac">
                                        <icon-fa-exclamation-circle class="icon color-orange-500" />仅限在kucoder系统内可以使用
                                        <el-tooltip content="对于违反的行为，保留追究法律责任的权利" placement="top">
                                            <icon-fa-question-circle class="icon" />
                                        </el-tooltip>
                                    </el-row>
                                    <el-row class="x ac">
                                        <icon-fa-exclamation-circle class="icon color-orange-500" />禁止用于任何形式的违法用途
                                        <el-tooltip content="禁止将插件用于任何形式的违法用途，否则将依法追究相关责任，一经发现，永久封禁账号，并向公安机关举报"
                                            placement="top">
                                            <icon-fa-question-circle class="icon" />
                                        </el-tooltip>
                                    </el-row>
                                </div>
                            </div>
                            <!-- 免费授权 -->
                            <div v-if="plugin.fee_type == 0">
                                <div class="x sb pb-5">
                                    <el-text>免费授权 <span class="color-red-500">￥0</span></el-text>
                                    <el-button type="info" size="small"
                                        @click="buyPlugin(plugin, 'free')">购买</el-button>
                                </div>

                                <div class="y gap-y-3">
                                    <el-row class="x ac">
                                        <icon-fa-times-circle class="icon color-red-400" />禁止转售或分享插件源码
                                        <el-tooltip content="对于违反的行为，保留追究法律责任的权利" placement="top">
                                            <icon-fa-question-circle class="icon" />
                                        </el-tooltip>
                                    </el-row>
                                    <el-row class="x ac">
                                        <icon-fa-exclamation-circle class="icon color-orange-500" />仅限在kucoder系统内可以使用
                                        <el-tooltip content="对于违反的行为，保留追究法律责任的权利" placement="top">
                                            <icon-fa-question-circle class="icon" />
                                        </el-tooltip>
                                    </el-row>
                                    <el-row class="x ac">
                                        <icon-fa-exclamation-circle class="icon color-orange-500" />禁止用于任何形式的违法用途
                                        <el-tooltip content="禁止将插件用于任何形式的违法用途，否则将依法追究相关责任，一经发现，永久封禁账号，并向公安机关举报"
                                            placement="top">
                                            <icon-fa-question-circle class="icon" />
                                        </el-tooltip>
                                    </el-row>
                                </div>
                            </div>
                        </div>

                        <!-- 版本历史 -->

                    </div>
                </div>
            </el-dialog>
        </div>

        <el-dialog v-model="pluginOrderDialog" title="插件订单" width="30%" append-to-body draggable>
            <div class="y as gap-y-3 fs-14">
                <span>订单标题: 购买插件授权 {{ plugin.title }}</span>
                <span>订单金额: ￥{{ plugin.amount }}</span>
                <span class="x ac">授权类型：
                    <el-tag v-if="plugin.authorize_type === 0" type="primary" size="small">免费授权</el-tag>
                    <el-tag v-if="plugin.authorize_type === 1" type="info" size="small">普通授权</el-tag>
                    <el-tag v-if="plugin.authorize_type === 2" type="success" size="small">高级授权</el-tag>
                    <el-tooltip placement="top">
                        <template #content>
                            免费授权：针对免费插件<br />普通授权：仅能用于自营项目，更新维护期限为一年<br />高级授权：能用于自营及外包项目，更新维护期限为{{
                                numberToChinese(advance_year) }}年，正版授权，可商用可二开
                        </template>
                        <el-icon><icon-ep-question-filled /></el-icon>
                    </el-tooltip>
                </span>
                <span>购买用户：{{ userStore.kc.user.nickname }} ( {{ userStore.kc.user.username }} )</span>
                <span class="x ac">支付方式：
                    <el-radio-group v-model="plugin.pay_type" @change="pluginPayTypeChange">
                        <!-- works when >=2.6.0, recommended ✔️ not work when <2.6.0 ❌ -->
                        <el-radio value="wechat" v-if="plugin_pay_types.includes('wechat')">微信</el-radio>
                        <el-radio value="alipay" v-if="plugin_pay_types.includes('alipay')">支付宝</el-radio>
                        <el-radio value="cardkey" v-if="plugin_pay_types.includes('cardkey')">卡号密钥</el-radio>
                    </el-radio-group>
                </span>
                <div class="y as gap-y-3 fs-14" v-if="cardKeyVisible">
                    <span class="x">
                        <span>授权卡号：</span>
                        <span>
                            <el-input v-model="plugin.cardkey_card" placeholder="请输入卡号" size="small" class="min-w-80" />
                        </span>
                    </span>
                    <span class="x">
                        <span>授权密钥：</span>
                        <span>
                            <el-input v-model="plugin.cardkey_key" placeholder="请输入密钥" size="small" class="min-w-80" />
                        </span>
                    </span>
                    <span>卡号密钥请到kucoder官方淘宝店铺购买：
                        <el-link :href="plugin.e_shop_url" target="_blank" type="danger"> <icon-ep-link
                                class="icon" />店铺插件链接</el-link>
                    </span>
                </div>
            </div>
            <template #footer>
                <el-button @click="pluginOrderDialog = false">取消</el-button>
                <el-button type="primary" @click="submitBuyPlugin">付款下单</el-button>
            </template>
        </el-dialog>

        <el-dialog v-model="payDialog" title="支付二维码" width="20%" append-to-body draggable>
            <div class="y c ac">
                <img :src="payImg" alt="支付二维码" class="w-40">
                <div class="x sb ac mt-3 mb-3">
                    <img v-if="plugin.pay_type === 'wechat'" src="@/assets/icons/svg/wxpay.svg" alt="微信支付" class="w-20">
                    <img v-if="plugin.pay_type === 'alipay'" src="@/assets/icons/svg/alipay.svg" alt="支付宝支付"
                        class="w-20">
                    <span class="color-red-500 ms-2">￥{{ plugin.amount }}</span>
                </div>

            </div>
        </el-dialog>
    </div>
</template>

<script setup>
import DOMPurify from 'dompurify';
import { buy, payQuery } from '@/api/kucoder/plugin/market'
import { handleEnumField, imgUrl, join_path, numberToChinese, kcMsg } from "@/utils/kucoder"
import kcFetch from '@/utils/kcFetch'
import useUserStore from '@/store/modules/user'
import QRCode from 'qrcode'

console.log('market cookie', document.cookie)


const userStore = useUserStore()
const pluginList = ref([])
const enumFieldData = ref({})
const marketFileDomain = ref('')
const common_year = ref(1)
const advance_year = ref(5)
const plugin_pay_types = ref([])
// 查询菜单列表
const getList = () => {
    /* index().then(res => {
        console.log('插件市场列表', res)
        pluginList.value = res.data.data
        enumFieldData.value = handleEnumField(res.enumFieldData || {})
        // 插件市场文件域名
        marketFileDomain.value = res.data.sys_file_url ? res.data.sys_file_url : userStore.site_set.sys_file_url
    }).catch(err => {

    }) */
    const url = join_path(userStore.site_set.sys_url + '/kapi/market/index')
    kcFetch.get(url)
        .then(({ data, msg, code }) => {
            console.log('插件市场列表', data, msg, code)
            if (code !== 1) {
                kcMsg(msg)
                return;
            }
            pluginList.value = data.list
            enumFieldData.value = handleEnumField(data.enumFieldData || {})
            // 插件市场文件域名
            marketFileDomain.value = data.setting.sys_file_url ? data.setting.sys_file_url : userStore.site_set.sys_file_url
            common_year.value = data.setting.common_authorization_year
            advance_year.value = data.setting.advance_authorization_year
            plugin_pay_types.value = data.setting.plugin_pay_types
        })
        .catch(err => {
            console.log('kcFetch err', err)

        })
}

const goto = (URL) => {
    window.open(URL, '_blank')
}

function strToArray(str) {
    return str.split(',')
}

function getImgList(imgs) {
    return strToArray(imgs).map(item => imgUrl(item, marketFileDomain.value))
}

// 插件点击事件
const plugin = ref({})
const dialogVisible = ref(false)
const fullscreen = ref(false)
const fullscreenText = ref('全屏')
const pluginClick = (item) => {
    console.log('item', item)
    plugin.value = item
    dialogVisible.value = true
}
const fullscreenClick = () => {
    fullscreen.value = !fullscreen.value
    fullscreenText.value = fullscreen.value ? '退出全屏' : '全屏'
}



// 登录kucoder
const kcLoginRef = useTemplateRef('kcLoginRef')
const kcLoginSuccess = (data) => {
    console.log('登录成功', data)
    console.log('userStore', userStore)
    plugin.value.kcToken = data.token
}

// 购买插件
const pluginOrderDialog = ref(false)
function buyPlugin(item, type) {
    // 是否登录kucoder
    if (!userStore.kc.user.token) {
        kcLoginRef.value.kcLoginMesBox()
        return false
    }
    plugin.value = item
    if (type == 'common') {
        plugin.value.amount = plugin.value.common_fee
        plugin.value.authorize_type = 1
    } else if (type == 'advance') {
        plugin.value.amount = plugin.value.advance_fee
        plugin.value.authorize_type = 2
    } else if (type == 'free') {
        plugin.value.amount = 0
        plugin.value.authorize_type = 0
    }
    plugin.value.pay_type = plugin_pay_types.value[0]  //默认支付方式
    pluginOrderDialog.value = true
}
// 卡密购买
const cardKeyVisible = computed({
    get() {
        return plugin.value.pay_type === 'cardkey'
    },
    set(newvVal) { }
})
/* const cardkey_card = ref('')
const cardkey_key = ref('') */
const pluginPayTypeChange = (val) => {
    console.log('pluginPayTypeChange', val)
    if (val === 'cardkey') {
        cardKeyVisible.value = true
    } else {
        cardKeyVisible.value = false
    }
}
// 确认购买
const payImg = ref('')
const payDialog = ref(false)
const order = ref({})
function submitBuyPlugin() {
    if (plugin.value.pay_type === 'cardkey') {
        if (!plugin.value.cardkey_card || !plugin.value.cardkey_key) {
            ElMessage.error('请输入卡号和密码')
            return false
        }
    }
    plugin.value.pay_gateway = 'scan'
    plugin.value.subject = '购买插件授权： ' + plugin.value.title
    plugin.value.market_id = plugin.value.id
    plugin.value.kcToken = userStore.kc.user.token
    order.value = { ...plugin.value }
    const keysToDelete = ['intro', 'plugin_imgs'];
    for (let key of keysToDelete) {
        if (key in order.value) {
            delete order.value[key];
        }
    }
    console.log('order', order.value)
    if (order.value.pay_type !== 'cardkey') {
        buy(order.value)
            .then(async ({ res }) => {
                console.log('购买插件res', res)
                payImg.value = await QRCode.toDataURL(res.data.pay_code_url)
                payDialog.value = true
                return res.data
            })
            .then(data => {
                data.kcToken = userStore.kc.user.token
                const timer = setInterval(async () => {
                    console.log('定时器执行', data)
                    const { res } = await payQuery(data, { showErrMsg: false })
                    console.log('支付查询结果', res)
                    if (Number(res.data?.pay_status) === 1) {
                        clearInterval(timer)
                        console.log('order.value', order.value)
                        payDialog.value = false
                        pluginOrderDialog.value = false
                        ElMessageBox.alert('支付成功,请到插件列表查看', '提示', {
                            confirmButtonText: '确定',
                        })
                    }
                }, 3000)
            })
            .catch(err => {
                console.warn('购买插件失败', err)
            })
    } else {
        buy(order.value)
            .then(({ data, msg, code }) => {

            })
            .catch(err => {
                console.warn('购买插件失败', err)
            })
    }
}


getList()
</script>

<style lang="scss" scoped>
.plugin {
    width: 300px;
    border: 1px solid #f7f5f5;
    border-radius: 5px;
    cursor: pointer;
    transition: transform 0.3s ease;

    &:hover {
        background-color: #f5f7fa;
        transform: translateY(-10px);
        /* 向上移动10像素 */
    }
}

.infos {
    div {
        display: flex;
        row-gap: 10px;
        column-gap: 10px;
    }
}
</style>