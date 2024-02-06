# Redis

webman'ın redis bileşeni varsayılan olarak [illuminate/redis](https://github.com/illuminate/redis) kullanır, yani laravel'in redis kütüphanesini kullanır ve laravel ile aynı şekilde kullanılır.

`illuminate/redis`'i kullanmadan önce `php-cli`'ye redis eklentisi kurmak gereklidir.

> **Not**
> `php-cli`'ye redis eklentisinin kurulu olup olmadığını kontrol etmek için `php -m | grep redis` komutunu kullanabilirsiniz. Lütfen dikkat edin: `php-fpm`'de redis eklentisini kurmanız, `php-cli`'de de kullanabileceğiniz anlamına gelmez çünkü `php-cli` ve `php-fpm` farklı uygulamalardır ve farklı `php.ini` yapılandırmalarını kullanabilir. Kullandığınız `php-cli` için hangi `php.ini` yapılandırma dosyasının kullanıldığını görmek için `php --ini` komutunu kullanabilirsiniz.

## Kurulum

```php
composer require -W illuminate/redis illuminate/events
```

Kurulumdan sonra yeniden başlatılması gerekmektedir (reload işlevsiz olacaktır)

## Yapılandırma
Redis yapılandırma dosyası `config/redis.php` içinde bulunmaktadır
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

## Redis API'si
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
Eşdeğeri 
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

> **Not**
> `Redis::select($db)` arayüzünü dikkatli kullanın, webman sürekli bellekte olan bir yapı olduğu için bir istek bir veritabanı seçtikten sonra sonraki diğer istekleri etkileyebilir. Birden fazla veritabanı için farklı `$db`'leri farklı Redis bağlantı yapılandırmalarına ayırmanız önerilir.

## Birden Fazla Redis Bağlantısı Kullanımı
Örneğin yapılandırma dosyası `config/redis.php` şu şekilde olabilir:
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
Varsayılan olarak `default` altında yapılandırılan bağlantıyı kullanır, `Redis::connection()` yöntemiyle hangi redis bağlantısının kullanılacağını seçebilirsiniz.
```php
$redis = Redis::connection('cache');
$redis->get('test_key');
```

## Küme Yapılandırma
Eğer uygulamanız Redis sunucusu kümesi kullanıyorsa, bu kümeleri tanımlamak için Redis yapılandırma dosyasında `clusters` anahtarını kullanmalısınız:
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

Varsayılan olarak, küme, istemci tarafı kesilmiş uygulamalara izin veren bir düğümde çalışır, böylece bir düğüm havuzu oluşturabilir ve büyük miktarda kullanılabilir bellek oluşturabilirsiniz. Burada dikkat edilmesi gereken şey, istemci paylaşımının başarısızlık durumlarıyla başa çıkmayacağıdır; bu nedenle bu özellik genellikle diğer birincil veritabanından önbellek verileri alırken kullanılır. Redis'in asıl kümesini kullanmak istiyorsanız, `options` anahtarında aşağıdaki gibi belirtmelisiniz:

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
Sunucuya birçok komut göndermeniz gerektiğinde pipeline komutlarını kullanmanızı öneririz. pipeline yöntemi bir Redis örneği kapanış fonksiyonu alır. Tüm komutlar Redis örneğine gönderilebilir ve bunlar tek bir işlemde tamamlanır:
```php
Redis::pipeline(function ($pipe) {
    for ($i = 0; $i < 1000; $i++) {
        $pipe->set("key:$i", $i);
    }
});
```
