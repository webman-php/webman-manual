## Yönlendirme
## Varsayılan Yönlendirme Kuralları
webman'in varsayılan yönlendirme kuralı `http://127.0.0.1:8787/{kontrolcü}/{aksiyon}` şeklindedir.

Varsayılan kontrolcü olarak `app\controller\IndexController` ve varsayılan aksiyon olarak `index` kullanılır.

Örneğin:
- `http://127.0.0.1:8787` adresine gidildiğinde varsayılan olarak `app\controller\IndexController` sınıfının `index` metodu çağrılır.
- `http://127.0.0.1:8787/foo` adresine gidildiğinde varsayılan olarak `app\controller\FooController` sınıfının `index` metodu çağrılır.
- `http://127.0.0.1:8787/foo/test` adresine gidildiğinde varsayılan olarak `app\controller\FooController` sınıfının `test` metodu çağrılır.
- `http://127.0.0.1:8787/admin/foo/test` adresine gidildiğinde varsayılan olarak `app\admin\controller\FooController` sınıfının `test` metodu çağrılır (bkz. [Çoklu Uygulama](multiapp.md)).

Ayrıca webman 1.4 sürümünden itibaren daha karmaşık varsayılan yönlendirme desteği sunmaktadır:
```php
app
├── admin
│   └── v1
│       └── v2
│           └── v3
│               └── controller
│                   └── IndexController.php
└── controller
    ├── v1
    │   └── IndexController.php
    └── v2
        └── v3
            └── IndexController.php
```

Bir isteğin yönlendirme yolunu değiştirmek istediğinizde `config/route.php` konfigürasyon dosyasını düzenlemelisiniz.

Varsayılan yönlendirmeyi kapatmak istiyorsanız, `config/route.php` dosyasının en alt satırına şu konfigürasyonu ekleyin:
```php
Route::disableDefaultRoute();
```

## Kapatma Yönlendirme
`config/route.php` dosyasına aşağıdaki yönlendirme kodunu ekleyin:
```php
use support\Request;
Route::any('/test', function (Request $request) {
    return response('test');
});

```
> **Not**
> Kapatma fonksiyonu herhangi bir kontrolcüye ait olmadığı için, `$request->app` `$request->controller` `$request->action` tümü boş dize olacaktır.

`http://127.0.0.1:8787/test` adresine gidildiğinde, `test` dizesi döndürülecektir.

> **Not**
> Yönlendirme yolu `/` ile başlamalıdır, örneğin

```php
use support\Request;
// Yanlış kullanım
Route::any('test', function (Request $request) {
    return response('test');
});

// Doğru kullanım
Route::any('/test', function (Request $request) {
    return response('test');
});
```


## Sınıf Yönlendirmesi
`config/route.php` dosyasına aşağıdaki yönlendirme kodunu ekleyin:
```php
Route::any('/testclass', [app\controller\IndexController::class, 'test']);
```
`http://127.0.0.1:8787/testclass` adresine gidildiğinde, `app\controller\IndexController` sınıfının `test` metodunun dönüş değeri alınacaktır.


## Notasyonla Yönlendirme

Denetleyici metotlarındaki notasyonlarla rotaları tanımlayın, `config/route.php` içinde yapılandırma gerekmez.

> **Not**
> Bu özellik webman-framework >= v2.2.0 gerektirir

### Temel kullanım

```php
namespace app\controller;
use support\annotation\route\Get;
use support\annotation\route\Post;

class UserController
{
    #[Get('/user/{id}')]
    public function show($id)
    {
        return "user $id";
    }

    #[Post('/user')]
    public function store()
    {
        return 'created';
    }
}
```

Kullanılabilir notasyonlar: `#[Get]` `#[Post]` `#[Put]` `#[Delete]` `#[Patch]` `#[Head]` `#[Options]` `#[Any]` (herhangi bir yöntem). Yol `/` ile başlamalıdır. İkinci parametre rota adını belirtebilir, `route()` ile URL oluşturmak için kullanılır.

### Parametresiz notasyonlar: varsayılan rota HTTP yöntemini kısıtlama

Yol olmadan yalnızca bu eyleme izin verilen HTTP yöntemlerini kısıtlar, varsayılan rota yolunu kullanmaya devam eder:

```php
#[Post]
public function create() { ... }  // Yalnızca POST izinli, yol hâlâ /user/create

#[Get]
public function index() { ... }   // Yalnızca GET izinli
```

