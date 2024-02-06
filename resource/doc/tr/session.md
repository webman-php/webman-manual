# Oturum Yönetimi

## Örnek
```php
<?php
namespace app\controller;

use support\Request;

class UserController
{
    public function hello(Request $request)
    {
        $name = $request->get('name');
        $session = $request->session();
        $session->set('name', $name);
        return response('hello ' . $session->get('name'));
    }
}
```

`$request->session();` kullanarak `Workerman\Protocols\Http\Session` örneğini alın ve örnek yöntemleri kullanarak oturum verilerini ekleyin, değiştirin veya silin.

> Not: Oturum nesnesi yok edildiğinde otomatik olarak oturum verileri kaydedilir. Bu nedenle, `$request->session()` tarafından döndürülen nesneyi oturumun kaydedilmemesine neden olacak şekilde global bir diziye veya sınıf üyesine kaydetmeyin.

## Tüm oturum verilerini almak
```php
$session = $request->session();
$all = $session->all();
```
Bir dizi döndürür. Herhangi bir oturum verisi yoksa, boş bir dizi döndürür.

## Bir oturumdaki belirli bir değeri almak
```php
$session = $request->session();
$name = $session->get('name');
```
Veri mevcut değilse null döndürür.

Ayrıca get yöntemine ikinci bir parametre olarak varsayılan bir değer iletebilirsiniz. Eğer oturum dizisinde karşılık gelen bir değer bulunamazsa, varsayılan değeri döndürür. Örneğin:
```php
$session = $request->session();
$name = $session->get('name', 'tom');
```

## Oturumu saklamak
Bir veriyi saklamak için set yöntemi kullanılır.
```php
$session = $request->session();
$session->set('name', 'tom');
```
set yöntemi bir değer döndürmez, oturum nesnesi yok edildiğinde oturum otomatik olarak kaydedilir.

Birden çok değer saklanırken put yöntemi kullanılır.
```php
$session = $request->session();
$session->put(['name' => 'tom', 'age' => 12]);
```
Aynı şekilde, put yöntemi de bir değer döndürmez.

## Oturum verilerini silmek
Bir veya birden fazla oturum verisini silmek için `forget` yöntemi kullanılır.
```php
$session = $request->session();
// Bir öğe silmek
$session->forget('name');
// Birden fazla öğe silmek
$session->forget(['name', 'age']);
```

Ayrıca sistem, forget yöntemi yerine sadece bir öğe silebilen delete yöntemini sağlar.
```php
$session = $request->session();
// $session->forget('name'); ile aynı
$session->delete('name');
```

## Bir oturum değerini alıp silmek
```php
$session = $request->session();
$name = $session->pull('name');
```
Bu, aşağıdaki kodla aynı etkiye sahiptir
```php
$session = $request->session();
$value = $session->get($name);
$session->delete($name);
```
Eğer karşılık gelen oturum mevcut değilse, null döner.

## Tüm oturum verilerini silmek
```php
$request->session()->flush();
```
Değer döndürmez, oturum nesnesi yok edildiğinde oturum otomatik olarak depolamadan silinir.

## Karşılık gelen oturum verilerinin varlığını kontrol etmek
```php
$session = $request->session();
$has = $session->has('name');
```
Yukarıdaki kod, karşılık gelen oturum mevcut değilse veya karşılık gelen oturum değeri nullsa false döndürür, aksi takdirde true döndürür.

```
$session = $request->session();
$has = $session->exists('name');
```
Yukarıdaki kod da oturum verilerinin varlığını kontrol etmek için kullanılır; farkı, karşılık gelen oturum öge değeri nullsa bile true döndürmesidir.

## Helper fonksiyon: session()
> 2020-12-09 yenilik

webman, aynı işlevselliği sağlamak için yardımcı fonksiyon `session()` sağlar.
```php
// Oturum örneğini al
$session = session();
// Şu kodla aynıdır
$session = $request->session();

// Bir değeri al
$value = session('key', 'default');
// Şu kodla aynıdır
$value = session()->get('key', 'default');
// Şu kodla aynıdır
$value = $request->session()->get('key', 'default');

// Oturuma değer atama
session(['key1'=>'değer1', 'key2' => 'değer2']);
// Şu kodla aynıdır
session()->put(['key1'=>'değer1', 'key2' => 'değer2']);
// Şu kodla aynıdır
$request->session()->put(['key1'=>'değer1', 'key2' => 'değer2']);

```

