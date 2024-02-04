# Coroutine

> **Coroutine Requirements**
> PHP>=8.1workerman>=5.0 webman-framework>=1.5 revolt/event-loop>1.0.0
> To upgrade webman, use the following command: `composer require workerman/webman-framework ^1.5.0`
> To upgrade workerman, use the following command: `composer require workerman/workerman ^5.0.0`
> Fiber coroutine requires installation of `composer require revolt/event-loop ^1.0.0`

# Beispiel
### Verzögerte Antwort

```php
<?php

namespace app\controller;

use support\Request;
use Workerman\Timer;

class TestController
{
    public function index(Request $request)
    {
        // 1.5 Sekunden schlafen
        Timer::sleep(1.5);
        return $request->getRemoteIp();
    }
}
```
`Timer::sleep()` ähnelt der in PHP eingebauten Funktion `sleep()`, der Unterschied besteht darin, dass `Timer::sleep()` den Prozess nicht blockiert.

### HTTP-Anfrage starten

> **Beachte**
> Es muss `composer require workerman/http-client ^2.0.0` installiert sein.

```php
<?php

namespace app\controller;

use support\Request;
use Workerman\Http\Client;

class TestController
{
    public function index(Request $request)
    {
        static $client;
        $client = $client ?: new Client();
        $response = $client->get('http://example.com'); // Asynchrone Anforderung mit synchroner Methode starten
        return $response->getBody()->getContents();
    }
}
```
Auch die Anforderung `$client->get('http://example.com')` ist nicht blockierend, was in webman für nicht blockierende Ausführung von HTTP-Anforderungen genutzt werden kann, um die Leistung der Anwendung zu steigern.

Weitere Informationen findest du unter [workerman/http-client](https://www.workerman.net/doc/workerman/components/workerman-http-client.html)

### Hinzufügen der support\Context-Klasse

Die Klasse `support\Context` wird zur Speicherung von Kontextdaten der Anfrage verwendet. Sobald die Anfrage abgeschlossen ist, werden die entsprechenden Kontextdaten automatisch gelöscht. Das bedeutet, dass der Lebenszyklus der Kontextdaten dem Lebenszyklus der Anfrage folgt. `support\Context` unterstützt die Fiber-, Swoole- und Swow-Koroutinenumgebung.

### Swoole-Koroutine
Nach der Installation der Swoole-Erweiterung (mindestens Swoole-Version 5.0) kann die Swoole-Koroutine durch Konfiguration der Datei `config/server.php` aktiviert werden.
```php
'event_loop' => \Workerman\Events\Swoole::class,
```

Weitere Informationen findest du unter [workerman Event-Driven](https://www.workerman.net/doc/workerman/appendices/event.html)

### Verunreinigung von globalen Variablen

In einer Koroutine-Umgebung ist es untersagt, **anfragebezogene** Statusinformationen in globalen oder statischen Variablen zu speichern, da dies zu einer Verunreinigung von globalen Variablen führen kann, wie folgendes Beispiel zeigt:

```php
<?php

namespace app\controller;

use support\Request;
use Workerman\Timer;

class TestController
{
    protected static $name = '';

    public function index(Request $request)
    {
        static::$name = $request->get('name');
        Timer::sleep(5);
        return static::$name;
    }
}
```

Indem die Nummer der Prozesse auf 1 eingestellt wird, und wenn zwei aufeinanderfolgende Anfragen gesendet werden  
http://127.0.0.1:8787/test?name=lilei  
http://127.0.0.1:8787/test?name=hanmeimei  
Erwartet man, dass die beiden Anfragen die Ergebnisse `lilei` und `hanmeimei` zurückgeben, aber tatsächlich geben sie beide `hanmeimei` zurück. Das liegt daran, dass die zweite Anfrage die statische Variable `$name` überschrieben hat, und wenn die erste Anfrage nach dem Aufwachen zurückkehrt, ist die statische Variable `$name` bereits `hanmeimei`.

**Die richtige Methode ist es, Zustandsdaten der Anfrage im Context zu speichern**

```php
<?php

namespace app\controller;

use support\Request;
use support\Context;
use Workerman\Timer;

class TestController
{
    public function index(Request $request)
    {
        Context::set('name', $request->get('name'));
        Timer::sleep(5);
        return Context::get('name');
    }
}
```

**Lokale Variablen verursachen keine Datenverunreinigung**
```php
<?php

namespace app\controller;

use support\Request;
use support\Context;
use Workerman\Timer;

class TestController
{
    public function index(Request $request)
    {
        $name = $request->get('name');
        Timer::sleep(5);
        return $name;
    }
}
```
Da `$name` eine lokale Variable ist, können Koroutinen nicht auf lokale Variablen in anderen Koroutinen zugreifen, daher sind lokale Variablen koroutinen-sicher.

# Über Koroutinen
Koroutinen sind keine Allheilmittel. Die Einführung von Koroutinen erfordert Aufmerksamkeit auf das Problem der Verunreinigung von globalen/​statischen Variablen und erfordert die Einrichtung des Kontexts. Darüber hinaus ist das Debuggen von Fehlern in der koroutine Umgebung etwas komplexer als das blockierendes Programmieren.

In Wirklichkeit ist das blockierende Programmieren von Webman bereits schnell genug. Basierend auf den Benchmark-Daten von [techempower.com](https://www.techempower.com/benchmarks/#section=data-r21&l=zijnjz-6bj&test=db&f=1ekg-cbcw-2t4w-27wr68-pc0-iv9slc-0-1ekgw-39g-kxs00-o0zk-4fu13d-2x8do8-2) in den letzten drei Jahren sind Webman-Blockierungsgeschäfte bei der Datenbankleistung fast doppelt so schnell wie Golang-Web-Frameworks wie Gin und Echo und fast 40-mal schneller als traditionelle Frameworks wie Laravel.
![](../../assets/img/benchemarks-go-sw.png?)

Wenn sich die Datenbank und Redis im lokalen Netzwerk befinden, ist die Leistung des mehrprozessblockierenden Programmierens möglicherweise höher als die der Koroutinen, da die Kosten für das Erstellen, Planen und Beenden von Koroutinen möglicherweise höher sind als die Kosten für das Prozesswechseln, wenn die Datenbank, Redis usw. schnell genug sind. Daher führt die Einführung von Koroutinen in solchen Fällen nicht unbedingt zu signifikanten Leistungssteigerungen.

# Wann Koroutinen verwenden
Wenn es langsame Zugriffe im Geschäftsbetrieb gibt, z.B. wenn das Geschäft externe Schnittstellen aufrufen muss, kann die Leistungsfähigkeit der Anwendung durch asynchrone HTTP-Anfragen mit [workerman/http-client](https://www.workerman.net/doc/workerman/components/workerman-http-client.html) in einer Koroutine verbessert werden.
