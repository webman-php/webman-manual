# 协程

> **协程要求**
> PHP>=8.1workerman>=5.0 webman-framework>=1.5 revolt/event-loop>1.0.0
> webman升级命令 `composer require workerman/webman-framework ^1.5.0`
> workerman升级命令 `composer require workerman/workerman ^5.0.0`
> Fiber协程需要安装 `composer require revolt/event-loop ^1.0.0`

# 示例
### 延迟响应

```php
<?php

namespace app\controller;

use support\Request;
use Workerman\Timer;

class TestController
{
    public function index(Request $request)
    {
        // 睡眠1.5秒
        Timer::sleep(1.5);
        return $request->getRemoteIp();
    }
}
```
`Timer::sleep()` 类似 PHP自带的`sleep()`函数，区别是`Timer::sleep()`不会阻塞进程


### 发起HTTP请求

> **注意**
> 需要安装 composer require workerman/http-client ^2.0.0

```php
<?php

namespace app\controller;

use support\Request;
use Workerman\Http\Client;

class TestController
{
    public function index(Request $request)
    {
        static $client;
        $client = $client ?: new Client();
        $response = $client->get('http://example.com'); // 同步方法发起异步请求
        return $response->getBody()->getContents();
    }
}
```
同样的`$client->get('http://example.com')`请求是非阻塞的，这可用于在webman中非阻塞发起http请求，提高应用性能。

更多参考[workerman/http-client](https://www.workerman.net/doc/workerman/components/workerman-http-client.html)

### 增加 support\Context 类

`support\Context`类用于存储请求上下文数据，当请求完成时，相应的context数据会自动删除。也就是说context数据生命周期是跟随请求生命周期的。`support\Context`支持Fiber、Swoole、Swow协程环境。



### Swoole协程
安装swoole扩展(要求swoole>=5.0)后，通过配置config/server.php开启swoole协程
```php
'event_loop' => \Workerman\Events\Swoole::class,
```

更多参考[workerman事件驱动](https://www.workerman.net/doc/workerman/appendices/event.html)

### 全局变量污染

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

# 关于协程
协程不是银弹，引入协程意味着需要注意全局变量/静态变量污染问题，需要设置context上下文。另外协程环境调试bug比阻塞式编程更复杂一些。

webman阻塞式编程实际上已经足够快，通过[techempower.com](https://www.techempower.com/benchmarks/#section=data-r21&l=zijnjz-6bj&test=db&f=1ekg-cbcw-2t4w-27wr68-pc0-iv9slc-0-1ekgw-39g-kxs00-o0zk-4fu13d-2x8do8-2) 最近三年的三轮的压测数据看，webman阻塞式编程带数据库业务比go的web框架gin、echo等性能高近1倍，比传统框架laravel性能高出近40倍。
![](../../assets/img/benchemarks-go-sw.png?)

当数据库、redis都在内网时，多进程阻塞式编程性能可能往往高于协程，这是由于数据库、redis等足够快时，协程创建、调度、销毁的开销可能要大于进程切换的开销，所以这时引入协程并不能显著提升性能。

# 什么时候使用协程
当业务中有慢访问时，例如业务需要访问第三方接口时，可以采用[workerman/http-client](https://www.workerman.net/doc/workerman/components/workerman-http-client.html)以协程的方式发起异步HTTP调用，提高应用并发能力。