# webman Event Kütüphanesi webman-event

[![lisans](https://img.shields.io/github/license/Tinywan/webman-event)]()
[![webman-event](https://img.shields.io/github/v/release/tinywan/webman-event?include_prereleases)]()
[![webman-event](https://img.shields.io/badge/build-passing-brightgreen.svg)]()
[![webman-event](https://img.shields.io/github/last-commit/tinywan/webman-event/main)]()
[![webman-event](https://img.shields.io/github/v/tag/tinywan/webman-event?color=ff69b4)]()

Olaylar, ara yazılımdan daha hassas bir konumlandırmaya (veya daha ince bir tane) sahip olması ve bazı iş senaryolarının genişletilmesi için daha uygun olması açısından olaylara göre avantaj sağlar. Örneğin, genellikle kullanıcı kaydolduktan veya giriş yaptıktan sonra bazı işlemler yapmamız gerekebilir. Olay sistemi, mevcut kodlara müdahale etmeden giriş işleminin genişletilmesini sağlayarak sistemdeki bağlantıyı azaltırken aynı zamanda hata olasılığını da azaltır.

## Proje adresi

[https://github.com/Tinywan/webman-permission](https://github.com/Tinywan/webman-permission)

## Bağımlılıklar

- [symfony/event-dispatcher](https://github.com/symfony/event-dispatcher)

## Kurulum

```shell script
composer require tinywan/webman-event
```
## Yapılandırma

Olay yapılandırma dosyası `config/event.php` aşağıdaki içeriğe sahiptir.

```php
return [
    // Olay dinleyicisi
    'listener'    => [],

    // Olay abonesi
    'subscriber' => [],
];
```
### İşlem başlatma yapılandırması

`config/bootstrap.php` dosyasını açın ve aşağıdaki yapılandırmayı ekleyin:

```php
return [
    // Diğer yapılandırmalar burada bulunmaktadır ...
    webman\event\EventManager::class,
];
```
## Hızlı başlangıç

### Olayları tanımlama

Olay sınıfı `LogErrorWriteEvent.php`

```php
declare(strict_types=1);

namespace extend\event;

use Symfony\Contracts\EventDispatcher\Event;

class LogErrorWriteEvent extends Event
{
    const NAME = 'log.error.write';  // Olay adı, olayın benzersiz kimliği

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

### Olayları dinleme
```php
return [
    // Olay dinleyicisi
    'listener'    => [
        \extend\event\LogErrorWriteEvent::NAME  => \extend\event\LogErrorWriteEvent::class,
    ],
];
```

### Olayları abone olma

Abone sınıfı `LoggerSubscriber.php`

```php
namespace extend\event\subscriber;

use extend\event\LogErrorWriteEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class LoggerSubscriber implements EventSubscriberInterface
{
    /**
     * @desc: Method description
     * @return array|string[]
     */
    public static function getSubscribedEvents()
    {
        return [
            LogErrorWriteEvent::NAME => 'onLogErrorWrite',
        ];
    }

    /**
     * @desc: Olay tetikleme
     * @param LogErrorWriteEvent $event
     */
    public function onLogErrorWrite(LogErrorWriteEvent $event)
    {
        // Bazı belirli iş mantığı
        var_dump($event->handle());
    }
}
```

Olay aboneliği
```php
return [
    // Olay aboneliği
    'subscriber' => [
        \extend\event\subscriber\LoggerSubscriber::class,
    ],
];
```

### Olay tetikleyici

`LogErrorWriteEvent` olayını tetikleyin.

```php
$error = [
    'errorMessage' => 'Hata mesajı',
    'errorCode' => 500
];
EventManager::trigger(new LogErrorWriteEvent($error),LogErrorWriteEvent::NAME);
```

Sonuç
![result](./trigger.png)

## Lisans

Bu proje [Apache 2.0 lisansı](LICENSE) altında lisanslanmıştır.
