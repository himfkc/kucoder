import { login, logout } from '@/api/kucoder/login'
import { logoutKc } from '@/api/kucoder/plugin/kcLogin';
import { imgUrl } from '@/utils/kucoder'
import { defineStore } from 'pinia'
import usePermissionStore from './permission'
import useSettingsStore from './settings'
import useTagsViewStore from './tagsView'


const useUserStore = defineStore(
  'user',
  {
    state: () => ({
      token: '',
      name: '',
      avatar: '',
      site_set: {},
      kc: {
        user: {}
      },
    }),
    actions: {
      // 登录
      login(data) {
        const settingsStore = useSettingsStore()
        return new Promise((resolve, reject) => {
          login(data)
            .then(({ res, code, msg }) => {
              console.log('登录结果', res)
              this.token = res.token
              this.name = res.nickname
              this.avatar = imgUrl(res.avatar, '', 'avatar')
              this.site_set = res.site_set
              settingsStore.setTitle(res.site_set.site_name || 'kucoder - php高性能框架')
              if (res?.kc_user) {
                this.kc.user = res.kc_user
              }
              resolve()
            })
            .catch(error => {
              reject(error)
            })
        })
      },
      // 退出系统
      async logout() {
        return new Promise((resolve, reject) => {
          logout(this.token)
            .then(async () => {
              // 插件市场token
              /* if (this.kc.user.token) {
                logoutKc({ kcToken: this.kc.user.token })
                  .then(res => {
                    console.log('已退出插件市场')
                    this.kc.user = {}
                  })
              } */
              /* console.log('已退出系统');
              console.log('准备退出插件市场')
              await logoutKc()
              console.log('已退出插件市场')
              this.kc.user = {} */

              await this.clear()
              resolve()
            })
            .catch(error => {
              reject(error)
            })
        })
      },
      // 清空用户信息
      async clear() {
        // 清空permission
        const permissionStore = usePermissionStore()
        permissionStore.routes = []
        permissionStore.source_routes = []
        permissionStore.topbarRouters = []
        permissionStore.sidebarRouters = []
        permissionStore.btns = []
        permissionStore.roleMenus = []
        // 清空tagView
        const tagsViewStore = useTagsViewStore()
        tagsViewStore.visitedViews = []
        tagsViewStore.cachedViews = []
        tagsViewStore.iframeViews = []

        // 清空user
        this.token = ''
        this.name = ''
        this.avatar = ''
        this.site_set = {}
      },
    },
    
    persist: true
  }
)

export default useUserStore
