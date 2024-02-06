# Bắt đầu nhanh

Cơ sở dữ liệu của webman mặc định sử dụng [illuminate/database](https://github.com/illuminate/database), cũng chính là [cơ sở dữ liệu của Laravel](https://learnku.com/docs/laravel/8.x/database/9400), sử dụng cách tương tự như Laravel.

Tất nhiên bạn có thể tham khảo chương trình [Sử dụng các thành phần cơ sở dữ liệu khác](others.md) để sử dụng ThinkPHP hoặc các cơ sở dữ liệu khác.

## Cài đặt

`composer require -W illuminate/database illuminate/pagination illuminate/events symfony/var-dumper`

Sau khi cài đặt, cần restart lại (reload không có tác dụng)

> **Lưu ý**
> Nếu không cần phân trang, sự kiện cơ sở dữ liệu, in câu lệnh SQL, chỉ cần chạy
> `composer require -W illuminate/database`

## Cấu hình cơ sở dữ liệu
`config/database.php`
```php
return [
    // Cơ sở dữ liệu mặc định
    'default' => 'mysql',

    // Các cấu hình cơ sở dữ liệu khác nhau
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
                \PDO::ATTR_TIMEOUT => 3
            ]
        ],
    ],
];
```

## Sử dụng
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
        return response("xin chào $name");
    }
}
```
