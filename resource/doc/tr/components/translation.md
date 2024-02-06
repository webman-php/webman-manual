# Çoklu Dil Desteği

Çoklu dil desteği için [symfony/translation](https://github.com/symfony/translation) bileşeni kullanılmaktadır.

## Kurulum
```
composer require symfony/translation
```

## Dil Paketi Oluşturma
webman, dil paketlerini varsayılan olarak `resource/translations` dizini altına yerleştirir (varsa kendiniz oluşturun), dizini değiştirmek istiyorsanız `config/translation.php` dosyasında ayarlayabilirsiniz.
Her dil, bu dizinde bir alt klasörle eşleşir ve dil tanımları varsayılan olarak `messages.php` içinde bulunur. Örnek yapısı şu şekilde olabilir:
```
resource/
└── translations
    ├── en
    │   └── messages.php
    └── zh_CN
        └── messages.php
```

Tüm dil dosyaları, aşağıdaki gibi bir dizi döndürür:
```php
// resource/translations/en/messages.php

return [
    'hello' => 'Merhaba webman',
];
```

## Yapılandırma

`config/translation.php`

```php
return [
    // Varsayılan dil
    'locale' => 'zh_CN',
    // Geriye dönüş dili, mevcut dilde çeviri bulunamadığında geriye dönüş dilindeki çeviriyi denemek için kullanılır
    'fallback_locale' => ['zh_CN', 'en'],
    // Dil dosyalarının bulunduğu klasör
    'path' => base_path() . '/resource/translations',
];
```

## Çeviri

Çeviri `trans()` yöntemi kullanılarak yapılır.

Aşağıdaki gibi dil dosyası oluşturun: `resource/translations/zh_CN/messages.php`
```php
return [
    'hello' => 'Merhaba Dünya!',
];
```

`app/controller/UserController.php` dosyasını oluşturun:
```php
<?php
namespace app\controller;

use support\Request;

class UserController
{
    public function get(Request $request)
    {
        $hello = trans('hello'); // Merhaba Dünya!
        return response($hello);
    }
}
```

`http://127.0.0.1:8787/user/get` adresine gidildiğinde "Merhaba Dünya!" dönecektir.

## Varsayılan Dili Değiştirme

Dil değiştirmek için `locale()` yöntemi kullanılır.

Yeni bir dil dosyası oluşturun: `resource/translations/en/messages.php`:
```php
return [
    'hello' => 'hello world!',
];
```

```php
<?php
namespace app\controller;

use support\Request;

class UserController
{
    public function get(Request $request)
    {
        // Dili değiştir
        locale('en');
        $hello = trans('hello'); // hello world!
        return response($hello);
    }
}
```
`http://127.0.0.1:8787/user/get` adresine gidildiğinde "hello world!" dönecektir.

`trans()` işlevinin dördüncü argümanını kullanarak dil geçici olarak değiştirmek de mümkündür. Yukarıdaki örnek ve aşağıdaki örnek eşdeğerdir:
```php
<?php
namespace app\controller;

use support\Request;

class UserController
{
    public function get(Request $request)
    {
        // Dördüncü argümanı kullanarak dil değiştirme
        $hello = trans('hello', [], null, 'en'); // hello world!
        return response($hello);
    }
}
```

## Her İstek İçin Ayrı Dilde Dil Belirtme
translation bir örnek üzerinde çalıştığı için, tüm istekler bu örneği paylaşır. Dolayısıyla, bir istek `locale()` ile varsayılan dili ayarlarsa, bu örneğin sonraki tüm isteklerini etkiler. Bu yüzden her istek için ayrı bir dil belirtmeliyiz. Örneğin aşağıdaki gibi bir ara katman kullanabiliriz:

`app/middleware/Lang.php` dosyasını oluşturun (yoksa kendiniz oluşturun):
```php
<?php
namespace app\middleware;

use Webman\MiddlewareInterface;
use Webman\Http\Response;
use Webman\Http\Request;

class Lang implements MiddlewareInterface
{
    public function process(Request $request, callable $handler) : Response
    {
        locale(session('lang', 'zh_CN'));
        return $handler($request);
    }
}
```

`config/middleware.php` dosyasına aşağıdaki gibi genel bir ara katman ekleyin:
```php
return [
    // Genel ara katmanlar
    '' => [
        // ... Diğer ara katmanları burada atlayın
        app\middleware\Lang::class,
    ]
];
```

## Yer Tutucu Kullanımı
Bazı durumlarda, çevrilmek istenen değişkenleri içeren bir mesaj vardır, örneğin 
```php
trans('hello ' . $name);
```
Bu durumla karşılaşıldığında yer tutucu kullanılır.

`resource/translations/zh_CN/messages.php` dosyasını aşağıdaki gibi değiştirin:
```php
return [
    'hello' => 'Merhaba %name%!',
```
Çeviri yapılırken, veriler yer tutucuyla ilişkili değerleri ikinci parametre aracılığıyla aktarılır
```php
trans('hello', ['%name%' => 'webman']); // Merhaba webman!
```

## Çoğul Durumu İfadesi
Bazı dillerde, nesne miktarına bağlı olarak farklı ifadeler kullanılır, örneğin `There is %count% apple`. `%count%` 1'den fazla olduğunda cümle yanlış olacaktır.

Bu tür durumla karşılaşıldığında çoğul ifadeleri belirtmek için **boru**(`|`) işareti kullanılır.

Dil dosyasına `resource/translations/en/messages.php` dosyasına `apple_count` ekleyin:
```php
return [
    // ...
    'apple_count' => 'There is one apple|There are %count% apples',
];
```

```php
trans('apple_count', ['%count%' => 10]); // There are 10 apples
```

Ayrıca, sayısal aralığını belirterek daha karmaşık çoğul kuralları oluşturabiliriz:
```php
return [
    // ...
    'apple_count' => '{0} There are no apples|{1} There is one apple|]1,19] There are %count% apples|[20,Inf[ There are many apples'
];
```

```php
trans('apple_count', ['%count%' => 20]); // There are many apples
```

## Belirli Bir Dil Dosyasını Kullanma
Dil dosyasının varsayılan adı `messages.php` olmasına rağmen, aslında farklı adlarda dil dosyaları oluşturabilirsiniz.

`resource/translations/zh_CN/admin.php` dosyasını aşağıdaki gibi oluşturun:
```php
return [
    'hello_admin' => 'Merhaba Yönetici!',
```

`trans()` yönteminin üçüncü parametresi ile dil dosyasını belirtebilirsiniz (`.php` uzantısını atlayın).
```php
trans('hello', [], 'admin', 'zh_CN'); // Merhaba Yönetici!
```

## Daha Fazla Bilgi
[Symfony/translation belgeleri](https://symfony.com/doc/current/translation.html)ne bakın
