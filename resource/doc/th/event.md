# webman ไลบรารีเหตุการณ์ webman-event

[![license](https://img.shields.io/github/license/Tinywan/webman-event)]()
[![webman-event](https://img.shields.io/github/v/release/tinywan/webman-event?include_prereleases)]()
[![webman-event](https://img.shields.io/badge/build-passing-brightgreen.svg)]()
[![webman-event](https://img.shields.io/github/last-commit/tinywan/webman-event/main)]()
[![webman-event](https://img.shields.io/github/v/tag/tinywan/webman-event?color=ff69b4)]()

ข้อดีของเหตุการณ์เมื่อเปรียบเทียบกับไลบรารีกลางทางคือเหตุการณ์มีความแม่นยำมากกว่าการใช้ไลบรารีกลางทาง (หรืออาจจะเพิ่มเท่าไหร่ก็ได้) และเหมาะกับการขยายธุรกิจในบางสถานการณ์ เช่น เรามักจะพบว่าหลังจากที่ผุ้ใช้ทำการลงทะเบียนหรือเข้าสู่ระบบ ต้องการทำงานต่อสติบแรกบางอย่าง ผ่านระบบเหตุการณ์สามารถทำได้โดยไม่ต้องแทรกระหว่างรหัสปัจจุบัน ทำงานของการเข้าสู่ระบบที่เตรียมไว้, ลดความเชื่อมโยงของระบบ พร้อมทั้งลดความเป็นไปได้ของข้อผิดพลาดได้

## ที่อยู่ของโปรเจค

[https://github.com/Tinywan/webman-permission](https://github.com/Tinywan/webman-permission)

## ความพึงพอใจในการใช้งาน

- [symfony/event-dispatcher](https://github.com/symfony/event-dispatcher)

## การติดตั้ง

```shell script
composer require tinywan/webman-event
```
## การกำหนดค่า 

ไฟล์กำหนดค่าเหตุการณ์ `config/event.php` เนื้อหาดังต่อไปนี้

```php
return [
    // การติดตามเหตุการณ์
    'listener'    => [],

    // ผู้รับการติดตามเหตุการณ์
    'subscriber' => [],
];
```
### การกำหนดค่าการเริ่มต้นของกระบวนการ

เปิด `config/bootstrap.php` และใส่การกำหนดค่าตามนี้:

```php
return [
    // ที่นี่ข้ามการกำหนดค่าอื่น ๆ ...
    webman\event\EventManager::class,
];
```
## เริ่มต้นอย่างรวดเร็ว

### การกำหนดเหตุการณ์

คลาสเหตุการณ์ `LogErrorWriteEvent.php`

```php
declare(strict_types=1);

namespace extend\event;

use Symfony\Contracts\EventDispatcher\Event;

class LogErrorWriteEvent extends Event
{
    const NAME = 'log.error.write';  // ชื่อเหตุการณ์หรือตัวระบุเหตุการณ์เฉพาะเจาะจง

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

### การติดตามเหตุการณ์
```php
return [
    // การติดตามเหตุการณ์
    'listener'    => [
        \extend\event\LogErrorWriteEvent::NAME  => \extend\event\LogErrorWriteEvent::class,
    ],
];
```

### ผู้รับการติดตามเหตุการณ์

คลาสสินสับสําหรับการติดตามเหตุการณ์ `LoggerSubscriber.php`

```php
namespace extend\event\subscriber;

use extend\event\LogErrorWriteEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class LoggerSubscriber implements EventSubscriberInterface
{
    /**
     * @desc: การบรรยายเมดท็อด
     * @return array|string[]
     */
    public static function getSubscribedEvents()
    {
        return [
            LogErrorWriteEvent::NAME => 'onLogErrorWrite',
        ];
    }

    /**
     * @desc: การบุัดกรองเหตุการณ์
     * @param LogErrorWriteEvent $event
     */
    public function onLogErrorWrite(LogErrorWriteEvent $event)
    {
        // บางลักษณะของลอจิกซ์ทางธุรกิจ
        var_dump($event->handle());
    }
}
```

การติดตตามเหตุการณ์ 
```php
return [
    // การติดตามเหตุการณ์
    'subscriber' => [
        \extend\event\subscriber\LoggerSubscriber::class,
    ],
];
```

### การเรียกใช้ตัวกระตรวงเหตุการณ์

เรียกใช้เหตุการณ์ `LogErrorWriteEvent` ขึ้นชื่อ.

```php
$error = [
    'errorMessage' => 'ข้อความผิด',
    'errorCode' => 500
];
EventManager::trigger(new LogErrorWriteEvent($error),LogErrorWriteEvent::NAME);
```

ผลลัพธ์การทำงาน

![ผลลัพธ์การทำงาน](./trigger.png)

## ใบอนุญาต

โปรเจคนี้ได้รับอนุญาตภายใต้ [ใบอนุญาต Apache 2.0](LICENSE).
