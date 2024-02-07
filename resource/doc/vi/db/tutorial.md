# Bắt đầu nhanh chóng

webman sử dụng mặc định cơ sở dữ liệu [illuminate/database](https://github.com/illuminate/database), tức là [cơ sở dữ liệu của laravel](https://learnku.com/docs/laravel/8.x/database/9400), và cách sử dụng tương tự như laravel.

Tất nhiên bạn cũng có thể tham khảo chương trình [Sử dụng các thành phần cơ sở dữ liệu khác](others.md) để sử dụng ThinkPHP hoặc cơ sở dữ liệu khác.

## Cài đặt

`composer require -W illuminate/database illuminate/pagination illuminate/events symfony/var-dumper`

Sau khi cài đặt, bạn cần phải khởi động lại (reload không có hiệu lực)

> **Lưu ý**
> Nếu không cần phân trang, sự kiện cơ sở dữ liệu, in SQL, chỉ cần thực hiện
> `composer require -W illuminate/database`

## Cấu hình cơ sở dữ liệu
`config/database.php`
```php
return [
    // Cơ sở dữ liệu mặc định
    'default' => 'mysql',

    // Cấu hình cơ sở dữ liệu khác nhau
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
