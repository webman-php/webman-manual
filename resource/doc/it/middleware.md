# Middleware
I middleware sono comunemente utilizzati per intercettare le richieste o le risposte. Ad esempio, eseguono la verifica dell'identità dell'utente in modo uniforme prima di eseguire il controller, come reindirizzare l'utente alla pagina di accesso se non ha effettuato l'accesso, aggiungere un determinato header alla risposta, oppure calcolare la percentuale di richieste per un determinato uri e così via.

## Modello a cipolla dei middleware

```   
                                   
            ┌──────────────────────────────────────────────────────┐
            │                     middleware1                      │ 
            │     ┌──────────────────────────────────────────┐     │
            │     │               middleware2                │     │
            │     │     ┌──────────────────────────────┐     │     │
            │     │     │         middleware3          │     │     │        
            │     │     │     ┌──────────────────┐     │     │     │
            │     │     │     │                  │     │     │     │
   ── Richiesta ───────────────────────> Controllore ─ Risposta ───────────────────────────> Cliente
            │     │     │     │                  │     │     │     │
            │     │     │     └──────────────────┘     │     │     │
            │     │     │                              │     │     │
            │     │     └──────────────────────────────┘     │     │
            │     │                                          │     │
            │     └──────────────────────────────────────────┘     │
            │                                                      │
            └──────────────────────────────────────────────────────┘
```
I middleware e i controller costituiscono il classico modello a cipolla, in cui i middleware sono simili a strati di buccia di cipolla e i controller rappresentano il cuore della cipolla. Come mostrato nell'immagine, la richiesta attraversa i middleware 1, 2, 3 per raggiungere il controller, il quale restituisce una risposta che attraversa i middleware nell'ordine 3, 2, 1 prima di essere restituita al cliente. In altre parole, all'interno di ciascun middleware possiamo ottenere sia la richiesta sia la risposta.

## Intercezione delle richieste
A volte non vogliamo che una determinata richiesta raggiunga il livello del controller. Ad esempio, se nel middleware2 scopriamo che l'utente attuale non ha effettuato l'accesso, possiamo intercettare direttamente la richiesta e restituire una risposta di accesso. Il processo è simile a quanto segue:

```   
                            
            ┌────────────────────────────────────────────────────────────┐
            │                         middleware1                        │ 
            │     ┌────────────────────────────────────────────────┐     │
            │     │                   middleware2                  │     │
            │     │          ┌──────────────────────────────┐      │     │
            │     │          │        middleware3           │      │     │       
            │     │          │    ┌──────────────────┐      │      │     │
            │     │          │    │                  │      │      │     │
   ── Richiesta ───────┐     │    │    Controllore   │      │      │     │
            │     │ Risposta │    │                  │      │      │     │
   <───────────────────┘     │    └──────────────────┘      │      │     │
            │     │          │                              │      │     │
            │     │          └──────────────────────────────┘      │     │
            │     │                                                │     │
            │     └────────────────────────────────────────────────┘     │
            │                                                            │
            └────────────────────────────────────────────────────────────┘
```
Come mostrato nell'immagine, la richiesta raggiunge il middleware2 e genera una risposta di accesso; la risposta passa attraverso il middleware 1 e viene restituita al cliente.

## Interfaccia dei middleware
I middleware devono implementare l'interfaccia `Webman\MiddlewareInterface`.
```php
interface MiddlewareInterface
{
    /**
     * Process an incoming server request.
     *
     * Processes an incoming server request in order to produce a response.
     * If unable to produce the response itself, it may delegate to the provided
     * request handler to do so.
     */
    public function process(Request $request, callable $handler): Response;
}
```
In altre parole, devono implementare obbligatoriamente il metodo `process`, il quale deve restituire un oggetto `support\Response`. Di default, questo oggetto è generato da `$handler($request)` (la richiesta continuerà a passare attraverso la cipolla), oppure può essere generato da funzioni di utilità come `response()`, `json()`, `xml()`, `redirect()` e così via (la richiesta si fermerà prima di continuare a passare attraverso la cipolla).
## Ottenere richiesta e risposta nei middleware

Nei middleware è possibile ottenere la richiesta e anche la risposta dopo l'esecuzione del controller, quindi internamente il middleware è diviso in tre parti.
1. Fase di attraversamento della richiesta, cioè la fase prima dell'elaborazione della richiesta
2. Fase di gestione della richiesta del controller, cioè la fase di gestione della richiesta
3. Fase di uscita della risposta, cioè la fase dopo l'elaborazione della richiesta

