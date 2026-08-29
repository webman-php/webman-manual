# Webman 控制器配方

新增、修改或审查控制器动作时，**先读取本页**。它给出控制器可以控制什么以及首选组合；具体的路由配置、输入 API、验证规则、上传和生成器细节再按下方链接读取。先确认目标项目的 `config/app.php`、既有路由风格、统一响应/异常格式和 `controller_reuse`，不要把其它 PHP 框架的控制器习惯直接带入。

## 控制器的能力地图

| 需要控制的事 | 首选方式 | 需要深入时 |
|---|---|---|
| 路径、HTTP 方法、路由名 | 已有项目沿用 `config/route.php`；已采用 Attribute 路由的 2.2+ 项目在控制器用 `#[Get]` / `#[Post]` 等 | [routing-and-middleware.md](routing-and-middleware.md) |
| 基础输入、必填、类型和默认值 | 控制器参数绑定：`string $name`、`int $id`、`int $page = 1` | 本页「参数绑定」 |
| 长度、格式、跨字段、场景和字段来源 | 已安装 `webman/validation` 时使用 `#[Param]` / `#[Validate]` 或复用 Validator | [validation-recipes.md](validation-recipes.md) |
| Header、Cookie、JSON 原始包体、上传文件 | 只在需要这些非普通字段时注入 `support\Request` | [http-recipes.md](http-recipes.md) |
| 认证、限流、审计等横切规则 | `#[Middleware(...)]` 或项目既有路由/全局中间件 | [routing-and-middleware.md](routing-and-middleware.md) |
| JSON、文件、下载和状态码 | 从动作 `return` 字符串、数字或 `support\Response` | [http-recipes.md](http-recipes.md) |
| 普通 CRUD 骨架 | 已安装且获准使用 `webman/console` 时考虑 `make:crud` | [console-generators.md](console-generators.md) |

## 推荐的动作形状

对新的、稳定的 API，优先让一个控制器动作清楚表达：**路径/方法 → 参数来源与类型 → 字段规则 → 认证范围 → 响应**。下面示例只适用于已确认 `webman-framework >= 2.2.0`、已采用 Attribute 路由且安装了 `webman/validation` 的项目：

```php
<?php
namespace app\controller;

use app\middleware\AuthMiddleware;
use support\annotation\Middleware;
use support\annotation\route\DisableDefaultRoute;
use support\annotation\route\Get;
use support\annotation\route\Post;
use support\annotation\route\RouteGroup;
use support\Request;
use support\Response;
use support\validation\annotation\Validate;

#[DisableDefaultRoute]
#[RouteGroup('/api/v1')]
#[Middleware(AuthMiddleware::class)]
class UserController
{
    // 验证顺序与参数绑定一致：body → query → path，后者优先。
    #[Get('/users/{id:\d+}', 'api.users.show')]
    #[Validate(
        rules: [
            'id' => 'min:1',
            'page' => 'min:1|max:100',
        ],
        in: ['body', 'query', 'path']
    )]
    public function show(
        int $id,
        int $page = 1,
    ): Response {
        return json(['code' => 0, 'data' => compact('id', 'page')]);
    }

    // 严格只接受 body：校验和实际读取都明确来自 post body。
    #[Post('/users', 'api.users.store')]
    #[Validate(rules: [
        'name' => 'required|string|min:2|max:50',
        'age' => 'integer|min:0',
    ], in: 'body')]
    public function store(Request $request): Response
    {
        $input = $request->post();
        $name = (string) $input['name'];
        $age = isset($input['age']) ? (int) $input['age'] : 18;

        // 只把经过规则与业务判断后允许的字段交给服务层/模型。
        return json(['code' => 0, 'data' => compact('name', 'age')]);
    }
}
```

