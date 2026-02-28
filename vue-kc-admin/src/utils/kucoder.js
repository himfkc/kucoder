import { isHttp, isEmpty } from "@/utils/validate"
import assetsAvatar from '@/assets/images/avatar.png'
import { ElLoading, ElMessage, ElMessageBox } from 'element-plus'
import useUserStore from '@/store/modules/user'
// import { adminBasePath } from "@/api/adminRouteBasePath";

/**
 * 拼接多个路径片段，智能处理斜杠/反斜杠，兼容URL和本地路径
 * - 自动保留 `http://` 或 `https://` 的双斜杠
 * - 统一路径分隔符为 `/`（可选配置为 `\`）
 * @param {...string} paths - 多个路径片段
 * @returns {string} 拼接后的规范化路径
 */
export function join_path(...paths) {
    if (paths.length === 0) return '';

    // 检测第一个路径是否是HTTP(S)协议
    const isFirstPathUrl = /^https?:\/\//i.test(paths[0]);
    let protocolPrefix = '';

    // 如果第一个路径是URL，提取协议部分（如 "http://"）
    if (isFirstPathUrl) {
        const protocolMatch = paths[0].match(/^(https?:\/\/)/i);
        protocolPrefix = protocolMatch ? protocolMatch[1] : '';
        paths[0] = paths[0].slice(protocolPrefix.length); // 移除协议部分临时处理
    }

    // 处理路径拼接
    const normalizedPath = paths
        .map((path, index) => {
            // 跳过空路径
            if (!path) return '';

            let normalized = path;

            // 非首路径：移除开头的斜杠/反斜杠
            if (index > 0) {
                normalized = normalized.replace(/^[\/\\]+/, '');
            }

            // 非末路径：移除结尾的斜杠/反斜杠
            if (index < paths.length - 1) {
                normalized = normalized.replace(/[\/\\]+$/, '');
            }

            return normalized;
        })
        .filter(Boolean) // 过滤空字符串
        .join('/')
        .replace(/[\/\\]+/g, '/'); // 统一替换为 `/`

    // 如果是URL，还原协议部分
    return isFirstPathUrl
        ? `${protocolPrefix}${normalizedPath}`
        : normalizedPath;
}

export function kcLoading(msg) {
    return ElLoading.service({
        lock: true,
        text: msg,
        // spinner: 'el-icon-loading', // ❌ 不需要，也不建议这样写 可能导致图标无法显示
        background: 'rgba(0, 0, 0, 0.7)'
    })
}

export function kcMsg(msg, type = 'warning', duration = 3000) {
    return ElMessage({
        message: msg,
        type: type,
        duration: duration
    })
}

// alert默认不显示取消按钮
export function kcAlert(msg, title = '提示', options = {}) {
    return ElMessageBox.alert(msg, title || '提示', { type: 'warning', ...options, center: true, draggable: true })
}

// confirm默认显示取消按钮
export function kcConfirm(msg, title = '提示', options = {}) {
    return ElMessageBox.confirm(msg, title || '提示', { ...options, center: true, draggable: true })
}

// prompt默认显示取消按钮
export function kcPrompt(msg, title = '提示', options = {}) {
    return ElMessageBox.prompt(msg, title || '提示', { ...options, center: true, draggable: true })
}

export async function getLoginPath() {
    const adminRouteBasePathObj = await import('@/api/adminRouteBasePath');
    const adminBasePath = adminRouteBasePathObj.adminBasePath;
    let currentUrl = window.location.href
    const loginUrl  = currentUrl.replace('/admin',adminBasePath).replace('/install','/login')
    return loginUrl
}

export function imgUrl(img, domain = '', type = 'img') {
    const userStore = useUserStore()
    const file_domain = userStore.site_set.oss_domain || import.meta.env.VITE_APP_BASE_API
    if (type === 'avatar') {
        if (!isHttp(img)) {
            // 如果没有图片，使用默认头像
            if (isEmpty(img)) {
                return assetsAvatar
            }
            // 否则拼接路径
            return domain ? join_path(domain, img) : join_path(file_domain, img)
        }
    } else {
        if (!isHttp(img)) {
            img = (isEmpty(img)) ? '' : domain ? join_path(domain, img) : join_path(file_domain, img)
        }
    }

    return img
}

export function numberToChinese(num) {
    // 先确保输入是一个有效的数字且在 0~10 范围内
    const number = Number(num);

    if (isNaN(number)) {
        return '输入无效，请输入 0~10 的数字';
    }

    if (number < 0 || number > 10) {
        return '数字超出范围，请输入 0~10 之间的数字';
    }

    // 0~10 数字对应的中文
    const chineseNumbers = [
        '零', '一', '二', '三', '四',
        '五', '六', '七', '八', '九', '十'
    ];

    return chineseNumbers[number];
}