Birden fazla notasyon birleştirilerek birden fazla istek yöntemine izin verilebilir:

```php
#[Get]
#[Post]
public function form() { ... }  // GET ve POST izinli
```

Notasyonlarda bildirilmeyen istek yöntemleri 405 döndürür.

Yol içeren birden fazla notasyon bağımsız rotalar olarak kaydedilir: `#[Get('/a')] #[Post('/b')]` GET /a ve POST /b iki rota oluşturur.

### Rota grubu öneki

Sınıfta `#[RouteGroup]` kullanarak tüm metot rotalarına önek ekleyin:

```php
use support\annotation\route\RouteGroup;
use support\annotation\route\Get;

#[RouteGroup('/api/v1')]
class UserController
{
    #[Get('/user/{id}')]  // Gerçek yol /api/v1/user/{id}
    public function show($id) { ... }
}
```

### Özel HTTP yöntemleri ve rota adı

```php
use support\annotation\route\Route;

#[Route('/user', ['GET', 'POST'], 'user.form')]
public function form() { ... }
```

### Ara katman

Denetleyicide veya metotta `#[Middleware]` notasyon rotalarına uygulanır, `support\annotation\Middleware` ile aynı şekilde kullanılır.

## Yönlendirme Parametreleri
Eğer yönlendirme içinde parametre varsa, eşleşen sonuçlar ilgili kontrolcü metodlarına parametre olarak iletilir (ikinci parametreden başlayarak sırayla iletilir), örneğin:
```php
// /user/123 veya /user/abc eşleşir
Route::any('/user/{id}', [app\controller\UserController::class, 'get']);
```
```php
namespace app\controller;
use support\Request;

class UserController
{
    public function get(Request $request, $id)
    {
        return response('Parametre alındı: '.$id);
    }
}
```
Daha fazla örnek:
```php
use support\Request;
// /user/123 eşleşir, /user/abc eşleşmez
Route::any('/user/{id:\d+}', function (Request $request, $id) {
    return response($id);
});

// /user/foobar eşleşir, /user/foo/bar eşleşmez
Route::any('/user/{name}', function (Request $request, $name) {
   return response($name);
});

// /user /user/123 ve /user/abc eşleşir   [] isteğe bağlı
Route::any('/user[/{name}]', function (Request $request, $name = null) {
   return response($name ?? 'tom');
});

// /user/ öneki olan tüm isteklerle eşleşir
Route::any('/user/[{path:.+}]', function (Request $request) {
    return $request->path();
});

// Tüm options istekleri eşleşir   : adlandırılmış parametre için regex belirtir
Route::options('[{path:.+}]', function () {
    return response('');
});
```

Gelişmiş kullanım özeti

> Webman rotalarında `[]` sözdizimi ağırlıklı olarak isteğe bağlı yol bölümlerini veya dinamik rota eşleştirmesini işlemek için kullanılır; daha karmaşık yol yapıları ve eşleştirme kuralları tanımlamanızı sağlar
>
> `:` düzenli ifade belirtmek için kullanılır

## Yönlendirme Grupları
Bazı durumlarda yönlendirme büyük miktarda aynı ön ek içerir, bu durumda yönlendirme grupları kullanarak tanımlamayı basitleştirebiliriz. Örneğin:

```php
use support\Request;
Route::group('/blog', function () {
   Route::any('/create', function (Request $request) {return response('create');});
   Route::any('/edit', function (Request $request) {return response('edit');});
   Route::any('/view/{id}', function (Request $request, $id) {return response("view $id");});
});
```
Aşağıdaki tanımlamaya eşittir:
```php
Route::any('/blog/create', function (Request $request) {return response('create');});
Route::any('/blog/edit', function (Request $request) {return response('edit');});
Route::any('/blog/view/{id}', function (Request $request, $id) {return response("view $id");});
```

Grup içi grup kullanımı

```php
Route::group('/blog', function () {
   Route::group('/v1', function () {
      Route::any('/create', function (Request $request) {return response('create');});
      Route::any('/edit', function (Request $request) {return response('edit');});
      Route::any('/view/{id}', function (Request $request, $id) {return response("view $id");});
   });  
});
```
## Rotalama Ara Katman

