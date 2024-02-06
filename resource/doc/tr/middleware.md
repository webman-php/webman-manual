# Ara yazılım
Ara yazılım genellikle istekleri veya yanıtları engellemek için kullanılır. Örneğin, bir denetleyiciyi çalıştırmadan önce kullanıcı kimliğini genel bir şekilde doğrulamak, kullanıcı oturum açmamışsa oturum açma sayfasına yönlendirmek veya yanıta belirli bir başlık eklemek gibi işlemler için kullanılır. Bir diğer örnek ise belirli bir URI isteğinin oranını hesaplamaktır.

## Ara yazılım soğan modeli

```
                              
            ┌──────────────────────────────────────────────────────┐
            │                     middleware1                      │ 
            │     ┌──────────────────────────────────────────┐     │
            │     │               middleware2                │     │
            │     │     ┌──────────────────────────────┐     │     │
            │     │     │         middleware3          │     │     │        
            │     │     │     ┌──────────────────┐     │     │     │
            │     │     │     │                  │     │     │     │
 　── İstek ───────────────────────> Denetleyici ─ Yanıt ───────────────────────────> İstemci
            │     │     │     │                  │     │     │     │
            │     │     │     └──────────────────┘     │     │     │
            │     │     │                              │     │     │
            │     │     └──────────────────────────────┘     │     │
            │     │                                          │     │
            │     └──────────────────────────────────────────┘     │
            │                                                      │
            └──────────────────────────────────────────────────────┘
```
Ara yazılım ve denetleyici, klasik bir soğan modelinin bir parçasını oluşturur. Ara yazılım, kat kat soğan kabuğu gibi birbirine eklenmiştir, denetleyiciyse soğanın çekirdeğidir. Yukarıdaki şekilde görüldüğü gibi, istek, ara yazılım 1, 2, 3'ten geçerek denetleyiciye ulaşır; denetleyici bir yanıt döndürür ve bu yanıt tekrar 3, 2, 1 sırasıyla ara yazılımlardan geçerek sonunda istemciye döner. Yani her ara yazılımın içinde isteği alabilir ve yanıtı elde edebiliriz.

## İstek engelleme
Bazen belirli bir isteğin denetleyici katmanına ulaşmasını istemeyebiliriz, örneğin, bir kimlik doğrulama ara yazılımında mevcut kullanıcının giriş yapmadığını fark edersek isteği doğrudan engelleyip giriş yanıtı döndürebiliriz. Bu durumda işlem aşağıdaki gibi olabilir:

```
                              
            ┌───────────────────────────────────────────────────────┐
            │                     middleware1                       │ 
            │     ┌───────────────────────────────────────────┐     │
            │     │          　 　 Kimlik Doğrulama Ara Yazılımı   │     │
            │     │      ┌──────────────────────────────┐     │     │
            │     │      │         middleware3          │     │     │       
            │     │      │     ┌──────────────────┐     │     │     │
            │     │      │     │                  │     │     │     │
  　── İstek ───────────┐   │     │       Denetleyici     │     │     │
            │     │ Yanıt │     │                  │     │     │     │
   <─────────────────┘   │     └──────────────────┘     │     │     │
            │     │      │                              │     │     │
            │     │      └──────────────────────────────┘     │     │
            │     │                                           │     │
            │     └───────────────────────────────────────────┘     │
            │                                                       │
            └───────────────────────────────────────────────────────┘
```

Yukarıdaki gibi, istek kimlik doğrulama ara yazılımına ulaştığında giriş yanıtı döndürmüş ve yanıt, kimlik doğrulama ara yazılımından geçerek ara yazılım 1'e ulaşmış ve tarayıcıya geri dönmüştür.

