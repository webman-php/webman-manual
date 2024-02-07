## Redis Kuyruğu

Redis tabanlı mesaj kuyruğu, mesajların gecikmeli işlenmesini destekler.

## Kurulum
`composer require webman/redis-queue`

## Yapılandırma Dosyası
Redis yapılandırma dosyası varsayılan olarak `config/plugin/webman/redis-queue/redis.php` dizininde oluşturulur ve aşağıdaki gibi içeriğe sahip olur:
```php
<?php
return [
    'default' => [
        'host' => 'redis://127.0.0.1:6379',
        'options' => [
            'auth' => '',         // Şifre, isteğe bağlı parametre
            'db' => 0,            // Veritabanı
            'max_attempts'  => 5, // Tüketim başarısız olduğunda tekrar deneme sayısı
            'retry_seconds' => 5, // Tekrar deneme aralığı, saniye cinsinden
        ]
    ],
];
```

### Başarısız Tüketimi Tekrar Deneme
Eğer tüketim başarısız olursa (istisna oluşursa), mesaj gecikmeli kuyruğa konur ve bir sonraki denemeyi bekler. Tekrar deneme sayısı `max_attempts` parametresi ile kontrol edilir. Tekrar deneme aralığı ise `retry_seconds` ve `max_attempts` parametreleriyle birlikte kontrol edilir. Örneğin, `max_attempts` 5 ve `retry_seconds` 10 olarak ayarlandığında, ilk deneme aralığı `1*10` saniye, ikinci deneme aralığı `2*10` saniye, üçüncü deneme aralığı `3*10` saniye şeklinde olur ve bunun gibi 5 kez tekrar deneme yapılır. Eğer `max_attempts` ayarlanan deneme sayısını aşarsa, mesaj `{redis-queue}-failed` adlı başarısız kuyruğa konulur.

## Mesaj Gönderme (Senkronize)
> **Not**
> `webman/redis-queue` sürüm 1.2.0 veya üstüne ihtiyaç duyar, redis eklentisi desteği gerektirir.

```php
<?php
namespace app\controller;

use support\Request;
use Webman\RedisQueue\Redis;

class Index
{
    public function queue(Request $request)
    {
        // Kuyruk adı
        $queue = 'send-mail';
        // Veri, doğrudan dizi olarak iletilir, seri hale getirmeye gerek yok
        $data = ['to' => 'tom@gmail.com', 'content' => 'hello'];
        // Mesaj gönderme
        Redis::send($queue, $data);
        // Gecikmeli mesaj gönderme, mesaj 60 saniye sonra işlenir
        Redis::send($queue, $data, 60);

        return response('redis queue test');
    }

}
```
`Redis::send()` başarıyla mesaj gönderildiğinde true döner, aksi halde false döner veya istisna fırlatır.

> **İpucu**
> Gecikmeli kuyruğun tüketim süresinde hata oluşabilir, örneğin tüketim hızı üretim hızından düşük olduğunda kuyruk tıkanabilir ve bu sayede tüketim gecikebilir. Bu durumu hafifletmek için daha fazla tüketim süreci eklemek faydalı olabilir.

## Mesaj Gönderme (Asenkron)
```php
<?php
namespace app\controller;

use support\Request;
use Webman\RedisQueue\Client;

class Index
{
    public function queue(Request $request)
    {
        // Kuyruk adı
        $queue = 'send-mail';
        // Veri, doğrudan dizi olarak iletilir, seri hale getirmeye gerek yok
        $data = ['to' => 'tom@gmail.com', 'content' => 'hello'];
        // Mesaj gönderme
        Client::send($queue, $data);
        // Gecikmeli mesaj gönderme, mesaj 60 saniye sonra işlenir
        Client::send($queue, $data, 60);

        return response('redis queue test');
    }

}
```
`Client::send()` geriye bir değer döndürmez, bu asenkron bir gönderimdir, mesajın %100 redis'e ulaşacağını garanti etmez.

> **İpucu**
> `Client::send()` asenkron olarak çalışır, bu sadece workerman çalışma ortamında kullanılabilir, komut satırı betiği için senkron arayüzü `Redis::send()` kullanılmalıdır.

