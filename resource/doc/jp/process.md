# カスタムプロセス

webmanでは、workermanと同様に、リスンやプロセスをカスタマイズすることができます。

> **注意**
> Windowsユーザーは、webmanを起動するために `php windows.php` を使用する必要があります。

## カスタムHTTPサーバ
時には、webmanのHTTPサービスのコアコードをカスタマイズする必要があります。その場合は、カスタムプロセスを使用して実装できます。

例えば、app\Server.phpを新規作成します。

```php
<?php

namespace app;

use Webman\App;

class Server extends App
{
    // ここで、Webman\Appのメソッドを上書きします
}
```

`config/process.php`に以下の設定を追加します。

```php
use Workerman\Worker;

return [
    // ... 他の設定は省略...

    'my-http' => [
        'handler' => app\Server::class,
        'listen' => 'http://0.0.0.0:8686',
        'count' => 8, // プロセス数
        'user' => '',
        'group' => '',
        'reusePort' => true,
        'constructor' => [
            'request_class' => \support\Request::class, // リクエストクラスの設定
            'logger' => \support\Log::channel('default'), // ログのインスタンス
            'app_path' => app_path(), // appディレクトリの場所
            'public_path' => public_path() // publicディレクトリの場所
        ]
    ]
];
```

> **ヒント**
> webmanの組み込みのHTTPプロセスを無効にしたい場合は、 `config/server.php` で `listen=>''` を設定すればよいです。

## カスタムWebSocketリスンの例

`app/Pusher.php`を新規作成します。
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
> 注意：すべてのonXXXプロパティはpublicです

`config/process.php`に以下の設定を追加します。
```php
return [
    // ... 他のプロセスの設定は省略...

    // websocket_test はプロセス名です
    'websocket_test' => [
        // ここで、プロセスクラスを指定します。上記で定義したPusherクラスです
        'handler' => app\Pusher::class,
        'listen'  => 'websocket://0.0.0.0:8888',
        'count'   => 1,
    ],
];
```

## カスタム非リスンプロセスの例

`app/TaskTest.php`を新規作成します。
```php
<?php
namespace app;

use Workerman\Timer;
use support\Db;

class TaskTest
{
  
    public function onWorkerStart()
    {
        // 10秒ごとにデータベースを確認し、新しいユーザーが登録されていないかを確認する
        Timer::add(10, function(){
            Db::table('users')->where('regist_timestamp', '>', time()-10)->get();
        });
    }
    
}
```
`config/process.php`に以下の設定を追加します。
```php
return [
    // ... 他のプロセスの設定は省略...

    'task' => [
        'handler'  => app\TaskTest::class
    ],
];
```

> 注意：listenを省略すると、どのポートにもリスンしません。countを省略すると、プロセス数はデフォルトで1になります。

## 設定ファイルの説明

プロセスの完全な設定は次のように定義されます。
```php
return [
    // ... 

    // websocket_test はプロセス名です
    'websocket_test' => [
        // ここで、プロセスクラスを指定します
        'handler' => app\Pusher::class,
        // リスンするプロトコル、IP、ポート（オプション）
        'listen'  => 'websocket://0.0.0.0:8888',
        // プロセス数（オプション、デフォルトは1）
        'count'   => 2,
        // プロセスの実行ユーザー（オプション、デフォルトは現在のユーザー）
        'user'    => '',
        // プロセスの実行グループ（オプション、デフォルトは現在のユーザーグループ）
        'group'   => '',
        // このプロセスはreloadをサポートしているかどうか（オプション、デフォルトはtrue）
        'reloadable' => true,
        // reusePortを使用するかどうか（オプション、php>=7.0が必要でデフォルトはtrue）
        'reusePort'  => true,
        // transport（オプション、sslを有効にする場合はsslに設定し、デフォルトはtcp）
        'transport'  => 'tcp',
        // context（オプション、transportがsslの場合は、証明書のパスを渡す必要があります）
        'context'    => [], 
        // プロセスクラスのコンストラクタパラメータ、ここではprocess\Pusher::classのコンストラクタパラメータです（オプション）
        'constructor' => [],
    ],
];
```

## 結論
webmanのカスタムプロセスは、実際にはworkermanの簡単なラッパーであり、設定とビジネスロジックを分離し、workermanの `onXXX` コールバックをクラスのメソッドで実装することによって、他の使用法は完全に同じです。
