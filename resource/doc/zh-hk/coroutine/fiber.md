# 協程

> **協程需求**
> PHP>=8.1 workerman>=5.0 webman-framework>=1.5 revolt/event-loop>1.0.0
> 使用以下指令升級 webman：`composer require workerman/webman-framework ^1.5.0`
> 使用以下指令升級 workerman：`composer require workerman/workerman ^5.0.0`
> 需要安裝 Fiber 協程：`composer require revolt/event-loop ^1.0.0`

# 範例
### 延遲回應

```php
<?php

namespace app\controller;

use support\Request;
use Workerman\Timer;

class TestController
{
    public function index(Request $request)
    {
        // 等待1.5秒
        Timer::sleep(1.5);
        return $request->getRemoteIp();
    }
}
```
`Timer::sleep()` 與 PHP 內建的 `sleep()` 函數相似，不同之處在於 `Timer::sleep()` 不會阻塞進程。

### 發送 HTTP 請求

> **注意**
> 需要安裝 `composer require workerman/http-client ^2.0.0`

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
        $response = $client->get('http://example.com'); // 發出非阻塞的同步請求
        return $response->getBody()->getContents();
    }
}
```
同樣的 `$client->get('http://example.com')` 請求是非阻塞的，可用於在 webman 中非阻塞地發起 HTTP 請求，提升應用性能。

更多資訊參見 [workerman/http-client](https://www.workerman.net/doc/workerman/components/workerman-http-client.html)

### 增加 support\Context 類

`support\Context` 類用於存儲請求上下文數據，當請求完成時，相應的上下文數據將自動刪除。換句話說，上下文數據的生命周期與請求生命周期相同。`support\Context` 支持 Fiber、Swoole、Swow 協程環境。

### Swoole 協程
安裝 swoole 擴展（要求 swoole>=5.0），通過配置 `config/server.php` 開啟 swoole 協程
```php
'event_loop' => \Workerman\Events\Swoole::class,
```

更多資訊請參看 [workerman 事件驅動](https://www.workerman.net/doc/workerman/appendices/event.html)

### 全域變數污染

在協程環境中，禁止將**與請求相關**的狀態信息存儲在全域變數或靜態變數中，因為這可能導致全域變數污染，例如

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

當我們將進程數量設置為 1 時，當我們連續發起兩個請求時  
http://127.0.0.1:8787/test?name=lilei  
http://127.0.0.1:8787/test?name=hanmeimei  
我們期望兩個請求返回的結果分別是 `lilei` 和 `hanmeimei`，但實際上返回的結果都是 `hanmeimei`。
這是因為第二個請求覆蓋了靜態變數 `$name`，當第一個請求睡眠結束時，返回結果找靜態變數 `$name` 已經變成了 `hanmeimei`。

**正確的做法應該是使用上下文存儲請求狀態數據**
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

**區域變數不會導致數據污染**
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
因為 `$name` 是區域變數，協程之間無法相互訪問區局部變數，因此使用區域變數是協程安全的。

# 有關協程
協程並非萬能解決方案，引入協程意味著需要注意全域變數/靜態變數污染問題，需要設置上下文。此外，協程環境中調試 Bugs 比阻塞式編程更為複雜。

實際上，webman 的阻塞式編程已經非常快速，在 [techempower.com](https://www.techempower.com/benchmarks/#section=data-r21&l=zijnjz-6bj&test=db&f=1ekg-cbcw-2t4w-27wr68-pc0-iv9slc-0-1ekgw-39g-kxs00-o0zk-4fu13d-2x8do8-2) 近三年的三輪壓測數據顯示，webman 阻塞式編程與數據庫業務相比，比 go 的 web 框架 gin、echo 等性能高出近 1 倍，比傳統框架 laravel 性能高出近 40 倍。
![](../../assets/img/benchemarks-go-sw.png?)

當數據庫、redis 都在內網時，多進程阻塞式編程性能可能高於協程，這是由於當數據庫、redis 等足夠快時，協程的創建、調度、銷毀開銷可能大於進程切換的開銷，因此在這種情況下引入協程可能無法顯著提升性能。

# 何時使用協程
當業務中存在慢訪問時，例如業務需要訪問第三方接口時，可以使用 [workerman/http-client](https://www.workerman.net/doc/workerman/components/workerman-http-client.html) 以協程方式發出異步 HTTP 調用，提升應用並發能力。