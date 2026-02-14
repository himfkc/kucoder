<h1 align="center">Kucoder - 高性能PHP框架</h1>

<p align="center">
  <!-- <img src="/logo.png" alt="Kucoder" width="200"/> -->
</p>

<!-- 头部star图标 -->
<p align="center">
  <a href="https://www.php.net/">
    <img src="https://img.shields.io/badge/PHP-%3E=8.1-blue" alt="PHP version"/>
  </a>
    <a href="https://redis.io/">
    <img src="https://img.shields.io/badge/Redis-blue" alt="Redis"/>
  </a>
  <a href="https://www.workerman.net/">
    <img src="https://img.shields.io/badge/Workerman-5.1-green" alt="Workerman"/>
  </a>
  <a href="https://www.workerman.net/doc/webman/">
    <img src="https://img.shields.io/badge/Webman-2.1-green" alt="Webman version"/>
  </a>
  <a href="https://vuejs.org/">
    <img src="https://img.shields.io/badge/Vue-3.5+-42b883" alt="Vue version"/>
  </a>
  <a href="https://element-plus.org/">
    <img src="https://img.shields.io/badge/Element%20Plus-2.10+-409eff" alt="Element Plus"/>
  </a>
  <a href="https://vitejs.dev/">
    <img src="https://img.shields.io/badge/Vite-6.0+-646cff" alt="Vite"/>
  </a>
    <a href="https://kucoder.com">
    <img src="https://img.shields.io/badge/Website-Kucoder.com-ff6b6b" alt="Website"/>
  </a>
  <a href="https://www.apache.org/licenses/LICENSE-2.0">
    <img src="https://img.shields.io/badge/License-Apache--2.0-green.svg" alt="License"/>
  </a>

</p>

## 📚 简介

