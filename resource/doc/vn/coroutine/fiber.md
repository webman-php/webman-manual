# Coroutines

> **Requirements**
> PHP>=8.1workerman>=5.0 webman-framework>=1.5 revolt/event-loop>1.0.0
> To upgrade webman, use the command: `composer require workerman/webman-framework ^1.5.0`
> To upgrade workerman, use the command: `composer require workerman/workerman ^5.0.0`
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
> Installation required: `composer require workerman/http-client ^2.0.0`

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
Similarly, the `$client->get('http://example.com')` request is non-blocking, which can be used to non-blocking initiate HTTP requests in webman, improving application performance.

For more, refer to [workerman/http-client](https://www.workerman.net/doc/workerman/components/workerman-http-client.html)

### Adding the `support\Context` Class

The `support\Context` class is used to store request context data, and when the request is completed, the corresponding context data will be automatically deleted. In other words, the context data lifecycle follows the request lifecycle. `support\Context` supports Fiber, Swoole, and Swow coroutines.

### Swoole Coroutines
After installing the swoole extension (requires swoole>=5.0), enable swoole coroutines through the configuration in config/server.php
```php
'event_loop' => \Workerman\Events\Swoole::class,
```

For more, refer to [workerman event-driven](https://www.workerman.net/doc/workerman/appendices/event.html)

### Global Variable Contamination

In a coroutine environment, it is forbidden to store **request-related** state information in global variables or static variables, as this may lead to global variable contamination, for example:

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

When we set the number of processes to 1, and then continuously make two requests:
http://127.0.0.1:8787/test?name=lilei
http://127.0.0.1:8787/test?name=hanmeimei
We expect the results of the two requests to be `lilei` and `hanmeimei`, respectively. However, the actual results are both `hanmeimei`.
This is because the second request overrides the static variable `$name`, and when the first request finishes sleeping, the static variable `$name` has already become `hanmeimei`.

**The correct approach is to use context to store request status data:**
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
Since `$name` is a local variable, coroutines cannot access local variables from each other, so using local variables is coroutine safe.

# About Coroutines
Coroutines are not a silver bullet. Introducing coroutines means that attention needs to be paid to global variable/static variable contamination issues and setting context. Additionally, debugging bugs in a coroutine environment is more complex than in blocking programming.

Webman's blocking programming is actually already fast enough. Based on the benchmark data from [techempower.com](https://www.techempower.com/benchmarks/#section=data-r21&l=zijnjz-6bj&test=db&f=1ekg-cbcw-2t4w-27wr68-pc0-iv9slc-0-1ekgw-39g-kxs00-o0zk-4fu13d-2x8do8-2) over the past three years, Webman's blocking programming with database business is nearly 1.5 times faster than the Go web framework Gin, Echo, and nearly 40 times faster than the traditional framework Laravel.
![](../../assets/img/benchemarks-go-sw.png?)

When the database and Redis are in the intranet, the performance of multi-process blocking programming may often be higher than that of coroutines, because when the database, Redis, etc., are fast enough, the overhead of coroutine creation, scheduling, and destruction may be greater than the overhead of process switching. Therefore, introducing coroutines may not significantly improve performance in such cases.

# When to Use Coroutines
Coroutines can be used when there are slow accesses in the business, such as when the business needs to access third-party APIs, coroutines can be used to initiate asynchronous HTTP calls using [workerman/http-client](https://www.workerman.net/doc/workerman/components/workerman-http-client.html) to improve application concurrency capabilities.