## Ara yazılım arayüzü
Ara yazılımların `Webman\MiddlewareInterface` arayüzünü uygulamaları gerekmektedir.
```php
interface MiddlewareInterface
{
    /**
     * Process an incoming server request.
     *
     * Processes an incoming server request in order to produce a response.
     * If unable to produce the response itself, it may delegate to the provided
     * request handler to do so.
     */
    public function process(Request $request, callable $handler): Response;
}
```
Yani `process` metodunu uygulamaları gerekmektedir. `process` metodunun varsayılan olarak `support\Response` nesnesi döndürmesi gerekmektedir; bu nesne genellikle `$handler($request)` tarafından oluşturulur (istek soğanın çekirdeğine doğru devam eder), ayrıca `response()` `json()` `xml()` `redirect()` gibi yardımcı fonksiyonlar kullanılarak oluşturulabilir (istek soğanın çekirdeğinde durur).

## Ara yazılımda isteği ve yanıtı elde etme
Ara yazılım içinde isteği ve denetleyiciden gelen yanıtı elde edebiliriz, dolayısıyla ara yazılım içinde üç aşama bulunmaktadır.
1. İstek geçişi aşaması, yani talep işleme aşamasından önceki aşama
2. Denetleyicinin isteği işleme aşaması, yani talep işleme aşaması
3. Yanıt çıkışı aşaması, yani işlemden sonra gelen aşama

Ara yazılımdaki üç aşama şu şekilde gösterilir:
```php
<?php
namespace app\middleware;

use Webman\MiddlewareInterface;
use Webman\Http\Response;
use Webman\Http\Request;

class Test implements MiddlewareInterface
{
    public function process(Request $request, callable $handler) : Response
    {
        echo 'Burası istek geçişi aşaması, yani işlem öncesidir.';
        
        $response = $handler($request); // Soğanın çekirdeğine doğru devam eder, denetleyici yanıtını elde edene kadar
        
        echo 'Burası yanıt çıkışı aşaması, yani işlem sonrasıdır.';
        
        return $response;
    }
}
```
## Örnek: Kimlik Doğrulama Ara Katman
`app/middleware/AuthCheckTest.php` adında bir dosya oluşturun (`app` dizini mevcut değilse lütfen kendiniz oluşturun) aşağıdaki gibi:

```php
<?php
namespace app\middleware;

use ReflectionClass;
use Webman\MiddlewareInterface;
use Webman\Http\Response;
use Webman\Http\Request;

class AuthCheckTest implements MiddlewareInterface
{
    public function process(Request $request, callable $handler) : Response
    {
        if (session('user')) {
            // Zaten oturum açıldı, istek devam etmek için soğanın içinden geçmeye devam et
            return $handler($request);
        }

        // Yönlendirici hangi yöntemlerin giriş yapılmasına gerek olmadığını yansıtabilir
        $controller = new ReflectionClass($request->controller);
        $noNeedLogin = $controller->getDefaultProperties()['noNeedLogin'] ?? [];

        // Erişilmek istenen yöntem için giriş yapılması gerekiyor
        if (!in_array($request->action, $noNeedLogin)) {
            // İsteği engelle, yeniden yönlendiren bir yanıt döndür ve isteği soğanın içinden geçirmeyi durdur
            return redirect('/user/login');
        }

        // Giriş yapmaya gerek yok, isteği soğanın içinden geçmeye devam et
        return $handler($request);
    }
}
```

Yeni bir denetleyici oluşturun `app/controller/UserController.php`
```php
<?php
namespace app\controller;
use support\Request;

class UserController
{
    /**
     * Giriş yapılması gerekmeyen yöntemler
     */
    protected $noNeedLogin = ['login'];

    public function login(Request $request)
    {
        $request->session()->set('user', ['id' => 10, 'name' => 'webman']);
        return json(['code' => 0, 'msg' => 'giriş başarılı']);
    }

    public function info()
    {
        return json(['code' => 0, 'msg' => 'başarılı', 'veri' => session('user')]);
    }
}
```

> **Dikkat**
> `$noNeedLogin` içerisinde mevcut denetleyicinin giriş yapılmasına gerek olmadan erişilebilen yöntemleri kayıt edilir

`config/middleware.php` dosyasına aşağıdaki gibi global ara katman ekleyin:
```php
return [
    // Global ara katmanlar
    '' => [
        // ... Diğer ara katmanlar burada kısaltılmıştır
        app\middleware\AuthCheckTest::class,
    ]
];
```

