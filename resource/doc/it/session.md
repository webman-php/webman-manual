# Gestione della sessione

## Esempio
```php
<?php
namespace app\controller;

use support\Request;

class UserController
{
    public function hello(Request $request)
    {
        $name = $request->get('nome');
        $session = $request->session();
        $session->set('nome', $name);
        return response('ciao ' . $session->get('nome'));
    }
}
```

Ottieni un'istanza di `Workerman\Protocols\Http\Session` tramite `$request->session();` e usa i suoi metodi per aggiungere, modificare, eliminare i dati della sessione.

> Nota: quando l'oggetto sessione viene distrutto, i dati della sessione vengono automaticamente salvati, quindi non salvare l'oggetto restituito da `$request->session()` in un'array globale o in un membro di classe, in quanto ciò potrebbe impedire il salvataggio della sessione.

## Ottenere tutti i dati della sessione
```php
$session = $request->session();
$all = $session->all();
```
Restituisce un array. Se non ci sono dati di sessione, restituisce un array vuoto.


## Ottenere un valore dalla sessione
```php
$session = $request->session();
$nome = $session->get('nome');
```
Se i dati non esistono, restituisce null.

È anche possibile passare un valore predefinito come secondo argomento al metodo get. Se l'elemento di sessione corrispondente non viene trovato, verrà restituito il valore predefinito. Ad esempio:
```php
$session = $request->session();
$nome = $session->get('nome', 'tom');
```


## Salvare una sessione
Utilizza il metodo `set` per salvare un dato.
```php
$session = $request->session();
$session->set('nome', 'tom');
```
Il metodo set non restituisce nulla, la sessione viene automaticamente salvata quando l'oggetto sessione viene distrutto.

Quando si salvano più valori, utilizza il metodo `put`.
```php
$session = $request->session();
$session->put(['nome' => 'tom', 'età' => 12]);
```
Anche in questo caso, il metodo put non restituisce nulla.

## Eliminare i dati della sessione
Utilizza il metodo `forget` per eliminare uno o più dati della sessione.
```php
$session = $request->session();
// Elimina un elemento
$session->forget('nome');
// Elimina più elementi
$session->forget(['nome', 'età']);
```

Inoltre, è disponibile il metodo `delete`, che differisce dal metodo `forget` in quanto può eliminare solo un elemento.
```php
$session = $request->session();
// Equivalente a $session->forget('nome');
$session->delete('nome');
```

## Ottenere e cancellare un valore dalla sessione
```php
$session = $request->session();
$nome = $session->pull('nome');
```
È equivalente al seguente codice:
```php
$session = $request->session();
$valore = $session->get($nome);
$session->delete($nome);
```
Se la sessione corrispondente non esiste, restituisce null.


## Eliminare tutti i dati della sessione
```php
$request->session()->flush();
```
Il metodo non restituisce nulla, i dati della sessione vengono automaticamente eliminati dallo storage quando l'oggetto sessione viene distrutto.

## Verificare se esistono dei dati nella sessione
```php
$session = $request->session();
$esiste = $session->has('nome');
```
Il metodo restituirà false se la sessione corrispondente non esiste o se il valore associato alla sessione è null, altrimenti restituirà true.

```php
$session = $request->session();
$esiste = $session->exists('nome');
```
Anche questo metodo serve per verificare l'esistenza dei dati nella sessione, ma se il valore associato all'elemento della sessione è null, restituirà true.

## Funzione helper `session()`
> Aggiunto il 09-12-2020

Webman fornisce la funzione helper `session()` che offre la stessa funzionalità.
```php
// Ottenere un'istanza della sessione
$session = session();
// Equivalente a
$session = $request->session();

// Ottenere un valore specifico
$valore = session('chiave', 'predefinito');
// Equivalente a
$valore = session()->get('chiave', 'predefinito');
// Equivalente a
$valore = $request->session()->get('chiave', 'predefinito');

// Assegnare un valore alla sessione
session(['chiave1'=>'valore1', 'chiave2' => 'valore2']);
// Equivalente a
session()->put(['chiave1'=>'valore1', 'chiave2' => 'valore2']);
// Equivalente a
$request->session()->put(['chiave1'=>'valore1', 'chiave2' => 'valore2']);
```

