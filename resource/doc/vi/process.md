# Tiến trình tùy chỉnh

Trong webman, bạn có thể tạo và lắng nghe các tiến trình tùy chỉnh giống như trong workerman.

> **Lưu ý**
> Người dùng Windows cần sử dụng `php windows.php` để khởi động webman và có thể bắt đầu tiến trình tùy chỉnh.

## Dịch vụ http tùy chỉnh
Đôi khi bạn có thể có yêu cầu đặc biệt và cần thay đổi mã lõi của dịch vụ http của webman. Trong trường hợp này, bạn có thể sử dụng tiến trình tùy chỉnh để thực hiện.

Ví dụ, tạo mới app\Server.php

```php
<?php

namespace app;

use Webman\App;

class Server extends App
{
    // Ghi đè phương thức trong Webman\App ở đây
}
```

Thêm cấu hình sau vào `config/process.php`

```php
use Workerman\Worker;

return [
    // ... phần cấu hình khác...
    
    'my-http' => [
        'handler' => app\Server::class,
        'listen' => 'http://0.0.0.0:8686',
        'count' => 8, // Số tiến trình
        'user' => '',
        'group' => '',
        'reusePort' => true,
        'constructor' => [
            'request_class' => \support\Request::class, // Cài đặt lớp request
            'logger' => \support\Log::channel('default'), // Ví dụ nhật ký
            'app_path' => app_path(), // Vị trí thư mục app
            'public_path' => public_path() // Vị trí thư mục public
        ]
    ]
];
```

> **Gợi ý**
> Nếu muốn tắt tiến trình http có sẵn của webman, chỉ cần cài đặt `listen=>''` trong config/server.php

## Ví dụ lắng nghe websocket tùy chỉnh

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
> Lưu ý: Tất cả các thuộc tính onXXX đều là public.

Thêm cấu hình sau vào `config/process.php`
```php
return [
    // ... Cấu hình tiến trình khác được lược bỏ ...
    
    // websocket_test là tên tiến trình
    'websocket_test' => [
        // Chỉ định lớp tiến trình là lớp Pusher đã định nghĩa ở trên
        'handler' => app\Pusher::class,
        'listen'  => 'websocket://0.0.0.0:8888',
        'count'   => 1,
    ],
];
```

## Ví dụ tiến trình không lắng nghe tùy chỉnh

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
        // Kiểm tra cơ sở dữ liệu có người dùng mới đăng ký không mỗi 10 giây
        Timer::add(10, function(){
            Db::table('users')->where('regist_timestamp', '>', time()-10)->get();
        });
    }
    
}
```
Thêm cấu hình sau vào `config/process.php`
```php
return [
    // ... Cấu hình tiến trình khác được lược bỏ
    
    'task' => [
        'handler'  => app\TaskTest::class
    ],
];
```

> Lưu ý: Nếu lược bỏ listen sẽ không lắng nghe bất kỳ cổng nào, lược bỏ count sẽ mặc định tiến trình là 1.

## Mô tả cấu hình file

Cấu hình đầy đủ cho một tiến trình là:

```php
return [
    // ... 
    
    // websocket_test là tên tiến trình
    'websocket_test' => [
        // Chỉ định lớp tiến trình
        'handler' => app\Pusher::class,
        // Giao thức ip và cổng lắng nghe (tùy chọn)
        'listen'  => 'websocket://0.0.0.0:8888',
        // Số tiến trình (tùy chọn, mặc định là 1)
        'count'   => 2,
        // Người dùng chạy tiến trình (tùy chọn, mặc định là người dùng hiện tại)
        'user'    => '',
        // Nhóm người dùng chạy tiến trình (tùy chọn, mặc định là nhóm người dùng hiện tại)
        'group'   => '',
        // Tiến trình này có hỗ trợ reload hay không (tùy chọn, mặc định là true)
        'reloadable' => true,
        // Bật reusePort (tùy chọn, yêu cầu php>=7.0, mặc định là true)
        'reusePort'  => true,
        // transport (tùy chọn, đặt là ssl khi cần kích hoạt ssl, mặc định là tcp)
        'transport'  => 'tcp',
        // context (tùy chọn, khi transport là ssl, cần truyền đường dẫn chứng chỉ)
        'context'    => [], 
        // Tham số hàm tạo của lớp tiến trình, ở đây là tham số hàm tạo của lớp process\Pusher::class (tùy chọn)
        'constructor' => [],
    ],
];
```

## Tổng kết
Tiến trình tùy chỉnh trong webman thực sự chỉ là một gói đóng gói đơn giản của workerman. Nó tách cấu hình và công việc ra khỏi nhau và triển khai các cuộc gọi `onXXX` của workerman thông qua phương thức của lớp. Các cách sử dụng khác hoàn toàn giống như trong workerman.
