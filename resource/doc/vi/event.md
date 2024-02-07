# Thư viện sự kiện của webman webman-event

[![license](https://img.shields.io/github/license/Tinywan/webman-event)]()
[![webman-event](https://img.shields.io/github/v/release/tinywan/webman-event?include_prereleases)]()
[![webman-event](https://img.shields.io/badge/build-passing-brightgreen.svg)]()
[![webman-event](https://img.shields.io/github/last-commit/tinywan/webman-event/main)]()
[![webman-event](https://img.shields.io/github/v/tag/tinywan/webman-event?color=ff69b4)]()

Ưu điểm của sự kiện so với middleware là sự kiện có định vị chính xác hơn so với middleware (hoặc nói cách khác là tinh tế hơn), và phù hợp hơn với một số kịch bản kinh doanh mở rộng. Ví dụ, chúng ta thường phải thực hiện một loạt các hoạt động sau khi người dùng đăng ký hoặc đăng nhập, có thể thực hiện việc đăng nhập mà không can thiệp vào mã nguồn ban đầu thông qua hệ thống sự kiện, giảm bớt tính kết nối của hệ thống, giảm thiểu khả năng xảy ra lỗi.

## Địa chỉ dự án

[https://github.com/Tinywan/webman-permission](https://github.com/Tinywan/webman-permission)

## Dependency

- [symfony / event-dispatcher](https://github.com/symfony/event-dispatcher)

## Cài đặt

```shell script
composer require tinywan/webman-event
```

## Cấu hình

Nội dung tệp cấu hình sự kiện `config/event.php` như sau

```php
return [
    // Trình lắng nghe sự kiện
    'listener'    => [],

    // Người đăng ký sự kiện
    'subscriber' => [],
];
```
### Cấu hình khởi động quá trình

Mở `config/bootstrap.php`, thêm cấu hình sau:

```php
return [
    // Cấu hình khác đã bị bỏ qua ở đây ...
    webman\event\EventManager::class,
];
```

## Bắt đầu nhanh chóng

### Xác định sự kiện

Lớp sự kiện `LogErrorWriteEvent.php`

```php
declare(strict_types=1);

namespace extend\event;

use Symfony\Contracts\EventDispatcher\Event;

class LogErrorWriteEvent extends Event
{
    const NAME = 'log.error.write';  // Tên sự kiện, định danh duy nhất của sự kiện

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
    // Trình lắng nghe sự kiện
    'listener'    => [
        \extend\event\LogErrorWriteEvent::NAME  => \extend\event\LogErrorWriteEvent::class,
    ],
];
```

### Đăng ký sự kiện

Lớp đăng ký `LoggerSubscriber.php`

```php
namespace extend\event\subscriber;

use extend\event\LogErrorWriteEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class LoggerSubscriber implements EventSubscriberInterface
{
    /**
     * @desc: Mô tả phương thức
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
        // Một số logic kinh doanh cụ thể
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
EventManager::trigger(new LogErrorWriteEvent($error),LogErrorWriteEvent::NAME);
```

Kết quả thực hiện

![Kết quả in](./trigger.png)

## Giấy phép

Dự án này được cấp phép theo [giấy phép Apache 2.0](LICENSE).
