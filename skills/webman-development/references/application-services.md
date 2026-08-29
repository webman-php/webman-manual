# Webman 应用服务：配置、异常与日志

仅在读取或修改配置、设计统一错误响应、抛出业务异常、记录业务日志、处理 Session、渲染视图、提供静态文件或排查框架日志时读取本文件。先检查项目的 `config/`、已有 Handler、日志 channel、响应协议、Session 存储、视图引擎、静态资源路径和多应用结构；不要为新接口另造一套错误格式、日志框架、会话方案或模板引擎。

## 配置：进程启动后只读

配置文件位于 `config/`，通过 `config()` 按点号读取。配置是进程启动时加载的运行时状态，不是每个请求都重新读取的动态键值库；不能把它当作开关、计数器、当前用户或请求级数据的存储位置。

```php
// config/payment.php 返回的数组
$gateway = config('payment.gateway', 'none');
$timeout = config('payment.timeout_seconds', 3);
```

- 新增项目配置时创建 `config/<name>.php` 并返回数组，使用固定的 `config('<name>.<key>')` 读取。密钥、密码和不同环境值沿用项目已有的环境变量或机密注入机制，不能写进控制器、日志或示例代码。
- 在 `config/<directory>/` 下拆分配置时，当前 Webman 规则要求该目录有 `app.php` 且 `enable` 为 `true`，例如读取 `config/order/status.php` 用 `config('order.status')`。这是目录加载开关，不是业务开关；业务功能开关应放在明确的配置键中。
- 普通 PHP 配置和业务代码变更按 [core-runtime.md](core-runtime.md) 的实际运行方式 **reload**；`config/process.php`、旧版 `config/server.php`、监听地址、worker 数或事件循环变更必须 **restart**。不要在运行中的 PHP 进程内试图修改 `config()` 返回值来更新全局配置。
- 配置键、默认值和环境覆盖方式是项目契约。先搜索调用点与部署配置，保留旧键或给出兼容迁移，不能仅因名称更好看而重命名。

## 统一异常响应

默认映射在 `config/exception.php`：空字符串键对应默认应用 Handler。目标项目使用多应用时才按应用名配置各自 Handler；每个应用只应有一个生效的异常处理器。处理器必须实现 `Webman\Exception\ExceptionHandlerInterface` 的 `report(Throwable $exception)` 与 `render(Request $request, Throwable $exception): Response`。

```php
<?php

namespace app\exception;

use Throwable;
use Webman\Http\Request;
use Webman\Http\Response;
use support\exception\Handler as BaseHandler;

final class ApiHandler extends BaseHandler
{
    public function render(Request $request, Throwable $exception): Response
    {
        // 先让项目既有的业务异常/专用异常自行渲染。
        if (method_exists($exception, 'render') && ($response = $exception->render($request))) {
            return $response;
        }

        // 意外异常不向客户端泄露 message、trace、SQL 或机密。
        return json(['code' => 'INTERNAL_ERROR', 'message' => 'Server internal error']);
    }
}
```

- 优先继承项目已有 Handler 或 `support\exception\Handler`，只修改需要的 `report()` 或 `render()` 分支。直接从零实现时，必须保留意外异常的记录、`app.debug` 的泄露边界，以及异常自身 `render()` 的既有语义。
- 不要在每个控制器捕获 `\Throwable` 后静默返回 JSON；这会丢失日志、错误上下文和统一处理。预期的业务失败抛业务异常，意外失败让 Handler 记录并渲染。
- 生产环境 `app.debug` 必须关闭。错误响应不应包含堆栈、SQL、文件路径、访问令牌、Cookie、密码或内部服务地址；这些信息只进入受控日志。修改 debug 值同样需要按配置边界 reload/restart 后确认实际生效。
- `json()` 默认 HTTP 状态是 200。若当前 API 协议需要 4xx/5xx HTTP 状态，Handler 或异常的 `render()` 必须显式构造对应状态的 `Response`；不能只把 `code` 字段改为 422/500 就假定 HTTP 状态也改变。状态码与响应 body 格式应遵循项目既有客户端契约。