## File di configurazione
Il file di configurazione della sessione si trova in `config/session.php` e ha un contenuto simile al seguente:
```php
use Webman\Session\FileSessionHandler;
use Webman\Session\RedisSessionHandler;
use Webman\Session\RedisClusterSessionHandler;

return [
    // FileSessionHandler::class o RedisSessionHandler::class o RedisClusterSessionHandler::class 
    'handler' => FileSessionHandler::class,
    
    // Se handler è FileSessionHandler::class, il valore è file,
    // Se handler è RedisSessionHandler::class, il valore è redis,
    // Se handler è RedisClusterSessionHandler::class, il valore è redis_cluster, ovvero una cluster di Redis
    'type'    => 'file',

    // Configurazioni diverse per handler diversi
    'config' => [
        // Configurazioni per il tipo file
        'file' => [
            'save_path' => runtime_path() . '/sessions',
        ],
        // Configurazioni per il tipo redis
        'redis' => [
            'host'      => '127.0.0.1',
            'port'      => 6379,
            'auth'      => '',
            'timeout'   => 2,
            'database'  => '',
            'prefix'    => 'redis_session_',
        ],
        'redis_cluster' => [
            'host'    => ['127.0.0.1:7000', '127.0.0.1:7001', '127.0.0.1:7001'],
            'timeout' => 2,
            'auth'    => '',
            'prefix'  => 'redis_session_',
        ]
        
    ],

    'session_name' => 'PHPSID', // Nome del cookie per memorizzare l'ID della sessione
    
    // === Le seguenti configurazioni richiedono webman-framework>=1.3.14 workerman>=4.0.37 ===
    'auto_update_timestamp' => false,  // Se aggiornare automaticamente la sessione, predefinito a falso
    'lifetime' => 7*24*60*60,          // Tempo di scadenza della sessione
    'cookie_lifetime' => 365*24*60*60, // Tempo di scadenza del cookie per memorizzare l'ID della sessione
    'cookie_path' => '/',              // Percorso del cookie per l'ID della sessione
    'domain' => '',                    // Dominio del cookie per l'ID della sessione
    'http_only' => true,               // Abilitare solo HTTP, predefinito abilitato
    'secure' => false,                 // Abilitare la sessione solo in HTTPS, predefinito disabilitato
    'same_site' => '',                 // Prevenire attacchi CSRF e tracciamento dell'utente, valori opzionali: strict/lax/none
    'gc_probability' => [1, 1000],     // Probabilità di pulizia della sessione
];
```

> **Nota** 
> A partire da webman 1.4.0, il namespace di SessionHandler è cambiato da
> use Webman\FileSessionHandler;  
> use Webman\RedisSessionHandler;  
> use Webman\RedisClusterSessionHandler;
> a
> use Webman\Session\FileSessionHandler;  
> use Webman\Session\RedisSessionHandler;  
> use Webman\Session\RedisClusterSessionHandler;

## Configurazione della durata di validità
Quando webman-framework è inferiore alla versione 1.3.14, il tempo di validità della sessione in webman deve essere configurato in `php.ini`.

```php
session.gc_maxlifetime = x
session.cookie_lifetime = x
session.gc_probability = 1
session.gc_divisor = 1000
```

Supponendo che il tempo di validità sia di 1440 secondi, la configurazione sarebbe la seguente
```php
session.gc_maxlifetime = 1440
session.cookie_lifetime = 1440
session.gc_probability = 1
session.gc_divisor = 1000
```

> **Nota**
> Si può utilizzare il comando `php --ini` per trovare la posizione di `php.ini`
