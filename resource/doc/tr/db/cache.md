# Önbellek

webman varsayılan olarak önbellek bileşeni olarak [symfony/cache](https://github.com/symfony/cache) kullanır.

> `symfony/cache` kullanmadan önce `php-cli`'ye redis uzantısının yüklenmesi gerekir.

## Kurulum
**php 7.x**
```php
composer require -W illuminate/redis ^8.2.0 symfony/cache ^5.2
```
**php 8.x**
```php
composer require -W illuminate/redis symfony/cache
```

Kurulumdan sonra yeniden başlatılması gerekmektedir (yeniden yükleme işlemi geçerli değildir).

## Redis Yapılandırması
Redis yapılandırma dosyası `config/redis.php` içinde yer alır.
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
> Anahtar (key) çakışmalarını önlemek için mümkünse bir önek ekleyin ve bu sayede diğer redis kullanan işlemlerle çakışmayı önleyin

## Diğer Önbellek Bileşenlerinin Kullanımı 

[ThinkCache](https://github.com/top-think/think-cache) bileşeninin kullanımı için [Diğer Veritabanları](others.md#ThinkCache)'na bakabilirsiniz.
