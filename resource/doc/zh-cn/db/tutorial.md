# 快速开始

[webman/database](https://github.com/webman-php/database)是基于[illuminate/database](https://github.com/illuminate/database)开发的，并加入了连接池功能，支持协程和非协程环境，用法与laravel相同。

开发者也可以参考[使用其它数据库组件](others.md)章节使用ThinkPHP或者其它数据库。

> **注意**
> 当前手册为 webman-v2 版本，如果您使用的是webman-v1版本，请查看 [v1版本手册](/doc/webman-v1/db/tutorial.html)

## 安装

`composer require -W webman/database illuminate/pagination illuminate/events symfony/var-dumper`

安装后需要restart重启(reload无效)

> **提示**
> 如果不需要分页、数据库事件、记录SQL，则只需要执行
> `composer require -W webman/database`

## 数据库配置
`config/database.php`
```php

return [
    // 默认数据库
    'default' => 'mysql',

    // 各种数据库配置
    'connections' => [
        'mysql' => [
            'driver'      => 'mysql',
            'host'        => '127.0.0.1',
            'port'        => 3306,
            'database'    => 'test',
            'username'    => 'root',
            'password'    => '',
            'unix_socket' => '',
            'charset'     => 'utf8',
            'collation'   => 'utf8_unicode_ci',
            'prefix'      => '',
            'strict'      => true,
            'engine'      => null,
            'options' => [
                PDO::ATTR_EMULATE_PREPARES => false, // 当使用swoole或swow作为驱动时是必须的
            ],
            'pool' => [ // 连接池配置
                'max_connections' => 5, // 最大连接数
                'min_connections' => 1, // 最小连接数
                'wait_timeout' => 3,    // 从连接池获取连接等待的最大时间，超时后会抛出异常。仅在协程环境有效
                'idle_timeout' => 60,   // 连接池中连接最大空闲时间，超时后会关闭回收，直到连接数为min_connections
                'heartbeat_interval' => 50, // 连接池心跳检测时间，单位秒，建议小于60秒
            ],
        ],
    ],
];
```

## 关于连接池
* 每个进程有自己的连接池，进程间不共享连接池。
* 不开启协程时，业务在进程内排队执行，不会产生并发，所以连接池最多只有1个连接。
* 开启协程后，业务在进程内并发执行，连接池会根据需要动态调整连接数，最多不超过`max_connections`，最少不小于`min_connections`。
* 因为连接池连接数最大为`max_connections`，当操作数据库的协程数大于`max_connections`时，会有协程排队等待，最多等待`wait_timeout`秒，超过则触发异常。
* 在空闲的情况下(包括协程和非协程环境)，连接会在`idle_timeout`时间后被回收，直到连接数为`min_connections`(`min_connections`可为0)。


## 使用
```php
<?php
namespace app\controller;

use support\Request;
use support\Db;

class UserController
{
    public function db(Request $request)
    {
        $default_uid = 29;
        $uid = $request->get('uid', $default_uid);
        $name = Db::table('users')->where('uid', $uid)->value('username');
        return response("hello $name");
    }
}
```
