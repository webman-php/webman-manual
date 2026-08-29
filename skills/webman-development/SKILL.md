---
name: webman-development
description: Develop, modify, review, debug, test, or deploy Webman PHP applications built on Workerman. Use when a project contains workerman/webman-framework or the task explicitly concerns Webman routing, controllers, middleware, configuration, long-lived workers, custom processes, queues, coroutines, databases, or plugins. Do not use for standalone Workerman or ordinary PHP, Laravel, or ThinkPHP projects without Webman.
---

# Webman 开发

将 Webman 视为基于 Workerman 的常驻内存、多进程应用。先确认当前项目实际安装的版本、配置和既有约定，再选择实现方式；不要按 PHP-FPM、Laravel 或 ThinkPHP 项目的默认运行模型推断。

## 开始前检查

按当前任务需要检查：

- `composer.json` 和 `composer.lock` 中的 `workerman/webman-framework`、`workerman/workerman` 及已安装的数据库、队列、协程组件。
- `start.php`、`windows.php`、`app/`、`config/`、`public/` 和 `runtime/` 的实际位置。
- `config/app.php` 中的 `controller_reuse`、控制器后缀和运行时路径。
- `config/process.php` 或旧项目的 `config/server.php`，以确认进程、监听端口和事件循环配置。
- 已有的路由、中间件、异常响应、日志、数据库与缓存写法。

如果项目只有 `workerman/workerman` 而没有 Webman 框架或 Webman 目录结构，不要套用本 skill 的控制器、路由和配置约定。

保留项目已有架构；除非用户明确要求，不要仅为了“更标准”而切换 ORM、容器、路由模式、队列或进程模型。

## 始终遵守

- 用控制器返回值或 `Response` 返回 HTTP 内容；不要把 `echo`、`var_dump` 当作响应。
- 不在请求处理中调用 `exit`、`die` 或 `pcntl_fork`。
- 不把请求、当前用户、事务、上传文件或其他请求状态写入全局变量、静态变量、单例、容器缓存对象或可复用控制器的属性。
- 普通局部变量在方法结束后可回收；只有确实需要跨请求复用且无请求状态的对象，才可放入长生命周期位置。
- 不假设协程已启用。若项目启用了协程，避免多个协程共享同一数据库连接、文件句柄或可变全局状态。
- 代码或普通配置变更后说明 reload 边界；进程/服务器配置变更或安装 Composer 包后说明 restart 边界。
- 事务、队列、进程、协程和部署改动必须根据已安装版本与真实配置验证，不能只凭通用 PHP 经验猜测。

## 按需读取参考资料

- 修改启动方式、配置、生命周期、控制器复用、业务初始化、监控或 Windows/Linux 行为时，读取 [references/core-runtime.md](references/core-runtime.md)。
- 处理请求参数、Header、Cookie、客户端 IP、上传、JSON 响应、重定向、文件下载或分段响应时，读取 [references/http-recipes.md](references/http-recipes.md)。
- 新增、修改或审查控制器动作、参数绑定、类型/默认值、HTTP 方法 Attribute、Attribute 路由、注解验证、Request 注入、控制器/方法中间件、控制器返回值或 CRUD 骨架时，先读取 [references/controller-recipes.md](references/controller-recipes.md)，再按其中链接读取专项 reference。
- 修改 `config/route.php` 显式/默认路由、路由参数、CORS/OPTIONS、404、全局/应用/路由中间件范围或鉴权入口边界时，读取 [references/routing-and-middleware.md](references/routing-and-middleware.md)。
- 处理数据库配置、查询、模型、事务、迁移、连接池、Redis 或 Cache 时，读取 [references/data-recipes.md](references/data-recipes.md)。
- 读取或修改配置、统一异常响应、业务异常、应用日志、Session、视图、静态文件或其生产代理边界时，读取 [references/application-services.md](references/application-services.md)。
- 校验请求字段、使用 `webman/validation`、参数注解、文件校验或处理校验失败时，读取 [references/validation-recipes.md](references/validation-recipes.md)。
- 检查或启用协程、修改 `eventLoop`、使用 Context/Parallel/连接池，或排查协程并发状态污染时，读取 [references/coroutine-runtime.md](references/coroutine-runtime.md)。
- 选择慢任务方案、使用队列、增加自定义进程/监听、定时任务、业务启动初始化、隔离慢 HTTP 或排查后台 worker 时，读取 [references/process-and-async.md](references/process-and-async.md)。
- 部署、发布、Nginx/HTTPS/代理、生产故障、安全审查、文件权限或公网暴露相关任务时，读取 [references/production-and-security.md](references/production-and-security.md)。
- 创建、维护、安装、升级、卸载、打包或发布基础插件、应用插件时，读取 [references/plugin-development.md](references/plugin-development.md)。
- 新增或运行测试、复现故障、排查日志/异常/事务、确认配置生效或说明验证范围时，读取 [references/testing-and-debugging.md](references/testing-and-debugging.md)。
- 创建控制器、模型、CRUD、验证器、中间件、命令、Bootstrap 或自定义进程等脚手架时，读取 [references/console-generators.md](references/console-generators.md)，先确认 `webman/console`（以及 Validator 所需的 `webman/validation`）已安装、目标版本参数和 `--help`；生成/覆盖文件、安装依赖或执行数据操作仍须逐项获授权。再读取该产物对应的路由、数据、验证、运行时或进程 reference。
- 尚未被本 skill 覆盖的具体 API、低频组件或运维主题，读取 [references/manual-index.md](references/manual-index.md)；先查目标项目锁定版本对应的官方文档或源码，不要用未确认的上游 Laravel、ThinkORM 或新版本 Webman API 替代。

## 实施与验证

- 先读取与任务有关的路由、控制器、配置和依赖，再编辑最小范围的文件。
- 修改需要重载或重启的内容时，明确给出原因和适用命令；Windows 不具备 Linux 守护/进程管理命令时不得照搬。
- 至少运行与改动相称的检查，例如 PHP 语法检查、现有测试、配置加载或实际 HTTP/队列验证。说明实际运行过什么，未运行什么。
- 不把语法检查、静态检查或单进程验证描述为生产、多进程、数据库或并发验证。
