/**
 * v-auth-del 删除按钮权限处理
 * 自动根据当前组件的 name 生成权限标识，判断是否有删除权限
 */

import usePermissionStore from '@/store/modules/permission'
import { isEmpty } from '@/utils/validate'

/**
 * 将驼峰命名转换为斜杠分隔的小写路径
 */
function convertNameToPermission(name,arg) {
  if (!name || typeof name !== 'string') {
    return ''
  }
  let path = '';
  if(name.includes('_')){
    path = name.replace(/_/g, '/')
  }else{
    path = name.replace(/([A-Z])/g, '/$1').toLowerCase().replace(/^\/+/, '')
  }
  return `${path}/${arg}`
}

export default {
  mounted(el, binding, vnode) {
    const permissions = usePermissionStore().btns
    let directiveValue = binding.value ?? '';
    // console.log('指令值：',directiveValue)
    if(!isEmpty(directiveValue)){
        if(Array.isArray(directiveValue)){
            directiveValue = directiveValue[0]
        }
        if(directiveValue.includes(':')){
            directiveValue = directiveValue.replace(':','/')
        }
    }
    if(isEmpty(directiveValue)){
        // 使用 binding.instance 获取组件实例
        const instance = binding.instance
        // console.log('组件实例：',instance)
        
        // 获取组件name（优先从组件实例，其次从 vnode，最后从 binding.value）
        let componentName = ''
        
        if (instance) {
        // Vue3 中 defineOptions 设置的 name 可以通过 $ 访问
        componentName = instance.$?.type?.name || 
                        instance.$options?.name || 
                        ''
        // console.log('组件实例上定义的defineOptions中的name-1-instance.$options?.name:',componentName)
        }
        
        if (!componentName && vnode?.component?.type?.name) {
        componentName = vnode.component.type.name
        // console.log('组件实例上定义的defineOptions中的name-2-vnode.component.type.name:',componentName)
        }
        
        if (!componentName && binding.value) {
        componentName = binding.value
        // console.log('组件实例上定义的defineOptions中的name-3-binding.value:',componentName)
        }
        
        if (!componentName) {
            console.warn('v-auth: 无法获取组件 name')
            el.parentNode?.removeChild(el)
            return
        }
        
        directiveValue = convertNameToPermission(componentName,binding.arg)
        // console.log('转化后的权限按钮：',directiveValue)
    }

    const hasPermission = permissions.some(permission => permission === directiveValue)
    
    if (!hasPermission) {
      el.parentNode?.removeChild(el)
    }
  }
}
