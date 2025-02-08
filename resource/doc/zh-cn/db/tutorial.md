# 快速开始

[webman/database](https://github.com/webman-php/database)是基于[illuminate/database](https://github.com/illuminate/database)开发的，并加入了连接池功能，支持协程和非协程环境，用法与laravel相同。

开发者也可以参考[使用其它数据库组件](others.md)章节使用ThinkPHP或者其它数据库。

## 安装

`composer require -W webman/database illuminate/pagination illuminate/events symfony/var-dumper`

安装后需要restart重启(reload无效)

> **提示**
> 如果不需要分页、数据库事件、打印SQL，则只需要执行
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
            'pool' => [ // 连接池配置，仅支持swoole/swow驱动
                'max_connections' => 5, // 最大连接数
                'min_connections' => 1, // 最小连接数
                'wait_timeout' => 3,    // 从连接池获取连接等待的最大时间，超时后会抛出异常
                'idle_timeout' => 60,   // 连接池中连接最大空闲时间，超时后会关闭回收，直到连接数为min_connections
                'heartbeat_interval' => 50, // 连接池心跳检测时间，单位秒，不要小于60
            ],
        ],
    ],
];
```


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
