# Coroutine

> **Coroutine Requirements**
> PHP >= 8.1, Workerman >= 5.0, webman-framework >= 1.5, revolt/event-loop > 1.0.0
> Upgrade webman command `composer require workerman/webman-framework ^1.5.0`
> Upgrade workerman command `composer require workerman/workerman ^5.0.0`
> Installation of Fiber coroutine is required `composer require revolt/event-loop ^1.0.0`

# Example
### Delayed response

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

### Making HTTP requests

> **Note**
> Installation needed `composer require workerman/http-client ^2.0.0`

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
        $response = $client->get('http://example.com'); // Synchronously making an asynchronous request
        return $response->getBody()->getContents();
    }
}
```
The request `$client->get('http://example.com')` is non-blocking, allowing non-blocking initiation of HTTP requests in webman, which enhances application performance.

For more information, see [workerman/http-client](https://www.workerman.net/doc/workerman/components/workerman-http-client.html)

### Adding the support\Context class

The class `support\Context` is used to store request context data, and when the request is completed, the corresponding context data is automatically deleted. This means that the lifetime of context data follows the request lifetime. `support\Context` supports Fiber, Swoole, and Swow coroutine environments.

### Swoole Coroutine

After installing the swoole extension (required swoole >= 5.0), enable the swoole coroutine through the configuration config/server.php
```php
'event_loop' => \Workerman\Events\Swoole::class,
```
For more information, see [workerman event-driven](https://www.workerman.net/doc/workerman/appendices/event.html)

### Global Variable Contamination

In the coroutine environment, it is forbidden to store **request-related** status information in global or static variables, as this could lead to global variable contamination, for example

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
When setting the number of processes to 1, and making two continuous requests:
http://127.0.0.1:8787/test?name=lilei
http://127.0.0.1:8787/test?name=hanmeimei
We expect the results of the two requests to be `lilei` and `hanmeimei`, but in reality, both responses are `hanmeimei`.
This is because the second request has overridden the static variable `$name`, so by the time the first request finishes sleeping, the static variable `$name` has already become `hanmeimei`.

**The correct method is to use context storage for request status data**
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

**Local variables do not cause data contamination**
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
Coroutines are not a silver bullet. Introducing coroutines means paying attention to global/static variable contamination issues, and setting context contexts. Additionally, debugging bugs in coroutine environments is more complex than in blocking programming.

In fact, webman's blocking programming is already fast enough. According to the [techempower.com](https://www.techempower.com/benchmarks/#section=data-r21&l=zijnjz-6bj&test=db&f=1ekg-cbcw-2t4w-27wr68-pc0-iv9slc-0-1ekgw-39g-kxs00-o0zk-4fu13d-2x8do8-2) recent three-year round-robin stress test data, webman's blocking programming with database business outperforms web frameworks such as gin and echo of go by nearly 1 time, and is nearly 40 times faster than traditional framework laravel.
![](../../assets/img/benchemarks-go-sw.png?)

When the database, Redis, and other services are in the intranet, the performance of multi-process blocking programming is often higher than that of coroutines. This is because when the database, Redis, and other services are fast enough, the overhead of coroutine creation, scheduling, and destruction may be greater than the overhead of process switching, so introducing coroutines may not significantly improve performance.

# When to Use Coroutines
When there are slow accesses in the business, such as when the business needs to access third-party APIs, it can use [workerman/http-client](https://www.workerman.net/doc/workerman/components/workerman-http-client.html) to make asynchronous HTTP calls in a coroutine, which improves the application's concurrent capability.
