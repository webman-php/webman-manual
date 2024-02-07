# 自定義進程

在webman中，您可以像workerman那樣自定義監聽或進程。

> **注意**
> Windows使用者需要使用 `php windows.php` 啟動webman才能啟動自定義進程。

## 自定義HTTP服務
有時候您可能有某種特殊的需求，需要更改webman HTTP服務的內核代碼，這時可以採用自定義進程來實現。

例如新增 app\Server.php

```php
<?php

namespace app;

use Webman\App;

class Server extends App
{
    // 這裡重寫了 Webman\App 裡的方法
}
```

在 `config/process.php` 中添加如下配置

```php
use Workerman\Worker;

return [
    // ... 這裡省略了其他配置...
    
    'my-http' => [
        'handler' => app\Server::class,
        'listen' => 'http://0.0.0.0:8686',
        'count' => 8, // 進程數
        'user' => '',
        'group' => '',
        'reusePort' => true,
        'constructor' => [
            'request_class' => \support\Request::class, // request類設置
            'logger' => \support\Log::channel('default'), // 日誌實例
            'app_path' => app_path(), // app目錄位置
            'public_path' => public_path() // public目錄位置
        ]
    ]
];
```

> **提示**
> 如果想關閉webman自帶的HTTP進程，只需要在 config/server.php 裡設置 `listen=>''`

## 自定義WebSocket監聽範例

新增 `app/Pusher.php`
```php
<?php
namespace app;

use Workerman\Connection\TcpConnection;

class Pusher
{
    public function onConnect(TcpConnection $connection)
    {
        echo "onConnect\n";
    }

    public function onWebSocketConnect(TcpConnection $connection, $http_buffer)
    {
        echo "onWebSocketConnect\n";
    }

    public function onMessage(TcpConnection $connection, $data)
    {
        $connection->send($data);
    }

    public function onClose(TcpConnection $connection)
    {
        echo "onClose\n";
    }
}
```
> 注意: 所有 onXXX 屬性均為public

在 `config/process.php` 中添加如下配置
```php
return [
    // ... 其他進程配置省略 ...
    
    // websocket_test 為進程名稱
    'websocket_test' => [
        // 這裡指定進程類，就是上面定義的Pusher類
        'handler' => app\Pusher::class,
        'listen'  => 'websocket://0.0.0.0:8888',
        'count'   => 1,
    ],
];
```

## 自定義非監聽進程範例
新增 `app/TaskTest.php`
```php
<?php
namespace app;

use Workerman\Timer;
use support\Db;

class TaskTest
{
  
    public function onWorkerStart()
    {
        // 每隔10秒檢查一次數據庫是否有新使用者註冊
        Timer::add(10, function(){
            Db::table('users')->where('regist_timestamp', '>', time()-10)->get();
        });
    }
    
}
```
在 `config/process.php` 中添加如下配置
```php
return [
    // ... 其他進程配置省略
    
    'task' => [
        'handler'  => app\TaskTest::class
    ],
];
```

> 注意: listen省略則不監聽任何端口，count省略則進程數預設為1。

## 配置文件說明

一個進程完整的配置定義如下：
```php
return [
    // ... 
    
    // websocket_test 為進程名稱
    'websocket_test' => [
        // 這裡指定進程類
        'handler' => app\Pusher::class,
        // 監聽的協議 ip 及端口 （可選）
        'listen'  => 'websocket://0.0.0.0:8888',
        // 進程數 （可選，預設1）
        'count'   => 2,
        // 進程運行使用者 （可選，預設當前使用者）
        'user'    => '',
        // 進程運行使用者組 （可選，預設當前使用者組）
        'group'   => '',
        // 目前進程是否支持reload （可選，預設true）
        'reloadable' => true,
        // 是否開啟reusePort （可選，此選項需要PHP>=7.0，預設為true）
        'reusePort'  => true,
        // transport (可選，當需要開啟SSL時設置為ssl，預設為tcp)
        'transport'  => 'tcp',
        // context （可選，當transport為是ssl時，需要傳遞證書路徑）
        'context'    => [], 
        // 進程類構造函數參數，這裡為 process\Pusher::class 類的構造函數參數 （可選）
        'constructor' => [],
    ],
];
```

## 總結
webman的自定義進程實際上就是workerman的一個簡單封裝，它將配置與業務分離，並且將workerman的`onXXX`回調通過類的方法來實現，其它用法與workerman完全相同。
