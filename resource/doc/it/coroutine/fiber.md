# Coroutines

> **Coroutines Requirements**
> PHP>=8.1workerman>=5.0 webman-framework>=1.5 revolt/event-loop>1.0.0
> Upgrade webman command `composer require workerman/webman-framework ^1.5.0`
> Upgrade workerman command `composer require workerman/workerman ^5.0.0`
> Fiber coroutine requires installation of `composer require revolt/event-loop ^1.0.0`

# Esempio
### Risposta ritardata

```php
<?php

namespace app\controller;

use support\Request;
use Workerman\Timer;

class TestController
{
    public function index(Request $request)
    {
        // Dormi per 1,5 secondi
        Timer::sleep(1.5);
        return $request->getRemoteIp();
    }
}
```
`Timer::sleep()` è simile alla funzione`sleep()` nativa di PHP, la differenza è che `Timer::sleep()` non bloccherà il processo.

### Effettuare richieste HTTP

> **Nota**
> È necessario installare composer require workerman/http-client ^2.0.0

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
        $response = $client->get('http://example.com'); //Effettua una richiesta asincrona utilizzando un metodo sincrono
        return $response->getBody()->getContents();
    }
}
```
Anche la richiesta `$client->get('http://example.com')` è non bloccante, questo può essere utilizzato per effettuare richieste http non bloccanti in webman, migliorando le prestazioni dell'applicazione.

Per ulteriori informazioni, consulta [workerman/http-client](https://www.workerman.net/doc/workerman/components/workerman-http-client.html)

### Aggiunta della classe `support\Context`

La classe `support\Context` viene utilizzata per memorizzare i dati del contesto della richiesta; quando la richiesta è completata, i dati di contesto corrispondenti vengono eliminati automaticamente. In altre parole, il ciclo di vita dei dati del contesto segue il ciclo di vita della richiesta. `support\Context` supporta l'ambiente di esecuzione delle coroutine di Fiber, Swoole e Swow.

### Coroutine Swoole
Dopo aver installato l'estensione swoole (richiesta swoole>=5.0), abilita le coroutine di swoole attraverso la configurazione di config/server.php
```php
'event_loop' => \Workerman\Events\Swoole::class,
```
Per ulteriori informazioni, consulta [workerman evento guidato](https://www.workerman.net/doc/workerman/appendices/event.html)

### Inquinamento delle variabili globali

L'ambiente di esecuzione delle coroutine vieta il salvataggio delle informazioni dello stato **correlate alla richiesta** in variabili globali o variabili statiche, poiché ciò potrebbe causare inquinamento delle variabili globali, ad esempio

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

Impostando il numero di processi su 1, quando lanciamo due richieste consecutive  
http://127.0.0.1:8787/test?name=lilei  
http://127.0.0.1:8787/test?name=hanmeimei  
ci aspettiamo che i risultati restituiti dalle due richieste siano rispettivamente `lilei` e `hanmeimei`, ma in realtà entrambi restituiscono `hanmeimei`.
Questo è dovuto al fatto che la seconda richiesta sovrascrive la variabile statica `$name`, quindi quando la prima richiesta termina la pausa, la variabile statica `$name` è già diventata `hanmeimei`.

**Il metodo corretto è utilizzare il contesto per memorizzare l'informazione di stato della richiesta**
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

**Le variabili locali non causano inquinamento dei dati**
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
Poiché `$name` è una variabile locale, le coroutine non possono accedere alle variabili locali l'una dell'altra, quindi l'utilizzo delle variabili locali è sicuro per le coroutine.

# Su Coroutines
Le coroutine non sono una soluzione universale; l'introduzione delle coroutine implica la necessità di prestare attenzione all'inquinamento delle variabili globali/variabili statiche e di impostare il contesto. Inoltre, il debug dei bug nell'ambiente di esecuzione delle coroutine è più complicato rispetto alla programmazione bloccante.

La programmazione bloccante in webman è già abbastanza veloce; i dati di test delle ultime tre serie di test di benchmarks su [techempower.com](https://www.techempower.com/benchmarks/#section=data-r21&l=zijnjz-6bj&test=db&f=1ekg-cbcw-2t4w-27wr68-pc0-iv9slc-0-1ekgw-39g-kxs00-o0zk-4fu13d-2x8do8-2) mostrano che webman con operazioni su database supera prestazioni di framework web come Gin e Echo di Go di quasi il 100%, e supera le prestazioni del traditionale framework Laravel di quasi 40 volte.

![](../../assets/img/benchemarks-go-sw.png?)

Quando i database, Redis, etc. sono tutti all'interno della rete, la programmazione bloccante con più processi può spesso avere prestazioni migliori rispetto alle coroutine, poiché quando i database, Redis, etc. sono sufficientemente veloci, i costi di creazione, scheduling e distruzione delle coroutine potrebbero essere superiori al costo di commutazione dei processi. Pertanto, l'introduzione delle coroutine in tal caso potrebbe non migliorare significativamente le prestazioni.

# Quando usare le coroutine
Quando è presente un accesso lento nell'attività, ad esempio quando è necessario accedere a un'API di terze parti, è possibile utilizzare [workerman/http-client](https://www.workerman.net/doc/workerman/components/workerman-http-client.html) per avviare chiamate HTTP asincrone tramite coroutine, aumentando così la capacità di concorrenza dell'applicazione.
