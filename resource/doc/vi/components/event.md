# Xử lý sự kiện

`webman/event` cung cấp cơ chế sự kiện tinh tế, cho phép thực hiện một số logic kinh doanh mà không xâm nhập vào mã nguồn, từ đó giảm thiểu sự ràng buộc giữa các module logic kinh doanh. Một ví dụ điển hình là khi có một người dùng mới đăng ký thành công, chỉ cần phát ra một sự kiện tùy chỉnh như `user.register`, các module khác có thể nhận được sự kiện này và thực hiện logic kinh doanh tương ứng.

## Cài đặt
`composer require webman/event`

## Đăng ký sự kiện
Đăng ký sự kiện được thực hiện thông qua tệp `config/event.php`
```php
<?php
return [
    'user.register' => [
        [app\event\User::class, 'register'],
        // ...các hàm xử lý sự kiện khác...
    ],
    'user.logout' => [
        [app\event\User::class, 'logout'],
        // ...các hàm xử lý sự kiện khác...
    ]
];
```
**Giải thích:**
- `user.register`, `user.logout` là tên sự kiện, kiểu chuỗi, khuyến nghị viết thường và phân tách bằng dấu chấm (.)
- Một sự kiện có thể tương ứng với nhiều hàm xử lý sự kiện, thứ tự gọi là thứ tự được cấu hình

## Hàm xử lý sự kiện
Hàm xử lý sự kiện có thể là bất kỳ phương thức lớp, hàm, hàm đóng lại nào.
Ví dụ, tạo lớp xử lý sự kiện `app/event/User.php` (tạo thư mục nếu thư mục không tồn tại)
```php
<?php
namespace app\event;
class User
{
    function register($user)
    {
        var_export($user);
    }
 
    function logout($user)
    {
        var_export($user);
    }
}
```

## Phát ra sự kiện
Sử dụng `Event::emit($event_name, $data);` để phát ra sự kiện, ví dụ
```php
<?php
namespace app\controller;
use support\Request;
use Webman\Event\Event;
class User
{
    public function register(Request $request)
    {
        $user = [
            'name' => 'webman',
            'age' => 2
        ];
        Event::emit('user.register', $user);
    }
}
```

> **Gợi ý**
> Tham số `$data` của `Event::emit($event_name, $data);` có thể là bất kỳ dữ liệu nào, ví dụ mảng, thực thể lớp, chuỗi, v.v.

## Lắng nghe sự kiện thông qua dấu hoa thị
Đăng ký lắng nghe thông qua dấu hoa thị cho phép bạn xử lý nhiều sự kiện trên cùng một trình nghe, ví dụ như được cấu hình trong `config/event.php`
```php
<?php
return [
    'user.*' => [
        [app\event\User::class, 'deal']
    ],
];
```
Chúng ta có thể sử dụng tham số thứ hai `$event_data` trong hàm xử lý sự kiện để nhận tên sự kiện cụ thể
```php
<?php
namespace app\event;
class User
{
    function deal($user, $event_name)
    {
        echo $event_name; // Tên sự kiện cụ thể, như user.register user.logout, v.v.
        var_export($user);
    }
}
```

## Dừng phát lại sự kiện
Khi chúng ta trả về `false` trong hàm xử lý sự kiện, sự kiện sẽ bị dừng lại

## Xử lý sự kiện thông qua hàm đóng lại
Hàm xử lý sự kiện có thể là phương thức lớp, cũng có thể là hàm đóng lại, ví dụ
```php
<?php
return [
    'user.login' => [
        function($user){
            var_dump($user);
        }
    ]
];
```

## Xem sự kiện và trình nghe
Sử dụng lệnh `php webman event:list` để xem tất cả sự kiện và trình nghe đã được cấu hình trong dự án

## Lưu ý
Xử lý sự kiện không phải là bất đồng bộ, không phù hợp để xử lý các hoạt động kinh doanh chậm, các hoạt động kinh doanh chậm nên được xử lý thông qua hàng đợi thông báo, ví dụ như [webman/redis-queue](https://www.workerman.net/plugin/12)
