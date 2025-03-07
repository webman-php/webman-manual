# 协程
webman是基于workerman开发的，所以webman可以使用workerman的协程特性。

## 前提条件
- PHP >= 8.1
- Workerman >= 5.1.0 (`composer require workerman/workerman ~v5.1`)
- webman-framework >= 2.1 (`composer require workerman/webman-framework ~v2.1`)
- 安装了swoole或者swow扩展，或者安装了`composer require revolt/event-loop` (Fiber)
- 协程默认是关闭的，需要单独设置eventLoop开启

## 开启方法
webman支持为不同的进程开启不同的驱动，所以你可以在`config/process.php`中通过`eventLoop`配置协程驱动：

```php
return [
    'webman' => [
        'handler' => Http::class,
        'listen' => 'http://0.0.0.0:8787',
        'count' => 1,
        'user' => '',
        'group' => '',
        'reusePort' => false,
        'eventLoop' => '', // 默认为空自动选择Select或者Event，不开启协程
        'context' => [],
        'constructor' => [
            'requestClass' => Request::class,
            'logger' => Log::channel('default'),
            'appPath' => app_path(),
            'publicPath' => public_path()
        ]
    ],
    'my-coroutine' => [
        'handler' => Http::class,
        'listen' => 'http://0.0.0.0:8686',
        'count' => 1,
        'user' => '',
        'group' => '',
        'reusePort' => false,
        // 开启协程需要设置为 Workerman\Events\Swoole::class 或者 Workerman\Events\Swow::class 或者 Workerman\Events\Fiber::class
        'eventLoop' => Workerman\Events\Swoole::class,
        'context' => [],
        'constructor' => [
            'requestClass' => Request::class,
            'logger' => Log::channel('default'),
            'appPath' => app_path(),
            'publicPath' => public_path()
        ]
    ]
    
    // ... 其它配置省略 ...
];
```

> **提示**
> webman可以为不同的进程设置不同的eventLoop，这意味着你可以选择性的为特定进程开启协程。
> 例如上面配置中8787端口的服务没有开启协程，8686端口的服务开启了协程，配合nginx转发可以实现协程和非协程混合部署。

## 协程示例
```php
<?php
namespace app\controller;

use support\Response;
use Workerman\Coroutine;
use Workerman\Timer;

class IndexController
{
    public function index(): Response
    {
        Coroutine::create(function(){
            Timer::sleep(1.5);
            echo "hello coroutine\n";
        });
        return response('hello webman');
    }

}
```

当`eventLoop`为`Swoole` `Swow` `Fiber`时，webman会为每个请求创建一个协程来运行，在处理请求时可以继续创建新的协程执行业务代码。


## 协程限制

* 当使用Swoole Swow为驱动时，业务遇到阻塞IO协程会自动切换，能实现同步代码异步执行。
* 当使用Fiber驱动时，遇到阻塞IO时，协程不会发生切换，进程进入阻塞状态。
* 使用协程时，不能多个协程同时对同一个资源进行操作，例如数据库连接，文件操作等，这可能会引起资源竞争，正确的用法是使用连接池或者锁来保护资源。
* 使用协程时，不能将请求相关的状态数据存储在全局变量或者静态变量中，这可能会引起全局数据污染，正确的用法是使用协程上下文`context`来存取它们。

## 其它注意事项
Swow底层会自动hook php的阻塞函数，但是因为这种hook影响了PHP的某些默认行为，所以在你没有使用Swow但却装了Swow时可能会产生bug。

**所以建议：**  
* 如果你的项目没有使用Swow时，请不要安装Swow扩展
* 如果你的项目使用了Swow，请设置`eventLoop`为`Workerman\Events\Swow::class` 

## 协程上下文

协程环境禁止将**请求相关**的状态信息存储在全局变量或者静态变量中，因为这可能会导致全局变量污染，例如

```php
<?php

namespace app\controller;

use support\Request;
use Workerman\Timer;

class TestController
{
    protected static $name = '';

    public function index(Request $request)
    {
        static::$name = $request->get('name');
        Timer::sleep(5);
        return static::$name;
    }
}
```

