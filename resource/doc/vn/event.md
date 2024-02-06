# Thư viện sự kiện webman webman-event

[![bản quyền](https://img.shields.io/github/license/Tinywan/webman-event)] ()
[![webman-event](https://img.shields.io/github/v/release/tinywan/webman-event?include_prereleases)] ()
[![webman-event](https://img.shields.io/badge/build-passing-brightgreen.svg)] ()
[![webman-event](https://img.shields.io/github/last-commit/tinywan/webman-event/main)] ()
[![webman-event](https://img.shields.io/github/v/tag/tinywan/webman-event?color=ff69b4)] ()

Ưu điểm của sự kiện so với middleware là sự kiện có vị trí cụ thể hơn (hoặc có thể nói là tinh lọc hơn) và phù hợp hơn với việc mở rộng một số tình huống kinh doanh. Ví dụ, chúng ta thường gặp phải những hoạt động cần được thực hiện sau khi người dùng đăng ký hoặc đăng nhập, thông qua hệ thống sự kiện có thể mở rộng hoạt động đăng nhập mà không ảnh hưởng đến mã nguồn gốc, giảm thiểu sự ràng buộc của hệ thống đồng thời giảm thiểu khả năng xảy ra lỗi.

## URL dự án

[https://github.com/Tinywan/webman-permission](https://github.com/Tinywan/webman-permission)

## Phụ thuộc

- [symfony/event-dispatcher](https://github.com/symfony/event-dispatcher)

## Cài đặt

```shell script
composer require tinywan/webman-event
```
## Cấu hình

Nội dung tập tin cấu hình sự kiện `config/event.php` như sau

```php
return [
    // Lắng nghe sự kiện
    'listener'    => [],

    // Người đăng ký sự kiện
    'subscriber' => [],
];
```
### Cấu hình khởi động quá trình

Mở tệp `config/bootstrap.php`, thêm cấu hình như sau:

```php
return [
    // Bỏ qua các cấu hình khác ...
    webman\event\EventManager::class,
];
```
## Bắt đầu nhanh

### Xác định sự kiện

Lớp sự kiện `LogErrorWriteEvent.php`

```php
declare(strict_types=1);

namespace extend\event;

use Symfony\Contracts\EventDispatcher\Event;

class LogErrorWriteEvent extends Event
{
    const NAME = 'log.error.write';  // Tên sự kiện, là danh tính duy nhất của sự kiện

    /** @var array */
    public array $log;

    public function __construct(array $log)
    {
        $this->log = $log;
    }

    public function handle()
    {
        return $this->log;
    }
}
```

### Lắng nghe sự kiện
```php
return [
    // Lắng nghe sự kiện
    'listener'    => [
        \extend\event\LogErrorWriteEvent::NAME  => \extend\event\LogErrorWriteEvent::class,
    ],
];
```

### Người đăng ký sự kiện

Lớp người đăng ký `LoggerSubscriber.php`

```php
namespace extend\event\subscriber;

use extend\event\LogErrorWriteEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class LoggerSubscriber implements EventSubscriberInterface
{
    /**
     * @desc: Mô tả chức năng
     * @return array|string[]
     */
    public static function getSubscribedEvents()
    {
        return [
            LogErrorWriteEvent::NAME => 'onLogErrorWrite',
        ];
    }

    /**
     * @desc: Kích hoạt sự kiện
     * @param LogErrorWriteEvent $event
     */
    public function onLogErrorWrite(LogErrorWriteEvent $event)
    {
        // Một số lôgic kinh doanh cụ thể
        var_dump($event->handle());
    }
}
```

Sự kiện đăng ký
```php
return [
    // Người đăng ký sự kiện
    'subscriber' => [
        \extend\event\subscriber\LoggerSubscriber::class,
    ],
];
```

### Bộ kích hoạt sự kiện

Kích hoạt sự kiện `LogErrorWriteEvent`.

```php
$error = [
    'errorMessage' => 'Thông báo lỗi',
    'errorCode' => 500
];
EventManager::trigger(new LogErrorWriteEvent($error), LogErrorWriteEvent::NAME);
```

Kết quả thực hiện

![Kết quả in](./trigger.png)

## Giấy phép

Dự án này được cấp phép theo [giấy phép Apache 2.0](LICENSE).
