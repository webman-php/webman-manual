# Coroutine

> **Coroutines Requirements**
> PHP>=8.1workerman>=5.0 webman-framework>=1.5 revolt/event-loop>1.0.0
> webman upgrade command `composer require workerman/webman-framework ^1.5.0`
> workerman upgrade command `composer require workerman/workerman ^5.0.0`
> Fiber coroutines require installation of `composer require revolt/event-loop ^1.0.0`

# Beispiele
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
        // Schlafe für 1,5 Sekunden
        Timer::sleep(1.5);
        return $request->getRemoteIp();
    }
}
```
`Timer::sleep()` ist ähnlich wie die eingebaute Funktion `sleep()` in PHP, der Unterschied besteht darin, dass `Timer::sleep()` den Prozess nicht blockiert.

### HTTP-Anforderung senden

> **Hinweis**
> Installiere `composer require workerman/http-client ^2.0.0`

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
        $response = $client->get('http://example.com'); // Synchron sendet eine asynchrone Anfrage 
        return $response->getBody()->getContents();
    }
}
```
Die Anfrage `$client->get('http://example.com')` ist ebenfalls nicht blockierend und kann in webman verwendet werden, um nicht blockierende HTTP-Anforderungen zu senden und so die Anwendungsleistung zu steigern.

Weitere Informationen finden Sie unter [workerman/http-client](https://www.workerman.net/doc/workerman/components/workerman-http-client.html)

### Hinzufügen der Klasse `support\Context`

Die Klasse `support\Context` wird verwendet, um die Kontextdaten der Anfrage zu speichern. Wenn die Anfrage abgeschlossen ist, werden die entsprechenden Kontextdaten automatisch gelöscht. Das bedeutet, dass die Lebensdauer der Kontextdaten der Anfrage entspricht. `support\Context` unterstützt die Fiber-, Swoole- und Swow-Koroutinenumgebungen.

### Swoole Coroutine
Nach der Installation der Swoole-Erweiterung (erfordert Swoole >= 5.0) können Swoole Coroutinen durch Konfigurieren von `config/server.php` aktiviert werden.
```php
'event_loop' => \Workerman\Events\Swoole::class,
```

Weitere Informationen finden Sie unter [workerman Event-Loop](https://www.workerman.net/doc/workerman/appendices/event.html)

### Globale Variablenverschmutzung

Es ist verboten, **anfragebezogene** Statusinformationen in globalen oder statischen Variablen zu speichern, da dies zu einer Verschmutzung globaler Variablen führen kann, zum Beispiel:

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
Durch Einstellen der Prozessnummer auf 1, wenn wir zwei aufeinander folgende Anfragen senden:
http://127.0.0.1:8787/test?name=lilei
http://127.0.0.1:8787/test?name=hanmeimei
erwarten wir, dass die Ergebnisse der beiden Anfragen jeweils `lilei` und `hanmeimei` sind, aber tatsächlich werden beide als `hanmeimei` zurückgegeben.
Das liegt daran, dass die zweite Anfrage die statische Variable `$name` überschreibt und wenn die erste Anfrage nach dem Schlafen zurückkehrt, ist die statische Variable `$name` bereits `hanmeimei`.

**Der richtige Weg ist jedoch, den Anfragestatus mit Context zu speichern:**
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

**Lokale Variablen verursachen keine Datenverschmutzung:**
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
Da `$name` eine lokale Variable ist, können Koroutinen nicht auf lokale Variablen anderer Koroutinen zugreifen, weshalb die Verwendung lokaler Variablen koroutinen-sicher ist.

# Über Koroutinen
Koroutinen sind keine universelle Lösung für alle Probleme, die Einführung von Koroutinen bedeutet, dass auf globale Variablen/”static variable pollution”-Probleme geachtet werden muss und der Kontext festgelegt werden muss. Darüber hinaus ist das Debuggen von Fehlern in der Koroutinenumgebung etwas komplizierter als das blockierende Programmieren.

Tatsächlich ist blockierendes Programmieren in webman bereits schnell genug, gemäß der Benchmark-Daten der letzten drei Jahre von [techempower.com](https://www.techempower.com/benchmarks/#section=data-r21&l=zijnjz-6bj&test=db&f=1ekg-cbcw-2t4w-27wr68-pc0-iv9slc-0-1ekgw-39g-kxs00-o0zk-4fu13d-2x8do8-2) ist webman blockierend für datenbankbezogene Geschäftsanforderungen fast doppelt so schnell wie das go-Web-Framework gin, echo und sogar fast 40-mal schneller als herkömmliche Frameworks wie Laravel.
![](../../assets/img/benchemarks-go-sw.png?)

Wenn die Datenbank, Redis usw. alle im internen Netzwerk sind, ist die Leistung von Mehrprozessen blockierendem Programmieren möglicherweise höher als die von Koroutinen, da die Overheads für die Erstellung, Planung und Zerstörung von Koroutinen im Vergleich zu Prozesswechseloverheads bei schnellen Datenbanken, Redis usw. möglicherweise größer sind. Daher führt die Einführung von Koroutinen in diesen Fällen möglicherweise nicht zu einer signifikanten Leistungssteigerung.

# Wann sind Koroutinen zu verwenden
Wenn in der Anwendung langsame Zugriffe wie z.B. auf externe Schnittstellen vorkommen, kann die asynchrone Absendung von HTTP-Anfragen mit [workerman/http-client](https://www.workerman.net/doc/workerman/components/workerman-http-client.html) in Koroutinenumgebung verwendet werden, um die parallele Leistungsfähigkeit der Anwendung zu verbessern.
