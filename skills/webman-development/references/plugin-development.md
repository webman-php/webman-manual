# Webman 插件开发

仅在任务明确要创建、维护、安装、升级、卸载、打包或发布 Webman **基础插件**或**应用插件**时读取。本文件不把普通 Webman 应用改造成插件，也不代替第三方组件的文档。

## 先识别插件类型与现状

先检查目标项目的 `composer.lock`、`plugin/`、`config/plugin/`、`webman/console` 版本及已有插件调用；再确认本次是项目内模块、可安装应用，还是 Composer 组件。不要把目录存在或名称相似当作可以安全安装、卸载或发布的许可。

| 目标 | 选择 | 关键边界 |
|---|---|---|
| 只服务当前项目的业务模块 | 普通应用代码 | 不因“可复用”就迁移为插件。 |
| 可安装的完整业务应用，如 CMS、商城 | 应用插件 | 源码在 `plugin/<标识>/`，默认 URL 前缀是 `/app/<标识>`。 |
| 可由多个项目 Composer 安装的通用库、中间件、路由或进程 | 基础插件 | 包代码位于 Composer `vendor/`；配置通过 `config/plugin/<厂商>/<包名>/` 接入。 |

应用插件的标识同时影响目录、`plugin\<标识>` 命名空间、默认 URL 和数据表前缀。已有标识不可擅自改名；若要发布到公共应用市场，先按其当前规则确认标识可用。基础插件使用小写 Composer 包名 `<厂商>/<包名>`，并以其作为配置命名空间。

## 应用插件的隔离与全局边界

- 代码放在 `plugin/<标识>/`；控制器使用 `plugin\<标识>\app\controller`。`app/`、`config/`、`public/` 和 `api/` 的职责与普通应用相似。
- `api/` 是供主项目或其它插件进行 **PHP 函数/类调用** 的内部接口，不是自动暴露的 HTTP API。网络入口仍需路由、鉴权和输入校验。
- 默认路由形如 `/app/<标识>/...`。插件的 `config/route.php` 仍操作全局路由表：检查重复路径、优先级、fallback 和鉴权，不能把“插件配置隔离”误解为路由隔离。
- 插件的 `app`、`middleware`、`view`、`container`、`exception` 等配置通常只作用于该插件；使用 `config('plugin.<标识>.<配置>')` 读取。`server.php`、`session.php`、`app.request_class`、`app.public_path`、`app.runtime_path` 不属于应用插件支持的隔离配置，不能复制主项目配置来假设生效。
- 静态资源放在 `plugin/<标识>/public/`，但仍须遵守 [production-and-security.md](production-and-security.md) 的公网文件与脚本执行边界。控制器复用、请求状态和协程限制仍遵守 [core-runtime.md](core-runtime.md) 与 [coroutine-runtime.md](coroutine-runtime.md)。

## 数据、缓存与日志

先选定“复用宿主资源”还是“插件自有连接”，把选择、权限和安装说明写清楚；不要默认依赖 `webman-admin` 或任何宿主插件。

- 插件自有 Laravel 数据库连接通过 `Db::connection('plugin.<标识>.<连接名>')` 使用；Redis 对应 `Redis::connection('plugin.<标识>.<连接名>')`。只有目标项目确实安装了相应组件时才使用这些 API。
- 独立插件的表名应以标识为前缀，例如 `foo_orders`，避免与宿主或其它插件冲突；业务授权和租户范围不能只靠表前缀实现。
- 不将数据库密码、Redis 密码或生产地址打进发布包。若安装必须填写连接信息，设计经授权的安装引导、密钥注入或运维配置流程。
- 查询、事务、迁移和缓存的具体用法读取 [data-recipes.md](data-recipes.md)；日志 channel 与脱敏读取 [application-services.md](application-services.md)。

## 创建、安装、升级与卸载

执行会安装依赖、写入数据库、删除文件/表、修改菜单或重启服务的命令前，先获得相应授权，并先阅读命令的 `--help` 和当前已安装版本的实现。

### 应用插件

1. 确认 `webman/console` 可用后，用 `php webman app-plugin:create <标识>` 生成骨架；缺少 `webman/console` 时，安装 Composer 依赖须先获用户同意。
2. 按插件目录和命名空间完善模块，并在重启后验证 `/app/<标识>` 的实际路由、鉴权、静态资源和异常响应。代码或插件配置变化的 reload/restart 边界仍以当前项目和 [core-runtime.md](core-runtime.md) 为准。
3. 从源码包安装时，先将目录放入 `plugin/<标识>/`，再执行 `php webman app-plugin:install <标识>`。安装脚本、`install.sql`、菜单或外部资源都可能产生持久副作用；先在可恢复环境检查目标连接和变更内容。
4. 升级的新增结构变更应按当前插件的 `Install.php`/`install.sql` 约定追加并以分号结束；同时测试全新安装与从旧版本升级。不要将数据删除、不可逆转换或外部调用伪装成普通安装步骤。
5. 卸载先备份或明确数据保留策略，再运行 `php webman app-plugin:uninstall <标识>`；确认脚本和 `uninstall.sql` 是否会删表或删资源，最后才删除 `plugin/<标识>/`。这是破坏性操作，必须逐项获授权。

打包前核对 `config/app.php` 版本、许可证/说明、`install.sql`、升级与卸载行为，并移除测试上传文件、密钥和环境专属配置。发布到市场、Git 仓库、Packagist 或其它外部服务是单独的外部写操作，不能因为“打包完成”而默认执行。

### 基础插件

- 创建/导出前确认 `webman/console` 和命令参数：通常为 `php webman plugin:create --name=<厂商>/<包名>` 与 `php webman plugin:export --name=<厂商>/<包名>`；版本不确定时先运行相应 `--help`。
- 基础插件的自动识别配置位于 `config/plugin/<厂商>/<包名>/`。当前手册列出的自动合并文件包括 `app.php`、`bootstrap.php`、`route.php`、`middleware.php`、`process.php`、`database.php`、`redis.php`、`thinkorm.php`；其他文件是否被加载必须核对已安装版本或现有插件。
- 导出会生成或更新 `Install.php`，安装时可把配置复制到宿主 `config/plugin/`，卸载时可能删除它们。将 `Install.php` 当作受审计的安装程序：最小化对宿主的写入，不复制不必要的业务代码，不覆盖用户修改，也不在未授权时执行数据库、网络或删除操作。
- 自定义控制台命令应使用 `<厂商>-<包名>:<命令>` 前缀以避免冲突；命令注册与 Console API 的具体签名读取 [manual-index.md](manual-index.md)。

## 交付前验证

- 检查 Composer/插件标识、PSR-4 命名空间、路由路径与配置键一致，且没有与宿主或其它插件冲突。
- 在隔离环境验证：创建后的插件可启动、受保护路由拒绝未授权请求、静态文件和内部 `api/` 边界符合预期。
- 涉及数据时，分别验证空安装、升级路径和卸载预演；明确哪些步骤未在真实数据库或真实插件市场执行。
- 打包前检查归档内容不含 `.env`、密钥、运行时日志、测试上传物或环境专属连接信息；外部发布仅在用户明确授权后进行。

来源以目标项目锁定的 `webman/console`、插件目录和官方“基础插件创建”“应用插件”章节为准；本文件不复制完整 Console、ORM 或应用市场 API。
