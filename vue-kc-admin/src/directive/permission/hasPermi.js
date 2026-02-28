/**
* v-hasPermi 操作权限处理
* Copyright (c) 2019 ruoyi
*/

import usePermissionStore from '@/store/modules/permission'

export default {
  mounted(el, binding, vnode) {
    const { value } = binding
    const all_permission = "*:*:*"
    const permissions = usePermissionStore().btnsSet

    if (value && value instanceof Array && value.length > 0) {
      const permissionFlag = value
      const btnAuth = []
      permissionFlag.forEach(item => {
        if (item && item.indexOf(":") !== -1) {
          item = item.replace(/:/g, "/")
          btnAuth.push(item)
        }
      })

      const hasPermissions = permissions.has(all_permission) || btnAuth.some(item => permissions.has(item))

      if (!hasPermissions) {
        el.parentNode && el.parentNode.removeChild(el)
      }
    } else {
      throw new Error(`请设置操作权限标签值`)
    }
  }
}