**Kucoder** 是一款基于Webman高性能PHP框架开发的快速开发系统，基于Workerman + Webman + PHP8 + Vue3 + ElementPlus + JavaScript(非ts)，遵循Apache-2.0开源协议，采用插件化架构设计，内置完整的管理后台，开箱即用，为开发者提供高性能、安全、现代化的 Web 开发体验。
- 官网：[https://kucoder.com](https://kucoder.com)
- 文档：[https://doc.kucoder.com](https://doc.kucoder.com)
---

## 🚀 核心特性

### ⚡ 高性能

- 🚀 基于 **Workerman/Webman** 框架，性能高于传统 PHP-FPM 框架 **10-100 倍**
- 🚦  **常驻内存**：常驻内存架构，无容器到 PHP 的通讯开销，单机吞吐量性能高度释放
- ⚡ **多进程 + Epoll + 非阻塞 IO** 设计，每个进程能维持上万并发连接
- 🔄 支持 **HTTP / HTTPS / WebSocket / TCP / UDP** 多种通讯协议
- 💾 内置 **Redis** 高速缓存，支持连接池、分布式部署
- 🎯 **协程支持**：协程是一种比线程更轻量级的用户级并发机制，能够在进程中实现多任务调度，可以实现PHP阻塞函数自动协程化
- 📦 **数据库连接池** 技术，消除连接建立开销，大幅提升数据库访问效率
- 🌐 **分布式扩展能力**：支持多服务器横向扩展，系统承载能力成倍增加
- 🎪 **插件集成**：采用插件化设计，kucoder 不会影响 webman 框架本身，webman 可自由升级
- ⏱️ **定时任务**：内置 Crontab 支持，秒级精准定时
- 🚀 **异步支持**：异步 HTTP、异步 Redis、异步消息队列，轻松应对高并发场景
- 🔄 **事件驱动**：事件驱动架构，模块解耦更灵活
- 🔌 **自定义进程**：支持自定义进程，可以做 Workerman 能做的任何事情
- ⚡ **强类型**：PHP8 强类型严格模式

---

### 🔒 安全性

- 🔐 **密码安全**：抛弃 md5/sha1 等不安全加密方式，采用强单向哈希算法，不可逆转换，即使数据库泄露也无法还原原始密码
- 🔒 **多重身份认证**：Cookie + Redis Session + JWT + 登录指纹，有效防止 XSS 及 CSRF 攻击
- 🛡️ **RBAC 权限控制**：细粒度到每一个按钮方法的权限管理，无需修改代码，无需写权限注解，Vue 前端动态加载，傻瓜式操作
- 🔑 **JWT 无状态认证**：安全可靠的身份验证机制
- 🔐 **OpenSSL 高强度加密**：支持 **AES-256-GCM**、**ChaCha20-Poly1305**、**AES-256-CCM** 等业界最强加密算法
- 🔑 **Libsodium 现代加密**：标配"高级别"的开箱即用加密函数，支持对称加密、非对称加密、数字签名
- 🔒 **CORS 跨域防护**：防火墙级别的跨域访问控制
- 🚫 **SQL 注入防护**：基于 illuminate/database、ThinkORM的参数化查询，有效防止 SQL 注入攻击
- 📝 **完整日志审计**：操作日志 + 登录日志，全程可追溯

---

### 🛠️ 后端技术特点
- 🚀 **workerman**：Workerman是一款纯PHP开发的开源高性能的PHP 应用容器。你可以用它开发tcp代理、梯子代理、做游戏服务器、邮件服务器、ftp服务器、甚至开发一个php版本的redis、php版本的数据库、php版本的nginx、php版本的php-fpm等等。
- ⚡ **webman**：Webman是一款基于Workerman构建的高性能服务框架，集成了HTTP、WebSocket、TCP、UDP等多种模块。通过常驻内存、协程、连接池等先进技术，Webman不仅突破了传统PHP的性能瓶颈，还极大地扩展了其应用场景。
- 🐘 **php8**：PHP 8 比 PHP 5 快 3-4倍，内存节省 50%-60%，开启JIT在计算密集型场景带来3-5倍提升，性能更好安全性更高
- 🗄️ **mysql**：MySQL是一个开源的、关系型的数据库管理系统（RDBMS），它以高性能、高可靠性、易用性和开源免费的特点著称
- 💾 **redis**： 一个开源的、基于内存的、高性能的键值对（Key-Value）存储数据库，通常用作缓存、消息队列、会话存储、排行榜、实时统计等场景。它支持多种数据结构（如字符串、哈希、列表、集合、有序集合等），读写速度极快（微秒级响应），支持持久化、高可用、分布式等特性
- 📦 **composer**：kucoder基于composer生态，你可以在kucoder里使用最熟悉的功能组件，很方便的就可以使用symfony、laravel、thinkphp等集成框架的优秀组件
- 🔧 **依赖注入**：kucoder采用PHP-DI依赖注入组件，支持完整的依赖注入container，修改重构不用到处修改实例化类
- 📨 **消息队列**：消息队列常用于任务异步处理、系统解耦、流量削峰、日志收集、事件驱动架构等场景，是构建高并发、高可用、分布式系统的重要技术组件之一
- ⏱️ **接口限流**：接口支持注解限流，支持apcu、redis、memory驱动
- ⚡ **快速CRUD**：集成一个功能丰富的CRUD Trait，控制器不用再手写增删改查代码，大大加速项目开发
- 🪝 **控制器钩子**：增删改查方法每一个都配备钩子函数，执行前执行后你都可以完善拓展你的业务逻辑
- 🛡️ **中间件**：系统支持全局中间件、应用中间件、控制器中间件、方法中间件，系统已集成一个功能完善的跨域访问控制中间件，在kucoder中你可以拓展任何功能的自定义中间件
- 🎯 **事件Event**：事件相比较中间件的优势是事件比中间件更加精准定位，更适合一些业务场景的扩展。kucoder基于webman/event 提供一种精巧的事件机制，可实现在不侵入代码的情况下执行一些业务逻辑，实现业务模块之间的解耦
- 🛣️ **路由route**：支持复杂的多应用多目录业务的路由实现
- 🏢 **多应用**：高度支持多应用多目录，为开发大型系统大型项目奠定框架基础
- 🧩 **应用插件**：开发者可使用kucoder开发各种功能的应用插件，kucoder已提供好插件底层支持
- 🔄 **协程支持**：协程支持Swoole Swow和Fiber三种驱动，支持为不同的进程开启不同的驱动
- 🔌 **自定义进程**：支持自定义HTTP / HTTPS / WebSocket / TCP / UDP多种进程
- 🎁 更多功能等你探索

---

### 🛠️ 前端技术特点(vue-kc-admin)
- ⚡ **vite**：新一代前端构建工具和开发服务器，最初是为了更好地支持 Vue 3 而设计，但现已成为一套通用的、极速的、现代化的前端构建解决方案，支持多种框架（如 Vue、React、Svelte 等）和库。Vite 在开发阶段实现"秒级启动"、"极速热更新"
- 💚 **vue3**：采用vue3组合式写法，作为当前生产环境使用最广泛的JavaScript框架之一，Vue支持自底向上增量开发，可应用于静态HTML增强、单页应用（SPA）、服务端渲染（SSR）、静态站点生成（SSG）等场景，其生态覆盖WebComponents、移动端、桌面端等多终端开发需求
- 📜 **Javascript**：JavaScript（简称 JS）是一种轻量级、解释型、面向对象的脚本编程语言，不仅可以在浏览器中运行，还能通过 Node.js 在服务器端运行，用于开发 Web 应用、移动应用、桌面应用、游戏、物联网、自动化脚本等，是目前世界上最流行、应用最广泛的编程语言之一。
- 🛣️ **Vue-Router**：Vue Router 是 Vue.js 的官方路由。它与 Vue.js 核心深度集成，让用 Vue.js 构建单页应用变得轻而易举。功能包括：嵌套路由映射、动态路由选择模块化、基于组件的路由配置路由参数查询、通配符展示由 Vue.js 的过渡系统提供的过渡效果、细致的导航控制、自动激活 CSS 类的链接、HTML5 history 模式或 hash 模式、可定制的滚动行为、URL 的正确编码
- 🎨 **ElementPlus**：kucoder按需引入ElementPlus，ElementPlus是一套为 Vue 3 设计的桌面端组件库，基于 Vue 3 和 TypeScript 开发，提供了丰富、美观、易用的 UI 组件（如按钮、表格、表单、弹窗、导航、布局等），遵循 Vue 3 的 Composition API 规范，支持现代化开发模式，内置 TypeScript 类型支持，开箱即用，是开发 Vue 3 管理后台、企业级前端应用、数据展示界面等场景的首选 UI 组件库之一
- 🍍 **Pinia**：Pinia 是 Vue 官方推荐的状态管理库，用于在 Vue.js 应用中集中管理全局状态（如用户信息、全局配置、共享数据等）。它是 Vuex 的现代替代方案，设计更简单、更灵活、更符合Vue3的Composition API 风格
- 💾 **pinia-plugin-persistedstate**：提供对Pinia的持久化。无论你是习惯使用默认值来保存一个完整的 store，还是需要具有多个 storage 和一个自定义序列化器的细粒度配置，该插件都能帮你搞定
- 📦 **pnpm**：pnpm是一个快速、高效、节省磁盘空间的 Node.js 包管理工具，用于安装和管理 JavaScript / TypeScript 项目中的第三方依赖包（如 npm 包）。pnpm 采用了一种独特的"硬链接 + 符号链接"存储机制，使得相同的依赖包在硬盘上只存储一份，显著节省磁盘空间并提升安装速度，同时保持与 npm/yarn 生态的完全兼容
- 🛠️ **VueUse**： VueUse 是一个由 Vue 官方团队成员开发并维护的、功能强大且实用的 Vue 3 组合式函数（Composition API Utilities）工具库，它提供了大量开箱即用、复用性高、类型安全（TypeScript 友好）的 Composition API 工具函数，是 Vue 3 开发者的"瑞士军刀"和最佳实践工具集"
- 🌐 **Axios**： Axios 是一个简单易用的基于 Promise 的浏览器和 node.js HTTP 客户端。提供了一个简单易用的库，体积小巧，接口可扩展性强。它支持请求和响应拦截器、转换请求数据和响应数据、取消请求、自动转换 JSON 数据等功能
- 🎭 **Iconify**：Iconify 是一个开源的、统一的、现代化的图标管理平台与工具生态系统，旨在为开发者提供一个统一的接口来使用来自多个流行图标集（如 Font Awesome、Material Design Icons、Ant Design Icons、Ionicons 等）的图标，而无需为每个图标库单独安装和引入,广泛用于 Vue、React、Svelte、原生 HTML / JS 等前端项目中
- 🎨 **Unocss**： UnoCSS 继承了 Windi CSS 的按需特性，属性化模式、快捷方式、变体组、编译模式 等等。最重要的是，UnoCSS 是从头开始构建的，考虑到了最大的可扩展性和性能，使我们能够引入 纯 CSS 图标、无值的属性化、标签化、网络字体 等新功能
- 🌊 **TailwindCSS**：Tailwind CSS 是一个功能强大、高度可定制的实用优先（Utility-First）CSS 框架，它不提供预设的 UI 组件（如按钮、卡片等），而是提供大量低层次的 CSS 工具类（Utility Classes）开发者可以通过自由组合这些工具类，快速构建出任何想要的 UI 界面，广泛用于 Vue、React、Next.js、Nuxt、原生 HTML 等项目中。
- 📊 **vue-echarts**：Vue-ECharts 是 ECharts 的 Vue 组件封装,内置响应式，数据变化时自动更新图表，解决了原生echarts需要手动管理生命周期、需要手动处理响应式、需要手动处理 resize等痛点
- ⚙️ **自动加载**：unplugin-auto-import用于自动导入ref、reactive、computed等vue vue-route pinia函数，无需手动引入，提高开发效率
- 🧩 **组件自动加载**：unplugin-vue-components是一款功能十分强大的插件，旨在简化组件的自动导入和使用，可以帮助我们在Vue项目中自动导入并注册我们使用的任何Vue组件，从而提高开发效率
- 🔐 **权限控制**：kucoder的前端权限控制非常简单，只需在对应操作的按钮上添加v-auth即可
- 📦 **组件封装**：系统已封装好一些常用的component，比如markdown编辑器、图片上传、文件上传、icon图标组件等，开箱即用
- 🎁 更多功能等你探索

---


## 🖥️ 快速开始

### 📋 安装

请参考📚 kucoder文档：[https://doc.kucoder.com](https://doc.kucoder.com)

#### 🚫使用限制
🚫Kucoder仅限**中国大陆内已备案网站**安装使用(本地调试环境不限制)

---

## 📄 开源、版权须知

- 本项目基于 **Apache-2.0** 协议开源，你可以免费用于商业项目
- **不得复制Kucoder源代码发行同类衍生性分发版本**，否则将追究侵权者法律责任
- kucoder前端项目vue-kc-admin仅限用于kucoder系统内，用于其它非kucoder系统需征得kucoder同意
- 本项目包含的第三方源码和二进制文件之版权信息另行标注

---

## 📝 法律声明
- Kucoder 是免费开源软件，发布在 GitHub、Gitee 等开源托管平台上。 任何个人或组织都可以自由选择使用或不用 Kucoder 进行合法的互联网开发行为
- 用户可自由选择使用或不使用 Kucoder。基于用户的自主选择和决定， Kucoder 对任何原因在使用本软件时可能对用户自己或他人造成的任何形式的损失和伤害不承担责任
- kucoder是免费开源软件，不得将kucoder用于任何违法违规行为，kucoder保留追究法律责任的权利

---

## 🌐 相关链接

- 🌐 Kucoder官网：[https://kucoder.com](https://kucoder.com)
- 📚 Kucoder文档：[https://doc.kucoder.com](https://doc.kucoder.com)
- 📝 Kucoder问题：[https://kucoder.com/question](https://kucoder.com/question)
- 🔌 Kucoder插件：[https://kucoder.com/plugin](https://kucoder.com/plugin)

---

## ❤️ 致谢

感谢以下开源项目：

#### 后端
- [Workerman](https://www.workerman.net/) - 一款纯PHP开发的开源高性能的PHP应用容器
- [Webman](https://www.workerman.net/) - 基于workerman的高性能web框架
- [Linux]() - 开源、免费、类 Unix 的操作系统内核
- [Nginx](https://nginx.org/) - 高性能、轻量级、开源的 Web 服务器、反向代理服务器、负载均衡器以及 HTTP 缓存服务器
- [PHP](https://www.php.net/manual/zh/index.php) - 开源的、服务器端的、脚本型编程语言
- [Mysql](https://www.mysql.com/) - 开源的、关系型的数据库管理系统（RDBMS）
- [Redis](https://redis.io/) - 开源的、基于内存的、高性能的键值对（Key-Value）存储数据库
- [Composer](https://www.phpcomposer.com/) - PHP 的一个依赖管理工具（类似于 Node.js 的 npm、Python 的 pip、Ruby 的 Bundler）
- [Symfony](http://www.symfonychina.com/) - 开源的、高性能的、全栈的 PHP Web 应用框架
- [laravel](https://laravel.com/) - 功能强大且优雅的 PHP Web 框架
- [thinkphp](https://doc.thinkphp.cn/v8_0/) - 免费开源的，快速、简单的面向对象的轻量级PHP开发框架
- [ThinkORM](https://www.kancloud.cn/manual/think-orm/) - 一个基于PHP和PDO的数据库中间层和ORM类库
- [firebase/php-jwt](https://packagist.org/packages/firebase/php-jwt) - JWT类库
- [gregwar/captcha](https://packagist.org/packages/gregwar/captcha) - 验证码生成器
- [guzzlehttp](https://packagist.org/packages/guzzlehttp/guzzle) - HTTP客户端
- [workerman/http-client](https://packagist.org/packages/workerman/http-client) - 异步HTTP客户端
- [vlucas/phpdotenv](https://packagist.org/packages/vlucas/phpdotenv) - 加载环境变量
- [hyperf/pimple](https://packagist.org/packages/hyperf/pimple) - 基于 pimple/pimple 实现的轻量级符合 PSR11 规范 的容器组件
- [ramsey/uuid](https://packagist.org/packages/ramsey/uuid) - 生成UUID的php类库
- [php-di](https://packagist.org/packages/php-di/php-di) - 一个功能完整的依赖注入容器
- [jetBrains](https://www.jetbrains.com/) - 软件开发者和团队的必备工具
- []() - 更多未能全部列出


---

#### 前端
- [Javascript](https://developer.mozilla.org/zh-CN/docs/Web/JavaScript) - 轻量级、解释型、面向对象的脚本编程语言
- [Vue 3](https://vuejs.org/) - 渐进式 JavaScript 框架
- [Element Plus](https://element-plus.org/) - Vue 3 UI 组件库
- [Vite](https://cn.vite.dev/) - 新一代前端构建工具和开发服务器
- [Vue-Router](https://router.vuejs.org/zh/) - Vue.js 的官方路由
- [Pinia](https://pinia.vuejs.org/zh/) - Vue 官方推荐的状态管理库
- [npm](https://www.npmjs.com/) - 开源开发人员使用npm来共享和借用包，许多组织也使用 npm来管理私有开发
- [Pnpm](https://pnpm.io/zh/) - 快速、高效、节省磁盘空间的 Node.js 包管理工具
- [VueUse](https://vueuse.nodejs.cn/) -  Vue 官方团队成员开发并维护的、功能强大且实用的 Vue 3 组合式函数库
- [Axios](https://axios.org.cn/) - 简单易用的基于 Promise 的浏览器和 node.js HTTP 客户端
- [Iconify](https://iconify.design/) - 开源的、统一的、现代化的图标管理平台与工具生态系统
- [Unocss](https://unocss.nodejs.cn/) - 原子 CSS 引擎
- [TailwindCSS](https://tailwind.nodejs.cn/) - 功能强大、高度可定制的实用优先（Utility-First）CSS 框架
- [vue-echarts](https://www.npmjs.com/package/vue-echarts) - ECharts的Vue组件封装库
- [unplugin-auto-import](https://www.npmjs.com/package/unplugin-auto-import) - 自动导入
- [unplugin-vue-components](https://www.npmjs.com/package/unplugin-vue-components) - 组件按需导入
- []() - 更多未能全部列出
---

<p align="center">
  <strong>Made with ❤️ by Kucoder Team</strong>
</p>
