# Ciclo di vita

## Ciclo di vita del processo
- Ogni processo ha un lungo ciclo di vita
- Ogni processo viene eseguito indipendentemente e non interferisce con gli altri
- Ogni processo può gestire più richieste durante il suo ciclo di vita
- Quando un processo riceve i comandi `stop`, `reload`, `restart`, eseguirà l'uscita e terminerà il ciclo di vita corrente

> **Nota**
> Ogni processo è indipendente e non interferisce con gli altri, il che significa che ogni processo mantiene le proprie risorse, variabili, istanze di classe, etc. Questo si traduce nel fatto che ogni processo ha la propria connessione al database e alcuni singleton vengono inizializzati ogni volta che un processo viene avviato, il che significa che più processi inizializzeranno i singleton più volte.

## Ciclo di vita della richiesta
- Ogni richiesta genera un oggetto `$request`
- L'oggetto `$request` viene deallocato dopo aver gestito la richiesta

## Ciclo di vita del controller
- Ogni controller viene istanziato solo una volta per processo, multiple istanze per processi multipli (ad eccezione della chiusura del controller di riutilizzo, vedi [Ciclo di vita del controller](https://www.workerman.net/doc/webman/controller.html#%E7%94%9F%E5%91%BD%E5%91%A8%E6%9C%9F))
- L'istanza del controller viene condivisa tra multiple richieste all'interno del processo corrente (ad eccezione della chiusura del controller di riutilizzo)
- Il ciclo di vita del controller termina quando il processo esce (ad eccezione della chiusura del controller di riutilizzo)

## Circa il ciclo di vita della variabile
webman è basato su PHP, quindi segue completamente il meccanismo di deallocazione delle variabili di PHP. Le variabili temporanee generate dalla logica aziendale, comprese le istanze di classe create con la parola chiave `new`, vengono deallocate automaticamente alla fine di una funzione o di un metodo senza la necessità di effettuare manualmente il `unset`. Questo significa che lo sviluppo con webman ha un'esperienza simile allo sviluppo con i framework tradizionali. Ad esempio, nell'esempio seguente, l'istanza `$foo` verrà deallocata automaticamente dopo l'esecuzione del metodo index:
```php
<?php

namespace app\controller;

use app\service\Foo;
use support\Request;

class IndexController
{
    public function index(Request $request)
    {
        $foo = new Foo(); // Supponiamo di avere una classe Foo
        return response($foo->sayHello());
    }
}
```
Se si desidera riutilizzare un'istanza di una certa classe, è possibile salvare l'istanza in una proprietà statica della classe o in una proprietà di un oggetto a lunga durata (come un controller), oppure è possibile utilizzare il metodo `get` del contenitore per inizializzare un'istanza della classe, come segue:
```php
<?php

namespace app\controller;

use app\service\Foo;
use support\Container;
use support\Request;

class IndexController
{
    public function index(Request $request)
    {
        $foo = Container::get(Foo::class);
        return response($foo->sayHello());
    }
}
```

Il metodo `Container::get()` viene utilizzato per creare e salvare un'istanza della classe e restituirla quando viene chiamato di nuovo con gli stessi parametri.

> **Nota**
> `Container::get()` può inizializzare solo istanze senza parametri costruttore. `Container::make()` può creare istanze con parametri costruttore, ma a differenza di `Container::get()`, `Container::make()` non riutilizza le istanze, il che significa che anche se viene chiamato più volte con gli stessi parametri, `Container::make()` restituirà sempre una nuova istanza.

# Riguardo alle perdite di memoria
Nella maggior parte dei casi, il nostro codice di business non causa perdite di memoria (molto pochi utenti segnalano perdite di memoria), basta prestare un po' di attenzione a non far espandere all'infinito i dati degli array a lunga durata. Si consideri il seguente codice:
```php
<?php
namespace app\controller;

use support\Request;

class FooController
{
    // Proprietà dell'array
    public $data = [];
    
    public function index(Request $request)
    {
        $this->data[] = time();
        return response('ciao index');
    }

    public function hello(Request $request)
    {
        return response('ciao webman');
    }
}
```
Di default, il controller ha una lunga durata (ad eccezione della chiusura del controller di riutilizzo), allo stesso modo la proprietà dell'array `$data` del controller ha una lunga durata. Con l'aumentare delle richieste per `foo/index`, gli elementi dell'array `$data` aumentano continuamente causando perdite di memoria.

Per ulteriori informazioni correlate, si prega di fare riferimento a [Perdite di memoria](./memory-leak.md)