## 业务异常：只表达可预期失败

对于校验、状态冲突、权限拒绝等调用方可预期的失败，项目已使用 `support\exception\BusinessException` 时可直接抛出，让框架的异常链处理；不需要在业务层再捕获一次。

```php
use support\exception\BusinessException;

if (!$canEdit) {
    throw new BusinessException('无权修改该资源', 40301);
}
```

- 默认 BusinessException 的 JSON body 使用业务 `code` 和 `msg`，但 HTTP 状态仍可能是 200；是否改成 HTTP 4xx 由当前项目统一 Handler 或自定义业务异常决定，不能在新接口中单独改变。
- BusinessException 在默认 Handler 的 `dontReport` 列表中，通常不记录为系统故障。不要把数据库断连、第三方超时、程序错误或安全异常包装为业务异常来“消除日志”。
- 需要稳定业务错误码时，沿用项目已有枚举/错误码表与国际化方式；消息面向客户端且不能拼入未脱敏的异常消息或用户敏感数据。

## 日志：可检索、可脱敏、有责任边界

Webman 的 `support\Log` 基于项目配置的 Monolog channel。默认 channel 可直接调用；额外 channel 必须先存在于 `config/log.php`，避免在请求中临时创建文件或处理器。

```php
use support\Log;

Log::info('order.created', [
    'order_id' => $orderId,
    'tenant_id' => $tenantId,
    'request_id' => $request->header('x-request-id'),
]);

Log::channel('payment')->warning('payment.callback_rejected', [
    'event_id' => $eventId,
    'reason' => 'signature_invalid',
]);
```

- 使用稳定、可检索的事件名和结构化 context。context 只记录诊断所需的标识、状态、耗时和已脱敏字段；不得记录密码、Authorization、Cookie、完整支付数据、上传文件内容或原始个人信息。
- `debug()` 适合受控开发诊断，`info()` 记录正常关键事件，`warning()` 表示可恢复异常，`error()`/更高级别表示需处理的故障。不要用高频成功路径逐条 `info()` 记录完整请求体，也不要为了“方便排查”把所有异常降成 debug。
- channel 的 handler、formatter、保留期与写入目标在 `config/log.php`。修改后按配置边界 reload；运行目录、容器文件系统或外部日志收集器不可写时，应修复部署配置或降级策略，不能用 `echo` 代替业务日志。
- 框架 stdout、Workerman 错误日志和应用日志可能位于不同位置，以 `config/server.php`、`config/log.php`、实际 `runtime_path()` 与运行环境为准。排障时先带 request/order/job 等关联 ID 搜索，不要只凭时间猜测。

## Session：请求绑定的认证状态

Session 通过 `$request->session()` 取得，或在 HTTP 请求上下文中用 `session()` 助手函数。它是当前请求的对象，不是常驻进程的全局服务；不要把 Session 实例、Session ID、当前用户或其可变内容保存到静态变量、单例、定时器或自定义常驻进程。

```php
$session = $request->session();

$userId = $session->get('auth.user_id');
$session->put([
    'auth.user_id' => $userId,
    'auth.login_at' => time(),
]);

// 退出登录或失效认证时，清除整个会话。
$session->flush();
```

- 高频接口为 `get($key, $default)`、`set()`、`put()`、`forget()`、`pull()`、`has()`、`exists()`、`all()` 与 `flush()`。`has()` 把值为 `null` 视为不存在，`exists()` 不会；不要用它们替代明确的登录态或权限判断。
- 只存放少量、可序列化的会话数据，例如用户 ID、认证时间和一次性提示。认证权限、余额、购物车最终状态等仍以数据库或专用服务为事实来源；不要存对象实例或不可信反序列化内容。
- Session 对象在请求结束销毁时自动保存。保存它到全局或长生命周期对象会阻止这一生命周期，可能导致未保存或跨请求污染；遇到需要手动 `save()` 的代码，先审查是否可以恢复为请求内局部变量。
- 队列消费者、定时任务、CLI 和没有 HTTP Request 的自定义进程没有可用的浏览器 Session。此类任务必须通过显式参数、持久化数据或项目已有认证机制取得身份上下文，不能调用 `session()` 期待得到当前用户。

