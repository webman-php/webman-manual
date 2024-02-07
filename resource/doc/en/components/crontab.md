# crontab scheduling component

## workerman/crontab

### Description

`workerman/crontab` is similar to the linux crontab, but the difference is that `workerman/crontab` supports scheduling at the second level.

Time format:
```plaintext
0   1   2   3   4   5
|   |   |   |   |   |
|   |   |   |   |   +------ day of week (0 - 6) (Sunday=0)
|   |   |   |   +------ month (1 - 12)
|   |   |   +-------- day of month (1 - 31)
|   |   +---------- hour (0 - 23)
|   +------------ min (0 - 59)
+-------------- sec (0-59) [optional, if not present, the minimum time granularity is minutes]
```

### Project Link

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
        // Run every second
        new Crontab('*/1 * * * * *', function(){
            echo date('Y-m-d H:i:s')."\n";
        });
        
        // Run every 5 seconds
        new Crontab('*/5 * * * * *', function(){
            echo date('Y-m-d H:i:s')."\n";
        });
        
        // Run every minute
        new Crontab('0 */1 * * * *', function(){
            echo date('Y-m-d H:i:s')."\n";
        });
        
        // Run every 5 minutes
        new Crontab('0 */5 * * * *', function(){
            echo date('Y-m-d H:i:s')."\n";
        });
        
        // Run on the first second of every minute
        new Crontab('1 * * * * *', function(){
            echo date('Y-m-d H:i:s')."\n";
        });
      
        // Run at 7:50am every day. Note that the second field is omitted here
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
    // Other configurations...
  
    'task'  => [
        'handler'  => process\Task::class
    ],
];
```
  
**Step 3: Restart webman**

> Note: The scheduled tasks will not execute immediately. They will start counting and executing from the next minute.

### Explanation
Crontab is not asynchronous. For example, if a task process sets two timers, A and B, to run every second, and A takes 10 seconds to complete, then B will have to wait for A to finish before it can be executed, causing a delay in B's execution.
If the business is sensitive to time intervals, it is recommended to run the sensitive tasks in a separate process to prevent them from being affected by other tasks. For example, configure `config/process.php` as follows:

```php
return [
    // Other configurations...
  
    'task1'  => [
        'handler'  => process\Task1::class
    ],
    'task2'  => [
        'handler'  => process\Task2::class
    ],
];
```
Place the sensitive tasks in `process/Task1.php` and other tasks in `process/Task2.php`.

### More
For more configuration options in `config/process.php`, please refer to [Custom Processes](../process.md).
