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

透過`$request->session();` 取得`Workerman\Protocols\Http\Session`實例，並使用該實例的方法來新增、修改、刪除session資料。

> 注意：session物件銷毀時會自動保存session資料，因此不要將`$request->session()`返回的物件保存在全局陣列或者類別成員中，以免導致session無法保存。

## 取得所有session資料
```php
$session = $request->session();
$all = $session->all();
```
返回一個陣列。如果沒有任何session資料，則返回一個空陣列。

## 取得session中特定的值
```php
$session = $request->session();
$name = $session->get('name');
```
若資料不存在則返回null。

你也可以給get方法第二個參數傳遞一個預設值，如果session陣列中沒有找到對應值則返回預設值。例如：
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
同樣地，put也沒有返回值。

## 刪除session資料
刪除某個或者某些session資料時使用`forget`方法。
```php
$session = $request->session();
// 刪除一項
$session->forget('name');
// 刪除多項
$session->forget(['name', 'age']);
```

此外系統提供了delete方法，與forget方法區別是，delete只能刪除一項。
```php
$session = $request->session();
// 等同於 $session->forget('name');
$session->delete('name');
```

## 取得並刪除session特定的值
```php
$session = $request->session();
$name = $session->pull('name');
```
效果與如下程式碼相同
```php
$session = $request->session();
$value = $session->get($name);
$session->delete($name);
```
若對應session不存在，則返回null。

## 刪除所有session資料
```php
$request->session()->flush();
```
沒有返回值，session物件銷毀時session會自動從存儲中刪除。

## 判斷對應session資料是否存在
```php
$session = $request->session();
$has = $session->has('name');
```
以上當對應的session不存在或者對應的session值為null時返回false，否則返回true。

```php
$session = $request->session();
$has = $session->exists('name');
```
以上程式碼也是用來判斷session資料是否存在，區別是當對應的session項值為null時，也返回true。

## 助手函數session()
> 2020-12-09 新增

webman提供了助手函數`session()`完成相同的功能。
```php
// 取得session實例
$session = session();
// 等同於
$session = $request->session();

// 取得某個值
$value = session('key', 'default');
// 等同於
$value = session()->get('key', 'default');
// 等同於
$value = $request->session()->get('key', 'default');

// 給session賦值
session(['key1'=>'value1', 'key2' => 'value2']);
// 相當於
session()->put(['key1'=>'value1', 'key2' => 'value2']);
// 相當於
$request->session()->put(['key1'=>'value1', 'key2' => 'value2']);

```
## 設定檔
session的設定檔位於`config/session.php`，內容如下：
```php
use Webman\Session\FileSessionHandler;
use Webman\Session\RedisSessionHandler;
use Webman\Session\RedisClusterSessionHandler;

return [
    // FileSessionHandler::class or RedisSessionHandler::class or RedisClusterSessionHandler::class 
    'handler' => FileSessionHandler::class,
    
    // 當 handler 為 FileSessionHandler::class 時值為 file，
    // 當 handler 為 RedisSessionHandler::class 時值為 redis，
    // 當 handler 為 RedisClusterSessionHandler::class 時值為 redis_cluster 即為 Redis 集群
    'type'    => 'file',

    // 不同的 handler 使用不同的設定
    'config' => [
        // 當 type 為 file 時的設定
        'file' => [
            'save_path' => runtime_path() . '/sessions',
        ],
        // 當 type 為 redis 時的設定
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

    'session_name' => 'PHPSID', // 存儲 session_id 的 cookie 名稱
    
    // === 以下設定需要 webman-framework >= 1.3.14 workerman >= 4.0.37 ===
    'auto_update_timestamp' => false,  // 是否自動刷新 session，默認關閉
    'lifetime' => 7*24*60*60,          // session 過期時間
    'cookie_lifetime' => 365*24*60*60, // 存儲 session_id 的 cookie 過期時間
    'cookie_path' => '/',              // 存儲 session_id 的 cookie 路徑
    'domain' => '',                    // 存儲 session_id 的 cookie 域名
    'http_only' => true,               // 是否啟用 httpOnly，默認啟用
    'secure' => false,                 // 僅在 https 下啟用 session，默認關閉
    'same_site' => '',                 // 用於防止 CSRF 攻擊和使用者追蹤，可選值 strict/lax/none
    'gc_probability' => [1, 1000],     // 回收 session 的機率
];
```

> **注意** 
> webman 從 1.4.0 起更改了SessionHandler的命名空間，由原來的
> use Webman\FileSessionHandler;  
> use Webman\RedisSessionHandler;  
> use Webman\RedisClusterSessionHandler;  
> 改為  
> use Webman\Session\FileSessionHandler;  
> use Webman\Session\RedisSessionHandler;  
> use Webman\Session\RedisClusterSessionHandler;  



## 有效期配置
當 webman-framework < 1.3.14 時，webman 中 session 過期時間需要在 `php.ini` 配置。

```ini
session.gc_maxlifetime = x
session.cookie_lifetime = x
session.gc_probability = 1
session.gc_divisor = 1000
```

假設設定有效期為1440秒，則配置如下
```ini
session.gc_maxlifetime = 1440
session.cookie_lifetime = 1440
session.gc_probability = 1
session.gc_divisor = 1000
```

> **提示**
> 可使用命令 `php --ini` 來查找 `php.ini` 的位置
