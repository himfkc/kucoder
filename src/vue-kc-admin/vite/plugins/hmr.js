export function hmr() {
    const VITE_DEPLOY_DIR = import.meta.env.VITE_DEPLOY_DIR
    let hmrEnabled = true
    let originalSend = null

    return {
        name: 'hmr',

        configureServer(server) {
            // 保存原始的发送方法
            if (server.ws && server.ws.send) {
                originalSend = server.ws.send.bind(server.ws)
            }

            // 重写发送方法
            if (server.ws) {
                server.ws.send = (payload) => {
                    if (!hmrEnabled) {
                        // console.log('🚫 阻止 HMR 消息:', payload.type)
                        return // 不发送任何消息
                    }
                    return originalSend?.(payload)
                }
            }

            // 设置控制端点
            server.middlewares.use(`'${VITE_DEPLOY_DIR}/__hmr_control'`, (req, res) => {
                res.setHeader('Content-Type', 'application/json')

                if (req.method === 'GET') {
                    res.end(JSON.stringify({ enabled: hmrEnabled }))
                } else if (req.method === 'POST') {
                    let body = ''
                    req.on('data', chunk => body += chunk)
                    req.on('end', () => {
                        try {
                            const { action } = JSON.parse(body)

                            if (action === 'disable') {
                                hmrEnabled = false
                                console.log('🚫 HMR 已禁用 - 阻止热更新消息')
                            } else if (action === 'enable') {
                                hmrEnabled = true
                                console.log('✅ HMR 已启用 - 允许热更新消息')
                            }

                            res.end(JSON.stringify({ success: true, enabled: hmrEnabled }))
                        } catch (e) {
                            res.statusCode = 400
                            res.end(JSON.stringify({ error: e.message }))
                        }
                    })
                }
            })
        }
    }
}