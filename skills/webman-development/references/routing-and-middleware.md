# Webman 路由、控制器与中间件

仅在新增或调整 `config/route.php` 显式路由、默认路由、404、路由参数、CORS 或中间件时读取本文件。控制器动作、参数绑定、HTTP 方法 Attribute、验证注解与 Request 注入先读 [controller-recipes.md](controller-recipes.md)。先检查目标项目的 `config/route.php`、`config/middleware.php`、多应用目录及 `config/app.php`；路由和中间件是应用的公开入口与安全边界，不能只按其他 PHP 框架的习惯补一段代码。

## 先确认公开入口

- `app/controller/` 及其子目录只放控制器。控制器目录中的其它类可能在默认路由下被意外定位；优先在 `config/app.php` 启用并沿用 `controller_suffix => 'Controller'`。
- 查清项目是否仍启用默认路由、是否已有 API 前缀、统一错误格式、鉴权方案和多应用约定。不要为了新增一个接口而混入第二套路由或鉴权风格。
- 新项目或安全敏感 API 优先使用显式路由，并决定是否关闭默认路由；既有项目则先评估关闭默认路由会不会破坏已有入口。
- 路由、控制器和中间件配置属于运行时启动/加载内容。完成修改后按 [core-runtime.md](core-runtime.md) 中项目实际命令说明 reload 或 restart 边界。

## 控制器动作

控制器动作的参数绑定、类型和默认值、`#[Get]` / `#[Post]`、Attribute 路由、`#[DisableDefaultRoute]`、`Request` 注入、`#[Param]` / `#[Validate]`、Attribute 中间件、返回值、控制器复用与 `make:crud`，统一改读 [controller-recipes.md](controller-recipes.md)。本页只保留路由注册、CORS、404 和中间件范围等入口层细节。

## CORS 与统一 OPTIONS 预检

浏览器的跨域 `POST`、非简单请求 Header 等请求可能先发送 `OPTIONS` 预检；它不是业务 `POST`。若控制器动作只声明 `#[Post]` 或路由只注册 `Route::post()`，预检到达该动作会是 405，浏览器不会继续发送真实请求。因此 CORS 必须在控制器动作之前统一处理，不能靠每个业务动作单独添加 `#[Options]`。

首选做法是在 `config/middleware.php` 的正确范围注册**全局 CORS 中间件**，并让它位于认证/鉴权中间件之前：

```php
return [
    '' => [
        app\middleware\CorsMiddleware::class, // 必须在 AuthMiddleware 前面
        app\middleware\AuthMiddleware::class,
    ],
];
```

1. 从部署配置读取受信任 Origin、允许方法、允许请求 Header、暴露响应 Header，以及是否真的需要 Cookie 凭证；不要从请求直接生成策略。
2. 对不在 Origin 白名单中的预检请求直接拒绝；对合法预检直接返回 `204`，**不要调用** `$handler($request)`，使其不会进入路由、方法 Attribute、认证或控制器。
3. 对合法跨域实际请求调用 `$handler($request)`，然后在其正常响应和错误响应上附加同一份 CORS 响应 Header。没有 `Origin` 的同源/服务端请求照常通过。
4. 允许凭证时，`Access-Control-Allow-Origin` 必须是已经验证的具体 Origin，并仅在确实使用 Cookie 等凭证时发送 `Access-Control-Allow-Credentials: true`。`Access-Control-Allow-Methods` 和 `Access-Control-Allow-Headers` 使用固定白名单（例如 `GET, POST, PUT, PATCH, DELETE` 与 `Authorization, Content-Type, X-Request-Id`），不回显任意请求值；按 Origin 动态返回时加入 `Vary: Origin`。

```php
<?php
namespace app\middleware;

use Webman\Http\Request;
use Webman\Http\Response;
use Webman\MiddlewareInterface;

final class CorsMiddleware implements MiddlewareInterface
{
    // 实际项目应从受版本管理的部署配置读取，不要接受客户端传入的 Origin。
    private const ALLOWED_ORIGINS = ['https://admin.example.com'];

    public function process(Request $request, callable $handler): Response
    {
        $origin = $request->header('origin');
        $allowed = $origin !== null && in_array($origin, self::ALLOWED_ORIGINS, true);

        // 只有带 Origin 的 OPTIONS 才是此 CORS 策略应短路的浏览器预检。
        if ($request->method() === 'OPTIONS' && $origin !== null) {
            return $allowed
                ? $this->withCors(response('', 204), $origin)
                : response('', 403);
        }

        $response = $handler($request);
        return $allowed ? $this->withCors($response, $origin) : $response;
    }

    private function withCors(Response $response, string $origin): Response
    {
        $response->withHeaders([
            'Access-Control-Allow-Origin' => $origin,
            'Access-Control-Allow-Methods' => 'GET, POST, PUT, PATCH, DELETE',
            'Access-Control-Allow-Headers' => 'Authorization, Content-Type, X-Request-Id',
            'Access-Control-Expose-Headers' => 'X-Request-Id',
            'Vary' => 'Origin',
        ]);

        return $response;
    }
}
```