Le tre fasi sono rappresentate all'interno del middleware in questo modo
```php
<?php
namespace app\middleware;

use Webman\MiddlewareInterface;
use Webman\Http\Response;
use Webman\Http\Request;

class Test implements MiddlewareInterface
{
    public function process(Request $request, callable $handler) : Response
    {
        echo 'Questa è la fase di attraversamento della richiesta, ovvero prima dell'elaborazione della richiesta';
        
        $response = $handler($request); // Continua ad attraversare, fino a eseguire il controller e ottenere la risposta
        
        echo 'Questa è la fase di uscita della risposta, ovvero dopo l'elaborazione della richiesta';
        
        return $response;
    }
}
```
 
## Esempio: Middleware di autenticazione

Creare il file `app/middleware/AuthCheckTest.php` (se la directory non esiste, crearla manualmente) come segue:
```php
<?php
namespace app\middleware;

use ReflectionClass;
use Webman\MiddlewareInterface;
use Webman\Http\Response;
use Webman\Http\Request;

class AuthCheckTest implements MiddlewareInterface
{
    public function process(Request $request, callable $handler) : Response
    {
        if (session('user')) {
            // Utente già autenticato, la richiesta continua a attraversare
            return $handler($request);
        }

        // Ottieni i metodi del controller che non richiedono l'autenticazione usando la riflessione
        $controller = new ReflectionClass($request->controller);
        $noNeedLogin = $controller->getDefaultProperties()['noNeedLogin'] ?? [];

        // Il metodo richiesto richiede l'autenticazione
        if (!in_array($request->action, $noNeedLogin)) {
            // Interrompi la richiesta, restituisci una risposta di reindirizzamento e fermati
            return redirect('/user/login');
        }

        // Non è necessaria l'autenticazione, la richiesta continua a attraversare
        return $handler($request);
    }
}
```
  
Creare il controller `app/controller/UserController.php`
```php
<?php
namespace app\controller;
use support\Request;

class UserController
{
    /**
     * Metodi che non richiedono l'autenticazione
     */
    protected $noNeedLogin = ['login'];

    public function login(Request $request)
    {
        $request->session()->set('user', ['id' => 10, 'name' => 'webman']);
        return json(['code' => 0, 'msg' => 'accesso effettuato']);
    }

    public function info()
    {
        return json(['code' => 0, 'msg' => 'ok', 'data' => session('user')]);
    }
}
```

In `config/middleware.php` aggiungere il middleware globale come segue:
```php
return [
    // Middleware globale
    '' => [
        // ... Altri middleware omessi
        app\middleware\AuthCheckTest::class,
    ]
];
```

Con il middleware di autenticazione, possiamo concentrarci sulla scrittura del codice di business a livello di controller senza preoccuparci se l'utente è autenticato.

## Esempio: Middleware per le richieste cross-domain

Creare il file `app/middleware/AccessControlTest.php` (se la directory non esiste, crearla manualmente) come segue:
```php
<?php
namespace app\middleware;

use Webman\MiddlewareInterface;
use Webman\Http\Response;
use Webman\Http\Request;

class AccessControlTest implements MiddlewareInterface
{
    public function process(Request $request, callable $handler) : Response
    {
        // Se è una richiesta OPTIONS, restituisci una risposta vuota, altrimenti continua a passare attraverso e ottieni una risposta
        $response = $request->method() == 'OPTIONS' ? response('') : $handler($request);
        
        // Aggiungi intestazioni HTTP relative al controllo degli accessi al dominio
        $response->withHeaders([
            'Access-Control-Allow-Credentials' => 'true',
            'Access-Control-Allow-Origin' => $request->header('origin', '*'),
            'Access-Control-Allow-Methods' => $request->header('access-control-request-method', '*'),
            'Access-Control-Allow-Headers' => $request->header('access-control-request-headers', '*'),
        ]);
        
        return $response;
    }
}
```

In `config/middleware.php` aggiungere il middleware globale come segue:
```php
return [
    // Middleware globale
    '' => [
        // ... Altri middleware omessi
        app\middleware\AccessControlTest::class,
    ]
];
```

Se la richiesta Ajax personalizza i campi dell'intestazione, è necessario aggiungere questi campi personalizzati nell'intestazione `Access-Control-Allow-Headers` del middleware, altrimenti verrà generato un errore `Request header field XXXX is not allowed by Access-Control-Allow-Headers in preflight response`.

## Note

