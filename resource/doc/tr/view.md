## Görünüm
webman varsayılan olarak PHP'nin doğal sözdizimini şablon olarak kullanır ve `opcache`'yi açtıktan sonra en iyi performansa sahiptir. PHP'nin doğal şablonu dışında, webman ayrıca [Twig](https://twig.symfony.com/doc/3.x/), [Blade](https://learnku.com/docs/laravel/8.x/blade/9377), [think-template](https://www.kancloud.cn/manual/think-template/content) şablon motorlarını da sağlar.

## opcache'yi Açma
Görünüm kullanırken, `php.ini` dosyasında `opcache.enable` ve `opcache.enable_cli` seçeneklerini açmanızı kesinlikle öneririz, böylece şablon motoru en iyi performansa ulaşabilir.

## Twig Kurulumu
1. Composer ile kurulum

    `composer require twig/twig`

2. Yapılandırma dosyasını (`config/view.php`) aşağıdaki gibi güncelleyin
```php
<?php
use support\view\Twig;

return [
    'handler' => Twig::class
];
```
> **Not**
> Diğer yapılandırma seçenekleri, örneğin `options` ile geçirilebilir

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

    `composer require psr/container ^1.1.1 webman/blade`

2. Yapılandırma dosyasını (`config/view.php`) aşağıdaki gibi güncelleyin
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

2. Yapılandırma dosyasını (`config/view.php`) aşağıdaki gibi güncelleyin
```php
<?php
use support\view\ThinkPHP;

return [
    'handler' => ThinkPHP::class,
];
```
> **Not**
> Diğer yapılandırma seçenekleri, örneğin `options` ile geçirilebilir

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

Aşağıdaki gibi `app/view/user/hello.html` dosyasını oluşturun

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
Yapılandırma dosyasını (`config/view.php`) aşağıdaki gibi güncelleyin
```php
<?php
use support\view\Twig;

return [
    'handler' => Twig::class
];
```

`app/controller/UserController.php` aşağıdaki gibi

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

`app/view/user/hello.html` dosyası aşağıdaki gibi

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

