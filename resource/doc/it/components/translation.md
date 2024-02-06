# Lingua multipla

La lingua multipla è utilizzata dal componente [symfony/translation](https://github.com/symfony/translation).

## Installazione
```
composer require symfony/translation
```

## Creazione del pacchetto lingua
Per impostazione predefinita, webman colloca il pacchetto lingua nella directory `resource/translations` (creala tu stesso se non è presente); se desideri modificare la directory, devi impostarla in `config/translation.php`.
Ogni lingua corrisponde a una sottocartella in cui la definizione della lingua è di default collocata in `messages.php` come mostrato di seguito:
```
resource/
└── translations
    ├── en
    │   └── messages.php
    └── zh_CN
        └── messages.php
```

Tutti i file di lingua restituiscono un array come segue:
```php
// resource/translations/en/messages.php

return [
    'hello' => 'Ciao webman',
];
```

## Configurazione

`config/translation.php`

```php
return [
    // Lingua predefinita
    'locale' => 'zh_CN',
    // Lingua di fallback, tenta di utilizzare la traduzione dalla lingua di fallback se non trova la traduzione nella lingua corrente
    'fallback_locale' => ['zh_CN', 'en'],
    // Percorso in cui sono collocati i file di lingua
    'path' => base_path() . '/resource/translations',
];
```

## Traduzione

La traduzione avviene tramite il metodo `trans()`.

Crea il file di lingua `resource/translations/zh_CN/messages.php` come segue:
```php
return [
    'hello' => 'Ciao mondo!',
];
```

Crea il file `app/controller/UserController.php`
```php
<?php
namespace app\controller;

use support\Request;

class UserController
{
    public function get(Request $request)
    {
        $hello = trans('hello'); // Ciao mondo!
        return response($hello);
    }
}
```

Accedendo a `http://127.0.0.1:8787/user/get` restituirà "Ciao mondo!"

## Cambia la lingua predefinita

Per cambiate la lingua si utilizza il metodo `locale()`.

Aggiungi il file di lingua `resource/translations/en/messages.php` come segue:
```php
return [
    'hello' => 'ciao mondo!',
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
        // Cambia la lingua
        locale('en');
        $hello = trans('hello'); // ciao mondo!
        return response($hello);
    }
}
```
Accedendo a `http://127.0.0.1:8787/user/get` restituirà "ciao mondo!"

È anche possibile utilizzare il quarto parametro della funzione `trans()` per cambiare temporaneamente la lingua, ad esempio, l'esempio sopra e quello successivo sono equivalenti:
```php
<?php
namespace app\controller;

use support\Request;

class UserController
{
    public function get(Request $request)
    {
        // Cambia la lingua con il quarto parametro
        $hello = trans('hello', [], null, 'en'); // ciao mondo!
        return response($hello);
    }
}
```

## Impostare esplicitamente la lingua per ogni richiesta
La traduzione è un singleton, il che significa che tutte le richieste condividono questa istanza. Se una richiesta utilizza `locale()` per impostare la lingua predefinita, influenzerà tutte le richieste successive in quel processo. Pertanto, si dovrebbe impostare esplicitamente la lingua per ogni richiesta. Ad esempio, utilizzando il middleware seguente.

Crea il file `app/middleware/Lang.php` (crealo tu stesso se la directory non esiste) come segue:
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

Aggiungi il middleware globale in `config/middleware.php` come segue:
```php
return [
    // Middleware globale
    '' => [
        // ... altri middleware qui
        app\middleware\Lang::class,
    ]
];
```

## Uso dei segnaposto
A volte un messaggio contiene delle variabili da tradurre, ad esempio
```php
trans('hello ' . $name);
```
In questi casi si utilizzano dei segnaposto.

Modifica `resource/translations/zh_CN/messages.php` come segue:
```php
return [
    'hello' => 'Ciao %name%!',
];
```
Quando si effettua la traduzione, i dati vengono passati attraverso il secondo parametro per corrispondere ai valori dei segnaposto
```php
trans('hello', ['%name%' => 'webman']); // Ciao webman!
```

## Gestione dell'accordo in caso di pluralità
Alcune lingue mostrano una struttura della frase diversa a seconda della quantità di oggetti, ad esempio "Ci sono %count% mele"; la frase è corretta quando `%count%` è uguale a 1, ma è errata quando è maggiore di 1.

In questi casi si utilizza il **pipe** (`|`) per elencare le forme di accordo.

Aggiungi al file di lingua `resource/translations/en/messages.php` la chiave `apple_count` come segue:
```php
return [
    // ...
    'apple_count' => 'C\'è una mela|Ci sono %count% mele',
];
```

```php
trans('apple_count', ['%count%' => 10]); // Ci sono 10 mele
```

È persino possibile specificare un intervallo numerico per creare regole di accordo più complesse:
```php
return [
    // ...
    'apple_count' => '{0} Non ci sono mele|{1} C'è una mela|]1,19] Ci sono %count% mele|[20,Inf[ Ci sono molte mele'
];
```

```php
trans('apple_count', ['%count%' => 20]); // Ci sono molte mele
```

## Specificare il file di lingua
Il file di lingua ha di default il nome `messages.php`, ma in realtà è possibile creare file di lingua con altri nomi.

Crea il file di lingua `resource/translations/zh_CN/admin.php` come segue:
```php
return [
    'hello_admin' => 'Ciao amministratore!',
];
```

Specifica il file di lingua utilizzando il terzo parametro di `trans()` (omettendo l'estensione `.php`)
```php
trans('hello', [], 'admin', 'zh_CN'); // Ciao amministratore!
```

## Ulteriori informazioni
Consulta il [manuale di symfony/translation](https://symfony.com/doc/current/translation.html)