## Diğer Projelere Mesaj Gönderme
Bazı durumlarda `webman\redis-queue` kullanılamayan diğer projelere mesaj göndermek gerekebilir, aşağıdaki fonksiyonu kullanarak kuyruğa mesaj gönderme işlemini gerçekleştirebilirsiniz.

```php
function redis_queue_send($redis, $queue, $data, $delay = 0) {
    $queue_waiting = '{redis-queue}-waiting';
    $queue_delay = '{redis-queue}-delayed';
    $now = time();
    $package_str = json_encode([
        'id'       => rand(),
        'time'     => $now,
        'delay'    => $delay,
        'attempts' => 0,
        'queue'    => $queue,
        'data'     => $data
    ]);
    if ($delay) {
        return $redis->zAdd($queue_delay, $now + $delay, $package_str);
    }
    return $redis->lPush($queue_waiting.$queue, $package_str);
}
```

Burada, `$redis` parametresi bir redis örneğidir. Örneğin, redis eklentisi kullanımı aşağıdaki gibidir:
```php
$redis = new Redis;
$redis->connect('127.0.0.1', 6379);
$queue = 'user-1';
$data= ['bazı', 'veriler'];
redis_queue_send($redis, $queue, $data);
````
## Tüketim
Tüketim işlemi yapılandırma dosyası `config/plugin/webman/redis-queue/process.php` içinde bulunur.
Tüketici dizini `app/queue/redis/` altındadır.

`php webman redis-queue:consumer my-send-mail` komutunu çalıştırmak, `app/queue/redis/MyMailSend.php` dosyasını oluşturacaktır.

> **İpucu**
> Eğer komut mevcut değilse manuel olarak da oluşturulabilir.

```php
<?php

namespace app\queue\redis;

use Webman\RedisQueue\Consumer;

class MyMailSend implements Consumer
{
    // Tüketilmesi gereken kuyruk adı
    public $queue = 'send-mail';

    // Bağlantı adı, plugin/webman/redis-queue/redis.php dosyasındaki bağlantıya karşılık gelir
    public $connection = 'default';

