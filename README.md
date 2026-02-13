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
    <img src="https://img.shields.io/badge/Redis-6.0+-dc382d" alt="Redis"/>
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

**Kucoder** 是一款基于 [Webman](https://www.workerman.net/doc/webman/) 高性能 PHP 框架开发的快速开发系统，基于Workerman + Webman + PHP8 + Vue3 + ElementPlus + JavaScript(非ts)，遵循apache-2.0开源协议，采用插件化架构设计，内置完整的管理后台，为开发者提供高性能、安全、现代化的 Web 开发体验。
- 官网：[https://kucoder.com](https://kucoder.com)
- 文档: [https://doc.kucoder.com](https://doc.kucoder.com)
---

## 🚀 核心特性

### ⚡ 高性能

- 🚀 基于 **Workerman/Webman** 框架，性能高于传统 PHP-FPM 框架 **10-100 倍**
- 🚦  **常驻内存**：常驻内存架构，无容器到 PHP 的通讯开销，单机吞吐量性能高度释放
- ⚡ **多进程 + Epoll + 非阻塞 IO** 设计，每个进程能维持上万并发连接
- 🔄 支持 **HTTP / WebSocket / TCP / UDP** 多种通讯协议
- 💾 内置 **Redis** 高速缓存，支持连接池、分布式部署
- 🎯 **协程支持**：协程屏障、并发控制、协程锁、协程通道
- 📦 **数据库连接池** 技术，消除连接建立开销，大幅提升数据库访问效率
- 🌐 **分布式扩展能力**：支持多服务器横向扩展，系统承载能力成倍增加
- 🎪 采用插件化设计，kucoder 不会影响 webman 框架本身，webman 可自由升级
- ⏱️ **定时任务**：内置 Crontab 支持，秒级精准定时
- 🚀 **异步支持**：异步 HTTP、异步 Redis、异步消息队列，轻松应对高并发场景
- 🔄 **事件驱动**：事件驱动架构，模块解耦更灵活
- 🔌 **自定义进程**：支持自定义进程，可以做 Workerman 能做的任何事情

### 🔒 高安全性

- 🔐 **密码安全**：抛弃 md5/sha1 等不安全加密方式，采用强单向哈希算法，不可逆转换，即使数据库泄露也无法还原原始密码
- 🔒 **多重身份认证**：Cookie + Redis Session + JWT + 登录指纹，有效防止 XSS 及 CSRF 攻击
- 🛡️ **RBAC 权限控制**：细粒度到每一个按钮方法的权限管理，无需修改代码，无需写权限注解，Vue 前端动态加载，傻瓜式操作
- 🔑 **JWT 无状态认证**：安全可靠的身份验证机制
- 🔐 **OpenSSL 高强度加密**：支持 **AES-256-GCM**、**ChaCha20-Poly1305**、**AES-256-CCM** 等业界最强加密算法
- 🔑 **Libsodium 现代加密**：提供"高级别"的开箱即用加密函数，支持对称加密、非对称加密、数字签名
- 🔒 **CORS 跨域防护**：防火墙级别的跨域访问控制
- 🚫 **SQL 注入防护**：基于 illuminate/database、ThinkORM的参数化查询，有效防止 SQL 注入攻击
- 📝 **完整日志审计**：操作日志 + 登录日志，全程可追溯

### 🎨 后端技术特点
- workerman: Workerman是一款纯PHP开发的开源高性能的PHP 应用容器。你可以用它开发tcp代理、梯子代理、做游戏服务器、邮件服务器、ftp服务器、甚至开发一个php版本的redis、php版本的数据库、php版本的nginx、php版本的php-fpm等等。
- webman: Webman是一款基于Workerman构建的高性能服务框架，集成了HTTP、WebSocket、TCP、UDP等多种模块。通过常驻内存、协程、连接池等先进技术，Webman不仅突破了传统PHP的性能瓶颈，还极大地扩展了其应用场景。
- php8：PHP 8 比 PHP 5 快 3-4倍，内存节省 50%-60%，开启JIT在计算密集型场景带来3-5倍提升，性能更好安全性更高
- mysql:MySQL是一个开源的、关系型的数据库管理系统（RDBMS），它以高性能、高可靠性、易用性和开源免费的特点著称
- redis: 一个开源的、基于内存的、高性能的键值对（Key-Value）存储数据库，通常用作缓存、消息队列、会话存储、排行榜、实时统计等场景。它支持多种数据结构（如字符串、哈希、列表、集合、有序集合等），读写速度极快（微秒级响应），支持持久化、高可用、分布式等特性
- composer: kucoder基于composer生态，你可以在webman里使用最熟悉的功能组件，很方便的就可以使用symfony、laravel、thinkphp等集成框架的优秀组件
- 依赖注入：kucoder采用PHP-DI依赖注入组件，


### 🎨 前端技术栈










### 🛠️ 技术栈前沿

| 层级 | 技术选型 |
|------|----------|
| 后端核心 | Webman 1.5+ / Workerman |
| PHP 版本 | PHP 8.1+ |
| 数据库 | MySQL 8.0+ (ThinkORM) |
| 缓存 | Redis 6.0+ |
| 前端框架 | Vue 3.5 + Vite 6 |
| UI 组件 | Element Plus 2.10 |
| 状态管理 | Pinia 3.0 |
| 图表 | ECharts 6 |
| 富文本 | VueQuill |
| 样式方案 | UnoCSS |

---

---

## 📂 目录结构

```
kc/                          # 项目根目录
├── app/                     # 应用主目录
├── config/                  # 配置文件
├── plugin/                  # 插件目录
│   ├── kucoder/            # 🚀 核心后端 (已开源)
│   │   ├── app/
│   │   │   ├── admin/     # 后台管理模块
│   │   │   ├── api/       # API 接口模块
│   │   │   ├── index/     # 门户模块
│   │   │   └── kucoder/   # 核心类库
│   │   │       ├── auth/  # 认证授权
│   │   │       ├── lib/   # 工具类
│   │   │       └── service/ # 业务服务
│   │   └── config/        # 插件配置
│   ├── kcweb/             # Web 接口插件
│   ├── member/            # 会员插件
│   ├── pay/               # 支付插件
│   └── weixin/            # 微信插件
├── public/                 # 静态资源
├── runtime/                # 运行时文件
├── vendor/                 # Composer 依赖
├── vue-kc-admin/           # 🚀 Vue3 管理后台 (已开源)
└── vue-kc-nuxt/           # 🔒 移动端 (私有)
```

---

## 🖥️ 快速开始

### 环境要求

| 环境 | 要求 |
|------|------|
| PHP | ≥ 8.1 |
| MySQL | ≥ 8.0 |
| Redis | ≥ 6.0 |
| Node.js | ≥ 18.0 |
| pnpm | ≥ 8.0 |

### 后端部署

```bash
# 1. 克隆项目
git clone https://gitee.com/kucoder/kucoder.git
cd kucoder

# 2. 安装依赖
composer install

# 3. 配置环境
cp .env.example .env
# 编辑 .env 配置数据库和 Redis

# 4. 导入数据库
mysql -u root -p webman_kucoder < webman_kucoder.sql

# 5. 启动服务 (Linux/Mac)
php webman start

# 5. 启动服务 (Windows)
php windows.php
```

服务默认运行在：`http://0.0.0.0:8788`

### 前端部署

```bash
# 进入管理后台目录
cd vue-kc-admin

# 安装依赖 (必须使用 pnpm)
pnpm install

# 开发模式
pnpm dev

# 生产构建
pnpm build
```

---

## 📋 核心模块说明

### plugin/kucoder 核心功能

| 模块 | 说明 |
|------|------|
| `RBAC` | 基于角色的访问控制，支持细粒度权限 |
| `JWT` | JSON Web Token 无状态认证 |
| `Crypto` | AES-256-GCM / ChaCha20-Poly1305 高强度加密 |
| `Captcha` | 图像验证码 + 滑动验证码 |
| `Log` | 操作日志记录与分析 |
| `Cache` | Redis 缓存管理 |
| `Event` | 事件系统，解耦业务逻辑 |
| `Plugin` | 插件热安装/卸载 |

### vue-kc-admin 功能特性

- 📊 **数据可视化**：ECharts 图表集成
- 📝 **富文本编辑**：VueQuill 富文本编辑器
- 🖼️ **图片处理**：vue-cropper 图片裁剪
- 🔍 **全局搜索**：Fuse.js 模糊搜索
- 📋 **数据表格**：Element Plus Table 高级功能
- 🎨 **主题切换**：明暗主题无缝切换
- 📱 **响应式布局**：适配各种屏幕尺寸

---

## 🔧 配置说明

### 数据库配置 (.env)

```env
DB_HOST=127.0.0.1
DB_PORT=3306
DB_NAME=webman_kucoder
DB_USER=root
DB_PASSWORD=your_password
DB_PREFIX=kc_
```

### Redis 配置 (.env)

```env
REDIS_HOST=127.0.0.1
REDIS_PORT=6379
REDIS_PASSWORD=
REDIS_DATABASE=0
```

---

## 📄 开源协议

本项目基于 **Apache-2.0** 协议开源，你可以免费使用于商业项目。

---

## 🌐 相关链接

- 🌐 官方网站：[http://kucoder.com](http://kucoder.com)
- 📚 Webman 文档：[https://www.workerman.net/doc/webman/](https://www.workerman.net/doc/webman/)
- 🎯 Vue 3 文档：[https://vuejs.org/](https://vuejs.org/)
- 💡 Element Plus：[https://element-plus.org/](https://element-plus.org/)

---

## ❤️ 致谢

感谢以下开源项目：

- [Webman](https://www.workerman.net/) - 高性能 PHP 协程框架
- [Vue 3](https://vuejs.org/) - 渐进式 JavaScript 框架
- [Element Plus](https://element-plus.org/) - Vue 3 UI 组件库
- [ThinkORM](https://www.kancloud.cn/manual/think-orm/) - ThinkPHP ORM

---

<p align="center">
  <strong>Made with ❤️ by Kucoder Team</strong>
</p>
