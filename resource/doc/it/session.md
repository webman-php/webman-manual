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
        $name = $request->get('name');
        $session = $request->session();
        $session->set('name', $name);
        return response('ciao ' . $session->get('name'));
    }
}
```

Ottenere un'istanza di `Workerman\Protocols\Http\Session` tramite `$request->session();` e utilizzare i suoi metodi per aggiungere, modificare, eliminare i dati della sessione.

> Nota: Quando l'oggetto sessione viene distrutto, i dati della sessione sono salvati automaticamente, quindi non salvare l'oggetto restituito da `$request->session()` in un array globale o in un membro della classe, altrimenti la sessione non verrà salvata.

## Ottenere tutti i dati della sessione
```php
$session = $request->session();
$all = $session->all();
```
Ritorna un array. Se non ci sono dati di sessione, verrà restituito un array vuoto.

## Ottenere un valore dalla sessione
```php
$session = $request->session();
$name = $session->get('name');
```
Se i dati non esistono, verrà restituito null.

È anche possibile passare un valore predefinito al metodo get come secondo argomento. Se l'elemento corrispondente non viene trovato nell'array di sessione, verrà restituito il valore predefinito. Ad esempio:
```php
$session = $request->session();
$name = $session->get('name', 'tom');
```

## Salvare in sessione
Per salvare un dato specifico, utilizzare il metodo set.
```php
$session = $request->session();
$session->set('name', 'tom');
```
Il metodo set non restituisce alcun valore; i dati della sessione verranno salvati automaticamente quando l'oggetto sessione viene distrutto.

Quando si desidera salvare più valori, utilizzare il metodo put.
```php
$session = $request->session();
$session->put(['name' => 'tom', 'age' => 12]);
```
Anche in questo caso, il metodo put non restituisce alcun valore.

## Eliminare i dati della sessione
Per eliminare uno o più dati della sessione, utilizzare il metodo `forget`.
```php
$session = $request->session();
// Eliminare un elemento
$session->forget('name');
// Eliminare più elementi
$session->forget(['name', 'age']);
```

Inoltre è disponibile il metodo delete, che, a differenza del metodo forget, può eliminare solo un elemento.
```php
$session = $request->session();
// Equivale a $session->forget('name');
$session->delete('name');
```

## Ottenere e eliminare un valore dalla sessione
```php
$session = $request->session();
$name = $session->pull('name');
```
L'effetto è lo stesso del seguente codice
```php
$session = $request->session();
$value = $session->get($name);
$session->delete($name);
```
Se la sessione corrispondente non esiste, verrà restituito null.

## Eliminare tutti i dati della sessione
```php
$request->session()->flush();
```
Il metodo non restituisce alcun valore; i dati della sessione verranno eliminati automaticamente quando l'oggetto sessione viene distrutto.

## Verificare l'esistenza di un dato nella sessione
```php
$session = $request->session();
$has = $session->has('name');
```
Il metodo restituirà false se la sessione corrispondente non esiste o se il valore della sessione è null; in caso contrario, restituirà true.

```
$session = $request->session();
$has = $session->exists('name');
```
Questo codice serve a verificare l'esistenza dei dati della sessione. La differenza è che quando il valore dell'elemento di sessione è null, restituirà comunque true.

## Funzione di supporto session()
> 2020-12-09 Nuovo

Webman fornisce la funzione di supporto `session()` per ottenere lo stesso comportamento.
```php
// Ottenere un'istanza di sessione
$session = session();
// Equivale a
$session = $request->session();

// Ottenere un valore specifico
$value = session('key', 'default');
// Equivale a
$value = session()->get('key', 'default');
// Equivale a
$value = $request->session()->get('key', 'default');

// Salvare in sessione
session(['key1'=>'value1', 'key2' => 'value2']);
// Equivale a
session()->put(['key1'=>'value1', 'key2' => 'value2']);
// Equivale a
$request->session()->put(['key1'=>'value1', 'key2' => 'value2']);

```

## File di configurazione
Il file di configurazione della sessione si trova in `config/session.php` ed è simile al seguente:
```php
use Webman\Session\FileSessionHandler;
use Webman\Session\RedisSessionHandler;
use Webman\Session\RedisClusterSessionHandler;

return [
    'handler' => FileSessionHandler::class O RedisSessionHandler::class O RedisClusterSessionHandler::class,
    
    'type'    => 'file' O 'redis' O 'redis_cluster',
    'config' => [
        'file' => [
            'save_path' => runtime_path() . '/sessions',
        ],
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

    'session_name' => 'PHPSID',
    
    'auto_update_timestamp' => false,
    'lifetime' => 7*24*60*60,
    'cookie_lifetime' => 365*24*60*60,
    'cookie_path' => '/',
    'domain' => '',
    'http_only' => true,
    'secure' => false,
    'same_site' => '' O 'strict' O 'lax' O 'none',
    'gc_probability' => [1, 1000],
];
```

> **Nota**
> A partire da webman 1.4.0, il namespace di SessionHandler è cambiato da:
> use Webman\FileSessionHandler;  
> use Webman\RedisSessionHandler;  
> use Webman\RedisClusterSessionHandler;  
> a  
> use Webman\Session\FileSessionHandler;  
> use Webman\Session\RedisSessionHandler;  
> use Webman\Session\RedisClusterSessionHandler;  


## Configurazione della durata
Quando webman-framework < 1.3.14, per impostare la durata della sessione in webman, è necessario configurare il valore in `php.ini`.

```
session.gc_maxlifetime = x
session.cookie_lifetime = x
session.gc_probability = 1
session.gc_divisor = 1000
```

Ad esempio, se si imposta la durata a 1440 secondi, è necessario configurare così:
```
session.gc_maxlifetime = 1440
session.cookie_lifetime = 1440
session.gc_probability = 1
session.gc_divisor = 1000
```

> **Nota**
> È possibile utilizzare il comando `php --ini` per cercare la posizione del file `php.ini`.
