# Middleware
Middleware wird in der Regel verwendet, um Anfragen oder Antworten abzufangen. Zum Beispiel kann die Benutzeridentität vor der Ausführung des Controllers einheitlich überprüft werden. Wenn ein Benutzer nicht angemeldet ist, wird er beispielsweise zur Anmeldeseite weitergeleitet. Oder es könnte eine bestimmte Header-Zeile zur Antwort hinzugefügt werden. Es kann auch dazu verwendet werden, das Verhältnis der Anforderungen für eine bestimmte URI zu messen, usw.

## Middleware-Zwiebelmodell

```
                              
            ┌──────────────────────────────────────────────────────┐
            │                     middleware1                      │ 
            │     ┌──────────────────────────────────────────┐     │
            │     │               middleware2                │     │
            │     │     ┌──────────────────────────────┐     │     │
            │     │     │         middleware3          │     │     │        
            │     │     │     ┌──────────────────┐     │     │     │
            │     │     │     │                  │     │     │     │
 　── Anfrage ───────────────────────> Controller ─ Antwort ───────────────────────────> Kunde
            │     │     │     │                  │     │     │     │
            │     │     │     └──────────────────┘     │     │     │
            │     │     │                              │     │     │
            │     │     └──────────────────────────────┘     │     │
            │     │                                          │     │
            │     └──────────────────────────────────────────┘     │
            │                                                      │
            └──────────────────────────────────────────────────────┘
```
Middleware und Controller bilden ein klassisches Zwiebelmodell, bei dem Middleware wie Schalen einer Zwiebel übereinander liegen und der Controller das Herzstück der Zwiebel ist. Wie in der Abbildung gezeigt, durchläuft die Anfrage wie ein Pfeil die Middleware 1, 2, 3, um den Controller zu erreichen. Der Controller gibt eine Antwort zurück, die dann in umgekehrter Reihenfolge durch die Middleware zurück zum Endkunden gelangt. Das heißt, in jedem Middleware können wir sowohl die Anfrage als auch die Antwort bekommen.

## Anfrageinterception
Manchmal möchten wir nicht, dass eine bestimmte Anfrage den Controller erreicht. Zum Beispiel, wenn wir in einer Authentifizierungsmiddleware feststellen, dass der aktuelle Benutzer nicht angemeldet ist, können wir die Anfrage direkt abfangen und eine Anmeldeantwort zurückgeben. Der Prozess sieht dann wie folgt aus:

```
                              
            ┌───────────────────────────────────────────────────────┐
            │                     middleware1                       │ 
            │     ┌───────────────────────────────────────────┐     │
            │     │          　 　 Authentifizierungsmiddleware        │     │
            │     │      ┌──────────────────────────────┐     │     │
            │     │      │         middleware3          │     │     │       
            │     │      │     ┌──────────────────┐     │     │     │
            │     │      │     │                  │     │     │     │
 　── Anfrage ───────────┐   │     │       Controller      │     │     │
            │     │ Antwort │     │                  │     │     │     │
   <─────────────────┘   │     └──────────────────┘     │     │     │
            │     │      │                              │     │     │
            │     │      └──────────────────────────────┘     │     │
            │     │                                           │     │
            │     └───────────────────────────────────────────┘     │
            │                                                       │
            └───────────────────────────────────────────────────────┘
```

Wie in der Abbildung gezeigt, wird die Anfrage nach dem Erreichen der Authentifizierungsmiddleware abgefangen und eine Anmeldeantwort wird von der Authentifizierungsmiddleware wieder durch Middleware 1 an den Browser zurückgegeben.

## Middleware-Schnittstelle
Middleware muss das `Webman\MiddlewareInterface`-Interface implementieren.
```php
interface MiddlewareInterface
{
    /**
     * Verarbeiten einer eingehenden Serveranfrage.
     *
     * Verarbeitet eine eingehende Serveranfrage, um eine Antwort zu generieren.
     * Wenn sie selbst nicht in der Lage ist, die Antwort zu generieren, kann sie an den bereitgestellten
     * Request Handler delegieren, um dies zu tun.
     */
    public function process(Request $request, callable $handler): Response;
}
```
Das bedeutet, dass die `process`-Methode implementiert werden muss. Die `process`-Methode muss ein `support\Response`-Objekt zurückgeben. Standardmäßig wird dieses Objekt durch `$handler($request)` generiert (die Anfrage wird weiterhin durch die Middleware zum Zwiebelherz durchlaufen). Es kann auch eine Antwort sein, die von Helferfunktionen wie `response()`, `json()`, `xml()`, `redirect()` usw. generiert wurde, die die Anfrage stoppen, bevor sie das Zwiebelherz erreicht.

