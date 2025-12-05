import {
    defineConfig,
    // presetMini,
    // presetWind3,
    presetIcons,  //使用方法<div class="i-mdi-alarm" /> 前缀名i-图标集mdi-图标名alarm   https://unocss.nodejs.cn/presets/icons
    presetAttributify,
    presetTypography,
    presetWebFonts,
    transformerDirectives,
    transformerVariantGroup
} from 'unocss'
import presetWind4 from '@unocss/preset-wind4'

export default defineConfig({
    shortcuts: [
        // ...
    ],
    theme: {
        colors: {
            // ...
        }
    },
    presets: [
        // presetMini(),  //UnoCSS 的基本预设，仅包含最基本的工具 此预设是 @unocss/preset-wind3 的子集，仅包含与 CSS 属性一致的最基本工具 该预设包含在 unocss 包中
        // presetWind3(), //适用于 UnoCSS 的 Tailwind CSS / Windi CSS 紧凑预设 该预设包含在 unocss 包中，你也可以从那里导入它：import { presetWind3 } from 'unocss'
        presetIcons(),  //对 UnoCSS 使用纯 CSS 的任何图标预设。该预设包含在 unocss 包中，你也可以从那里导入它 import { presetIcons } from 'unocss 
        presetWind4(),  //适用于 UnoCSS 的 Tailwind4 CSS 紧凑预设。它兼容 PresetWind3 的所有功能，并对其进行了进一步增强。
        presetAttributify(),  //使得 属性化模式 能够用于其他预设 该预设包含在 unocss 包中，你也可以从那里导入它：import { presetAttributify } from 'unocss
        presetTypography(), //提供一组散文类，可用于将排版默认值添加到普通 HTML  该预设包含在 unocss 包中
        presetWebFonts({    //只需提供字体名称即可使用 谷歌字体、FontShare 中的网络字体  该预设包含在 unocss 包中
            fonts: {
                // ...
            },
        }),
    ],
    transformers: [
        transformerDirectives(),
        transformerVariantGroup(),
    ],
})