Belirli bir rotayı veya rotalar grubunu bir ara katmanla donatabiliriz.
Örneğin:
```php
Route::any('/admin', [app\admin\controller\IndexController::class, 'index'])->middleware([
    app\middleware\MiddlewareA::class,
    app\middleware\MiddlewareB::class,
]);

Route::group('/blog', function () {
   Route::any('/create', function () {return response('create');});
   Route::any('/edit', function () {return response('edit');});
   Route::any('/view/{id}', function ($request, $id) {return response("view $id");});
})->middleware([
    app\middleware\MiddlewareA::class,
    app\middleware\MiddlewareB::class,
]);
```

```php
# Yanlış Kullanım Örneği (webman-framework >= 1.5.7 sürümünden itibaren bu kullanım geçerlidir)
Route::group('/blog', function () {
   Route::group('/v1', function () {
      Route::any('/create', function (Request $request) {return response('create');});
      Route::any('/edit', function (Request $request) {return response('edit');});
      Route::any('/view/{id}', function (Request $request, $id) {return response("view $id");});
   });  
})->middleware([
    app\middleware\MiddlewareA::class,
    app\middleware\MiddlewareB::class,
]);
```

```php
# Doğru Kullanım Örneği
Route::group('/blog', function () {
   Route::group('/v1', function () {
      Route::any('/create', function (Request $request) {return response('create');});
      Route::any('/edit', function (Request $request) {return response('edit');});
      Route::any('/view/{id}', function (Request $request, $id) {return response("view $id");});
   })->middleware([
        app\middleware\MiddlewareA::class,
        app\middleware\MiddlewareB::class,
    ]);  
});
```

## Kaynak Tipi Rotalar
```php
Route::resource('/test', app\controller\IndexController::class);

// Özel kaynak rotaları
Route::resource('/test', app\controller\IndexController::class, ['index','create']);

// Tanımlanmayan kaynak rotalar
// notify adresine erişildiğinde her ikisi de /test/notify veya /test/notify/{id} routeName olarak test.notify olur
Route::resource('/test', app\controller\IndexController::class, ['index','create','notify']);
```

| Fiil   | URI                 | Eylem   | Rota Adı       |
|--------|---------------------|----------|---------------|
| GET    | /test               | index    | test.index    |
| GET    | /test/create        | create   | test.create   |
| POST   | /test               | store    | test.store    |
| GET    | /test/{id}          | show     | test.show     |
| GET    | /test/{id}/edit     | edit     | test.edit     |
| PUT    | /test/{id}          | update   | test.update   |
| DELETE | /test/{id}          | destroy  | test.destroy  |
| PUT    | /test/{id}/recovery | recovery | test.recovery |


## URL Oluşturma
> **Not** 
> Geçici olarak iç içe geçmiş rotaların URL'sini oluşturmayı desteklememektedir.

Örneğin rotası:
```php
Route::any('/blog/{id}', [app\controller\BlogController::class, 'view'])->name('blog.view');
```
Bu rotanın URL'sini aşağıdaki yöntemle oluşturabiliriz.
```php
route('blog.view', ['id' => 100]); // Sonuç /blog/100 olur
```

Görünümde rota URL'sini kullanırken bu yöntemi kullanarak, rota kuralları nasıl değişirse değişsin, URL otomatik olarak oluşturulur ve rota adresi değişikliğinden kaynaklanan çok sayıda görünüm dosyasını değiştirmekten kaçınılır.

## Rota Bilgilerini Almak

`$request->route` nesnesi aracılığıyla mevcut istek rotası bilgilerini alabiliriz, örneğin

```php
$route = $request->route; // $route = request()->route; ile eşdeğerdir
if ($route) {
    var_export($route->getPath());
    var_export($route->getMethods());
    var_export($route->getName());
    var_export($route->getMiddleware());
    var_export($route->getCallback());
    var_export($route->param());
}
```

> **Not**
> Eğer mevcut istek config/route.php dosyasında yapılandırılan herhangi bir rotayla eşleşmiyorsa, `$request->route` null olacaktır, yani varsayılan rotaya giderken, `$request->route` null olacaktır.

## 404 Durumu İşleme
Rota bulunamadığında varsayılan olarak 404 durum kodu döner ve ilgili 404 içeriği görüntülenir.

Geliştiriciler, rota bulunamadığında iş sürecine müdahale etmek için webman'in sağladığı geriye düşük rota `Route::fallback($callback)` yöntemini kullanabilirler. Örneğin, aşağıdaki kod mantığı, rota bulunamadığında ana sayfaya yönlendirmeyi içerir.
```php
Route::fallback(function(){
    return redirect('/');
});
```
Bunun yanı sıra, rota bulunamadığında bir json verisi döndürmek, webman'in API arayüzü olarak kullanıldığında oldukça kullanışlıdır.
```php
Route::fallback(function(){
    return json(['code' => 404, 'msg' => '404 not found']);
});
```

