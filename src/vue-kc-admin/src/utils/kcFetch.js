let headerOptions = {
    "Content-Type": "application/json",
    'Accept': 'application/json',
}

export default {
    async request(method, url, options) {
        if (options?.token) {
            headerOptions['Authorization'] = 'Bearer ' + options.token
        }
        if (options?.header) {
            headerOptions = { ...headerOptions, ...options.header }
        }
        let params = {
            method,
            headers: headerOptions,
        }
        if (method === 'POST') {
            params.body = JSON.stringify(options?.data)
        }
        // 构造请求Request对象
        const request = new Request(url, params);
        // 发起请求
        const response = await fetch(request);

        // console.log(`fetch ${method} 的响应`, response);
        // 检查响应是否成功 (状态码 200-299)
        if (!response.ok) {
            throw new Error(`HTTP error: ${response.status}`);
        }

        // 解析响应体为 JSON（如果是文本，用 response.text()） response.json()只能被使用一次 否则报错
        const data = await response.json();
        return data
    },
    async get(url, options) {
        return await this.request('GET', url, options)
    },
    async post(url, options) {
        return await this.request('POST', url, options)
    },
}