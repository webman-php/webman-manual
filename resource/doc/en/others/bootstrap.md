# Business Initialization

Sometimes we need to perform business initialization after the process starts, and this initialization only executes once during the process lifecycle, such as setting a timer after the process starts or initializing a database connection. Below, we will explain how to achieve this.

## Principle
According to the explanation in the **[execution process](process.md)**, webman loads the classes set in `config/bootstrap.php` (including `config/plugin/*/*/bootstrap.php`) after the process starts, and then executes the start method of those classes. We can add business code in the start method to complete the initialization operation after the process starts.

## Process
Assuming we need to create a timer to periodically report the memory usage of the current process, we can create a class named `MemReport` to achieve this.

#### Execute the Command

Run the command `php webman make:bootstrap MemReport` to generate the initialization file `app/bootstrap/MemReport.php`.

> **Note**
> If your webman does not have `webman/console` installed, run the command `composer require webman/console` to install it.

#### Edit the Initialization File
Edit `app/bootstrap/MemReport.php` with content similar to the following:
```php
<?php

namespace app\bootstrap;

use Webman\Bootstrap;

class MemReport implements Bootstrap
{
    public static function start($worker)
    {
        // Is it a command-line environment?
        $is_console = !$worker;
        if ($is_console) {
            // If you do not want this initialization to run in the command-line environment, you can directly return here.
            return;
        }
        
        // Run every 10 seconds
        \Workerman\Timer::add(10, function () {
            // For demonstration purposes, output is used here instead of the actual reporting process
            echo memory_get_usage() . "\n";
        });
        
    }

}
```

> **Note**
> When using the command line, the framework will also execute the start method configured in `config/bootstrap.php`. We can use `$worker` to check if it is null to determine if it is a command-line environment, and decide whether to execute the business initialization code.

#### Configure it to Start with the Process
Open `config/bootstrap.php` and add the `MemReport` class to the list of startup items.
```php
return [
    // ...other configuration here...

    app\bootstrap\MemReport::class,
];
```

This completes the business initialization process.

## Additional Explanation
[Custom processes](../process.md) will also execute the start method configured in `config/bootstrap.php` after being started. We can use `$worker->name` to determine the current process and decide whether your business initialization code should be executed in that process. For example, if we do not need to monitor the `monitor` process, the content of `MemReport.php` will be similar to the following:
```php
<?php

namespace app\bootstrap;

use Webman\Bootstrap;

class MemReport implements Bootstrap
{
    public static function start($worker)
    {
        // Is it a command-line environment?
        $is_console = !$worker;
        if ($is_console) {
            // If you do not want this initialization to run in the command-line environment, you can directly return here.
            return;
        }
        
        // Do not execute the timer for the monitor process
        if ($worker->name == 'monitor') {
            return;
        }
        
        // Run every 10 seconds
        \Workerman\Timer::add(10, function () {
            // For demonstration purposes, output is used here instead of the actual reporting process
            echo memory_get_usage() . "\n";
        });
        
    }

}
```