# Koşullu

> **Koşul gereksinimleri**
> PHP>=8.1workerman>=5.0 webman-framework>=1.5 revolt/event-loop>1.0.0
> webman yükseltme komutu `composer require workerman/webman-framework ^1.5.0`
> workerman yükseltme komutu `composer require workerman/workerman ^5.0.0`
> Fiber koşulu yüklemek için: `composer require revolt/event-loop ^1.0.0`

# Örnek
### Gecikmeli yanıt

```php
<?php

namespace app\controller;

use support\Request;
use Workerman\Timer;

class TestController
{
    public function index(Request $request)
    {
        // 1.5 saniye bekleyin
        Timer::sleep(1.5);
        return $request->getRemoteIp();
    }
}
```
`Timer::sleep()` PHP’nin yerleşik `sleep()` fonksiyonuna benzer, fakat `Timer::sleep()` işlemi engellemez

### HTTP isteği gönderme

> **Dikkat**
> `composer require workerman/http-client ^2.0.0` kurulumu gereklidir

```php
<?php

namespace app\controller;

use support\Request;
use Workerman\Http\Client;

class TestController
{
    public function index(Request $request)
    {
        static $client;
        $client = $client ?: new Client();
        $response = $client->get('http://example.com'); // Asenkron isteğin senkron bir şekilde başlatılması
        return $response->getBody()->getContents();
    }
}
```
Ayrıca `$client->get('http://example.com')` isteği engellenmeyen bir istektir, bu webman'da bloke olmayan bir şekilde HTTP isteği başlatmak için kullanılır.

Daha fazlası için [workerman/http-client](https://www.workerman.net/doc/workerman/components/workerman-http-client.html) sitesini kontrol edin.

### support\Context sınıfının eklenmesi

`support\Context` sınıfı istek bağlamı verilerini saklamak için kullanılır, istek tamamlandığında, ilgili bağlam verileri otomatik olarak silinir. Yani bağlam verilerinin yaşam döngüsü istek yaşam döngüsüne bağlıdır. `support\Context`, Fiber, Swoole, Swow koşul ortamlarını destekler.

### Swoole Coroutine
Swoole uzantısını kurduktan sonra (swoole>=5.0) config/server.php dosyasını yapılandırarak Swoole koşullarını etkinleştirin
```php
'event_loop' => \Workerman\Events\Swoole::class,
```

Daha fazlası için [workerman event-loop](https://www.workerman.net/doc/workerman/appendices/event.html) sitesini kontrol edin.

### Global Değişken Kirliliği

Koşul ortamında **istekle ilgili** durum bilgilerini global değişkenlerde veya statik değişkenlerde saklamak yasaktır, çünkü bu global değişken kirliliğine neden olabilir, örneğin:

```php
<?php

namespace app\controller;

use support\Request;
use Workerman\Timer;

class TestController
{
    protected static $name = '';

    public function index(Request $request)
    {
        static::$name = $request->get('name');
        Timer::sleep(5);
        return static::$name;
    }
}
```

İşlem sayısını 1 olarak ayarladığımızda, iki istek ardışık olarak gönderildiğinde  
http://127.0.0.1:8787/test?name=lilei  
http://127.0.0.1:8787/test?name=hanmeimei  
İki isteğin beklenen sonuçlarla `lilei` ve `hanmeimei` olması gerektiğini bekleriz, ancak aslında her ikisinin de `hanmeimei` olduğunu görürüz.
Bu, ikinci istekin statik değişken `$name`'i üzerine yazmasından dolayıdır, ilk istek uyku modundan çıkarken, statik değişken `$name` artık `hanmeimei` olmuştur.

**Doğru yöntem, istek durum verilerini bağlamda saklamaktır**
```php
<?php

namespace app\controller;

use support\Request;
use support\Context;
use Workerman\Timer;

class TestController
{
    public function index(Request $request)
    {
        Context::set('name', $request->get('name'));
        Timer::sleep(5);
        return Context::get('name');
    }
}
```

**Yerel değişkenler veri kirliliğine neden olmaz**
```php
<?php

namespace app\controller;

use support\Request;
use support\Context;
use Workerman\Timer;

class TestController
{
    public function index(Request $request)
    {
        $name = $request->get('name');
        Timer::sleep(5);
        return $name;
    }
}
```
Çünkü `$name` yerel bir değişkendir, koşullar arasında yerel değişkenlere erişilemez, bu nedenle yerel değişken kullanımı koşul güvenlidir.

# Koşullar Hakkında
Koşullar bir gümüş kurşun değildir, koşulların dahil edilmesi global değişken/statik değişken kirliliği sorunlarına dikkat etmek, bağlamı ayarlamak gerektiği anlamına gelir. Ayrıca, koşullar ortamındaki hata ayıklama, engellenen programlamaya göre daha karmaşıktır.

webman'ın engellenen programlaması aslında yeterince hızlıdır, [techempower.com](https://www.techempower.com/benchmarks/#section=data-r21&l=zijnjz-6bj&test=db&f=1ekg-cbcw-2t4w-27wr68-pc0-iv9slc-0-1ekgw-39g-kxs00-o0zk-4fu13d-2x8do8-2) sitesindeki son üç yılın veri setine göre, webman'ın, veritabanı işletmeleeri dahil, Go'nun web çerçevesi gin, echo gibi web çerçevelerinden neredeyse 2 kat daha fazla performans gösterdiği, geleneksel çerçeve Laravel'e göre yaklaşık 40 kat daha performanslı olduğunu görüyoruz.
![](../../assets/img/benchemarks-go-sw.png?)

Veritabanı, redis gibi iç ağda olduğunda, çoklu işlem engellenen programlamadan daha performanslı olabilir, çünkü veritabanı, redis gibi sistemler yeterince hızlı ise, koşul oluşturma, planlama, yok etme maliyetleri, işleç değişim maliyetlerinden daha fazla olabilir, bu yüzden koşulları eklemek, performansı belirgin bir şekilde artırmayabilir.

# Koşullar Ne Zaman Kullanılmalıdır
İşte, işlem hızlıca başlatma ihtiyacı olduğunda, örneğin, iş, üçüncü parti bir API'ye erişim gerektirdiğinde [workerman/http-client](https://www.workerman.net/doc/workerman/components/workerman-http-client.html) kullanılarak asenkron HTTP çağrısı yapmak, uygulama eşzamanlı kapasitesini artırmak için kullanılabilir.
