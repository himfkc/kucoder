import Cookies from 'js-cookie'
import useUserStore from '@/store/modules/user'

const TokenKey = 'Admin-Token'

/* export function getToken() {
  return Cookies.get(TokenKey)
}

export function setToken(token) {
  return Cookies.set(TokenKey, token)
}

export function removeToken() {
  return Cookies.remove(TokenKey)
} */

export function getToken() {
  const userStore = useUserStore()
  return userStore.token
}

export function setToken(token) {
  const userStore = useUserStore()
  userStore.token = token
}

export function removeToken() {
  setToken('')
}
