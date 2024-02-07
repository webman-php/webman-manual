# crontab定時任務組件

## workerman/crontab

### 說明

`workerman/crontab`類似於Linux的crontab，不同之處在於`workerman/crontab`支持秒級定時。

時間說明：

```
0   1   2   3   4   5
|   |   |   |   |   |
|   |   |   |   |   +------ 週幾（0 - 6）（星期天=0）
|   |   |   |   +------ 月份（1 - 12）
|   |   |   +-------- 每月的日期（1 - 31）
|   |   +---------- 小時（0 - 23）
|   +------------ 分鐘（0 - 59）
+-------------- 秒（0-59）[可省略，如果沒有0位，則最小時間粒度是分鐘]
```

### 專案地址

https://github.com/walkor/crontab

### 安裝

```php
composer require workerman/crontab
```

### 使用

**步驟一：新建進程文件 `process/Task.php`**

```php
<?php
namespace process;

use Workerman\Crontab\Crontab;

class Task
{
    public function onWorkerStart()
    {
    
        // 每秒鐘執行一次
        new Crontab('*/1 * * * * *', function(){
            echo date('Y-m-d H:i:s')."\n";
        });
        
        // 每5秒執行一次
        new Crontab('*/5 * * * * *', function(){
            echo date('Y-m-d H:i:s')."\n";
        });
        
        // 每分鐘執行一次
        new Crontab('0 */1 * * * *', function(){
            echo date('Y-m-d H:i:s')."\n";
        });
        
        // 每5分鐘執行一次
        new Crontab('0 */5 * * * *', function(){
            echo date('Y-m-d H:i:s')."\n";
        });
        
        // 每分鐘的第一秒執行
        new Crontab('1 * * * * *', function(){
            echo date('Y-m-d H:i:s')."\n";
        });
      
        // 每天的7點50執行，注意這裡省略了秒位
        new Crontab('50 7 * * *', function(){
            echo date('Y-m-d H:i:s')."\n";
        });
        
    }
}
```

**步驟二：配置進程文件隨webman啟動**

打開配置文件 `config/process.php`，新增如下配置

```php
return [
    ....其它配置，這裡省略....
  
    'task'  => [
        'handler'  => process\Task::class
    ],
];
```

**步驟三：重啟webman**

> 注意：定時任務不會馬上執行，所有定時任務進入下一分鐘才會開始計時執行

### 說明

crontab並不是異步的，例如一個task進程裡設置了A和B兩個定時器，都是每秒執行一次任務，但是A任務耗時10秒，那麼B需要等待A執行完才能被執行，導致B執行會有延遲。
如果業務對於時間間隔很敏感，需要將敏感的定時任務放到單獨的進程去運行，防止被其他定時任務影響。例如 `config/process.php` 做如下配置

```php
return [
    ....其它配置，這裡省略....
  
    'task1'  => [
        'handler'  => process\Task1::class
    ],
    'task2'  => [
        'handler'  => process\Task2::class
    ],
];
```
將時間敏感的定時任務放在 `process/Task1.php` 裡，其他定時任務放在 `process/Task2.php` 裡

### 更多

更多`config/process.php`配置說明，請參考[自定義進程](../process.md)