## 404'e Ara Katman Ekleme

Varsayılan olarak 404 istekleri herhangi bir ara katmandan geçmez. 404 isteklerine ara katman eklemek gerekiyorsa aşağıdaki kodu inceleyin:
```php
Route::fallback(function(){
    return json(['code' => 404, 'msg' => '404 not found']);
})->middleware([
    app\middleware\MiddlewareA::class,
    app\middleware\MiddlewareB::class,
]);
```

İlgili bağlantı [Özel 404 500 sayfaları](others/custom-error-page.md)

## Varsayılan Rotayı Devre Dışı Bırakma

```php
// Ana projenin varsayılan rotasını devre dışı bırak, eklentileri etkilemez
Route::disableDefaultRoute();
// Ana projenin admin rotasını devre dışı bırak, eklentileri etkilemez
Route::disableDefaultRoute('', 'admin');
// foo eklentisinin varsayılan rotasını devre dışı bırak, ana projeyi etkilemez
Route::disableDefaultRoute('foo');
// foo eklentisinin admin rotasını devre dışı bırak, ana projeyi etkilemez
Route::disableDefaultRoute('foo', 'admin');
// Denetleyici [\app\controller\IndexController::class, 'index'] varsayılan rotasını devre dışı bırak
Route::disableDefaultRoute([\app\controller\IndexController::class, 'index']);
```

## Notasyonla Varsayılan Rotayı Devre Dışı Bırakma

Bir denetleyicinin varsayılan rotasını notasyonlarla devre dışı bırakabiliriz:

```php
namespace app\controller;
use support\annotation\DisableDefaultRoute;

#[DisableDefaultRoute]
class IndexController
{
    public function index()
    {
        return 'index';
    }
}
```

Benzer şekilde, bir denetleyici metotunun varsayılan rotasını da notasyonlarla devre dışı bırakabiliriz:

```php
namespace app\controller;
use support\annotation\DisableDefaultRoute;

class IndexController
{
    #[DisableDefaultRoute]
    public function index()
    {
        return 'index';
    }
}
```

## Rota Arayüzü
```php
// $uri'nin herhangi bir yöntem isteği rota atanıyor
Route::any($uri, $callback);
// $uri'nin get isteği rota atanıyor
Route::get($uri, $callback);
// $uri'nin isteği rota atanıyor
Route::post($uri, $callback);
// $uri'nin put isteği rota atanıyor
Route::put($uri, $callback);
// $uri'nin patch isteği rota atanıyor
Route::patch($uri, $callback);
// $uri'nin delete isteği rota atanıyor
Route::delete($uri, $callback);
// $uri'nin head isteği rota atanıyor
Route::head($uri, $callback);
// $uri'nin options isteği rota atanıyor
Route::options($uri, $callback);
// birden fazla istek türünü aynı anda atayan rota
Route::add(['GET', 'POST', 'PUT', 'DELETE', 'PATCH', 'HEAD', 'OPTIONS'], $uri, $callback);
// grup rota
Route::group($path, $callback);
// kaynak rota
Route::resource($path, $callback, [$options]);
// rota devre dışı bırakma
Route::disableDefaultRoute($plugin = '');
// geriye düşük rota, varsayılan rota koruyucuyu ayarlama
Route::fallback($callback, $plugin = '');
// Tüm rota bilgilerini al
Route::getRoutes();
```
Eğer uri'ye karşılık gelen bir rota yoksa (varsayılan rota dahil), ve geriye düşük rota da ayarlanmamışsa, 404 hatası döner.
## Birden Fazla Rota Yapılandırma Dosyası
Eğer rotaları yönetmek için birden fazla rota yapılandırma dosyası kullanmak istiyorsanız, örneğin [çoklu uygulama] (multiapp.md) durumunda her uygulamanın kendi rota yapılandırmasına sahip olduğu zaman, dışarıdan rota yapılandırma dosyalarını yüklemek için `require` yöntemini kullanabilirsiniz.
Örneğin `config/route.php` içinde
```php
<?php

// admin uygulamasının rota yapılandırmasını yükle
require_once app_path('admin/config/route.php');
// api uygulamasının rota yapılandırmasını yükle
require_once app_path('api/config/route.php');

```