- 上例是无 Cookie 凭证的 API 策略；若明确采用跨站 Cookie，会在已验证具体 Origin 的前提下额外发送 `Access-Control-Allow-Credentials: true`，并另行落实 Cookie 的 `SameSite`/`Secure` 与 CSRF 防护。CORS 不是认证或防止请求副作用的机制。
- `config/middleware.php` 的 `''` 只覆盖主项目；多应用应放到对应应用键。需要覆盖主项目与插件时才评估 `@` 超全局范围，不要因 CORS 方便而意外扩大影响面。
- 若响应已有 `Vary`，应合并 `Origin` 而非覆盖现有取值；上例为独立、最小策略，接入项目时必须与既有响应 Header 约定合并。
- 默认 404 不经过普通中间件。若 API 要让未命中路由也返回 CORS Header，定义项目约定的 `Route::fallback(...)`，并明确把同一 CORS 中间件挂到该 fallback；不要假定全局注册会覆盖默认 404。
- 若既有架构的 CORS 中间件只能给响应添加 Header、不能统一短路预检，可在 `config/route.php` 注册一次 `Route::options('[{path:.+}]', static fn () => response('', 204));`，并确保该响应仍经过同一 CORS Header 策略。它是**替代方案**：已由全局 CORS 中间件短路 OPTIONS 时，不再重复添加此 catch-all 路由。
- 开启或改动 CORS 后，至少分别验证允许 Origin 的预检、允许 Origin 的真实请求、拒绝 Origin、带自定义 Header 的预检、未命中路由和需要凭证的请求；仅用 Postman/curl 不能证明浏览器凭证与 CORS 行为正确。

## 显式路由与路径参数

路由定义写在项目约定的路由配置文件中，路径必须以 `/` 开头。优先按真实 HTTP 语义限定方法，除非确有必要，不要用 `Route::any()` 放宽一个写操作的请求方法。

```php
<?php
use app\controller\UserController;
use app\middleware\AuthMiddleware;
use Webman\Route;

Route::get('/api/users/{id:\d+}', [UserController::class, 'show'])
    ->name('users.show')
    ->middleware([AuthMiddleware::class]);

Route::post('/api/users', [UserController::class, 'store'])
    ->middleware([AuthMiddleware::class]);
```

- `{id}` 匹配单个路径段；`{id:\d+}` 用正则限制匹配；`/users[/{name}]` 表示可选路径段。捕获任意后缀的写法应非常谨慎，避免吞掉更具体的路由。
- 路由参数只限制路径形状，不代表该用户有权访问该 `id`。在控制器或统一授权层根据当前身份再校验资源归属。
- 仅当路由稳定且确实需要反向生成 URL 时命名路由，例如 `route('users.show', ['id' => 42])`。涉及嵌套路由组的 URL 生成时先在目标版本验证，不要依赖手册快照外的行为。
- `Route::resource()` 适合严格遵循 index/show/store/update/destroy 等 REST 约定的控制器；它会注册一组公开入口。权限、动作或方法不标准时，用显式路由更清晰，并只列出必要动作。

## 路由组与默认路由

```php
use app\controller\UserController;
use app\middleware\AuthMiddleware;
use Webman\Route;

Route::group('/api/v1', function () {
    Route::get('/users/{id:\d+}', [UserController::class, 'show'])
        ->name('api.users.show');
})->middleware([AuthMiddleware::class]);
```

- 路由组用于共享路径前缀和中间件；同一层的中间件按声明顺序执行。嵌套路由组的中间件继承在历史版本中有差异：目标项目未确认版本时，把中间件挂在直接目标组，并用实际路由或 HTTP 测试验证覆盖范围。
- 默认路由通常会把 `/controller/action` 解析到控制器的公有动作。对于仅应由显式 API 路由访问的应用，可在确认影响范围后使用 `Route::disableDefaultRoute()`；也可按控制器、动作、应用或插件缩小禁用范围。

## Attribute 路由与控制器动作

Attribute 路由的路径/方法、`RouteGroup`、路由名、`DisableDefaultRoute`、参数绑定与 `#[Middleware(...)]` 的完整组合，改读 [controller-recipes.md](controller-recipes.md)。本页的 `config/route.php` 示例仅适用于项目选择显式路由时；同一公开入口不要两边重复注册。

