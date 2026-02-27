<template>
  <el-dialog title="站点授权" v-model="dialogVisible" width="600px" draggable class="p-5" center align-center>
    <el-form ref="kcAuthorizeFormRef" :model="kcAuthorizeForm" :rules="kcAuthorizeFormRules" label-width="100px"
      class="y c">
      <el-form-item label="顶级域名" prop="domain">
        <el-input v-model="kcAuthorizeForm.domain" placeholder="请输入你的顶级域名 比如https://***.taobao.com则输入taobao.com" />
      </el-form-item>
      <el-form-item label="icp备案号" prop="icp">
        <el-input v-model="kcAuthorizeForm.icp" placeholder="请输入你的icp备案号" />
      </el-form-item>
      <el-form-item label="邮箱" prop="email">
        <el-input v-model="kcAuthorizeForm.email" placeholder="请输入邮箱 审核成功后会发送邮件到该邮箱" />
      </el-form-item>
      <el-form-item label="验证码" prop="code" class="x c ac">
        <el-row :gutter="20" class="x ac">
          <el-col :span="14">
            <el-input v-model="kcAuthorizeForm.code" placeholder="请输入验证码" />
          </el-col>
          <el-col :span="10">
            <img :src="codeImg" @click="getCode" class="cursor-pointer h-30px w-120px" title="点击刷新" />
          </el-col>
        </el-row>
      </el-form-item>
    </el-form>
    <template #footer>
      <el-button @click="cancelkcAuthorize">取消</el-button>
      <el-button type="primary" @click="submitkcAuthorize">提交</el-button>
    </template>
  </el-dialog>
</template>

<script setup>
import { getCodeImg, } from "@/api/kucoder/plugin/kcLogin"
import { isValidDomain, join_path, kcMsg, kcAlert } from "@/utils/kucoder"
import kcFetch from '@/utils/kcFetch'
import useUserStore from '@/store/modules/user'

const userStore = useUserStore();
const dialogVisible = defineModel({ type: Boolean, default: false });
// const kcAuthorizeFormRef = ref(null);
const kcAuthorizeFormRef = useTemplateRef('kcAuthorizeFormRef');
const kcAuthorizeForm = ref({
  domain: '',
  icp: '',
  email: '',
  code: '',
  uuid: ''
});
const kcAuthorizeFormRules = ref({
  domain: [
    { required: true, message: '请输入顶级域名', trigger: 'blur' },
    {
      validator: (rule, value, callback) => {
        const transformedValue = value.replace(/^https?:\/\//, '');
        if (!isValidDomain(transformedValue)) {
          callback(new Error('请输入正确的顶级域名'));
        } else {
          callback();
        }
      },
      trigger: 'blur'
    }
  ],
  icp: [
    { required: true, message: '请输入icp备案号', trigger: 'blur' },
  ],
  email: [
    { required: true, message: '请输入邮箱', trigger: 'blur' },
    { type: 'email', message: '请输入正确的邮箱地址', trigger: ['blur'] },
  ],
  code: [
    { required: true, message: '请输入验证码', trigger: 'blur' },
    { pattern: /^[a-zA-Z0-9]+$/, message: '请输入正确的验证码', trigger: 'blur' },
  ],
});
const codeImg = ref('');
const getCode = () => {
  getCodeImg().then(({ res, code, msg }) => {
    console.log('response', res)
    codeImg.value = "data:image/gif;base64," + res.captcha.img_base64;
    kcAuthorizeForm.value.uuid = res.captcha.uuid;
  })
};
const submitkcAuthorize = () => {
  console.log('确认提交授权', kcAuthorizeForm.value)
  kcAuthorizeFormRef.value.validate(valid => {
    console.log('参数验证结果', valid)
    if (valid) {
      console.log('参数验证通过', valid)
      const url = join_path(userStore.site_set.sys_url + '/kapi/site/authorize')
      kcFetch.post(url, { data: kcAuthorizeForm.value })
        .then(({ data, msg, code }) => {
          console.log('授权结果', data, msg, code)
          if (code !== 1) {
            kcAlert(msg)
            return;
          } else {
            kcAlert(msg, '提交成功', { type: 'success' })
          }
          dialogVisible.value = false;
        })
        .catch(err => {
          console.log('kcFetch err', err)
          kcMsg(err.msg)
        })
    }
  });
};
const cancelkcAuthorize = () => {
  // kcAuthorizeDialogVisible.value = false;
  dialogVisible.value = false;
}
onMounted(() => {
  getCode()
})
</script>

<style lang="scss" scoped></style>