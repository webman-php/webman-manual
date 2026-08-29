# Webman 数据库基础

仅在实现或审查数据库配置、查询、模型、事务、Redis、Cache、连接池或并发数据一致性时读取本文件。先确认目标项目已安装的组件、实际配置文件、连接名与现有模型基类；Webman 不自带一套可假定存在的 ORM、Redis 或 Cache 封装。

## 先识别组件，不要混用

| 已安装线索 | Webman 入口 | 配置位置 | 处理原则 |
|---|---|---|---|
| `webman/database` | `support\Db`、`support\Model` | `config/database.php` | 基于 Laravel 数据库组件；只把 Webman 的连接池、配置和生命周期差异写入本 skill。 |
| `webman/think-orm` | `support\think\Db`、`support\think\Model` | `config/think-orm.php` | 基于 ThinkORM；完整查询 API 查其上游文档和已安装源码。 |
| 仅有 `illuminate/database` 或其它自定义包 | 以项目现有封装为准 | 以项目为准 | 不假设 `support\Db`、模型基类或连接池已由 Webman 集成。 |

- 先查 `composer.json`、`composer.lock`、`config/database.php`、`config/think-orm.php`、现有 `app/model/` 和调用点。一个项目已经选定其中一种路线时，不要为一个新功能再引入另一套 ORM。
- 只有用户明确需要新数据库组件时，才安装依赖。手册中的 `composer require -W webman/database` 或 `composer require -W webman/think-orm` 会改变依赖树，安装后需要 **restart**，不是 reload。
- 迁移、MongoDB、Medoo、关联和完整分页 API 不在本阶段展开；所需接口未覆盖时，先核对目标版本官方手册和已安装源码，不要凭 Laravel 或 ThinkPHP 记忆猜测。

## 配置与连接选择

连接名是配置中的稳定契约：Laravel 风格以 `config/database.php` 的 `default` 和 `connections` 键为准；模型的 `$connection`、查询与事务必须引用同一个连接名。密码、TLS 选项和环境变量沿用项目既有机密管理方式，不能在代码或 skill 示例中硬编码。

```php
use support\Db;

// 默认连接：config/database.php 的 default。
$user = Db::table('users')->where('id', $id)->first();

// 指定连接：connections 中的 primary 键。
$user = Db::connection('primary')
    ->table('users')
    ->where('id', $id)
    ->first();
```

- 连接名不是数据库名，也不能由请求参数直接决定。多租户、读写库或报表库选择应通过受控映射与项目既有策略完成。
- 修改 `config/database.php` 或 `config/think-orm.php` 等普通配置通常按 [core-runtime.md](core-runtime.md) 通过 reload 生效；新增 Composer 依赖、扩展初始化或进程/协程配置才需要 restart。先确认目标项目的实际运行方式。
- 当前手册快照将 `pool` 配置标注为 Swoole/Swow 驱动支持。未确认协程驱动和并发需求前，不要为了“性能”添加或放大连接池；保留项目已验证的设置。需要启用协程或评估资源隔离时，再读取 [coroutine-runtime.md](coroutine-runtime.md)。

## Laravel 风格查询与写入

以下配方只适用于已安装 `webman/database` 且项目使用 `support\Db` 的情形。表名、列名和排序字段必须来自代码中的白名单；参数绑定只能保护值，不能安全地绑定任意 SQL 标识符。

```php
use support\Db;

$user = Db::connection('primary')
    ->table('users')
    ->where('tenant_id', $tenantId)
    ->where('id', $userId)
    ->first();

$newId = Db::connection('primary')
    ->table('users')
    ->insertGetId([
        'tenant_id' => $tenantId,
        'display_name' => $validatedName,
    ]);

$affected = Db::connection('primary')
    ->table('users')
    ->where('tenant_id', $tenantId)
    ->where('id', $userId)
    ->update(['display_name' => $validatedName]);
```

- `first()`、`value()`、`exists()`、`get()`、`insertGetId()`、`update()` 和 `delete()` 是高频入口；只写当前任务需要的最小调用，不复制完整 Query Builder API。
- 使用 `where()`、`selectRaw($sql, $bindings)` 等带绑定参数的入口处理**值**。不要把输入拼入 `Db::raw()`、`whereRaw()`、表名、列名、`orderByRaw()` 或排序方向；动态字段必须先映射到固定白名单。
- 批量读取大表时使用目标版本支持的 `chunkById()` 或游标式处理，避免一次 `get()` 读入全表。不要在 `chunkById()` 回调中删除正在遍历的记录。
- `updateOrInsert()` 不能替代数据库唯一约束。涉及唯一身份、库存、余额或幂等写入时，仍须设计唯一索引，并按业务需要使用事务与锁。
- `truncate()`、无条件 `delete()`、宽泛 `update()` 属于高风险操作；必须有用户授权、明确范围与可验证的目标，不能为实现普通接口而顺手使用。

