## Vista
Di default, webman utilizza la sintassi nativa di PHP come modello, ottenendo le migliori prestazioni quando `opcache` è abilitato. Oltre al modello nativo di PHP, webman fornisce anche i motori di template [Twig](https://twig.symfony.com/doc/3.x/), [Blade](https://learnku.com/docs/laravel/8.x/blade/9377), [think-template](https://www.kancloud.cn/manual/think-template/content).

## Abilitare opcache
Quando si utilizza la vista, è vivamente consigliato abilitare le opzioni `opcache.enable` e `opcache.enable_cli` nel file php.ini, per ottenere le migliori prestazioni dai motori di template.

## Installazione di Twig
1. Installazione tramite composer

```bash
composer require twig/twig
```

2. Modificare la configurazione in `config/view.php` come segue
```php
<?php
use support\view\Twig;

return [
    'handler' => Twig::class
];
```
> **Nota**
> Altre opzioni di configurazione possono essere passate tramite l'array `options`, ad esempio
```php
return [
    'handler' => Twig::class,
    'options' => [
        'debug' => false,
        'charset' => 'utf-8'
    ]
];
```

## Installazione di Blade
1. Installazione tramite composer

```bash
composer require psr/container ^1.1.1 webman/blade
```

2. Modificare la configurazione in `config/view.php` come segue
```php
<?php
use support\view\Blade;

return [
    'handler' => Blade::class
];
```

## Installazione di think-template
1. Installazione tramite composer

```bash
composer require topthink/think-template
```

2. Modificare la configurazione in `config/view.php` come segue
```php
<?php
use support\view\ThinkPHP;

return [
    'handler' => ThinkPHP::class,
];
```
> **Nota**
> Altre opzioni di configurazione possono essere passate tramite l'array `options`, ad esempio
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

## Esempio di template PHP nativo
Creare il file `app/controller/UserController.php` come segue

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

Creare il file `app/view/user/hello.html` come segue

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

## Esempio di template Twig
Modificare la configurazione in `config/view.php` come segue
```php
<?php
use support\view\Twig;

return [
    'handler' => Twig::class
];
```

`app/controller/UserController.php` come segue

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

Il file `app/view/user/hello.html` come segue

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

Per ulteriori informazioni vedere [Twig](https://twig.symfony.com/doc/3.x/)

## Esempio di template Blade
Modificare la configurazione in `config/view.php` come segue
```php
<?php
use support\view\Blade;

return [
    'handler' => Blade::class
];
```

`app/controller/UserController.php` come segue

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

Il file `app/view/user/hello.blade.php` come segue

> Nota che il file del template Blade ha estensione `.blade.php`

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

Per ulteriori informazioni vedere [Blade](https://learnku.com/docs/laravel/8.x/blade/9377)

## Esempio di template think-template
Modificare la configurazione in `config/view.php` come segue
```php
<?php
use support\view\ThinkPHP;

return [
    'handler' => ThinkPHP::class
];
```

`app/controller/UserController.php` come segue

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

Il file `app/view/user/hello.html` come segue

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

Per ulteriori informazioni vedere [think-template](https://www.kancloud.cn/manual/think-template/content)

## Assegnazione del template
Oltre all'uso di `view(template, data_array)` per assegnare il template, possiamo anche assegnare il template in qualsiasi punto chiamando `View::assign()`. Ad esempio:
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

`View::assign()` è molto utile in alcuni scenari, ad esempio se ogni pagina di un sistema deve mostrare le informazioni dell'utente loggato, assegnare queste informazioni a ciascuna pagina tramite `view('template', ['user_info' => 'User Info']);` sarebbe molto complicato. La soluzione è ottenere le informazioni dell'utente in un middleware e poi assegnarle al template tramite `View::assign()`.

## Percorso dei file di template

#### Controller
Quando il controller chiama `view('nome_template',[])`, i file di template sono cercati secondo le seguenti regole:
   
1. Se non è attivo il multi-app, vengono utilizzati i file di template corrispondenti nella directory `app/view/`.
   
2. Se è attivo il multi-app, vengono utilizzati i file di template corrispondenti nella directory `app/nome_app/view/`.

In sintesi, se `$request->app` è vuoto, si utilizzano i file di template nella directory `app/view/`, altrimenti si utilizzano i file di template nella directory `app/{$request->app}/view/`.

#### Funzione di chiusura (Closure)
Poiché la funzione di chiusura ha `$request->app` vuoto e non appartiene a nessuna app, utilizza i file di template nella directory `app/view/`. Ad esempio, se si definisce una route nel file `config/route.php` come segue:
```php
Route::any('/admin/user/get', function (Request $request) {
    return view('user', []);
});
```
verrà utilizzato il file di template `app/view/user.html` (se si utilizza il template Blade, il file sarebbe `app/view/user.blade.php`).

#### Specificare l'applicazione
Nel caso di multi-app, per consentire il riutilizzo dei file di template, `view($template, $data, $app = null)` fornisce un terzo parametro `$app` per specificare quale directory dell'applicazione utilizzare. Ad esempio, `view('user', [], 'admin')` utilizzerà i file di template nella directory `app/admin/view/`.

## Estensione di Twig

> **Nota**
> Questa funzionalità richiede webman-framework>=1.4.8

È possibile estendere l'istanza di Twig attraverso la configurazione `view.extension`, ad esempio modificando `config/view.php` come segue
```php
<?php
use support\view\Twig;
return [
    'handler' => Twig::class,
    'extension' => function (Twig\Environment $twig) {
        $twig->addExtension(new your\namespace\YourExtension()); // Aggiunge un'estensione
        $twig->addFilter(new Twig\TwigFilter('rot13', 'str_rot13')); // Aggiunge un filtro
        $twig->addFunction(new Twig\TwigFunction('function_name', function () {})); // Aggiunge una funzione
    }
];
```

## Estensione di Blade

> **Nota**
> Questa funzionalità richiede webman-framework>=1.4.8

Analogamente, è possibile estendere l'istanza di Blade attraverso la configurazione `view.extension`, ad esempio modificando `config/view.php` come segue
```php
<?php
use support\view\Blade;
return [
    'handler' => Blade::class,
    'extension' => function (Jenssegers\Blade\Blade $blade) {
        // Aggiunge direttive a Blade
        $blade->directive('mydate', function ($timestamp) {
            return "<?php echo date('Y-m-d H:i:s', $timestamp); ?>";
        });
    }
];
```

## Utilizzo del componente di Blade

> **Nota**
> Richiede webman/blade>=1.5.2

Supponiamo di voler aggiungere un componente Alert

**Creare `app/view/components/Alert.php`**
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

**Creare `app/view/components/alert.blade.php`**
```
<div>
    <b style="color: red">hello blade component</b>
</div>
```

**`/config/view.php` come segue**
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

Così, il componente Blade Alert viene configurato e, quando utilizzato nel template, appare come segue
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


## Estensione di think-template
think-template utilizza `view.options.taglib_pre_load` per estendere le librerie dei tag, ad esempio
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

Per ulteriori informazioni vedere [think-template estensione dei tag](https://www.kancloud.cn/manual/think-template/1286424)
