# ビジネスの初期化

時にはプロセスの起動後にビジネスの初期化を行う必要があります。この初期化はプロセスのライフサイクルで一度だけ実行されます。例えば、プロセスの起動後にタイマーを設定したり、データベース接続を初期化したりすることがあります。以下ではこれについて説明します。

## 原理
**[プロセスフロー](process.md)** によると、Webmanはプロセスの起動後に`config/bootstrap.php`（`config/plugin/*/*/bootstrap.php`も含む）で設定されたクラスをロードし、クラスのstartメソッドを実行します。startメソッドにビジネスコードを追加することで、プロセスの起動後のビジネス初期化操作を完了できます。

## プロセス
例として、現在のプロセスのメモリ使用量を定期的に報告するためのタイマーを作成することを考えます。このクラスの名前は`MemReport`とします。

#### コマンドの実行

`php webman make:bootstrap MemReport` コマンドを実行して、初期化ファイル `app/bootstrap/MemReport.php` を生成します。

> **ヒント**
> もしwebmanが`webman/console`をインストールしていない場合は、`composer require webman/console` コマンドを実行してインストールしてください。

#### 初期化ファイルの編集
`app/bootstrap/MemReport.php` を編集し、以下のような内容にします。

```php
<?php

namespace app\bootstrap;

use Webman\Bootstrap;

class MemReport implements Bootstrap
{
    public static function start($worker)
    {
        // コマンドライン環境であるかどうか？
        $is_console = !$worker;
        if ($is_console) {
            // 自分はコマンドライン環境でこの初期化を実行したくない場合は、ここで直接リターンします
            return;
        }
        
        // 10秒ごとに実行
        \Workerman\Timer::add(10, function () {
            // この例では、報告プロセスを代わりに出力しています
            echo memory_get_usage() . "\n";
        });
        
    }

}
```

> **ヒント**
> コマンドラインを使用している場合でも、フレームワークは`config/bootstrap.php`で設定されたstartメソッドを実行します。このため、`$worker`がnullであるかどうかを使って、コマンドライン環境であるかどうかを判断し、それに基づいてビジネス初期化コードを実行するかどうかを決定できます。

#### プロセス起動時の設定
`config/bootstrap.php`を開いて、`MemReport`クラスを起動アイテムに追加します。

```php
return [
    // ...他の設定はここで省略されています...

    app\bootstrap\MemReport::class,
];
```

これでビジネス初期化の流れが完成しました。

## 追加情報
[カスタムプロセス](../process.md)が起動した場合も、`config/bootstrap.php`でstartメソッドが実行されます。`$worker->name`を使用して現在のプロセスが何であるかを判断し、そのプロセスでビジネス初期化コードを実行するかどうかを決定できます。たとえば、monitorプロセスを監視する必要がない場合、`MemReport.php`は以下のような内容になります。

```php
<?php

namespace app\bootstrap;

use Webman\Bootstrap;

class MemReport implements Bootstrap
{
    public static function start($worker)
    {
        // コマンドライン環境であるかどうか？
        $is_console = !$worker;
        if ($is_console) {
            // 自分はコマンドライン環境でこの初期化を実行したくない場合は、ここで直接リターンします
            return;
        }
        
        // monitorプロセスではタイマーを実行しない
        if ($worker->name == 'monitor') {
            return;
        }
        
        // 10秒ごとに実行
        \Workerman\Timer::add(10, function () {
            // この例では、報告プロセスを代わりに出力しています
            echo memory_get_usage() . "\n";
        });
        
    }

}
```
