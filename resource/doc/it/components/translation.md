# Multilingua

Il multilingua utilizza il componente [symfony/translation](https://github.com/symfony/translation).

## Installazione
``` 
compositore richiede symfony/translation
```

## Creare il pacchetto lingua
Per impostazione predefinita, webman mette il pacchetto lingua nella directory `resource/translations` (se non esiste, crealo tu stesso). Se è necessario modificare la directory, impostala nel file `config/translation.php`.
Ogni lingua corrisponde a una sottocartella, e le definizioni delle lingue vanno di default nel file `messages.php`. Ecco un esempio:

``` 
resource/
└── translations
    ├── en
    │   └── messages.php
    └── zh_CN
        └── messages.php
```

Tutti i file linguistiche restituiscono un array, ad esempio:
```php
<?php
// resource/translations/en/messages.php

return [
    'hello' => 'Hello webman',
];
```

## Configurazione

`config/translation.php`

```php
return [
    // Lingua predefinita
    'locale' => 'zh_CN',
    // Lingua di fallback, se la traduzione non è disponibile nella lingua corrente, proverà a usare la traduzione nelle lingue di fallback
    'fallback_locale' => ['zh_CN', 'en'],
    // Percorso in cui salvare i file linguistici
    'path' => base_path() . '/resource/translations',
];
```

## Traduzione

La traduzione avviene utilizzando il metodo `trans()`.

Crea il file linguistico `resource/translations/zh_CN/messages.php` come segue:
```php
<?php
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

Visitando `http://127.0.0.1:8787/user/get` restituirà "Ciao mondo!"

## Cambiare la lingua predefinita

Per cambiare la lingua, usa il metodo `locale()`.

Aggiungi il file linguistico `resource/translations/en/messages.php` come segue:
```php
<?php
return [
    'hello' => 'Ciao mondo!',
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
        $hello = trans('hello'); // Ciao mondo!
        return response($hello);
    }
}
```
Visitando `http://127.0.0.1:8787/user/get` restituirà "Ciao mondo!"

Puoi anche utilizzare il quarto parametro della funzione `trans()` per cambiare temporaneamente la lingua, ad esempio:
```php
<?php
namespace app\controller;

use support\Request;

class UserController
{
    public function get(Request $request)
    {
        // Cambia la lingua utilizzando il quarto parametro
        $hello = trans('hello', [], null, 'en'); // Ciao mondo!
        return response($hello);
    }
}
```

## Impostare esplicitamente la lingua per ogni richiesta

La traduzione è un singleton, il che significa che tutte le richieste condividono questa istanza. Se una richiesta utilizza il metodo `locale()` per impostare la lingua predefinita, avrà un impatto su tutte le richieste successive in quel processo. Quindi dovremmo impostare esplicitamente la lingua per ogni richiesta. Ad esempio, utilizzando il middleware seguente:

Crea il file `app/middleware/Lang.php` (se la cartella non esiste, creala tu stesso) come segue:
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
    // Middleware globali
    '' => [
        // ... Altri middleware sono omessi qui
        app\middleware\Lang::class,
    ]
];
```

## Utilizzo dei segnaposto

A volte, un messaggio contiene variabili da tradurre, ad esempio
```php
trans('hello ' . $name);
```
Quando ti trovi in questa situazione, utilizza dei segnaposto per gestire questi casi.

Modifica `resource/translations/zh_CN/messages.php` come segue:
```php
<?php
return [
    'hello' => 'Ciao %name%!',
];
```
Nella traduzione, passa i dati corrispondenti al segnaposto attraverso il secondo parametro
```php
trans('hello', ['%name%' => 'webman']); // Ciao webman!
```

## Gestione dei plurali

In alcune lingue, la struttura delle frasi varia a seconda del numero di oggetti, ad esempio "Ci sono %count% mele", corretto per %count% uguale a 1, ma scorretto per valori superiori a 1.

In questi casi, utilizziamo il **pipe** (`|`) per elencare le forme plurali.

Aggiungi la voce `apple_count` al file linguistico `resource/translations/en/messages.php` come segue:
```php
<?php
return [
    // ...
    'apple_count' => 'There is one apple|There are %count% apples',
];
```

```php
trans('apple_count', ['%count%' => 10]); // There are 10 apples
```

Possiamo addirittura specificare un intervallo numerico, creando così regole per i plurali più complesse:
```php
return [
    // ...
    'apple_count' => '{0} There are no apples|{1} There is one apple|]1,19] There are %count% apples|[20,Inf[ There are many apples'
];
```

```php
trans('apple_count', ['%count%' => 20]); // There are many apples
```

## Specificare il file linguistico

Il nome predefinito del file linguistico è `messages.php`, ma in realtà è possibile creare file con nomi diversi.

Crea il file linguistico `resource/translations/zh_CN/admin.php` come segue:
```php
<?php
return [
    'hello_admin' => 'Ciao Amministratore!',
];
```

Specificare il file linguistico tramite il terzo parametro di `trans()` (senza includere l'estensione `.php`).
```php
trans('hello', [], 'admin', 'zh_CN'); // Ciao Amministratore!
```

## Ulteriori informazioni
Consulta il manuale di [symfony/translation](https://symfony.com/doc/current/translation.html)
