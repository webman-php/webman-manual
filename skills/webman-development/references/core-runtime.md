# Webman 运行模型、配置与生命周期

仅在需要理解或修改 Webman 的启动、配置生效、进程、生命周期、控制器复用、业务初始化、监控或 Windows/Linux 差异时读取本文件。

## 项目和版本确认

先检查 `composer.lock`，再以当前项目文件为准。Webman 在不同版本之间曾把部分服务器字段从 `config/server.php` 移到 `config/process.php`；不要假设字段位置或示例配置与当前项目一致。

不要把当前手册的 PHP 或 Webman 版本要求反向套用到已有项目。已有项目以 `composer.json`、`composer.lock` 和实际运行环境为准；新建项目时再核对当前官方安装要求。

通常可从以下文件确认运行方式：

- `start.php`：Linux/Unix 的启动入口。
- `windows.php` 或 `windows.bat`：Windows 开发入口。
- `config/app.php`：应用、控制器和路径配置。
- `config/process.php`：当前版本常见的 Webman 和自定义进程定义。
- `config/server.php`：旧版本或仍保留的服务器级配置。
- `runtime/`：PID、日志和其他运行期文件的位置；部署时必须可写。

Linux 进程管理通常需要 PHP 的 `posix` 和 `pcntl` 扩展；事件扩展属于可选性能增强。Windows 不依赖这两个扩展，但也不具备相同的多进程和守护进程能力。检查环境时区分“必需能力”和“可选性能优化”。

## 启动与变更生效

Linux 开发模式通常使用：

```bash
php start.php start
```

Linux 守护进程模式通常使用：

```bash
php start.php start -d
```

Windows 仅适合开发和调试，使用项目提供的 `windows.bat` 或：

```bash
php windows.php
```

Windows 不支持 Linux 的守护进程模式和完整的 `stop`、`reload`、`status`、`connections` 等进程管理命令。不要在 Windows 使用说明或自动化中假设这些命令可用。

| 变更类型 | 通常的生效方式 | 处理原则 |
|---|---|---|
| 业务 PHP 代码、普通配置 | reload | 先确认当前运行方式和项目是否关闭/替换了默认监控。 |
| `config/server.php`、`config/process.php`、监听端口、进程数、事件循环 | restart | 进程定义在启动时创建，reload 不足以重建它。 |
| `composer require`、`composer dumpautoload`、扩展/依赖初始化变化 | restart | 新类、Composer `autoload.files` 和启动期初始化需在新进程中加载。 |
| Windows 开发环境 | 停止后重新运行 `php windows.php` | 不要给出 Linux reload/restart 命令作为已执行步骤。 |

只有在用户授权实际操作时才执行重载或重启；在代码建议或修改说明中指出所需命令即可。

Linux 前台进程通常使用 `php start.php reload` 或 `php start.php restart`；守护进程是否需要保留 `-d` 应按当前启动方式和项目运维约定处理，不要不加检查地改变运行模式。

## 常驻内存和进程隔离

Webman worker 进程会处理多个请求。每个进程彼此独立：它们分别拥有变量、类实例、单例和数据库连接。某项初始化会在每个 worker 各执行一次，不能把单进程内存当作全局共享存储。

请求对象是请求级的，在请求结束后回收。方法内的临时变量和临时对象通常也可随作用域结束被回收，无需为了“常驻内存”而对所有局部变量调用 `unset`。

需要跨请求复用的对象必须同时满足：

- 不保存 `Request`、当前用户、认证结果、事务、上传文件或其他请求专属数据。
- 内部数组、缓存和连接不会无限增长，且有清晰的生命周期或上限。
- 多个进程各自实例化是可接受的；若需要跨进程协调，使用数据库、Redis、队列或其他外部共享服务。

默认 `support\Container::get()` 会缓存无构造参数的对象；使用默认容器时把它当作长生命周期对象处理。项目替换了 PSR-11 容器时先检查其作用域和缓存规则。需要带参数的新对象或每请求对象时，使用当前项目约定的构造方式，不要滥用容器缓存。

## 控制器复用

