## Ansichten
Webman verwendet standardmäßig die native PHP-Syntax als Vorlage und erreicht nach dem Öffnen von `opcache` die beste Leistung. Neben der nativen PHP-Vorlage bietet Webman auch Vorlagen-Engines wie [Twig](https://twig.symfony.com/doc/3.x/), [Blade](https://learnku.com/docs/laravel/8.x/blade/9377) und [think-template](https://www.kancloud.cn/manual/think-template/content).

## Opcache aktivieren
Beim Verwenden von Ansichten wird dringend empfohlen, die Optionen `opcache.enable` und `opcache.enable_cli` in der `php.ini` zu aktivieren, um die optimale Leistung der Vorlagen-Engine zu erreichen.

## Twig installieren
1. Composer installieren

   `composer require twig/twig`

2. Konfigurationsänderung in `config/view.php`:

```php
<?php
use support\view\Twig;

return [
    'handler' => Twig::class
];
```

> **Hinweis**
> Andere Konfigurationsoptionen werden über `options` übergeben, zum Beispiel:

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
1. Composer installieren

   `composer require psr/container ^1.1.1 webman/blade`

2. Konfigurationsänderung in `config/view.php`:

```php
<?php
use support\view\Blade;

return [
    'handler' => Blade::class
];
```

## Think-Template installieren
1. Composer installieren

   `composer require topthink/think-template`

2. Konfigurationsänderung in `config/view.php`:

```php
<?php
use support\view\ThinkPHP;

return [
    'handler' => ThinkPHP::class,
];
```

> **Hinweis**
> Andere Konfigurationsoptionen werden über `options` übergeben, zum Beispiel:

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
Erstellen Sie die Datei `app/controller/UserController.php` wie folgt:

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

Erstellen Sie die Datei `app/view/user/hello.html` wie folgt:

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

## Beispiel für Twig-Vorlagen-Engine
Ändern Sie die Konfiguration in `config/view.php` wie folgt:

```php
<?php
use support\view\Twig;

return [
    'handler' => Twig::class
];
```

`app/controller/UserController.php` sieht folgendermaßen aus:

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

Die Datei `app/view/user/hello.html` sieht folgendermaßen aus:

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

Weitere Informationen finden Sie in der [Twig-Dokumentation](https://twig.symfony.com/doc/3.x/).

## Beispiel für Blade-Vorlagen-Engine
Ändern Sie die Konfiguration in `config/view.php` wie folgt:

```php
<?php
use support\view\Blade;

return [
    'handler' => Blade::class
];
```

`app/controller/UserController.php` sieht folgendermaßen aus:

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

Die Datei `app/view/user/hello.blade.php` sieht folgendermaßen aus:

> Beachten Sie, dass die Blade-Dateierweiterung `.blade.php` ist.

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

Weitere Informationen finden Sie in der [Blade-Dokumentation](https://learnku.com/docs/laravel/8.x/blade/9377).

## Beispiel für ThinkPHP-Vorlagen-Engine
Ändern Sie die Konfiguration in `config/view.php` wie folgt:

```php
<?php
use support\view\ThinkPHP;

return [
    'handler' => ThinkPHP::class
];
```

`app/controller/UserController.php` sieht folgendermaßen aus:

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

Die Datei `app/view/user/hello.html` sieht folgendermaßen aus:

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

Weitere Informationen finden Sie in der [ThinkPHP-Dokumentation](https://www.kancloud.cn/manual/think-template/content).

## Vorlagenzuweisung
Neben der Verwendung von `view(template, variable_array)` zur Zuweisung von Vorlagen können wir auch jederzeit mithilfe von `View::assign()` Vorlagen zuweisen. Zum Beispiel:

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
            'name2'=> 'value2',
        ]);
        View::assign('name3', 'value3');
        return view('user/test', ['name' => 'webman']);
    }
}
```

`View::assign()` ist in einigen Szenarien sehr nützlich, zum Beispiel wenn auf jeder Seite eines Systems Informationen zum angemeldeten Benutzer angezeigt werden müssen. Es wäre umständlich, diese Informationen auf jeder Seite mit `view('template', ['user_info' => 'user info'])` zuzuweisen. Die Lösung besteht darin, die Benutzerinformationen im Middleware zu erhalten und sie mit `View::assign()` der Vorlage zuzuweisen.

## Über die Pfade der Vorlagendateien

#### Controller
Wenn ein Controller `view('template_name', [])` aufruft, werden die Vorlagendateien gemäß den folgenden Regeln gesucht:

1. Wenn es sich nicht um eine Mehrfachanwendung handelt, werden die entsprechenden Vorlagendateien unter `app/view/` verwendet.
2. Im Falle einer [Mehrfachanwendung](multiapp.md) werden die entsprechenden Vorlagendateien unter `app/application_name/view/` verwendet.

Zusammenfassend lässt sich sagen, dass, wenn `$request->app` leer ist, die Vorlagendateien unter `app/view/` verwendet werden. Andernfalls werden die Vorlagendateien unter `app/{$request->app}/view/` verwendet.

#### Closure-Funktion
Die Closure-Funktion mit `$request->app` leer gehört keiner Anwendung an. Daher verwendet sie die Vorlagendateien unter `app/view/`, z.B. wenn Routen in der Datei `config/route.php` definiert sind:

```php
Route::any('/admin/user/get', function (Request $request) {
    return view('user', []);
});
```

Es werden die Vorlagendateien unter `app/view/user.html` (bei Verwendung von Blade ist die Vorlagendatei `app/view/user.blade.php`) verwendet.

#### Anwendung spezifizieren
Um Vorlagen in einer Mehrfachanwendung wiederverwenden zu können, bietet `view($template, $data, $app = null)` einen dritten Parameter `$app`, um anzugeben, welche Anwendungsverzeichnisse für Vorlagen verwendet werden sollen. Zum Beispiel wird `view('user', [], 'admin')` dazu führen, dass die Vorlagendateien unter `app/admin/view/` verwendet werden.

## Twig erweitern

> **Hinweis**
> Diese Funktion erfordert webman-framework>=1.4.8

Wir können die Twig-Ansichtsinstanz erweitern, indem wir der Konfiguration `view.extension` eine Rückruffunktion hinzufügen. Zum Beispiel `config/view.php`:

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

## Blade erweitern
> **Hinweis**
> Diese Funktion erfordert webman-framework>=1.4.8
Ebenso können wir die Blade-Ansichtsinstanz erweitern, indem wir der Konfiguration `view.extension` eine Rückruffunktion hinzufügen. Zum Beispiel `config/view.php`:

```php
<?php
use support\view\Blade;
return [
    'handler' => Blade::class,
    'extension' => function (Jenssegers\Blade\Blade $blade) {
        // Anweisungen zu Blade hinzufügen
        $blade->directive('mydate', function ($timestamp) {
            return "<?php echo date('Y-m-d H:i:s', $timestamp); ?>";
        });
    }
];
```
## Verwendung von Blade-Komponenten

> **Hinweis
> Erfordert webman/blade>=1.5.2**

Angenommen, wir müssen ein `Alert`-Komponente hinzufügen

**Neue Datei `app/view/components/Alert.php`**
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

**Neue Datei `app/view/components/alert.blade.php`**
```php
<div>
    <b style="color: red">Hallo Blade-Komponente</b>
</div>
```

**`/config/view.php` ähnlich wie der folgende Code**

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

Damit ist die Blade-Komponente "Alert" eingerichtet, und die Verwendung im Template sieht ähnlich wie folgt aus
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

## Erweiterung von think-template
think-template verwendet `view.options.taglib_pre_load`, um Tag-Bibliotheken zu erweitern, zum Beispiel
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

Weitere Details finden Sie unter [think-template标签扩展](https://www.kancloud.cn/manual/think-template/1286424)
