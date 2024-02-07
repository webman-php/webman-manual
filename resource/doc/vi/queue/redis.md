## Hàng đợi Redis

Hàng đợi tin nhắn dựa trên Redis, hỗ trợ xử lý trễ tin nhắn.

## Cài đặt
`composer require webman/redis-queue`

## Tệp cấu hình
Tệp cấu hình Redis tự động tạo ra tại `config/plugin/webman/redis-queue/redis.php`, nội dung tương tự như sau:
```php
<?php
return [
    'default' => [
        'host' => 'redis://127.0.0.1:6379',
        'options' => [
            'auth' => '',         // Mật khẩu, tham số tùy chọn
            'db' => 0,            // Cơ sở dữ liệu
            'max_attempts'  => 5, // Số lần thử lại sau khi tiêu thụ không thành công
            'retry_seconds' => 5, // Khoảng thời gian thử lại, đơn vị là giây
        ]
    ],
];
```

### Thử lại khi tiêu thụ không thành công
Nếu tiêu thụ không thành công (xảy ra ngoại lệ), tin nhắn sẽ được đưa vào hàng đợi trễ, chờ đợi thử lại lần sau. Số lần thử lại được điều khiển bởi tham số `max_attempts`, khoảng thời gian thử lại được quản lý bởi cả `retry_seconds` và `max_attempts`. Ví dụ nếu `max_attempts` là 5, `retry_seconds` là 10, khoảng thời gian thử lại lần 1 là `1*10` giây, khoảng thời gian thử lại lần 2 là `2*10` giây, khoảng thời gian thử lại lần 3 là `3*10` giây, và cứ tiếp tục như vậy cho đến lần thử lại thứ 5. Nếu vượt quá số lần thử lại đã thiết lập thì tin nhắn sẽ được đưa vào hàng đợi thất bại với key là `{redis-queue}-failed`.

## Gửi tin nhắn (đồng bộ)
> **Lưu ý**
> Yêu cầu webman/redis >= 1.2.0, phụ thuộc vào redis extension

```php
<?php
namespace app\controller;

use support\Request;
use Webman\RedisQueue\Redis;

class Index
{
    public function queue(Request $request)
    {
        // Tên hàng đợi
        $queue = 'send-mail';
        // Dữ liệu, có thể truyền mảng trực tiếp, không cần serialize
        $data = ['to' => 'tom@gmail.com', 'content' => 'hello'];
        // Gửi tin nhắn
        Redis::send($queue, $data);
        // Gửi tin nhắn trễ, tin nhắn sẽ được xử lý sau 60 giây
        Redis::send($queue, $data, 60);

        return response('Kiểm tra hàng đợi redis');
    }

}
```
Nếu gửi thành công, `Redis::send()` trả về true, ngược lại trả về false hoặc ném ra ngoại lệ.

> **Gợi ý**
> Thời gian xử lý hàng đợi trễ có thể có sai số, ví dụ như tốc độ tiêu thụ nhỏ hơn tốc độ sản xuất dẫn đến quá tải hàng đợi và sau đó gây ra trễ lệch. Cách giảm nhẹ là mở thêm một số tiến trình tiêu thụ.

## Gửi tin nhắn (bất đồng bộ)
```php
<?php
namespace app\controller;

use support\Request;
use Webman\RedisQueue\Client;

class Index
{
    public function queue(Request $request)
    {
        // Tên hàng đợi
        $queue = 'send-mail';
        // Dữ liệu, có thể truyền mảng trực tiếp, không cần serialize
        $data = ['to' => 'tom@gmail.com', 'content' => 'hello'];
        // Gửi tin nhắn
        Client::send($queue, $data);
        // Gửi tin nhắn trễ, tin nhắn sẽ được xử lý sau 60 giây
        Client::send($queue, $data, 60);

        return response('Kiểm tra hàng đợi redis');
    }

}
```
`Client::send()` không trả về giá trị, nó thuộc về gửi bất đồng bộ, không đảm bảo tin nhắn %100 được gửi đến redis.

> **Gợi ý**
> Nguyên tắc của `Client::send()` là xây dựng một hàng đợi bộ nhớ cục bộ, gửi tin nhắn bất đồng bộ đồng bộ đến redis (tốc độ đồng bộ rất nhanh, khoảng 1 vạn tin nhắn mỗi giây). Nếu quá trình khởi động lại và ngẫu nhiên có dữ liệu trong hàng đợi bộ nhớ cục bộ chưa được đồng bộ, sẽ dẫn đến mất tin nhắn. `Client::send()` thích hợp để gửi tin nhắn không quan trọng.

