# Webman 后台任务、队列与自定义进程

仅在处理耗时业务、异步投递、Redis Queue、独立 HTTP 进程、自定义监听进程、定时任务、业务启动初始化或后台常驻任务时读取本文件。先确认任务是否需要同步结果、可否重试、是否必须持久化、预期峰值、现有队列/进程配置和部署权限；不要把“异步”当作无条件的性能优化。

## 先选执行路径

| 需求 | 首选路径 | 关键边界 |
|---|---|---|
| 请求必须立即得到短小、确定的结果 | 普通 HTTP 控制器 | 只做必要数据库/内存工作；不等待邮件、外部 HTTP、批量计算或订阅循环。 |
| 可稍后完成、需消化突发、可重试 | 已有持久化消息队列 | HTTP 返回已接受/任务标识；消费端必须幂等，队列不是同步 RPC。 |
| 必须直接返回较慢结果，且可隔离有限并发 | 专用 HTTP 进程组 + 代理路由 | 隔离不等于无限容量；需要超时、限流和真实入口验证。 |
| 需要逐步输出远端异步结果 | 分段/流式响应 | 连接仍被占用；处理断开、超时和背压，不能当作持久任务队列。 |
| 常驻消费、轮询、监听或非 HTTP 协议 | 自定义进程 | 每个 worker 都会执行生命周期回调；明确副本数、资源释放和分布式重复执行。 |
| 按日历规则定期执行 | 已安装的 `workerman/crontab` + 专用自定义进程 | 回调在该 worker 内同步运行；慢或时间敏感任务应隔离进程，集群唯一性另行保证。 |
| 每个 worker 启动后的一次初始化或本地短周期检查 | `config/bootstrap.php` 的 `Bootstrap::start()` 或已有自定义进程 | 启动回调不是全局单例，也没有当前 HTTP 请求；必须快速、幂等且能在 restart 后重复运行。 |

- 不要在普通 HTTP 控制器中 `sleep()`、阻塞读取队列、无限重试、循环订阅或等待不可控第三方服务。协程未确认启用时更不能假定阻塞 I/O 自动让出 worker。
- 任务需要可靠结果时，先持久化任务/业务状态，再投递可重试的消息；客户端查询任务状态或通过现有推送机制获知完成。不要只把 PHP 内存中的闭包、Request、Session、模型对象或上传临时文件路径交给后台。
- 选择专用端口、WebSocket、流式 HTTP 或新增队列会影响网络、进程数、监控和部署。没有用户授权或既有基础设施时，先给出选型与所需变更，不能暗中新增端口、守护服务或 Composer 依赖。

## Redis Queue：可靠投递与幂等消费

仅在项目已安装 `webman/redis-queue` 时使用其 `Webman\RedisQueue\Redis`/`Client` API 和 `config/plugin/webman/redis-queue/` 配置。它的 Redis 连接与 `support\Redis`、业务 Cache 或 Session Redis 配置不是同一个契约，不能混用连接名、前缀或可用性假设。

```php
use Webman\RedisQueue\Redis;

// 只传可序列化、可追踪的业务标识；由消费者重新读取需要的数据。
$sent = Redis::send('send-email', [
    'message_id' => $messageId,
    'tenant_id' => $tenantId,
]);

if ($sent !== true) {
    throw new \RuntimeException('Queue delivery was not confirmed');
}
```

- 需要确认投递时使用同步 `Redis::send()` 并处理 false/异常。`Client::send()` 是 Workerman 运行环境中的异步本地内存缓冲，进程在尚未写入 Redis 时重启可能丢失消息；它只适用于可丢失的低价值通知，且不能照搬到 CLI 脚本。
- 消费成功不等于“任务只会执行一次”。消费者应以业务 ID、唯一约束、状态机或幂等键抵抗重复投递/重试；邮件、扣款、库存、外部回调等副作用尤其要先检查是否已完成。
- 消费抛出异常会进入组件的重试/失败路径。保留可诊断日志和失败消息处理方案，按真实耗时、依赖恢复时间和业务风险设置 attempts/retry；不要让永久格式错误无限重试，也不要吞掉异常假装成功。
- 数据库变更必须触发后台工作时，先提交事务再投递，或沿用项目已有 Outbox/可靠事件方案。绝不能从未提交事务中投递一条消费者可能先执行的消息。
- 将慢队列与快队列划分到不同消费者进程只在现有吞吐/积压证据支持时进行；先观测积压、耗时、失败和 Redis 连接上限，再改变 `count` 或新增进程组。

## 定时任务与业务初始化：以 worker 为边界

`workerman/crontab` 是可选组件。仅当 `composer.lock` 已安装它，或用户明确批准添加依赖后，才在一个专用 process 的 `onWorkerStart()` 中注册 `Crontab`。它支持秒级六字段表达式，也兼容省略秒字段的五字段表达式；不要在 skill 中猜测项目未锁定版本的完整表达式语法。

```php
<?php

namespace app\process;

use Workerman\Crontab\Crontab;
use Workerman\Worker;

final class CleanupSchedule
{
    public function onWorkerStart(Worker $worker): void
    {
        new Crontab('0 */5 * * * *', function (): void {
            // 投递一个可幂等的业务 ID，或执行短小、可重入的维护工作。
            // 不保存 Request、Session、模型实例或上传临时文件路径。
        });
    }
}
```