## Laravel 风格模型

模型继承 `support\Model`。显式配置与数据表不一致的表名、主键、时间戳和连接；批量写入必须使用 `$fillable` 白名单，不能把 `$request->all()` 直接传给 `create()` 或 `fill()`。

```php
<?php
namespace app\model;

use support\Model;

class User extends Model
{
    protected $connection = 'primary';
    protected $table = 'users';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = ['display_name'];
}
```

- 模型默认连接来自数据库配置；一旦模型声明 `$connection`，之后与该模型共同参与的事务必须开启在同名连接上。
- `findOrFail()`、`firstOrFail()` 等未找到即抛异常的 API 要接入项目统一异常响应，不要让框架默认 HTML 错误页意外成为 API 响应。
- 关联、观察者、序列化、软删除和高级 Eloquent 功能按需查目标版本。模型观察者还依赖额外的 `illuminate/events`，未安装时不要生成相应代码。

当 `webman/console` 已安装且用户明确要求新增模型或一组 CRUD 骨架时，读取 [console-generators.md](console-generators.md)：`make:model` 可根据受控表结构生成模型属性，`make:crud` 可生成模型、控制器和条件性的 Validator。两者都不替代 `$fillable`、事务、资源级授权、租户范围或数据库约束；生成后必须收敛为当前项目的模型和 API 契约。

## ThinkORM 边界

项目已使用 `webman/think-orm` 时，使用其专用入口与模型基类，不要混入 `support\Db` 或 `support\Model`：

```php
use support\think\Db;

$user = Db::table('users')
    ->where('id', $userId)
    ->find();
```

- ThinkORM 配置位于 `config/think-orm.php`，模型继承 `support\think\Model`；表名、主键和连接属性遵循当前已安装 ThinkORM 版本的约定。
- 查询、关联、模型事件和分页的完整用法属于 ThinkORM 上游 API。先读项目锁定版本文档或源码，避免把 Laravel 语义迁移过来。
- ThinkORM 事务同样必须保证“开启、模型写入、提交、回滚”使用同一连接；下节的连接一致性原则同样适用。

## 事务、锁与连接一致性

需要把多次写入作为一个整体时，使用同一连接对象完成整个事务，并捕获 `\Throwable`（不能只捕获 `\Exception`）。失败后回滚并继续抛出，让项目统一异常处理器决定 HTTP 响应与日志。

```php
use support\Db;

$connection = Db::connection('primary');
$connection->beginTransaction();

try {
    $account = $connection->table('accounts')
        ->where('id', $accountId)
        ->lockForUpdate()
        ->first();

    // 校验余额、授权和状态后，继续在 $connection 上写入。
    $connection->table('accounts')
        ->where('id', $accountId)
        ->update(['balance' => $newBalance]);

    $connection->commit();
} catch (\Throwable $exception) {
    $connection->rollBack();
    throw $exception;
}
```

- 若模型声明 `protected $connection = 'primary'`，事务也必须通过 `Db::connection('primary')` 开启、提交和回滚；在默认连接开启事务而让模型写到另一连接，事务不会覆盖模型写入。
- `lockForUpdate()` 与 `sharedLock()` 仅在有效事务内才有一致性意义。锁定前先缩小 `where` 条件，保持事务短小，避免在事务内调用外部 HTTP、发送邮件或等待人工输入。
- 跨多个数据库连接、外部 API、消息发送和文件系统操作不自动构成一个原子事务。需要跨边界可靠性时，先了解项目已有的幂等、重试或 Outbox 方案；不要声称单个数据库事务可以覆盖它们。
- 不要把连接、Query Builder、事务或已加载模型保存到静态变量、单例、计时器或可复用控制器属性。协程场景中每个协程都应通过当前组件的连接池取得适当连接，不能共享一个进行中事务。
- 若已安装 `webman/log`，它会在请求结束时记录未提交事务，排查关键字为 `Uncommitted transactions`。这只能辅助发现遗漏，不能替代成功/异常路径的数据库测试。

## Redis：连接、键与过期时间

仅在已安装 `webman/redis` 且项目使用 `support\Redis` 时使用以下入口。默认配置在 `config/redis.php`；安装组件后需要 restart。手册要求 PHP CLI 具备 Redis 扩展，当前组件也可能按项目配置使用其它客户端实现，因此始终以锁定依赖和实际配置为准。

```php
use support\Redis;

$redis = Redis::connection('cache');
$key = 'cache:user:' . $tenantId . ':' . $userId;

$redis->setEx($key, 60, json_encode($profile, JSON_THROW_ON_ERROR));
$cachedProfile = $redis->get($key);
$redis->del($key);
```

