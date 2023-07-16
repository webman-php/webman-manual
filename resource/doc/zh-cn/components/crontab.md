# crontab定时任务组件

## workerman/crontab

### 说明

`workerman/crontab`类似linux的crontab，不同的是`workerman/crontab`支持秒级定时。

时间说明：

```
0   1   2   3   4   5
|   |   |   |   |   |
|   |   |   |   |   +------ day of week (0 - 6) (Sunday=0)
|   |   |   |   +------ month (1 - 12)
|   |   |   +-------- day of month (1 - 31)
|   |   +---------- hour (0 - 23)
|   +------------ min (0 - 59)
+-------------- sec (0-59)[可省略，如果没有0位,则最小时间粒度是分钟]
```

### 项目地址

https://github.com/walkor/crontab
  
### 安装
 
```php
composer require workerman/crontab
```
  
### 使用

**步骤一：新建进程文件 `process/Task.php`**

```php
<?php
namespace process;

use Workerman\Crontab\Crontab;

class Task
{
    public function onWorkerStart()
    {
    
        // 每秒钟执行一次
        new Crontab('*/1 * * * * *', function(){
            echo date('Y-m-d H:i:s')."\n";
        });
        
        // 每5秒执行一次
        new Crontab('*/5 * * * * *', function(){
            echo date('Y-m-d H:i:s')."\n";
        });
        
        // 每分钟执行一次
        new Crontab('0 */1 * * * *', function(){
            echo date('Y-m-d H:i:s')."\n";
        });
        
        // 每5分钟执行一次
        new Crontab('0 */5 * * * *', function(){
            echo date('Y-m-d H:i:s')."\n";
        });
        
        // 每分钟的第一秒执行
        new Crontab('1 * * * * *', function(){
            echo date('Y-m-d H:i:s')."\n";
        });
      
        // 每天的7点50执行，注意这里省略了秒位
        new Crontab('50 7 * * *', function(){
            echo date('Y-m-d H:i:s')."\n";
        });
        
    }
}
```
  
**步骤二：配置进程文件随webman启动**
  
打开配置文件 `config/process.php`，新增如下配置

```php
return [
    ....其它配置，这里省略....
  
    'task'  => [
        'handler'  => process\Task::class
    ],
];
```
  
**步骤三：重启webman**

> 注意：定时任务不会马上执行，所有定时任务进入下一分钟才会开始计时执行

### 说明
crontab并不是异步的，例如一个task进程里设置了A和B两个定时器，都是每秒执行一次任务，但是A任务耗时10秒，那么B需要等待A执行完才能被执行，导致B执行会有延迟。
如果业务对于时间间隔很敏感，需要将敏感的定时任务放到单独的进程去运行，防止被其它定时任务影响。例如 `config/process.php` 做如下配置

```php
return [
    ....其它配置，这里省略....
  
    'task1'  => [
        'handler'  => process\Task1::class
    ],
    'task2'  => [
        'handler'  => process\Task2::class
    ],
];
```
将时间敏感的定时任务放在 `process/Task1.php` 里，其它定时任务放在 `process/Task2.php` 里

### 更多
更多`config/process.php`配置说明，请参考 [自定义进程](../process.md)
