# Middleware
I middleware vengono comunemente utilizzati per intercettare le richieste o le risposte. Ad esempio, per verificare in modo uniforme l'identità dell'utente prima di eseguire il controller, come ad esempio reindirizzare l'utente alla pagina di accesso se non ha effettuato l'accesso, o per aggiungere un'intestazione specifica alla risposta. Possono anche essere utilizzati per raccogliere statistiche sulla percentuale di richieste per una determinata URI e così via.

## Modello ad Anello per i Middleware

```
                              
            ┌──────────────────────────────────────────────────────┐
            │                     middleware1                      │ 
            │     ┌──────────────────────────────────────────┐     │
            │     │               middleware2                │     │
            │     │     ┌──────────────────────────────┐     │     │
            │     │     │         middleware3          │     │     │        
            │     │     │     ┌──────────────────┐     │     │     │
            │     │     │     │                  │     │     │     │
 　── Richiesta ───────────────────────> Controller ─ Risposta ───────────────────────────> Client
            │     │     │     │                  │     │     │     │
            │     │     │     └──────────────────┘     │     │     │
            │     │     │                              │     │     │
            │     │     └──────────────────────────────┘     │     │
            │     │                                          │     │
            │     └──────────────────────────────────────────┘     │
            │                                                      │
            └──────────────────────────────────────────────────────┘
```
I middleware e i controller costituiscono un classico modello ad anello, in cui i middleware formano i vari strati dell'anello mentre il controller costituisce il nucleo dell'anello. Come mostrato nel diagramma, la richiesta attraversa i middleware 1, 2, 3 per raggiungere il controller, il quale restituirà una risposta. La risposta attraverserà quindi i middleware in ordine inverso (3, 2, 1) prima di essere restituita al client. Questo significa che in ciascun middleware possiamo accedere sia alla richiesta che alla risposta.

## Intercezione delle Richieste
A volte non vogliamo che una determinata richiesta raggiunga il livello del controller. Ad esempio, se in un middleware di autenticazione verifichiamo che l'utente attuale non sia loggato, possiamo intercettare direttamente la richiesta e restituire una risposta di accesso. Il processo appare quindi come segue:

```
                              
            ┌───────────────────────────────────────────────────────┐
            │                     middleware1                       │ 
            │     ┌───────────────────────────────────────────┐     │
            │     │          　 　 Middleware di Autenticazione    │     │
            │     │      ┌──────────────────────────────┐     │     │
            │     │      │         middleware3          │     │     │       
            │     │      │     ┌──────────────────┐     │     │     │
            │     │      │     │                  │     │     │     │
 　── Richiesta ───────────┐   │     │       Controller      │     │     │
            │     │ Risposta │     │                  │     │     │     │
   <─────────────────┘   │     └──────────────────┘     │     │     │
            │     │      │                              │     │     │
            │     │      └──────────────────────────────┘     │     │
            │     │                                           │     │
            │     └───────────────────────────────────────────┘     │
            │                                                       │
            └───────────────────────────────────────────────────────┘
```

Come mostrato nel diagramma, la richiesta viene intercettata nel middleware di autenticazione e viene restituita una risposta di accesso che si muove all'indietro attraverso il middleware 1 e torna al browser.

## Interfaccia del Middleware
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
Questo significa che devono implementare il metodo `process`. Il metodo `process` deve restituire un oggetto `support\Response`. Per default, questo oggetto è generato da `$handler($request)` (la richiesta continuerà a attraversare l'anello). Tuttavia, può anche essere generato da una delle funzioni di supporto come `response()`, `json()`, `xml()`, `redirect()`, etc. (la richiesta si ferma e non continua attraverso l'anello).

## Accesso alla Richiesta e alla Risposta all'interno dei Middleware
All'interno dei middleware è possibile accedere sia alla richiesta che alla risposta generata dopo l'esecuzione del controller. Quindi, i middleware si suddividono in tre parti:
1. Fase di attraversamento della richiesta, ovvero la fase prima del trattamento della richiesta
2. Fase di trattamento della richiesta da parte del controller, ovvero la fase di trattamento della richiesta
3. Fase di uscita della risposta, ovvero la fase successiva al trattamento della richiesta

