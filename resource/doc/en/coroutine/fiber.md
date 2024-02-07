# Coroutines

> **Requirements**
> PHP>=8.1workerman>=5.0 webman-framework>=1.5 revolt/event-loop>1.0.0
> webman upgrade command `composer require workerman/webman-framework ^1.5.0`
> workerman upgrade command `composer require workerman/workerman ^5.0.0`
> Fiber coroutine requires installation of `composer require revolt/event-loop ^1.0.0`

# Example
### Delayed Response

```php
<?php

namespace app\controller;

use support\Request;
use Workerman\Timer;

class TestController
{
    public function index(Request $request)
    {
        // Sleep for 1.5 seconds
        Timer::sleep(1.5);
        return $request->getRemoteIp();
    }
}
```
`Timer::sleep()` is similar to PHP's built-in `sleep()` function, but the difference is that `Timer::sleep()` does not block the process.


### Sending HTTP Requests

> **Note**
> Requires installation of `composer require workerman/http-client ^2.0.0`

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
        $response = $client->get('http://example.com'); // Asynchronously send a synchronous request
        return $response->getBody()->getContents();
    }
}
```
Similarly, the `$client->get('http://example.com')` request is non-blocking, which can be used to asynchronously send HTTP requests in webman and improve application performance.

For more information, refer to [workerman/http-client](https://www.workerman.net/doc/workerman/components/workerman-http-client.html)

### Adding support\Context Class

The `support\Context` class is used to store request context data. When the request is completed, the corresponding context data is automatically deleted. In other words, the context data's lifespan follows the request's lifespan. `support\Context` supports Fiber, Swoole, and Swow coroutine environments.

### Swoole Coroutine
After installing the Swoole extension (requires swoole>=5.0), enable Swoole coroutine by configuring config/server.php
```php
'event_loop' => \Workerman\Events\Swoole::class,
```

For more information, refer to [workerman event-driven](https://www.workerman.net/doc/workerman/appendices/event.html)

### Global Variable Contamination

In a coroutine environment, it is prohibited to store **request-related** state information in global variables or static variables, as this may lead to global variable contamination. For example:

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

Setting the number of processes to 1, when we make two consecutive requests:  
http://127.0.0.1:8787/test?name=lilei  
http://127.0.0.1:8787/test?name=hanmeimei  
We expect the results of the two requests to be `lilei` and `hanmeimei`, respectively, but actually both return `hanmeimei`.
This is because the second request overwrites the static variable `$name`, and when the first request completes its sleep and returns, the static variable `$name` has already become `hanmeimei`.

**The correct approach is to use context to store request state data:**
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

**Local variables do not cause data contamination:**
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
Because `$name` is a local variable, coroutines cannot access local variables from other coroutines, so using local variables is coroutine-safe.

# About Coroutines
Coroutines are not a silver bullet. Introducing coroutines means that you need to be aware of global variable/static variable contamination and set up the context. Additionally, debugging bugs in a coroutine environment is more complex than in blocking programming.

In webman, blocking programming is already fast enough. According to the latest round of benchmarking data from [techempower.com](https://www.techempower.com/benchmarks/#section=data-r21&l=zijnjz-6bj&test=db&f=1ekg-cbcw-2t4w-27wr68-pc0-iv9slc-0-1ekgw-39g-kxs00-o0zk-4fu13d-2x8do8-2), over the past three years, webman's blocking programming with database business is nearly 2 times faster than Go web frameworks such as gin and echo, and nearly 40 times faster than traditional frameworks like Laravel.
![](../../assets/img/benchemarks-go-sw.png?)

When the database and Redis are on the intranet, blocking programming performance may be higher than coroutine programming. This is because when the database and Redis are fast enough, the overhead of coroutine creation, scheduling, and destruction may be greater than the overhead of process switching. Therefore, introducing coroutines may not significantly improve performance in such cases.

# When to Use Coroutines
Coroutines can be used when there is slow access in the business logic, such as when the business needs to access a third-party API. In such cases, you can use the [workerman/http-client](https://www.workerman.net/doc/workerman/components/workerman-http-client.html) to asynchronously make an HTTP call in a coroutine, thereby improving the application's concurrent capability.