### 存储、过期和 Cookie 边界

会话设置在 `config/session.php`，启动时由 Webman 绑定到 Workerman 的 Session handler。先查看当前 `handler`、`type`、`config`、`session_name`、`lifetime` 与 cookie 属性，不能只看控制器代码判断跨进程行为。

| 方案 | 适用边界 | 不应假定 |
|---|---|---|
| `FileSessionHandler` | 单机、共享运行目录的多个 worker | 默认本地 `runtime/sessions` 不会跨服务器或容器实例。 |
| `RedisSessionHandler` | 多 worker、多实例需要共享登录态 | 它使用 `config/session.php` 的 Redis handler 配置；不等同于业务 `support\Redis` 连接或 `config/redis.php`。 |
| `RedisClusterSessionHandler` | 项目已有 Redis Cluster 会话配置 | 不能仅因业务 Redis 是集群就自行切换 handler。 |

- 多机负载均衡、容器弹性扩缩或无黏性会话时，使用项目已验证的共享 Session 存储；不能依赖文件 Session 或负载均衡“恰好”把请求分到同一台机器。切换 handler、key 前缀或 Session ID cookie 名会使既有登录态失效，必须按发布计划完成。
- `lifetime` 管 Session 数据过期，`cookie_lifetime` 管浏览器保存 Session ID 的时间，二者需共同满足认证策略。`auto_update_timestamp` 是否滑动续期、GC 概率和 Redis TTL 的实际行为应按锁定 Workerman 版本和 handler 验证，不能只把 Cookie 延长就认为服务端会话仍有效。
- 生产 HTTPS 使用 `secure => true`；认证 Cookie 保持 `http_only => true`。为相同站点和跨站嵌入分别明确 `same_site` 策略：跨站 Cookie 使用 `none` 时，浏览器要求同时设置 Secure，且仍需项目的 CSRF 防护；不要为解决前端跨域问题直接放宽所有 Cookie。
- 登录、登出、修改权限或切换租户时，遵循项目已有的 Session 清理和 Session ID 轮换流程。目标版本未确认相关 API 时先查已安装 Workerman 源码或官方文档，不能照搬 PHP 原生或 Laravel 的 `regenerate()` 调用。

### 验证边界

- 修改会话配置或认证逻辑后，在受控环境验证：登录后的 Set-Cookie 属性、后续请求读取、登出后的失效、过期行为，以及两个 worker/实例之间的共享需求。单 worker 的手工请求不能证明 Redis、负载均衡或跨域 Cookie 正确。
- `config/session.php` 在 worker 启动时应用。普通配置调整按 [core-runtime.md](core-runtime.md) 的实际方式 reload；新增自定义 handler 的 Composer 依赖、或同次修改进程配置时使用 restart，并确认旧 Session 的迁移/失效策略。

## 视图：先识别引擎，再选择转义语法

先检查 `config/view.php`、`composer.lock`、现有模板后缀和 `app/view/`（多应用则 `app/<name>/view/`）。当前项目可能使用原生 PHP `support\view\Raw`、Twig、Blade 或 Think 模板；模板语法、自动转义、缓存和扩展都由实际 handler 决定，不能混写或为了一个页面安装新的引擎。

```php
return view('account/profile', [
    'displayName' => $user->display_name,
    'canEdit' => $canEdit,
]);
```

