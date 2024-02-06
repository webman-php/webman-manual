# MongoDB

webman sử dụng mặc định [jenssegers/mongodb](https://github.com/jenssegers/laravel-mongodb) như một thành phần mongodb, nó đã được tách ra từ dự án laravel và có cách sử dụng tương tự.

Trước khi sử dụng `jenssegers/mongodb`, bạn cần cài đặt extension mongodb cho `php-cli`.

> Sử dụng lệnh `php -m | grep mongodb` để kiểm tra xem `php-cli` đã cài đặt extension mongodb hay chưa. Lưu ý: Ngay cả khi bạn đã cài đặt extension mongodb cho `php-fpm`, điều này không có nghĩa là bạn có thể sử dụng nó trong `php-cli`, vì `php-cli` và `php-fpm` là hai ứng dụng khác nhau và có thể sử dụng cấu hình `php.ini` khác nhau. Sử dụng lệnh `php --ini` để xem bạn đang sử dụng cấu hình `php.ini` nào cho `php-cli`.

## Cài đặt

PHP>7.2
```php
composer require -W illuminate/database jenssegers/mongodb ^3.8.0
```
PHP=7.2
```php
composer require -W illuminate/database jenssegers/mongodb ^3.7.0
```

Sau khi cài đặt, cần phải restart (reload không có tác dụng)

## Cấu hình
Trong tệp `config/database.php`, thêm connection `mongodb`, tương tự như sau:
```php
return [

    'default' => 'mysql',

    'connections' => [

         ...Bỏ qua cấu hình khác ở đây...

        'mongodb' => [
            'driver'   => 'mongodb',
            'host'     => '127.0.0.1',
            'port'     =>  27017,
            'database' => 'test',
            'username' => null,
            'password' => null,
            'options' => [
                // ở đây bạn có thể truyền thêm cài đặt cho Mongo Driver Manager
                // xem https://www.php.net/manual/en/mongodb-driver-manager.construct.php dưới "Uri Options" để biết danh sách đầy đủ các tham số mà bạn có thể sử dụng

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

## Để biết thêm chi tiết, vui lòng truy cập

https://github.com/jenssegers/laravel-mongodb
