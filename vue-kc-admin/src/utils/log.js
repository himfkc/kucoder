/* 
import { log } from '@/utils/log'
log.info('调试信息')  // 开发环境输出，生产环境不输出
log.error('错误信息') // 开发和生产都输出
*/
const isDev = import.meta.env.DEV
export const log = {
  info: (...args) => isDev && console.log(...args),
  warn: (...args) => isDev && console.warn(...args),
  debug: (...args) => isDev && console.debug(...args),
  error: (...args) => console.error(...args), // 错误信息保留
}