# Webman 协程：按进程启用与资源隔离

仅在审查或启用协程、修改 `eventLoop`、使用 `Context`、`Parallel`、连接池，或排查并发状态污染时读取本文件。协程不是 Webman 的默认运行方式；先证明目标 **进程** 已实际启用兼容驱动，再写协程专用代码。普通 Webman 常驻 worker 的生命周期规则仍然成立。

## 先确认驱动、版本和配置位置

按以下顺序检查，不要把当前手册的安装命令直接套进已有项目：

1. 从 `composer.lock` 确认 PHP、`workerman/workerman`、`workerman/webman-framework` 版本，以及是否已有 Swoole、Swow 或 Fiber 所需事件循环依赖。
2. 检查运行环境实际扩展和现有 process 定义。当前项目可能在 `config/process.php` 的每项使用 `eventLoop`，也可能是旧版 `config/server.php` 的 `event_loop`；字段位置和命名以已安装框架源码及项目配置为准。
3. 确认目标请求究竟进入哪个 worker。只有该 worker 配置为 `Workerman\Events\Swoole::class`、`Workerman\Events\Swow::class` 或 `Workerman\Events\Fiber::class` 等兼容事件循环时，才能按协程语义推理。

协程配置、扩展或 Composer 依赖变化都要求 **restart**，不是 reload。没有明确的性能瓶颈、I/O 类型、库兼容性和部署授权时，不要为普通接口自行安装扩展、替换事件循环或新增协程端口。

| 情况 | 正确决策 |
|---|---|
| 未确认协程驱动，或 `eventLoop` 为空/普通事件循环 | 按普通 Workerman worker 编码；不假设阻塞 I/O 会让出执行权。 |
| Swoole 或 Swow 已在目标进程启用 | 阻塞 I/O 可能让协程切换；仍需处理请求状态隔离、资源竞争、库兼容性和并发上限。 |
| Fiber 已启用 | 可以使用协程 API，但普通阻塞 I/O 不会自动切换，阻塞仍会占住 worker。 |
| 只是一小部分慢 I/O 路由需要协程 | 只有在现有代理、端口和监控允许时，才为指定 process 启用并由受控路由导入；不要改变全部 HTTP 流量的运行模型。 |

Swow 会 hook PHP 的阻塞函数，即使未在业务中显式创建协程也可能改变扩展或库的行为。项目不使用 Swow 时不要因“可能更快”而安装它；项目已经使用时，应明确将相应进程配置为 Swow 驱动，而不是让环境和配置不一致。

## 按进程配置，不复制整份 server 定义

在当前 `config/process.php` 的既有 worker 项中，才可能出现类似配置；保留原有 `handler`、`listen`、`count`、`context` 和 constructor，不能用文档示例替换完整配置。

```php
// 仅示意已有 process 项中的协程开关；修改后 restart。
'api-io' => [
    // ... 保留项目已有的 handler、listen、count 等字段
    'eventLoop' => Workerman\Events\Swoole::class,
],
```

- 每个 process 可选择不同事件循环。协程与非协程进程可以并存，但若因此新增监听端口、Nginx upstream 或路由前缀，须同时设计认证、超时、限流、上传限制、健康检查与真实入口验证。
- 协程不会合并多个 OS worker 的内存；`count`、容器副本和服务器数仍会独立初始化资源。它提升的是一个进程内可交错执行 I/O 的能力，不是自动的跨进程扩容。
- 不要仅把 `count` 降为 1 来“避免并发”。这既不能保证集群唯一，也会把吞吐和故障隔离问题隐藏起来。

## 请求状态：局部变量优先，跨调用用 Context

局部变量天然属于当前函数调用，最简单也最安全。只有中间件、服务层或异步回调需要在同一请求链路中读取少量请求状态时，才使用 `support\Context`；不要把它当作跨请求缓存、后台任务载体或进程间通信。

```php
use support\Context;

// 鉴权中间件或应用服务写入最小、非敏感的请求关联信息。
Context::set('request.actor_id', $actorId);

// 同一请求/协程链路中的下游代码读取。
$actorId = Context::get('request.actor_id');
```

