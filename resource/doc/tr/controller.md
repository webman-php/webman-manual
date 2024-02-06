# Denetleyiciler

Yeni bir denetleyici dosyası oluşturun `app/controller/FooController.php`.

```php
<?php
namespace app\controller;

use support\Request;

class FooController
{
    public function index(Request $request)
    {
        return response('hello index');
    }
    
    public function hello(Request $request)
    {
        return response('hello webman');
    }
}
```

`http://127.0.0.1:8787/foo` adresine erişildiğinde, sayfa `hello index` dönecektir.

`http://127.0.0.1:8787/foo/hello` adresine erişildiğinde, sayfa `hello webman` dönecektir.

Tabii ki, yol eşlemesini değiştirmek için yol eşlemesi yapılandırmasını kullanabilirsiniz, [Yol Eşlemesi](route.md) bölümüne bakınız.

> **İpucu**
> 404 hatası alırsanız, `config/app.php` dosyasını açın ve `controller_suffix` öğesini `Controller` olarak ayarlayın ve yeniden başlatın.

## Denetleyici Soneki
webman sürüm 1.3'ten itibaren, `config/app.php` dosyasında denetleyici son eki ayarını desteklemektedir. Eğer `config/app.php` dosyasında `controller_suffix` öğesini boş `''` olarak ayarlarsanız, denetleyici aşağıdaki gibi olacaktır.

`app\controller\Foo.php`.

```php
<?php
namespace app\controller;

use support\Request;

class Foo
{
    public function index(Request $request)
    {
        return response('hello index');
    }
    
    public function hello(Request $request)
    {
        return response('hello webman');
    }
}
```

Denetleyici sonekini `Controller` olarak ayarlamayı şiddetle tavsiye ederiz. Bu, denetleyici ve model sınıf adları arasındaki çakışmayı önlemenin yanı sıra güvenliği de artırabilir.

## Açıklama
- Framework otomatik olarak `support\Request` nesnesini denetleyiciye geçirir, bu sayede kullanıcı giriş verilerine (get, post, başlık, tanımlayıcı çerez vb.) erişebilirsiniz, bkz. [İstek](request.md)
- Denetleyici, sayı, dize veya `support\Response` nesnesi döndürebilir, ancak diğer türde veri döndüremez.
- `support\Response` nesnesi, `response()`, `json()`, `xml()`, `jsonp()`, `redirect()` vb. yardımcı işlevlerle oluşturulabilir.

## Denetleyici Yaşam Döngüsü

`config/app.php` dosyasında `controller_reuse` öğesi `false` olarak ayarlandığında, her istek karşılanırken ilgili denetleyici örneği her seferinde başlatılır, istek sona erdikten sonra denetleyici örneği yok edilir ve bellekten alınır. Bu, geleneksel framework'lerin çalışma mekanizmasıyla aynıdır.

`config/app.php` dosyasında `controller_reuse` öğesi `true` olarak ayarlandığında, tüm istekler aynı denetleyici örneğini yeniden kullanır, yani bir denetleyici örneği bir kez oluşturulduğunda sürekli bellekte kalır ve tüm istekler bu örneği yeniden kullanır.

> **Dikkat**
> Denetleyici yeniden kullanımını kapatmak için webman sürüm 1.4.0 veya üstüne ihtiyaç vardır, yani 1.4.0'dan önce denetleyiciler varsayılan olarak tüm istekleri yeniden kullanır ve değiştirilemez.

> **Dikkat**
> Denetleyici yeniden kullanımını etkinleştirdiğinizde, isteklerin denetleyicinin herhangi bir özelliğini değiştirmemesi gerekir, çünkü bu tür değişiklikler sonraki istekleri etkiler.

```php
<?php
namespace app\controller;

use support\Request;

class FooController
{
    protected $model;
    
    public function update(Request $request, $id)
    {
        $model = $this->getModel($id);
        $model->update();
        return response('ok');
    }
    
    public function delete(Request $request, $id)
    {
        $model = $this->getModel($id);
        $model->delete();
        return response('ok');
    }
    
    protected function getModel($id)
    {
        // Bu yöntem, update?id=1 isteği yapıldıktan sonra modeli saklar
        // delete?id=2 isteği yapıldığında, 1'in verilerini silecektir
        if (!$this->model) {
            $this->model = Model::find($id);
        }
        return $this->model;
    }
}
```

> **İpucu**
> Yapıcı fonksiyon `__construct()` içinde veri döndürmek herhangi bir etkiye sahip olmaz, örneğin

```php
<?php
namespace app\controller;

use support\Request;

class FooController
{
    public function __construct()
    {
        // Yapıcı fonksiyon içinde veri döndürmek herhangi bir etkiye sahip değildir, tarayıcı bu yanıtı almayacaktır
        return response('hello'); 
    }
}
```

## Denetleyiciyi Yeniden Kullanmamanın ve Yeniden Kullanmanın Farkı
Farklar aşağıdaki gibidir:

#### Denetleyiciyi Yeniden Kullanmama
Her istek için yeni bir denetleyici örneği oluşturulur, istek sona erdikten sonra bu örnek serbest bırakılır ve bellek geri kazanılır. Denetleyiciyi yeniden kullanmama, geleneksel framework'lerin alışkanlıklarına uygun durumdadır. Denetleyicinin tekrar tekrar oluşturulması ve yok edilmesi nedeniyle performansı, yeniden kullanma durumundan biraz daha düşüktür (helloworld yük testinde performans yaklaşık %10 daha düşüktür, işleve dayalı çoğu durumda ihmal edilebilir).

#### Denetleyiciyi Yeniden Kullanma
Eğer yeniden kullanıma izin verilirse, bir işlem sadece bir kez bir denetleyici oluşturur, istek sona erdikten sonra bu denetleyici örneğini serbest bırakmaz, işlemin sonraki istekleri bu örneği yeniden kullanır. Denetleyiciyi yeniden kullanma durumu performans bakımından daha iyidir, ancak çoğu geliştiricinin alışkanlıklarına uygun değildir.

#### Aşağıdaki Durumlarda Denetleyiciyi Yeniden Kullanamazsınız

İstek, denetleyicinin özelliklerini değiştiriyorsa, denetleyici yeniden kullanılamaz, çünkü bu özelliklerin değişikliği sonraki istekleri etkiler.

Bazı geliştiriciler, her istek için denetleyici oluşturmak için yapıcı fonksiyon `__construct()` içinde bazı başlatmalar yapmayı tercih ederler, bu durumda denetleyiciyi yeniden kullanamazsınız, çünkü mevcut işlemde yapılandırıcı fonksiyon yalnızca bir kez çağrılır, her istek için çağrılmaz.