    // Tüketim
    public function consume($data)
    {
        // Dezilemelere gerek yok
        var_export($data); // Çıktı ['to' => 'tom@gmail.com', 'content' => 'hello']
    }
}
```

> **Not**
> Tüketim sırasında herhangi bir istisna veya hata fırlatılmazsa tüketim başarılı kabul edilir, aksi takdirde tüketim başarısız olur ve yeniden deneme kuyruğuna girer.
> redis-queue'un ack mekanizması yoktur, onu otomatik bir ack (herhangi bir istisna veya hata oluşmaması durumunda) olarak düşünebilirsiniz. Eğer tüketim sırasında mevcut iletişimin tüketilmediğini işaretlemek istiyorsanız, el ile bir istisna fırlatıp mevcut iletişimi yeniden deneme kuyruğuna gönderebilirsiniz. Bu aslında ack mekanizmasıyla tamamen aynıdır.

> **İpucu**
> Tüketici, aynı ileti **tekrar** tüketilmez ve tüketildikten sonra otomatik olarak kuyruktan kaldırılır, manuel olarak kaldırılması gerekmez.

> **İpucu**
> Tüketim işlemi aynı anda birden fazla sunucuda ve çoklu süreçte gerçekleştirilebilir ve aynı ileti **tekrar** tüketilmez. Yeni kuyruk eklemek için `process.php` dosyasını değiştirmenize gerek yoktur. Yeni kuyruk tüketicisi eklerken, yalnızca `app/queue/redis` altına karşılık gelen `Consumer` sınıfını eklemeniz ve sınıf özelliği `$queue` ile tüketilmesi gereken kuyruk adını belirtmeniz yeterlidir.

> **İpucu**
> Windows kullanıcıları, webman'ı başlatmak için `php windows.php` komutunu çalıştırmalıdır, aksi takdirde tüketim işlemi başlatılmaz.

## Farklı kuyruklar için farklı tüketim süreçleri ayarlama
Varsayılan olarak, tüm tüketiciler aynı tüketim sürecini paylaşırlar. Ancak bazen bazı kuyrukları ayrı ayrı tüketmek isteyebiliriz, örneğin yavaş işlemleri bir grup süreçte hızlı işlemleri ise başka bir grup süreçte tüketmek isteyebiliriz. Bunun için tüketicileri iki farklı dizine ayırabiliriz, örneğin `app_path().'/queue/redis/fast'` ve `app_path() . '/queue/redis/slow'` (dikkat: tüketici sınıfının isim alanını da değiştirmeniz gerekmektedir), yapılandırma şu şekilde olacaktır:

```php
return [
    ...diğer yapılandırmalar burada kısaltıldı...
    
    'redis_consumer_fast'  => [
        'handler'     => Webman\RedisQueue\Process\Consumer::class,
        'count'       => 8,
        'constructor' => [
            // Tüketici sınıf dizini
            'consumer_dir' => app_path() . '/queue/redis/fast'
        ]
    ],
    'redis_consumer_slow'  => [
        'handler'     => Webman\RedisQueue\Process\Consumer::class,
        'count'       => 8,
        'constructor' => [
            // Tüketici sınıf dizini
            'consumer_dir' => app_path() . '/queue/redis/slow'
        ]
    ]
];
```

Dizin sınıflandırması ve ilgili yapılandırma ile farklı tüketiciler için farklı süreçleri kolayca ayarlayabiliriz.

## Çoklu redis yapılandırması
#### Yapılandırma
`config/plugin/webman/redis-queue/redis.php`
```php
<?php
return [
    'default' => [
        'host' => 'redis://192.168.0.1:6379',
        'options' => [
            'auth' => null,       // şifre, string türü, isteğe bağlı
            'db' => 0,            // veritabanı
            'max_attempts'  => 5, // tüketim başarısız olduğunda, yeniden deneme sayısı
            'retry_seconds' => 5, // yeniden deneme aralığı, saniye cinsinden
        ]
    ],
    'other' => [
        'host' => 'redis://192.168.0.2:6379',
        'options' => [
            'auth' => null,       // şifre, string türü, isteğe bağlı
            'db' => 0,             // veritabanı
            'max_attempts'  => 5, // tüketim başarısız olduğunda, yeniden deneme sayısı
            'retry_seconds' => 5, // yeniden deneme aralığı, saniye cinsinden
        ]
    ],
];
```

Yapılandırmada `other` key'ine sahip bir redis yapılandırması eklenmiştir.

####  Çoklu redis mesaj gönderimi

```php
// `default` anahtarına sahip kuyruğa mesaj gönder
Client::connection('default')->send($queue, $data);
Redis::connection('default')->send($queue, $data);
//  Aynı işlevi görür
Client::send($queue, $data);
Redis::send($queue, $data);

// `other` anahtarına sahip kuyruğa mesaj gönder
Client::connection('other')->send($queue, $data);
Redis::connection('other')->send($queue, $data);
```

####  Çoklu redis tüketimi
Tüketim yapılandırmasında `other` anahtarına sahip kuyruğa mesaj gönderme
```php
namespace app\queue\redis;

use Webman\RedisQueue\Consumer;

class SendMail implements Consumer
{
    // Tüketilmesi gereken kuyruk adı
    public $queue = 'send-mail';

    // === Buraya `other` olarak ayarlandığından, tüketim yapılandırmasında `other` anahtarına sahip kuyruk tüketimdir ===
    public $connection = 'other';

    // Tüketim
    public function consume($data)
    {
        // Dezilemelere gerek yok
        var_export($data);
    }
}
```
## Yaygın Sorunlar

**Neden `Workerman\Redis\Exception: Workerman Redis Wait Timeout (600 seconds)` hatası alıyorum?**

Bu hata sadece asenkron gönderim arabiriminde `Client::send()` fonksiyonunda mevcut olabilir. Asenkron gönderim, öncelikle iletileri yerel bellekte saklar ve işlem boşta olduğunda iletileri redis'e gönderir. Eğer redis, iletilerin üretim hızından daha yavaş bir hızda ileti alıyorsa veya işlem sürekli olarak başka bir işle uğraşıp bellekteki iletileri redis'e yeterince hızlı aktaramazsa, iletiler sıkışabilir. Eğer iletiler 600 saniyeden fazla süre sıkışırsa, bu hataya neden olur.

Çözüm: İleti gönderimi için eşzamanlı gönderim arabirimini `Redis::send()` kullanın.