- `Context` 在协程模式按协程隔离，在非协程模式也会在请求结束时清理。键名应有命名空间，值应限于当前请求必要的 ID、追踪信息或明确短生命周期对象。
- 不把当前用户、租户、Request、Response、Session、事务、上传对象或可变数组写入全局变量、静态变量、单例、容器缓存或可复用控制器属性。协程发生切换后，这些位置会被其它请求看到。
- 全局配置、无请求状态的服务定义和由**协程安全连接池**管理的共享客户端可以长期存在；“可共享”不等于能让多个协程同时操作同一条活动连接或同一个可变句柄。

## 连接、事务与其它可变资源

以下对象只要可能在一次 I/O 让出后被另一协程访问，就视为不可并发共享：一条正在使用的数据库/Redis 连接、进行中的事务、Query Builder、文件句柄、Socket、流式响应、上传文件操作和非协程安全 SDK 客户端。

- 数据库、Redis、Cache 与 ORM 一律先采用项目当前组件提供的协程适配或连接池；不能从某个单例取一条连接后交给多个协程。池大小还要按 worker 数、协程并发、队列/自定义进程和数据库/Redis 服务端连接上限一起评估。
- 一个事务只能在当前协程以同一连接完成。不要在事务内启动 `Parallel`、将模型/连接传入新协程，或等待外部 HTTP、队列、文件处理等慢操作。
- 对没有池且无法确认协程安全的 SDK，优先让其调用留在非协程进程，或按其官方契约接入池/串行保护。锁只能保护当前资源竞争，不能替代数据库唯一约束、事务、幂等设计或跨服务器协调。
- 协程任务抛错、超时或被取消时，必须按所用客户端的真实 API 归还连接、关闭文件/流、停止子任务并记录可诊断错误；不要只依赖正常路径的回收。

## 并发执行不是可靠后台任务

已确认协程驱动且请求确实需要并发聚合多个短 I/O 结果时，可使用目标版本的 `Workerman\Coroutine\Parallel`。先设置每个依赖自己的超时和总请求预算，限制并发数量，并对部分失败定义降级或失败响应；不要对不受信任输入生成无界并发。

```php
use Workerman\Coroutine\Parallel;

$parallel = new Parallel();
$parallel->add(static fn () => $profileClient->fetch($userId));
$parallel->add(static fn () => $permissionClient->forUser($userId));

$results = $parallel->wait();
// 按项目契约检查每个结果和异常，再构造 Response。
```

- `Coroutine::create()` 或 `Parallel` 中的闭包仍依附当前 worker 内存和生命周期；进程退出、重启或异常时没有持久化、重试、状态查询或交付保证。
- 邮件、扣款、库存同步、导出、回调等需要可靠交付的工作，仍按 [process-and-async.md](process-and-async.md) 先持久化状态并投递到已有队列。不能以“已创建协程”作为任务已提交的证明。
- 并行分支不能共用同一活动资源，也不能静默吞掉某一分支错误后返回看似完整的授权、金额或库存结果。

## 验证协程改动

- restart 后确认目标 worker 的实际配置、进程数、监听和事件循环；不能只根据代码中的类名断言生产已启用协程。
- 构造至少两个会在 I/O/显式让出期间交错的请求，验证各自的用户、租户、追踪 ID 和响应没有串值；单请求测试无法发现 Context/静态变量污染。
- 对数据库、Redis 和外部 SDK 测试并发连接、事务回滚、超时、异常释放及服务端连接数；语法检查或单 worker 冒烟不足以证明连接池安全。
- 若调整了进程、代理或端口，还要验证认证和限流从真实入口依然生效，并明确未做的多实例、容量或故障切换验证。

本页刻意不复制 Swoole、Swow、Fiber、`Pool`、`Locker` 或 `Parallel` 的完整 API。未覆盖的参数、版本限制和取消/超时语义，必须以目标项目锁定的 Workerman/Webman 文档与已安装源码为准。
