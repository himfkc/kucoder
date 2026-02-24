// 插件path
export const pluginPath = '/app/kucoder'
// 插件path带域名
export const pluginPathUrl = import.meta.env.VITE_APP_BASE_API + pluginPath
// admin模块path 无admin应用则为空 即 pluginPath + ''
export const basePath = pluginPath + '/admin'