## 404 与路由诊断

显式路由不匹配时，`$request->route` 为 `null`；默认路由同样没有路由对象。所有读取路由信息的中间件必须先判空。

```php
use Webman\Route;

Route::fallback(static function () {
    return response('Not Found', 404);
});
```

- API 的 404 应遵循项目既有 JSON 错误格式；自定义状态码 JSON 的写法见 [http-recipes.md](http-recipes.md)。
- 默认 404 不经过普通中间件。需要记录、统一错误格式或跨域处理时，把中间件明确挂到 `Route::fallback(...)->middleware([...])`。
- 路由冲突、错误方法（405）、前缀拼接和中间件遗漏应通过目标项目的路由列表能力或真实 HTTP 请求验证；不要只看 `config/route.php` 的文本顺序推断结果。

## 编写中间件

中间件实现 `Webman\MiddlewareInterface`。调用 `$handler($request)` 前是请求阶段，调用后是响应阶段；若直接返回 `Response`，请求会被短路，不会进入控制器。

```php
<?php
namespace app\middleware;

use Webman\Http\Request;
use Webman\Http\Response;
use Webman\MiddlewareInterface;

class AuthMiddleware implements MiddlewareInterface
{
    public function process(Request $request, callable $handler): Response
    {
        // 示例采用会话；令牌、SSO 等项目应调用其既有认证服务。
        $user = session('user');
        if ($user === null) {
            return response('Unauthorized', 401);
        }

        // Request 是本次请求对象；使用语义明确且不会冲突的名称。
        $request->authUser = $user;

        return $handler($request);
    }
}
```

- 中间件实例、容器对象或配置中直接创建的对象都可能跨请求存活。构造函数和对象属性只能保存无请求状态的配置或可安全复用的依赖；当前用户、认证结果、计时器、事务和响应对象应留在 `process()` 的局部变量或当前 Request 上。
- 中间件可把本次请求已验证的上下文写入 `$request->authUser` 这类专用属性，再由控制器读取。避免通用名如 `data`，不要把此上下文写进静态变量、单例或控制器属性。
- 不在中间件中 `echo`、`var_dump` 或吞掉异常。需要观测异常时按项目日志和异常处理约定记录；响应上的异常信息只用于诊断，不能把内部异常文本直接回显给客户端。

## 注册范围与顺序

| 范围 | 入口 | 适用场景 |
|---|---|---|
| 主项目全局 | `config/middleware.php` 的 `''` | 仅主项目所有请求都必须执行的逻辑。 |
| 多应用 | `config/middleware.php` 的应用名键 | 仅多应用模式中特定应用。 |
| 控制器/方法 | 已确认支持的 `#[Middleware(...)]` 或项目既有方式 | 对整个控制器或单个动作的规则。 |
| 路由/路由组 | `->middleware([...])` | API 前缀、少量端点或明确的接口组。 |
| 超全局 `@` | 中间件配置中的 `@` | 会波及主项目与插件；仅在确有全局插件治理需求时使用。 |

请求进入时，常规顺序为：**全局 → 应用 → 控制器 → 路由 → 方法 → 控制器动作**；响应按反向穿出。同层多个中间件以实际配置顺序为准。闭包路由没有控制器/方法中间件层。

构造函数传参可使用中间件实例，例如 `new RateLimitMiddleware($policy)`，但该实例仍必须无请求状态。静态路由配置可用 `->setParams(['policy' => 'admin'])` 传递固定参数；中间件读取 `$request->route?->param('policy')` 前必须处理默认路由或 404 时没有 route 的情况。不要把密钥、会变化的用户数据或外部输入塞进路由静态参数。

## 安全核对清单

- 写接口使用 `POST`、`PUT`、`PATCH` 或 `DELETE` 等明确方法，并同时落实认证、角色/权限和对象级授权；仅有路由前缀或登录检查并不足够。
- 路由正则、控制器参数类型、输入校验和数据库白名单各负责不同边界，不能互相替代。
- 不让默认路由、资源路由或宽泛的 catch-all 路由意外暴露内部控制器动作。
- 修改鉴权、中间件顺序、CORS、默认路由或 404 时，至少做已授权、未授权、错误方法、未命中路由和预检请求的真实 HTTP 验证。

## 未覆盖的 API

本页只保留高频路由与中间件决策。多路由文件、多应用/插件专属路由、路由对象完整 API、属性的组合规则或版本兼容细节，先检查目标项目锁定版本的源码和官方路由/中间件手册；插件任务改读后续 `plugin-development.md`，不要把主应用路径规则套到插件上。
