# 业务初始化

有时我们需要在进程启动后做一些业务初始化，这个初始化在进程生命周期只执行一次，例如进程启动后设置一个定时器，或者初始化数据库连接等。下面我们将对此进行讲解。

## 原理
根据 **[执行流程](process.md)** 中的说明，webman在进程启动后会加载`config/bootstrap.php`(包括`config/plugin/*/*/bootstrap.php`)中设置的类，并执行类的start方法。我们在start方法中可以加入业务代码，即可完成进程启动后业务初始化操作。

## 流程
假设我们要做一个定时器，用于定时上报当前进程的内存占用，这个类取名为`MemReport`。

#### 执行命令

执行命令 `php webman make:bootstrap MemReport` 生成初始化文件 `app/bootstrap/MemReport.php`

> **提示**
> 如果你的webman没有安装 `webman/console`，执行命令 `composer require webman/console` 安装

#### 编辑初始化文件
编辑`app/bootstrap/MemReport.php`，内容类似如下：
```php
<?php

namespace app\bootstrap;

use Webman\Bootstrap;

class MemReport implements Bootstrap
{
    public static function start($worker)
    {
        // 是否是命令行环境 ?
        $is_console = !$worker;
        if ($is_console) {
            // 如果你不想命令行环境执行这个初始化，则在这里直接返回
            return;
        }
        
        // 每隔10秒执行一次
        \Workerman\Timer::add(10, function () {
            // 为了方便演示，这里使用输出代替上报过程
            echo memory_get_usage() . "\n";
        });
        
    }

}
```

> **提示**
> 在使用命令行时，框架也会执行`config/bootstrap.php`配置的start方法，我们可以通过`$worker`是否是null来判断是否是命令行环境，从而决定是否执行业务初始化代码。

#### 配置随进程启动
打开 `config/bootstrap.php`将`MemReport`类加入到启动项中。
```php
return [
    // ...这里省略了其它配置...
    
    app\bootstrap\MemReport::class,
];
```

这样我们就完成了一个业务初始化流程。

## 补充说明
[自定义进程](../process.md)启动后也会执行`config/bootstrap.php`配置的start方法，我们可以通过`$worker->name` 来判断当前进程是什么进程，然后决定是否在该进程执行你的业务初始化代码，例如我们不需要监控monitor进程，则`MemReport.php`内容类似如下：
```php
<?php

namespace app\bootstrap;

use Webman\Bootstrap;

class MemReport implements Bootstrap
{
    public static function start($worker)
    {
        // 是否是命令行环境 ?
        $is_console = !$worker;
        if ($is_console) {
            // 如果你不想命令行环境执行这个初始化，则在这里直接返回
            return;
        }
        
        // monitor进程不执行定时器
        if ($worker->name == 'monitor') {
            return;
        }
        
        // 每隔10秒执行一次
        \Workerman\Timer::add(10, function () {
            // 为了方便演示，这里使用输出代替上报过程
            echo memory_get_usage() . "\n";
        });
        
    }

}
```
