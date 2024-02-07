# Denetleyiciler

Yeni bir denetleyici dosyası oluşturun: `app/controller/FooController.php`.

```php
<?php
namespace app\controller;

use support\Request;

class FooController
{
    public function index(Request $request)
    {
        return response('merhaba index');
    }
    
    public function hello(Request $request)
    {
        return response('merhaba webman');
    }
}
```

`http://127.0.0.1:8787/foo` adresine erişildiğinde, sayfa `merhaba index` döndürecektir.

`http://127.0.0.1:8787/foo/hello` adresine erişildiğinde, sayfa `merhaba webman` döndürecektir.

Tabii ki, rota kurallarını değiştirmek için rota yapılandırmasını kullanabilirsiniz, bkz.[Rota](route.md).

> **İpucu**
> Eğer 404 hatası alırsanız, `config/app.php` dosyasını açın ve `controller_suffix` değerini `Controller` olarak ayarlayın, ardından sunucuyu yeniden başlatın.

## Denetleyici Sonekleri
webman 1.3 sürümünden itibaren, `config/app.php` dosyasında denetleyici soneği ayarlamayı desteklemektedir. Eğer `config/app.php` dosyasında `controller_suffix` boş olarak ayarlanırsa, denetleyici sınıfı aşağıdaki gibi olacaktır.

`app\controller\Foo.php`.

```php
<?php
namespace app\controller;

use support\Request;

class Foo
{
    public function index(Request $request)
    {
        return response('merhaba index');
    }
    
    public function hello(Request $request)
    {
        return response('merhaba webman');
    }
}
```

Denetleyici soneğini `Controller` olarak ayarlamak şiddetle tavsiye edilir, bu şekilde denetleyici ve model sınıf adları arasındaki çakışmayı önleyebilir ve güvenliği artırabilirsiniz.

## Açıklama
- Çerçeve otomatik olarak `support\Request` nesnesini denetleyiciye iletir; bu nesne aracılığıyla kullanıcı giriş verilerini (get, post, başlık, çerez vb.) alabilirsiniz, bkz.[İstek](request.md).
- Denetleyiciler, sayı, dize veya `support\Response` nesnesi döndürebilir, ancak diğer türde veri döndüremezler.
- `support\Response` nesnesi, `response()`, `json()`, `xml()`, `jsonp()`, `redirect()` vb. yardımcı fonksiyonlar aracılığıyla oluşturulabilir.

## Denetleyici Hayat Döngüsü
`config/app.php` dosyasındaki `controller_reuse` değeri `false` olarak ayarlandığında, her istek için ilgili denetleyici örneği yeniden başlatılır, istek sona erdikten sonra denetleyici örneği imha edilir; bu, geleneksel çerçeve çalışma mekanizmasıyla aynıdır.

`config/app.php` dosyasındaki `controller_reuse` değeri `true` olarak ayarlandığında, tüm istekler denetleyici örneğini yeniden kullanır, yani bir kez oluşturulduğunda bu denetleyici örneği sürekli hafızada kalır ve tüm isteklerde yeniden kullanılır.

> **Dikkat**
> Denetleyici yeniden kullanımının devre dışı bırakılması webman>=1.4.0 sürümünü gerektirir; yani 1.4.0'dan önce tüm istekler otomatik olarak denetleyiciyi yeniden kullanır ve bu ayar değiştirilemez.

> **Dikkat**
> Denetleyici yeniden kullanımı etkinleştirildiğinde, isteklerin denetleyicinin herhangi bir özelliğini değiştirmesi gerekmektedir, çünkü bu değişiklikler sonraki istekleri etkileyecektir.

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
        return response('tamam');
    }
    
    public function delete(Request $request, $id)
    {
        $model = $this->getModel($id);
        $model->delete();
        return response('tamam');
    }
    
    protected function getModel($id)
    {
        // Bu yöntem, ilk kez update?id=1 isteğinden sonra modeli saklar
        // Eğer tekrar delete?id=2 isteği yapılırsa, 1'in verilerini silecektir
        if (!$this->model) {
            $this->model = Model::find($id);
        }
        return $this->model;
    }
}
```

> **İpucu**
> Denetleyici `__construct()` yapısından veri döndürmek herhangi bir etki yaratmayacaktır, örneğin;

```php
<?php
namespace app\controller;

use support\Request;

class FooController
{
    public function __construct()
    {
        // Oluşturucu fonksiyonundan dönen verinin herhangi bir etkisi olmayacak, tarayıcı bu yanıtı almaz
        return response('merhaba'); 
    }
}
```

## Denetleyici Yeniden Kullanımı vs. Yeniden Kullanmama
Farklar şunlardır:

#### Denetleyici Yeniden Kullanma
Her istek, yeni bir denetleyici örneği oluşturacak ve istek sona erdikten sonra bu örneği serbest bırakacak ve belleği geri kazanacaktır. Denetleyici yeniden kullanmama ve geleneksel çerçeveyle aynıdır, çoğu geliştirici alışkanlığına uygundur. Denetleyicinin sürekli olarak oluşturulup imha edilmesi nedeniyle performans, yeniden kullanılan denetleyiciden biraz düşüktür (helloworld performans testinde yaklaşık%10 performans düşer, işleme benzer şekilde ihmal edilebilir).

#### Denetleyici Yeniden Kullanmama
Yeniden kullanma durumunda, bir işlem sadece bir kez bir denetleyici oluşturur ve istek sona erdikten sonra bu denetleyici örneğini serbest bırakmaz, ardışık isteklerde bu örnekleri yeniden kullanır. Yeniden kullanılan denetleyicinin performansı daha iyidir, ancak çoğu geliştirici alışkanlığına uygundan farklıdır.

#### Aşağıdaki durumlarda, Denetleyici Yeniden Kullanmayı Kullanamazsınız

İstekler, denetleyicinin özelliklerini değiştirecekse, denetleyici yeniden kullanılamaz, çünkü bu özelliklerin değişimi sonraki istekleri etkileyecektir.

Bazı geliştiriciler, her istek için başlangıçta bir şeyler yapmak için denetleyici oluşturucu fonksiyonu `__construct()` kullanırlar, bu durumda denetleyici yeniden kullanılamaz, çünkü bu özelliğin yapıcı fonksiyonu yalnızca bir kez çağrılır, her istek için çağrılmaz.
