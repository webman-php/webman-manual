# Bộ nhớ cache

Trong webman, mặc định sử dụng [symfony/cache](https://github.com/symfony/cache) làm thành phần bộ nhớ cache.

> Trước khi sử dụng `symfony/cache`, bạn cần cài đặt mở rộng redis cho `php-cli`.

## Cài đặt
**php 7.x**
```php
composer require -W illuminate/redis ^8.2.0 symfony/cache ^5.2
```
**php 8.x**
```php
composer require -W illuminate/redis symfony/cache
```

Sau khi cài đặt, bạn cần restart (không thể reload)

## Cấu hình Redis
Tệp cấu hình redis nằm trong `config/redis.php`
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
> Để tránh xung đột với các doanh nghiệp sử dụng redis khác, hãy thêm một tiền tố cho key.

## Sử dụng các thành phần bộ nhớ cache khác
[Bộ nhớ cache ThinkCache](https://github.com/top-think/think-cache) sử dụng tham khảo [các cơ sở dữ liệu khác](others.md#ThinkCache)
