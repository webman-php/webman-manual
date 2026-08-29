# Webman 官方手册索引

仅在现有 reference 没有覆盖所需的**具体 API、低频组件、插件或运维主题**时读取本文件。这里是检索路由，不是手册副本：先读取下方对应的 skill 配方，再打开目标项目锁定版本可用的官方页面或源码。

## 查询顺序与版本边界

1. 先从 `composer.lock`、现有配置和调用点确认该组件已安装、已启用，以及项目的 Webman/Workerman 版本。
2. 查下方内部 reference；其中的配方优先解决 Webman 特有的生命周期、配置、生效方式和安全边界。
3. 配方未覆盖的方法，再打开对应的 Webman 官方页面，核对命名空间、签名、配置项和版本说明；当前网页可能比目标项目更新。
4. 无网络、文档版本不匹配或页面仍无法确定时，检查目标项目 `vendor/` 中锁定包的源码、PHPDoc、类型定义与已有调用。仍不能确认时，说明不确定性并请求文档/版本信息，不能把 Laravel、ThinkPHP、Swoole 或网上旧示例当作已验证 API。

不要因为查到一页文档就执行 `composer require`、迁移、重启、发布、创建端口或修改代理；这些操作仍需用户授权与对应 reference 的边界检查。

## 先读现有配方