## Config dosyası
Oturum yapılandırma dosyası `config/session.php` içindedir, içeriği aşağıdakine benzer:
```php
use Webman\Session\FileSessionHandler;
use Webman\Session\RedisSessionHandler;
use Webman\Session\RedisClusterSessionHandler;

return [
    // FileSessionHandler::class veya RedisSessionHandler::class veya RedisClusterSessionHandler::class 
    'handler' => FileSessionHandler::class,
    
    // handler değeri FileSessionHandler::class için file,
    // handler değeri RedisSessionHandler::class için redis
    // handler değeri RedisClusterSessionHandler::class için redis_cluster (redis kümesi)
    'type'    => 'file',

    // Farklı handler'lar için farklı yapılandırmalar kullanılır
    'config' => [
        // type değeri file olduğunda yapılandırma
        'file' => [
            'save_path' => runtime_path() . '/sessions',
        ],
        // type değeri redis olduğunda yapılandırma
        'redis' => [
            'host'      => '127.0.0.1',
            'port'      => 6379,
            'auth'      => '',
            'timeout'   => 2,
            'database'  => '',
            'prefix'    => 'redis_session_',
        ],
        'redis_cluster' => [
            'host'    => ['127.0.0.1:7000', '127.0.0.1:7001', '127.0.0.1:7001'],
            'timeout' => 2,
            'auth'    => '',
            'prefix'  => 'redis_session_',
        ]
        
    ],

    'session_name' => 'PHPSID', // Oturum kimliğini saklayan çerez adı
    
    // === webman-framework >= 1.3.14 workerman >= 4.0.37 gerekli olan aşağıdaki yapılandırma ===
    'auto_update_timestamp' => false,  // Oturumu otomatik olarak yenilemek için, varsayılan olarak kapalı
    'lifetime' => 7*24*60*60,          // Oturumun süresi
    'cookie_lifetime' => 365*24*60*60, // Oturum kimliğini saklayan çerezin süresi
    'cookie_path' => '/',              // Oturum kimliğini saklayan çerezin yolu
    'domain' => '',                    // Oturum kimliğini saklayan çerezin etki alanı
    'http_only' => true,               // httpOnly özelliğini etkinleştirmek için, varsayılan olarak etkin
    'secure' => false,                 // Sadece https'te oturumu etkinleştirmek için, varsayılan olarak kapalı
    'same_site' => '',                 // CSRF saldırılarına ve kullanıcı izleme için, seçenek değerleri: strict/lax/none
    'gc_probability' => [1, 1000],     // Oturumların geri dönüşüm olasılığı
];
```

> **Not** 
> 1.4.0 sürümünden itibaren webman, SessionHandler'ın ad alanını değiştirdi, önceden
> use Webman\FileSessionHandler;  
> use Webman\RedisSessionHandler;  
> use Webman\RedisClusterSessionHandler;  
> şimdi
> use Webman\Session\FileSessionHandler;  
> use Webman\Session\RedisSessionHandler;  
> use Webman\Session\RedisClusterSessionHandler;  

## Süre ayarı yapılandırması
Webman-framework < 1.3.14 olduğunda, webman'daki oturum süresi yapılandırmalarını `php.ini` dosyasında yapılmalıdır.

```
session.gc_maxlifetime = x
session.cookie_lifetime = x
session.gc_probability = 1
session.gc_divisor = 1000
```

Örneğin, süreyi 1440 saniye olarak ayarlamak için, aşağıdaki gibi yapılandırabilirsiniz
```
session.gc_maxlifetime = 1440
session.cookie_lifetime = 1440
session.gc_probability = 1
session.gc_divisor = 1000
```

> **İpucu**
> `php --ini` komutunu kullanarak `php.ini` dosyasının konumunu bulabilirsiniz.
