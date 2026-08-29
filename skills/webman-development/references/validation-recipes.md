# `webman/validation` 请求校验

仅在项目使用或准备使用 `webman/validation` 来新增/修改 HTTP 输入校验、验证器、参数注解、文件校验或验证错误响应时读取本文件。本页刻意只讲 `webman/validation`；其它验证器继续遵循项目已有封装和其上游文档，不能混用本页的类、注解或 console 命令。校验决定输入形状，不替代授权、对象归属检查、数据库约束、事务或上传文件安全检查。

## 先识别已安装组件

先检查 `composer.lock`、现有控制器、`app/validation/`、插件配置和现有错误响应，确认 `webman/validation` 已安装并且项目确实采用它。

| 状态 | 入口与处理原则 |
|---|---|
| 已安装 `webman/validation` | 使用 `support\validation\Validator`、`#[Validate]`、`#[Param]` 与本页配方；规则语义仍以锁定的 Illuminate 版本为准。 |
| 未安装，或项目使用其它方案 | 不套用本页 API；新增 `webman/validation` 或 `webman/console` 都是 Composer 依赖变更，须得到用户授权。 |

- `webman/validation` 安装会改变 Composer 依赖并自动加载自己的验证中间件；已安装时不需要为了注解功能再手动注册一份中间件。安装新包或改变其启动配置后按 [core-runtime.md](core-runtime.md) 的 restart 边界处理。
- 注解/属性校验只在项目已经采用该风格、且 PHP/组件版本满足要求时使用。普通小接口优先沿用现有手动验证，不为减少几行代码改变控制器调用、反射或错误响应路径。

## 输入来源与白名单

先按接口契约提取应接受的字段，再验证；不要把 `$request->all()`、`$request->post()` 或原始 JSON 整包直接传给模型 `fill()`、`create()`、`update()` 或服务层。验证“通过”不会自动删除未知字段，也不能自动完成授权。

```php
use support\validation\Validator;

// 只取此接口声明接收的 body 字段。
$input = [
    'display_name' => $request->post('display_name'),
    'email' => $request->post('email'),
];

Validator::make($input, [
    'display_name' => 'required|string|min:2|max:50',
    'email' => 'required|email|max:254',
])->validate();

// 后续只使用 $input 中的白名单字段；授权和租户范围另行检查。
```

- 路径参数、query 与 body 中同名字段必须明确谁是事实来源。例如资源 ID 通常只取路由参数，body 不应允许覆盖。`#[Validate]`/`#[Param]` 的 `in` 能限制来源；未指定时组件会合并 query、body、path，且后面的来源覆盖前面来源。
- `in` 只决定验证器取数，验证完成后不会改写控制器参数绑定的实参。控制器绑定固定按“路由参数 > GET > POST”取值；若两者并用，按该优先级使用 `['body', 'query', 'path']`，或改用 `$request->post()` / `$request->get()` 严格读取来源。控制器组合示例见 [controller-recipes.md](controller-recipes.md)。
- `integer`、`boolean`、`array` 等规则用于接受/拒绝输入，不应被当作业务对象的自动类型转换。验证成功后仍根据字段语义显式转换、限制范围并做业务判断；金额、权限、排序字段和标识符尤其不能直接相信字符串输入。
- `in`、枚举、最大长度、数组结构和字段白名单应反映接口真正接受的集合。规则中的 `exists`/`unique` 依赖数据库组件，且不能消除并发竞争；唯一性、外键/归属和金额/库存正确性仍由数据库约束、事务和授权检查保证。

## `webman/validation` 的最小使用

`Validator::make(...)->validate()` 失败时抛出 `support\validation\ValidationException`。它继承 BusinessException，默认不按系统故障记录；让项目统一异常 Handler 渲染，除非当前 API 契约明确要求控制器就地处理字段错误。

- `fails()` 与 `errors()` 适合现有接口已经约定返回字段错误集合的情况；不要在新接口中临时发明另一种 `{errors: ...}` 格式。
- 当前组件的 JSON body 默认可带业务 code（通常为 422）和 `msg`/`data`，但 HTTP 状态仍取决于当前 BusinessException/Handler 实现。与 [application-services.md](application-services.md) 的错误响应协议保持一致，不能只因校验失败就假定一定返回 HTTP 422。
- 参数签名推导和 `#[Param]` 是附加规则，不会替代业务语义；例如 `int $id` 不能证明该 ID 属于当前租户或当前用户有权访问。

## 可复用规则集与场景

当创建、更新、删除等接口真正复用同一套字段规则时，继承 `support\validation\Validator` 定义 `rules`、`messages`、`attributes` 与可选 `scenes`。未指定场景会验证全部规则；只有显式调用 `withScene()` 或在 `#[Validate(..., scene: ...)]` 中指定时才验证该场景字段。

```php
<?php

namespace app\validation;

use support\validation\Validator;

final class UserValidator extends Validator
{
    protected array $rules = [
        'id' => 'required|integer|min:1',
        'name' => 'required|string|min:2|max:50',
        'email' => 'required|email|max:254',
    ];

    protected array $scenes = [
        'create' => ['name', 'email'],
        'update' => ['id', 'name', 'email'],
    ];
}
```

