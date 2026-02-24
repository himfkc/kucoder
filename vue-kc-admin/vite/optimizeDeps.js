import fs from "fs"

// 优化 element-plus 的依赖
const optimizeDepsElementPlusIncludes = ["element-plus/es"]

// 使用同步方式读取目录并处理
const elementPlusComponents = fs.readdirSync("node_modules/element-plus/es/components")
elementPlusComponents.forEach((dirname) => {
    const stylePath = `node_modules/element-plus/es/components/${dirname}/style/css.mjs`
    if (fs.existsSync(stylePath)) {
        optimizeDepsElementPlusIncludes.push(
            `element-plus/es/components/${dirname}/style/css`
        )
    }
})

export default optimizeDepsElementPlusIncludes