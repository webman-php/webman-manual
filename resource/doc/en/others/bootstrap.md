# Business Initialization

Sometimes, we need to perform some business initialization after the process starts. This initialization is only performed once during the process lifecycle, such as setting up a timer or initializing database connections after the process starts. In the following sections, we will explain how to achieve this.

## Principle
According to the explanation in the [Execution Process](process.md) section, webman loads the classes set in `config/bootstrap.php` (including `config/plugin/*/*/bootstrap.php`) after the process starts, and executes the `start` method of these classes. We can add our business code to the `start` method to complete the business initialization after the process starts.

## Procedure
Let's say we want to create a timer that reports the memory usage of the current process periodically. We'll name this class `MemReport`.

#### Execute Command

Run the command `php webman make:bootstrap MemReport` to generate the initialization file `app/bootstrap/MemReport.php`.

> **Note**
> If your webman does not have `webman/console` installed, run the command `composer require webman/console` to install it.

#### Edit Initialization File
Edit `app/bootstrap/MemReport.php`, and the content will look like the following:

```php
<?php

namespace app\bootstrap;

use Webman\Bootstrap;

class MemReport implements Bootstrap
{
    public static function start($worker)
    {
        // Is it a command line environment?
        $is_console = !$worker;
        if ($is_console) {
            // If you don't want this initialization to be executed in the command line environment, return directly here.
            return;
        }

        // Execute every 10 seconds
        \Workerman\Timer::add(10, function () {
            // For the sake of demonstration, we use output instead of reporting process.
            echo memory_get_usage() . "\n";
        });
    }
}
```

> **Note**
> The framework also executes the `start` method configured in `config/bootstrap.php` when using the command line. We can determine whether it is a command line environment by checking whether `$worker` is `null`, and decide whether to execute the business initialization code accordingly.

#### Configuration Starts with the Process
Open `config/bootstrap.php` and add the `MemReport` class to the list of startup items.
```php
return [
    // ... Other configurations are omitted here...
    
    app\bootstrap\MemReport::class,
];
```

In this way, we have completed the business initialization process.

## Additional Explanation
[Custom Processes](../process.md) also execute the `start` method configured in `config/bootstrap.php`. We can use `$worker->name` to determine the current process and decide whether to execute your business initialization code in that process. For example, if we do not need to monitor the monitor process, the content of `MemReport.php` will be as follows:

```php
<?php

namespace app\bootstrap;

use Webman\Bootstrap;

class MemReport implements Bootstrap
{
    public static function start($worker)
    {
        // Is it a command line environment?
        $is_console = !$worker;
        if ($is_console) {
            // If you don't want this initialization to be executed in the command line environment, return directly here.
            return;
        }
        
        // Do not execute the timer in the monitor process.
        if ($worker->name == 'monitor') {
            return;
        }
        
        // Execute every 10 seconds
        \Workerman\Timer::add(10, function () {
            // For the sake of demonstration, we use output instead of reporting process.
            echo memory_get_usage() . "\n";
        });
        
    }

}
```