- `get()`、`setEx()`、`del()`、`expire()`、`incr()`、`connection()` 是高频入口。读写数据的序列化格式、Key 前缀与 TTL 应由当前业务约定统一；不要把用户输入直接拼成无边界的 Key。
- 多个逻辑用途使用 `config/redis.php` 中命名的独立连接，例如业务、缓存和锁。用 `Redis::connection('cache')` 选择，而不是在请求中调用 `Redis::select()` 切换数据库；常驻连接会让一次 `select()` 影响后续请求。
- 每个 Webman 进程拥有自己的 Redis 连接池。协程开启后，池大小、worker 数量和其它自定义进程会共同决定 Redis 连接总量；按真实并发与服务端上限配置，不要只看单个 `max_connections`。
- 不把阻塞队列读取、订阅循环或无限重试塞进普通 HTTP 控制器。队列、订阅和长任务有独立进程/异步参考页，不能因为 Redis 可用就默认在请求线程中执行。

## Cache：选择存储并保持失效边界

`webman/cache` 使用 `support\Cache`，配置在 `config/cache.php`。即使当前版本在缺少配置时有默认行为，也应显式定义项目的默认 store 和 stores，避免环境升级后缓存位置悄然变化。

| Store | 共享范围 | 使用边界 |
|---|---|---|
| `file` | 同一台机器的多个进程 | 不跨服务器；需保证运行目录可写。 |
| `array` | 当前进程 | 重启失效，也不跨进程；不能保存登录态、全局限流或需要一致性的结果。 |
| `apcu` | 同一台机器 | 不跨服务器，需要 APCu 扩展；不适合频繁写入或清理。 |
| `redis` | 跨进程、跨服务器 | 依赖 `webman/redis`，复用 `config/redis.php` 中指定连接及其连接池。 |

```php
use support\Cache;

// `.`、`_`、`-` 等安全字符组成的 key 可兼容 PSR 缓存键限制。
$key = 'user.profile.' . $tenantId . '.' . $userId;

$profile = Cache::get($key);
if ($profile === null) {
    $profile = $repository->profile($tenantId, $userId);
    Cache::set($key, $profile, 60);
}

// 数据更新已提交后，调用专用失效方法。
function invalidateProfileCache(int $tenantId, int $userId): void
{
    Cache::delete('user.profile.' . $tenantId . '.' . $userId);
}

// 有意切换 store 时显式声明。
Cache::store('redis')->set($key, $profile, 60);
```

- `webman/cache` 的高频 PSR-16 入口为 `get()`、`set($key, $value, $ttl)`、`delete()`、`has()`、`clear()` 和 `store()`。不要把 ThinkCache 的 `remember()`、标签或锁 API 当作 `support\Cache` 的接口；先检查目标包版本。
- `Cache::clear()` 会清空当前 store，不能用作单个用户、单个租户或一次发布的常规失效手段。优先删精确 key，或使用受控版本号让旧 key 自然过期。
- `support\Cache` 使用 PSR 缓存键约束，Key 不能包含 `{}` `()` `/` `\` `@` `:`。不要通过修改 `zend.assertions` 绕过校验；为 Cache 使用 `.`、`_`、`-` 等安全分隔符。直接 Redis Key 可以沿用项目已有命名规则，但不要与 Cache Key 规则混淆。
- 对会变的数据显式给 TTL；缓存不是事实来源。缓存的值、Key 和失效范围必须包含影响结果的租户、权限、语言、版本或筛选维度，不能让不同用户命中同一份敏感数据。
- 简单的 get-计算-set 在高并发失效时会产生缓存击穿。只有确认项目已有可靠锁或可接受重复计算时才使用；不可接受的业务正确性仍须由数据库唯一约束或事务保证。

## 缓存失效与 Redis 锁

- 数据库事务修改了缓存依赖的数据时，先提交事务，再删除或更新对应缓存。事务回滚时不要提前发布新缓存或删除仍代表已提交数据的缓存。
- Redis `SET ... NX EX` 可用于短期、可重复计算的缓存回填协调。获取锁后要设置有限过期时间和随机 token；释放时必须原子地比较 token 再删除，不能用非原子的 `get()` 后 `del()`，否则锁过期并被其他请求取得后可能误删对方锁。
- 需要库存、余额、一次性业务动作等强正确性时，不要只依赖 Redis 锁。应结合上一节的数据库事务、行锁、唯一索引和幂等设计；Redis 锁只能是额外协调层。
- 不确定当前项目是否有经过验证的锁封装时，不要临时生成一套 Lua、续租或 Redlock 实现。先检查已有依赖和运维约束，再按其文档接入。

## ThinkCache 边界

项目已明确安装 `webman/think-cache` 时，使用 `config/think-cache.php` 和 `support\think\Cache`；其 `set($key, $value, $ttl)`、`get()`、`delete()`、store、标签和 `remember()` 语义属于 ThinkCache，不要复制到 `support\Cache`。

```php
use support\think\Cache;

