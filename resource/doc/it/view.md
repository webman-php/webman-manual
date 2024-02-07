## Vista
webman utilizza di default la sintassi nativa di PHP come modello e, una volta abilitato `opcache`, ottiene le migliori prestazioni. Oltre al modello nativo di PHP, webman fornisce anche i motori di template [Twig](https://twig.symfony.com/doc/3.x/), [Blade](https://learnku.com/docs/laravel/8.x/blade/9377), [think-template](https://www.kancloud.cn/manual/think-template/content).

## Abilitare opcache
Quando si utilizza la visualizzazione, è fortemente consigliato abilitare le opzioni `opcache.enable` e `opcache.enable_cli` nel file php.ini, per consentire al motore del template di ottenere le migliori prestazioni.

## Installare Twig
1. Installazione tramite composer

```
composer require twig/twig
```

2. Modificare la configurazione `config/view.php` come segue
```php
<?php
use support\view\Twig;

return [
    'handler' => Twig::class
];
```
> **Nota**
> Altre opzioni di configurazione possono essere passate tramite l'array `$options`, ad esempio

```php
return [
    'handler' => Twig::class,
    'options' => [
        'debug' => false,
        'charset' => 'utf-8'
    ]
];
```

## Installare Blade
1. Installazione tramite composer

```
composer require psr/container ^1.1.1 webman/blade
```

2. Modificare la configurazione `config/view.php` come segue
```php
<?php
use support\view\Blade;

return [
    'handler' => Blade::class
];
```

## Installare think-template
1. Installazione tramite composer

```
composer require topthink/think-template
```

2. Modificare la configurazione `config/view.php` come segue
```php
<?php
use support\view\ThinkPHP;

return [
    'handler' => ThinkPHP::class,
];
```
> **Nota**
> Altre opzioni di configurazione possono essere passate tramite l'array `$options`, ad esempio

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

## Esempio di motore di template PHP nativo
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

## Esempio di motore di template Twig
Modificare la configurazione `config/view.php` come segue
```php
<?php
use support\view\Twig;

return [
    'handler' => Twig::class
];
```

In `app/controller/UserController.php` come segue

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

Per ulteriori informazioni consultare [Twig](https://twig.symfony.com/doc/3.x/).

## Esempio di motore di template Blade
Modificare la configurazione `config/view.php` come segue
```php
<?php
use support\view\Blade;

return [
    'handler' => Blade::class
];
```

In `app/controller/UserController.php` come segue

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

> Notare che l'estensione del modello Blade è `.blade.php`
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

Per ulteriori informazioni consultare [Blade](https://learnku.com/docs/laravel/8.x/blade/9377).

## Esempio di motore di template ThinkPHP
Modificare la configurazione `config/view.php` come segue
```php
<?php
use support\view\ThinkPHP;

return [
    'handler' => ThinkPHP::class
];
```

In `app/controller/UserController.php` come segue

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

Per ulteriori informazioni consultare [think-template](https://www.kancloud.cn/manual/think-template/content).

## Assegnazione di modelli
Oltre all'uso di `view(modello, array_di_variabili)` per assegnare i modelli, è possibile assegnare i modelli in qualsiasi punto chiamando `View::assign()`. Ad esempio:
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
`View::assign()` è molto utile in alcuni scenari, ad esempio, se un sistema deve mostrare le informazioni dell'utente acceduto in ogni pagina, sarà scomodo assegnare queste informazioni a ogni pagina usando `view('modello', ['user_info' => 'informazioni_utente'])`. La soluzione è ottenere le informazioni dell'utente in un middleware e assegnarle al modello tramite `View::assign()`.

## Percorso dei file del modello
#### Controller
Quando un controller chiama `view('nome_modello', [])`, i file del modello vengono cercati secondo le seguenti regole:

1. Se non è un'applicazione multipla, vengono utilizzati i file del modello sotto `app/view/`
2. In caso di [applicazione multipla](multiapp.md), vengono utilizzati i file del modello sotto `app/nome_app/view/`

In sintesi, se `$request->app` è vuoto, vengono utilizzati i file del modello sotto `app/view/`, altrimenti vengono utilizzati i file del modello sotto `app/{$request->app}/view/`.

#### Funzione di chiusura
In una funzione di chiusura, `$request->app` è vuoto e non appartiene a nessuna applicazione, pertanto vengono utilizzati i file del modello sotto `app/view/`, ad esempio, quando si definiscono le route nel file `config/route.php`
```php
Route::any('/admin/user/get', function (Reqeust $reqeust) {
    return view('user', []);
});
```
verrà utilizzato il file del modello `app/view/user.html` (quando si utilizza il modello Blade, il file del modello diventa `app/view/user.blade.php`).

#### Specificare l'applicazione
Per consentire il riutilizzo dei modelli in modalità multi-applicazione, la funzione `view($template, $data, $app = null)` fornisce un terzo parametro, `$app`, che può essere utilizzato per specificare quale cartella dell'applicazione utilizzare per i modelli. Ad esempio, `view('user', [], 'admin')` forzerà l'utilizzo dei file del modello sotto `app/admin/view/`.

## Estensione di Twig

> **Nota**
> Questa funzionalità richiede webman-framework >= 1.4.8

È possibile estendere l'istruzione Twig aggiungendo un callback `view.extension` alla configurazione come mostrato di seguito nel file `config/view.php`
```php
<?php
use support\view\Twig;
return [
    'handler' => Twig::class,
    'extension' => function (Twig\Environment $twig) {
        $twig->addExtension(new your\namespace\YourExtension()); // Aggiungi un'estensione
        $twig->addFilter(new Twig\TwigFilter('rot13', 'str_rot13')); // Aggiungi un filtro
        $twig->addFunction(new Twig\TwigFunction('nome_funzione', function () {})); // Aggiungi una funzione
    }
];
```
## Estensione di Blade
> **Nota**
> Questa funzionalità richiede webman-framework>=1.4.8
Analogamente, possiamo estendere l'istanza della vista di Blade attraverso il callback di configurazione `view.extension`, ad esempio `config/view.php` come segue:

```php
<?php
use support\view\Blade;
return [
    'handler' => Blade::class,
    'extension' => function (Jenssegers\Blade\Blade $blade) {
        // Aggiungi direttive a Blade
        $blade->directive('mydate', function ($timestamp) {
            return "<?php echo date('Y-m-d H:i:s', $timestamp); ?>";
        });
    }
];
```

## Utilizzo del componente component di Blade

> **Nota**
> richiede webman/blade>=1.5.2**

Supponiamo di voler aggiungere un componente di allerta

**Crea `app/view/components/Alert.php`**
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

**Crea `app/view/components/alert.blade.php`**
```
<div>
    <b style="color: red">ciao componente di Blade</b>
</div>
```

**`/config/view.php` simile al seguente codice**
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

A questo punto, il componente di Blade Alert è stato impostato e può essere utilizzato nel template come segue
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

Per ulteriori dettagli, consulta [estensione dei tag in think-template](https://www.kancloud.cn/manual/think-template/1286424)