- I middleware sono divisi in middleware globali, middleware dell'applicazione (validi solo in modalità multi-applicazione, vedere [multi-app](multiapp.md)), middleware del percorso
- Attualmente non è supportato il middleware per singoli controller (ma è possibile implementare funzionalità simili al middleware del controller attraverso la verifica di `$request->controller` nel middleware)
- Posizione del file di configurazione del middleware in `config/middleware.php`
- La configurazione del middleware globale è sotto la chiave `''`
- La configurazione del middleware dell'applicazione è sotto il nome dell'applicazione specifica, ad esempio

```php
return [
    // Middleware globale
    '' => [
        app\middleware\AuthCheckTest::class,
        app\middleware\AccessControlTest::class,
    ],
    // Middleware dell'applicazione api (valido solo in modalità multi-applicazione)
    'api' => [
        app\middleware\ApiOnly::class,
    ]
];
```

## Middleware del percorso

È possibile impostare un middleware per una singola o un gruppo di percorsi.
Ad esempio, aggiungere la seguente configurazione in `config/route.php`:

```php
<?php
use support\Request;
use Webman\Route;

Route::any('/admin', [app\admin\controller\IndexController::class, 'index'])->middleware([
    app\middleware\MiddlewareA::class,
    app\middleware\MiddlewareB::class,
]);

Route::group('/blog', function () {
   Route::any('/create', function () {return response('create');});
   Route::any('/edit', function () {return response('edit');});
   Route::any('/view/{id}', function ($r, $id) {response("view $id");});
})->middleware([
    app\middleware\MiddlewareA::class,
    app\middleware\MiddlewareB::class,
]);
```
## Costruttore middleware con parametri

> **N.B.**
> Questa funzionalità richiede webman-framework >= 1.4.8

A partire dalla versione 1.4.8, è possibile istanziare direttamente il middleware o una funzione anonima nel file di configurazione per consentire il passaggio dei parametri attraverso il costruttore del middleware.
Ad esempio, nel file `config/middleware.php`, è possibile configurare come segue:
```
return [
    // Middleware globale
    '' => [
        new app\middleware\AuthCheckTest($param1, $param2, ...),
        function(){
            return new app\middleware\AccessControlTest($param1, $param2, ...);
        },
    ],
    // Middleware dell'applicazione API (valido solo in modalità multi-applicazione)
    'api' => [
        app\middleware\ApiOnly::class,
    ]
];
```

Analogamente, è possibile passare parametri al middleware attraverso il costruttore anche nell'ambito del routing. Ad esempio, nel file `config/route.php`:
```
Route::any('/admin', [app\admin\controller\IndexController::class, 'index'])->middleware([
    new app\middleware\MiddlewareA($param1, $param2, ...),
    function(){
        return new app\middleware\MiddlewareB($param1, $param2, ...);
    },
]);
```

## Ordine di esecuzione dei middleware
- L'ordine di esecuzione dei middleware è `middleware globale` -> `middleware dell'applicazione` -> `middleware del percorso`.
- Se ci sono più middleware globali, vengono eseguiti nell'ordine in cui sono configurati nel file di configurazione (analogamente per i middleware dell'applicazione e del percorso).
- Le richieste 404 non attivano alcun middleware, compresi i middleware globali.

## Passaggio di parametri al middleware (route->setParams)

**Configurazione del percorso `config/route.php`**
```php
<?php
use support\Request;
use Webman\Route;

Route::any('/test', [app\controller\IndexController::class, 'index'])->setParams(['some_key' =>'some value']);
```

**Middleware (ipoteticamente un middleware globale)**
```php
<?php
namespace app\middleware;

use Webman\MiddlewareInterface;
use Webman\Http\Response;
use Webman\Http\Request;

class Hello implements MiddlewareInterface
{
    public function process(Request $request, callable $handler) : Response
    {
        // Il percorso predefinito $request->route è nullo, quindi è necessario verificare se $request->route è vuoto
        if ($route = $request->route) {
            $value = $route->param('some_key');
            var_export($value);
        }
        return $handler($request);
    }
}
```

## Passaggio di parametri dal middleware al controller

A volte, il controller potrebbe aver bisogno di utilizzare i dati generati nel middleware. In tal caso, è possibile passare i parametri al controller aggiungendo attributi all'oggetto `$request`. Ad esempio:

**Middleware**
```php
<?php
namespace app\middleware;

use Webman\MiddlewareInterface;
use Webman\Http\Response;
use Webman\Http\Request;

class Hello implements MiddlewareInterface
{
    public function process(Request $request, callable $handler) : Response
    {
        $request->data = 'some value';
        return $handler($request);
    }
}
```

**Controller:**
```php
<?php
namespace app\controller;

use support\Request;

