# 關於記憶體洩漏
webman是一個常駐記憶體框架，所以我們需要稍微關注一下記憶體洩漏的情況。不過開發者不必過於擔心，因為記憶體洩漏發生在非常極端的條件下，而且很容易規避。webman開發與傳統框架開發體驗基本一致，不必為記憶體管理做多餘的操作。

> **提示**
> webman自帶的monitor進程會監控所有進程記憶體使用情況，如果進程使用記憶體即將達到php.ini裡`memory_limit`設定的值時，會自動安全重啟對應的進程，達到釋放記憶體的作用，期間對業務沒有影響。

## 記憶體洩漏定義
隨著請求的不斷增加，webman佔用的記憶體也**無限增加**(注意是**無限增加**)，達到幾百M甚至更多，這種是記憶體洩漏。
如果是記憶體有增長，但是後面不再增長不算記憶體洩漏。

一般進程佔用幾十M記憶體是很正常的情況，當進程處理超大請求或者維護海量連接時，單個進程記憶體佔用可能會達到上百M也是常有的事。這部分記憶體使用後php可能並不會全部交還操作系統。而是留著複用，所以可能會出現處理某個大請求後記憶體佔用變大不釋放記憶體的情況，這是正常現象。(調用gc_mem_caches()方法可以釋放部分空閒記憶體)

## 記憶體洩漏是如何發生的
**記憶體洩漏發生必須滿足以下兩個條件：**
1. 存在**長生命週期的**陣列(注意是長生命週期的陣列，普通陣列沒事)
2. 並且這個**長生命週期的**陣列會無限擴張(業務無限向其插入數據，從不清理數據)

如果1 2條件**同時滿足**(注意是同時滿足)，那麼將會產生記憶體洩漏。反之不滿足以上條件或者只滿足其中一個條件則不是記憶體洩漏。

## 長生命週期的陣列
webman裡長生命週期的陣列包括：
1. static關鍵字的陣列
2. 單例的陣列屬性
3. global關鍵字的陣列

> **注意**
> webman中允許使用長生命週期的數據，但是需要保證數據內的數據是有限的，元素個數不會無限擴張。

以下分別舉例說明

#### 無限膨脹的static陣列
```php
class Foo
{
    public static $data = [];
    public function index(Request $request)
    {
        self::$data[] = time();
        return response('hello');
    }
}
```

以`static`關鍵字定義的`$data`陣列是長生命週期的陣列，並且示例中`$data`陣列隨著請求不斷增加而不斷膨脹，導致記憶體洩漏。

#### 無限膨脹的單例陣列屬性
```php
class Cache
{
    protected static $instance;
    public $data = [];
    
    public function instance()
    {
        if (!self::$instance) {
            self::$instance = new self;
        }
        return self::$instance;
    }
    
    public function set($key, $value)
    {
        $this->data[$key] = $value;
    }
}
```

調用程式碼
```php
class Foo
{
    public function index(Request $request)
    {
        Cache::instance()->set(time(), time());
        return response('hello');
    }
}
```

`Cache::instance()`返回一個Cache單例，它是一個長生命週期的類實例，雖然它的`$data`屬性雖然沒有使用`static`關鍵字，但是由於類本身是長生命週期，所以`$data`也是長生命週期的陣列。隨著不斷向`$data`陣列裡添加不同key的數據，程式佔用記憶體也越來越大，造成記憶體洩漏。

> **注意**
> 如果 Cache::instance()->set(key, value) 添加的key是有限數量的，則不會記憶體洩漏，因為`$data`陣列並沒有無限膨脹。

#### 無限膨脹的global陣列
```php
class Index
{
    public function index(Request $request)
    {
        global $data;
        $data[] = time();
        return response($foo->sayHello());
    }
}
```
global 關鍵字定義的陣列並不會在函數或者類方法執行完畢後回收，所以它是長生命週期的陣列，以上程式碼隨著請求不斷增加會產生記憶體洩漏。同理在函數或者方法內以static關鍵字定義的陣列也是長生命週期的陣列，如果陣列無限膨脹也會記憶體洩漏，例如：
```php
class Index
{
    public function index(Request $request)
    {
        static $data = [];
        $data[] = time();
        return response($foo->sayHello());
    }
}
```

## 建議
建議開發者不用特別關注記憶體洩漏，因為它極少發生，如果不幸發生我們可以通過壓測找到哪段程式碼產生洩漏，從而定位出問題。即使開發者沒有找到洩漏點，webman自帶的monitor服務會適時安全重啟發生記憶體洩漏的進程，釋放記憶體。

如果你實在想盡量規避記憶體洩漏，可以參考以下建議。
1. 盡量不使用`global`,`static`關鍵字的陣列，如果使用確保其不會無限膨脹
2. 對於不熟悉的類，盡量不使用單例，用new關鍵字初始化。如果需要單例，則查看其是否有無限膨脹的陣列屬性