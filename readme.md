# HlwAdmin

HlwAdmin 是基于 ThinkPHP 的后台管理应用骨架，集成 `hlw2326/think-library`、后台管理模块与 Workerman 运行服务。项目使用 Composer 管理依赖，开箱支持 SQLite，也可切换至 MySQL。

- 官方网站：[https://hlwadmin.top](https://hlwadmin.top)
- PHP 要求：`^8.2`
- 许可证：MIT（各 Composer 扩展包以其自身许可证为准）

## 功能概览

- 后台管理模块与权限控制
- 系统配置、菜单、日志、字典、文件等基础管理能力
- ThinkPHP 6 / 8 框架兼容支持
- SQLite 本地数据库与 MySQL 数据库配置
- Workerman HTTP 服务及自定义进程支持
- API 信封加密联调接口
- Composer 插件安装机制，用于初始化扩展所需的配置和资源

## 环境要求

安装前请确认本机具备以下环境：

- PHP 8.2 或更高版本
- Composer 2
- PHP 扩展：`gd`、`json`、`openssl`
- 使用 ThinkLibrary 完整功能时，还需要：`curl`、`zlib`、`iconv`、`mbstring`
- 使用 MySQL 时，需要启用对应的 PDO 驱动（通常为 `pdo_mysql`）
- 使用 SQLite 时，需要启用 `pdo_sqlite`

检查 PHP 版本和已启用扩展：

```shell
php -v
php -m
composer --version
```

## 安装

### 1. 获取代码并安装依赖

在项目根目录执行：

```shell
composer install --optimize-autoloader
```

> `composer install` 会严格按 `composer.lock` 安装已锁定的依赖版本；只有需要主动升级依赖时，才应使用 `composer update`。

### 2. 配置运行环境

项目提供了环境配置文件：

- `.env.dev`：开发环境
- `.env.prod`：生产环境
- `.env.crypt`：加密相关配置

请按部署环境配置数据库、缓存、日志及其他服务参数。环境文件可能包含敏感信息，禁止提交真实的账号、密码、密钥或令牌。

### 3. 配置数据库

默认数据库连接为 SQLite，文件位置为：

```text
database/sqlite.db
```

数据库配置位于 `config/database.php`。如需使用 MySQL，请将默认连接切换为 `mysql`，并设置以下环境变量：

```dotenv
DB_TYPE=mysql
DB_MYSQL_HOST=127.0.0.1
DB_MYSQL_PORT=3306
DB_MYSQL_DATABASE=hlwadmin
DB_MYSQL_USERNAME=root
DB_MYSQL_PASSWORD=
DB_MYSQL_PREFIX=
DB_MYSQL_CHARSET=utf8mb4
```

数据库结构变更使用 ThinkPHP 迁移机制管理。迁移文件位于 `database/migrations/`；新增迁移请遵循项目命名规则：

```text
YYYYMMDDHHMMSS_动词_模块_用途.php
```

例如：`20261017123000_create_admin_schema.php`。

### 4. 执行初始化或迁移

先查看当前项目实际可用命令：

```shell
php think list
```

确认迁移命令后，再根据项目输出执行相应的迁移操作。例如：

```shell
php think migrate:run
```

## 本地运行

使用 ThinkPHP 内置开发服务器启动应用：

```shell
php think run --host 127.0.0.1 --port 8088
```

在浏览器访问：

```text
http://127.0.0.1:8088
```

根路径会跳转到后台登录页。生产环境请将 Web 根目录配置为 `public/`，并确保所有不存在的静态资源请求都由 `public/index.php` 处理。

## Workerman 服务

项目内置 Workerman 服务配置，默认监听地址为 `127.0.0.1:2346`，配置文件为 `config/worker.php`。

```php
'host' => '127.0.0.1',
'port' => 2346,
'worker' => [
    'name'  => 'HlwAdmin',
    'count' => 4,
],
```

启动或管理 Workerman 前，请通过以下命令确认本项目提供的具体命令及参数：

```shell
php think list
```

部署到公网前，请根据网络拓扑调整监听地址、端口、防火墙和反向代理配置；不要在未完成访问控制的情况下直接暴露管理服务。

## API 加密联调

项目提供 API 信封加密联调控制器：`app/index/controller/Api.php`。

| 请求 | 用途 |
| --- | --- |
| `GET /index/api/publicKey` | 获取服务端 RSA 公钥 |
| `POST /index/api/index` | 验证请求解密、验签并回显解密后的数据 |

第二个接口需要按 API 加密中间件约定携带 `x-client-*` 请求头。该控制器仅用于联调示例；上线前应结合实际业务添加鉴权、限流与审计措施。

## 目录结构

```text
├── app/
│   ├── admin/                 后台模块、路由、视图及服务
│   └── index/                 默认应用与 API 联调控制器
├── config/                    应用、数据库、路由、队列及 Workerman 配置
├── database/
│   ├── migrations/            数据库迁移文件
│   └── sqlite.db              默认 SQLite 数据库文件
├── public/                    Web 入口与公开静态资源
├── runtime/                   运行时缓存、日志等可再生文件
├── vendor/                    Composer 安装的依赖（不应手动修改）
├── composer.json              项目依赖与自动加载配置
└── think                      ThinkPHP 命令行入口
```

## 开发说明

- 使用 `app\` 到 `app/` 的 PSR-4 自动加载映射；新增业务代码请置于 `app/` 下。
- `app/admin/` 的后台基础代码主要由 Composer 包提供和维护。升级依赖前，请确认自定义修改不会被覆盖；业务扩展优先放在独立模块或应用代码中。
- 修改 `composer.json` 后执行 `composer update` 或 `composer install`，并提交对应的 `composer.lock` 变更。
- 不要直接编辑 `vendor/`；需要修改第三方能力时，应通过扩展、覆盖配置或向上游提交变更实现。
- `runtime/` 为运行时生成目录，通常无需提交版本控制。

## 常用命令

```shell
# 查看全部可用命令
php think list

# 启动本地开发服务器
php think run --host 127.0.0.1 --port 8088

# 查看路由（命令存在时）
php think route:list

# 查看迁移状态（命令存在时）
php think migrate:status
```

## 安全建议

- 生产环境使用独立、权限受限的数据库账号。
- 将 `.env.*`、私钥和生产凭据妥善保存在部署环境，不要提交到代码仓库。
- Web 服务器仅应暴露 `public/` 目录，禁止直接访问项目根目录、`vendor/`、`config/`、`database/` 与环境配置文件。
- 上线前关闭调试信息，设置正确的日志级别和错误页，并启用 HTTPS。
- 及时更新 Composer 依赖并评估安全公告对项目的影响。

## 许可证与版权

本项目遵循 [MIT License](LICENSE)（如仓库未包含许可证文件，请以项目发布页的声明为准）。Composer 安装的插件和依赖可能使用不同许可证，使用前请查阅各依赖包的 `composer.json` 与许可证文件。

Copyright © 2014-2026 HlwAdmin. All rights reserved.
