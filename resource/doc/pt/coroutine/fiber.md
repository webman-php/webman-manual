# Coroutines

> **Coroutines Requirements**
> PHP>=8.1workerman>=5.0 webman-framework>=1.5 revolt/event-loop>1.0.0
> To upgrade webman, use the command `composer require workerman/webman-framework ^1.5.0`
> To upgrade workerman, use the command `composer require workerman/workerman ^5.0.0`
> Fiber coroutines need to be installed using `composer require revolt/event-loop ^1.0.0`

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
`Timer::sleep()` is similar to PHP's `sleep()` function, but the difference is that `Timer::sleep()` does not block the process.

### Sending HTTP Requests

> **Note**
> It requires the installation of `composer require workerman/http-client ^2.0.0`

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
        $response = $client->get('http://example.com'); // Synchronously sends an asynchronous request
        return $response->getBody()->getContents();
    }
}
```
Similarly, the `$client->get('http://example.com')` request is non-blocking, and it can be used to non-blockingly send HTTP requests in webman, improving application performance.

For more information, refer to [workerman/http-client](https://www.workerman.net/doc/workerman/components/workerman-http-client.html)

### Adding support\Context Class

The `support\Context` class is used to store request context data, and when the request is completed, the corresponding context data will be automatically deleted. This means that context data has a lifecycle that follows the request's lifecycle. `support\Context` supports Fiber, Swoole, and Swow coroutine environments.

### Swoole Coroutines

After installing the Swoole extension (requires swoole>=5.0), enable Swoole coroutines through configuration in config/server.php
```php
'event_loop' => \Workerman\Events\Swoole::class,
```
For more information, refer to [workerman event-driven](https://www.workerman.net/doc/workerman/appendices/event.html)

### Global Variable Contamination

Coroutines environment forbids storing **request-related** state information in global or static variables, as this may lead to global variable contamination, for example:

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

When the number of processes is set to 1, and we make two consecutive requests
http://127.0.0.1:8787/test?name=lilei
http://127.0.0.1:8787/test?name=hanmeimei
We expect the results of the two requests to be `lilei` and `hanmeimei`, respectively. However, the actual results are both `hanmeimei`.
This is because the second request overrides the static variable `$name`, and when the first request's sleep ends, the static variable `$name` has already become `hanmeimei`.

**The correct method is to use context to store request state data**
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
Since `$name` is a local variable, coroutines cannot access local variables from each other, so using local variables is coroutine-safe.

# About Coroutines
Coroutines are not a silver bullet, and introducing coroutines means that you need to pay attention to the problem of global variable/static variable contamination and need to set context context. Additionally, debugging bugs in a coroutine environment is more complicated than in blocking programming.

Webman's blocking programming is already fast enough. Based on the recent three rounds of benchmark data from [techempower.com](https://www.techempower.com/benchmarks/#section=data-r21&l=zijnjz-6bj&test=db&f=1ekg-cbcw-2t4w-27wr68-pc0-iv9slc-0-1ekgw-39g-kxs00-o0zk-4fu13d-2x8do8-2), Webman's blocking programming with database business is nearly 1.4 times more efficient than the Go web framework gin, echo, and other frameworks, and almost 40 times more efficient than the traditional Laravel framework.
![](../../assets/img/benchemarks-go-sw.png?)

When the database and Redis are both on the intranet, the performance of multi-process blocking programming may often be higher than that of coroutines. This is because when the database and Redis are fast enough, the overhead of coroutine creation, scheduling, and destruction may be greater than the overhead of process switching. Therefore, introducing coroutines may not significantly improve performance in such cases.

# When to Use Coroutines
When there are slow accesses in the business, such as when the business needs to access a third-party interface, you can use [workerman/http-client](https://www.workerman.net/doc/workerman/components/workerman-http-client.html) to asynchronously send HTTP calls in a coroutine manner, increasing the application's concurrent capabilities.