Kimlik doğrulama ara katmanı ile, artık denetleyici katmanında giriş yapılıp yapılmadığıyla ilgilenmeden iş mantığı kodları yazabiliriz.

## Örnek: Cross-Origin Request (CORS) Ara Katmanı
`app/middleware/AccessControlTest.php` adında bir dosya oluşturun (`app` dizini mevcut değilse lütfen kendiniz oluşturun) aşağıdaki gibi:
```php
<?php
namespace app\middleware;

use Webman\MiddlewareInterface;
use Webman\Http\Response;
use Webman\Http\Request;

class AccessControlTest implements MiddlewareInterface
{
    public function process(Request $request, callable $handler) : Response
    {
        // Eğer bir options isteği ise boş bir yanıt döndür, aksi halde soğanın içinden geçmeye devam et ve bir yanıt al
        $response = $request->method() == 'OPTIONS' ? response('') : $handler($request);
        
        // Yanıta CORS'a ilişkin HTTP başlıkları ekle
        $response->withHeaders([
            'Access-Control-Allow-Credentials' => 'true',
            'Access-Control-Allow-Origin' => $request->header('origin', '*'),
            'Access-Control-Allow-Methods' => $request->header('access-control-request-method', '*'),
            'Access-Control-Allow-Headers' => $request->header('access-control-request-headers', '*'),
        ]);
        
        return $response;
    }
}
```

> **Not**
> CORS, OPTIONS isteği oluşturabilir, bu nedenle OPTIONS isteğinin denetleyiciye girmesini istemiyoruz, bu nedenle OPTIONS isteği için doğrudan boş bir yanıt döndürdük (`response('')`).
> Eğer API'nizin rotası ayar gerektiriyorsa, `Route::any(..)` veya `Route::add(['POST', 'OPTIONS'], ..)` kullanmanız gerekmektedir.

`config/middleware.php` dosyasına aşağıdaki gibi global ara katman ekleyin:
```php
return [
    // Global ara katmanlar
    '' => [
        // ... Diğer ara katmanlar burada kısaltılmıştır
        app\middleware\AccessControlTest::class,
    ]
];
```

> **Dikkat**
> Ajax isteği özel başlık eklerse, bu özel başlığı ara katman içinde `Access-Control-Allow-Headers` alanına eklemelisiniz, aksi halde `Request header field XXXX is not allowed by Access-Control-Allow-Headers in preflight response.` hatası alabilirsiniz.

## Açıklama

- Ara katmanlar global, uygulama (sadece çoklu uygulama modunda etkilidir, bkz. [multiapp.md](multiapp.md)) veya rota ara katmanları olarak ayrılır.
- Şu anda tek bir denetleyici için ara katmanı desteği yoktur (ancak benzer şekilde denetleyici ara katmanı işlevselliğini `$request->controller` ile değerlendirerek ara katmanda uygulayabilirsiniz).
- Ara katman yapılandırma dosyası konumu `config/middleware.php` dizinindedir.
- Global ara katman yapılandırması `''` anahtar altında bulunur.
- Uygulama ara katman yapılandırması belirli bir uygulama adı altında bulunur, örneğin

```php
return [
    // Global ara katmanlar
    '' => [
        app\middleware\AuthCheckTest::class,
        app\middleware\AccessControlTest::class,
    ],
    // api uygulama ara katmanı (sadece çoklu uygulama modunda etkilidir)
    'api' => [
        app\middleware\ApiOnly::class,
    ]
];
```

## Rota Ara Katmanları

Bir veya bir grup rotaya ara katman ekleyebiliriz.
Örneğin `config/route.php` dosyasına aşağıdaki yapılandırmayı ekleyebiliriz:
```php
<?php
use support\Request;
use Webman\Route;

Route::any('/admin', [app\admin\controller\IndexController::class, 'index'])->middleware([
    app\middleware\MiddlewareA::class,
    app\middleware\MiddlewareB::class,
]);

Route::group('/blog', function () {
   Route::any('/create', function () {return response('create');});
   Route::any('/edit', function () {return response('edit');});
   Route::any('/view/{id}', function ($r, $id) {response("view $id");});
})->middleware([
    app\middleware\MiddlewareA::class,
    app\middleware\MiddlewareB::class,
]);
```

