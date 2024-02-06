# webman ইভেন্ট লাইব্রেরি webman-event

[![license](https://img.shields.io/github/license/Tinywan/webman-event)]()
[![webman-event](https://img.shields.io/github/v/release/tinywan/webman-event?include_prereleases)]()
[![webman-event](https://img.shields.io/badge/build-passing-brightgreen.svg)]()
[![webman-event](https://img.shields.io/github/last-commit/tinywan/webman-event/main)]()
[![webman-event](https://img.shields.io/github/v/tag/tinywan/webman-event?color=ff69b4)]()

ইভেন্ট মিডলওয়্যারের তুলনায়, ইভেন্টের সুবিধা হলো মিডলওয়্যারের তুলনায় ইভেন্ট ঠিকানাটি (বা বলা যায়, গুণমান আরও সুক্ষ্ম) এবং কিছু ব্যবসা পরিস্থিতি বিস্তারের জন্য আরো উপযোগী। উদাহরণস্বরূপ, আমরা সাধারণত ব্যবহারকারী নিবন্ধক বা লগইনের পরে কিছু কাজ করতে হবে, ইভেন্ট সিস্টেমের মাধ্যমে পূর্ববর্তী কোডকে আক্রমণকরণ ছাড়াই লগইনের কাজ বিস্তার করা যেতে পারে, সিস্টেমের সংযোগের কমপ্লেক্সিটি হ্রাস সাথে, বাগের সম্ভাবনা মঞ্চ করা তুলে ধরা হয়।

## প্রকল্পের ঠিকানা

[https://github.com/Tinywan/webman-permission](https://github.com/Tinywan/webman-permission)

## নির্ভরণ

- [symfony/event-dispatcher](https://github.com/symfony/event-dispatcher)

## ইনস্টলেশন

```shell script
composer require tinywan/webman-event
```

## কনফিগারেশন

ইভেন্ট কনফিগারেশন ফাইল `config/event.php` এর বিষয়বস্তু

```php
return [
    // ইভেন্ট শোনারা
    'listener'    => [],

    // ইভেন্ট উপাদান
    'subscriber' => [],
];
```

### প্রসেস স্টার্ট কনফিগারেশন

`config/bootstrap.php` ফাইল খোলো, নিম্নের কোনও কনফিগারেশন যোগ করুন:

```php
return [
    // এখানে অন্যান্য কনফিগারেশনগুলি অবিলম্বে হ্রাস করা হয়েছে ...
    webman\event\EventManager::class,
];
```

## দ্রুত শুরু করুন

### ইভেন্ট সংজ্ঞা

ইভেন্ট ক্লাস `LogErrorWriteEvent.php`

```php
declare(strict_types=1);

namespace extend\event;

use Symfony\Contracts\EventDispatcher\Event;

class LogErrorWriteEvent extends Event
{
    const NAME = 'log.error.write';  // ইভেন্ট নাম, ইভেন্টের অদ্ভুত শনাক্ত

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

### ইভেন্ট শুনুন

```php
return [
    // ইভেন্ট শুনারা
    'listener'    => [
        \extend\event\LogErrorWriteEvent::NAME  => \extend\event\LogErrorWriteEvent::class,
    ],
];
```

### ইভেন্ট অনুসরণকারী

অনুসরণকারী ক্লাস `LoggerSubscriber.php`

```php
namespace extend\event\subscriber;

use extend\event\LogErrorWriteEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class LoggerSubscriber implements EventSubscriberInterface
{
    /**
     * @desc: পদক্ষেপ বিবরণ 
     * @return array|string[]
     */
    public static function getSubscribedEvents()
    {
        return [
            LogErrorWriteEvent::NAME => 'onLogErrorWrite',
        ];
    }

    /**
     * @desc: ইভেন্ট সক্রিয় করুন
     * @param LogErrorWriteEvent $event
     */
    public function onLogErrorWrite(LogErrorWriteEvent $event)
    {
        // কিছু নির্দিষ্ট ব্যবসায়িক মতামত
        var_dump($event->handle());
    }
}
```

ইভেন্ট অনুসরণ
```php
return [
    // ইভেন্ট অনুসরণ
    'subscriber' => [
        \extend\event\subscriber\LoggerSubscriber::class,
    ],
];
```

### ইভেন্ট ট্রিগার

`LogErrorWriteEvent` ইভেন্ট ট্রিগার করুন।

```php
$error = [
    'errorMessage' => 'ভুল বার্তা',
    'errorCode' => 500
];
EventManager::trigger(new LogErrorWriteEvent($error),LogErrorWriteEvent::NAME);
```

অগ্রগতি ফলাফল

![প্রিন্ট আউট](./trigger.png)

## লাইসেন্স

এই প্রকল্পটি [অ্যাপাচি ২.০ লাইসেন্স এর অধীনে পরিচালিত](LICENSE)।