读取 `config/app.php` 的 `controller_reuse` 后再决定控制器是否长生命周期：

- `false`：每个请求通常得到新的控制器实例。
- `true`：同一进程内会复用控制器实例；构造函数不是每个请求都会执行。

不要从通用“生命周期”说明推断该开关的默认值；不同版本、脚手架或项目可能不同，目标项目的实际配置优先。

当控制器复用开启时，不要在控制器属性中保存请求参数、模型实例、查询构造器、当前用户、响应对象或可增长数组。它们会影响后续请求，并可能导致跨用户数据泄露或内存增长。

即使控制器复用关闭，静态变量、全局变量、单例和容器缓存对象仍可能跨请求存活，应同样避免写入请求状态。

## 配置

使用 `config()` 读取已加载的配置，例如：

```php
$debug = config('app.debug');
$timeout = config('payment.timeout', 10);
```

不要通过运行时写内存数组来模拟持久化配置变更。修改配置文件后，需要按“启动与变更生效”中的边界 reload 或 restart。

在 `config/` 下新增顶层配置文件可以通过点号读取。若新增配置子目录，遵循当前项目和 Webman 版本的启用规则：手册示例要求该子目录存在 `app.php` 且返回 `enable => true`。先检查项目现有配置目录，避免在不同版本中机械复制。

不要把密钥或环境差异直接硬编码进业务代码；使用当前项目已经采用的环境变量和配置加载方式。

## 启动流程与初始化

典型启动流程是：加载 `config/`，创建 Webman 和自定义进程，然后在各 worker 的启动阶段加载自动加载文件、中间件、bootstrap，并加载路由。插件也可能提供自己的 bootstrap、中间件和路由。

因此：

- `composer.json` 的 PSR-4 和 `autoload.files` 在 Webman 启动前由 Composer 加载，修改后运行 `composer dumpautoload` 并 restart。
- `config/autoload.php` 在 worker 启动阶段加载，适合框架级自定义文件，修改后通常可通过 reload 生效；仍应优先使用 Composer PSR-4，不要随意引入全局函数文件。
- `config/bootstrap.php` 中的 `Bootstrap::start()` 在每个相关 worker 启动时执行一次，可能也会在命令行环境执行。
- bootstrap 中的定时器、外部连接、缓存预热或单例初始化必须幂等，并按 `$worker`、进程名和 worker id 限定执行范围。
- 只需要单次、跨进程协调的初始化不能仅依赖“worker 0”；还需使用可靠的外部锁或部署流程，并明确用户期望的范围。

不要在 bootstrap 中保存请求状态，也不要执行未经授权的外部写操作。

若项目已安装 `webman/console` 且用户同意创建文件，可读取 [console-generators.md](console-generators.md) 后用 `make:bootstrap` 创建骨架。生成器可能把类写入 bootstrap 配置；仍须检查每 worker 执行、幂等性、外部副作用和 restart 边界。

## Monitor 与内存增长

开发模式的 monitor 可以监控指定目录文件变化并自动 reload。Linux 上它还可以在进程内存接近 `memory_limit` 时安全重启进程；这依赖项目实际的 `monitor` 配置，不能假设在所有环境都启用。

监控目录和扩展名应保持必要且有限，避免因为扫描过多文件拖慢开发环境。Windows 的能力受限，文件监控通常需要通过 `windows.bat` 或 `php windows.php` 启动。

排查内存问题时，优先检查：

- 长生命周期对象或静态数组是否不断追加数据。
- 控制器属性、单例、容器缓存、Timer 回调是否持有大对象或请求对象。
- 数据库、文件、Socket 或 HTTP 客户端资源是否按其库的要求释放或复用。
- 是否只在一个 worker 中观察到问题；多个 worker 的内存和日志需要分别查看。

## 与 HTTP/API 任务的边界

本文件只定义运行模型和配置边界。请求、响应、上传、路由、中间件、数据库、队列和协程 API 配方将在独立 reference 中提供。当前任务若需要这些具体方法，先以目标项目锁定版本对应的 Webman 官方文档为准，避免猜测接口。
