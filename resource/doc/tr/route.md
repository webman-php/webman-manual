## Yönlendirme
## Varsayılan Yönlendirme Kuralları
webman'in varsayılan yönlendirme kuralı `http://127.0.0.1:8787/{controller}/{action}` şeklindedir.

Varsayılan denetleyici `app\controller\IndexController`, varsayılan eylem ise `index` şeklindedir.

Örneğin ziyaret:
- `http://127.0.0.1:8787` varsayılan olarak `app\controller\IndexController` sınıfının `index` yöntemine erişecektir.
- `http://127.0.0.1:8787/foo` varsayılan olarak `app\controller\FooController` sınıfının `index` yöntemine erişecektir.
- `http://127.0.0.1:8787/foo/test` varsayılan olarak `app\controller\FooController` sınıfının `test` yöntemine erişecektir.
- `http://127.0.0.1:8787/admin/foo/test` varsayılan olarak `app\admin\controller\FooController` sınıfının `test` yöntemine erişecektir (bkz. [Çoklu Uygulama](multiapp.md)).

Ayrıca webman 1.4'ten itibaren daha karmaşık varsayılan yönlendirmeyi desteklemektedir, örneğin
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

Bir isteğin yönlendirme yolunu değiştirmek istediğinizde `config/route.php` yapılandırma dosyasını değiştirmeniz gerekmektedir.

Varsayılan yönlendirmeyi kapatmak isterseniz, `config/route.php` dosyasının son satırına aşağıdaki yapılandırmayı ekleyiniz:
```php
Route::disableDefaultRoute();
```

## Kapatıcı Yönlendirme
`config/route.php` dosyasına aşağıdaki yönlendirme kodunu ekleyiniz
```php
Route::any('/test', function ($request) {
    return response('test');
});

```
> **Not**
> Kapatıcı işlev hiçbir denetleyiciye ait olmadığı için, `$request->app` `$request->controller` `$request->action` tümü boş dize olarak döner.

`http://127.0.0.1:8787/test` adresine erişildiğinde `test` dizesi dönecektir.

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


## Sınıf Yönlendirme
`config/route.php` dosyasına aşağıdaki yönlendirme kodunu ekleyiniz
```php
Route::any('/testclass', [app\controller\IndexController::class, 'test']);
```
`http://127.0.0.1:8787/testclass` adresine erişildiğinde `app\controller\IndexController` sınıfının `test` yönteminin dönüş değeri dönecektir.

## Yönlendirme Parametreleri
Eğer yönlendirmede parametre varsa, eşleşen sonuçlar karşılık gelen denetleyici metodu parametrelerine (ikinci parametreden başlayarak sırayla) iletilir, örneğin:
```php
// /user/123 /user/abc eşleşir
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

// Tüm options isteklerini eşleştir
Route::options('[{path:.+}]', function () {
    return response('');
});
```

## Yönlendirme Grupları
Bazı durumlarda yönlendirme, aynı önekleri içeren çok sayıda rotayı kapsıyorsa, yönlendirme grupları kullanarak tanımı kolaylaştırabiliriz. Örneğin:

```php
Route::group('/blog', function () {
   Route::any('/create', function ($request) {return response('create');});
   Route::any('/edit', function ($request) {return response('edit');});
   Route::any('/view/{id}', function ($request, $id) {return response("view $id");});
});
```
Şununla aynıdır
```php
Route::any('/blog/create', function ($request) {return response('create');});
Route::any('/blog/edit', function ($request) {return response('edit');});
Route::any('/blog/view/{id}', function ($request, $id) {return response("view $id");});
```

Grup iç içe kullanım

```php
Route::group('/blog', function () {
   Route::group('/v1', function () {
      Route::any('/create', function ($request) {return response('create');});
      Route::any('/edit', function ($request) {return response('edit');});
      Route::any('/view/{id}', function ($request, $id) {return response("view $id");});
   });  
});
```

## Yönlendirme Aracıları
Bir veya bir grup rotaya araçlar ekleyebiliriz.
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

> **Not**: 
> webman-framework <= 1.5.6  sürümünde `->middleware()` yönlendirme aracı gruplanmasını gerçekleştirmek için, mevcut rotaların bu grup içinde olması gerekmektedir.

```php
# Yanlış Kullanım Örneği (webman-framework >= 1.5.7 sürümünde bu kullanım geçerlidir)
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

## Kaynak Yönlendirme
```php
Route::resource('/test', app\controller\IndexController::class);

//Kaynak rotayı belirtmek
Route::resource('/test', app\controller\IndexController::class, ['index','create']);