Queste tre fasi all'interno dei middleware sono rappresentate come segue:
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
        echo 'Fase di attraversamento della richiesta, ovvero la fase prima del trattamento della richiesta';
        
        $response = $handler($request); // Il processo continua a attraversare gli anelli fino all'esecuzione del controller e ottenere una risposta
        
        echo 'Fase di uscita della risposta, ovvero la fase successiva al trattamento della richiesta';
        
        return $response;
    }
}
```
## Esempio: Middleware di autenticazione
Creare il file `app/middleware/AuthCheckTest.php` (creare la directory se necessario) come segue:
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
            // Se l'utente è già loggato, il processo continua attraverso gli strati della cipolla
            return $handler($request);
        }

        // Ottenere tramite reflection i metodi del controller che non richiedono l'autenticazione
        $controller = new ReflectionClass($request->controller);
        $noNeedLogin = $controller->getDefaultProperties()['noNeedLogin'] ?? [];

        // Il metodo richiesto richiede l'autenticazione
        if (!in_array($request->action, $noNeedLogin)) {
            // Interrompere la richiesta e restituire una risposta di reindirizzamento, fermando il passaggio attraverso gli strati della cipolla
            return redirect('/user/login');
        }

        // Non è richiesta l'autenticazione, il processo continua attraverso gli strati della cipolla
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
     * Metodi non richiedenti l'autenticazione
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

> **Nota**
> `$noNeedLogin` contiene i metodi del controller che possono essere accessibili senza autenticazione

Aggiungere il middleware globale al file `config/middleware.php` come segue:
```php
return [
    // Middleware globali
    '' => [
        // ... Altri middleware omessi qui
        app\middleware\AuthCheckTest::class,
    ]
];
```

Con il middleware di autenticazione, è possibile concentrarsi sulla scrittura del codice di business nel livello del controller senza preoccuparsi se l'utente è loggato.

## Esempio: Middleware di richiesta cross-domain
Creare il file `app/middleware/AccessControlTest.php` (creare la directory se necessario) come segue:
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
        // Se si tratta di una richiesta options, restituire una risposta vuota, altrimenti continuare attraverso gli strati della cipolla e ottenere una risposta
        $response = $request->method() == 'OPTIONS' ? response('') : $handler($request);
        
        // Aggiungere all'intestazione della risposta le intestazioni HTTP correlate al cross-domain
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

> **Nota**
> Le richieste cross-domain potrebbero generare richieste OPTIONS. Non vogliamo che le richieste OPTIONS arrivino al controller, quindi restituiamo direttamente una risposta vuota (`response('')`) per intercettare la richiesta.
> Se le tue API richiedono routing, utilizza `Route::any(..)` oppure `Route::add(['POST', 'OPTIONS'], ..)` per impostare il routing.

Aggiungere il middleware globale al file `config/middleware.php` come segue:
```php
return [
    // Middleware globali
    '' => [
        // ... Altri middleware omessi qui
        app\middleware\AccessControlTest::class,
    ]
];
```

> **Nota**
> Se le richieste ajax hanno intestazioni personalizzate, è necessario includere queste intestazioni personalizzate nel campo `Access-Control-Allow-Headers` del middleware, altrimenti verrà generato l'errore `Request header field XXXX is not allowed by Access-Control-Allow-Headers in preflight response.`

## Spiegazione

- I middleware possono essere globali, locali (validi solo in modalità multi-app, vedere [multiapp.md](multiapp.md)) e dei routing.
- Attualmente non è supportato il middleware per un singolo controller (ma è possibile implementare una funzionalità simile a un middleware del controller attraverso la verifica di `$request->controller` all'interno del middleware).
- Il file di configurazione del middleware si trova in `config/middleware.php`.
- La configurazione del middleware globale è sotto la chiave `''`.
- La configurazione del middleware di app è sotto il nome dell'applicazione specifica, ad esempio:

```php
return [
    // Middleware globali
    '' => [
        app\middleware\AuthCheckTest::class,
        app\middleware\AccessControlTest::class,
    ],
    // Middleware dell'applicazione api (validi solo in modalità multi-app)
    'api' => [
        app\middleware\ApiOnly::class,
    ]
];
```

## Middleware di routing

È possibile impostare i middleware per una singola route o per un gruppo di route.
Ad esempio, nel file `config/route.php` aggiungere la seguente configurazione:
```php
<?php
use support\Request;
use Webman\Route;

Route::any('/admin', [app\admin\controller\IndexController::class, 'index'])->middleware([
    app\middleware\MiddlewareA::class,
    app\middleware\MiddlewareB::class,
]);

