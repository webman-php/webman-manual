# 協程

> **協程要求**
> PHP>=8.1 workerman>=5.0 webman-framework>=1.5 revolt/event-loop>1.0.0
> webman升級命令 `composer require workerman/webman-framework ^1.5.0`
> workerman升級命令 `composer require workerman/workerman ^5.0.0`
> Fiber協程需要安裝 `composer require revolt/event-loop ^1.0.0`

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
        // 睡眠1.5秒
        Timer::sleep(1.5);
        return $request->getRemoteIp();
    }
}
```
`Timer::sleep()` 類似 PHP自帶的`sleep()`函數，區別是`Timer::sleep()`不會阻塞進程


### 發起HTTP請求

> **注意**
> 需要安裝 composer require workerman/http-client ^2.0.0

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
        $response = $client->get('http://example.com'); // 同步方法發起異步請求
        return $response->getBody()->getContents();
    }
}
```
同樣的`$client->get('http://example.com')`請求是非阻塞的，這可用於在webman中非阻塞發起http請求，提高應用性能。

更多參考[workerman/http-client](https://www.workerman.net/doc/workerman/components/workerman-http-client.html)

### 增加 support\Context 類

`support\Context`類用於存儲請求上下文數據，當請求完成時，相應的context數據會自動刪除。也就是說context數據生命週期是跟隨請求生命周期的。`support\Context`支持Fiber、Swoole、Swow協程環境。

### Swoole協程
安裝swoole擴展(要求swoole>=5.0)後，通過配置config/server.php開啟swoole協程
```php
'event_loop' => \Workerman\Events\Swoole::class,
```

更多參考[workerman事件驅動](https://www.workerman.net/doc/workerman/appendices/event.html)

### 全局變數污染

協程環境禁止將**請求相關**的狀態信息存儲在全局變數或者靜態變數中，因為這可能會導致全局變數污染，例如

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
將進程數設置為1，當我們連續發起兩個請求時  
http://127.0.0.1:8787/test?name=lilei  
http://127.0.0.1:8787/test?name=hanmeimei  
我們期望兩個請求返回的結果分別是 `lilei` 和 `hanmeimei`，但實際上返回的都是`hanmeimei`。
這是因為第二個請求將靜態變數`$name`覆蓋了，第一個請求睡眠結束時返回時靜態變數`$name`已經成為`hanmeimei`。

**正確但方法應該是使用context存儲請求狀態數據**
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

**局部變量不會造成數據污染**
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
因為`$name`是局部變量，協程之間無法互相訪問局部變量，所以使用局部變量是協程安全的。

# 關於協程
協程不是銀彈，引入協程意味著需要注意全局變數/靜態變數污染問題，需要設置context上下文。另外協程環境調試bug比阻塞式編程更複雜一些。

webman阻塞式編程實際上已經足夠快，通過[techempower.com](https://www.techempower.com/benchmarks/#section=data-r21&l=zijnjz-6bj&test=db&f=1ekg-cbcw-2t4w-27wr68-pc0-iv9slc-0-1ekgw-39g-kxs00-o0zk-4fu13d-2x8do8-2) 最近三年的三輪的壓測數據看，webman阻塞式編程帶數據庫業務比go的web框架gin、echo等性能高近1倍，比傳統框架laravel性能高出近40倍。
![](../../assets/img/benchemarks-go-sw.png?)

當數據庫、redis都在內網時，多進程阻塞式編程性能可能往往高於協程，這是由於數據庫、redis等足夠快時，協程創建、調度、銷毀的開銷可能要大於進程切換的開銷，所以這時引入協程並不能顯著提升性能。

# 什麼時候使用協程
當業務中有慢訪問時，例如業務需要訪問第三方接口時，可以採用[workerman/http-client](https://www.workerman.net/doc/workerman/components/workerman-http-client.html)以協程的方式發起異步HTTP調用，提高應用並發能力。
