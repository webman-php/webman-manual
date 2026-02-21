# Bộ giới hạn tốc độ

Bộ giới hạn tốc độ webman, hỗ trợ giới hạn bằng annotation.
Hỗ trợ driver apcu, redis và memory.

## Mã nguồn

https://github.com/webman-php/limiter

## Cài đặt

```
composer require webman/limiter
```

## Sử dụng

```php
<?php
namespace app\controller;

use RuntimeException;
use support\limiter\annotation\Limit;

class UserController
{

    #[Limit(limit: 10)]
    public function index(): string
    {
        // Mặc định giới hạn theo IP, cửa sổ thời gian mặc định 1 giây
        return 'Tối đa 10 yêu cầu mỗi IP mỗi giây';
    }

    #[Limit(limit: 100, ttl: 60, key: Limit::UID)]
    public function search(): string
    {
        // key: Limit::UID, giới hạn theo ID người dùng, yêu cầu session('user.id') không rỗng
        return 'Tối đa 100 tìm kiếm mỗi người dùng mỗi 60 giây';
    }

    #[Limit(limit: 1, ttl: 60, key: Limit::SID, message: 'Chỉ 1 email mỗi người mỗi phút')]
    public function sendMail(): string
    {
        // key: Limit::SID, giới hạn theo session_id
        return 'Email gửi thành công';
    }

    #[Limit(limit: 100, ttl: 24*60*60, key: 'coupon', message: 'Phiếu giảm giá hôm nay đã hết, vui lòng thử lại ngày mai')]
    #[Limit(limit: 1, ttl: 24*60*60, key: Limit::UID, message: 'Mỗi người dùng chỉ có thể nhận một phiếu mỗi ngày')]
    public function coupon(): string
    {
        // key: 'coupon', khóa tùy chỉnh cho giới hạn toàn cục, tối đa 100 phiếu mỗi ngày
        // Cũng giới hạn theo ID người dùng, mỗi người dùng một phiếu mỗi ngày
        return 'Phiếu giảm giá gửi thành công';
    }

    #[Limit(limit: 5, ttl: 24*60*60, key: [UserController::class, 'getMobile'], message: 'Tối đa 5 SMS mỗi số mỗi ngày')]
    public function sendSms2(): string
    {
        // Khi key là biến: [lớp, phương_thức_tĩnh], vd [UserController::class, 'getMobile'] dùng giá trị trả về của UserController::getMobile() làm khóa
        return 'SMS gửi thành công';
    }

    /**
     * Khóa tùy chỉnh, lấy số điện thoại, phải là phương thức tĩnh
     * @return string
     */
    public static function getMobile(): string
    {
        return request()->get('mobile');
    }

    #[Limit(limit: 1, ttl: 10, key: Limit::IP, message: 'Tốc độ bị giới hạn', exception: RuntimeException::class)]
    public function testException(): string
    {
        // Ngoại lệ mặc định khi vượt quá: support\limiter\RateLimitException, thay đổi được qua tham số exception
        return 'ok';
    }

}
```

**Ghi chú**

* Sử dụng thuật toán cửa sổ cố định
* Cửa sổ thời gian ttl mặc định: 1 giây
* Đặt cửa sổ qua ttl, vd `ttl:60` cho 60 giây
* Chiều giới hạn mặc định: IP (mặc định `127.0.0.1` không giới hạn, xem cấu hình bên dưới)
* Tích hợp: giới hạn IP, UID (yêu cầu `session('user.id')` không rỗng), SID (theo `session_id`)
* Khi dùng proxy nginx, truyền header `X-Forwarded-For` cho giới hạn IP, xem [proxy nginx](../others/nginx-proxy.md)
* Kích hoạt `support\limiter\RateLimitException` khi vượt quá, lớp ngoại lệ tùy chỉnh qua `exception:xx`
* Thông báo lỗi mặc định khi vượt quá: `Too Many Requests`, thông báo tùy chỉnh qua `message:xx`
* Thông báo lỗi mặc định cũng sửa được qua [đa ngôn ngữ](translation.md), tham khảo Linux:

```
composer require symfony/translation
mkdir resource/translations/zh_CN/ -p
echo "<?php
return [
    'Too Many Requests' => '请求频率受限'
];" > resource/translations/zh_CN/messages.php
php start.php restart
```

## API

Đôi khi nhà phát triển muốn gọi bộ giới hạn trực tiếp trong mã, xem ví dụ sau:

```php
<?php
namespace app\controller;

use RuntimeException;
use support\limiter\Limiter;

class UserController {

    public function sendSms(string $mobile): string
    {
        // mobile được dùng làm khóa ở đây
        Limiter::check($mobile, 5, 24*60*60, 'Tối đa 5 SMS mỗi số mỗi ngày');
        return 'SMS gửi thành công';
    }
}
```

## Cấu hình

**config/plugin/webman/limiter/app.php**

```
<?php

use support\limiter\RateLimitException;

return [
    'enable' => true,
    'driver' => 'auto', // auto, apcu, memory, redis
    'stores' => [
        'redis' => [
            'connection' => 'default',
        ]
    ],
    // Các IP này không bị giới hạn (chỉ có hiệu lực khi key là Limit::IP)
    'ip_whitelist' => [
        '127.0.0.1',
    ],
    'exception' => RateLimitException::class
];
```

* **enable**: Bật giới hạn tốc độ
* **driver**: Một trong `auto`, `apcu`, `memory`, `redis`; `auto` tự động chọn giữa `apcu` (ưu tiên) và `memory`
* **stores**: Cấu hình Redis, `connection` tương ứng với khóa trong `config/redis.php`
* **ip_whitelist**: Các IP trong whitelist không bị giới hạn (chỉ có hiệu lực khi key là `Limit::IP`)

## Chọn driver

**memory**

* Giới thiệu
  Không cần cài đặt extension, hiệu năng tốt nhất.

* Hạn chế
  Giới hạn chỉ có hiệu lực với tiến trình hiện tại, không chia sẻ dữ liệu giữa các tiến trình, không hỗ trợ giới hạn cluster.

* Trường hợp sử dụng
  Môi trường phát triển Windows; nghiệp vụ không cần giới hạn chặt; phòng thủ tấn công CC.

**apcu**

* Cài đặt extension
  Cần extension apcu, cấu hình php.ini:

```
apc.enabled=1
apc.enable_cli=1
```

Vị trí php.ini bằng lệnh `php --ini`

* Giới thiệu
  Hiệu năng rất tốt, hỗ trợ chia sẻ đa tiến trình.

* Hạn chế
  Không hỗ trợ cluster

* Trường hợp sử dụng
  Mọi môi trường phát triển; giới hạn máy đơn production; cluster không cần giới hạn chặt; phòng thủ tấn công CC.

**redis**

* Phụ thuộc
  Cần extension redis và component Redis, cài đặt:

```
composer require -W webman/redis illuminate/events
```

* Giới thiệu
  Hiệu năng thấp hơn apcu, hỗ trợ giới hạn chính xác máy đơn và cluster

* Trường hợp sử dụng
  Môi trường phát triển; máy đơn production; môi trường cluster