Route::group('/blog', function () {
   Route::any('/create', function () {return response('crea');});
   Route::any('/edit', function () {return response('modifica');});
   Route::any('/view/{id}', function ($r, $id) {response("visualizza $id");});
})->middleware([
    app\middleware\MiddlewareA::class,
    app\middleware\MiddlewareB::class,
]);
```

## Parametri nel costruttore del middleware

> **Nota**
> Questa funzionalità richiede webman-framework >= 1.4.8

Dalla versione 1.4.8, è possibile istanziare direttamente il middleware nel file di configurazione o tramite funzioni anonime, permettendo di passare i parametri al middleware tramite il costruttore.
Ad esempio, nel file `config/middleware.php` è possibile configurare anche in questo modo:
```
return [
    // Middleware globali
    '' => [
        new app\middleware\AuthCheckTest($param1, $param2, ...),
        function(){
            return new app\middleware\AccessControlTest($param1, $param2, ...);
        },
    ],
    // Middleware dell'applicazione api (validi solo in modalità multi-app)
    'api' => [
        app\middleware\ApiOnly::class,
    ]
];
```

Analogamente, è possibile passare i parametri al middleware dei route attraverso il costruttore. Ad esempio nel file `config/route.php`:
```
Route::any('/admin', [app\admin\controller\IndexController::class, 'index'])->middleware([
    new app\middleware\MiddlewareA($param1, $param2, ...),
    function(){
        return new app\middleware\MiddlewareB($param1, $param2, ...);
    },
]);
```

## Ordine di esecuzione del middleware

- L'ordine di esecuzione del middleware è `middleware globale` -> `middleware dell'applicazione` -> `middleware del routing`.
- Se ci sono molti middleware globali, vengono eseguiti nell'ordine effettivo della configurazione (stessa logica per il middleware dell'applicazione e il middleware del routing).
- Le richieste 404 non attivano alcun middleware, inclusi i middleware globali.

## Passaggio dei parametri al middleware dalla route (route->setParams)

**Configurazione della route in `config/route.php`**
```php
<?php
use support\Request;
use Webman\Route;

Route::any('/test', [app\controller\IndexController::class, 'index'])->setParams(['some_key' =>'some value']);
```

**Middleware (supponendo sia un middleware globale)**
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
        // Poiché per impostazione predefinita $request->route è null, è necessario verificare che $request->route non sia vuoto
        if ($route = $request->route) {
            $value = $route->param('some_key');
            var_export($value);
        }
        return $handler($request);
    }
}
```

## Passaggio dei parametri al controller dal middleware

A volte il controller potrebbe aver bisogno di utilizzare i dati generati dal middleware. In tal caso, è possibile passare i parametri al controller attraverso l'aggiunta di attributi all'oggetto `$request`. Ad esempio:

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

## Middleware per ottenere le informazioni sul percorso della richiesta attuale
> **Nota**
> Richiede webman-framework >= 1.3.2

Possiamo utilizzare `$request->route` per ottenere l'oggetto del percorso e quindi richiamare i metodi corrispondenti per ottenere le informazioni desiderate.

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
        // Se la richiesta non corrisponde a nessun percorso (eccetto il percorso predefinito), $request->route sarà null
        // Supponendo che l'indirizzo visitato dal browser sia /user/111, verranno stampate le seguenti informazioni
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

> **Nota**
> Il metodo `$route->param()` richiede webman-framework >= 1.3.16


## Middleware per ottenere eccezioni
> **Nota**
> Richiede webman-framework >= 1.3.15

Durante il processo di gestione delle attività, potrebbe verificarsi un'eccezione. È possibile utilizzare `$response->exception()` all'interno del middleware per ottenere l'eccezione.

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

## Middleware globale
> **Nota**
> Questa funzionalità richiede webman-framework >= 1.5.16

I middleware globali del progetto principale influenzano solo il progetto principale e non hanno alcun impatto sugli [plugin dell'applicazione](app/app.md). A volte potremmo voler aggiungere un middleware che influenzi globalmente tutti i plugin, in tal caso possiamo utilizzare un middleware globale.

Configurazione in `config/middleware.php`:
```php
return [
    '@' => [ // Aggiungi middleware globale al progetto principale e a tutti i plugin
        app\middleware\MiddlewareGlobl::class,
    ], 
    '' => [], // Aggiungi solo un middleware globale al progetto principale
];
```

> **Suggerimento**
> Il middleware `@` può essere configurato non solo nel progetto principale, ma anche in un plugin specifico. Ad esempio, se viene configurato nel file `plugin/ai/config/middleware.php`, influenzerà anche il progetto principale e tutti i plugin.

## Aggiunta di un middleware a un plugin specifico

> **Nota**
> Questa funzionalità richiede webman-framework >= 1.5.16

A volte potremmo voler aggiungere un middleware a un [plugin dell'applicazione](app/app.md) senza dover modificare il codice del plugin stesso (per evitare sovrascritture durante l'aggiornamento). In tal caso, possiamo configurare il middleware desiderato nel progetto principale per influenzare il plugin.

Configurazione in `config/middleware.php`:
```php
return [
    'plugin.ai' => [], // Aggiungi middleware al plugin ai
    'plugin.ai.admin' => [], // Aggiungi il middleware al modulo admin del plugin ai
];
```

> **Suggerimento**
> Naturalmente, è possibile configurare una simile impostazione anche in un determinato plugin per influenzare altri plugin. Ad esempio, se viene aggiunta al file `plugin/foo/config/middleware.php` l'impostazione sopra menzionata, influenzerà anche il plugin ai.