## Zugriff auf Anfrage und Antwort in Middleware
In einer Middleware können wir sowohl auf die Anfrage zugreifen als auch auf die Antwort nach der Controllerausführung. Die Middleware ist daher in drei Teile unterteilt:
1. Anforderungsüberquerungsphase vor der Anfrageverarbeitung
2. Controllerverarbeitungsphase der Anfrageverarbeitung
3. Antwortüberquerungsphase nach der Anfrageverarbeitung

Die Manifestation dieser drei Phasen in der Middleware sieht wie folgt aus:
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
        echo 'Dies ist die Anforderungsüberquerungsphase, also vor der Anfrageverarbeitung';
        
        $response = $handler($request); // Weiterhin durch die Zwiebelschale zum Zwiebelherz durchlaufen, bis die Controllerantwort erhalten wird
        
        echo 'Dies ist die Antwortüberquerungsphase, also nach der Anfrageverarbeitung';
        
        return $response;
    }
}
```
## Beispiel: Authentifizierungsmiddleware
Erstellen Sie die Datei `app/middleware/AuthCheckTest.php` (erstellen Sie das Verzeichnis, falls es nicht existiert) wie folgt:
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
            // Bereits angemeldet, die Anfrage soll weiter durch die Zwiebelschichten gehen
            return $handler($request);
        }

        // Mit Reflexion die Methoden des Controllers erhalten, die keine Anmeldung benötigen
        $controller = new ReflectionClass($request->controller);
        $noNeedLogin = $controller->getDefaultProperties()['noNeedLogin'] ?? [];

        // Die aufgerufene Methode erfordert eine Anmeldung
        if (!in_array($request->action, $noNeedLogin)) {
            // Anfrage abfangen, eine Umleitungsantwort zurückgeben, die Anfrage wird gestoppt und durchläuft nicht die Zwiebelschichten
            return redirect('/user/login');
        }

        // Keine Anmeldung erforderlich, die Anfrage soll weiter durch die Zwiebelschichten gehen
        return $handler($request);
    }
}
```

Erstellen Sie den Controller `app/controller/UserController.php`:
```php
<?php
namespace app\controller;
use support\Request;

class UserController
{
    /**
     * Methoden, für die keine Anmeldung erforderlich ist
     */
    protected $noNeedLogin = ['login'];

    public function login(Request $request)
    {
        $request->session()->set('user', ['id' => 10, 'name' => 'webman']);
        return json(['code' => 0, 'msg' => 'Anmeldung erfolgreich']);
    }

    public function info()
    {
        return json(['code' => 0, 'msg' => 'ok', 'data' => session('user')]);
    }
}
```

> **Hinweis**
> `$noNeedLogin` enthält die Methoden des aktuellen Controllers, die ohne Anmeldung aufgerufen werden können

Fügen Sie im `config/middleware.php` globale Middleware wie folgt hinzu:
```php
return [
    // Globale Middleware
    '' => [
        // ... Andere Middleware hier ausgelassen
        app\middleware\AuthCheckTest::class,
    ]
];
```

Mit der Authentifizierungsmiddleware können wir uns nun auf das Schreiben des Geschäftslogikcodes in der Controller-Ebene konzentrieren, ohne uns um die Anmeldung des Benutzers kümmern zu müssen.

## Beispiel: Cross-Origin-Anfrage-Middleware
Erstellen Sie die Datei `app/middleware/AccessControlTest.php` (erstellen Sie das Verzeichnis, falls es nicht existiert) wie folgt:
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
        // Wenn es sich um eine OPTIONS-Anfrage handelt, geben Sie eine leere Antwort zurück, andernfalls setzen Sie die Anfrage fort und erhalten eine Antwort
        $response = $request->method() == 'OPTIONS' ? response('') : $handler($request);
        
        // Fügen Sie der Antwort die für Cross-Origin relevanten HTTP-Header hinzu
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

