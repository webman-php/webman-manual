# 生命週期

## 進程生命週期
- 每個進程都有非常長的生命週期。
- 每個進程都是獨立運行並互不干擾的。
- 每個進程在其生命週期內可以處理多個請求。
- 當進程接收到 `stop` `reload` `restart` 命令時會執行退出，結束本次生命週期。

> **提示**
> 每個進程都是獨立互不干擾的，這意味著每個進程都維護著自己的資源、變數和類實例等。這表示每個進程都有自己的資料庫連接，一些單例在每個進程中初始化一次，這樣多個進程就會初始化多次。

## 請求生命週期
- 每個請求會產生一個 `$request` 對象。
- `$request` 對象在請求處理完畢後會被回收。

## 控制器生命週期
- 每個控制器每個進程只會實例化一次，多個進程實例化多次（除了關閉控制器復用外，詳見[控制器生命週期](https://www.workerman.net/doc/webman/controller.html#%E7%94%9F%E5%91%BD%E5%91%A8%E6%9C%9F)）。
- 控制器實例會被當前進程內的多個請求共享（除了關閉控制器復用外）。
- 控制器生命週期在進程退出後結束（除了關閉控制器復用外）。

## 關於變數生命週期
webman是基於PHP開發的，因此它完全遵循PHP的變數回收機制。業務邏輯中產生的臨時變數包括使用 `new` 關鍵字創建的类實例，都會在函數或方法結束後自動回收，無需手動 `unset` 釋放。也就是說，使用webman開發與傳統框架開發體驗基本一致。例如下面的例子中，`$foo` 實例會隨著 `index` 方法執行完畢而自動釋放：
```php
<?php

namespace app\controller;

use app\service\Foo;
use support\Request;

class IndexController
{
    public function index(Request $request)
    {
        $foo = new Foo(); // 這裡假設有一個Foo類
        return response($foo->sayHello());
    }
}
```
如果你想要某個類的實例被復用，則可以將類保存到類的靜態屬性中或長生命週期的對象（如控制器）的屬性中，也可以使用Container容器的 `get` 方法來初始化類的實例，例如：
```php
<?php

namespace app\controller;

use app\service\Foo;
use support\Container;
use support\Request;

class IndexController
{
    public function index(Request $request)
    {
        $foo = Container::get(Foo::class);
        return response($foo->sayHello());
    }
}
```
`Container::get()` 方法用於創建並保存類的實例，下次再次以同樣的參數調用時將返回之前創建的類實例。

> **注意**
> `Container::get()` 只能初始化沒有構造參數的實例。`Container::make()` 可以創建帶構造函數參數的實例，但是與 `Container::get()` 不同的是，`Container::make()` 並不會復用實例，也就是說即使以同樣的參數 `Container::make()` 始終返回一個新的實例。

## 關於內存洩漏
絕大部分情況下，我們的業務代碼並不會發生內存洩漏（極少有用戶反饋發生內存洩漏），我們只要稍微注意下長生命週期的陣列數據不要無限擴張即可。請看以下代碼：
```php
<?php
namespace app\controller;

use support\Request;

class FooController
{
    // 陣列屬性
    public $data = [];
    
    public function index(Request $request)
    {
        $this->data[] = time();
        return response('hello index');
    }

    public function hello(Request $request)
    {
        return response('hello webman');
    }
}
```
控制器默認是長生命週期的（除了關閉控制器復用外），同樣的控制器的 `$data` 陣列屬性也是長週期的，隨著 `foo/index` 請求不斷增加，`$data` 陣列元素越來越多導致內存洩漏。

更多相關信息請參考[內存洩漏](./memory-leak.md)。
