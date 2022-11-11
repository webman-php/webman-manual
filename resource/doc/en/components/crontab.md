# crontabTimed Task Component

## workerman/crontab

### Description

`workerman/crontab`Similar to the linux crontab, except that `workerman/crontab` supports second-level timing。

Time description：

```
0   1   2   3   4   5
|   |   |   |   |   |
|   |   |   |   |   +------ day of week (0 - 6) (Sunday=0)
|   |   |   |   +------ month (1 - 12)
|   |   |   +-------- day of month (1 - 31)
|   |   +---------- hour (0 - 23)
|   +------------ min (0 - 59)
+-------------- sec (0-59)[can be omitted, if there is no 0 bit, then the minimum time granularity is minutes]
```

### Project address

https://github.com/walkor/crontab
  
### Install
 
```php
composer require workerman/crontab
```
  
### Usage

**Step 1: Create a new process file `process/Task.php`**

```php
<?php
namespace process;

use Workerman\Crontab\Crontab;

class Task
{
    public function onWorkerStart()
    {
    
        // Execute every second
        new Crontab('*/1 * * * * *', function(){
            echo date('Y-m-d H:i:s')."\n";
        });
        
        // Execute every 5 seconds
        new Crontab('*/5 * * * * *', function(){
            echo date('Y-m-d H:i:s')."\n";
        });
        
        // Execute once per minute
        new Crontab('* */1 * * * *', function(){
            echo date('Y-m-d H:i:s')."\n";
        });
        
        // Execute every 5 minutes
        new Crontab('* */5 * * * *', function(){
            echo date('Y-m-d H:i:s')."\n";
        });
        
        // Execute in the first second of every minute
        new Crontab('1 * * * * *', function(){
            echo date('Y-m-d H:i:s')."\n";
        });
      
        // Execute at 7:50 every day, note the omission of the seconds bit here
        new Crontab('50 7 * * *', function(){
            echo date('Y-m-d H:i:s')."\n";
        });
        
    }
}
```
  
**Step 2: Configure the process file to start with webman**
  
Open the configuration file `config/process.php` and add the following configuration

```php
return [
    ....Other configuration, omitted here....
  
    'task'  => [
        'handler'  => process\Task::class
    ],
];
```
  
**Step 3: Restartwebman**

> Note: timed tasks will not be executed immediately, all timed tasks will not start timing until the next minute。


