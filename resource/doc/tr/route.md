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
Route::any('/test', function ($request) {
    return response('test');
});

```
> **Not**
> Kapatma fonksiyonu herhangi bir kontrolcüye ait olmadığı için, `$request->app` `$request->controller` `$request->action` tümü boş dize olacaktır.

`http://127.0.0.1:8787/test` adresine gidildiğinde, `test` dizesi döndürülecektir.

> **Not**
> Yönlendirme yolu `/` ile başlamalıdır, örneğin

```php
// Yanlış kullanım
Route::any('test', function ($request) {
    return response('test');
});

// Doğru kullanım
Route::any('/test', function ($request) {
    return response('test');
});
```


## Sınıf Yönlendirmesi
`config/route.php` dosyasına aşağıdaki yönlendirme kodunu ekleyin:
```php
Route::any('/testclass', [app\controller\IndexController::class, 'test']);
```
`http://127.0.0.1:8787/testclass` adresine gidildiğinde, `app\controller\IndexController` sınıfının `test` metodunun dönüş değeri alınacaktır.


## Yönlendirme Parametreleri
Eğer yönlendirme içinde parametre varsa, eşleşen sonuçlar ilgili kontrolcü metodlarına parametre olarak iletilir (ikinci parametreden başlayarak sırayla iletilir), örneğin:
```php
// /user/123 veya /user/abc eşleşir
Route::any('/user/{id}', [app\controller\UserController::class, 'get']);
```
```php
namespace app\controller;
class UserController
{
    public function get($request, $id)
    {
        return response('Parametre alındı: '.$id);
    }
}
```
Daha fazla örnek:
```php
// /user/123 eşleşir, /user/abc eşleşmez
Route::any('/user/{id:\d+}', function ($request, $id) {
    return response($id);
});

// /user/foobar eşleşir, /user/foo/bar eşleşmez
Route::any('/user/{name}', function ($request, $name) {
   return response($name);
});

// /user, /user/123 ve /user/abc eşleşir
Route::any('/user[/{name}]', function ($request, $name = null) {
   return response($name ?? 'tom');
});

// Tüm options istekleri eşleşir
Route::options('[{path:.+}]', function () {
    return response('');
});
```

## Yönlendirme Grupları
Bazı durumlarda yönlendirme büyük miktarda aynı ön ek içerir, bu durumda yönlendirme grupları kullanarak tanımlamayı basitleştirebiliriz. Örneğin:

```php
Route::group('/blog', function () {
   Route::any('/create', function ($request) {return response('create');});
   Route::any('/edit', function ($request) {return response('edit');});
   Route::any('/view/{id}', function ($request, $id) {return response("view $id");});
});
```
Aşağıdaki tanımlamaya eşittir:
```php
Route::any('/blog/create', function ($request) {return response('create');});
Route::any('/blog/edit', function ($request) {return response('edit');});
Route::any('/blog/view/{id}', function ($request, $id) {return response("view $id");});
```

Grup içi grup kullanımı

```php
Route::group('/blog', function () {
   Route::group('/v1', function () {
      Route::any('/create', function ($request) {return response('create');});
      Route::any('/edit', function ($request) {return response('edit');});
      Route::any('/view/{id}', function ($request, $id) {return response("view $id");});
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
   Route::any('/view/{id}', function ($request, $id) {response("view $id");});
})->middleware([
    app\middleware\MiddlewareA::class,
    app\middleware\MiddlewareB::class,
]);
```

> **Uyarı**: 
> webman-framework <= 1.5.6 sürümünde `->middleware()` yönlendirme ara katmanı gruplarından sonra etkili olduğunda, geçerli yönlendirme mevcut grubun altında olmalıdır.

```php
# Yanlış Kullanım Örneği (webman-framework >= 1.5.7 sürümünden itibaren bu kullanım geçerlidir)
Route::group('/blog', function () {
   Route::group('/v1', function () {
      Route::any('/create', function ($request) {return response('create');});
      Route::any('/edit', function ($request) {return response('edit');});
      Route::any('/view/{id}', function ($request, $id) {return response("view $id");});
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
      Route::any('/create', function ($request) {return response('create');});
      Route::any('/edit', function ($request) {return response('edit');});
      Route::any('/view/{id}', function ($request, $id) {return response("view $id");});
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
> **Not**
> webman-framework >= 1.3.2 gerektirir

`$request->route` nesnesi aracılığıyla mevcut istek rotası bilgilerini alabiliriz, örneğin

```php
$route = $request->route; // $route = request()->route; ile eşdeğerdir
if ($route) {
    var_export($route->getPath());
    var_export($route->getMethods());
    var_export($route->getName());
    var_export($route->getMiddleware());
    var_export($route->getCallback());
    var_export($route->param()); // Bu özellik webman-framework >= 1.3.16 gerektirir
}
```

> **Not**
> Eğer mevcut istek config/route.php dosyasında yapılandırılan herhangi bir rotayla eşleşmiyorsa, `$request->route` null olacaktır, yani varsayılan rotaya giderken, `$request->route` null olacaktır.

## 404 Durumu İşleme
Rota bulunamadığında varsayılan olarak 404 durum kodu döner ve `public/404.html` dosyası içeriğini görüntüler.

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

İlgili bağlantı [Özel 404 500 sayfaları](others/custom-error-page.md)

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
