# 开启数据库心跳

数据库会关闭长时间不活跃的连接，导致应用发生类似 `mysql server gone away` 错误。

解决办法是定时发送心跳，维持数据库连接活跃避免断开。

> 注意：redis扩展有自动重连机制，所以通过redis扩展访问redis时一般无需心跳维持。

## Laravel的 illuminate/database 开启心跳方法

打开 `config/bootstrap.php` 添加如下配置。

```php
return [
    //  ... 这里省略了其它配置...
    
    support\bootstrap\db\Heartbeat::class,
];
```

## ThinkORM 开启数据库心跳方法

1、新建 support/bootstrap/db/ThinkHeartbeat.php
```php
<?php
namespace support\bootstrap\db;

use Workerman\Worker;
use Workerman\Timer;
use Webman\Bootstrap;
use think\facade\Db;

class ThinkHeartbeat implements Bootstrap
{
    /**
     * @param Worker $worker
     *
     * @return void
     */
    public static function start($worker)
    {
        $connections = config('database.connections');
        if (!$connections) {
            return;
        }
        Timer::add(55, function () use ($connections){
            foreach ($connections as $key => $item) {
                Db::connect($key)->query('select 1 limit 1');
            }
        });
    }
}

```

2、打开 `config/bootstrap.php` 添加如下配置。

```php
return [
    //  ... 这里省略了其它配置...
    
    support\bootstrap\db\ThinkHeartbeat::class,
];
```

## 其它数据库组件

其它数据库组件可以参考 ThinkORM开启数据库心跳方法 开启心跳。
