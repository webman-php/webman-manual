# Redis

Redis là một thành phần mặc định trong webman và sử dụng thư viện [illuminate/redis](https://github.com/illuminate/redis), tức là thư viện redis của laravel, cách sử dụng giống như laravel.

Trước khi sử dụng `illuminate/redis` cần cài đặt extension redis cho `php-cli`.

> **Chú ý**
> Sử dụng lệnh `php -m | grep redis` để kiểm tra xem `php-cli` đã cài đặt extension redis chưa. Lưu ý rằng: ngay cả khi bạn đã cài đặt extension redis cho `php-fpm`, điều này không đồng nghĩa là bạn có thể sử dụng nó trong `php-cli`, vì `php-cli` và `php-fpm` là hai ứng dụng khác nhau và có thể sử dụng cấu hình `php.ini` khác nhau. Sử dụng lệnh `php --ini` để kiểm tra xem `php-cli` sử dụng cấu hình `php.ini` nào.

## Cài đặt

```php
composer require -W illuminate/redis illuminate/events
```

Sau khi cài đặt cần khởi động lại (không hợp lệ nếu dùng reload).


## Cấu hình
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
use support\Redis;

class UserController
{
    public function db(Request $request)
    {
        $key = 'test_key';
        Redis::set($key, rand());
        return response(Redis::get($key));
    }
}
```

## Giao diện Redis
```php
Redis::append($key, $value)
Redis::bitCount($key)
Redis::decr($key, $value)
Redis::decrBy($key, $value)
Redis::get($key)
Redis::getBit($key, $offset)
Redis::getRange($key, $start, $end)
Redis::getSet($key, $value)
Redis::incr($key, $value)
Redis::incrBy($key, $value)
Redis::incrByFloat($key, $value)
Redis::mGet(array $keys)
Redis::getMultiple(array $keys)
Redis::mSet($pairs)
Redis::mSetNx($pairs)
Redis::set($key, $value, $expireResolution = null, $expireTTL = null, $flag = null)
Redis::setBit($key, $offset, $value)
Redis::setEx($key, $ttl, $value)
Redis::pSetEx($key, $ttl, $value)
Redis::setNx($key, $value)
Redis::setRange($key, $offset, $value)
Redis::strLen($key)
Redis::del(...$keys)
Redis::exists(...$keys)
Redis::expire($key, $ttl)
Redis::expireAt($key, $timestamp)
Redis::select($dbIndex)
```
Tương đương với
```php
$redis = Redis::connection('default');
$redis->append($key, $value)
$redis->bitCount($key)
$redis->decr($key, $value)
$redis->decrBy($key, $value)
$redis->get($key)
$redis->getBit($key, $offset)
...
```

> **Chú ý**
> Hãy sử dụng `Redis::select($db)` cẩn thận, vì webman là một framework lưu trữ trong bộ nhớ và nếu một yêu cầu sử dụng `Redis::select($db)` để chuyển đổi cơ sở dữ liệu, nó sẽ ảnh hưởng đến các yêu cầu sau. Đối với nhiều cơ sở dữ liệu, khuyến nghị làm theo cách khác nhau bằng cách cấu hình `$db` khác nhau cho kết nối Redis.

## Sử dụng nhiều kết nối Redis
Ví dụ về tệp cấu hình `config/redis.php`
```php
return [
    'default' => [
        'host'     => '127.0.0.1',
        'password' => null,
        'port'     => 6379,
        'database' => 0,
    ],

    'cache' => [
        'host'     => '127.0.0.1',
        'password' => null,
        'port'     => 6379,
        'database' => 1,
    ],

]
```
Mặc định sử dụng là kết nối được cấu hình trong phần `default`, bạn có thể sử dụng phương thức `Redis::connection()` để chọn kết nối redis nào sẽ sử dụng.
```php
$redis = Redis::connection('cache');
$redis->get('test_key');
```

## Cấu hình nhóm
Nếu ứng dụng của bạn sử dụng cụm máy chủ Redis, bạn nên sử dụng khóa nhóm trong tệp cấu hình Redis để xác định các cụm này:
```php
return [
    'clusters' => [
        'default' => [
            [
                'host'     => 'localhost',
                'password' => null,
                'port'     => 6379,
                'database' => 0,
            ],
        ],
    ],

];
```
Mặc định, nhóm có thể triển khai chia sẻ khách hàng trên các nút, cho phép bạn triển khai hàng loạt nút và tạo ra một lượng lớn bộ nhớ khả dụng. Điều quan trọng ở đây là chia sẻ khách hàng không xử lý tình huống lỗi; do đó, tính năng này chủ yếu phù hợp cho việc lấy dữ liệu cache từ cơ sở dữ liệu chính khác. Nếu muốn sử dụng nhóm gốc của Redis, bạn cần thực hiện cài đặt sau đây trong phần tùy chọn tệp cấu hình:

```php
return[
    'options' => [
        'cluster' => 'redis',
    ],

    'clusters' => [
        // ...
    ],
];
```

## Lệnh đường ống
Khi bạn cần gửi nhiều lệnh đến máy chủ trong một hoạt động, bạn nên sử dụng lệnh đường ống. Phương thức pipeline nhận một closure của Redis instance. Bạn có thể gửi tất cả các lệnh đến Redis instance, chúng sẽ được thực hiện trong một hoạt động:
```php
Redis::pipeline(function ($pipe) {
    for ($i = 0; $i < 1000; $i++) {
        $pipe->set("key:$i", $i);
    }
});
```