> **注意**
> 协程环境下并非禁止使用全局变量或静态变量，而是禁止使用全局变量或静态变量存储**请求相关的状态数据**。
> 例如全局配置、数据库连接、一些类的单例等需要全局共享的对象数据是推荐用全局变量或静态变量存储的。

将进程数设置为1，当我们连续发起两个请求时  
http://127.0.0.1:8787/test?name=lilei  
http://127.0.0.1:8787/test?name=hanmeimei  
我们期望两个请求返回的结果分别是 `lilei` 和 `hanmeimei`，但实际上返回的都是`hanmeimei`。
这是因为第二个请求将静态变量`$name`覆盖了，第一个请求睡眠结束时返回时静态变量`$name`已经成为`hanmeimei`。

**正确但方法应该是使用context存储请求状态数据**
```php
<?php

namespace app\controller;

use support\Request;
use support\Context;
use Workerman\Timer;

class TestController
{
    public function index(Request $request)
    {
        Context::set('name', $request->get('name'));
        Timer::sleep(5);
        return Context::get('name');
    }
}
```

`support\Context`类用于存储协程上下文数据，当协程执行完毕后，相应的context数据会自动删除。
协程环境里，因为每个请求都是单独的协程，所以当请求完成时context数据会自动销毁。
非协程环境里，context会在请求结束时会自动销毁。

**局部变量不会造成数据污染**
```php
<?php

namespace app\controller;

use support\Request;
use support\Context;
use Workerman\Timer;

class TestController
{
    public function index(Request $request)
    {
        $name = $request->get('name');
        Timer::sleep(5);
        return $name;
    }
}
```
因为`$name`是局部变量，协程之间无法互相访问局部变量，所以使用局部变量是协程安全的。


## Locker 锁
有时候一些组件或者业务没有考虑到协程环境，可能会出现资源竞争或原子性问题，这时候可以使用`Workerman\Locker`加锁来实现排队处理，防止并发问题。

```php
<?php

namespace app\controller;

use Redis;
use support\Response;
use Workerman\Coroutine\Locker;

class IndexController
{
    public function index(): Response
    {
        static $redis;
        if (!$redis) {
            $redis = new Redis();
            $redis->connect('127.0.0.1', 6379);
        }
        // 如果不加锁，Swoole下会触发类似 "Socket#10 has already been bound to another coroutine#10" 错误
        // Swow下可能会触发coredump
        // Fiber下因为Redis扩展是同步阻塞IO，所以不会有问题
        Locker::lock('redis');
        $time = $redis->time();
        Locker::unlock('redis');
        return json($time);
    }

}
```

## Parallel 并发执行
当我们需要并发执行多个任务并获取结果时，可以使用`Workerman\Parallel`来实现。

```php
<?php

namespace app\controller;

use support\Response;
use Workerman\Coroutine\Parallel;

class IndexController
{
    public function index(): Response
    {
        $parallel = new Parallel();
        for ($i=1; $i<5; $i++) {
            $parallel->add(function () use ($i) {
                // Do something
                return $i;
            });
        }
        $results = $parallel->wait();
        return json($results); // Response: [1,2,3,4]
    }

}
```

## Pool 连接池
多个协程共用同一个连接会导致数据混乱，所以需要使用连接池来管理数据库、redis等连接资源。

webman已经提供了 [webman/database](../db/tutorial.md) [webman/redis](../db/redis.md) [webman/cache](../db/cache.md) [webman/think-orm](../db/thinkorm.md) [webman/think-cache](../db/thinkcache.md)等组件，它们都集成了连接池，支持在协程和非协程环境下使用。

如果你想改造一个没有连接池的组件，可以使用`Workerman\Pool`来实现，参考如下代码。

**数据库组件**

