# Coroutine

> **Coroutines Requirement**
> PHP >= 8.1 workerman >= 5.0 webman-framework >= 1.5 revolt/event-loop>1.0.0
> To upgrade webman, use the command `composer require workerman/webman-framework ^1.5.0`
> To upgrade workerman, use the command `composer require workerman/workerman ^5.0.0`
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
`Timer::sleep()` is similar to the built-in `sleep()` function in PHP, but the difference is that `Timer::sleep()` does not block the process.

### Sending HTTP Request

> **Note**
> Installation of `composer require workerman/http-client ^2.0.0` is required

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
        $response = $client->get('http://example.com'); // Asynchronous request made using a synchronous method
        return $response->getBody()->getContents();
    }
}
```
Similarly, the request `$client->get('http://example.com')` is non-blocking, which can be used in webman to asynchronously send an HTTP request, thus improving application performance.

For more information, refer to [workerman/http-client](https://www.workerman.net/doc/workerman/components/workerman-http-client.html)

### Addition of the `support\Context` class

The `support\Context` class is used to store the request context data. When the request is completed, the corresponding context data is automatically deleted. In other words, the context data lifecycle follows the request lifecycle. `support\Context` supports Fiber, Swoole, and Swow coroutine environments.

### Swoole Coroutine

After installing the Swoole extension (requires swoole >= 5.0), enable Swoole coroutine by configuring config/server.php:
```php
'event_loop' => \Workerman\Events\Swoole::class,
```

For more information, refer to [workerman event-driven](https://www.workerman.net/doc/workerman/appendices/event.html)

### Global Variable Contamination

In a coroutine environment, it is prohibited to store **request-related** state information in global variables or static variables, as this may lead to global variable contamination, for example:

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

When the number of processes is set to 1, and we make two consecutive requests:
http://127.0.0.1:8787/test?name=lilei
http://127.0.0.1:8787/test?name=hanmeimei
We expect the results of the two requests to be `lilei` and `hanmeimei` respectively, but in reality, both return `hanmeimei`.
This is because the second request overrides the static variable `$name`, and when the first request completes the sleep, the static variable `$name` has already become `hanmeimei`.

**The correct approach is to use context to store the request state data:**

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
Because `$name` is a local variable, coroutines cannot access local variables from one another, making the use of local variables coroutine-safe.

# About Coroutines

Coroutines are not a silver bullet. Introducing coroutines means paying attention to the issue of global variable/static variable contamination and the need to set context. Additionally, debugging bugs in the coroutine environment is more complex than in the blocking programming.

In reality, webman's blocking programming is already fast enough. Based on the benchmark data from [techempower.com](https://www.techempower.com/benchmarks/#section=data-r21&l=zijnjz-6bj&test=db&f=1ekg-cbcw-2t4w-27wr68-pc0-iv9slc-0-1ekgw-39g-kxs00-o0zk-4fu13d-2x8do8-2) over the past three years, webman's blocking programming with database business surpasses Go's web frameworks such as gin and echo by nearly double the performance, and exceeds traditional framework Laravel's performance by nearly 40 times.
![](../../assets/img/benchemarks-go-sw.png?)

When the database and Redis are both within the intranet, the performance of multi-process blocking programming may often be higher than that of coroutines. This is because when the database, Redis, etc., are fast enough, the overhead of coroutine creation, scheduling, and destruction might be greater than the overhead of process switching. Therefore, introducing coroutines may not significantly improve performance in such cases.

# When to Use Coroutines

When there are slow accesses in the business, for example, when the business needs to access a third-party interface, consider using [workerman/http-client](https://www.workerman.net/doc/workerman/components/workerman-http-client.html) to make asynchronous HTTP calls in a coroutine to improve application concurrent capabilities.