//Belirtilmemiş kaynak rotası
//Örneğin notify erişim adresi için her iki türlü de kullanılabilir /test/notify veya /test/notify/{id} routeName test.notify
Route::resource('/test', app\controller\IndexController::class, ['index','create','notify']);
```
| Fiil   | URI                 | Eylem   | Yönlendirme Adı    |
|--------|---------------------|----------|---------------|
| GET    | /test               | index    | test.index    |
| GET    | /test/create        | create   | test.create   |
| POST   | /test               | store    | test.store    |
| GET    | /test/{id}          | show     | test.show     |
| GET    | /test/{id}/edit     | edit     | test.edit     |
| PUT    | /test/{id}          | update   | test.update   |
| DELETE | /test/{id}          | destroy  | test.destroy  |
| PUT    | /test/{id}/recovery | recovery | test.recovery |




## URL oluşturma
> **Not** 
> Şu anda yerleşik gruplandırma yapılmış yönlendirmeler için URL oluşturmayı desteklememektedir  

Örneğin yönlendirme:
```php
Route::any('/blog/{id}', [app\controller\BlogController::class, 'view'])->name('blog.view');
```
Bu rotanın URL'sini oluşturmak için aşağıdaki yöntemi kullanabiliriz.
```php
route('blog.view', ['id' => 100]); // Sonuç olarak /blog/100
```

Görünüm dosyalarında yönlendirmenin URL'sini oluştururken bu yöntemi kullanarak, yönlendirme adresinin değişmesi durumunda birçok görünüm dosyasını değiştirmekten kaçınarak otomatik olarak URL oluşturabiliriz.


## Yönlendirme Bilgisini Almak
> **Not**
> webman-framework >= 1.3.2 gerekmektedir

`$request->route` nesnesi vasıtasıyla mevcut isteğin yönlendirme bilgilerini alabiliriz. Örneğin

```php
$route = $request->route; // $route = request()->route; ile aynıdır.
if ($route) {
    var_export($route->getPath());
    var_export($route->getMethods());
    var_export($route->getName());
    var_export($route->getMiddleware());
    var_export($route->getCallback());
    var_export($route->param()); // Bu özellik webman-framework >= 1.3.16 sürümü gerektirir
}
```

> **Not**
> Eğer mevcut istek, `config/route.php` yapılandırma dosyasında tanımlanmış herhangi bir yönlendirme ile eşleşmiyorsa, o zaman `$request->route` null olacaktır, yani varsayılan yönlendirmede `$request->route` null olacaktır.


## 404 İşleme
Yönledirme bulunamadığında varsayılan olarak 404 durum kodunu döndürür ve `public/404.html` dosyasının içeriğini gösterir.

Geliştiriciler yönlendirme bulunamadığında iş akışına müdahale etmek istiyorlarsa, webman'in sağladığı geriye dönüş yönlendirmesi `Route::fallback($callback)` yöntemini kullanabilir. Örneğin, aşağıdaki kod mantığı, yönlendirme bulunamadığında anasayfaya yönlendirmektir.
```php
Route::fallback(function(){
    return redirect('/');
});
```
Örneğin, yönlendirme bulunamadığında bir json verisi dönmek istiyorlarsa, bu webman'in API arayüzü olarak kullanıldığında oldukça kullanışlıdır.
```php
Route::fallback(function(){
    return json(['code' => 404, 'msg' => '404 not found']);
});
```

İlgili bağlantı [Özel 404 500 sayfaları](others/custom-error-page.md)
## Rotalama Arayüzü
```php
// $uri'nin herhangi bir yöntem isteğine karşılık gelen rotayı ayarlayın
Route::any($uri, $callback);
// $uri'nin get isteğine karşılık gelen rotayı ayarlayın
Route::get($uri, $callback);
// $uri'nin isteğine karşılık gelen rotayı ayarlayın
Route::post($uri, $callback);
// $uri'nin put isteğine karşılık gelen rotayı ayarlayın
Route::put($uri, $callback);
// $uri'nin patch isteğine karşılık gelen rotayı ayarlayın
Route::patch($uri, $callback);
// $uri'nin delete isteğine karşılık gelen rotayı ayarlayın
Route::delete($uri, $callback);
// $uri'nin head isteğine karşılık gelen rotayı ayarlayın
Route::head($uri, $callback);
// Birden fazla istek türünü aynı anda ayarlayın
Route::add(['GET', 'POST', 'PUT', 'DELETE', 'PATCH', 'HEAD', 'OPTIONS'], $uri, $callback);
// Grup rotası
Route::group($path, $callback);
// Kaynak rotası
Route::resource($path, $callback, [$options]);
// Rotalamayı devre dışı bırakın
Route::disableDefaultRoute($plugin = '');
// Yedekleme rotası, varsayılan rotayı ayarlayın
Route::fallback($callback, $plugin = '');
```
Eğer uri için herhangi bir rota (varsayılan rota dahil) ayarlanmamışsa ve yedekleme rotası da ayarlanmamışsa, 404 hatası döndürülür.

## Birden Fazla Rota Yapılandırma Dosyası
Rotaları yönetmek için birden fazla rota yapılandırma dosyası kullanmak istiyorsanız, örneğin [çoklu uygulama](multiapp.md) durumunda her uygulama kendi rota yapısına sahip olduğunda, dışarıdan rota yapılandırma dosyalarını `require` kullanarak yükleyebilirsiniz.
Örneğin `config/route.php` içerisinde
```php
<?php

// admin uygulamasının rota yapılandırmasını yükleyin
require_once app_path('admin/config/route.php');
// api uygulamasının rota yapılandırmasını yükleyin
require_once app_path('api/config/route.php');
```
