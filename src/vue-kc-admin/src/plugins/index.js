// 全局对象
import tab from './tab'
import auth from './auth'
import cache from './cache'
import modal from './modal'
import download from './download'

// 全局方法
import { useDict } from '@/utils/dict'
import { download as downloadApi } from '@/utils/request'
import { parseTime, resetForm, addDateRange, handleTree, selectDictLabel, selectDictLabels } from '@/utils/ruoyi'


// 一个插件可以是一个拥有 install() 方法的对象，也可以直接是一个安装函数本身。
// 安装函数会接收到安装它的应用实例和传递给 app.use() 的额外选项作为参数
export default function installPlugins(app) {
  // 全局对象挂载
  // 页签操作
  app.config.globalProperties.$tab = tab
  // 认证对象
  app.config.globalProperties.$auth = auth
  // 缓存对象
  app.config.globalProperties.$cache = cache
  // 模态框对象
  app.config.globalProperties.$modal = modal
  // 下载文件
  app.config.globalProperties.$download = download


  // 全局方法挂载
  app.config.globalProperties.useDict = useDict
  app.config.globalProperties.download = downloadApi
  app.config.globalProperties.parseTime = parseTime
  app.config.globalProperties.resetForm = resetForm
  app.config.globalProperties.handleTree = handleTree
  app.config.globalProperties.addDateRange = addDateRange
  app.config.globalProperties.selectDictLabel = selectDictLabel
  app.config.globalProperties.selectDictLabels = selectDictLabels

  // 全局组件  已经使用了unplugin-vue-components按需加载组件 不需要全局注册组件了 打包体积大

  /* // 分页组件
  import Pagination from '@/components/Pagination'
  // 自定义表格工具组件
  import RightToolbar from '@/components/RightToolbar'
  // 富文本组件
  import Editor from "@/components/Editor"
  // 文件上传组件
  import FileUpload from "@/components/FileUpload"
  // 图片上传组件
  import ImageUpload from "@/components/ImageUpload"
  // 图片预览组件
  import ImagePreview from "@/components/ImagePreview"
  // 自定义树选择组件
  import TreeSelect from '@/components/TreeSelect'
  // 字典标签组件
  import DictTag from '@/components/DictTag'
  // svg图标组件
  import SvgIcon from '@/components/SvgIcon' */

  // 全局组件挂载
  /* app.component('Pagination', Pagination)
  app.component('RightToolbar', RightToolbar)
  app.component('Editor', Editor)
  app.component('FileUpload', FileUpload)
  app.component('ImageUpload', ImageUpload)
  app.component('ImagePreview', ImagePreview)
  app.component('TreeSelect', TreeSelect)
  app.component('DictTag', DictTag)
  app.component('svg-icon', SvgIcon) */

}