> **Gợi ý**
> `Client::send()` là bất đồng bộ, nó chỉ có thể sử dụng trong môi trường chạy của workerman, các kịch bản dòng lệnh vui lòng sử dụng giao diện đồng bộ `Redis::send()`

## Gửi tin nhắn từ dự án khác
Đôi khi bạn cần gửi tin nhắn từ dự án khác và không thể sử dụng `webman\redis-queue`, sau đây là một số hàm tham khảo để gửi tin nhắn vào hàng đợi.

```php
function redis_queue_send($redis, $queue, $data, $delay = 0) {
    $queue_waiting = '{redis-queue}-waiting';
    $queue_delay = '{redis-queue}-delayed';
    $now = time();
    $package_str = json_encode([
        'id'       => rand(),
        'time'     => $now,
        'delay'    => $delay,
        'attempts' => 0,
        'queue'    => $queue,
        'data'     => $data
    ]);
    if ($delay) {
        return $redis->zAdd($queue_delay, $now + $delay, $package_str);
    }
    return $redis->lPush($queue_waiting.$queue, $package_str);
}
```

Trong đó, tham số `$redis` là một ví dụ về Redis. Ví dụ về cách sử dụng redis extension tương tự như sau:
```php
$redis = new Redis;
$redis->connect('127.0.0.1', 6379);
$queue = 'user-1';
$data= ['some', 'data'];
redis_queue_send($redis, $queue, $data);
```
## Tiêu thụ
Tệp cấu hình tiến trình tiêu thụ nằm trong `config/plugin/webman/redis-queue/process.php`.  
Thư mục tiêu thụ nằm trong `app/queue/redis/`.  

Chạy lệnh `php webman redis-queue:consumer my-send-mail` sẽ tạo ra tệp `app/queue/redis/MyMailSend.php`. 

> **Lưu ý**
> Nếu lệnh không tồn tại, bạn cũng có thể tạo bằng tay.  

```php
<?php

namespace app\queue\redis;

use Webman\RedisQueue\Consumer;

class MyMailSend implements Consumer
{
    // Tên hàng đợi cần tiêu thụ
    public $queue = 'send-mail';

    // Tên kết nối, tương ứng với kết nối trong tệp `plugin/webman/redis-queue/redis.php`
    public $connection = 'default';

    // Tiêu thụ
    public function consume($data)
    {
        // Không cần phục hồi
        var_export($data); // đầu ra ['to' => 'tom@gmail.com', 'content' => 'hello']
    }
}
```

> **Chú ý**
> Trong quá trình tiêu thụ, nếu không có ngoại lệ và Lỗi được ném ra thì xem như tiêu thụ thành công, nếu không, tiêu thụ thất bại và vào hàng đợi thử lại.  
> Redis-queue không có cơ chế ack, bạn có thể coi nó như là ack tự động (nếu không có ngoại lệ hoặc Lỗi xảy ra). Nếu trong quá trình tiêu thụ bạn muốn đánh dấu tin nhắn hiện tại không được tiêu thụ thành công, bạn có thể ném ra ngoại lệ một cách thủ công để đưa tin nhắn hiện tại vào hàng đợi thử lại. Trong thực tế, điều này không khác biệt với cơ chế ack.

> **Gợi ý**
> Người tiêu dùng hỗ trợ đa máy chủ đa tiến trình và một tin nhắn **sẽ không** bị tiêu thụ lại. Tin nhắn đã được tiêu thụ sẽ tự động bị xóa khỏi hàng đợi mà không cần phải xóa bằng tay.

> **Gợi ý**
> Quá trình tiêu thụ có thể tiêu thụ cùng lúc nhiều loại hàng đợi khác nhau, việc thêm hàng đợi mới không cần phải sửa cấu hình trong `process.php`, chỉ cần thêm lớp `Consumer` tương ứng trong `app/queue/redis` và sử dụng thuộc tính lớp `$queue` để chỉ định tên hàng đợi cần tiêu thụ.

> **Gợi ý**
> Người dùng Windows cần chạy `php windows.php` để khởi động webman, nếu không, không thể khởi động quá trình tiêu thụ.