## Ara Katman Constructor'a Parametre Aktarma

> **Not**
> Bu özellik için webman-framework >= 1.4.8 gerekir

1.4.8 sürümünden itibaren yapılandırmada ara katmanı veya anonim fonksiyonu doğrudan örnekleyebiliriz, bu da constructor fonksiyonuna parametre aktarılmasını kolaylaştırır.
Örneğin `config/middleware.php` dosyasında aşağıdaki gibi yapılandırmalar yapılabilir:
```
return [
    // Global ara katmanlar
    '' => [
        new app\middleware\AuthCheckTest($param1, $param2, ...),
        function(){
            return new app\middleware\AccessControlTest($param1, $param2, ...);
        },
    ],
    // api uygulama ara katmanı (sadece çoklu uygulama modunda etkilidir)
    'api' => [
        app\middleware\ApiOnly::class,
    ]
];
```

Benzer şekilde rota ara katmanları da constructor fonksiyonuna parametre aktarabilmektedir. Örneğin `config/route.php` dosyasında aşağıdaki şekilde yapılandırmalar yapılabilmektedir:
```
Route::any('/admin', [app\admin\controller\IndexController::class, 'index'])->middleware([
    new app\middleware\MiddlewareA($param1, $param2, ...),
    function(){
        return new app\middleware\MiddlewareB($param1, $param2, ...);
    },
]);
```

## Ara Katman Sıralaması

- Ara katman sıralaması `global ara katmanlar`->`uygulama ara katmanları`->`rota ara katmanları` şeklindedir.
- Birden çok global ara katmanı olduğunda, ara katmanların asıl yapılandırma sırasına göre çalışır (uygulama ara katmanları ve rota ara katmanları da aynı şekilde çalışır).
- 404 isteği hiçbir ara katmanı tetiklemez, bunlar global ara katmanlar da dahil olmak üzere.

## Rota Ara Katmanına Parametre Geçme (rota->setParams)

**Route yapılandırması `config/route.php`**
```php
<?php
use support\Request;
use Webman\Route;

Route::any('/test', [app\controller\IndexController::class, 'index'])->setParams(['some_key' =>'some value']);
```

**Ara Katman (varsayılan olarak global) (global ara katman olarak varsayarsak)**
```php
<?php
namespace app\middleware;

use Webman\MiddlewareInterface;
use Webman\Http\Response;
use Webman\Http\Request;

class Hello implements MiddlewareInterface
{
    public function process(Request $request, callable $handler) : Response
    {
        // Varsayılan rota $request->route null olduğu için, $request->route boş olup olmadığını kontrol etmelisiniz
        if ($route = $request->route) {
            $value = $route->param('some_key');
            var_export($value);
        }
        return $handler($request);
    }
}
```

## Ara Katmandan Denetleyiciye Parametre Geçme

Bazen denetleyicinin ara katmanda oluşan veriyi kullanması gerekebilir, bu durumda denetleyiciye parametre göndermek için `$request` nesnesine özellik ekleyerek yapabiliriz. Örneğin:

**Ara Katman**
```php
<?php
namespace app\middleware;

use Webman\MiddlewareInterface;
use Webman\Http\Response;
use Webman\Http\Request;

class Hello implements MiddlewareInterface
{
    public function process(Request $request, callable $handler) : Response
    {
        $request->data = 'some value';
        return $handler($request);
    }
}
```

**Denetleyici:**
```php
<?php
namespace app\controller;

use support\Request;

class FooController
{
    public function index(Request $request)
    {
        return response($request->data);
    }
}
```

## Middleware, Geçerli İstek Yolu Bilgisini Alma

> **Not**
> webman-framework >= 1.3.2 gereklidir

`$request->route` kullanarak route nesnesini alabilir ve ilgili bilgilere erişmek için ilgili yöntemleri çağırabiliriz.

**Route Konfigürasyonu**
```php
<?php
use support\Request;
use Webman\Route;

Route::any('/user/{uid}', [app\controller\UserController::class, 'view']);
```

