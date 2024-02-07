# Önbellek

webman varsayılan olarak [symfony/cache](https://github.com/symfony/cache) önbellek bileşenini kullanır.

> `symfony/cache`'i kullanmadan önce, `php-cli`'ye redis eklentisi kurulmalıdır.

## Kurulum
**php 7.x**
```php
composer require -W illuminate/redis ^8.2.0 symfony/cache ^5.2
```
**php 8.x**
```php
composer require -W illuminate/redis symfony/cache
```

Kurulumdan sonra yeniden başlatma gereklidir (reload işlemi etkisiz olabilir)

## Redis Yapılandırması
Redis yapılandırma dosyası `config/redis.php` içindedir.
```php
return [
    'default' => [
        'host'     => '127.0.0.1',
        'password' => null,
        'port'     => 6379,
        'database' => 0,
    ]
];
```

## Örnek
```php
<?php
namespace app\controller;

use support\Request;
use support\Cache;

class UserController
{
    public function db(Request $request)
    {
        $key = 'test_key';
        Cache::set($key, rand());
        return response(Cache::get($key));
    }
}
```

> **Not**
> Anahtar (key) çakışmasını önlemek için mümkünse bir ön ek eklenmelidir.

## Diğer Önbellek Bileşenleri Kullanma

[ThinkCache](https://github.com/top-think/think-cache) bileşeninin kullanımı için [Diğer Veritabanları](others.md#ThinkCache) kısmına bakın.