> **Hinweis**
> Cross-Origin-Anfragen können eine OPTIONS-Anfrage auslösen. Wenn wir nicht möchten, dass OPTIONS-Anfragen den Controller erreichen, geben wir einfach eine leere Antwort (`response('')`) zurück, um die Anfrage abzufangen.
> Wenn Ihre Schnittstelle Routen benötigt, verwenden Sie `Route::any(..)` oder `Route::add(['POST', 'OPTIONS'], ..)`.

Fügen Sie in `config/middleware.php` globale Middleware wie folgt hinzu:
```php
return [
    // Globale Middleware
    '' => [
        // ... Andere Middleware hier ausgelassen
        app\middleware\AccessControlTest::class,
    ]
];
```

> **Hinweis**
> Wenn Ajax-Anfragen benutzerdefinierte Header verwenden, müssen Sie diese benutzerdefinierten Header in das Feld `Access-Control-Allow-Headers` der Middleware aufnehmen, ansonsten wird ein Fehler gemeldet: "Request header field XXXX is not allowed by Access-Control-Allow-Headers in preflight response."

## Erklärung

- Middleware wird in globale Middleware, Applikations- (nur gültig im Multi-App-Modus, siehe [Multi-App](multiapp.md)) und Routen-Middleware unterteilt
- Derzeit wird keine Middleware für einzelne Controller unterstützt, aber Sie können überprüfen, ob der Controller durch die Verwendung von `$request->controller` ähnliche Funktionen wie Controller-Middleware durchführt
- Die Position der Middleware-Konfigurationsdatei befindet sich in `config/middleware.php`
- Die Konfiguration der globalen Middleware erfolgt unter dem Schlüssel `''`
- Die Konfiguration der Applikations-Middleware erfolgt unter dem spezifischen Applikationsnamen, z.B.

```php
return [
    // Globale Middleware
    '' => [
        app\middleware\AuthCheckTest::class,
        app\middleware\AccessControlTest::class,
    ],
    // Middleware der API-Applikation (nur gültig im Multi-App-Modus)
    'api' => [
        app\middleware\ApiOnly::class,
    ]
];
```

## Routen-Middleware

Sie können einer einzelnen oder einer Gruppe von Routen Middleware zuweisen. Zum Beispiel fügen Sie die folgende Konfiguration in `config/route.php` hinzu:
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

## Middleware-Konstruktorparameter

> **Hinweis**
> Diese Funktion erfordert webman-framework >= 1.4.8

Nach der Version 1.4.8 können Konfigurationsdateien direkt Middleware-Instanzen oder anonyme Funktionen erstellen. Auf diese Weise können Sie Konstruktorparameter an die Middleware übergeben. Zum Beispiel können Sie die `config/middleware.php` wie folgt konfigurieren:

```php
return [
    // Globale Middleware
    '' => [
        new app\middleware\AuthCheckTest($param1, $param2, ...),
        function(){
            return new app\middleware\AccessControlTest($param1, $param2, ...);
        },
    ],
    // Middleware der API-Applikation (nur gültig im Multi-App-Modus)
    'api' => [
        app\middleware\ApiOnly::class,
    ]
];
```

Ebenso können Sie Routen-Middleware durch Konstruktorparameter übergeben, z.B. in `config/route.php`:
```php
Route::any('/admin', [app\admin\controller\IndexController::class, 'index'])->middleware([
    new app\middleware\MiddlewareA($param1, $param2, ...),
    function(){
        return new app\middleware\MiddlewareB($param1, $param2, ...);
    },
]);
```

## Reihenfolge der Middleware-Ausführung
- Die Reihenfolge der Middleware-Ausführung ist `Globale Middleware` -> `Applikations-Middleware` -> `Routen-Middleware`.
- Bei mehreren globalen Middleware wird die Ausführung in der Reihenfolge der tatsächlichen Konfiguration der Middleware durchgeführt (entsprechend auch für Applikations- und Routen-Middleware).
- Eine 404-Anfrage löst keine Middleware aus, einschließlich globaler Middleware

## Weitergabe von Parametern an Middleware über die Route (route->setParams)

**Konfiguration der Route `config/route.php`**
```php
<?php
use support\Request;
use Webman\Route;

Route::any('/test', [app\controller\IndexController::class, 'index'])->setParams(['some_key' =>'some value']);
```