- `view()` 返回 `Response`。相对模板名按当前应用的 view 目录解析；省略模板名只适用于类控制器的约定路径。闭包路由和多应用的解析规则不同，路径不明确时写出固定模板名或使用当前 API 指定应用，不能依赖猜测。
- 模板名必须由代码常量或白名单映射决定；不要把路由参数、查询参数、文件名或用户输入直接传给 `view()`，尤其不要生成以 `/` 开头的绝对模板路径。
- 原生 PHP 模板没有自动 HTML 转义。输出文本、属性或 URL 参数时使用与上下文匹配的转义；普通 HTML 文本可用 `htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8')`。Twig、Blade、Think 模板也应使用当前引擎的默认转义表达式；关闭自动转义或输出 raw HTML 只接受经可信来源或严格净化后的内容。
- 优先通过每次 `view($template, $data)` 传递页面数据。当前版本的 `View::assign()` 依附请求对象，可用于中间件提供的少量共享展示数据；仍不能把可变用户数据塞入自行维护的静态全局变量，也不应让共享变量覆盖页面显式传参。
- Blade/Think 等引擎可能在 `runtime/` 写入编译缓存。确保运行目录可写、非 public 暴露，并在发布后按项目已有缓存/重载流程处理；不要在每个请求清理模板缓存。安装模板依赖或切换 `view.handler` 会改变 Composer/配置，应取得授权并按 restart 边界处理。

## 静态文件：public 是唯一公开文件边界

静态资源路径、是否由 Webman 提供和静态中间件由 `config/static.php` 与实际 `public_path()` 决定。先检查 `static.enable`、`static.middleware`、`app.public_path` 与前置服务器配置；不要假定任一版本默认开启静态服务。

- Web 根目录只能指向项目 `public/`（或项目实际配置的公开目录），绝不能是项目根目录。`config/`、`vendor/`、`runtime/`、迁移、`.env`、日志和私密上传文件不能放在可直接下载的位置。
- 只把设计为公开访问的编译资源、图像或下载文件放入 public。需要鉴权、临时访问、原始上传或含个人信息的文件保存在 public 外，通过受控下载响应、签名 URL 或当前项目已有文件服务处理。
- 不在 public 中放置可执行 PHP 文件，也不要开启 `app.support_php_files` 来“方便访问脚本”。前置服务器同样应拒绝 public 内 `.php` 和点文件请求，避免框架与代理配置任一侧失误后泄露或执行文件。
- Webman 直接服务静态文件时，`config/static.php` 的 middleware 可增加特定响应头或拒绝路径；生产 Nginx/CDN 直接命中静态文件时这些 Webman middleware 不会执行。CORS、缓存、点文件限制和访问控制必须配置在真正返回文件的那一层，不能只改另一层。
- 修改公开目录优先使用当前项目支持的配置方式（当前框架读取 `app.public_path`）；不要改 `vendor/` 或框架 `support/helpers.php` 来改变路径。资源指纹、缓存时间和 CDN 路径沿用项目构建与发布约定，不能让用户可变文件获得长期不可失效缓存。

### 生产代理边界

- 生产通常由 Nginx/CDN 处理 TLS、静态资源和连接边界，再将动态请求代理到 Webman。代理的 `root` 必须是 public 目录；只有找不到静态文件时才转发到 Webman，且应传递 Host、客户端地址链和原始协议等必要 Header。
- 反向代理、真实客户端 IP 与 HTTPS 终止会影响 CORS、Secure Cookie、重定向和审计日志。修改代理规则前先确认应用实际信任的 Header 和 CDN/LB 拓扑，不能仅复制一份 Nginx 示例或让客户端伪造 `X-Forwarded-*`。
- 代理配置、CDN 行为和网络端口属于部署改动。确认实际静态文件、动态路由、404、重定向、上传大小限制与 WebSocket（如有）都经过目标入口后再发布；只访问 Webman 内网端口不能证明外网配置正确。

## 验证边界

- 修改配置或 Handler 后，至少在受控环境验证一次：目标配置键可读取、预期业务异常的 HTTP 状态/body、意外异常的日志与脱敏响应、以及指定 channel 的写入。PHP 语法检查不能证明 Handler 已被框架实例化或日志目录可写。
- 不在真实生产流量中用故意抛出的未处理异常验证错误页。若必须发布错误响应变更，先用测试环境或受控请求，并保留可回退的响应契约。
- 请求验证见 [validation-recipes.md](validation-recipes.md)。翻译、Env 与依赖注入不在本次展开；只有当前任务确实需要时，依据项目已安装组件和目标版本 Webman 官方文档补充，不要从本页的 API 推断它们的用法。
