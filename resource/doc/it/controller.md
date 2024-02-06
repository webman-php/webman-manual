# Controller

Crea un nuovo file controller `app/controller/FooController.php`.

```php
<?php
namespace app\controller;

use support\Request;

class FooController
{
    public function index(Request $request)
    {
        return response('ciao index');
    }
    
    public function hello(Request $request)
    {
        return response('ciao webman');
    }
}
```

Quando si accede a `http://127.0.0.1:8787/foo`, la pagina restituirà `ciao index`.

Quando si accede a `http://127.0.0.1:8787/foo/hello`, la pagina restituirà `ciao webman`.

Naturalmente puoi modificare le regole del percorso attraverso la configurazione del router, vedi [Routing](route.md).

> **Suggerimento**
> Se si verificano errori 404, aprire `config/app.php`, impostare `controller_suffix` su `Controller` e riavviare.

## Suffisso dei Controller
A partire dalla versione 1.3, webman supporta la configurazione del suffisso del controller in `config/app.php`. Se in `config/app.php` il valore di `controller_suffix` è vuoto `''`, i controller saranno simili a quanto segue

`app\controller\Foo.php`.

```php
<?php
namespace app\controller;

use support\Request;

class Foo
{
    public function index(Request $request)
    {
        return response('ciao index');
    }
    
    public function hello(Request $request)
    {
        return response('ciao webman');
    }
}
```

Si consiglia vivamente di impostare il suffisso del controller su `Controller`, in modo da evitare conflitti di nomi tra controller e modelli e aumentare la sicurezza.

## Spiegazioni
 - Il framework automaticamente trasmette all'istanza del controller l'oggetto `support\Request`, attraverso il quale è possibile ottenere i dati inseriti dall'utente (dati get, post, header, cookie, ecc), vedere [Richiesta](request.md)
 - Nel controller è possibile restituire numeri, stringhe o oggetti `support\Response`, ma non è possibile restituire altri tipi di dati.
 - L'oggetto `support\Response` può essere creato attraverso i metodi di utilità `response()`, `json()`, `xml()`, `jsonp()`, `redirect()` e altri.

## Ciclo di vita del Controller

Quando in `config/app.php` il valore di `controller_reuse` è `false`, sarà inizializzata un'istanza del controller corrispondente ad ogni richiesta e, al termine della richiesta, l'istanza del controller verrà distrutta, seguendo il meccanismo di esecuzione tradizionale dei framework.

Quando in `config/app.php` il valore di `controller_reuse` è `true`, tutte le richieste utilizzeranno la stessa istanza del controller, ovvero l'istanza del controller sarà persistente in memoria per tutta la durata del suo utilizzo, e verrà riutilizzata per tutte le richieste.

> **Nota**
> La disattivazione del riutilizzo del controller richiede webman>=1.4.0, quindi prima della versione 1.4.0, il controller è stato utilizzato per tutte le richieste, senza possibilità di modifica.

> **Nota**
> Quando il riutilizzo del controller è attivo, le richieste non dovrebbero modificare alcuna proprietà del controller, poiché tali modifiche influirebbero sulle richieste successive, ad esempio

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
        // Questo metodo conserverà il modello dopo la prima richiesta update?id=1
        // Se si richiede nuovamente delete?id=2, verrà eliminato il dato 1
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
        // Restituire dati nel costruttore non avrà alcun effetto e il browser non riceverà risposta
        return response('ciao'); 
    }
}
```

## Differenze tra il riutilizzo e la non riutilizzo del controller

Le differenze sono le seguenti

#### Controller non riutilizzabile
Verrà creata una nuova istanza del controller per ogni richiesta, che verrà distrutta alla fine della richiesta e la memoria liberata. Il comportamento è simile a quello tradizionale dei framework e corrisponde alle abitudini della maggior parte degli sviluppatori. Poiché il controller viene ripetutamente creato e distrutto, le prestazioni saranno leggermente inferiori rispetto all'utilizzo del controller (prestazioni inferiori di circa il 10% per un semplice test di prestazioni "helloworld", mentre con un carico di lavoro reale questo diventa trascurabile).

#### Controller riutilizzabile
In questo caso, un processo creerà una sola istanza del controller, che non verrà rilasciata alla fine della richiesta e verrà riutilizzata dalle richieste successive nello stesso processo. L'utilizzo del controller comporta migliori prestazioni, ma non corrisponde alle abitudini della maggior parte degli sviluppatori.

#### Casistiche in cui non è possibile utilizzare il riutilizzo del controller

Quando la richiesta può modificare le proprietà del controller, non è possibile abilitare il riutilizzo del controller, poiché le modifiche di tali proprietà influirebbero sulle richieste successive.

Alcuni sviluppatori amano inizializzare alcune operazioni per ogni richiesta nel costruttore del controller `__construct()`, in questo caso non è possibile riutilizzare il controller, poiché il costruttore del processo verrà chiamato una sola volta e non per ogni richiesta.