```php
<?php
namespace app;

use Workerman\Coroutine\Context;
use Workerman\Coroutine;
use Workerman\Coroutine\Pool;

class Db
{
    private static ?Pool $pool = null;

    public static function __callStatic($name, $arguments)
    {
        if (self::$pool === null) {
            self::initializePool();
        }
        // 从协程上下文中获取连接，保证同一个协程使用同一个连接
        $pdo = Context::get('pdo');
        if (!$pdo) {
            // 从连接池中获取连接
            $pdo = self::$pool->get();
            Context::set('pdo', $pdo);
            // 当协程结束时，自动归还连接
            Coroutine::defer(function () use ($pdo) {
                self::$pool->put($pdo);
            });
        }
        return call_user_func_array([$pdo, $name], $arguments);
    }

    private static function initializePool(): void
    {
        // 创建一个连接池，最大连接数为10
        self::$pool = new Pool(10);
        // 设置连接创建器(为了简洁，省略了配置文件读取)
        self::$pool->setConnectionCreator(function () {
            return new \PDO('mysql:host=127.0.0.1;dbname=your_database', 'your_username', 'your_password');
        });
        // 设置连接关闭器
        self::$pool->setConnectionCloser(function ($pdo) {
            $pdo = null;
        });
        // 设置心跳检测器
        self::$pool->setHeartbeatChecker(function ($pdo) {
            $pdo->query('SELECT 1');
        });
    }

}
```

**使用**
```php
<?php
namespace app\controller;

use support\Response;
use app\Db;

class IndexController
{
    public function index(): Response
    {
        $value = Db::query('SELECT NOW() as now')->fetchAll();
        return json($value); // [{"now":"2025-02-06 23:41:03","0":"2025-02-06 23:41:03"}]
    }

}
```

## 更多协程及相关组件介绍

参考[workerman 协程文档](https://www.workerman.net/doc/workerman/coroutine/coroutine.html)

## 协程与非协程混合部署
webman支持协程和非协程混合部署，例如非协程处理普通业务，协程处理慢IO业务，通过nginx转发请求到不同的服务上。

例如 `config/process.php`

```php
return [
    'webman' => [
        'handler' => Http::class,
        'listen' => 'http://0.0.0.0:8787',
        'count' => 1,
        'user' => '',
        'group' => '',
        'reusePort' => false,
        'eventLoop' => '', // 默认为空自动选择Select或者Event，不开启协程
        'context' => [],
        'constructor' => [
            'requestClass' => Request::class,
            'logger' => Log::channel('default'),
            'appPath' => app_path(),
            'publicPath' => public_path()
        ]
    ],
    'my-coroutine' => [
        'handler' => Http::class,
        'listen' => 'http://0.0.0.0:8686',
        'count' => 1,
        'user' => '',
        'group' => '',
        'reusePort' => false,
        // 开启协程需要设置为 Workerman\Events\Swoole::class 或者 Workerman\Events\Swow::class 或者 Workerman\Events\Fiber::class
        'eventLoop' => Workerman\Events\Swoole::class,
        'context' => [],
        'constructor' => [
            'requestClass' => Request::class,
            'logger' => Log::channel('default'),
            'appPath' => app_path(),
            'publicPath' => public_path()
        ]
    ],
    
    // ... 其它配置省略 ...
];
```

然后通过nginx配置转发请求到不同的服务上

```nginx
upstream webman {
    server 127.0.0.1:8787;
    keepalive 10240;
}

# 新增一个8686 upstream
upstream task {
   server 127.0.0.1:8686;
   keepalive 10240;
}

server {
  server_name webman.com;
  listen 80;
  access_log off;
  root /path/webman/public;

  # 以/tast开头的请求走8686端口，请按实际情况将/tast更改为你需要的前缀
  location /tast {
      proxy_set_header X-Real-IP $remote_addr;
      proxy_set_header Host $host;
      proxy_http_version 1.1;
      proxy_set_header Connection "";
      proxy_pass http://task;
  }

  # 其它请求走原8787端口
  location / {
      proxy_set_header X-Real-IP $remote_addr;
      proxy_set_header Host $host;
      proxy_http_version 1.1;
      proxy_set_header Connection "";
      if (!-f $request_filename){
          proxy_pass http://webman;
      }
  }
}
```
