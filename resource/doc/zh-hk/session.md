# 會話管理

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

通過`$request->session();` 取得`Workerman\Protocols\Http\Session`實例，並通過實例的方法來增加、修改、刪除會話資料。

> 注意：會話對象銷毀時會自動保存會話資料，因此不要將`$request->session()` 返回的對象保存在全局陣列或者類成員中導致會話無法保存。

## 獲取所有會話資料
```php
$session = $request->session();
$all = $session->all();
```
返回的是一個陣列。如果沒有任何會話資料，則返回一個空陣列。

## 獲取會話中某個值
```php
$session = $request->session();
$name = $session->get('name');
```
如果數據不存在則返回null。

你也可以給get方法第二個參數傳遞一個默認值，如果會話陣列中沒找到對應值則返回默認值。例如：
```php
$session = $request->session();
$name = $session->get('name', 'tom');
```

## 存儲會話
存儲某一項數據時用set方法。
```php
$session = $request->session();
$session->set('name', 'tom');
```
set沒有返回值，會話對象銷毀時會自動保存。

當存儲多個值時使用put方法。
```php
$session = $request->session();
$session->put(['name' => 'tom', 'age' => 12]);
```
同樣的，put也沒有返回值。

## 刪除會話資料
刪除某個或者某些會話資料時用`forget`方法。
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

## 獲取並刪除會話某個值
```php
$session = $request->session();
$name = $session->pull('name');
```
效果與如下代碼相同
```php
$session = $request->session();
$value = $session->get($name);
$session->delete($name);
```
如果對應會話不存在，則返回null。

## 刪除所有會話資料
```php
$request->session()->flush();
```
沒有返回值，會話對象銷毀時會自動從存儲中刪除。

## 判斷對應會話資料是否存在
```php
$session = $request->session();
$has = $session->has('name');
```
以上當對應的會話不存在或者對應的會話值為null時返回false，否則返回true。

```php
$session = $request->session();
$has = $session->exists('name');
```
以上代碼也是用來判斷會話數據是否存在，區別是當對應的會話項值為null時，也返回true。

## 助手函數session()
> 2020-12-09 新增

webman提供了助手函數`session()`完成相同的功能。
```php
// 獲取session實例
$session = session();
// 等價於
$session = $request->session();

// 獲取某個值
$value = session('key', 'default');
// 等價於
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
會話設定檔位於`config/session.php`，內容類似如下：

```php
use Webman\Session\FileSessionHandler;
use Webman\Session\RedisSessionHandler;
use Webman\Session\RedisClusterSessionHandler;

return [
    // FileSessionHandler::class 或者 RedisSessionHandler::class 或者 RedisClusterSessionHandler::class 
    'handler' => FileSessionHandler::class,
    
    // handler為FileSessionHandler::class時值為file，
    // handler為RedisSessionHandler::class時值為redis
    // handler為RedisClusterSessionHandler::class時值為redis_cluster 即Redis集群
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

    'session_name' => 'PHPSID', // 存儲session_id的cookie名
    
    // === 以下配置需要 webman-framework>=1.3.14 workerman>=4.0.37 ===
    'auto_update_timestamp' => false,  // 是否自動刷新session，默认關閉
    'lifetime' => 7*24*60*60,          // session過期時間
    'cookie_lifetime' => 365*24*60*60, // 存儲session_id的cookie過期時間
    'cookie_path' => '/',              // 存儲session_id的cookie路徑
    'domain' => '',                    // 存儲session_id的cookie域名
    'http_only' => true,               // 是否開啟httpOnly，默认開啟
    'secure' => false,                 // 僅在https下開啟session，默认關閉
    'same_site' => '',                 // 用於防止CSRF攻擊和使用者追蹤，可選值strict/lax/none
    'gc_probability' => [1, 1000],     // 回收session的機率
];
```

> **備註**
> 從webman 1.4.0 開始，SessionHandler的命名空間已更改為
> use Webman\Session\FileSessionHandler;
> use Webman\Session\RedisSessionHandler;
> use Webman\Session\RedisClusterSessionHandler;
> 原本為
> use Webman\FileSessionHandler;  
> use Webman\RedisSessionHandler;  
> use Webman\RedisClusterSessionHandler;  
> 更改後請留意 namespace 的調整。


## 有效期設定
當webman-framework < 1.3.14 時，webman中session過期時間需在`php.ini`設定。

```ini
session.gc_maxlifetime = x
session.cookie_lifetime = x
session.gc_probability = 1
session.gc_divisor = 1000
```

假設設定有效期為1440秒，則配置如下：
```ini
session.gc_maxlifetime = 1440
session.cookie_lifetime = 1440
session.gc_probability = 1
session.gc_divisor = 1000
```

> **提示**
> 可使用命令 `php --ini` 來查找`php.ini`的位置。
