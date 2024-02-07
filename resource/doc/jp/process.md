# カスタムプロセス

webmanでは、workermanと同様にリスナーやプロセスをカスタマイズできます。

> **注意**
> Windowsユーザーは、カスタムプロセスを起動するために `php windows.php` を使用してwebmanを起動する必要があります。

## カスタムHTTPサービス
時には、特定の要件に合わせてwebmanのHTTPサービスのコアコードを変更する必要があるかもしれません。このような場合は、カスタムプロセスを使用して実装することができます。

たとえば `app\Server.php` を新規作成します。

```php
<?php

namespace app;

use Webman\App;

class Server extends App
{
    // ここで、Webman\App のメソッドをオーバーライドします。
}
```

`config/process.php` に以下の設定を追加します。

```php
use Workerman\Worker;

return [
    // ... 他の設定は省略 ...

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
            'app_path' => app_path(), // アプリのディレクトリ位置
            'public_path' => public_path() // publicのディレクトリ位置
        ]
    ]
];
```

> **ヒント**
> webmanのデフォルトのHTTPプロセスを停止したい場合は、`config/server.php` で `listen=>''` を設定するだけです。

## カスタムWebSocketリスナーの例
`app/Pusher.php` を新規作成します。

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
> 注意：すべてのonXXXプロパティはpublicです。

`config/process.php` に以下の設定を追加します。

```php
return [
    // ... 他のプロセスの設定は省略 ...

    // websocket_test はプロセスの名前です
    'websocket_test' => [
        // ここでプロセスクラスを指定します（上記で定義したPusherクラスです）
        'handler' => app\Pusher::class,
        'listen'  => 'websocket://0.0.0.0:8888',
        'count'   => 1,
    ],
];
```

## カスタム非リスニングプロセスの例
`app/TaskTest.php` を新規作成します。

```php
<?php
namespace app;

use Workerman\Timer;
use support\Db;

class TaskTest
{
  
    public function onWorkerStart()
    {
        // 10秒毎にデータベースが新しいユーザーの登録をチェックします
        Timer::add(10, function(){
            Db::table('users')->where('regist_timestamp', '>', time()-10)->get();
        });
    }
    
}
```
`config/process.php` に以下の設定を追加します。

```php
return [
    // ... 他のプロセスの設定は省略 ...

    'task' => [
        'handler'  => app\TaskTest::class
    ],
];
```

> 注意：`listen` を省略すると、ポートはリスンされません。`count` を省略すると、デフォルトでプロセス数は1になります。

## 設定ファイルの説明

プロセスの完全な設定は以下のように定義されます。

```php
return [
    // ... 

    // websocket_test はプロセスの名前です
    'websocket_test' => [
        // ここでプロセスクラスを指定します
        'handler' => app\Pusher::class,
        // プロトコル、IP、およびポートのリスナー（オプション）
        'listen'  => 'websocket://0.0.0.0:8888',
        // プロセス数（オプション、デフォルト1）
        'count'   => 2,
        // プロセスの実行ユーザー（オプション、現在のユーザーがデフォルト）
        'user'    => '',
        // プロセスの実行ユーザーグループ（オプション、現在のユーザーグループがデフォルト）
        'group'   => '',
        // リロードをサポートするかどうか（オプション、デフォルトでtrue）
        'reloadable' => true,
        // reusePort を有効にするかどうか（オプション、PHP>=7.0以上が必要で、デフォルトでtrue）
        'reusePort'  => true,
        // transport（オプション、SSLを有効にする必要がある場合はsslに設定、デフォルトでtcp）
        'transport'  => 'tcp',
        // context（オプション、transportがsslの場合は証明書のパスを渡す必要があります）
        'context'    => [], 
        // プロセスクラスのコンストラクタパラメータ、ここでは process\Pusher::class クラスのコンストラクタパラメータ（オプション）
        'constructor' => [],
    ],
];
```

## 結論
webmanのカスタムプロセスは実質的にはworkermanの簡単なラッピングです。それは設定とビジネスロジックを分離し、workermanの`onXXX`コールバックをクラスのメソッドで実装します。その他の使用法はworkermanと全く同じです。
