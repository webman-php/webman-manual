# `webman/console` 代码生成器

仅当任务要新增控制器、模型、CRUD、验证器、中间件、命令、Bootstrap 或自定义进程时读取本文件。生成器是可选的脚手架工具，不是 Webman 框架必然自带的能力；未安装时不要假定 `php webman make:*` 可用，更不能擅自安装依赖。

## 先确认可用性与写入边界

1. 从 `composer.lock` 或 `composer show` 确认是否安装了 `webman/console`；当前官方 Console 页面说明其下列能力以 v2.2+ 为准。若项目没有该包，`composer require webman/console` 会改变依赖，必须先得到用户授权。
2. 执行前运行 `php webman <命令> --help`，以目标项目锁定版本实际输出为准；本页只列高价值、已确认的参数，不替代当前版本帮助。
3. 生成命令会新增源码，有些还会写入 `config/middleware.php`、`config/bootstrap.php` 或 `config/process.php`。先检查目标文件、命名空间、插件名和现有约定；不要默认使用 `--force`。
4. 生成后审查 diff、补全路由/鉴权/白名单/错误协议/测试，并按配置与 Composer 变更的 reload 或 restart 边界验证。脚手架不是完整业务实现，也不能替代权限、租户隔离、数据库约束或迁移。

```bash
# 只读确认命令与选项；帮助输出优先于本页示例。
php webman make:crud --help
```

## 高价值生成命令

| 场景 | 命令与关键参数 | 生成后的必查项 |
|---|---|---|
| 新控制器 | `php webman make:controller User`；`--plugin/-p`、`--path/-P`、`--no-suffix` | 路由、HTTP 方法、鉴权、中间件、请求白名单；生成控制器不等于公开接口已安全。 |
| 从表生成模型 | `php webman make:model User --table=wa_users --orm=laravel --database=mysql`；`-t/-o/-d`、`--plugin/-p`、`--path/-P` | 连接名、表名、主键、时间戳、`$fillable` 与敏感字段。表名来自受控数据库设计，不能来自 HTTP 输入。 |
| 从表生成 CRUD | `php webman make:crud --table=wa_users`；可指定 `--model/-m`、模型/控制器路径、`--controller/-c`、`--plugin/-p`、`--orm/-o`、`--database/-d`、`--no-interaction/-n` | 它生成模型、控制器及**仅在 `webman/validation` 可用时**的验证器。逐项删除不应写入的字段，补齐资源级授权、路由、分页/排序白名单、事务和错误契约。 |
| 新中间件 | `php webman make:middleware Auth`；`--plugin/-p`、`--path/-P` | 该生成器会注册到对应 middleware 配置；检查执行范围与顺序，避免意外全局启用。 |
| 新 CLI 命令 | `php webman make:command user:list`；`--plugin/-p`、`--path/-P` | 命令名冲突、输入/输出、权限和幂等性；命令的业务副作用仍须单独授权。 |
| 启动初始化 | `php webman make:bootstrap MetricsBootstrap`；`--plugin/-p`、`--path/-P` | 默认会把类加入对应 bootstrap 配置；确认每 worker 执行、幂等性、外部锁与 restart 边界。 |
| 自定义进程 | `php webman make:process ReportWorker`；`--plugin/-p`、`--path/-P` | 该生成器会写入 process 配置；监听地址、协议、端口暴露、`count`、资源释放和 restart。 |
| 从表生成验证器 | `php webman make:validator UserValidator --table=wa_users --scenes=crud`；需同时安装 `webman/validation`；详细参数见 [validation-recipes.md](validation-recipes.md) | 表结构推导只给基础规则；检查 `create/update/delete/detail` 场景、可写字段、文件输入、唯一/归属和业务规则。 |

## `make:crud`、`make:model` 与 `make:validator` 的取舍

- 单独新增或完善已有模型时，优先考虑 `make:model`；它可从受控表结构补全模型属性信息，但不应覆盖人工维护的模型配置。
- 新的普通后台 CRUD 模块可考虑 `make:crud`，它减少模型、控制器和验证器骨架的重复工作。它不会理解项目的路由前缀、权限模型、资源归属、租户字段、软删除策略、字段脱敏或 API 兼容性，生成后必须人工收敛。
- 只需要可复用输入规则或已有控制器时，使用 `make:validator`；它属于 `webman/validation` 的能力，既不能因为安装了 Console 就假定存在，也不能因为安装了 Validation 就跳过 Console 前提。
- 未传 `--table` 的 `make:crud`/`make:model` 会进入交互选表流程。自动化或非交互执行前，表名、数据库连接、ORM、路径和覆盖范围都要来自受控项目配置，并在命令帮助中核实。

## 共同的安全规则

- `--force/-f` 会覆盖已有源码；只有已查看 diff、确认精确目标且用户授权覆盖时才使用。
- `--plugin/-p` 与 `--path/-P` 可能影响目录和命名空间；组合时先核对实际插件目录，不能把任意名称或路径直接传给命令。
- `--table/-t` 和 `--database/-d` 是数据库元数据选择，不是客户端表单参数。生成器查询数据库结构之前，先确认连接指向正确环境，尤其不要在未知生产连接上试探选表。
- 生成器没有测试业务行为。至少检查生成 PHP 的语法，并针对新增接口、进程或命令运行相称的受控验证；配置或 Composer 改动分别按目标项目的 reload/restart 规则说明。

官方依据：<https://www.workerman.net/doc/webman/plugin/console.html>；验证器生成的补充依据：<https://www.workerman.net/doc/webman/components/validation.html>。
