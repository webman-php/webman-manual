# MongoDB

webman mặc định sử dụng [jenssegers/mongodb](https://github.com/jenssegers/laravel-mongodb) làm thành phần mongodb, nó được trích xuất từ dự án laravel và sử dụng cách tương tự như laravel.

Trước khi sử dụng `jenssegers/mongodb`, bạn cần cài đặt extension mongodb cho `php-cli`.

> Sử dụng lệnh `php -m | grep mongodb` để kiểm tra xem `php-cli` đã cài đặt extension mongodb hay chưa. Lưu ý: Ngay cả khi bạn đã cài đặt extension mongodb cho `php-fpm`, điều này không đảm bảo rằng bạn có thể sử dụng nó trong `php-cli` vì `php-cli` và `php-fpm` là hai ứng dụng khác nhau và có thể sử dụng cấu hình `php.ini` khác nhau. Sử dụng lệnh `php --ini` để xem file cấu hình `php.ini` nào được sử dụng bởi `php-cli`.

## Cài đặt

Đối với PHP>7.2
```php
composer require -W illuminate/database jenssegers/mongodb ^3.8.0
```
Đối với PHP=7.2
```php
composer require -W illuminate/database jenssegers/mongodb ^3.7.0
```

Sau khi cài đặt, bạn cần restart (không phải reload)

## Cấu hình
Trong tập tin `config/database.php`, thêm kết nối `mongodb`, tương tự như sau:
```php
return [

    'default' => 'mysql',

    'connections' => [

         ...Bạn có thể bỏ qua các cấu hình khác...

        'mongodb' => [
            'driver'   => 'mongodb',
            'host'     => '127.0.0.1',
            'port'     =>  27017,
            'database' => 'test',
            'username' => null,
            'password' => null,
            'options' => [
                // ở đây bạn có thể truyền thêm cài đặt cho Mongo Driver Manager
                // xem chi tiết các tham số mà bạn có thể sử dụng tại https://www.php.net/manual/en/mongodb-driver-manager.construct.php trong phần "Uri Options"

                'appname' => 'homestead'
            ],
        ],
    ],
];
```

## Ví dụ
```php
<?php
namespace app\controller;

use support\Request;
use support\Db;

class UserController
{
    public function db(Request $request)
    {
        Db::connection('mongodb')->collection('test')->insert([1,2,3]);
        return json(Db::connection('mongodb')->collection('test')->get());
    }
}
```

## Xem thêm tại

https://github.com/jenssegers/laravel-mongodb
