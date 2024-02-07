# ไลบรารีเหตุการณ์ webman webman-event

[![สัญญาอนุญาต](https://img.shields.io/github/license/Tinywan/webman-event)]()
[![webman-event](https://img.shields.io/github/v/release/tinywan/webman-event?include_prereleases)]()
[![webman-event](https://img.shields.io/badge/build-passing-brightgreen.svg)]()
[![webman-event](https://img.shields.io/github/last-commit/tinywan/webman-event/main)]()
[![webman-event](https://img.shields.io/github/v/tag/tinywan/webman-event?color=ff69b4)]()

ความเหมาะสมของเหตุการณ์เมื่อเทียบกับ middleware คือเหตุการณ์จะทำให้การระบุตำแหน่งที่แน่นอนมากกว่า middleware (หรือบอกได้ว่าจะมีความละเอียดสูงขึ้น) และเหมาะสำหรับการขยายธุรกิจบางประการเช่น เช่น เมื่อมีการลงทะเบียนหรือล็อกอินผู้ใช้ต้องทำการดำเนินการหลายอย่าง ผ่านระบบเหตุการณ์สามารถทำได้โดยไม่ทำลายโค้ดเดิม เพิ่มประสิทธิภาพของระบบพร้อมกันลดความเกี่ยวข้องของระบบให้น้อยลงและลดความเป็นไปได้ของบั๊กอย่างเช่น

## ที่อยู่โครงการ

[https://github.com/Tinywan/webman-permission](https://github.com/Tinywan/webman-permission)

## ความขึ้นอยู่

- [symfony/event-dispatcher](https://github.com/symfony/event-dispatcher)

## การติดตั้ง

```shell script
composer require tinywan/webman-event
```
## การตั้งค่า

ไฟล์ตั้งค่าเหตุการณ์ `config/event.php` มีเนื้อหาดังต่อไปนี้

```php
return [
    // ตัวฟังก์ชันการติดต่อกับเหตุการณ์
    'listener'    => [],

    // ตัวเติมเหตุการณ์
    'subscriber' => [],
];
```
### การตั้งค่าการเริ่มต้นของกระบวนการ

เปิด `config/bootstrap.php`, เพิ่มการตั้งค่าดังต่อไปนี้:

```php
return [
    // ปรับปรุงการตั้งค่า ...
    webman\event\EventManager::class,
];
```
## เริ่มต้นรวดเร็ว

### กำหนดเหตุการณ์

คลาสเหตุการณ์ `LogErrorWriteEvent.php`

```php
declare(strict_types=1);

namespace extend\event;

use Symfony\Contracts\EventDispatcher\Event;

class LogErrorWriteEvent extends Event
{
    const NAME = 'log.error.write';  // ชื่อเหตุการณ์, ตัวแทนเหตุการณ์เป็นค่าไม่ซ้ำเอาโดยสิ้นเชิง

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

### ฟังก์ชันการติดต่อกับเหตุการณ์

```php
return [
    // ตัวฟังก์ชันการติดต่อกับเหตุการณ์
    'listener'    => [
        \extend\event\LogErrorWriteEvent::NAME  => \extend\event\LogErrorWriteEvent::class,
    ],
];
```

### ตัวเติมเหตุการณ์

คลาสตัวเติม `LoggerSubscriber.php`

```php
namespace extend\event\subscriber;

use extend\event\LogErrorWriteEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class LoggerSubscriber implements EventSubscriberInterface
{
    /**
     * @desc: ประโยชน์ของเมธอด
     * @return array|string[]
     */
    public static function getSubscribedEvents()
    {
        return [
            LogErrorWriteEvent::NAME => 'onLogErrorWrite',
        ];
    }

    /**
     * @desc: เริ่มต้นเหตุการณ์
     * @param LogErrorWriteEvent $event
     */
    public function onLogErrorWrite(LogErrorWriteEvent $event)
    {
        // ตรวจสอบตามจุดเป้าหมายที่ระบุ
        var_dump($event->handle());
    }
}
```

การตั้งค่าเหตุการณ์
```php
return [
    // ตัวเติมการติดต่อกับเหตุการณ์
    'subscriber' => [
        \extend\event\subscriber\LoggerSubscriber::class,
    ],
];
```

### กำหนดไม้กระตุ้นเหตุการณ์

ไม้กระตุ้นเหตุการณ์ `LogErrorWriteEvent` การเริ่มต้นเหตุการณ์

```php
$error = [
    'errorMessage' => 'ข้อความผิดพลาด',
    'errorCode' => 500
];
EventManager::trigger(new LogErrorWriteEvent($error),LogErrorWriteEvent::NAME);
```

ผลลัพธ์การดำเนินการ

![ผลัพธ์การดำเนินการ](./trigger.png)

## สิทธิในการใช้งาน

โครงการนี้มีสิทธิ์ในการใช้งานภายใต้การอนุญาต [Apache 2.0 license](LICENSE).
