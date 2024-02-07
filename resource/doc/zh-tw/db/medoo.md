## Medoo

Medoo是一個輕量級的資料庫操作插件，[Medoo官網](https://medoo.in/)。

## 安裝
`composer require webman/medoo`

## 資料庫配置
配置文件位置在 `config/plugin/webman/medoo/database.php`

## 使用
```php
<?php
namespace app\controller;

use support\Request;
use Webman\Medoo\Medoo;

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
> 等同於
> `Medoo::instance('default')->get('user', '*', ['uid' => 1]);`

## 多資料庫配置

**配置**  
`config/plugin/webman/medoo/database.php` 里新增一個配置，key任意，這裡使用的是`other`。

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
        ]
    ],
    // 這裡新增了一個other的配置
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
        ]
    ],
];
```

**使用**
```php
$user = Medoo::instance('other')->get('user', '*', ['uid' => 1]);
```

## 詳細文檔
參見 [Medoo官方文檔](https://medoo.in/api/select)
