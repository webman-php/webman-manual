# Yaşam döngüsü

## Süreç Yaşam Döngüsü
- Her sürecin uzun bir yaşam döngüsü vardır
- Her süreç bağımsız çalışır ve birbirlerini etkilemez
- Her süreç yaşam döngüsü boyunca birden çok isteği işleyebilir
- Süreç, `stop`, `reload`, `restart` komutlarını aldığında çıkış yaparak bu yaşam döngüsünü sonlandırır.

> **İpucu**
> Her süreç bağımsız ve birbirini etkilemez, bu da her sürecin kendi kaynaklarını, değişkenlerini ve sınıf örneklerini koruduğu anlamına gelir. Bu durum, her sürecin kendi veritabanı bağlantısına sahip olması, bazı tekil örneklerin her süreçte bir kez başlatılması anlamına gelir. Bu durumda, çoklu süreçlerde başlatılacaktır.

## İstek Yaşam Döngüsü
- Her istek bir `$request` nesnesi oluşturur
- `$request` nesnesi isteğin işlemesinin ardından toplanır.

## Denetleyici Yaşam Döngüsü
- Her denetleyici her süreçte sadece bir kez örneklenir, çoklu süreçlerde çok kez örneklenir (denetleyici yeniden kullanımı kapatılmışsa, [Denetleyici Yaşam Döngüsü](https://www.workerman.net/doc/webman/controller.html#%E7%94%9F%E5%91%BD%E5%91%A8%E6%9C%9F)'ne bakınız)
- Denetleyici örnekleri mevcut süreç içindeki birden çok istekle paylaşılır (denetleyici yeniden kullanımı kapatılmışsa)
- Denetleyici yaşam döngüsü süreç çıkışından sonra sona erer (denetleyici yeniden kullanımı kapatılmışsa)

## Değişken Yaşam Döngüsü Hakkında
webman, PHP'ye dayalı olduğu için tamamen PHP değişken toplama mekanizmasını takip eder. İş mantığında oluşturulan geçici değişkenler, `new` anahtar kelimesiyle oluşturulan sınıf örnekleri, fonksiyon veya yöntem sona erdikten sonra otomatik olarak toplanır, manuel olarak `unset`ile serbest bırakılmasına gerek yoktur. Yani webman geliştirme deneyimi geleneksel çerçeve geliştirme deneyimiyle hemen hemen aynıdır. Aşağıdaki örnekte `$foo` örneğinin, `index` methodu tamamlandığında otomatik olarak serbest bırakılacağı anlamına gelir:
```php
<?php

namespace app\controller;

use app\service\Foo;
use support\Request;

class IndexController
{
    public function index(Request $request)
    {
        $foo = new Foo(); // Burada bir Foo sınıfı olduğunu varsayalım
        return response($foo->sayHello());
    }
}
```
Bir sınıf örneğinin yeniden kullanılmasını istiyorsanız, sınıfı sınıfın statik özelliklerine veya uzun ömürlü nesne özelliklerine kaydetmek veya sınıf örneğini başlatmak için Container konteynırının `get` yöntemini kullanabilirsiniz, örneğin:
```php
<?php

namespace app\controller;

use app\service\Foo;
use support\Container;
use support\Request;

class IndexController
{
    public function index(Request $request)
    {
        $foo = Container::get(Foo::class);
        return response($foo->sayHello());
    }
}
```

`Container::get()` yöntemi, bir sınıf örneği oluşturur ve kaydeder, aynı parametrelerle tekrar çağrıldığında daha önce oluşturulan sınıf örneğini döndürür.

> **Not**
> `Container::get()`, parametre almayan örnekler yalnızca başlatır. `Container::make()` parametre alan yapıcı fonksiyonlu örnekler oluşturabilir, ancak `Container::get()` ile farklı olarak, `Container::make()` örneği yeniden kullanmaz, yani aynı parametrelerle bile `Container::make()` her zaman yeni bir örnek döndürür.

## Bellek Sızıntısı Hakkında
Çoğu durumda, iş mantığımızda bellek sızıntısı olmaz (kullanıcıların çok az geri bildirimde bulunduğu çok az bellek sızıntısı yaşandığı durumlar haricinde), sadece uzun ömürlü dizilerin sonsuz genişlemesine dikkat etmek yeterlidir. Aşağıdaki kod örneğine bakınız:
```php
<?php
namespace app\controller;

use support\Request;

class FooController
{
    // Dizi özelliği
    public $data = [];
    
    public function index(Request $request)
    {
        $this->data[] = time();
        return response('hello index');
    }

    public function hello(Request $request)
    {
        return response('hello webman');
    }
}
```
Denetleyici varsayılan olarak uzun bir ömre sahiptir (denetleyici yeniden kullanımı kapatılmıştır), aynı şekilde denetleyicinin `$data` dizisi özelliği de uzun bir ömre sahiptir ve `foo/index` isteği devam ettikçe, `$data` dizisi elemanları giderek artar ve bellek sızıntısına neden olur.

Daha fazla bilgi için [Bellek Sızıntısı](./memory-leak.md) bölümüne bakınız.
