# session管理

## 範例
```php
<?php
namespace app\controller;

use support\Request;

class UserController
{
    public function hello(Request $request)
    {
        $name = $request->get('name');
        $session = $request->session();
        $session->set('name', $name);
        return response('hello ' . $session->get('name'));
    }
}
```

透過`$request->session();` 取得`Workerman\Protocols\Http\Session`實例，並使用這個實例的方法來新增、修改、刪除session資料。

> 注意：session物件銷毀時會自動保存session資料，因此請不要將`$request->session()`儲存在全域陣列或類別成員中，否則會導致session無法保存。

## 取得所有session資料
```php
$session = $request->session();
$all = $session->all();
```
回傳一個陣列。如果沒有任何session資料，則回傳一個空陣列。



## 取得session中的某個值
```php
$session = $request->session();
$name = $session->get('name');
```
如果資料不存在則回傳null。

您也可以給get方法第二個參數傳遞一個預設值，如果session陣列中沒有找到對應的值則回傳預設值。例如：
```php
$session = $request->session();
$name = $session->get('name', 'tom');
```


## 儲存session
當儲存某一項資料時使用set方法。
```php
$session = $request->session();
$session->set('name', 'tom');
```
set沒有返回值，session物件銷毀時session會自動保存。

當儲存多個值時使用put方法。
```php
$session = $request->session();
$session->put(['name' => 'tom', 'age' => 12]);
```
同樣的，put也沒有返回值。

## 刪除session資料
刪除某個或某些session資料時使用`forget`方法。
```php
$session = $request->session();
// 刪除一項
$session->forget('name');
// 刪除多項
$session->forget(['name', 'age']);
```

另外系統提供了delete方法，與forget方法區別是，delete只能刪除一項。
```php
$session = $request->session();
// 等同於 $session->forget('name');
$session->delete('name');
```

## 取得並刪除session某個值
```php
$session = $request->session();
$name = $session->pull('name');
```
效果與下述程式碼相同
```php
$session = $request->session();
$value = $session->get($name);
$session->delete($name);
```
如果對應session不存在，則回傳null。


## 刪除所有session資料
```php
$request->session()->flush();
```
沒有返回值，session物件銷毀時session會自動從儲存中刪除。


## 判斷對應session資料是否存在
```php
$session = $request->session();
$has = $session->has('name');
```
以上當對應的session不存在或者對應的session值為null時回傳false，否則回傳true。

```
$session = $request->session();
$has = $session->exists('name');
```
以上程式碼也是用來判斷session資料是否存在，區別是當對應的session項值為null時，也回傳true。

## 助手函數session()
> 2020-12-09 新增

webman提供了助手函數`session()`完成相同的功能。
```php
// 取得session實例
$session = session();
// 等價於
$session = $request->session();

// 取得某個值
$value = session('key', 'default');
// 等於
$value = session()->get('key', 'default');
// 等於
$value = $request->session()->get('key', 'default');

// 給session賦值
session(['key1'=>'value1', 'key2' => 'value2']);
// 相當於
session()->put(['key1'=>'value1', 'key2' => 'value2']);
// 相當於
$request->session()->put(['key1'=>'value1', 'key2' => 'value2']);

```

## 設定檔
session設定檔位於`config/session.php`，內容類似如下：
```php
use Webman\Session\FileSessionHandler;
use Webman\Session\RedisSessionHandler;
use Webman\Session\RedisClusterSessionHandler;

return [
    // FileSessionHandler::class 或者 RedisSessionHandler::class 或者 RedisClusterSessionHandler::class 
    'handler' => FileSessionHandler::class,
    
    // handler為FileSessionHandler::class時值為file，
    // handler為RedisSessionHandler::class時值為redis
    // handler為RedisClusterSessionHandler::class時值為redis_cluster 即redis集群
    'type'    => 'file',

    // 不同的handler使用不同的配置
    'config' => [
        // type為file時的配置
        'file' => [
            'save_path' => runtime_path() . '/sessions',
        ],
        // type為redis時的配置
        'redis' => [
            'host'      => '127.0.0.1',
            'port'      => 6379,
            'auth'      => '',
            'timeout'   => 2,
            'database'  => '',
            'prefix'    => 'redis_session_',
        ],
        'redis_cluster' => [
            'host'    => ['127.0.0.1:7000', '127.0.0.1:7001', '127.0.0.1:7001'],
            'timeout' => 2,
            'auth'    => '',
            'prefix'  => 'redis_session_',
        ]
        
    ],

    'session_name' => 'PHPSID', // 儲存session_id的cookie名
    
    // === 以下配置需要 webman-framework>=1.3.14 workerman>=4.0.37 ===
    'auto_update_timestamp' => false,  // 是否自動刷新session，默认关闭
    'lifetime' => 7*24*60*60,          // session過期時間
    'cookie_lifetime' => 365*24*60*60, // 儲存session_id的cookie過期時間
    'cookie_path' => '/',              // 儲存session_id的cookie路徑
    'domain' => '',                    // 儲存session_id的cookie域名
    'http_only' => true,               // 是否開啟httpOnly，默认開啟
    'secure' => false,                 // 僅在https下開啟session，默认關閉
    'same_site' => '',                 // 用於防止CSRF攻擊和用戶追蹤，可選值strict/lax/none
    'gc_probability' => [1, 1000],     // 回收session的機率
];
```

> **注意** 
> webman從1.4.0起更改了SessionHandler的命名空間，由原來的
> use Webman\FileSessionHandler;  
> use Webman\RedisSessionHandler;  
> use Webman\RedisClusterSessionHandler;  
> 改為  
> use Webman\Session\FileSessionHandler;  
> use Webman\Session\RedisSessionHandler;  
> use Webman\Session\RedisClusterSessionHandler;  



## 有效期設定
當webman-framework < 1.3.14時，webman中session過期時間需要在`php.ini`配置。

```
session.gc_maxlifetime = x
session.cookie_lifetime = x
session.gc_probability = 1
session.gc_divisor = 1000
```

假設設定有效期為1440秒，則配置如下
```
session.gc_maxlifetime = 1440
session.cookie_lifetime = 1440
session.gc_probability = 1
session.gc_divisor = 1000
```

> **提示**
> 可使用命令 `php --ini` 來尋找`php.ini`的位置