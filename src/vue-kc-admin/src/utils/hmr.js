export class HMR {
    static async disable() {
        try {
            const response = await fetch('/__hmr_control', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ action: 'disable' })
            })
            const result = await response.json()
            if (result.success) {
                localStorage.setItem('vite_hmr_enabled', 'false')
                console.log('✅ HMR 已禁用')
                return true
            }
        } catch (error) {
            console.error('禁用 HMR 失败:', error)
        }
        return false
    }

    static async enable() {
        try {
            const response = await fetch('/__hmr_control', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ action: 'enable' })
            })
            const result = await response.json()
            if (result.success) {
                localStorage.setItem('vite_hmr_enabled', 'true')
                console.log('✅ HMR 已启用')
                return true
            }
        } catch (error) {
            console.error('启用 HMR 失败:', error)
        }
        return false
    }

    static async getStatus() {
        try {
            const response = await fetch('/__hmr_control')
            const result = await response.json()
            return result.enabled
        } catch (error) {
            return null
        }
    }
}