Cache::set('user:profile:' . $userId, $profile, 60);
$profile = Cache::get('user:profile:' . $userId);
Cache::delete('user:profile:' . $userId);
```

- 仅因需要缓存不应新增 ThinkCache；沿用项目已有组件。要启用新组件时先取得授权，安装后 restart。
- ThinkCache 的标签、`remember()`、不同 store 和序列化细节以目标版本上游文档为准，尤其不要把 Cache 数据误用于授权或跨进程锁的唯一事实来源。

## Phinx 数据库迁移

Webman 手册以 Phinx 说明迁移，但目标项目可能已经使用其它迁移工具。先检查 `composer.lock`、`phinx.php` 或 `phinx.yml`、`database/migrations/` 和现有发布脚本；只有项目已使用 `robmorgan/phinx` 时，才沿用本节。不能因为要加一张表就同时引入 Phinx 与现有工具。

- 新增迁移工具或修改 Composer 依赖需要用户明确授权。Phinx 已安装时，从项目根目录通过 `php vendor/bin/phinx` 调用，避免假定机器安装了全局命令；配置文件、迁移目录和环境名以项目实际配置为准，数据库凭据仍从现有环境变量或机密机制取得。
- 下列命令中的 `<environment>` 必须替换为配置中实际存在的环境。`status` 是只读预检；`create` 会新增迁移文件；`migrate` 和 `rollback` 会写入目标数据库，执行前需要确认目标环境和用户授权。若项目需要显式配置文件，按其版本 `--help` 加上相应参数，不要猜测配置发现规则。

```bash
php vendor/bin/phinx status -e <environment>
php vendor/bin/phinx create AddUserStatus
php vendor/bin/phinx migrate -e <environment>
php vendor/bin/phinx rollback -e <environment>
```

- 每个迁移是带唯一时间戳的 PHP 类。已合并、已发布或可能已在任一环境执行过的迁移不可修改；修复应新增一份向前迁移，不能改旧文件或手动篡改版本记录来“补齐”状态。
- `change()` 只写 Phinx 能可靠反转的结构变更。新表使用 `create()`，已有表使用 `update()`，在 `change()` 中不能使用 `save()`；插入数据、破坏性变更或无法可靠回滚的操作，改用单独迁移的 `up()`/`down()`，并明确 `down()` 的真实限制。不要在同一类同时依赖 `change()` 与 `up()`/`down()`，因为前者存在时后两者会被忽略。

```php
<?php

use Phinx\Migration\AbstractMigration;

final class AddUserStatus extends AbstractMigration
{
    public function change()
    {
        $table = $this->table('users');
        $table
            ->addColumn('status', 'string', ['limit' => 20, 'default' => 'active'])
            ->addIndex(['status'])
            ->update();
    }
}
```

### 发布与生产边界

- 生产执行前先取得明确环境、备份或可恢复方案、发布窗口和预期 `status`；在同一目标数据库运行 `status` 后再决定是否 `migrate`。不要把 `rollback` 当作常规线上回退按钮：迁移可能已改变或删除数据，且应用代码也可能已不兼容旧 schema。
- 多实例或多版本并行时，优先采用向后兼容的扩展式变更：先新增可空列、默认值或新表，发布兼容读写代码，分批回填，再在后续发布中收紧约束或删除旧列。大表索引、列类型和数据回填要评估目标数据库的锁表、耗时与磁盘影响；不要假定 DDL 能被应用事务完整回滚。
- 迁移完成后确认 `status`、目标 schema、应用关键读写和必要的数据回填结果。纯 schema 迁移本身不要求 reload 或 restart；只有同次发布还修改了 PHP、常规配置、Composer 依赖或进程配置时，才按 [core-runtime.md](core-runtime.md) 的边界处理服务生效。

## 变更与验证边界

- 新增模型、查询或事务后，优先运行项目已有的数据库测试或受控测试库验证；语法检查无法证明提交、回滚、锁、索引或多连接行为。
- 修改普通数据库配置时说明 reload 边界；安装 Composer 包或修改进程/协程配置时说明 restart 边界。不要在未经授权的环境执行迁移、清表、建库或生产数据写入。
- 涉及并发扣减、库存、余额、唯一写入或跨库流程时，明确是否实际验证了并发与失败回滚；单请求手工测试不足以证明正确性。

## 未覆盖的 API

本页刻意不复制 Laravel Query Builder、Eloquent、ThinkORM、Redis、Cache 或 Phinx 的完整手册。关联、分页、MongoDB、Medoo 和低频数据库组件，应先按目标项目依赖和官方文档选择；后续 reference 会补充有重复使用价值的 Webman 特有配方。
