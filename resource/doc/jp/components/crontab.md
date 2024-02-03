# crontab定期実行コンポーネント

## workerman/crontab

### 説明

`workerman/crontab`はLinuxのcrontabに類似しており、異なるのは`workerman/crontab`が秒単位の定期実行をサポートしていることです。

時間の説明：

```
0   1   2   3   4   5
|   |   |   |   |   |
|   |   |   |   |   +------ 曜日 (0 - 6) (日曜日=0)
|   |   |   |   +------ 月 (1 - 12)
|   |   |   +-------- 月の日 (1 - 31)
|   |   +---------- 時間 (0 - 23)
|   +------------ 分 (0 - 59)
+-------------- 秒 (0-59)[省略可、0位がない場合、時間の最小単位は分]
```

### プロジェクトのリンク

https://github.com/walkor/crontab

### インストール

```php
composer require workerman/crontab
```

### 使用法

**ステップ1：プロセスファイル `process/Task.php` を新規作成**

```php
<?php
namespace process;

use Workerman\Crontab\Crontab;

class Task
{
    public function onWorkerStart()
    {
    
        // 1秒ごとに実行
        new Crontab('*/1 * * * * *', function(){
            echo date('Y-m-d H:i:s')."\n";
        });
        
        // 5秒ごとに実行
        new Crontab('*/5 * * * * *', function(){
            echo date('Y-m-d H:i:s')."\n";
        });
        
        // 1分ごとに実行
        new Crontab('0 */1 * * * *', function(){
            echo date('Y-m-d H:i:s')."\n";
        });
        
        // 5分ごとに実行
        new Crontab('0 */5 * * * *', function(){
            echo date('Y-m-d H:i:s')."\n";
        });
        
        // 1分の1秒ごとに実行
        new Crontab('1 * * * * *', function(){
            echo date('Y-m-d H:i:s')."\n";
        });
      
        // 毎日7時50分に実行、ここでは秒の位が省略されていることに注意
        new Crontab('50 7 * * *', function(){
            echo date('Y-m-d H:i:s')."\n";
        });
        
    }
}
```

**ステップ2：プロセスファイルをwebmanの起動時に設定**

設定ファイル `config/process.php` を開き、以下の設定を追加する

```php
return [
    ....他の設定は省略....
  
    'task'  => [
        'handler'  => process\Task::class
    ],
];
```

**ステップ3：webmanを再起動**

> 注意：定期実行タスクは直ちに実行されず、全てのタスクは次の分になってから実行が開始されます。

### 説明
crontabは非同期ではありません。例えば、1つのタスクプロセスにAとBの2つのタイマーが設定されており、それぞれ1秒ごとにタスクを実行するが、Aのタスクが10秒かかる場合、BはAの実行が終わるまで待たなければならず、Bの実行に遅延が発生します。
時間間隔に敏感なビジネスの場合は、時間に敏感なタイマータスクを別々のプロセスで実行し、他のタイマータスクに影響を受けないようにする必要があります。例えば `config/process.php` で以下のように設定します。

```php
return [
    ....他の設定は省略....
  
    'task1'  => [
        'handler'  => process\Task1::class
    ],
    'task2'  => [
        'handler'  => process\Task2::class
    ],
];
```
時間に敏感な定期タスクを `process/Task1.php` に配置し、他の定期タスクを`process/Task2.php`に配置します。

### その他
`config/process.php` のさらなる設定については [カスタムプロセス](../process.md) を参照してください。
