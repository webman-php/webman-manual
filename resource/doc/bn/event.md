# webman ইভেন্ট লাইব্রেরি webman-event

[![license](https://img.shields.io/github/license/Tinywan/webman-event)]()
[![webman-event](https://img.shields.io/github/v/release/tinywan/webman-event?include_prereleases)]()
[![webman-event](https://img.shields.io/badge/build-passing-brightgreen.svg)]()
[![webman-event](https://img.shields.io/github/last-commit/tinywan/webman-event/main)]()
[![webman-event](https://img.shields.io/github/v/tag/tinywan/webman-event?color=ff69b4)]()

ইভেন্ট মিডলওয়্যারের তুলনায়, ইভেন্টের সুবিধা হল উল্লেখিত মধ্যপনা পরিসরের ব্যাপ্তি (বা কানের ব্যাপ্তি) এবং কিছু ব্যবসায়িক স্থিতিতে প্রসারণের জন্য আগ্রহী। উদাহরণস্বরূপ, আমাদের সাধারণত এমন অবডে আর্ডার পর বা লগইনের পরে কিছু অপারেশন করতে হয়, ইভেন্ট সিস্টেমের মাধ্যমে আমরা ওয়াক্যিলি হোমফড হ্লালমডের অধীনে লগইনের অপারেশনগুলির প্রসারণ সম্পন্ন করে সিস্টেমের যৌক্তিকতা কমিয়ে এবং বাগের সম্ভাবনা কমিয়ে আমি আপলোড করা যাচ্ছে।

## প্রজেক্ট ঠিকানা

[https://github.com/Tinywan/webman-permission](https://github.com/Tinywan/webman-permission)

## নির্ভরণী

- [symfony/event-dispatcher](https://github.com/symfony/event-dispatcher)

## ইন্সটলেশন

```shell script
composer require tinywan/webman-event
```
## কনফিগারেশন 

ইভেন্ট কনফিগারেশন ফাইল `config/event.php` এর মূল্যাংকন 

```php
return [
    // ইভেন্ট লিস্টেনার
    'listener'    => [],

    // ইভেন্ট সাবস্ক্রাইবার
    'subscriber' => [],
];
```
### প্রসেস স্টার্ট কনফিগারেশন

`config/bootstrap.php` খোলুন, নিম্নলিখিত কনফিগারেশন যোগ করুন

```php
return [
    // এখানে অন্যান্য কনফিগারেশন অংশ অন্যান্য অংশগুলি অবাকা করা হয়েছে ...
    webman\event\EventManager::class,
];
```
## দ্রুত শুরু

### ইভেন্ট সংজ্ঞা

ইভেন্ট ক্লাস `LogErrorWriteEvent.php`

```php
declare(strict_types=1);

namespace extend\event;

use Symfony\Contracts\EventDispatcher\Event;

class LogErrorWriteEvent extends Event
{
    const NAME = 'log.error.write';  // ইভেন্ট নাম, ইভেন্টের অনন্য অনন্য অঙ্ক

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

### ইভেন্ট লিস্টেন

```php
return [
    // ইভেন্ট লিস্টেনার
    'listener'    => [
        \extend\event\LogErrorWriteEvent::NAME  => \extend\event\LogErrorWriteEvent::class,
    ],
];
```

### ইভেন্ট সাবস্ক্রাইব

সাবস্ক্রাইবার ক্লাস `LoggerSubscriber.php`

```php
namespace extend\event\subscriber;

use extend\event\LogErrorWriteEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class LoggerSubscriber implements EventSubscriberInterface
{
    /**
     * @desc: মেথড ডেসক্রিপশন
     * @return array|string[]
     */
    public static function getSubscribedEvents()
    {
        return [
            LogErrorWriteEvent::NAME => 'onLogErrorWrite',
        ];
    }

    /**
     * @desc: ঘটনা হ্টার
     * @param LogErrorWriteEvent $event
     */
    public function onLogErrorWrite(LogErrorWriteEvent $event)
    {
        // কিছু ব্যাপারিক ব্যবস্থা
        var_dump($event->handle());
    }
}
```

ইভেন্ট সাবস্ক্রাইব

```php
return [
    // ইভেন্ট সাবস্ক্রাইব
    'subscriber' => [
        \extend\event\subscriber\LoggerSubscriber::class,
    ],
];
```

### ইভেন্ট ট্রিগার

`LogErrorWriteEvent` ইভেন্ট ট্রিগার করুন।

```php
$error = [
    'errorMessage' => 'ত্রুটি বার্তা',
    'errorCode' => 500
];
EventManager::trigger(new LogErrorWriteEvent($error),LogErrorWriteEvent::NAME);
```

এক্সিকিউশন ফলাফল

![প্রাইন্ট রেজাল্ট](./trigger.png)

## লাইসেন্স

এই প্রজেক্টটি অনুমোদন প্রাপ্ত [অ্যাপাচি 2.0 লাইসেন্স](LICENSE) দ্বারা অনুমতিপ্রাপ্ত।
