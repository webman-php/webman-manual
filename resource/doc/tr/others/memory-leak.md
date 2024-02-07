# Bellek sızıntısı hakkında
webman, kalıcı bellek çerçevesidir, bu yüzden bellek sızıntısına biraz dikkat etmemiz gerekiyor. Ancak geliştiriciler endişelenmeye gerek duymazlar, çünkü bellek sızıntısı, son derece aşırı koşullarda gerçekleşir ve kolayca önlenir. webman geliştirme deneyimi, geleneksel çerçeve geliştirme deneyimi ile neredeyse aynıdır, fazladan bellek yönetimi işlemleri yapmanıza gerek yok.

> **İpucu**
> webman'e dahil edilmiş olan monitor süreci, tüm süreçlerin bellek kullanımını izler. Eğer bir sürecin bellek kullanımı, php.ini'deki `memory_limit` değerine ulaşmak üzereyse, ilgili süreci otomatik olarak güvenli bir şekilde yeniden başlatır ve bu sırada işleme herhangi bir zarar vermez.

## Bellek sızıntısının tanımı
webman'in isteğe bağlı olarak kullanılan belleği **sürekli artar** ve yüzlerce MB hatta daha fazlasına ulaşırken bellek sızıntısı meydana gelir. Eğer bellek artışı oluyorsa, ancak sonradan artış yaşanmıyorsa bu bellek sızıntısı olarak kabul edilmez.

Genellikle süreçlerin birkaç MB bellek kullanması oldukça normal bir durumdur, ancak bir sürecin, çok büyük istekleri işlediği veya büyük bir bağlantı havuzunu koruduğu durumlarda, tek bir sürecin bellek kullanımı yüzlerce MB'a ulaşabilir. Bu bellek kullanımının tamamı işletim sistemine geri verilmeyebilir. Bunun yerine tekrar kullanım için saklanabilir ve bu nedenle büyük bir isteği işledikten sonra bellek kullanımının artması ve belleği serbest bırakmaması durumu ortaya çıkabilir, bu normal bir durumdur. (gc_mem_caches() metodu çağrılarak boş belleğin bir kısmı serbest bırakılabilir)

## Bellek sızıntısı nasıl meydana gelir
**Bellek sızıntısı, aşağıdaki iki koşulu karşıladığında meydana gelir:**
1. **Uzun ömürlü bir** dizi varlığı (normal bir dizi olmadığına dikkat edin)
2. Ve bu **uzun ömürlü** dizi, sürekli genişler (iş, bu diziye sonsuz bir şekilde veri ekler fakat temizlik yapmaz)

Eğer 1 ve 2 koşulları **aynı anda** sağlanıyorsa (aynı anda sağlanması önemlidir), bellek sızıntısı oluşur. Aksi takdirde yukarıdaki koşullardan herhangi birini sağlamıyorsa veya sadece birini sağlıyorsa, bellek sızıntısı oluşmaz.

## Uzun ömürlü diziler
webman'de uzun ömürlü diziler şunları içerir:
1. static anahtar kelimesi ile tanımlanan diziler
2. Singleton (Tekil örnek) dizisi özellikleri
3. global anahtar kelimesi ile tanımlanan diziler

> **Not**
> webman'de uzun ömürlü verilerin kullanılmasına izin verilir, ancak verinin içindeki verinin sınırlı olduğu ve öğe sayısının sonsuz genişlemeyeceğinden emin olunması gerekir.

Aşağıda sırasıyla örnekler verilmiştir.

#### Sonsuz genişleyen statik dizi
```php
class Foo
{
    public static $data = [];
    public function index(Request $request)
    {
        self::$data[] = time();
        return response('hello');
    }
}
```

`static` anahtar kelimesi ile tanımlanan `$data` dizisi uzun ömürlü bir dizidir ve örnek içindeki `$data` dizisi, istekler arttıkça sürekli olarak genişler ve bu nedenle bellek sızıntısına neden olur.

#### Sonsuz genişleyen tekil (Singleton) dizi özellikleri
```php
class Cache
{
    protected static $instance;
    public $data = [];
    
    public function instance()
    {
        if (!self::$instance) {
            self::$instance = new self;
        }
        return self::$instance;
    }
    
    public function set($key, $value)
    {
        $this->data[$key] = $value;
    }
}
```

Çağrılan kod
```php
class Foo
{
    public function index(Request $request)
    {
        Cache::instance()->set(time(), time());
        return response('hello');
    }
}
```

`Cache::instance()`, bir Cache tekil örneğini döndürür, bu uzun ömürlü bir sınıf örneğidir. `$data` özelliği `static` anahtar kelimesi kullanılmamasına rağmen, sınıfın kendisi uzun ömürlü olduğundan, $data dizisi de uzun ömürlüdür. Sürekli olarak farklı anahtarlarla veri eklenmesiyle, program belleği gitgide artar ve bellek sızıntısına neden olur.

> **Not**
> Cache::instance()->set(anahtar, değer) eklenecek anahtar sınırlı sayıda ise bellek sızıntısı olmayacaktır, çünkü `$data` dizisi sonsuz genişlemeyecektir.

#### Sonsuz genişleyen global diziler
```php
class Index
{
    public function index(Request $request)
    {
        global $data;
        $data[] = time();
        return response($foo->sayHello());
    }
}
```
global anahtar kelimesi ile tanımlanan dizi, işlevin veya sınıfın tamamlandıktan sonra geri alınmaz, bu nedenle uzun ömürlü bir dizidir, yukarıdaki kod, istekler arttıkça bellek sızıntısına neden olacaktır. Benzer şekilde, işlev veya yöntem içinde static anahtar kelimesi ile tanımlanan diziler de uzun ömürlü dizilerdir, eğer dizi sonsuz genişlerse bellek sızıntısına neden olabilir, örneğin:
```php
class Index
{
    public function index(Request $request)
    {
        static $data = [];
        $data[] = time();
        return response($foo->sayHello());
    }
}
```

## Öneri
Geliştiricilere bellek sızıntısı konusuna özellikle odaklanmalarını önermiyoruz, çünkü nadiren gerçekleşir ve maalesef gerçekleşirse, sorunun nereden kaynaklandığını belirleyerek, stres testleri kullanarak bu sorunu tespit edebiliriz. Geliştiriciler, sızıntı noktasını bulamazlarsa bile, webman'ın dahili monitör hizmeti, bellek sızıntısı olan süreçleri zamanında güvenli bir şekilde yeniden başlatarak belleği serbest bırakır.

Eğer mümkünse bellek sızıntısını önlemek istiyorsanız, aşağıdaki önerilere göz atabilirsiniz.
1. `global`,`static` anahtar kelimesi kullanımından kaçının, bunları kullanmanız gerekiyorsa bile sonsuz genişlemeyeceğinden emin olun.
2. Bilmediğiniz sınıflar için tekil örnek (singleton) kullanmaktan kaçının, `new` anahtar kelimesiyle başlatın. Tekil (singleton) kullanmanız gerekiyorsa, sonsuz genişleyen bir dizi özelliği olup olmadığını kontrol edin.