```php
// config/process.php 中已有数组的一项；安装或修改后 restart。
'cleanup-schedule' => [
    'handler' => app\process\CleanupSchedule::class,
    'count' => 1,
],
```

- 同一 Crontab worker 内的回调不是异步的：一个耗时回调会延后其它回调。慢任务应写入已有持久化队列，或将互不影响的时间敏感任务放到各自 process；不能靠添加多个定时器解决阻塞。
- `count = 1` 只限制该应用实例的副本数。容器扩容、多台服务器、reload/restart 都可能重复触发调度；若副作用只允许全局一次，沿用已有分布式锁、leader 或外部调度器，并验证锁失效和故障接管。
- Crontab 在下一匹配周期运行，不把“注册成功”当作“已经执行”。验证应包含实际触发、跨 worker/实例重复行为、慢回调隔离和失败日志。

业务启动初始化使用 `config/bootstrap.php` 中实现 `Webman\Bootstrap` 的类。框架会在每个 worker 启动时调用静态 `start()`；命令行上下文的 `$worker` 为 `null`，自定义进程也会执行该配置。因此它适合注册本地 Timer、初始化无请求状态的资源或预热轻量只读数据，不适合迁移、回填、外部写入或依赖单次执行的业务动作。

```php
<?php

namespace app\bootstrap;

use Webman\Bootstrap;
use Workerman\Timer;
use Workerman\Worker;

final class MetricsBootstrap implements Bootstrap
{
    public static function start(?Worker $worker)
    {
        // 不让命令行任务意外注册常驻 Timer。
        if ($worker === null) {
            return;
        }

        // 只在当前应用实例的指定 worker 注册一次；不是跨服务器的互斥。
        if ($worker->name !== 'webman' || $worker->id !== 0) {
            return;
        }

        Timer::add(60, static function (): void {
            // 只做短小、可重入的本地检查；从持久化存储重新读取业务状态。
        });
    }
}
```

```php
// config/bootstrap.php 中保留已有启动项并追加：
return [
    // ...
    app\bootstrap\MetricsBootstrap::class,
];
```

- Bootstrap 类必须遵循目标框架版本的接口签名。它可能被插件 bootstrap 一同执行，故不应假设自己的类是唯一启动回调，也不能重置或覆盖已有启动项。
- 进程配置或 Composer 依赖变化需要 restart；仅改启动逻辑时也应让 worker 重建后再验证。Timer/连接等资源由创建它的 worker 管理，应在既有停止回调或进程退出策略中妥善释放。

## 自定义进程：配置是部署契约

`config/process.php` 定义监听或非监听 worker。增加/修改 handler、`listen`、`count`、用户、事件循环、`reusePort`、SSL context 或 constructor 都需要 **restart**，并同步评估端口、防火墙、反向代理、权限、日志和监控。Windows 开发环境必须通过 `php windows.php` 才会启动自定义进程；生产进程管理按项目实际平台处理。

若项目已安装 `webman/console` 且用户同意创建和注册进程，可先读取 [console-generators.md](console-generators.md) 后使用 `make:process`。该命令会写入 process 配置，交互式监听设置也不等于已完成端口、安全或部署评审。

```php
// config/process.php 中已有数组的一项；不要替换其它进程。
'report-worker' => [
    'handler' => app\process\ReportWorker::class,
    'count' => 1,
    'reloadable' => true,
],
```

```php
<?php

namespace app\process;

use Workerman\Timer;
use Workerman\Worker;

final class ReportWorker
{
    public function onWorkerStart(Worker $worker): void
    {
        Timer::add(60, function (): void {
            // 只执行短小、可重入的检查；业务状态从持久化存储重新读取。
        });
    }

    public function onWorkerStop(Worker $worker): void
    {
        // 关闭本 worker 创建的连接、timer 或临时资源。
    }
}
```

- 省略 `listen` 的进程不接收网络端口；需要监听时先选择协议、绑定地址、认证和代理方式。不要暴露一个未鉴权的内部 HTTP/Socket 管理端口到公网。
- `count > 1`、reload、多个服务器或容器副本都会让 `onWorkerStart()`/Timer 多次运行。若任务只允许全局单次执行，使用已有的分布式锁、leader 或调度器，并验证故障接管；不能仅把 `count` 写成 1 就假定整个集群唯一。
- Handler、连接、Timer 和缓存位于 worker 生命周期。不要保存上一次 HTTP Request、Session、数据库事务、可变用户数据或未经释放的大数组；处理回调异常应记录后决定重试/退出策略，不能在常规路径用 `echo` 代替日志。
- 为慢 HTTP 单独建进程组时，必须定义路由前缀/代理规则、认证、上传限制、超时和并发上限。它隔离主 HTTP worker，但面对突发请求仍会耗尽自己的 worker；需要排队和削峰时使用合适的消息队列。

## 验证边界

- 对队列至少验证：同步投递失败、消费者成功、可重试失败、永久失败进入失败处理、重复消息的幂等性，以及消费者进程确实在目标环境运行。一次 `send()` 成功不能证明消费、重试或多服务器正确。
- 对自定义进程至少验证：服务 restart 后配置生效、进程/端口状态、正常停止时的资源释放、worker 数量下的重复行为和日志输出。新增监听端口还要从真实代理/CDN 入口验证访问控制。
- Stomp、协程、WebSocket 协议细节、`webman/push` 和完整 Redis Queue/Crontab API 不在本页复制；只有项目已安装或任务明确需要时，按目标版本官方文档继续扩展。
