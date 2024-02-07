## Medoo

Medoo là một plugin thao tác cơ sở dữ liệu nhẹ, [Trang chủ của Medoo](https://medoo.in/).

## Cài đặt
`composer require webman/medoo`

## Cấu hình database
Tệp cấu hình nằm tại `config/plugin/webman/medoo/database.php`

## Sử dụng
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

> **Gợi ý**
> `Medoo::get('user', '*', ['uid' => 1]);`
> Tương đương với
> `Medoo::instance('default')->get('user', '*', ['uid' => 1]);`

## Cấu hình nhiều cơ sở dữ liệu

**Cấu hình**  
Thêm một cấu hình mới trong tệp `config/plugin/webman/medoo/database.php`, sử dụng key bất kỳ, ở đây sử dụng `other`.

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
    // Thêm một cấu hình other mới tại đây
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

**Sử dụng**
```php
$user = Medoo::instance('other')->get('user', '*', ['uid' => 1]);
```

## Tài liệu chi tiết
Xem chi tiết tại [Tài liệu chính thức của Medoo](https://medoo.in/api/select)