- `#[DisableDefaultRoute]` 防止默认路径 `/user/show`、`/user/store` 形成第二个入口；`#[RouteGroup('/api/v1')]` 统一添加 API 前缀；类中间件负责共同认证。
- `show()` 用一个方法级 `Validate` 集中描述路由 ID 与可选分页。它会从签名补齐 `required|integer` 或 `integer`，而 `in: ['body', 'query', 'path']` 的后者优先顺序与控制器的“路由 > GET > POST”取值一致；分页通常由 query 提供。若接口契约必须严格 query-only，改用 `$request->get()` 读取。
- `store()` 展示严格 body-only 写接口：`Validate(in: 'body')` 验证的字段，必须也用 `$request->post()` 读取。不要把 `#[Param(in: ['body'])] string $name` 与直接参数绑定并用；后者仍会让同名 GET 参数优先，造成“校验值”和“实际值”不一致。
- 普通项目若采用 `config/route.php`，保留同一参数/验证/响应形状，把 `#[Get(...)]`、`#[Post(...)]`、`#[RouteGroup]`、`#[DisableDefaultRoute]` 换成对应的显式路由即可。

## 参数绑定：默认的基础输入方案

Webman 会按**路由参数 → GET → POST**为同名控制器参数取值。类型声明进行转换；缺少必填参数或不能转换会报输入异常；带默认值的参数可省略。

```php
use support\Response;

public function show(int $id, int $page = 1): Response
{
    return json(['id' => $id, 'page' => $page]);
}
```

- 对简单 ID、分页、开关等输入，优先直接声明参数与类型；不必为每一个值先写 `$request->input()` 再手动转换。
- 参数绑定没有 `只从 body` 或 `只从 query` 的开关；需要严格来源时改用对应的 Request API，并使验证器的 `in` 与实际读取一致。
- 路由正则限制 URL 形状，参数类型检查值能否转换；两者都不代表字段格式完整、允许写入或访问该资源的授权。
- 参数名不应用于接收当前用户、租户、权限范围或可信资源归属；这些由认证/授权上下文决定。
- 类/模型实例也可被参数绑定，但公共写接口不要因此把未经白名单过滤的输入直接持久化。复杂模型写入读 [data-recipes.md](data-recipes.md)。

## `#[Validate]` 与 `#[Param]`：补足规则，不替代绑定

仅在 `webman/validation` 已安装时使用 `support\validation\annotation\Param`、`Validate`。控制器参数绑定负责取得值与 PHP 类型转换；验证器负责接受/拒绝输入。它们配合使用，而不是二选一。

- 多字段的普通动作优先在方法上写一个 `#[Validate]`，使规则、数据来源与消息集中可见；只有单个参数确实需要不同的来源、消息或属性名时再用 `#[Param]`。
- 方法带 `#[Validate]`，或任一参数带 `#[Param]` 时，组件会依据参数签名推导基础规则：非可选 `string $name` 推导 `required|string`，`int $id` 推导 `required|integer`；默认值与 nullable 类型会推导为非必填/nullable 的相应规则。
- 推导不覆盖业务要求。邮箱、长度、枚举、金额范围、跨字段关系、文件、唯一性和场景仍须通过 `#[Param(rules: ...)]`、`#[Validate(...)]` 或复用 Validator 明确描述。
- 多字段或场景复用时，例如 `#[Validate(validator: UserValidator::class, scene: 'create')]`；它的规则来源、白名单和失败响应读 [validation-recipes.md](validation-recipes.md)。不要在未安装组件的项目中写这些 Attribute。
- `Param(in: ...)` / `Validate(in: ...)` 只影响验证器取数；组件验证结束后原样调用控制器，不会把该值注入或覆盖控制器实参。
- 直接参数绑定又希望加 `Param` 时，校验来源必须模拟绑定优先级：用 `['body', 'query', 'path']`（后者覆盖前者）对应“路由 > GET > POST”。若接口规定 body-only 或 query-only，则不要直接绑定同名字段，改用 `$request->post()` / `$request->get()`。

## 方法、路径与默认路由

