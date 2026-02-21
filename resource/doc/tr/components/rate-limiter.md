# Hız Sınırlayıcı

webman hız sınırlayıcı, annotation tabanlı sınırlama desteği.
apcu, redis ve memory sürücülerini destekler.

## Kaynak deposu

https://github.com/webman-php/limiter

## Kurulum

```
composer require webman/limiter
```

## Kullanım

```php
<?php
namespace app\controller;

use RuntimeException;
use support\limiter\annotation\Limit;

class UserController
{

    #[Limit(limit: 10)]
    public function index(): string
    {
        // Varsayılan IP tabanlı sınırlama, varsayılan zaman penceresi 1 saniye
        return 'IP başına saniyede en fazla 10 istek';
    }

    #[Limit(limit: 100, ttl: 60, key: Limit::UID)]
    public function search(): string
    {
        // key: Limit::UID, kullanıcı ID'sine göre sınırlama, session('user.id') boş olmamalı
        return 'Kullanıcı başına 60 saniyede en fazla 100 arama';
    }

    #[Limit(limit: 1, ttl: 60, key: Limit::SID, message: 'Kişi başına dakikada sadece 1 e-posta')]
    public function sendMail(): string
    {
        // key: Limit::SID, session_id'ye göre sınırlama
        return 'E-posta başarıyla gönderildi';
    }

    #[Limit(limit: 100, ttl: 24*60*60, key: 'coupon', message: 'Bugünkü kuponlar tükendi, yarın tekrar deneyin')]
    #[Limit(limit: 1, ttl: 24*60*60, key: Limit::UID, message: 'Her kullanıcı günde sadece bir kupon alabilir')]
    public function coupon(): string
    {
        // key: 'coupon', global sınırlama için özel anahtar, günde max 100 kupon
        // Kullanıcı ID'sine göre de sınırlama, her kullanıcı günde bir kupon
        return 'Kupon başarıyla gönderildi';
    }

    #[Limit(limit: 5, ttl: 24*60*60, key: [UserController::class, 'getMobile'], message: 'Numara başına günde en fazla 5 SMS')]
    public function sendSms2(): string
    {
        // key değişken olduğunda: [sınıf, statik_metod], örn. [UserController::class, 'getMobile'] UserController::getMobile() dönüş değerini anahtar olarak kullanır
        return 'SMS başarıyla gönderildi';
    }

    /**
     * Özel anahtar, mobil numara al, statik metod olmalı
     * @return string
     */
    public static function getMobile(): string
    {
        return request()->get('mobile');
    }

    #[Limit(limit: 1, ttl: 10, key: Limit::IP, message: 'Hız sınırlandı', exception: RuntimeException::class)]
    public function testException(): string
    {
        // Aşıldığında varsayılan istisna: support\limiter\RateLimitException, exception parametresi ile değiştirilebilir
        return 'ok';
    }

}
```

**Notlar**

* Sabit pencere algoritması kullanır
* Varsayılan ttl zaman penceresi: 1 saniye
* ttl ile pencere ayarla, örn. `ttl:60` 60 saniye için
* Varsayılan sınırlama boyutu: IP (varsayılan `127.0.0.1` sınırlı değil, aşağıdaki yapılandırmaya bakın)
* Yerleşik: IP, UID (`session('user.id')` boş olmamalı), SID (`session_id`'ye göre) sınırlama
* nginx proxy kullanırken IP sınırlaması için `X-Forwarded-For` başlığını iletin, bkz. [nginx proxy](../others/nginx-proxy.md)
* Aşıldığında `support\limiter\RateLimitException` tetikler, özel istisna sınıfı `exception:xx` ile
* Aşıldığında varsayılan hata mesajı: `Too Many Requests`, özel mesaj `message:xx` ile
* Varsayılan hata mesajı [çeviri](translation.md) ile de değiştirilebilir, Linux referansı:

```
composer require symfony/translation
mkdir resource/translations/zh_CN/ -p
echo "<?php
return [
    'Too Many Requests' => '请求频率受限'
];" > resource/translations/zh_CN/messages.php
php start.php restart
```

## API

Bazen geliştiriciler sınırlayıcıyı doğrudan kodda çağırmak ister, aşağıdaki örneğe bakın:

```php
<?php
namespace app\controller;

use RuntimeException;
use support\limiter\Limiter;

class UserController {

    public function sendSms(string $mobile): string
    {
        // Burada mobile anahtar olarak kullanılıyor
        Limiter::check($mobile, 5, 24*60*60, 'Numara başına günde en fazla 5 SMS');
        return 'SMS başarıyla gönderildi';
    }
}
```

## Yapılandırma

**config/plugin/webman/limiter/app.php**

```
<?php

use support\limiter\RateLimitException;

return [
    'enable' => true,
    'driver' => 'auto', // auto, apcu, memory, redis
    'stores' => [
        'redis' => [
            'connection' => 'default',
        ]
    ],
    // Bu IP'ler sınırlanmaz (sadece key Limit::IP olduğunda geçerli)
    'ip_whitelist' => [
        '127.0.0.1',
    ],
    'exception' => RateLimitException::class
];
```

* **enable**: Hız sınırlamasını etkinleştir
* **driver**: `auto`, `apcu`, `memory`, `redis`'ten biri; `auto` otomatik olarak `apcu` (öncelikli) ve `memory` arasında seçer
* **stores**: Redis yapılandırması, `connection` `config/redis.php`'deki anahtara karşılık gelir
* **ip_whitelist**: Whitelist'teki IP'ler sınırlanmaz (sadece key `Limit::IP` olduğunda geçerli)

## Sürücü seçimi

**memory**

* Tanıtım
  Uzantı gerekmez, en iyi performans.

* Kısıtlamalar
  Sınırlama sadece mevcut işlem için geçerli, işlemler arası veri paylaşımı yok, küme sınırlaması desteklenmez.

* Kullanım senaryoları
  Windows geliştirme ortamı; katı sınırlama gerektirmeyen işler; CC saldırılarına karşı savunma.

**apcu**

* Uzantı kurulumu
  apcu uzantısı gerekli, php.ini ayarları:

```
apc.enabled=1
apc.enable_cli=1
```

php.ini konumu `php --ini` ile bulunur

* Tanıtım
  Çok iyi performans, çoklu işlem veri paylaşımını destekler.

* Kısıtlamalar
  Küme desteklenmez

* Kullanım senaryoları
  Herhangi bir geliştirme ortamı; üretim tek sunucu sınırlaması; katı sınırlama gerektirmeyen küme; CC saldırılarına karşı savunma.

**redis**

* Bağımlılıklar
  redis uzantısı ve Redis bileşeni gerekli, kurulum:

```
composer require -W webman/redis illuminate/events
```

* Tanıtım
  apcu'dan daha düşük performans, tek sunucu ve küme hassas sınırlamayı destekler

* Kullanım senaryoları
  Geliştirme ortamı; üretim tek sunucu; küme ortamı
