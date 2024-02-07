## Hàng đợi Stomp

Stomp là giao thức tin nhắn định hướng văn bản đơn giản, cung cấp định dạng kết nối tương tác cho phép khách hàng Stomp tương tác với bất kỳ trung tâm tin nhắn Stomp (Broker) nào. [workerman/stomp](https://github.com/walkor/stomp) triển khai khách hàng Stomp, chủ yếu được sử dụng trong các trường hợp hàng đợi tin nhắn như RabbitMQ, Apollo, ActiveMQ, v.v.

## Cài đặt
`composer require webman/stomp`

## Cấu hình
Tệp cấu hình nằm ở `config/plugin/webman/stomp`

## Gửi tin nhắn
```php
<?php
namespace app\controller;

use support\Request;
use Webman\Stomp\Client;

class Index
{
    public function queue(Request $request)
    {
        // Hàng đợi
        $queue = 'examples';
        // Dữ liệu (khi chuyển mảng cần tự serialize, ví dụ sử dụng json_encode, serialize, v.v.)
        $data = json_encode(['to' => 'tom@gmail.com', 'content' => 'hello']);
        // Thực hiện gửi
        Client::send($queue, $data);

        return response('redis queue test');
    }

}
```
> Để tương thích với các dự án khác, thành phần Stomp không cung cấp chức năng tự động serialize và unserialize. Nếu dữ liệu gửi là mảng, cần phải tự serialize và unserialize khi tiêu thụ.

## Tiêu thụ tin nhắn
Tạo mới `app/queue/stomp/MyMailSend.php` (tên lớp có thể là bất kỳ, chỉ cần tuân theo quy tắc psr4).
```php
<?php
namespace app\queue\stomp;

use Workerman\Stomp\AckResolver;
use Webman\Stomp\Consumer;

class MyMailSend implements Consumer
{
    // Tên hàng đợi
    public $queue = 'examples';

    // Tên kết nối, tương ứng với kết nối trong tệp stomp.php
    public $connection = 'default';

    // Khi giá trị là client, cần gọi $ack_resolver->ack() để thông báo rằng đã tiêu thụ thành công
    // Khi giá trị là auto, không cần gọi $ack_resolver->ack()
    public $ack = 'auto';

    // Tiêu thụ
    public function consume($data, AckResolver $ack_resolver = null)
    {
        // Nếu dữ liệu là mảng, cần phải tự unserialize
        var_export(json_decode($data, true)); // In ra ['to' => 'tom@gmail.com', 'content' => 'hello']
        // Thông báo rằng đã tiêu thụ thành công
        $ack_resolve->ack(); // Khi ack là auto, có thể bỏ qua cuộc gọi này
    }
}
```

# Mở giao thức stomp cho rabbitmq
RabbitMQ mặc định không mở giao thức stomp, cần thực hiện các lệnh sau để mở
```shell
rabbitmq-plugins enable rabbitmq_stomp
```
Sau khi mở, cổng mặc định của stomp sẽ là 61613.
