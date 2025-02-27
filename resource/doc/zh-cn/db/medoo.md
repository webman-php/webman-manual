## Medoo

[webman/medoo](https://github.com/webman-php/medoo)在[Medoo](https://medoo.in/)的基础上增加了连接池功能，并支持协程和非协程环境，用法与Medoo相同。


> **注意**
> 当前手册为 webman v2 版本，如果您使用的是webman v1版本，请查看 [v1版本手册](/doc/webman-v1/db/medoo.html)

## 安装
`composer require webman/medoo`

## 数据库配置
配置文件位置在 `config/plugin/webman/medoo/database.php`

## 使用
```php
<?php
namespace app\controller;

use support\Request;
use support\Medoo;

class Index
{
    public function index(Request $request)
    {
        $user = Medoo::get('user', '*', ['uid' => 1]);
        return json($user);
    }
}
```

> **提示**
> `Medoo::get('user', '*', ['uid' => 1]);`
> 等同于
> `Medoo::instance('default')->get('user', '*', ['uid' => 1]);`

## 多数据库配置

**配置**  
`config/plugin/webman/medoo/database.php` 里新增一个配置，key任意，这里使用的是`other`。

```php
<?php
return [
    'default' => [
        'type' => 'mysql',
        'host' => 'localhost',
        'database' => 'database',
        'username' => 'username',
        'password' => 'password',
        'charset' => 'utf8mb4',
        'collation' => 'utf8mb4_general_ci',
        'port' => 3306,
        'prefix' => '',
        'logging' => false,
        'error' => PDO::ERRMODE_EXCEPTION,
        'option' => [
            PDO::ATTR_CASE => PDO::CASE_NATURAL
        ],
        'command' => [
            'SET SQL_MODE=ANSI_QUOTES'
        ],
        'pool' => [ // 连接池配置
            'max_connections' => 5, // 最大连接数
            'min_connections' => 1, // 最小连接数
            'wait_timeout' => 60,   // 从连接池获取连接等待的最大时间，超时后会抛出异常
            'idle_timeout' => 3,    // 连接池中连接最大空闲时间，超时后会关闭回收，直到连接数为min_connections
            'heartbeat_interval' => 50, // 连接池心跳检测时间，单位秒，建议小于60秒
        ]
    ],
    // 这里新增了一个other的配置
    'other' => [
        'type' => 'mysql',
        'host' => 'localhost',
        'database' => 'database',
        'username' => 'username',
        'password' => 'password',
        'charset' => 'utf8mb4',
        'collation' => 'utf8mb4_general_ci',
        'port' => 3306,
        'prefix' => '',
        'logging' => false,
        'error' => PDO::ERRMODE_EXCEPTION,
        'option' => [
            PDO::ATTR_CASE => PDO::CASE_NATURAL
        ],
        'command' => [
            'SET SQL_MODE=ANSI_QUOTES'
        ],
        'pool' => [
            'max_connections' => 5,
            'min_connections' => 1,
            'wait_timeout' => 60,
            'idle_timeout' => 3,
            'heartbeat_interval' => 50,
        ],
    ],
];
```

**使用**
```php
$user = Medoo::instance('other')->get('user', '*', ['uid' => 1]);
```

## 详细文档
参见 [Medoo官方文档](https://medoo.in/api/select)

