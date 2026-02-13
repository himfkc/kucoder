# Kucoder 开源免费高性能PHP框架

<p align="center">
  <!-- <img src="/logo.png" alt="Kucoder" width="200"/> -->
</p>

<p align="center">
  <!-- <a href="https://gitee.com/kucoder/kucoder">
    <img src="https://gitee.com/kucoder/kucoder/badge/star.svg?theme=dark" alt="Gitee stars"/>
  </a>
  <a href="https://gitee.com/kucoder/kucoder">
    <img src="https://gitee.com/kucoder/kucoder/badge/fork.svg?theme=dark" alt="Gitee forks"/>
  </a> -->
  <a href="https://github.com/kucoder/kucoder">
    <img src="https://img.shields.io/github/stars/kucoder/kucoder?style=flat" alt="GitHub stars"/>
  </a>
  <a href="https://www.php.net/">
    <img src="https://img.shields.io/badge/PHP-%3E=8.1-blue" alt="PHP version"/>
  </a>
  <a href="https://www.workerman.net/doc/webman/">
    <img src="https://img.shields.io/badge/Webman-1.5+-green" alt="Webman version"/>
  </a>
</p>

## 📚 简介

**Kucoder** 是一款基于 [Webman](https://www.workerman.net/doc/webman/) 高性能 PHP 框架开发的快速开发系统，基于Workerman + Webman + PHP8 + Vue3 + ElementPlus + JavaScript(非ts)，遵循apache-2.0开源协议，采用插件化架构设计，内置完整的管理后台，为开发者提供高效、安全、现代化的 Web 开发体验。

---

## 🚀 核心特性

### ⚡ 高性能

- 🚀 基于 **Webman** 协程框架，默认 **WorkerMan** 高性能 HTTP 服务
- 采用插件化设计，kucoder不会影响webman框架本身，webman可自由升级
- 💾 内置 **Redis** 高速缓存，支持分布式部署
- ⚡ 异步 HTTP 请求支持，轻松应对高并发场景
- 🔄 事件驱动架构，模块解耦更灵活
- 📦 连接池技术，数据库访问更高效

### 🔒 高安全性

- 🛡️ 完整 **RBAC** 权限控制系统，细粒度权限管理
- 🔑 **JWT** 无状态身份认证，安全可靠
- 🔐 高强度加密支持：**AES-256-GCM**、**ChaCha20-Poly1305**、**AES-256-CCM**
- 🔒 防火墙级别的 **CORS** 跨域访问控制
- 👁️
- 📝 完整的 **操作日志** 与 **登录日志** 审计

### 🎨 功能丰富

- 👥 用户管理、角色管理、部门管理
- 📂 菜单管理（支持目录、菜单、按钮级别）
- 📚 字典管理、数据表管理
- 📊 操作日志、登录日志分析
- 🧩 插件化架构，灵活扩展
- 📥 文件上传管理（本地/云存储）
- 📲 接口文档生成
- 📈 数据统计与分析

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

## 🏗️ 系统架构

```
┌─────────────────────────────────────────────────────────┐
│                    vue-kc-admin                        │
│                 (Vue3 管理后台 - 已开源)                │
└──────────────────────┬──────────────────────────────────┘
                       │ HTTP / WebSocket
┌──────────────────────▼──────────────────────────────────┐
│                  Webman HTTP Server                     │
│  ┌─────────────────────────────────────────────────┐   │
│  │              plugin/kucoder                      │   │
│  │  ┌─────────────┬─────────────┬─────────────┐    │   │
│  │  │   Admin     │    API      │   Index     │    │   │
│  │  │  (后台管理)  │  (接口服务)  │  (门户入口)  │    │   │
│  │  └─────────────┴─────────────┴─────────────┘    │   │
│  │  ┌───────────────────────────────────────────┐    │   │
│  │  │   Auth │ RBAC │ JWT │ Crypto │ Cache      │    │   │
│  │  └───────────────────────────────────────────┘    │   │
│  └─────────────────────────────────────────────────┘   │
│  ┌─────────────────────────────────────────────────┐   │
│  │              plugin/kcweb (可选)                  │   │
│  └─────────────────────────────────────────────────┘   │
└──────────────────────┬──────────────────────────────────┘
                       │
        ┌──────────────┼──────────────┐
        ▼              ▼              ▼
    ┌────────┐    ┌────────┐    ┌────────┐
    │ MySQL  │    │ Redis  │    │  文件  │
    └────────┘    └────────┘    └────────┘
```

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
