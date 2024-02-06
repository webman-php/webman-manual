# Yaşam Döngüsü

## İşlem Yaşam Döngüsü
- Her işlem uzun bir yaşam döngüsüne sahiptir.
- Her işlem ayrı çalışır ve birbirlerine müdahale etmez.
- Her işlem yaşam döngüsü boyunca birden çok isteği işleyebilir.
- İşlem, `stop`, `reload`, `restart` komutları aldığında çıkış yapar ve bu döngüyü sonlandırır.

> **İpucu**
> Her işlem birbirinden bağımsızdır, yani her işlem kendi kaynaklarını, değişkenlerini ve sınıf örneklerini sürdürür, her işlemin kendi veritabanı bağlantısı bulunduğunu gösterir, bazı tekil örneklerin her işlemde bir kez başlatıldığı durumlarında, bu da demektir ki birden fazla işlemde birçok kez başlatılacaktır.

## İstek Yaşam Döngüsü
- Her istek bir `$request` nesnesi oluşturur.
- `$request` nesnesi istek işlemi tamamlandıktan sonra atılır.

## Kontrolcü Yaşam Döngüsü
- Her kontrolcü, her işlemde sadece bir kez örneklendirilir, birden çok işlemde birden fazla örneklenir (kontrolcü yeniden kullanımı kapatıldığında hariç, bkz. [Kontrolcü Yaşam Döngüsü](https://www.workerman.net/doc/webman/controller.html#%E7%94%9F%E5%91%BD%E5%91%A8%E6%9C%9F)).
- Kontrolcü örnekleri mevcut işlem içinde birden çok istem tarafından paylaşılır (kontrolcü yeniden kullanımı kapatıldığında hariç).
- Kontrolcü yaşam döngüsü, işlem çıkışından sonra sona erer (kontrolcü yeniden kullanımı kapatıldığında hariç).

## Değişken Yaşam Döngüsü Hakkında
webman, PHP'ye dayalı olduğu için tamamen PHP'nin değişken geri dönüşüm mekanizmasını takip eder. İş mantığı tarafında oluşturulan geçici değişkenler ve `new` anahtar kelimesi ile oluşturulan sınıf örnekleri, fonksiyon veya yöntem sona erdikten sonra otomatik olarak atılacaktır, manuel olarak `unset` kullanmaya gerek yoktur. Yani webman geliştirme deneyimi, geleneksel çerçeve geliştirme deneyimi ile temelde aynıdır. Aşağıdaki örnekte `$foo` örneğinin `index` metodunun tamamlanmasıyla otomatik olarak serbest bırakılacağını gösterir:
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
Eğer bir sınıfın örneğinin yeniden kullanılmasını istiyorsanız, sınıfı sınıfın statik özelliklerine veya uzun ömürlü nesnelerine (örneğin kontrolcü) kaydedebilir, ayrıca sınıfın örneğini başlatmak için Container kütüphanesinin `get` metodu kullanabilirsiniz, örn.:
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
`Container::get()` metodu sınıfın örneğini oluşturur ve saklar, aynı parametrelerle tekrar çağrıldığında daha önce oluşturulan sınıf örneğini döndürecektir.

> **Not**
> `Container::get()`, sadece yapılandırma parametresi olmayan örnekleri başlatabilir. `Container::make()`, yapılandırma parametreleri olan bir örnek oluşturabilir, ancak `Container::get()` ile farklı olarak, `Container::make()` örneği yeniden kullanmaz, yani aynı parametrelerle çağrılsa bile her zaman yeni bir örnek döndürür.

# Bellek Sızıntısı Hakkında
Çoğu durumda, iş mantığımızda bellek sızıntısı meydana gelmez (çok az kullanıcı geri bildiriminde bellek sızıntısı olduğunu belirtti), sadece uzun ömürlü veri dizilerinin sonsuz genişlemesine dikkat etmemiz yeterlidir. Aşağıdaki kodu inceleyin:
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
Kontrolcü varsayılan olarak uzun ömürlüdür (kontrolcü yeniden kullanımı kapatıldığında hariç), aynı şekilde kontrolcünün `$data` dizisi özelliği de uzun ömürlüdür, bu nedenle `foo/index` isteği devam ettikçe, `$data` dizisi öğeleri artarak bellek sızıntısına neden olur.

Daha fazla bilgi için [Bellek Sızıntısı](./memory-leak.md) bölümüne bakınız.
