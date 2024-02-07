# ビジネスの初期化

時にはプロセス起動後にビジネスの初期化を行う必要があります。この初期化はプロセスのライフサイクルで一度だけ実行され、例えばプロセス起動後にタイマーを設定したり、データベース接続を初期化したりすることができます。以下ではこれについて説明します。

## 原理
**[実行フロー](process.md)**に従って、webmanはプロセスの起動後に`config/bootstrap.php`（`config/plugin/*/*/bootstrap.php`も含む）で設定されたクラスを読み込み、クラスのstartメソッドを実行します。startメソッドにビジネスコードを追加することで、プロセスの起動後のビジネス初期化を完了させることができます。

## フロー
定期的に現在のプロセスのメモリ使用状況を報告するためのタイマーを作成する場合を考えてみます。このクラスの名前を`MemReport`とします。

#### コマンドの実行

`php webman make:bootstrap MemReport`コマンドを実行して、初期化ファイル `app/bootstrap/MemReport.php`を生成します。

> **ヒント**
> もしwebmanが`webman/console`をインストールしていない場合は、`composer require webman/console`コマンドを実行してください。

#### 初期化ファイルの編集
`app/bootstrap/MemReport.php`を編集し、以下のような内容にします。
```php
<?php

namespace app\bootstrap;

use Webman\Bootstrap;

class MemReport implements Bootstrap
{
    public static function start($worker)
    {
        // コマンドライン環境か？
        $is_console = !$worker;
        if ($is_console) {
            // コマンドライン環境でこの初期化を実行したくない場合は、ここで直接戻ります
            return;
        }
        
        // 10秒ごとに実行
        \Workerman\Timer::add(10, function () {
            // デモ目的で、ここでは報告プロセスを代用して出力を使用しています
            echo memory_get_usage() . "\n";
        });
        
    }

}
```

> **ヒント**
> コマンドラインを使用する際、フレームワークは`config/bootstrap.php`で設定されたstartメソッドも実行します。私たちは`$worker`がnullであるかどうかでコマンドライン環境かどうかを判断し、それに基づいてビジネスの初期化コードを実行するかどうかを決定することができます。

#### プロセス起動時の設定
`config/bootstrap.php`を開いて、`MemReport`クラスを起動項目に追加してください。
```php
return [
    // ...その他の設定は省略...
    
    app\bootstrap\MemReport::class,
];
```

これでビジネスの初期化フローを完成させました。

## 補足説明
[カスタムプロセス](../process.md)は起動後に`config/bootstrap.php`で設定されたstartメソッドも実行されます。私たちは`$worker->name`を使用して現在のプロセスがどのプロセスかを判断し、そのプロセスでビジネスの初期化コードを実行するかどうかを決定することができます。例えば、monitorプロセスを監視する必要がない場合、`MemReport.php`の内容は以下のようになります。
```php
<?php

namespace app\bootstrap;

use Webman\Bootstrap;

class MemReport implements Bootstrap
{
    public static function start($worker)
    {
        // コマンドライン環境か？
        $is_console = !$worker;
        if ($is_console) {
            // コマンドライン環境でこの初期化を実行したくない場合は、ここで直接戻ります
            return;
        }
        
        // monitorプロセスはタイマーを実行しない
        if ($worker->name == 'monitor') {
            return;
        }
        
        // 10秒ごとに実行
        \Workerman\Timer::add(10, function () {
            // デモ目的で、ここでは報告プロセスを代用して出力を使用しています
            echo memory_get_usage() . "\n";
        });
        
    }

}
```