class FooController
{
    public function index(Request $request)
    {
        return response($request->data);
    }
}
```

## Accesso alle informazioni sul percorso corrente nel middleware

> **N.B.**
> Richiede webman-framework >= 1.3.2

È possibile utilizzare `$request->route` per ottenere l'oggetto del percorso e accedere alle informazioni corrispondenti chiamando i metodi appropriati.

**Configurazione del percorso**
```php
<?php
use support\Request;
use Webman\Route;

Route::any('/user/{uid}', [app\controller\UserController::class, 'view']);
```

**Middleware**
```php
<?php
namespace app\middleware;

use Webman\MiddlewareInterface;
use Webman\Http\Response;
use Webman\Http\Request;

class Hello implements MiddlewareInterface
{
    public function process(Request $request, callable $handler) : Response
    {
        $route = $request->route;
        // Se la richiesta non corrisponde a nessun percorso (esclusi i percorsi predefiniti), $request->route è null
        // Ad esempio, se l'utente ha accesso all'URL /user/111, verrà stampata la seguente informazione
        if ($route) {
            var_export($route->getPath());       // /user/{uid}
            var_export($route->getMethods());    // ['GET', 'POST', 'PUT', 'DELETE', 'PATCH', 'HEAD','OPTIONS']
            var_export($route->getName());       // user_view
            var_export($route->getMiddleware()); // []
            var_export($route->getCallback());   // ['app\\controller\\UserController', 'view']
            var_export($route->param());         // ['uid'=>111]
            var_export($route->param('uid'));    // 111 
        }
        return $handler($request);
    }
}
```

> **N.B.**
> Il metodo `$route->param()` richiede webman-framework >= 1.3.16

## Gestione delle eccezioni nel middleware

> **N.B.**
> Richiede webman-framework >= 1.3.15

Durante l'elaborazione delle operazioni aziendali, potrebbero verificarsi delle eccezioni. Nel middleware, è possibile utilizzare `$response->exception()` per ottenere l'eccezione.

**Configurazione del percorso**
```php
<?php
use support\Request;
use Webman\Route;

Route::any('/user/{uid}', function (Request $request, $uid) {
    throw new \Exception('test eccezione');
});
```

**Middleware:**
```php
<?php
namespace app\middleware;

use Webman\MiddlewareInterface;
use Webman\Http\Response;
use Webman\Http\Request;

class Hello implements MiddlewareInterface
{
    public function process(Request $request, callable $handler) : Response
    {
        $response = $handler($request);
        $exception = $response->exception();
        if ($exception) {
            echo $exception->getMessage();
        }
        return $response;
    }
}
```

## Middleware sovra-globale

> **N.B.**
> Questa funzionalità richiede webman-framework >= 1.5.16

Il middleware globale del progetto principale influenza solo il progetto principale e non influisce sui [plugin dell'applicazione](app/app.md). A volte, potremmo voler aggiungere un middleware che influenzi non solo il progetto principale, ma anche tutti i plugin, in tal caso possiamo utilizzare il middleware sovra-globale `@`.

Nel file `config/middleware.php`, è possibile effettuare la configurazione come segue:
```php
return [
    '@' => [ // Aggiungi il middleware globale a tutti i plugin dell'applicazione
        app\middleware\MiddlewareGlobl::class,
    ], 
    '' => [], // Aggiungi semplicemente il middleware globale al progetto principale
];
```

> **Nota**
> Il middleware sovra-globale `@` può essere configurato non solo nel progetto principale, ma anche in un determinato plugin. Ad esempio, configurando il middleware sovra-globale `@` nel file `plugin/ai/config/middleware.php`, influenzerà anche il progetto principale e tutti i plugin.

## Aggiunta di un middleware a un plugin

> **N.B.**
> Questa funzionalità richiede webman-framework >= 1.5.16

A volte ci piacerebbe aggiungere un middleware a un [plugin dell'applicazione](app/app.md) senza modificare il codice del plugin stesso (per non sovrascrivere le modifiche durante l'aggiornamento). In tal caso, è possibile configurarlo nel progetto principale per applicarlo al plugin.

Nel file `config/middleware.php`, è possibile effettuare la configurazione come segue:
```php
return [
    'plugin.ai' => [], // Aggiungi il middleware al plugin ai
    'plugin.ai.admin' => [], // Aggiungi il middleware al modulo admin del plugin ai
];
```

> **Nota**
> Naturalmente, è anche possibile configurare un'impalcatura simile in un determinato plugin per influenzare altri plugin, ad esempio, aggiungendo la stessa configurazione nel file `plugin/foo/config/middleware.php` influenzerà anche il plugin ai.