**Middleware (angenommen, es handelt sich um globale Middleware)**
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
        // Standardroute $request->route ist null, daher müssen wir prüfen, ob $request->route leer ist
        if ($route = $request->route) {
            $value = $route->param('some_key');
            var_export($value);
        }
        return $handler($request);
    }
}
```

## Middleware-Übergabe von Parametern an den Controller

Manchmal muss der Controller Daten verwenden, die im Middleware erstellt wurden. In diesem Fall können wir dem Controller die Parameter über das Hinzufügen von Attributen zum `$request`-Objekt übergeben. Zum Beispiel:

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
## Middleware zum Abrufen von Routeninformationen

> **Hinweis**
> Erfordert webman-framework >= 1.3.2

Wir können `$request->route` verwenden, um das Routenobjekt zu erhalten, und durch Aufruf der entsprechenden Methoden die entsprechenden Informationen abzurufen.

**Routenkonfiguration**
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
        // Wenn die Anfrage keiner Route (mit Ausnahme der Standardroute) zugeordnet ist, ist $request->route null
        // Angenommen, der Browser ruft die Adresse /user/111 auf, dann wird folgende Information ausgegeben
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

> **Hinweis**
> Die Methode `$route->param()` erfordert webman-framework >= 1.3.16

## Middleware zum Abfangen von Ausnahmen

> **Hinweis**
> Erfordert webman-framework >= 1.3.15

Während der Geschäftsabwicklung können Ausnahmen auftreten. In einem Middleware können wir mit `$response->exception()` die Ausnahme abrufen.

**Routenkonfiguration**
```php
<?php
use support\Request;
use Webman\Route;

Route::any('/user/{uid}', function (Request $request, $uid) {
    throw new \Exception('Ausnahmetest');
});
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
        $response = $handler($request);
        $exception = $response->exception();
        if ($exception) {
            echo $exception->getMessage();
        }
        return $response;
    }
}
```

## Global Middleware

> **Hinweis**
> Diese Funktion erfordert webman-framework >= 1.5.16

Globale Middleware des Hauptprojekts wirken sich nur auf das Hauptprojekt aus und haben keine Auswirkungen auf [Plugin-Anwendungen](app/app.md). Manchmal möchten wir jedoch ein Middleware hinzufügen, das alle Anwendungen, einschließlich aller Plugins, beeinflusst. Dafür können wir das Global Middleware verwenden.

Konfiguration in `config/middleware.php`:
```php
return [
    '@' => [ // Fügt dem Hauptprojekt und allen Plugins ein globales Middleware hinzu
        app\middleware\MiddlewareGlobl::class,
    ], 
    '' => [], // Fügt nur dem Hauptprojekt ein globales Middleware hinzu
];
```

> **Hinweis**
> Das `@`-Globale Middleware kann nicht nur in der Konfiguration des Hauptprojekts verwendet werden, sondern auch in der Konfiguration eines Plugins. Wenn z.B. das `@`-Globale Middleware in der Konfiguration von `plugin/ai/config/middleware.php` verwendet wird, betrifft es auch das Hauptprojekt und alle Plugins.

## Middleware für ein bestimmtes Plugin hinzufügen

> **Hinweis**
> Diese Funktion erfordert webman-framework >= 1.5.16

Manchmal möchten wir einem [Plugin-Anwendung](app/app.md) ein Middleware hinzufügen, ohne den Code des Plugins zu ändern (da Änderungen überschrieben werden könnten). In diesem Fall können wir in unserem Hauptprojekt ein Middleware für das Plugin konfigurieren.

Konfiguration in `config/middleware.php`:
```php
return [
    'plugin.ai' => [], // Fügt dem AI-Plugin ein Middleware hinzu
    'plugin.ai.admin' => [], // Fügt dem Admin-Modul des AI-Plugins ein Middleware hinzu
];
```

> **Hinweis**
> Natürlich können Sie auch eine ähnliche Konfiguration in einem beliebigen Plugin hinzufügen, um andere Plugins zu beeinflussen. Wenn z.B. die Konfiguration `plugin/foo/config/middleware.php` eine solche Konfiguration enthält, beeinflusst sie auch das AI-Plugin.
