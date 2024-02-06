# مكتبة الأحداث ل webman webman-event

[![license](https://img.shields.io/github/license/Tinywan/webman-event)]()
[![webman-event](https://img.shields.io/github/v/release/tinywan/webman-event?include_prereleases)]()
[![webman-event](https://img.shields.io/badge/build-passing-brightgreen.svg)]()
[![webman-event](https://img.shields.io/github/last-commit/tinywan/webman-event/main)]()
[![webman-event](https://img.shields.io/github/v/tag/tinywan/webman-event?color=ff69b4)]()

مقارنة الأحداث بالوسيط تكمن في أن الأحداث أدق بشكل أكبر من الوسائط وأكثر تحديدًا (أو يمكن القول أن الحجم الحبيبي أكبر) ، وتناسب أيضًا توسيع بعض سيناريوهات الأعمال. على سبيل المثال، قد نحتاج عادة إلى القيام بسلسلة من العمليات بعد تسجيل المستخدم أو تسجيل الدخول، يمكن أن نتوسع عملية تسجيل الدخول دون أي تدخل في الشيفرة الأصلية، مما يقلل من تشابك النظام وفي الوقت نفسه يقلل من احتمالية وجود الأخطاء.

## رابط المشروع

[https://github.com/Tinywan/webman-permission](https://github.com/Tinywan/webman-permission)

## الاعتمادات

- [symfony/event-dispatcher](https://github.com/symfony/event-dispatcher)

## التثبيت

```shell script
composer require tinywan/webman-event
```
## تكوين 

محتوى ملف تكوين الحدث `config/event.php` كما يلي

```php
return [
    // مستمع الأحداث
    'listener'    => [],

    // مشترك الأحداث
    'subscriber' => [],
];
```
### تكوين بدء التشغيل

افتح `config/bootstrap.php` وأضف التكوين التالي:

```php
return [
    // تم حذف باقي التكوينات هنا ...
    webman\event\EventManager::class,
];
```
## البدء السريع

### تعريف حدث

فئة الحدث `LogErrorWriteEvent.php`

```php
declare(strict_types=1);

namespace extend\event;

use Symfony\Contracts\EventDispatcher\Event;

class LogErrorWriteEvent extends Event
{
    const NAME = 'log.error.write';  // اسم الحدث، الهوية الوحيدة للحدث

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

### استماع للحدث
```php
return [
    // مستمع الأحداث
    'listener'    => [
        \extend\event\LogErrorWriteEvent::NAME  => \extend\event\LogErrorWriteEvent::class,
    ],
];
```

### الاشتراك في الحدث

فئة الاشتراك `LoggerSubscriber.php`

```php
namespace extend\event\subscriber;

use extend\event\LogErrorWriteEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class LoggerSubscriber implements EventSubscriberInterface
{
    /**
     * @desc: وصف الأسلوب
     * @return array|string[]
     */
    public static function getSubscribedEvents()
    {
        return [
            LogErrorWriteEvent::NAME => 'onLogErrorWrite',
        ];
    }

    /**
     * @desc: تفعيل الحدث
     * @param LogErrorWriteEvent $event
     */
    public function onLogErrorWrite(LogErrorWriteEvent $event)
    {
        // بعض العمليات التجارية المحددة
        var_dump($event->handle());
    }
}
```

الاشتراك في الحدث
```php
return [
    // مشترك الأحداث
    'subscriber' => [
        \extend\event\subscriber\LoggerSubscriber::class,
    ],
];
```

### مشغل الحدث

تشغيل حدث `LogErrorWriteEvent`.

```php
$error = [
    'errorMessage' => 'رسالة الخطأ',
    'errorCode' => 500
];
EventManager::trigger(new LogErrorWriteEvent($error),LogErrorWriteEvent::NAME);
```

النتيجة
![نتيجة الطباعة](./trigger.png)

## الرخصة

يتم ترخيص هذا المشروع بموجب [رخصة Apache 2.0](LICENSE).
