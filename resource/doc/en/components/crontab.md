# Crontab Timing Task Component

## workerman/crontab

### Description

`workerman/crontab` is similar to the crontab in Linux, the difference is that `workerman/crontab` supports second-level timing.

Time Specification:


```
0   1   2   3   4   5
|   |   |   |   |   |
|   |   |   |   |   +------ day of week (0 - 6) (Sunday=0)
|   |   |   |   +------ month (1 - 12)
|   |   |   +-------- day of month (1 - 31)
|   |   +---------- hour (0 - 23)
|   +------------ min (0 - 59)
+-------------- sec (0-59)[optional, if the 0th position is omitted, the minimum time unit is minutes]
```

### Project Address

https://github.com/walkor/crontab

### Installation

```php
composer require workerman/crontab
```

### Usage

**Step 1: Create a process file `process/Task.php`**

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
        
        // Execute every minute
        new Crontab('0 */1 * * * *', function(){
            echo date('Y-m-d H:i:s')."\n";
        });
        
        // Execute every 5 minutes
        new Crontab('0 */5 * * * *', function(){
            echo date('Y-m-d H:i:s')."\n";
        });
        
        // Execute at the first second of every minute
        new Crontab('1 * * * * *', function(){
            echo date('Y-m-d H:i:s')."\n";
        });
      
        // Execute at 7:50 every day, note that the second position is omitted here
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
    ....other configurations, omitted here....
  
    'task'  => [
        'handler'  => process\Task::class
    ],
];
```

**Step 3: Restart webman**

> Note: Timing tasks will not be executed immediately, all timing tasks will start counting and executing in the next minute.

### Note

Crontab is not asynchronous. For example, a task process sets two timers, A and B, both of which execute a task every second. However, if task A takes 10 seconds to execute, then task B has to wait for A to finish before it can be executed, causing a delay in the execution of task B.
If the business is sensitive to time intervals, it is necessary to run sensitive timing tasks in a separate process to prevent interference from other timing tasks. For example, do the following configuration in `config/process.php`:

```php
return [
    ....other configurations, omitted here....
  
    'task1'  => [
        'handler'  => process\Task1::class
    ],
    'task2'  => [
        'handler'  => process\Task2::class
    ],
];
```

Place time-sensitive timing tasks in `process/Task1.php` and other timing tasks in `process/Task2.php`.

### More

For more `config/process.php` configuration instructions, please refer to [Custom Processes](../process.md)