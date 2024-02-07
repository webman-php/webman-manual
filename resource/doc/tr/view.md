## Görünüm
webman, varsayılan olarak şablon olarak php'nin doğal sözdizimini kullanır ve `opcache` etkinleştirildiğinde en iyi performansa sahiptir. Php'nin doğal şablonu dışında, webman ayrıca [Twig](https://twig.symfony.com/doc/3.x/), [Blade](https://learnku.com/docs/laravel/8.x/blade/9377), [think-template](https://www.kancloud.cn/manual/think-template/content) şablon motorlarını da sunar.

## Opcache'yi Açma
Görünüm kullanılırken, php.ini'de `opcache.enable` ve `opcache.enable_cli` seçeneklerinin etkinleştirilmesi şiddetle önerilir, böylece şablon motoru en iyi performansa ulaşabilir.

## Twig Kurulumu
1. Composer ile kurulum

   `composer require twig/twig`

2. `config/view.php` yapılandırmasını aşağıdaki gibi değiştirin
   ```php
   <?php
   use support\view\Twig;

   return [
       'handler' => Twig::class
   ];
   ```
   > **Not**
   > Diğer yapılandırma seçenekleri `options` ile iletilir, örneğin  

   ```php
   return [
       'handler' => Twig::class,
       'options' => [
           'debug' => false,
           'charset' => 'utf-8'
       ]
   ];
   ```

## Blade Kurulumu
1. Composer ile kurulum

   ```bash
   composer require psr/container ^1.1.1 webman/blade
   ```

2. `config/view.php` yapılandırmasını aşağıdaki gibi değiştirin
   ```php
   <?php
   use support\view\Blade;

   return [
       'handler' => Blade::class
   ];
   ```

## think-template Kurulumu
1. Composer ile kurulum

   `composer require topthink/think-template`

2. `config/view.php` yapılandırmasını aşağıdaki gibi değiştirin
   ```php
   <?php
   use support\view\ThinkPHP;

   return [
       'handler' => ThinkPHP::class,
   ];
   ```
   > **Not**
   > Diğer yapılandırma seçenekleri `options` ile iletilir, örneğin

   ```php
   return [
       'handler' => ThinkPHP::class,
       'options' => [
           'view_suffix' => 'html',
           'tpl_begin' => '{',
           'tpl_end' => '}'
       ]
   ];
   ```

## Doğal PHP Şablon Motoru Örneği
Aşağıdaki gibi `app/controller/UserController.php` dosyasını oluşturun

```php
<?php
namespace app\controller;

use support\Request;

class UserController
{
    public function hello(Request $request)
    {
        return view('user/hello', ['name' => 'webman']);
    }
}
```

Aşağıdaki gibi yeni dosya `app/view/user/hello.html` oluşturun

```html
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>webman</title>
</head>
<body>
hello <?=htmlspecialchars($name)?>
</body>
</html>
```

## Twig Şablon Motoru Örneği

`config/view.php` yapılandırmasını aşağıdaki gibi değiştirin
```php
<?php
use support\view\Twig;

return [
    'handler' => Twig::class
];
```

`app/controller/UserController.php` dosyası için aşağıdaki gibi

```php
<?php
namespace app\controller;

use support\Request;

class UserController
{
    public function hello(Request $request)
    {
        return view('user/hello', ['name' => 'webman']);
    }
}
```

Dosya `app/view/user/hello.html` için aşağıdaki gibi

```html
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>webman</title>
</head>
<body>
hello {{name}}
</body>
</html>
```

Daha fazla dokümantasyon için [Twig](https://twig.symfony.com/doc/3.x/) adresine bakınız

## Blade Şablon Motoru Örneği
`config/view.php` yapılandırmasını aşağıdaki gibi değiştirin
```php
<?php
use support\view\Blade;

return [
    'handler' => Blade::class
];
```

`app/controller/UserController.php` dosyası için aşağıdaki gibi

```php
<?php
namespace app\controller;

use support\Request;

class UserController
{
    public function hello(Request $request)
    {
        return view('user/hello', ['name' => 'webman']);
    }
}
```

Dosya `app/view/user/hello.blade.php` için aşağıdaki gibi

> Not: Blade şablonunun uzantısı `.blade.php` olarak belirtilmelidir

```html
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>webman</title>
</head>
<body>
hello {{$name}}
</body>
</html>
```

Daha fazla dokümantasyon için [Blade](https://learnku.com/docs/laravel/8.x/blade/9377) adresine bakınız

## ThinkPHP Şablon Motoru Örneği
`config/view.php` yapılandırmasını aşağıdaki gibi değiştirin
```php
<?php
use support\view\ThinkPHP;

return [
    'handler' => ThinkPHP::class
];
```

`app/controller/UserController.php` dosyası için aşağıdaki gibi

```php
<?php
namespace app\controller;

use support\Request;

class UserController
{
    public function hello(Request $request)
    {
        return view('user/hello', ['name' => 'webman']);
    }
}
```

Dosya `app/view/user/hello.html` için aşağıdaki gibi


```html
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>webman</title>
</head>
<body>
hello {$name}
</body>
</html>
```

Daha fazla dokümantasyon için [think-template](https://www.kancloud.cn/manual/think-template/content) adresine bakınız

## Şablon Değeri Atama
Şablon değeri atamak için `view(şablon, değişken_dizisi)` kullanmanın yanı sıra, herhangi bir yerde `View::assign()` çağırarak şablon değeri atayabiliriz. Örneğin:
```php
<?php
namespace app\controller;

use support\Request;
use support\View;

class UserController
{
    public function hello(Request $request)
    {
        View::assign([
            'name1' => 'değer1',
            'name2'=> 'değer2',
        ]);
        View::assign('name3', 'değer3');
        return view('user/test', ['name' => 'webman']);
    }
}
```

`View::assign()` bazı senaryolarda oldukça kullanışlıdır, örneğin bir sistemde her sayfanın üst kısmında giriş yapan kullanıcının bilgilerinin gösterilmesi gerekiyorsa, her sayfada `view('şablon', ['user_info' => 'kullanıcı bilgisi']);` ile bu bilgiyi atamak oldukça zor olacaktır. Bu sorunun çözümü, middleware'de kullanıcı bilgisini almak ve sonra `View::assign()` aracılığıyla kullanıcı bilgisini şablona atamaktır.
## Görünüm Dosyası Yolu Hakkında

#### Denetleyici
Denetleyici `view('templateName',[]);` çağrıldığında, görünüm dosyası aşağıdaki kurallara göre bulunur:

1. Birden fazla uygulama olmadığında, ilgili görünüm dosyası `app/view/` altında bulunur
2. [Birden fazla uygulama](multiapp.md) durumunda, ilgili görünüm dosyası `app/applicationName/view/` altında bulunur

Özetle, eğer `$request->app` boşsa, `app/view/` altındaki görünüm dosyaları kullanılır, aksi takdirde `app/{$request->app}/view/` altındaki görünüm dosyaları kullanılır.

#### Kapanış Fonksiyonu
Kapanış fonksiyonu `$request->app` boş olduğundan, herhangi bir uygulamaya ait değildir, bu yüzden kapanış fonksiyonu `app/view/` altındaki görünüm dosyalarını kullanır, örneğin `config/route.php` dosyasında tanımlı rotaya aşağıdaki gibi tanımlanmışsa
```php
Route::any('/admin/user/get', function (Reqeust $reqeust) {
    return view('user', []);
});
```
şablon dosyası olarak `app/view/user.html` kullanılır (Blade şablonu kullanıldığında, şablon dosyası `app/view/user.blade.php` olur).

#### Belirli Uygulama
Çoklu uygulama modunda şablonların yeniden kullanılabilmesi için view($template, $data, $app=null) üçüncü parametre olan `$app` ile hangi uygulama dizinine ait şablonun kullanılacağı belirtilebilir. Örneğin, `view('user', [], 'admin');` komutu `app/admin/view/` altındaki görünüm dosyasının zorunlu olarak kullanılmasını sağlar.

## Twig Genişletme

> **Not**
> Bu özellik webman-framework>=1.4.8 gerektirir

Twig görünüm örneğini genişletmek için `view.extension` geri çağırımını ayarlayarak yapabiliriz, örneğin `config/view.php` aşağıdaki gibi olabilir:
```php
<?php
use support\view\Twig;
return [
    'handler' => Twig::class,
    'extension' => function (Twig\Environment $twig) {
        $twig->addExtension(new your\namespace\YourExtension()); // Genişletme ekle
        $twig->addFilter(new Twig\TwigFilter('rot13', 'str_rot13')); // Filtre ekle
        $twig->addFunction(new Twig\TwigFunction('function_name', function () {})); // Fonksiyon ekle
    }
];
```


## Blade Genişletme
> **Not**
> Bu özellik webman-framework>=1.4.8 gerektirir
Blade görünüm örneğini genişletmek için `view.extension` geri çağırımını ayarlayarak yapabiliriz, örneğin `config/view.php` aşağıdaki gibi olabilir

```php
<?php
use support\view\Blade;
return [
    'handler' => Blade::class,
    'extension' => function (Jenssegers\Blade\Blade $blade) {
        // Blade'e direktif ekle
        $blade->directive('mydate', function ($timestamp) {
            return "<?php echo date('Y-m-d H:i:s', $timestamp); ?>";
        });
    }
];
```


## Blade Komponenti Kullanımı

> **Not**
> webman/blade>=1.5.2 gerektirir

Örneğin bir Alert bileşeni eklememiz gerekiyorsa

**Yeni bir `app/view/components/Alert.php` dosyası oluşturun:**
```php
<?php

namespace app\view\components;

use Illuminate\View\Component;

class Alert extends Component
{
    
    public function __construct()
    {
    
    }
    
    public function render()
    {
        return view('components/alert')->rawBody();
    }
}
```

**Yeni bir `app/view/components/alert.blade.php` dosyası oluşturun:**
```php
<div>
    <b style="color: red">hello blade component</b>
</div>
```

**`/config/view.php` dosyası benzer şekilde aşağıdaki gibi olabilir:**
```php
<?php
use support\view\Blade;
return [
    'handler' => Blade::class,
    'extension' => function (Jenssegers\Blade\Blade $blade) {
        $blade->component('alert', app\view\components\Alert::class);
    }
];
```

Bu şekilde, Blade bileşeni Alert ayarlanmış olur, şablon kullanımı aşağıdaki gibi olur
```html
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>webman</title>
</head>
<body>

<x-alert/>

</body>
</html>
```


## Think-template Genişletme
think-template, `view.options.taglib_pre_load` kullanarak etiket kütüphanesini genişletebilir, örneğin
```php
<?php
use support\view\ThinkPHP;
return [
    'handler' => ThinkPHP::class,
    'options' => [
        'taglib_pre_load' => your\namspace\Taglib::class,
    ]
];
```

Detaylar için [think-template Tag Genişletme](https://www.kancloud.cn/manual/think-template/1286424) sayfasına bakabilirsiniz.
