<template></template>
<script setup>
import useUserStore from '@/store/modules/user';
import { logoutKc } from '@/api/kucoder/plugin/kcLogin';

const userStore = useUserStore()
const router = useRouter()

const logout = () => {
    userStore
        .logout()
        .then(() => {
            // 清除token
            if (userStore.kc.user.token) {
                console.log('已登录插件市场')
                logoutKc({ kcToken: userStore.kc.user.token })
                    .then(res => {
                        console.log('已退出插件市场')
                        userStore.kc.user = {}
                        userStore.kc.site_set = {}
                    })
                    .then(() => {
                        router.push({ path: '/login' })
                    })
            } else {
                router.push({ path: '/login' })
            }
        })
}

logout()
</script>