## Thiết lập các quá trình tiêu thụ khác nhau cho các hàng đợi khác nhau
Mặc định, tất cả các người tiêu dùng sẽ chia sẻ cùng một quá trình tiêu thụ. Tuy nhiên, đôi khi chúng ta cần tách riêng một số hàng đợi để tiêu thụ, ví dụ, các doanh nghiệp tiêu thụ chậm được đưa vào một nhóm quá trình để tiêu thụ, các doanh nghiệp nhanh được đưa vào một nhóm quá trình khác để tiêu thụ. Để làm điều này, chúng ta có thể chia người tiêu dùng thành hai thư mục, ví dụ `app_path() . '/queue/redis/fast'` và `app_path() . '/queue/redis/slow'` (lưu ý rằng phải thay đổi không gian tên lớp người tiêu dùng tương ứng), sau đó cấu hình như sau:  
```php
return [
    ...Thiếu một số cấu hình khác...

    'redis_consumer_fast'  => [
        'handler'     => Webman\RedisQueue\Process\Consumer::class,
        'count'       => 8,
        'constructor' => [
            // Thư mục lớp tiêu thụ
            'consumer_dir' => app_path() . '/queue/redis/fast'
        ]
    ],
    'redis_consumer_slow'  => [
        'handler'     => Webman\RedisQueue\Process\Consumer::class,
        'count'       => 8,
        'constructor' => [
            // Thư mục lớp tiêu thụ
            'consumer_dir' => app_path() . '/queue/redis/slow'
        ]
    ]
];
```  
Thành công cấu hình theo thư mục và cấu hình tương ứng, chúng ta có thể dễ dàng thiết lập quá trình tiêu thụ khác nhau cho các người tiêu dùng khác nhau.

## Nhiều cấu hình Redis
#### Cấu hình
`config/plugin/webman/redis-queue/redis.php`
```php
<?php
return [
    'default' => [
        'host' => 'redis://192.168.0.1:6379',
        'options' => [
            'auth' => null,       // Mật khẩu, kiểu chuỗi, tham số tùy chọn
            'db' => 0,            // Cơ sở dữ liệu
            'max_attempts'  => 5, // Số lần thử lại sau khi tiêu thụ thất bại
            'retry_seconds' => 5, // Khoảng thời gian thử lại, tính bằng giây
        ]
    ],
    'other' => [
        'host' => 'redis://192.168.0.2:6379',
        'options' => [
            'auth' => null,       // Mật khẩu, kiểu chuỗi, tham số tùy chọn
            'db' => 0,             // Cơ sở dữ liệu
            'max_attempts'  => 5, // Số lần thử lại sau khi tiêu thụ thất bại
            'retry_seconds' => 5, // Khoảng thời gian thử lại, tính bằng giây
        ]
    ],
];
```  
Lưu ý rằng cấu hình đã được thêm một cấu hình redis có key là `other`.

#### Gửi tin nhắn qua nhiều Redis
```php
// Gửi tin nhắn đến hàng đợi có key là `default`
Client::connection('default')->send($queue, $data);
Redis::connection('default')->send($queue, $data);
// Tương đương với
Client::send($queue, $data);
Redis::send($queue, $data);

// Gửi tin nhắn đến hàng đợi có key là `other`
Client::connection('other')->send($queue, $data);
Redis::connection('other')->send($queue, $data);
```

#### Tiêu thụ thông qua nhiều Redis
Cấu hình tiêu thụ cho hàng đợi có key `other`
```php
namespace app\queue\redis;

use Webman\RedisQueue\Consumer;

class SendMail implements Consumer
{
    // Tên hàng đợi cần tiêu thụ
    public $queue = 'send-mail';

    // === Đặt là `other`, đại diện cho hàng đợi trong cấu hình tiêu thụ có key `other` ===
    public $connection = 'other';

    // Tiêu thụ
    public function consume($data)
    {
        // Không cần phục hồi
        var_export($data);
    }
}
```

## Câu hỏi phổ biến
**Tại sao lại bị lỗi `Workerman\Redis\Exception: Workerman Redis Wait Timeout (600 seconds)`**  

Lỗi này chỉ xảy ra trong giao diện gửi tin nhắn không đồng bộ `Client::send()`. Giao diện gửi tin nhắn không đồng bộ sẽ đầu tiên lưu tin nhắn trong bộ nhớ cục bộ, khi tiến trình rảnh rỗi, tin nhắn sẽ được gửi đến redis. Nếu tốc độ nhận của redis chậm hơn tốc độ sinh ra tin nhắn hoặc nếu tiến trình luôn bận với công việc khác và không có đủ thời gian để đồng bộ tin nhắn từ bộ nhớ cục bộ lên redis, điều này sẽ dẫn đến tình trạng bị ép tin nhắn. Nếu tin nhắn bị ép quá 600 giây, lỗi này sẽ được kích hoạt.  

Giải pháp: Sử dụng giao diện gửi tin nhắn đồng bộ `Redis::send()`.