// 判断是否是合法域名
export function isValidDomain(domain) {
    // 基本长度检查（域名最长253字符，每段最长63字符）
    if (!domain || domain.length > 253) {
        return false
    }

    // 正则验证
    const regex = /^(?:(?=[a-z0-9-]{1,63}\.)[a-z0-9]+(?:-[a-z0-9]+)*\.)+[a-z]{2,}$/i

    if (!regex.test(domain)) {
        return false
    }

    // 检查每段长度（每段不超过63字符）
    const parts = domain.split('.')
    for (const part of parts) {
        if (part.length > 63) {
            return false
        }
        // 每段不能以连字符开头或结尾
        if (part.startsWith('-') || part.endsWith('-')) {
            return false
        }
    }

    // 顶级域名至少2个字符
    const tld = parts[parts.length - 1]
    if (tld.length < 2) {
        return false
    }

    return true
}

/**
 * 实现 PHP array_column 功能的 JavaScript 函数
 * @param {Array} array - 输入数组
 * @param {String} columnKey - 要提取的列名
 * @param {String} [indexKey] - 作为返回数组键的列名（可选）
 * @returns {Array|Object} 返回提取的列或重组后的数组
 */
export function array_column(array, columnKey, indexKey = null) {
    // 检查输入是否为数组
    if (!Array.isArray(array)) {
        throw new TypeError('第一个参数必须是数组');
    }

    // 如果数组为空，直接返回空数组
    if (array.length === 0) {
        return [];
    }

    const result = [];

    for (const item of array) {
        // 如果指定了索引键
        if (indexKey !== null && indexKey !== undefined) {
            // 检查索引键是否存在
            if (!item.hasOwnProperty(indexKey)) {
                continue; // 或者可以抛出错误
            }

            // 检查列键是否存在
            if (columnKey !== null && columnKey !== undefined) {
                if (!item.hasOwnProperty(columnKey)) {
                    result[item[indexKey]] = undefined;
                } else {
                    result[item[indexKey]] = item[columnKey];
                }
            } else {
                // 如果列键为null，返回整个项
                result[item[indexKey]] = item;
            }
        } else {
            // 没有指定索引键
            if (columnKey !== null && columnKey !== undefined) {
                if (item.hasOwnProperty(columnKey)) {
                    result.push(item[columnKey]);
                } else {
                    result.push(undefined);
                }
            } else {
                // 如果列键也为null，返回整个项
                result.push(item);
            }
        }
    }

    return indexKey !== null ? (Array.isArray(result) ? result : Object.assign({}, result)) : result;
}

export function obj2tag(obj) {
    let newObj = {}
    /* for (const [k, v] of Object.entries(obj)) {
        newObj[k] = {
            value: k,
            label: v,
            tag: handleTagType(k)
        }
    } */
    Object.keys(obj).forEach((v, k, arr) => {
        newObj[v] = {
            value: v,
            label: obj[v],
            tag: handleTagType(k)
        }
    })
    return newObj
}

/**
 * 是否为数字字符串
 * @param str 
 * @returns 
 */
export function isNumberString(str) {
    return !isNaN(parseFloat(str)) && isFinite(str);
}

export function handleTagType(index) {
    // 处理标签类型
    const tagTypes = ['info', 'primary', 'success', 'warning', 'danger']
    if (index >= 0 && index < tagTypes.length) {
        return tagTypes[index]
    } else {
        return 'primary'
    }
}

export function handleEnumField(enumObj, tag = ['info', 'primary', 'success', 'warning', 'danger']) {
    const obj = {}
    if (!isEmpty(enumObj)) {
        console.log('enumOjb', enumObj)
        for (const key in enumObj) {
            const item = enumObj[key]
            obj[key] = []
            Object.keys(item).forEach((v, k, arr) => {
                obj[key].push({
                    value: isNumberString(v) ? Number(v) : v, // 如果是数字字符串则转换为数字
                    label: item[v],
                    // type: tag[v] ? tag[v] : handleTagType(k)
                    type: tag[v] ? tag[v] : 'primary'
                })
            })
        }
    }
    console.log('obj', obj)
    return obj;
}

/**
 * js判断是否为对象
 */
export function isObject(obj) {
    return obj !== null && typeof obj === 'object' && !Array.isArray(obj);
}

/**
 * 判断字符串是否为json
 * @param {*} str 
 * @returns 
 */
export function isJson(str) {
    try {
        JSON.parse(str);
        return true;
    } catch (e) {
        return false;
    }
}

/**
 * 克隆对象
 * @param {*} obj 
 * @returns 
 */
export function clone(obj) {
    return Object.create(Object.getPrototypeOf(obj), Object.getOwnPropertyDescriptors(obj));
}

/**
 * 深度克隆对象或数组
 */
export function cloneDeep(obj) {
    const d = Array.isArray(obj) ? obj : {};
    if (isObject(obj)) {
        for (const key in obj) {
            if (obj[key]) {
                if (obj[key] && typeof obj[key] === 'object') {
                    d[key] = cloneDeep(obj[key]);
                } else {
                    d[key] = obj[key];
                }
            }
        }
    }
    return d;
}

/**
 * html转义
 * @param {} htmlString 
 * @returns 
 */
export function htmlToEntities(htmlString) {
    return htmlString.replace(/[&<>"']/g, function (match) {
        switch (match) {
            case '&': return '&amp;';
            case '<': return '&lt;';
            case '>': return '&gt;';
            case '"': return '&quot;';
            case "'": return '&#39;';
            default: return match;
        }
    });
}