Daha fazla belge için [Twig belgeleri](https://twig.symfony.com/doc/3.x/) 'ne bakabilirsiniz.

## Blade Şablon Motoru Örneği
Yapılandırma dosyasını (`config/view.php`) aşağıdaki gibi güncelleyin
```php
<?php
use support\view\Blade;

return [
    'handler' => Blade::class
];
```

`app/controller/UserController.php` aşağıdaki gibi

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

`app/view/user/hello.blade.php` dosyası aşağıdaki gibi

> Not: blade şablonu uzantısı `.blade.php` olarak belirtilmelidir

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

Daha fazla belge için [Blade belgeleri](https://learnku.com/docs/laravel/8.x/blade/9377) 'ne bakabilirsiniz.

## ThinkPHP Şablon Motoru Örneği
Yapılandırma dosyasını (`config/view.php`) aşağıdaki gibi güncelleyin
```php
<?php
use support\view\ThinkPHP;

return [
    'handler' => ThinkPHP::class
];
```

`app/controller/UserController.php` aşağıdaki gibi

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

`app/view/user/hello.html` dosyası aşağıdaki gibi

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

Daha fazla belge için [think-template belgeleri](https://www.kancloud.cn/manual/think-template/content) 'ne bakabilirsiniz.

## Şablon Değer Atama
Şablon değerlerini `view(şablon, değişken_dizisi)` kullanarak atamanın yanı sıra, herhangi bir yerden `View::assign()` çağırarak da şablon değerleri atanabilir. Örneğin:
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

`View::assign()` bazı senaryolarda çok kullanışlıdır, örneğin, bir sistemde her sayfa üstbilgisinde geçerli kullanıcı bilgileri gösterilmelidir. Her sayfa için `view('şablon', ['user_info' => 'kullanıcı bilgisi']);` kullanmak zahmetli olacaktır. Çözüm, middleware'de kullanıcı bilgilerini almak ve `View::assign()` kullanarak kullanıcı bilgilerini şablona atamaktır.

## Görünüm Dosyası Yolu Hakkında

#### Kontrolcü
Kontrolcü `view('template_name',[]);` çağırdığında, görünüm dosyaları aşağıdaki kurallara göre bulunur:

1. Çoklu uygulama olmadığında, `app/view/` altındaki ilgili görünüm dosyası kullanılır
2. [Çoklu uygulama](multiapp.md) olduğunda, ilgili görünüm dosyası `app/app_name/view/` altında kullanılır

Özetle, eğer `$request->app` boşsa, `app/view/` altındaki görünüm dosyası kullanılır, aksi halde `app/{$request->app}/view/` altındaki görünüm dosyası kullanılır.

#### Kapanış Fonksiyonu
Kapanış fonksiyonu `$request->app` boş olduğundan herhangi bir uygulamaya ait değil, bu nedenle kapanış fonksiyonu, `app/view/` altındaki görünüm dosyasını kullanır. Örneğin `config/route.php` dosyasında bir yol tanımladığınızda
```php
Route::any('/admin/user/get', function (Reqeust $reqeust) {
    return view('user', []);
});
```
`app/view/user.html` dosyası şablon dosyası olarak kullanılır (Blade şablonu kullanılıyorsa şablon dosyası `app/view/user.blade.php` olmalıdır).

#### Uygulama Belirtme
Çoklu uygulama modunda şablonların yeniden kullanılabilmesi için `view($template, $data, $app = null)` üçüncü parametre olan `$app` kullanarak hangi uygulama dizininin şablonunu kullanılacağını belirtebilirsiniz. Örneğin `view('user', [], 'admin');` ifadesi `app/admin/view/` altındaki şablon dosyasını zorlar.

## Twig Genişletme

> **Not**
> Bu özellik webman-framework>=1.4.8 gerektirir

Twig görünüm örneğini genişletmek için yapılandırmaya `view.extension` geri çağrım fonksiyonunu ekleyebiliriz. Örneğin, `config/view.php` dosyası aşağıdaki gibi olabilir
```php
<?php
use support\view\Twig;
return [
    'handler' => Twig::class,
    'extension' => function (Twig\Environment $twig) {
        $twig->addExtension(new your\namespace\YourExtension()); // Eklenti eklemek
        $twig->addFilter(new Twig\TwigFilter('rot13', 'str_rot13')); // Filtre eklemek
        $twig->addFunction(new Twig\TwigFunction('function_name', function () {})); // Fonksiyon eklemek
    }
];
```

## Blade Genişletme
> **Not**
> Bu özellik webman-framework>=1.4.8 gerektirir

Benzer şekilde, `view.extension` geri çağrım fonksiyonunu kullanarak Blade görünüm örneğini genişletebiliriz, örneğin `config/view.php` dosyası aşağıdaki gibi olabilir

```php
<?php
use support\view\Blade;
return [
    'handler' => Blade::class,
    'extension' => function (Jenssegers\Blade\Blade $blade) {
        // Blade'e yönerge eklemek
        $blade->directive('mydate', function ($timestamp) {
            return "<?php echo date('Y-m-d H:i:s', $timestamp); ?>";
        });
    }
];
```

## Blade Component (Bileşen) Kullanımı

> **Not
> webman/blade>=1.5.2 gerektirir.**

Örneğin bir Alert bileşeni eklemek istiyoruz

**`app/view/components/Alert.php` dosyasını oluşturun**
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

**`app/view/components/alert.blade.php` dosyasını oluşturun**
```
<div>
    <b style="color: red">hello blade component</b>
</div>
```

**`/config/view.php` dosyası aşağıdaki gibi olabilir**

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

Bu şekilde, Blade bileşeni Alert ayarlaması tamamlanmış olur, şablonlarda kullanırken aşağıdaki gibi olacak
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

## think-template Genişletme
think-template, `view.options.taglib_pre_load` kullanılarak etiket kütüphanesini genişletebilir, örneğin
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

Ayrıntılar için [think-template etiket genişletme](https://www.kancloud.cn/manual/think-template/1286424) 'ne bakabilirsiniz.
