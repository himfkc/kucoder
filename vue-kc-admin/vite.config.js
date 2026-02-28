import { defineConfig, loadEnv } from 'vite'
import path from 'path'
import createVitePlugins from './vite/plugins'
import optimizeDepsIncludes from './vite/optimizeDeps'

// https://cn.vitejs.dev/config/
export default defineConfig(({ mode, command }) => {
  console.log('mode: ', mode) // mode: development/production
  console.log('command: ', command) // command: serve/build
  // 使用 loadEnv 加载环境变量
	// 参数1: 当前模式 (development, production 等)
	// 参数2: 项目根目录 (process.cwd())
  const env = loadEnv(mode, process.cwd())
  const { VITE_APP_BASE_API, VITE_DEPLOY_DIR, VITE_HMR, VITE_DEV_PROXY,VITE_DEV_PORT } = env
  const isHMREnabled = VITE_HMR !== 'false'
  return {
    // 部署生产环境和开发环境下的URL。
    // 默认情况下，vite 会假设你的应用是被部署在一个域名的根路径上 例如 https://kucoder.com/ 则 base 设置为 '/'
    // 如果应用被部署在一个子路径上，你就需要用这个选项指定这个子路径。例如，如果你的应用被部署在 https://kucoder.com/vue/，则设置 base 为 /vue/
    // base: VITE_APP_ENV === 'production' ? '/' : '/',
    base: VITE_DEPLOY_DIR,
    plugins: createVitePlugins(env, command === 'build'),
    resolve: {
      // https://cn.vitejs.dev/config/#resolve-alias
      alias: {
        // 设置路径
        '~': path.resolve(__dirname, './'),
        // 设置别名
        '@': path.resolve(__dirname, './src')
      },
      // https://cn.vitejs.dev/config/#resolve-extensions
      extensions: ['.mjs', '.js', '.ts', '.jsx', '.tsx', '.json', '.vue']
    },
    // 打包配置
    build: {
      // https://vite.dev/config/build-options.html
      /* sourcemap: command === 'build' ? false : 'inline',
      outDir: 'dist',
      assetsDir: 'assets',
      chunkSizeWarningLimit: 2000, */
      // rollup.config.js参数可写在rollupOptions里 这与从Rollup配置文件导出的选项相同
      rollupOptions: {
        /* output: {
          chunkFileNames: 'static/js/[name]-[hash].js',
          entryFileNames: 'static/js/[name]-[hash].js',
          assetFileNames: 'static/[ext]/[name]-[hash].[ext]'
        } */
        // 排除在打包之外 index.html中手动引入CDN链接 否则会导致运行时错误
        external: ['qrcode']
      },
    },
    // 生产环境移除console和debugger esbuild与build同级配置
    esbuild: command === 'build' ? {
        drop: ['console', 'debugger'],
      } : {},
    define: {
      // 恢复 console.error 和 console.warn（不被移除）
      'console.error': 'console.error',
      'console.warn': 'console.warn'
    },
    // server的以下选项仅适用于开发环境
    server: {
      // 指定服务器应该监听哪个 IP 地址。 如果将此设置为 0.0.0.0 或者 true 将监听所有地址，包括局域网和公网地址。
      host: true,
      // 指定开发服务器端口。注意：如果端口已经被使用，Vite 会自动尝试下一个可用的端口，所以这可能不是开发服务器最终监听的实际端口。
      port: VITE_DEV_PORT,
      //开发服务器启动时，自动在浏览器中打开应用程序。如果为真，将会打开默认浏览器。
      open: false,
      // Vite允许响应的主机名。 默认情况下，允许 localhost 及其下的所有 .localhost 域名和所有 IP 地址。 使用 HTTPS 时，将跳过此检查。
      // 将 server.allowedHosts 设置为 true 允许任何网站通过 DNS 重绑定攻击向你的开发服务器发送请求，从而使它们能够下载你的源代码和内容。
      allowedHosts: [],
      // 为开发服务器配置自定义代理规则。期望接收一个 { key: options } 对象，https://cn.vitejs.dev/config/#server-proxy
      proxy: {
        [VITE_DEV_PROXY]: {
          target: VITE_APP_BASE_API,
          changeOrigin: true,
          rewrite: (p) => p.replace(VITE_DEV_PROXY, '')
        },
      },
      // 热更新
      hmr: isHMREnabled,
      // 如果禁用 HMR，也优化文件监听  ignored: ['**/*']忽略所有文件变化
      watch: isHMREnabled ? {} : { ignored: ['**/*'] },
    },
    css: {
      postcss: {
        plugins: [
          {
            postcssPlugin: 'internal:charset-removal',
            AtRule: {
              charset: (atRule) => {
                if (atRule.name === 'charset') {
                  atRule.remove()
                }
              }
            }
          }
        ]
      },
      // element-plus在2.8.5及以后的版本, Sass的最低支持版本为1.79.0 如果您的终端提示 legacy JS API Deprecation Warning, 您可以配置以下代码
      preprocessorOptions: {
        scss: {
          api: 'modern-compiler' // or "modern"
        },
      },
    },
    // 默认情况下，不在 node_modules 中的，链接的包不会被预构建。使用此选项可强制预构建链接的包
    // 预构建依赖项 解决element-plus按需引入时，样式文件没有被打包到dist目录下的问题
    optimizeDeps: {
      include: optimizeDepsIncludes,
    }
  }
})