```php
use app\validation\UserValidator;

UserValidator::make($input)->withScene('create')->validate();
```

- 场景只选择字段规则，不会自动决定输入来源、类型转换、对象归属或可写字段。更新场景中的 `id` 仍必须以受控的路由参数/授权结果为准，不能允许 body 任意覆盖。
- 不要把创建、更新、管理员操作混进一个宽松的通用场景；规则差异明显时使用清晰的场景或独立 Validator。

## 用 `webman/console` 生成 Validator

关于 Console 是否可用、写入边界和其它生成器的共同规则，先读 [console-generators.md](console-generators.md)。本节仅补充 `webman/validation` 的 `make:validator` 参数与生成后审查。

仅当 `webman/validation` 与 `webman/console` 都已安装时，才使用 `php webman make:validator`。生成前先运行以下只读帮助命令，核对目标项目锁定版本真正支持的选项；不要根据此 skill 猜测未来版本的额外参数。

```bash
php webman make:validator --help
```

文档确认的高价值参数如下：

| 参数 | 作用与边界 |
|---|---|
| `UserValidator` | 必填的类名；默认生成到 `app/validation/`。不把用户输入直接拼入类名或文件路径。 |
| `--table=wa_users` / `-t wa_users` | 从**数据库表结构**推导基础规则（字段类型、可空、长度等）。它不是 HTTP 表单字段清单，也不知道路由、租户、授权、枚举或业务状态。 |
| `--database=mysql` / `-d mysql` | 指定已有配置中的数据库连接名，用于多连接项目；先核对 [data-recipes.md](data-recipes.md) 的连接契约。 |
| `--scenes=crud` / `-s crud` | 生成文档定义的 `create`、`update`、`delete`、`detail` 场景。`update` 包含主键与其它字段；`delete`/`detail` 默认只含主键。生成后必须复核每个场景的可写/可读边界。 |
| `--orm=laravel` 或 `--orm=thinkorm` / `-o` | 当项目同时存在多套 ORM 时显式选择；默认自动选择，两个都存在时文档说明会优先 illuminate。不能借此在项目中混用 ORM。 |
| `--force` / `-f` | 覆盖已有 Validator 文件。只有已检查差异、确认目标文件和用户允许覆盖时才能使用。 |

```bash
# 先确认连接名、表名、ORM 和现有文件；此命令会新增或覆盖 PHP 文件。
php webman make:validator UserValidator \
  --table=wa_users \
  --database=mysql \
  --scenes=crud \
  --orm=laravel
```

- 生成器会按所选 ORM 忽略常见时间/软删除字段，但不会替你建立唯一索引、外键、租户约束、权限规则、敏感字段策略或线上兼容场景。生成后逐条审查 `$rules` 和 `$scenes`，删除不应由客户端提交的字段，并补上接口实际需要的白名单和业务规则。
- 未提供 `--table` 时只能生成空模板；`--table`、`--database`、`--orm` 等值都必须来自当前项目受控配置，而不是请求参数、表单字段或不可信文本。
- 生成命令写入源码；在执行前检查目标文件是否存在。生成后运行 PHP 语法检查与该接口的成功/失败用例，并按安装/配置变更的 restart 边界说明生效方式。

## 上传与数据库规则的额外边界

- 注解中间件默认合并 query、body 和路由参数，不包含上传文件。涉及 `file`、`image`、`mimes`、MIME、尺寸或大小规则时，使用手动 `Validator::make()` 并明确把 `$request->file()` 的目标文件合并进输入，随后仍按 [http-recipes.md](http-recipes.md) 的文件名、MIME、存储位置和公开访问规则处理。
- 不要仅依据扩展名、客户端 `Content-Type` 或验证器通过就把上传文件公开。验证器、Webman `UploadFile` 与底层 PHP 扩展的可用 API 随锁定版本变化；生成文件校验规则前先检查目标项目已安装版本的签名。
- 数据库 `exists` 与 `unique` 规则会产生查询。对高频、批量或攻击面接口设置必要的前置格式/长度限制，避免用昂贵的 DNS、正则或数据库规则替代简单的边界校验；生产唯一写入仍应处理数据库冲突异常。

## 验证边界

- 为每个接口至少覆盖：合法输入、缺失/错误类型、越界长度、额外字段不会被写入、路径/query/body 冲突，以及需要时的上传失败与授权拒绝。校验通过不等于接口已安全：还要验证租户/对象授权和数据库约束。
- 新增或更改验证规则时，确认失败响应与既有客户端兼容，并在目标语言环境检查消息/字段名。不要把原始值、密码、令牌或内部规则细节直接回显到错误 body 或日志。
- 完整的 Laravel 风格规则集、自定义 Rule、复杂条件规则、语言包和其它验证器属于按需查询内容；以 `composer.lock` 对应版本和官方文档为准，不在本 skill 复制规则手册。
