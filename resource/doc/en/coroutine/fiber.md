# Coroutine

> **Coroutine Requirements**
> PHP>=8.1workerman>=5.0 webman-framework>=1.5 revolt/event-loop>1.0.0
> To upgrade webman, run command: `composer require workerman/webman-framework ^1.5.0`
> To upgrade workerman, run command: `composer require workerman/workerman ^5.0.0`
> Fiber coroutine requires installation of `composer require revolt/event-loop ^1.0.0`

# Examples
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
`Timer::sleep()` is similar to the `sleep()` function in PHP, but the difference is that `Timer::sleep()` does not block the process.


### Making HTTP Requests

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
        $response = $client->get('http://example.com'); // Synchronously initiate an asynchronous request
        return $response->getBody()->getContents();
    }
}
```
Similarly, the `$client->get('http://example.com')` request is non-blocking, which can be used to non-blockingly initiate HTTP requests in webman, improving application performance.

For more information, refer to [workerman/http-client](https://www.workerman.net/doc/workerman/components/workerman-http-client.html)

### Adding the support\Context class

The `support\Context` class is used to store request context data. When the request is completed, the corresponding context data will be automatically deleted. This means that the context data's lifecycle follows the request's lifecycle. `support\Context` supports Fiber, Swoole, and Swow coroutine environments.

### Swoole Coroutine

After installing the Swoole extension (requires swoole>=5.0), enable Swoole coroutine by configuring config/server.php as follows:
```php
'event_loop' => \Workerman\Events\Swoole::class,
```

For more information, refer to [workerman event-driven](https://www.workerman.net/doc/workerman/appendices/event.html)

### Global Variable Pollution

In a coroutine environment, it is forbidden to store **request-related** state information in global variables or static variables, as this may lead to global variable pollution. For example:

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

By setting the number of processes to 1, when we make two consecutive requests:
1. http://127.0.0.1:8787/test?name=lilei
2. http://127.0.0.1:8787/test?name=hanmeimei

We expect the results of the two requests to be `lilei` and `hanmeimei`, respectively. However, in reality, both responses return `hanmeimei`. This is because the second request overrides the static variable `$name`, and by the time the first request finishes sleeping, the static variable `$name` has already become `hanmeimei`.

**The correct approach is to use the context to store request state data**
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

**Local variables do not cause data pollution**
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
Because `$name` is a local variable, coroutines cannot access local variables from each other, so using local variables is coroutine-safe.

# About Coroutines
Coroutines are not a silver bullet. Introducing coroutines means needing to pay attention to the issue of global variable/static variable pollution and setting the context. In addition, debugging bugs in a coroutine environment is more complicated than in a blocking style of programming.

In reality, webman's blocking style of programming is already fast enough. According to the recent three rounds of benchmark data from [techempower.com](https://www.techempower.com/benchmarks/#section=data-r21&l=zijnjz-6bj&test=db&f=1ekg-cbcw-2t4w-27wr68-pc0-iv9slc-0-1ekgw-39g-kxs00-o0zk-4fu13d-2x8do8-2), in database-related businesses, webman's blocking style is nearly twice as performant as Go's web framework Gin, Echo, and others, and nearly 40 times as performant as the traditional framework Laravel.
![](../../assets/img/benchemarks-go-sw.png?)

When the database and Redis are both on the intranet, the performance of multi-process blocking style programming may often be higher than that of coroutines. This is because when the database, Redis, and other components are sufficiently fast, the overhead of coroutine creation, scheduling, and destruction may be greater than the overhead of process switching. Therefore, introducing coroutines at this point may not significantly improve performance.

# When to Use Coroutines
When there are slow accesses in the business, for example, when the business needs to access third-party interfaces, you can use [workerman/http-client](https://www.workerman.net/doc/workerman/components/workerman-http-client.html) to initiate asynchronous HTTP calls using coroutines to improve the application's concurrency capabilities.