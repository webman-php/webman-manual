# Bộ đệm Cache

Trong webman, mặc định sử dụng [symfony/cache](https://github.com/symfony/cache) làm thành phần cache.

> Trước khi sử dụng `symfony/cache`, bạn cần cài đặt phần mở rộng redis cho `php-cli`.

## Cài đặt
**php 7.x**
```php
composer require -W illuminate/redis ^8.2.0 symfony/cache ^5.2
```
**php 8.x**
```php
composer require -W illuminate/redis symfony/cache
```

Sau khi cài đặt, bạn cần restart (reload không có tác dụng)

## Cấu hình Redis
Tệp cấu hình redis nằm tại `config/redis.php`
```php
return [
    'default' => [
        'host'     => '127.0.0.1',
        'password' => null,
        'port'     => 6379,
        'database' => 0,
    ]
];
```

## Ví dụ
```php
<?php
namespace app\controller;

use support\Request;
use support\Cache;

class UserController
{
    public function db(Request $request)
    {
        $key = 'test_key';
        Cache::set($key, rand());
        return response(Cache::get($key));
    }
}
```

> **Lưu ý**
> Hãy cố gắng thêm một tiền tố cho key để tránh xung đột với các doanh nghiệp sử dụng redis khác.

## Sử dụng thành phần Cache khác
Bạn có thể tham khảo cách sử dụng thành phần [ThinkCache](https://github.com/top-think/think-cache) tại [cơ sở dữ liệu khác](others.md#ThinkCache)
