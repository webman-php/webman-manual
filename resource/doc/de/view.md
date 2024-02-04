## Sicht
Standardmäßig verwendet webman die native PHP-Syntax als Vorlage, die nach dem Öffnen von `opcache` die beste Leistung bietet. Neben der nativen PHP-Vorlage bietet webman auch die [Twig](https://twig.symfony.com/doc/3.x/), [Blade](https://learnku.com/docs/laravel/8.x/blade/9377) und [think-template](https://www.kancloud.cn/manual/think-template/content) Template-Engines.

## Opcache aktivieren
Bei der Verwendung von Ansichten wird dringend empfohlen, die Optionen `opcache.enable` und `opcache.enable_cli` in der `php.ini` zu aktivieren, um die Leistung der Template-Engine zu optimieren.

## Twig installieren
1. Composer-Installation

 ```php
composer require twig/twig
 ```

2. Ändern Sie die Konfiguration `config/view.php` wie folgt

```php
<?php
use support\view\Twig;

return [
    'handler' => Twig::class
];
```
> **Hinweis**
> Andere Konfigurationsoptionen werden über die Optionen übergeben, z. B.

```php
return [
    'handler' => Twig::class,
    'options' => [
        'debug' => false,
        'charset' => 'utf-8'
    ]
];
```


## Blade installieren
1. Composer-Installation

```
composer require psr/container ^1.1.1 webman/blade
```

2. Ändern Sie die Konfiguration `config/view.php` wie folgt

```php
<?php
use support\view\Blade;

return [
    'handler' => Blade::class
];
```

## think-template installieren
1. Composer-Installation

`composer require topthink/think-template`

2. Ändern Sie die Konfiguration `config/view.php` wie folgt

```php
<?php
use support\view\ThinkPHP;

return [
    'handler' => ThinkPHP::class,
];
```
> **Hinweis**
> Andere Konfigurationsoptionen werden über die Optionen übergeben, z. B.

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

## Beispiel für native PHP-Vorlagen-Engine
Erstellen Sie die Datei `app/controller/UserController.php` wie folgt

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

Erstellen Sie die Datei `app/view/user/hello.html` wie folgt

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

## Beispiel für die Twig-Template-Engine
Ändern Sie die Konfiguration `config/view.php` wie folgt

```php
<?php
use support\view\Twig;

return [
    'handler' => Twig::class
];
```

`app/controller/UserController.php` wie folgt

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

Die Datei `app/view/user/hello.html` sieht wie folgt aus

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

Weitere Dokumentationen finden Sie unter [Twig](https://twig.symfony.com/doc/3.x/)

## Beispiel für die Blade-Template-Engine
Ändern Sie die Konfiguration `config/view.php` wie folgt

```php
<?php
use support\view\Blade;

return [
    'handler' => Blade::class
];
```

`app/controller/UserController.php` wie folgt

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

Die Datei `app/view/user/hello.blade.php` sieht wie folgt aus

> Bitte beachten Sie, dass die Dateierweiterung für Blade-Vorlagen `.blade.php` lautet

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

Weitere Dokumentationen finden Sie unter [Blade](https://learnku.com/docs/laravel/8.x/blade/9377)

## Beispiel für die ThinkPHP-Template-Engine
Ändern Sie die Konfiguration `config/view.php` wie folgt

```php
<?php
use support\view\ThinkPHP;

return [
    'handler' => ThinkPHP::class
];
```

`app/controller/UserController.php` wie folgt

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

Die Datei `app/view/user/hello.html` sieht wie folgt aus

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

Weitere Dokumentationen finden Sie unter [think-template](https://www.kancloud.cn/manual/think-template/content)

## Template-Zuweisung
Neben der Verwendung von `view(template, variableArray)` zur Zuweisung von Templates können wir überall auch die Methode `View::assign()` aufrufen, um Templates zuzuweisen. Zum Beispiel:

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
            'name1' => 'value1',
            'name2' => 'value2',
        ]);
        View::assign('name3', 'value3');
        return view('user/test', ['name' => 'webman']);
    }
}
```

`View::assign()` ist in einigen Szenarien sehr nützlich, zum Beispiel muss in einem System auf jeder Seite die aktuelle Anmelderinformation angezeigt werden. Es ist sehr umständlich, diese Informationen auf jeder Seite mit `view('template', ['user_info' => 'user_info'])` zuzuweisen. Die Lösung besteht darin, die Benutzerinformationen im Middleware zu erhalten und dann mit `View::assign()` an das Template zuzuweisen.

## Über die Ansichtsdateipfade

#### Controller
Wenn der Controller `view('template_name', [])` aufruft, sucht die Ansichtsdatei nach folgendem Muster:

1. Wenn es sich nicht um eine Multi-App handelt, wird die Ansichtsdatei in `app/view/` gesucht.
2. Bei [Multi-Apps](multiapp.md) wird die Ansichtsdatei in `app/app_name/view/` gesucht.

Zusammengefasst: Wenn `$request->app` leer ist, wird die Ansichtsdatei in `app/view/` gesucht, andernfalls wird die Ansichtsdatei in `app/{$request->app}/view/` gesucht.

#### Anonyme Funktionen
Anonyme Funktionen mit `$request->app` leer gehören zu keiner App und verwenden daher die Ansichtsdatei in `app/view/`, z. B. in der in `config/route.php` definierten Route:
```php
Route::any('/admin/user/get', function (Request $request) {
    return view('user', []);
});
```
Hier wird die Datei `app/view/user.html` als Vorlagendatei verwendet (wenn Blade-Vorlagen verwendet werden, lautet die Vorlagendatei `app/view/user.blade.php`).

#### App spezifizieren
Um Vorlagen im Multi-App-Modus wiederzuverwenden, bietet `view($template, $data, $app = null)` den dritten Parameter `$app`, um anzugeben, welche Anwendungsverzeichnisvorlagen verwendet werden sollen. Beispielsweise wird `view('user', [], 'admin');` gezwungen, die Ansichtsdatei in `app/admin/view/` zu verwenden.

## Erweiterung von Twig

> **Hinweis**
> Diese Funktion erfordert webman-framework>=1.4.8

Wir können durch das Zurückgeben von `view.extension` im Konfigurationsrückruf Twig-View-Instanzen erweitern, beispielsweise `config/view.php` wie folgt:
```php
<?php
use support\view\Twig;
return [
    'handler' => Twig::class,
    'extension' => function (Twig\Environment $twig) {
        $twig->addExtension(new your\namespace\YourExtension()); // Erweiterung hinzufügen
        $twig->addFilter(new Twig\TwigFilter('rot13', 'str_rot13')); // Filter hinzufügen
        $twig->addFunction(new Twig\TwigFunction('function_name', function () {})); // Funktion hinzufügen
    }
];
```


## Blade-Erweiterung
> **Hinweis**
> Diese Funktion erfordert webman-framework>=1.4.8
Ebenso können wir durch das Zurückgeben von `view.extension` im Konfigurationsrückruf Blade-View-Instanzen erweitern, beispielsweise `config/view.php` wie folgt:

```php
<?php
use support\view\Blade;
return [
    'handler' => Blade::class,
    'extension' => function (Jenssegers\Blade\Blade $blade) {
        // Anweisung für Blade hinzufügen
        $blade->directive('mydate', function ($timestamp) {
            return "<?php echo date('Y-m-d H:i:s', $timestamp); ?>";
        });
    }
];
```

## Verwendung von Blade-Komponenten

> **Hinweis
> Erfordert webman/blade>=1.5.2**

Angenommen, es muss ein Alert-Komponenten hinzugefügt werden

**Neue `app/view/components/Alert.php` erstellen**
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

**Neue `app/view/components/alert.blade.php` erstellen**
```
<div>
    <b style="color: red">Hallo Blade-Komponente</b>
</div>
```

**`/config/view.php` ähnelt dem folgenden Code**

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

Damit ist das Blade-Komponenten-Alert abgeschlossen. Im Template wird es wie folgt verwendet:
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


## Erweiterung der PHP-Vorlage von Think
Die Think-Vorlage verwendet `view.options.taglib_pre_load`, um Tag-Bibliotheken zu erweitern, zum Beispiel
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

Weitere Informationen finden Sie unter [think-template](https://www.kancloud.cn/manual/think-template/1286424)

