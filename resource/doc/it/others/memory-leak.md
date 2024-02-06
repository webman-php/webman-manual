# Riguardo alle perdite di memoria
webman è un framework di memoria permanente, quindi è necessario prestare un po' di attenzione alle perdite di memoria. Tuttavia, gli sviluppatori non devono preoccuparsi troppo, perché le perdite di memoria si verificano in condizioni estreme e sono facilmente evitabili. Lo sviluppo con webman offre un'esperienza simile allo sviluppo con framework tradizionali e non richiede operazioni aggiuntive per la gestione della memoria.

> **Suggerimento**
> Il processo di monitoraggio incluso in webman monitorerà l'uso della memoria di tutti i processi. Se l'uso della memoria di un processo sta per raggiungere il valore impostato in `memory_limit` di php.ini, il processo verrà riavviato in modo sicuro, con l'obiettivo di rilasciare memoria senza influire sulle attività commerciali.

## Definizione delle perdite di memoria
Con l'aumentare delle richieste, la memoria utilizzata da webman **aumenta illimitatamente** (nota: **aumenta illimitatamente**), raggiungendo diverse centinaia di megabyte o addirittura di più. Questo è ciò che si intende per perdita di memoria.
Ricordiamo che non si tratta di perdita di memoria se c'è un aumento della memoria seguito da una stabilizzazione.

In generale, è normale che un processo utilizzi diverse decine di megabyte di memoria. Quando un processo gestisce richieste molto grandi o mantiene una grande quantità di connessioni, l'utilizzo della memoria da parte del singolo processo potrebbe raggiungere diverse centinaia di megabyte. In tal caso, potrebbe accadere che php non liberi completamente la memoria utilizzata, ma la mantenga per un riutilizzo futuro. Quindi potrebbe verificarsi un aumento dell'uso della memoria dopo il trattamento di una grande richiesta, senza rilasciare la memoria, e questo è un comportamento normale. (Chiamando il metodo gc_mem_caches() è possibile rilasciare una parte della memoria inutilizzata)

## Come si verificano le perdite di memoria
Le perdite di memoria si verificano solo se si verificano contemporaneamente le seguenti due condizioni:
1. Esistono degli array **a lunga durata** (nota: array a lunga durata, non array normali)
2. Questi array **a lunga durata** si espandono all'infinito (il business inserisce dati senza mai pulire i dati)

Se entrambe le condizioni 1 e 2 si verificano **contemporaneamente** (nota: contemporaneamente), si verificherà una perdita di memoria. Altrimenti, se non si verificano una di queste condizioni o entrambe, non si verificherà alcuna perdita di memoria.

## Array a lunga durata
Gli array a lunga durata in webman includono:
1. Array con la parola chiave static
2. Array attributi delle istanze singleton
3. Array con la parola chiave global

> **Nota**
> In webman è consentito l'utilizzo di dati a lunga durata, ma è necessario assicurarsi che i dati all'interno di tali strutture siano limitati, ovvero che il numero degli elementi non si espanda all'infinito.

Di seguito sono forniti diversi esempi per chiarire questo concetto.

#### Array statico in espansione infinita
```php
class Foo
{
    public static $data = [];
    public function index(Request $request)
    {
        self::$data[] = time();
        return response('hello');
    }
}
```

L'array `$data` definito con la parola chiave `static` è un array a lunga durata e, nell'esempio, l'array `$data` si espande illimitatamente con le richieste, provocando una perdita di memoria.

#### Array attributo singleton in espansione infinita
```php
class Cache
{
    protected static $instance;
    public $data = [];
    
    public function instance()
    {
        if (!self::$instance) {
            self::$instance = new self;
        }
        return self::$instance;
    }
    
    public function set($key, $value)
    {
        $this->data[$key] = $value;
    }
}
```

Codice di chiamata
```php
class Foo
{
    public function index(Request $request)
    {
        Cache::instance()->set(time(), time());
        return response('hello');
    }
}
```

`Cache::instance()` restituisce un'istanza singleton di Cache, che è una struttura a lunga durata. Anche se il suo attributo `$data` non utilizza la parola chiave `static`, poiché la classe stessa è a lunga durata, `$data` è considerato un array a lunga durata. Con l'aggiunta continua di dati con chiavi diverse all'array `$data`, l'uso della memoria del programma cresce sempre di più, causando una perdita di memoria.

> **Nota**
> Se le chiavi aggiunte con Cache::instance()->set(key, value) sono di numero limitato, non si verificherà alcuna perdita di memoria, poiché l'array `$data` non si espande all'infinito.

#### Array global in espansione infinita
```php
class Index
{
    public function index(Request $request)
    {
        global $data;
        $data[] = time();
        return response($foo->sayHello());
    }
}
```
L'array definito con la parola chiave global non viene liberato dopo l'esecuzione della funzione o del metodo della classe, pertanto è un array a lunga durata. Il codice sopra indicato, con l'aggiunta continua di dati, provoca una perdita di memoria con l'aumentare delle richieste. Allo stesso modo, un array definito all'interno di una funzione o di un metodo con la parola chiave static è considerato un array a lunga durata, e, se l'array si espande all'infinito, si verificherà una perdita di memoria, ad esempio:
```php
class Index
{
    public function index(Request $request)
    {
        static $data = [];
        $data[] = time();
        return response($foo->sayHello());
    }
}
```

## Consigli
Si consiglia agli sviluppatori di non concentrarsi eccessivamente sulle perdite di memoria, poiché si verificano raramente. Se, sfortunatamente, si verificano, è possibile individuare il punto in cui si verifica la perdita eseguendo dei test di stress e quindi risolvere il problema. Anche nel caso in cui gli sviluppatori non riescano a individuare il punto di perdita, il servizio di monitoraggio incluso in webman riavvierà in modo sicuro i processi che perdono memoria, liberandola.

Se si desidera evitare il più possibile le perdite di memoria, è possibile fare riferimento ai seguenti consigli:
1. Evitare l'uso delle parole chiave `global` e `static` per gli array, e nel caso in cui siano utilizzate, assicurarsi che non si espandano all'infinito.
2. Evitare l'uso di singleton per le classi sconosciute e preferire l'inizializzazione con la parola chiave `new`. Nel caso in cui sia necessario un singleton, verificare se ha degli attributi array che si espandono all'infinito.
