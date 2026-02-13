import axios from 'axios'
import { ElMessageBox, ElMessage, ElLoading } from 'element-plus'
import errorCode from '@/utils/errorCode'
import { tansParams, blobValidate } from '@/utils/ruoyi'
import { saveAs } from 'file-saver'
import useUserStore from '@/store/modules/user'
import { adminBasePath } from '@/api/adminRouteBasePath'
import { LOGIN_E_CODE, KC_CODE_PREFIX,SUCCESS_RES_CODE,ERROR_RES_CODE } from './constant'

let downloadLoadingInstance
console.log('环境变量', import.meta.env)
axios.defaults.headers['Content-Type'] = 'application/json;charset=utf-8';
axios.defaults.headers['Accept'] = 'application/json, text/plain, */*';
axios.defaults.headers['withCredentials'] = true; // 允许跨域请求携带 Cookie
// 创建axios实例
const service = axios.create({
  // axios中请求配置有baseURL选项，表示请求URL公共部分
  // baseURL: import.meta.env.VITE_APP_BASE_API,
  baseURL: import.meta.env.DEV ? import.meta.env.VITE_DEV_PROXY : import.meta.env.VITE_APP_BASE_API,
  // 5s超时
  timeout: 5000
})

// request拦截器
service.interceptors.request.use(config => {
  // 是否需要防止数据重复提交
  // const isRepeatSubmit = (config.headers || {}).repeatSubmit === false
  // get请求映射params参数
  if (config.method === 'get' && config.params) {
    let url = config.url + '?' + tansParams(config.params)
    url = url.slice(0, -1)
    config.params = {}
    config.url = url
    console.log('请求拦截器 config url', config.url)
  }
  /*   if (!isRepeatSubmit && (config.method === 'post' || config.method === 'put')) {
      const requestObj = {
        url: config.url,
        data: typeof config.data === 'object' ? JSON.stringify(config.data) : config.data,
        time: new Date().getTime()
      }
      const requestSize = Object.keys(JSON.stringify(requestObj)).length // 请求数据大小
      const limitSize = 5 * 1024 * 1024 // 限制存放数据5M
      if (requestSize >= limitSize) {
        console.warn(`[${config.url}]: ` + '请求数据大小超出允许的5M限制，无法进行防重复提交验证。')
        return config
      }
      const sessionObj = cache.session.getJSON('sessionObj')
      if (sessionObj === undefined || sessionObj === null || sessionObj === '') {
        cache.session.setJSON('sessionObj', requestObj)
      } else {
        const s_url = sessionObj.url                // 请求地址
        const s_data = sessionObj.data              // 请求数据
        const s_time = sessionObj.time              // 请求时间
        const interval = 1000                       // 间隔时间(ms)，小于此时间视为重复提交
        if (s_data === requestObj.data && requestObj.time - s_time < interval && s_url === requestObj.url) {
          const message = '数据正在处理，请勿重复提交'
          console.warn(`[${s_url}]: ` + message)
          return Promise.reject(new Error(message))
        } else {
          cache.session.setJSON('sessionObj', requestObj)
        }
      }
    } */
  config.headers.showErrMsg = config.headers.showErrMsg ?? true
  console.log('请求拦截器 config', config)
  return config
}, error => {
  console.log('请求拦截器error:', error)
  Promise.reject(error)
})

// 响应拦截器
service.interceptors.response.use(res => {
  console.log('响应', res)
  if (res.status !== 200) {
    ElMessage.error('请求失败')
    return Promise.reject(new Error('请求失败'))
  }
  // 二进制数据则直接返回
  if (res.request.responseType === 'blob' || res.request.responseType === 'arraybuffer') {
    return res.data
  }
  const { data, msg, code } = res.data
  if (code === SUCCESS_RES_CODE) {
    return Promise.resolve({ data, msg, code, res: data })
  }
  // 未登录或登录过期或登录异常
  if (LOGIN_E_CODE.includes(code)) {
    ElMessageBox.alert(msg, '系统提示', { confirmButtonText: '重新登录', type: 'warning' })
      .then(() => {
        useUserStore().kc.user = {}
        useUserStore().kc.site_set = {}
        useUserStore()
          .clear()
          .then(() => {
            if (!import.meta.env.DEV) {
              location.href = import.meta.env.VITE_APP_BASE_API + import.meta.env.VITE_DEPLOY_DIR + adminBasePath + '/login'
            } else {
              location.href = import.meta.env.VITE_DEPLOY_DIR + adminBasePath + '/login'
            }
          })
      })
      .catch(err => {
        console.log(err)
      })
  } else if (code.toString().startsWith(KC_CODE_PREFIX)) {
    // kucoder异常
    const kcCode = code.toString().substring(3)
    if (LOGIN_E_CODE.includes(Number(kcCode))) {
      useUserStore().kc.user = {}
      useUserStore().kc.site_set = {}
    }
    msg && res.config.headers.showErrMsg && ElMessage.warning(msg)
    return Promise.reject({ msg, code })
  } else {
    if (res.config.headers.showErrMsg) {
      msg && ElMessage.warning(msg)
    }
    return Promise.reject({ msg, code })
  }
},
  error => {
    console.log('响应拦截器 error:', error)
    let { message } = error
    if (message == "Network Error") {
      message = "后端接口连接异常"
    } else if (message.includes("timeout")) {
      message = "系统接口请求超时"
    } else if (message.includes("Request failed with status code")) {
      message = "系统接口" + message.substr(message.length - 3) + "异常"
    }
    ElMessage({ message: message, type: 'error', duration: 5 * 1000 })
    return Promise.reject(error)
  }
);

// 通用下载方法
export function download(url, params, filename, config) {
  downloadLoadingInstance = ElLoading.service({ text: "正在下载数据，请稍候", background: "rgba(0, 0, 0, 0.7)", })
  return service.post(url, params, {
    transformRequest: [(params) => { return tansParams(params) }],
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    responseType: 'blob',
    ...config
  }).then(async (data) => {
    const isBlob = blobValidate(data)
    if (isBlob) {
      const blob = new Blob([data])
      saveAs(blob, filename)
    } else {
      const resText = await data.text()
      const rspObj = JSON.parse(resText)
      const errMsg = errorCode[rspObj.code] || rspObj.msg || errorCode['default']
      ElMessage.error(errMsg)
    }
    downloadLoadingInstance.close()
  }).catch((r) => {
    console.error(r)
    ElMessage.error('下载文件出现错误，请联系管理员！')
    downloadLoadingInstance.close()
  })
}

export default service
