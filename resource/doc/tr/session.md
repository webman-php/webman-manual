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

`$request->session()` kullanarak `Workerman\Protocols\Http\Session` örneğini elde edebilir ve örneğin yöntemleri aracılığıyla oturum verilerini ekleyebilir, değiştirebilir veya silebilirsiniz.

> Not: Oturum nesnesi yok edildiğinde oturum verileri otomatik olarak kaydedilir, bu nedenle `global` dizisine veya sınıf üyelerine kaydetmeyin ve bunun sonucunda oturumun kaydedilemez duruma gelmesine neden olabilirsiniz.

## Tüm oturum verilerini almak
```php
$session = $request->session();
$all = $session->all();
```
Bu bir dizi döndürür. Eğer herhangi bir oturum verisi yoksa boş bir dizi döner.

## Belirli bir oturum değerini almak
```php
$session = $request->session();
$name = $session->get('name');
```
Veri mevcut değilse null döner.

Ayrıca, get yöntemine ikinci bir argüman olarak varsayılan bir değer iletebilirsiniz. Eğer oturum dizisinde ilgili değer bulunamazsa varsayılan değeri döner. Örneğin:
```php
$session = $request->session();
$name = $session->get('name', 'tom');
```

## Oturumu saklamak
Bir veriyi saklarken set yöntemini kullanın.
```php
$session = $request->session();
$session->set('name', 'tom');
```
set yöntemi bir değer döndürmez, oturum nesnesi yok olduğunda oturum otomatik olarak kaydedilir.

Birden çok değer saklarken put yöntemini kullanın.
```php
$session = $request->session();
$session->put(['name' => 'tom', 'age' => 12]);
```
Aynı şekilde, put yöntemi bir değer döndürmez.

## Oturum verisini silmek
Bir veya birkaç oturum verisini silmek için `forget` yöntemini kullanın.
```php
$session = $request->session();
// Bir öğe sil
$session->forget('name');
// Birden fazla öğe sil
$session->forget(['name', 'age']);
```

Ayrıca, sistem `delete` yöntemini sağlar, `forget` yönteminden farkı sadece bir öğeyi silebilmesidir.
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
Bu, aşağıdaki kodun etkisiyle aynıdır
```php
$session = $request->session();
$value = $session->get($name);
$session->delete($name);
```
Eğer ilgili oturum mevcut değilse null döner.

## Tüm oturum verilerini silmek
```php
$request->session()->flush();
```
Bir değer döndürmez, oturum nesnesi yok olduğunda oturum otomatik olarak depolamadan kaldırılır.

## Belirli oturum verisinin varlığını kontrol etmek
```php
$session = $request->session();
$has = $session->has('name');
```
Yukarıdaki kod, ilgili oturum mevcut olmadığında veya oturum değeri null olduğunda false döner, aksi takdirde true döner.

```php
$session = $request->session();
$has = $session->exists('name');
```
Yukarıdaki kod, oturum verisinin varlığını kontrol etmek için kullanılır, farkı ise ilgili oturum öğesi değeri null olduğunda bile true döner.

## Yardımcı işlev session()
> 2020-12-09 Eklendi

webman, aynı işlevselliği tamamlamak için `session()` yardımcı işlevini sunar.
```php
// Oturum örneğini al
$session = session();
// Aynıdır
$session = $request->session();

// Bir değer al
$value = session('key', 'default');
// Aynıdır
$value = session()->get('key', 'default');
// Aynıdır
$value = $request->session()->get('key', 'default');

// Oturuma bir değer atama
session(['key1'=>'value1', 'key2' => 'value2']);
// Aynıdır
session()->put(['key1'=>'value1', 'key2' => 'value2']);
// Aynıdır
$request->session()->put(['key1'=>'value1', 'key2' => 'value2']);
```
## Yapılandırma Dosyası
Session yapılandırma dosyası `config/session.php` konumunda bulunur ve aşağıdaki gibi içeriklere sahiptir:
```php
use Webman\Session\FileSessionHandler;
use Webman\Session\RedisSessionHandler;
use Webman\Session\RedisClusterSessionHandler;

return [
    // FileSessionHandler::class veya RedisSessionHandler::class veya RedisClusterSessionHandler::class 
    'handler' => FileSessionHandler::class,
    
    // handler için FileSessionHandler::class ise değer file,
    // handler için RedisSessionHandler::class ise değer redis
    // handler için RedisClusterSessionHandler::class ise değer redis_cluster yani redis kümesi
    'type'    => 'file',

    // Farklı handler'lar için farklı yapılandırmalar kullanılır
    'config' => [
        // type file ise yapılandırma
        'file' => [
            'save_path' => runtime_path() . '/sessions',
        ],
        // type redis ise yapılandırma
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

    'session_name' => 'PHPSID', // session_id'sini saklayan çerez adı

    // === Aşağıdaki yapılandırmalar webman-framework>=1.3.14 workerman>=4.0.37 gerektirir ===
    'auto_update_timestamp' => false,  // Otomatik oturumu yenilemek için, varsayılan olarak kapalı
    'lifetime' => 7*24*60*60,          // Oturum süresi
    'cookie_lifetime' => 365*24*60*60, // session_id çerezinin ömrü
    'cookie_path' => '/',              // session_id çerezinin yolu
    'domain' => '',                    // session_id çerezin domaini
    'http_only' => true,               // httpOnly özelliğini etkinleştirme, varsayılan olarak etkin
    'secure' => false,                 // Sadece https üzerinde oturumu aç, varsayılan olarak kapalı
    'same_site' => '',                 // CSRF saldırıları ve kullanıcı izimini engellemek için, strict/lax/none seçenekleri
    'gc_probability' => [1, 1000],     // Oturum süresinin olasılığı
];
```

> **Not**
> webman 1.4.0'dan itibaren SessionHandler'ın isim alanı değişti, önceki 
> use Webman\FileSessionHandler;  
> use Webman\RedisSessionHandler;  
> use Webman\RedisClusterSessionHandler;  
> yerine 
> use Webman\Session\FileSessionHandler;  
> use Webman\Session\RedisSessionHandler;  
> use Webman\Session\RedisClusterSessionHandler;  
> olarak değiştirildi.


## Süresi Ayarları
webman-framework < 1.3.14 sürümünde, session süresi `php.ini` içinde ayarlanmalıdır.

```ini
session.gc_maxlifetime = x
session.cookie_lifetime = x
session.gc_probability = 1
session.gc_divisor = 1000
```

Örneğin, 1440 saniye süre belirlendiği zaman, aşağıdaki gibi yapılandırılır:
```ini
session.gc_maxlifetime = 1440
session.cookie_lifetime = 1440
session.gc_probability = 1
session.gc_divisor = 1000
```

> **Not**
> `php.ini` dosyasının konumunu bulmak için `php --ini` komutunu kullanabilirsiniz.
