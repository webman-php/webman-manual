# Tiến trình tùy chỉnh

Trong webman, bạn có thể tùy chỉnh dịch vụ lắng nghe hoặc tiến trình giống như trong workerman.

> **Lưu ý**
> Người dùng Windows cần sử dụng `php windows.php` để khởi chạy webman để sử dụng các tiến trình tùy chỉnh.

## Tùy chỉnh dịch vụ HTTP
Đôi khi bạn có thể có nhu cầu đặc biệt nào đó và cần thay đổi mã lõi dịch vụ http của webman. Khi đó, bạn có thể sử dụng tiến trình tùy chỉnh để thực hiện điều này.

Ví dụ tạo mới app\Server.php

```php
<?php

namespace app;

use Webman\App;

class Server extends App
{
    // Ở đây, chúng ta ghi đè lên phương thức trong Webman\App
}
```

Thêm cấu hình sau vào `config/process.php`

```php
use Workerman\Worker;

return [
    // ... Bỏ qua cấu hình khác...

    'my-http' => [
        'handler' => app\Server::class,
        'listen' => 'http://0.0.0.0:8686',
        'count' => 8, // Số tiến trình
        'user' => '',
        'group' => '',
        'reusePort' => true,
        'constructor' => [
            'request_class' => \support\Request::class, // Thiết lập lớp yêu cầu
            'logger' => \support\Log::channel('default'), // Thực thể nhật ký
            'app_path' => app_path(), // Vị trí thư mục app
            'public_path' => public_path() // Vị trí thư mục public
        ]
    ]
];
```

> **Gợi ý**
> Nếu muốn tắt tiến trình http có sẵn trong webman, chỉ cần thiết lập `listen=>''` trong config/server.php

## Ví dụ về lắng nghe WebSocket tùy chỉnh

Tạo mới `app/Pusher.php`
```php
<?php
namespace app;

use Workerman\Connection\TcpConnection;

class Pusher
{
    public function onConnect(TcpConnection $connection)
    {
        echo "onConnect\n";
    }

    public function onWebSocketConnect(TcpConnection $connection, $http_buffer)
    {
        echo "onWebSocketConnect\n";
    }

    public function onMessage(TcpConnection $connection, $data)
    {
        $connection->send($data);
    }

    public function onClose(TcpConnection $connection)
    {
        echo "onClose\n";
    }
}
```
> Lưu ý: Tất cả các thuộc tính onXXX đều là public

Thêm cấu hình sau vào `config/process.php`
```php
return [
    // ... Bỏ qua cấu hình tiến trình khác ...

    // websocket_test là tên tiến trình
    'websocket_test' => [
        // Ở đây chúng ta chỉ định lớp tiến trình, đó chính là lớp Pusher được định nghĩa ở trên
        'handler' => app\Pusher::class,
        'listen'  => 'websocket://0.0.0.0:8888',
        'count'   => 1,
    ],
];
```

## Ví dụ về tiến trình không lắng nghe tùy chỉnh

Tạo mới `app/TaskTest.php`
```php
<?php
namespace app;

use Workerman\Timer;
use support\Db;

class TaskTest
{
  
    public function onWorkerStart()
    {
        // Kiểm tra cơ sở dữ liệu mỗi 10 giây xem có người dùng mới đăng ký hay không
        Timer::add(10, function(){
            Db::table('users')->where('regist_timestamp', '>', time()-10)->get();
        });
    }
    
}
```
Thêm cấu hình sau vào `config/process.php`
```php
return [
    // ... Bỏ qua cấu hình tiến trình khác...

    'task' => [
        'handler'  => app\TaskTest::class
    ],
];
```

> Lưu ý: Nếu không định nghĩa listen, tiến trình sẽ không lắng nghe bất kỳ cổng nào, nếu bỏ qua count, số tiến trình mặc định sẽ là 1.

## Giải thích cấu hình

Cấu hình đầy đủ của một tiến trình là như sau:
```php
return [
    // ... 
    
    // websocket_test là tên tiến trình
    'websocket_test' => [
        // Ở đây chúng ta chỉ định lớp tiến trình
        'handler' => app\Pusher::class,
        // Giao thức, địa chỉ ip và cổng lắng nghe (tùy chọn)
        'listen'  => 'websocket://0.0.0.0:8888',
        // Số tiến trình (tùy chọn, mặc định là 1)
        'count'   => 2,
        // Người dùng chạy tiến trình (tùy chọn, mặc định là người dùng hiện tại)
        'user'    => '',
        // Nhóm người dùng chạy tiến trình (tùy chọn, mặc định nhóm người dùng hiện tại)
        'group'   => '',
        // Tiến trình hiện tại có hỗ trợ reload hay không (tùy chọn, mặc định là true)
        'reloadable' => true,
        // Có bật reusePort hay không (tùy chọn, yêu cầu PHP>=7.0, mặc định là true)
        'reusePort'  => true,
        // transport (tùy chọn, khi cần bật ssl, thiết lập là ssl, mặc định là tcp)
        'transport'  => 'tcp',
        // context (tùy chọn, khi transport là ssl, cần truyền đường dẫn chứng chỉ)
        'context'    => [], 
        // Tham số hàm tạo của lớp tiến trình, ở đây là các tham số của hàm tạo lớp process\Pusher::class (tùy chọn)
        'constructor' => [],
    ],
];
```

## Tổng kết
Tiến trình tùy chỉnh trong webman thực tế là một cái bọc đơn giản của workerman, nó tách biệt cấu hình và nghiệp vụ, và chuyển các lời gọi `onXXX` của workerman thành các phương thức của lớp. Cách sử dụng khác hoàn toàn giống với workerman.