- 无路径 `#[Get]`、`#[Post]` 仅限制默认路由动作允许的方法；未声明方法返回 405。它们不定义一个新 URL。
- 带路径的 `#[Get('/users/{id:\d+}')]`、`#[Post('/users')]` 定义 Attribute 路由；`#[RouteGroup('/api/v1')]` 加共同前缀；稳定 URL 才添加路由名。
- 新接口选定 **Attribute 路由** 或 **`config/route.php` 显式路由**其中一个来源；不要为同一公开入口两边各注册一次。
- 使用带路径 Attribute 路由时，评估 `#[DisableDefaultRoute]` 或 `Route::disableDefaultRoute(...)`，防止默认路由仍公开控制器动作。迁移旧项目先盘点调用，不能一概关闭。
- 浏览器跨域预检不是业务动作的 OPTIONS。CORS 使用统一中间件处理；不要为每个 `#[Post]` 都写 `#[Options]`。具体配置读 [routing-and-middleware.md](routing-and-middleware.md)。

## 注入 `Request`：只用于普通绑定不适合的数据

当需要读取 Header、Cookie、客户端地址、文件、全部输入、raw JSON/body 或当前路由信息时，在动作首个参数声明 `support\Request`：

```php
use support\Request;
use support\Response;

public function upload(Request $request): Response
{
    $file = $request->file('avatar');
    $traceId = $request->header('x-request-id');

    // 先检查文件、白名单与业务权限；随后再移动/存储。
    return json(['trace_id' => $traceId]);
}
```

- 请求对象只属于本次调用；不保存到控制器属性、静态变量、单例或后台回调。
- 上传文件和 JSON/raw body 不属于“参数绑定就够用”的场景。文件校验、MIME、随机存储名和公开访问边界读 [http-recipes.md](http-recipes.md) 与 [validation-recipes.md](validation-recipes.md)。
- 需要少数普通字段时，优先参数绑定；只有调用特定 Request API 才注入 Request，避免把每个动作写成宽泛的 `$request->all()`。

## 控制器/方法中间件与授权

`#[Middleware(AuthMiddleware::class)]` 可加在类上或动作上。类级适合共同认证，方法级适合端点特有的限流、审计或权限补充；控制器仍需执行资源级授权，例如确认当前用户可访问参数 `$id`。

不要在控制器内散落重复登录判断，也不要把认证结果写到可复用控制器属性。中间件顺序、全局/应用/路由范围、CORS 与 404 边界读 [routing-and-middleware.md](routing-and-middleware.md)。

## 响应、生命周期与 CRUD 生成

- 动作只 `return` 字符串、数字或 `support\Response`；JSON、下载、重定向、状态码和错误响应沿用项目契约，详见 [http-recipes.md](http-recipes.md) 与 [application-services.md](application-services.md)。
- 控制器可能被复用。无论 `controller_reuse` 当前值如何，都不在属性保存本次 Request、用户、模型、查询条件、事务或上传文件；运行时细节读 [core-runtime.md](core-runtime.md)。
- 已安装 `webman/console` 且用户授权新增/覆盖文件时，`make:controller` 可生成控制器骨架；普通后台 CRUD 可考虑 `make:crud --table=...`，它可生成模型、控制器和（已安装 `webman/validation` 时）验证器。生成物不理解路由、字段白名单、授权、租户、事务或 API 契约，必须逐项收敛，详情读 [console-generators.md](console-generators.md)。

## 动作完成前核对

- 路径和方法是否唯一且符合项目选定的路由方式；默认路由是否意外暴露？
- 参数来源、类型、默认值、验证规则和允许写字段是否一致？
- 是否完成认证、角色/权限与资源级授权，而不只是验证 `id` 存在？
- CORS 预检、错误方法、未授权、未命中路由及正常响应是否按项目约定验证？
- 若改变了 Attribute 路由、中间件或控制器代码，是否说明 reload/restart 边界？
