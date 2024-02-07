# Riguardo alle perdite di memoria
webman è un framework in memoria persistente, quindi è necessario prestare un po' di attenzione alle perdite di memoria. Tuttavia, gli sviluppatori non devono preoccuparsi troppo, poiché le perdite di memoria avvengono in condizioni estreme e sono facilmente evitabili. Lo sviluppo con webman è praticamente simile allo sviluppo con framework tradizionali e non richiede operazioni di gestione della memoria inutili.

> **Nota**
> Il processo di monitoraggio incluso in webman controllerà l'utilizzo della memoria di tutti i processi. Se l'utilizzo della memoria di un processo sta per raggiungere il valore impostato dalla direttiva `memory_limit` nel file php.ini, il processo verrà riavviato in modo sicuro per liberare memoria. Questa operazione non ha alcun impatto sul business.

## Definizione di perdita di memoria
Con l'aumento continuo delle richieste, la memoria utilizzata da webman **cresce illimitatamente** (attenzione, cresce **illimitatamente**) fino a raggiungere centinaia di megabyte o addirittura di più, questo è considerato una perdita di memoria. Se la memoria continua a crescere e poi smette di farlo, non si tratta di una perdita di memoria.

In generale, è normale che un processo utilizzi decine di megabyte di memoria. Quando un processo gestisce richieste molto grandi o mantiene un gran numero di connessioni, l'occupazione di memoria in un singolo processo può raggiungere anche diverse centinaia di megabyte. In questo caso, potrebbe non essere restituita l'intera memoria al sistema operativo dopo l'uso da parte di PHP. Alcune parti della memoria potrebbero non essere liberate dopo l'elaborazione di una grande richiesta, è una situazione normale. (È possibile liberare parte della memoria inattiva chiamando il metodo `gc_mem_caches()`)

## Come si verificano le perdite di memoria
**Perché avvengono le perdite di memoria? Le perdite di memoria avvengono solo se si verificano entrambe le seguenti condizioni:**
1. Esiste un array con **lunga durata** (attenzione, lunga durata, non è valido per gli array normali)
2. Questo array con **lunga durata** si espande all'infinito (il business ci inserisce dati senza mai pulirli)

Se le condizioni 1 e 2 sono entrambe valide **allo stesso tempo** (attenzione, nello stesso momento), allora si verificherà una perdita di memoria. Altrimenti, se non vengono soddisfatte entrambe le condizioni o se vengono soddisfatte solo una delle due, non si avrà una perdita di memoria.

## Array con lunga durata
Gli array con lunga durata in webman includono:
1. Array con la parola chiave `static`
2. Proprietà array singleton
3. Array con la parola chiave `global`

> **Attenzione**
> In webman è consentito l'uso di dati a lunga durata, ma è necessario garantire che i dati all'interno siano limitati e che il numero di elementi non cresca all'infinito.

Di seguito sono riportati esempi illustrati:

#### Array `static` che si espande all'infinito
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

L'array `$data` definito con la parola chiave `static` è un array con lunga durata. Nel caso di esempio, l'array `$data` si espande continuamente con le richieste, causando perdite di memoria.

#### Proprietà array singleton che si espande all'infinito
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

`Cache::instance()` restituisce un singleton Cache, che ha una lunga durata come istanza di classe. Anche se la proprietà `$data` non utilizza la parola chiave `static`, poiché la classe stessa ha una lunga durata, anche `$data` diventa un array con lunga durata. Con l'aggiunta continua di dati con chiavi diverse all'array `$data`, il consumo di memoria del programma aumenta sempre di più, causando perdite di memoria.

> **Attenzione**
> Se la chiave passata a `Cache::instance()->set(key, value)` è di quantità limitata, non si verificheranno perdite di memoria, poiché l'array `$data` non si espanderà all'infinito.

#### Array `global` che si espande all'infinito
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

Un array definito con la parola chiave `global` non verrà liberato dopo l'esecuzione di una funzione o di un metodo di una classe, quindi è un array con lunga durata. Con il codice precedente, con l'aumento delle richieste, si verificheranno perdite di memoria. Allo stesso modo, un array definito all'interno di una funzione o di un metodo con la parola chiave `static` è un array con lunga durata, che causerà perdite di memoria se si espande all'infinito, ad esempio:
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

## Suggerimenti
Si consiglia agli sviluppatori di non concentrarsi in modo eccessivo sulle perdite di memoria, poiché sono rare. Se sfortunatamente si verificano, è possibile individuare il punto in cui si verificano le perdite di memoria tramite test di carico, e quindi risolvere il problema. Anche se gli sviluppatori non trovano il punto di perdita, il servizio di monitoraggio incluso in webman riavvierà in modo sicuro i processi con perdite di memoria, liberando memoria al momento opportuno.

Se si desidera evitare il più possibile le perdite di memoria, si possono seguire i seguenti suggerimenti:
1. Evitare l'uso delle parole chiave `global` e `static` per gli array. Se vengono utilizzate, assicurarsi che non si espandano all'infinito.
2. Evitare di utilizzare i singleton per le classi non conosciute, preferire l'inizializzazione con la parola chiave `new`. Se è necessario un singleton, verificare se ha proprietà array che si espandono all'infinito.
