# Ciclo di vita

## Ciclo di vita del processo
- Ogni processo ha un ciclo di vita molto lungo.
- Ogni processo funziona in modo indipendente senza interferire l'uno con l'altro.
- Ogni processo può gestire più richieste durante il suo ciclo di vita.
- Quando un processo riceve i comandi `stop`, `reload` o `restart`, termina e pone fine al ciclo di vita corrente.

> **Nota**
> Ogni processo è indipendente e non interferisce con gli altri, il che significa che ogni processo mantiene le proprie risorse, variabili, istanze di classe, ecc. In altre parole, ogni processo ha la propria connessione al database e inizializza le istanze di singoletto ogni volta che un processo inizia. Di conseguenza, multipli processi comportano inizializzazioni multiple.

## Ciclo di vita della richiesta
- Ogni richiesta produce un oggetto `$request`.
- L'oggetto `$request` viene eliminato dopo il completamento dell'elaborazione della richiesta.

## Ciclo di vita del controller
- Ogni controller viene istanziato solo una volta per ogni processo, ma viene istanziato più volte per processi multipli (ad eccezione della disattivazione del riutilizzo del controller, consultare [Ciclo di vita del controller](https://www.workerman.net/doc/webman/controller.html#%E7%94%9F%E5%91%BD%E5%91%A8%E6%9C%9F)).
- Le istanze del controller sono condivise tra le varie richieste all'interno del processo corrente (ad eccezione della disattivazione del riutilizzo del controller).
- Il ciclo di vita del controller termina dopo l'uscita dal processo corrente (ad eccezione della disattivazione del riutilizzo del controller).

## Circa il ciclo di vita della variabile
Poiché webman è basato su PHP, rispetta completamente il meccanismo di riciclo delle variabili di PHP. Le variabili temporanee create nella logica aziendale, inclusa l'istanza di classe creata con la parola chiave `new`, vengono eliminate automaticamente alla fine della funzione o del metodo senza la necessità di un'eliminazione manuale con `unset`. In altre parole, lo sviluppo con webman è essenzialmente simile allo sviluppo con i framework tradizionali. Ad esempio, l'istanza `$foo` nel seguente esempio viene rilasciata automaticamente alla fine dell'esecuzione del metodo `index`:
```php
<?php

namespace app\controller;

use app\service\Foo;
use support\Request;

class IndexController
{
    public function index(Request $request)
    {
        $foo = new Foo(); // Si ipotizzi l'esistenza di una classe Foo
        return response($foo->sayHello());
    }
}
```
Se si desidera riutilizzare un'istanza di una classe, è possibile salvarla come proprietà statica della classe o come proprietà dell'oggetto a lunga durata (come un controller), oppure è possibile inizializzare l'istanza della classe utilizzando il metodo `get` del contenitore. Ad esempio:
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
Il metodo `Container::get()` viene utilizzato per creare e memorizzare l'istanza della classe, in modo che quando viene chiamato nuovamente con gli stessi argomenti, restituirà l'istanza creata in precedenza.

> **Importante**
> `Container::get()` può inizializzare solo istanze senza parametri costruttore. `Container::make()` può creare istanze con parametri costruttore, ma a differenza di `Container::get()`, non riutilizza l'istanza. In altre parole, anche se chiamato con gli stessi argomenti, `Container::make()` restituirà sempre una nuova istanza.

## Sull'overflow di memoria
Nella stragrande maggioranza dei casi, il nostro codice aziendale non causerà overflow di memoria (è estremamente raro che gli utenti segnalino overflow di memoria). Basta fare attenzione a non far espandere all'infinito i dati dell'array a lunga durata. Ad esempio:
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
        return response('hello index');
    }

    public function hello(Request $request)
    {
        return response('hello webman');
    }
}
```
I controller hanno di default un lungo ciclo di vita (a meno che il riutilizzo del controller sia disattivato), allo stesso modo, la proprietà dell'array `$data` del controller ha un lungo ciclo di vita. Man mano che le richieste verso `foo/index` aumentano, gli elementi dell'array `$data` aumentano di conseguenza, portando all'overflow di memoria.

Per ulteriori informazioni, consultare [Overflow di memoria](./memory-leak.md)
