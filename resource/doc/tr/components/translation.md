# Çoklu Dil Desteği

Çoklu dil desteği için [symfony/translation](https://github.com/symfony/translation) bileşeni kullanılır.

## Kurulum
```composer require symfony/translation```

## Dil Paketi Oluşturma
webman varsayılan olarak dil paketlerini `resource/translations` dizinine yerleştirir (eğer yoksa kendiniz oluşturun), dizini değiştirmek isterseniz `config/translation.php` dosyasında ayarlayabilirsiniz.
Her dil için bir alt dizin oluşturulur ve dil tanımları genellikle `messages.php` dosyasında bulunur. Örnek dizin yapısı şöyle olabilir:
```bash
resource/
└── translations
    ├── en
    │   └── messages.php
    └── zh_CN
        └── messages.php
```

Tüm dil dosyaları genellikle bir dizi döndürülecek şekilde oluşturulur. Örneğin:
```php
// resource/translations/en/messages.php

return [
    'hello' => 'Hello webman',
];
```

## Yapılandırma

`config/translation.php`

```php
return [
    // Varsayılan dil
    'locale' => 'zh_CN',
    // Yedek dil - mevcut dilde çeviri bulunamazsa yedek dildeki çeviriyi deneyecek
    'fallback_locale' => ['zh_CN', 'en'],
    // Dil dosyalarının bulunduğu klasör
    'path' => base_path() . '/resource/translations',
];
```

## Çeviri

Çeviri için `trans()` yöntemi kullanılır.

`resource/translations/zh_CN/messages.php` dosyası aşağıdaki gibi oluşturulur:
```php
return [
    'hello' => 'Merhaba Dünya!',
];
```

`app/controller/UserController.php` dosyası şu şekilde oluşturulur:
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

## Varsayılan Dili Değiştirmek

Dil değişikliği için `locale()` yöntemi kullanılır.

`resource/translations/en/messages.php` dosyasına aşağıdaki gibi ek yapılır:
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
        // Dil değiştir
        locale('en');
        $hello = trans('hello'); // hello world!
        return response($hello);
    }
}
```

`http://127.0.0.1:8787/user/get` adresine gidildiğinde "hello world!" dönecektir.

Ayrıca dil geçici olarak değiştirmek için `trans()` fonksiyonunun dördüncü parametresini kullanabilirsiniz. Örneğin yukarıdaki örnek ile aşağıdaki örnek aynıdır:
```php
<?php
namespace app\controller;

use support\Request;

class UserController
{
    public function get(Request $request)
    {
        // Dil değiştirme için dördüncü parametre
        $hello = trans('hello', [], null, 'en'); // hello world!
        return response($hello);
    }
}
```
## Her İstek İçin Ayrı Ayrı Dil Belirleme
Çeviri bir örnektir ve bu nedenle tüm istekler bu örneği paylaşır. Bu nedenle, yalnızca belirli bir istek için dil belirlemek önemlidir. Bunun için aşağıdaki gibi bir ara yazılım kullanabilirsiniz:

`app/middleware/Lang.php` dosyası oluşturulur (dizin yoksa oluşturun):
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

`config/middleware.php` dosyasına genel bir ara yazılım ekleyin:
```php
return [
    // Global ara yazılımlar
    '' => [
        // ... Diğer ara yazılımlar buraya eklenir
        app\middleware\Lang::class,
    ]
];
```

## Yer Tutucu Kullanımı
Bazı durumlarda, bir çevirilecek olan ifadenin içinde değişkenler bulunabilir. Örneğin:
```php
trans('hello ' . $name);
```
Bu durumla karşılaşıldığında yer tutucular kullanılır.

`resource/translations/zh_CN/messages.php` dosyası aşağıdaki gibi değiştirilir:
```php
return [
    'hello' => 'Merhaba %name%!',
];
```
Çeviri yapılırken ikinci parametre olarak yer tutucuların değerleri aktarılır.
```php
trans('hello', ['%name%' => 'webman']); // Merhaba webman!
```

## Çoğul Durumları Kullanımı
Bazı dillerde miktar durumuna göre cümle yapısı değişebilir, örneğin `There is %count% apple`, `%count%` değeri 1 ise cümlenin yapısı doğrudur, 1'den büyükse yanlıştır.

Bu durumlar için **|** işareti kullanarak çoğul durumları belirtebiliriz.

`resource/translations/en/messages.php` dosyasına `apple_count` örneği aşağıdaki gibi ekleyebiliriz:
```php
return [
    // ...
    'apple_count' => 'There is one apple|There are %count% apples',
];
```

```php
trans('apple_count', ['%count%' => 10]); // There are 10 apples
```

Ayrıca, sayısal aralıkları da belirterek daha karmaşık kurallar oluşturabiliriz:
```php
return [
    // ...
    'apple_count' => '{0} There are no apples|{1} There is one apple|]1,19] There are %count% apples|[20,Inf[ There are many apples'
];
```

```php
trans('apple_count', ['%count%' => 20]); // There are many apples
```

## Belirli Dil Dosyasını Kullanma
Dil dosyalarının varsayılan adı `messages.php`dir, ancak aslında farklı adlarda da oluşturabilirsiniz.

Aşağıdaki gibi `trans()` fonksiyonunun üçüncü parametresi ile dil dosyasını belirleyebilirsiniz (`.php` uzantısını dahil etmeyin).
```php
trans('hello', [], 'admin', 'zh_CN'); // Merhaba Admin!
```

## Daha Fazla Bilgi
[symfony/translation belgeleri](https://symfony.com/doc/current/translation.html) sayfasına bakabilirsiniz.