| 任务关键词 | 先读 | 再查官方页 |
|---|---|---|
| `Request`、上传、下载、JSON、Cookie、Header、流式响应 | [http-recipes.md](http-recipes.md) | [请求](https://webman.workerman.net/doc/en/request.html)、[响应](https://webman.workerman.net/doc/en/response.html) |
| 控制器动作、参数绑定、类型/默认值、HTTP 方法 Attribute、注解路由、注解验证、Request 注入、控制器中间件、CRUD 骨架 | [controller-recipes.md](controller-recipes.md) | [控制器](https://webman.workerman.net/doc/en/controller.html)、[路由](https://webman.workerman.net/doc/en/route.html)、[验证器](https://webman.workerman.net/doc/en/components/validation.html) |
| 显式/默认路由、路由组、资源路由、CORS/OPTIONS、404、全局/应用/路由中间件 | [routing-and-middleware.md](routing-and-middleware.md) | [路由](https://webman.workerman.net/doc/en/route.html)、[中间件](https://webman.workerman.net/doc/en/middleware.html) |
| 配置、异常、日志、Session、视图、静态文件 | [application-services.md](application-services.md) | [配置](https://webman.workerman.net/doc/en/config.html)、[异常](https://webman.workerman.net/doc/en/exception.html)、[Session](https://webman.workerman.net/doc/en/session.html) |
| 字段校验、参数注解、上传文件校验、校验错误体 | [validation-recipes.md](validation-recipes.md) | [验证器](https://webman.workerman.net/doc/en/components/validation.html) |
| 查询、模型、事务、迁移、Redis、Cache、连接池 | [data-recipes.md](data-recipes.md) | [数据库章节](https://webman.workerman.net/doc/en/db/tutorial.html) |
| `eventLoop`、Context、Parallel、Pool、Swoole/Swow/Fiber | [coroutine-runtime.md](coroutine-runtime.md) | [协程](https://webman.workerman.net/doc/en/coroutine/coroutine.html) |
| 队列、定时任务、自定义进程、后台 worker、慢 I/O | [process-and-async.md](process-and-async.md) | [自定义进程](https://webman.workerman.net/doc/en/process.html)、[Redis Queue](https://webman.workerman.net/doc/en/queue/redis.html) |
| 启动、Windows、控制器复用、文件监控、生命周期 | [core-runtime.md](core-runtime.md) | [生命周期](https://webman.workerman.net/doc/en/others/lifecycle.html)、[启动流程](https://webman.workerman.net/doc/en/others/process.html) |
| 应用插件、基础插件、插件安装/升级/卸载/打包 | [plugin-development.md](plugin-development.md) | [应用插件](https://webman.workerman.net/doc/en/app/app.html)、[基础插件创建](https://webman.workerman.net/doc/en/plugin/create.html) |
| `make:controller`、`make:model`、`make:crud`、`make:validator`、`make:middleware`、`make:command`、`make:bootstrap`、`make:process` | [console-generators.md](console-generators.md) 及产物对应 reference | [命令行](https://www.workerman.net/doc/webman/plugin/console.html)、[验证器](https://www.workerman.net/doc/webman/components/validation.html) |
| PHPUnit/Pest、测试风险、日志/异常/事务排查、文件监控 | [testing-and-debugging.md](testing-and-debugging.md) | [单元测试](https://webman.workerman.net/doc/en/components/unitest.html)、[日志](https://webman.workerman.net/doc/en/log.html)、[异常](https://webman.workerman.net/doc/en/exception.html)、[事务](https://webman.workerman.net/doc/en/others/transaction.html) |

## 高频页面中的长尾 API

这些页面可解决“skill 没列出某个 getter/选项”的问题；先按页面标题和页面内搜索定位，不要凭相似框架名称猜测。

| 需要的能力 | 官方章节与可搜索词 |
|---|---|
| 特殊请求信息 | [请求](https://webman.workerman.net/doc/en/request.html)：`rawBody`、`file`、`getRealIp`、`expectsJson`、`controller`、`action`、`setGet`。代理真实 IP 仍须验证可信代理边界。 |
| 特殊响应形式 | [响应](https://webman.workerman.net/doc/en/response.html)：`withHeader`、`cookie`、`redirect`、`file`、`download`、`chunk`。 |
| 多应用、模板与静态资源 | [多应用](https://webman.workerman.net/doc/en/multiapp.html)、[视图](https://webman.workerman.net/doc/en/view.html)、[静态文件](https://webman.workerman.net/doc/en/static.html)。 |
| 统一错误与记录 | [异常处理](https://webman.workerman.net/doc/en/exception.html)、[日志](https://webman.workerman.net/doc/en/log.html)、[自定义 404/500](https://webman.workerman.net/doc/en/others/custom-error-page.html)。 |
| 控制台与自动生成 | 先读 [console-generators.md](console-generators.md)，再查 [命令行插件](https://www.workerman.net/doc/en/plugin/console.html)。仅在项目已安装/决定使用相应插件时，按锁定版本核对生成命令。 |

## 数据与消息组件

本 skill 只保留 Webman 接入和安全边界。关联、分页、MongoDB、Medoo、Stomp 或上游 ORM 的完整 API 应先确认组件已安装，再从以下章节继续。

| 主题 | 官方章节 |
|---|---|
| 数据库配置、查询、模型、关联与分页 | [配置](https://webman.workerman.net/doc/en/db/config.html)、[查询构造器](https://webman.workerman.net/doc/en/db/queries.html)、[模型](https://webman.workerman.net/doc/en/db/model.html)、[关联](https://webman.workerman.net/doc/en/db/relationships.html)、[分页](https://webman.workerman.net/doc/en/db/paginator.html) |
| 迁移、Redis、Cache | [迁移](https://webman.workerman.net/doc/en/db/migration.html)、[Redis](https://webman.workerman.net/doc/en/db/redis.html)、[Cache](https://webman.workerman.net/doc/en/db/cache.html) |
| ThinkORM、ThinkCache、MongoDB、Medoo | [ThinkORM](https://webman.workerman.net/doc/en/db/thinkorm.html)、[ThinkCache](https://webman.workerman.net/doc/en/db/thinkcache.html)、[MongoDB](https://webman.workerman.net/doc/en/db/mongo.html)、[Medoo](https://webman.workerman.net/doc/en/db/medoo.html) |
| 消息队列 | [Redis Queue](https://webman.workerman.net/doc/en/queue/redis.html)、[Stomp](https://webman.workerman.net/doc/en/queue/stomp.html)。不要把应用内 Event 当成持久化队列。 |

## 按需组件与基础插件

这些主题通常带独立依赖或配置。只有任务明确涉及、且目标项目已安装或用户批准新增时才打开并采用。

| 需求 | 官方章节 |
|---|---|
| 限流、分页、多语言、环境变量 | [限流器](https://webman.workerman.net/doc/en/components/rate-limiter.html)、[分页组件](https://webman.workerman.net/doc/en/components/paginator.html)、[多语言](https://webman.workerman.net/doc/en/components/translation.html)、[环境变量](https://webman.workerman.net/doc/en/components/env.html) |
| Event、Crontab、单元测试 | [Event](https://webman.workerman.net/doc/en/components/event.html)、[Crontab](https://webman.workerman.net/doc/en/components/crontab.html)、[单元测试](https://webman.workerman.net/doc/en/components/unitest.html) |
| 图片、验证码、Excel、微信、支付、Casbin | [图片](https://webman.workerman.net/doc/en/components/image.html)、[验证码](https://webman.workerman.net/doc/en/components/captcha.html)、[Excel](https://webman.workerman.net/doc/en/components/excel.html)、[微信 SDK](https://webman.workerman.net/doc/en/components/wechat.html)、[支付 SDK](https://webman.workerman.net/doc/en/components/payment.html)、[Casbin](https://webman.workerman.net/doc/en/components/casbin.html) |
| 基础插件、Push、发布基础插件 | [基础插件](https://webman.workerman.net/doc/en/plugin/base.html)、[Push](https://webman.workerman.net/doc/en/plugin/push.html)、[创建基础插件](https://webman.workerman.net/doc/en/plugin/create.html) |

## 应用插件、部署与诊断

应用插件有自己的目录、配置和发布生命周期；普通 Webman 应用不应为了引用一页插件文档而迁移到插件结构。

| 需求 | 官方章节 |
|---|---|
| 创建/维护应用插件 | [介绍](https://webman.workerman.net/doc/en/app/app.html)、[规范](https://webman.workerman.net/doc/en/app/standard.html)、[创建](https://webman.workerman.net/doc/en/app/create.html)、[目录](https://webman.workerman.net/doc/en/app/directory.html) |
| 插件路由、配置、控制器、视图、静态文件 | [路由](https://webman.workerman.net/doc/en/app/route.html)、[配置](https://webman.workerman.net/doc/en/app/config.html)、[控制器](https://webman.workerman.net/doc/en/app/controller.html)、[视图](https://webman.workerman.net/doc/en/app/view.html)、[静态文件](https://webman.workerman.net/doc/en/app/static.html) |
| 插件数据与交付 | [数据库](https://webman.workerman.net/doc/en/app/database.html)、[Redis](https://webman.workerman.net/doc/en/app/redis.html)、[日志](https://webman.workerman.net/doc/en/app/log.html)、[打包](https://webman.workerman.net/doc/en/app/pack.html)、[发布](https://webman.workerman.net/doc/en/app/publish.html)、[安装/卸载](https://webman.workerman.net/doc/en/app/install.html) |
| 生产代理与安全 | [Nginx 代理](https://webman.workerman.net/doc/en/others/nginx-proxy.html)、[安全](https://webman.workerman.net/doc/en/others/security.html)、[禁用函数检查](https://webman.workerman.net/doc/en/others/disable-function-check.html) |
| 启动问题、内存、脚本、性能 | [业务初始化](https://webman.workerman.net/doc/en/others/bootstrap.html)、[文件监控](https://webman.workerman.net/doc/en/others/monitor.html)、[内存泄漏](https://webman.workerman.net/doc/en/others/memory-leak.html)、[自定义脚本](https://webman.workerman.net/doc/en/others/scripts.html)、[性能](https://webman.workerman.net/doc/en/others/performance.html) |
| AOP、二进制/Phar、升级 | [AOP](https://webman.workerman.net/doc/en/aop.html)、[二进制打包](https://webman.workerman.net/doc/en/others/bin.html)、[Phar](https://webman.workerman.net/doc/en/others/phar.html)、[升级方法](https://webman.workerman.net/doc/en/others/upgrade.html) |

## 离线与旧版本回退

- 可使用项目自带或用户提供的本地手册搜索相同章节名，但不把某台开发机的文档路径写进可分发 skill。
- 正在跨版本升级时，先锁定起止版本并读对应升级记录；不要只看当前文档就直接改配置或替换 API。
- 官方页面、目标源码和项目现有调用冲突时，以目标项目锁定依赖和可运行配置为主，并把差异明确告诉用户。
