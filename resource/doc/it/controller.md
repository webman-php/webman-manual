# Controller

Creare un nuovo file controller `app/controller/FooController.php`.

```php
<?php
namespace app\controller;

use support\Request;

class FooController
{
    public function index(Request $request)
    {
        return response('ciao indice');
    }
    
    public function hello(Request $request)
    {
        return response('ciao webman');
    }
}
```

Quando si accede a `http://127.0.0.1:8787/foo`, la pagina restituisce `ciao indice`.

Quando si accede a `http://127.0.0.1:8787/foo/hello`, la pagina restituisce `ciao webman`.

Naturalmente è possibile modificare le regole del percorso tramite la configurazione del percorso, consultare [Routing](route.md).

> **Suggerimento**
> Se si verificano errori 404, aprire `config/app.php`, impostare `controller_suffix` su `Controller` e riavviare.

## Suffisso del Controller
webman, a partire dalla versione 1.3, supporta l'impostazione del suffisso del controller in `config/app.php`. Se `controller_suffix` in `config/app.php` viene impostato su `''`, la classe del controller sarà simile a quanto segue

`app\controller\Foo.php`.

```php
<?php
namespace app\controller;

use support\Request;

class Foo
{
    public function index(Request $request)
    {
        return response('ciao indice');
    }
    
    public function hello(Request $request)
    {
        return response('ciao webman');
    }
}
```

Si consiglia vivamente di impostare il suffisso del controller su `Controller`, in modo da evitare conflitti di nomi tra il controller e la classe del modello, aumentando al contempo la sicurezza.

## Spiegazione
- Il framework passa automaticamente all'oggetto controller `support\Request`, attraverso il quale è possibile ottenere i dati di input dell'utente (come dati get, post, header, cookie, ecc.), consultare [Richiesta](request.md)
- Il controller può restituire numeri, stringhe o oggetti `support\Response`, ma non può restituire altri tipi di dati.
- l'oggetto `support\Response` può essere creato tramite funzioni di aiuto come `response()`, `json()`, `xml()`, `jsonp()` e `redirect()`.

## Ciclo di vita del Controller
Quando `config/app.php` ha `controller_reuse` impostato su `false`, ogni richiesta inizializzerà un'istanza del controller corrispondente, e al termine della richiesta l'istanza del controller sarà eliminata, seguendo il meccanismo di esecuzione tradizionale del framework.

Quando `config/app.php` ha `controller_reuse` impostato su `true`, tutte le richieste utilizzeranno la stessa istanza del controller, cioè l'istanza del controller verrà mantenuta in memoria una volta creata e verrà utilizzata per tutte le richieste.

> **Nota**
> Per disabilitare il riutilizzo del controller, webman deve essere >=1.4.0; prima della versione 1.4.0 il controller viene sempre riutilizzato per tutte le richieste e non può essere modificato.

> **Nota**
> Quando si abilita il riutilizzo del controller, le richieste non dovrebbero modificare alcuna proprietà del controller, poiché tali modifiche influirebbero sulle richieste successive, ad esempio

```php
<?php
namespace app\controller;

use support\Request;

class FooController
{
    protected $model;
    
    public function update(Request $request, $id)
    {
        $model = $this->getModel($id);
        $model->update();
        return response('ok');
    }
    
    public function delete(Request $request, $id)
    {
        $model = $this->getModel($id);
        $model->delete();
        return response('ok');
    }
    
    protected function getModel($id)
    {
        // Questo metodo manterrà il modello dopo la prima richiesta update?id=1
        // Se viene effettuata un'altra richiesta di delete?id=2, verrà eliminato il dato 1
        if (!$this->model) {
            $this->model = Model::find($id);
        }
        return $this->model;
    }
}
```

> **Suggerimento**
> Nel costruttore `__construct()` del controller, restituire dati non avrà alcun effetto, ad esempio

```php
<?php
namespace app\controller;

use support\Request;

class FooController
{
    public function __construct()
    {
        // Restituire dati nel costruttore non avrà alcun effetto, il browser non riceverà questa risposta
        return response('ciao'); 
    }
}
```

## Differenza tra riutilizzo e non riutilizzo del Controller
Le differenze sono le seguenti

#### Controller non riutilizzato
Ogni richiesta creerà una nuova istanza del controller, la quale verrà rilasciata al termine della richiesta e la memoria verrà liberata. Il non riutilizzo del controller è simile al funzionamento dei framework tradizionali ed è in linea con le abitudini della maggior parte degli sviluppatori. Poiché il controller viene creato e distrutto ripetutamente, le prestazioni saranno leggermente inferiori rispetto al riutilizzo del controller (le prestazioni in un test helloworld sono inferiori di circa il 10%, e con un carico di lavoro reale possono essere trascurabili).

#### Controller riutilizzato
Se si riutilizza un controller, ogni processo creerà una sola volta il controller e non libererà l'istanza del controller al termine della richiesta, ma la riutilizzerà per le richieste successive nel processo corrente. Il riutilizzo del controller migliora le prestazioni, ma non è in linea con le abitudini della maggior parte degli sviluppatori.

#### Situazioni in cui non è possibile utilizzare il riutilizzo del controller
Non è possibile abilitare il riutilizzo del controller quando le richieste possono modificare le proprietà del controller, in quanto tali modifiche influenzeranno le richieste successive.

Alcuni sviluppatori preferiscono eseguire inizializzazioni specifiche per ciascuna richiesta nel costruttore `__construct()` del controller, in questo caso il riutilizzo del controller non è possibile, poiché il costruttore verrà chiamato una sola volta per il processo corrente e non per ogni richiesta.