**Middleware**
```php
<?php
namespace app\middleware;

use Webman\MiddlewareInterface;
use Webman\Http\Response;
use Webman\Http\Request;

class Hello implements MiddlewareInterface
{
    public function process(Request $request, callable $handler) : Response
    {
        $route = $request->route;
        // İstek herhangi bir rotaya eşleşmiyorsa (varsayılan rota hariç), $request->route null olacaktır
        // Varsayalım ki tarayıcı /user/111 adresine erişiyor, o zaman aşağıdaki bilgileri yazdırmalıdır
        if ($route) {
            var_export($route->getPath());       // /user/{uid}
            var_export($route->getMethods());    // ['GET', 'POST', 'PUT', 'DELETE', 'PATCH', 'HEAD', 'OPTIONS']
            var_export($route->getName());       // user_view
            var_export($route->getMiddleware()); // []
            var_export($route->getCallback());   // ['app\\controller\\UserController', 'view']
            var_export($route->param());         // ['uid'=>111]
            var_export($route->param('uid'));    // 111 
        }
        return $handler($request);
    }
}
```

> **Not**
> `$route->param()` yöntemi için webman-framework >= 1.3.16 gereklidir.


## Middleware, İstisna Alma

> **Not**
> webman-framework >= 1.3.15 gereklidir

İşlem sırasında istisna oluşabilir, middleware içinde `$response->exception()` kullanarak istisnayı alabiliriz.

**Route Konfigürasyonu**
```php
<?php
use support\Request;
use Webman\Route;

Route::any('/user/{uid}', function (Request $request, $uid) {
    throw new \Exception('istisna testi');
});
```

**Middleware:**
```php
<?php
namespace app\middleware;

use Webman\MiddlewareInterface;
use Webman\Http\Response;
use Webman\Http\Request;

class Hello implements MiddlewareInterface
{
    public function process(Request $request, callable $handler) : Response
    {
        $response = $handler($request);
        $exception = $response->exception();
        if ($exception) {
            echo $exception->getMessage();
        }
        return $response;
    }
}
```

## Süper Global Middleware

> **Not**
> Bu özellik webman-framework >= 1.5.16 gerektirir

Ana projenin global middleware'ı sadece ana projeyi etkiler, [uygulama eklentileri](app/app.md) üzerinde herhangi bir etki yaratmaz. Bazen tüm eklentileri etkileyen bir middleware eklemek isteyebiliriz, bu durumda süper global middleware'ı kullanabiliriz.

`config/middleware.php` içinde aşağıdaki gibi yapılandırabiliriz:
```php
return [
    '@' => [ // Ana projeye ve tüm eklentilere global middleware ekleyin
        app\middleware\MiddlewareGlobl::class,
    ], 
    '' => [], // Sadece ana projeye global middleware ekleyin
];
```

> **Not**
> `@` süper global middleware yalnızca ana projede değil, aynı zamanda bir eklenti içinde de yapılandırılabilir, örneğin `plugin/ai/config/middleware.php` içinde `@` süper global middleware yapılandırılırsa, bu durum ana projeyi ve tüm eklentileri etkiler.

## Bir Eklentiye Middleware Ekleme

> **Not**
> Bu özellik webman-framework >= 1.5.16 gerektirir

Bazen bir [uygulama eklentisi](app/app.md) ne middleware eklemek isteyebiliriz, ancak kodunu (güncellemelerle üzerine yazılacağı için) değiştirmek istemeyiz, bu durumda ana projede middleware ekleyebiliriz.

`config/middleware.php` içinde aşağıdaki gibi yapılandırabiliriz:
```php
return [
    'plugin.ai' => [], // ai eklentisine middleware ekle
    'plugin.ai.admin' => [], // ai eklentisinin admin modülüne middleware ekle
];
```

> **Not**
> Tabii ki, böyle bir yapılandırmayı diğer eklentileri etkileyecek şekilde bir eklentiye de ekleyebilirsiniz, örneğin `plugin/foo/config/middleware.php` içine yukarıdaki yapılandırmayı eklerseniz, bu ai eklentisini etkiler.
