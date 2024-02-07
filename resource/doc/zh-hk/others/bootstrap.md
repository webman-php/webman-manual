# 業務初始化

有時我們需要在進程啟動後進行一些業務初始化，這個初始化在進程生命週期只執行一次，例如進程啟動後設置一個定時器，或者初始化數據庫連接等。下面我們將對此進行講解。

## 原理
根據**[執行流程](process.md)**中的說明，webman在進程啟動後會加載`config/bootstrap.php`（包括`config/plugin/*/*/bootstrap.php`）中設置的類，並執行類的start方法。我們在start方法中可以加入業務代碼，即可完成進程啟動後業務初始化操作。

## 流程
假設我們要做一個定時器，用於定時上報當前進程的內存佔用，這個類取名為`MemReport`。

#### 執行命令

執行命令 `php webman make:bootstrap MemReport` 生成初始化檔案 `app/bootstrap/MemReport.php`

> **提示**
> 如果你的webman沒有安裝 `webman/console`，執行命令 `composer require webman/console` 安裝

#### 編輯初始化檔案
編輯`app/bootstrap/MemReport.php`，內容類似如下：
```php
<?php

namespace app\bootstrap;

use Webman\Bootstrap;

class MemReport implements Bootstrap
{
    public static function start($worker)
    {
        // 是否是命令行環境 ?
        $is_console = !$worker;
        if ($is_console) {
            // 如果你不想命令行環境執行這個初始化，則在這裡直接返回
            return;
        }
        
        // 每隔10秒執行一次
        \Workerman\Timer::add(10, function () {
            // 為了方便演示，這裡使用輸出代替上報過程
            echo memory_get_usage() . "\n";
        });
        
    }

}
```

> **提示**
> 在使用命令行時，框架也會執行`config/bootstrap.php`配置的start方法，我們可以通過`$worker`是否是null來判斷是否是命令行環境，從而決定是否執行業務初始化代碼。

#### 配置隨進程啟動
打開 `config/bootstrap.php`將`MemReport`類加入到啟動項中。
```php
return [
    // ...這裡省略了其他配置...
    
    app\bootstrap\MemReport::class,
];
```

這樣我們就完成了一個業務初始化流程。

## 補充說明
[自定義進程](../process.md)啟動後也會執行`config/bootstrap.php`配置的start方法，我們可以通過`$worker->name`來判斷當前進程是什麼進程，然後決定是否在該進程執行你的業務初始化代碼，例如我們不需要監控monitor進程，則`MemReport.php`內容類似如下：
```php
<?php

namespace app\bootstrap;

use Webman\Bootstrap;

class MemReport implements Bootstrap
{
    public static function start($worker)
    {
        // 是否是命令行環境 ?
        $is_console = !$worker;
        if ($is_console) {
            // 如果你不想命令行環境執行這個初始化，則在這裡直接返回
            return;
        }
        
        // monitor進程不執行定時器
        if ($worker->name == 'monitor') {
            return;
        }
        
        // 每隔10秒執行一次
        \Workerman\Timer::add(10, function () {
            // 為了方便演示，這裡使用輸出代替上報過程
            echo memory_get_usage() . "\n";
        });
        
    }

}
```
