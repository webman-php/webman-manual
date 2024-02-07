# Redis

webman'ın redis bileşeni varsayılan olarak [illuminate/redis](https://github.com/illuminate/redis) kullanır, yani laravel'in redis kütüphanesini kullanır. Kullanımı laravel ile aynıdır.

`illuminate/redis` kullanmadan önce `php-cli`'ye redis eklentisini kurmalısınız.

> **Dikkat**
> `php-cli`'de redis eklentisinin yüklü olup olmadığını kontrol etmek için `php -m | grep redis` komutunu kullanın. Lütfen unutmayın: Eğer `php-fpm`'e redis eklentisi kurduysanız, bu `php-cli`'de de kullanabileceğiniz anlamına gelmez çünkü `php-cli` ve `php-fpm` farklı uygulamalardır ve farklı `php.ini` yapılandırmaları kullanabilir. Kullandığınız `php-cli`'nin hangi `php.ini` yapılandırma dosyasını kullandığını görmek için `php --ini` komutunu kullanın.

## Yükleme

```php
composer require -W illuminate/redis illuminate/events
```

Yükledikten sonra, reload işlemi yeterli olmayacaktır, restart işlemi yapmalısınız.

## Yapılandırma
redis yapılandırma dosyası `config/redis.php` içinde bulunur.
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
use support\Redis;

class UserController
{
    public function db(Request $request)
    {
        $key = 'test_key';
        Redis::set($key, rand());
        return response(Redis::get($key));
    }
}
```

## Redis API
```php
Redis::append($key, $value)
Redis::bitCount($key)
Redis::decr($key, $value)
Redis::decrBy($key, $value)
Redis::get($key)
Redis::getBit($key, $offset)
Redis::getRange($key, $start, $end)
Redis::getSet($key, $value)
Redis::incr($key, $value)
Redis::incrBy($key, $value)
Redis::incrByFloat($key, $value)
Redis::mGet(array $keys)
Redis::getMultiple(array $keys)
Redis::mSet($pairs)
Redis::mSetNx($pairs)
Redis::set($key, $value, $expireResolution = null, $expireTTL = null, $flag = null)
Redis::setBit($key, $offset, $value)
Redis::setEx($key, $ttl, $value)
Redis::pSetEx($key, $ttl, $value)
Redis::setNx($key, $value)
Redis::setRange($key, $offset, $value)
Redis::strLen($key)
Redis::del(...$keys)
Redis::exists(...$keys)
Redis::expire($key, $ttl)
Redis::expireAt($key, $timestamp)
Redis::select($dbIndex)
```
Şu şekilde karşılık gelir:
```php
$redis = Redis::connection('default');
$redis->append($key, $value)
$redis->bitCount($key)
$redis->decr($key, $value)
$redis->decrBy($key, $value)
$redis->get($key)
$redis->getBit($key, $offset)
...
```

> **Dikkat**
> `Redis::select($db)` arabirimini dikkatli kullanın, webman sürekli bellekte olan bir çerçeve olduğundan, bir istek belirli bir veritabanını seçtikten sonra diğer istekleri etkileyebilir. Birden fazla veritabanı kullanılması durumunda, farklı `db`'leri farklı Redis bağlantı yapılandırmalarına ayarlamanız önerilir.

## Birden Çok Redis Bağlantısının Kullanımı
Örneğin, yapılandırma dosyası `config/redis.php` şu şekilde olabilir:
```php
return [
    'default' => [
        'host'     => '127.0.0.1',
        'password' => null,
        'port'     => 6379,
        'database' => 0,
    ],

    'cache' => [
        'host'     => '127.0.0.1',
        'password' => null,
        'port'     => 6379,
        'database' => 1,
    ],
]
```
Varsayılan olarak `default` bağlantısını kullanır, isterseniz `Redis::connection()` yöntemini kullanarak hangi redis bağlantısını kullanmak istediğinizi seçebilirsiniz.
```php
$redis = Redis::connection('cache');
$redis->get('test_key');
```

## Küme Yapılandırması
Uygulamanız Redis sunucusu kümeleri kullanıyorsa, bu kümeleri tanımlamak için redis yapılandırma dosyasında `clusters` anahtarını kullanmalısınız:
```php
return [
    'clusters' => [
        'default' => [
            [
                'host'     => 'localhost',
                'password' => null,
                'port'     => 6379,
                'database' => 0,
            ],
        ],
    ],

];
```
Varsayılan olarak, küme, bir istemci kesmesi sunucusunda gerçekleştirilebilir, böylece bir düğüm havuzu oluşturabilir ve büyük miktarda kullanılabilir bellek oluşturabilirsiniz. Bununla birlikte, müşteri tarafı başarısızlık durumlarıyla başa çıkmaz; bu nedenle, bu özellik genellikle başka bir ana veritabanından önbellek verisi almak için kullanılır. Redis'in doğal kümesini kullanmak istiyorsanız, yapılandırma dosyasındaki `options` anahtarı aşağıdaki gibi belirtilmelidir:
```php
return[
    'options' => [
        'cluster' => 'redis',
    ],

    'clusters' => [
        // ...
    ],
];
```

## Pipeline Komutu
Sunucuya birden çok komut göndermeniz gerektiğinde, pipeline komutunu kullanmanızı öneririz. pipeline yöntemi bir Redis örneğinin bir kapanış fonksiyonunu kabul eder. Tüm komutları Redis örneğine gönderebilir ve hepsi bir işlemde tamamlanacaktır:
```php
Redis::pipeline(function ($pipe) {
    for ($i = 0; $i < 1000; $i++) {
        $pipe->set("key:$i", $i);
    }
});
```
