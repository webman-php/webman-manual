# webman Event Kütüphanesi webman-event

[![lisans](https://img.shields.io/github/license/Tinywan/webman-event)]()
[![webman-event](https://img.shields.io/github/v/release/tinywan/webman-event?include_prereleases)]()
[![webman-event](https://img.shields.io/badge/build-passing-brightgreen.svg)]()
[![webman-event](https://img.shields.io/github/last-commit/tinywan/webman-event/main)]()
[![webman-event](https://img.shields.io/github/v/tag/tinywan/webman-event?color=ff69b4)]()

Etkinlik, bir şeyin işlemleri empoze etmesi ve bu işlemleri izlemesi bakımından ara yazılımdan daha avantajlıdır (yani daha ince tanelidir) ve bazı iş sahneleri için daha uygundur. Örneğin, genellikle kullanıcı kaydolduktan veya giriş yaptıktan sonra bir dizi işlem yapmamız gerekebilir, etkinlik sistemi sayesinde mevcut kodlara müdahale etmeden giriş işleminin genişletilmesini sağlayabilir, sistemdeki bağlantıyı azaltırken, hata olasılığını da azaltır.

## Proje adresi

[https://github.com/Tinywan/webman-permission](https://github.com/Tinywan/webman-permission)

## Bağımlılıklar

- [symfony/event-dispatcher](https://github.com/symfony/event-dispatcher)

## Kurulum

```shell script
composer require tinywan/webman-event
```
## Yapılandırma 

Etkinlik yapılandırma dosyası `config/event.php` aşağıdaki gibidir

```php
return [
    // Etkinlik dinleyici
    'listener'    => [],

    // Etkinlik abonesi
    'subscriber' => [],
];
```
### Süreç başlatma yapılandırması

`config/bootstrap.php` dosyasını açın ve aşağıdaki yapılandırmayı ekleyin:

```php
return [
    // Diğer yapılandırmalar burada gösterilmedi ...
    webman\event\EventManager::class,
];
```
## Hızlı başlangıç

### Etkinlik tanımlama

Etkinlik sınıfı `LogErrorWriteEvent.php`

```php
declare(strict_types=1);

namespace extend\event;

use Symfony\Contracts\EventDispatcher\Event;

class LogErrorWriteEvent extends Event
{
    const NAME = 'log.error.write';  // Etkinlik adı, etkinliğin tekil tanımlayıcısı

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

### Etkinliği dinleme
```php
return [
    // Etkinlik dinleyici
    'listener'    => [
        \extend\event\LogErrorWriteEvent::NAME  => \extend\event\LogErrorWriteEvent::class,
    ],
];
```

### Etkinlik aboneliği

Abone sınıfı `LoggerSubscriber.php`

```php
namespace extend\event\subscriber;

use extend\event\LogErrorWriteEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class LoggerSubscriber implements EventSubscriberInterface
{
    /**
     * @desc: Metod açıklaması
     * @return array|string[]
     */
    public static function getSubscribedEvents()
    {
        return [
            LogErrorWriteEvent::NAME => 'onLogErrorWrite',
        ];
    }

    /**
     * @desc: Etkinliği tetikle
     * @param LogErrorWriteEvent $event
     */
    public function onLogErrorWrite(LogErrorWriteEvent $event)
    {
        // Bazı belirli iş mantığı
        var_dump($event->handle());
    }
}
```

Etkinlik aboneliği
```php
return [
    // Etkinlik aboneliği
    'subscriber' => [
        \extend\event\subscriber\LoggerSubscriber::class,
    ],
];
```

### Etkinlik tetikleyici

`LogErrorWriteEvent` etkinliğini tetikleyin.

```php
$error = [
    'errorMessage' => 'Hata mesajı',
    'errorCode' => 500
];
EventManager::trigger(new LogErrorWriteEvent($error),LogErrorWriteEvent::NAME);
```

Sonuçları yürütün

![print result](./trigger.png)

## Lisans

Bu proje [Apache 2.0 lisansı](LICENSE) ile lisanslanmıştır.
