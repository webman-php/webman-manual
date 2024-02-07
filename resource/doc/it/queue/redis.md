## Coda Redis

Coda Redis è una coda dei messaggi basata su Redis che supporta il ritardo nell'elaborazione dei messaggi.

## Installazione
`composer require webman/redis-queue`

## File di configurazione
Il file di configurazione di Redis viene generato automaticamente in `config/plugin/webman/redis-queue/redis.php`, il cui contenuto è simile al seguente:

```php
<?php
return [
    'default' => [
        'host' => 'redis://127.0.0.1:6379',
        'options' => [
            'auth' => '',         // Password, parametro opzionale
            'db' => 0,            // Database
            'max_attempts'  => 5, // Tentativi di consumo dopo un fallimento
            'retry_seconds' => 5, // Intervallo di ritentativo, in secondi
        ]
    ],
];
```

### Ritentativi di consumo in caso di fallimento
Se si verifica un errore durante il consumo (ad esempio un'eccezione), il messaggio viene inserito in una coda di ritardo e sarà elaborato nuovamente in seguito. Il numero di ritentativi è controllato dal parametro `max_attempts`, mentre l'intervallo di ritentativo è controllato da `retry_seconds` insieme a `max_attempts`. Ad esempio, se `max_attempts` è 5 e `retry_seconds` è 10, il primo ritentativo avverrà dopo `1*10` secondi, il secondo dopo `2*10` secondi e così via fino a un massimo di 5 tentativi. Se il numero di ritentativi supera quello impostato in `max_attempts`, il messaggio viene inserito nella coda dei fallimenti con chiave `{redis-queue}-failed`.

## Invio di messaggi (sincrono)
> **Nota:**
> È necessario installare webman/redis >= 1.2.0 e dipendenze dall'estensione redis.

```php
<?php
namespace app\controller;

use support\Request;
use Webman\RedisQueue\Redis;

class Index
{
    public function queue(Request $request)
    {
        // Nome della coda
        $queue = 'send-mail';
        // Dati, è possibile passare direttamente un array senza la necessità di serializzarlo
        $data = ['to' => 'tom@gmail.com', 'content' => 'hello'];
        // Invio del messaggio
        Redis::send($queue, $data);
        // Invio di un messaggio con ritardo, che verrà elaborato dopo 60 secondi
        Redis::send($queue, $data, 60);

        return response('test della coda Redis');
    }
}
```
Il successo dell'invio con `Redis::send()` restituisce true, altrimenti restituirà false o genererà un'eccezione.

> **Suggerimento:**
> Il tempo di consumo della coda di ritardo potrebbe variare. Ad esempio, se la velocità di consumo è inferiore a quella di produzione, la coda potrebbe accumularsi, causando un ritardo nel consumo. Una soluzione è avviare più processi di consumo.

## Invio di messaggi (asincrono)
```php
<?php
namespace app\controller;

use support\Request;
use Webman\RedisQueue\Client;

class Index
{
    public function queue(Request $request)
    {
        // Nome della coda
        $queue = 'send-mail';
        // Dati, è possibile passare direttamente un array senza la necessità di serializzarlo
        $data = ['to' => 'tom@gmail.com', 'content' => 'hello'];
        // Invio del messaggio
        Client::send($queue, $data);
        // Invio di un messaggio con ritardo, che verrà elaborato dopo 60 secondi
        Client::send($queue, $data, 60);

        return response('test della coda Redis');
    }
}
```
`Client::send()` non restituisce valori ed è un invio asincrono, che non garantisce il 100% di consegna del messaggio a Redis.

> **Suggerimento:**
> Il principio di funzionamento di `Client::send()` è quello di creare una coda in memoria locale e inviare in modo asincrono i messaggi a Redis (il che avviene molto rapidamente, con circa 10.000 messaggi al secondo). Se si riavvia il processo e la coda locale in memoria non ha ancora completato la sincronizzazione, potrebbero verificarsi perdite di messaggi. `Client::send()` è adatto per l'invio di messaggi non importanti.

> **Suggerimento:**
> `Client::send()` è asincrono e può essere utilizzato solo nell'ambiente di esecuzione di Workerman. Per gli script della riga di comando, si consiglia di utilizzare l'interfaccia sincrona `Redis::send()`.


## Invio di messaggi da altri progetti
A volte potrebbe essere necessario inviare messaggi a una coda da altri progetti e non è possibile utilizzare `webman\redis-queue`. In questo caso, è possibile utilizzare la seguente funzione per inviare messaggi alla coda.

```php
function redis_queue_send($redis, $queue, $data, $delay = 0) {
    $queue_waiting = '{redis-queue}-waiting';
    $queue_delay = '{redis-queue}-delayed';
    $now = time();
    $package_str = json_encode([
        'id'       => rand(),
        'time'     => $now,
        'delay'    => $delay,
        'attempts' => 0,
        'queue'    => $queue,
        'data'     => $data
    ]);
    if ($delay) {
        return $redis->zAdd($queue_delay, $now + $delay, $package_str);
    }
    return $redis->lPush($queue_waiting.$queue, $package_str);
}
```

In questo caso, il parametro `$redis` è un'istanza di Redis. Ad esempio, l'utilizzo dell'estensione Redis è simile a quanto segue:

```php
$redis = new Redis;
$redis->connect('127.0.0.1', 6379);
$queue = 'user-1';
$data= ['qualche', 'dato'];
redis_queue_send($redis, $queue, $data);
````

## Consumo
Il file di configurazione del processo di consumo si trova in `config/plugin/webman/redis-queue/process.php`.
La directory dei consumatori si trova in `app/queue/redis/`.

Eseguendo il comando `php webman redis-queue:consumer my-send-mail` verrà generato il file `app/queue/redis/MyMailSend.php`.

> **Suggerimento:**
> Se il comando non esiste, è possibile generarlo manualmente.

```php
<?php

namespace app\queue\redis;

use Webman\RedisQueue\Consumer;

class MyMailSend implements Consumer
{
    // Nome della coda da consumare
    public $queue = 'send-mail';
    
    // Nome della connessione, corrispondente alla connessione definita in plugin/webman/redis-queue/redis.php
    public $connection = 'default';
    
    // Consumo
    public function consume($data)
    {
        // Nessuna necessità di deserializzare
        var_export($data); // Output: ['to' => 'tom@gmail.com', 'content' => 'hello']
    }
}
```

> **Nota:**
> Durante il consumo, l'assenza di eccezioni o errori viene considerata un consumo riuscito. In caso contrario, il consumo fallisce e il messaggio viene inserito nella coda di ritentativi. Coda Redis non dispone di un meccanismo di ack. È possibile considerarlo come un ack automatico (in assenza di eccezioni o errori). Se durante il consumo si desidera contrassegnare il messaggio corrente come non consumato con successo, è possibile generare manualmente un'eccezione in modo che il messaggio venga nuovamente inserito nella coda di ritentativi. In realtà, questo è esattamente equivalente al meccanismo di ack.

> **Suggerimento:**
> Il processo di consumo supporta più server e più processi, e lo stesso messaggio **non verrà** consumato più volte. I messaggi già consumati verranno automaticamente rimossi dalla coda, senza necessità di rimozione manuale.

> **Suggerimento:**
> È possibile consumare contemporaneamente diverse code con lo stesso consumatore. Aggiungere nuove code non richiede alcuna modifica alla configurazione presente in `process.php`. È sufficiente aggiungere una nuova classe `Consumer` corrispondente nella directory `app/queue/redis` e specificare il nome della coda tramite la proprietà di classe `$queue`.

> **Suggerimento:**
> Gli utenti Windows devono eseguire `php windows.php` per avviare Webman, altrimenti i processi di consumo non verranno avviati.
## Impostazione di processi di consumo diversi per code diverse
Per impostazione predefinita, tutti i consumatori condividono lo stesso processo di consumo. Tuttavia, a volte è necessario separare il consumo di alcune code, ad esempio mettendo in un gruppo separato i processi di consumo per le attività lente e in un altro gruppo i processi di consumo per le attività veloci. Per fare ciò, è possibile suddividere i consumatori in due directory, ad esempio `app_path() . '/queue/redis/fast'` e `app_path() . '/queue/redis/slow'` (si noti che è necessario apportare le opportune modifiche allo spazio dei nomi delle classi di consumo), quindi configurare come segue:
```php
return [
    ... altre configurazioni omesse ...

    'redis_consumer_fast'  => [
        'handler'     => Webman\RedisQueue\Process\Consumer::class,
        'count'       => 8,
        'constructor' => [
            // Directory delle classi dei consumatori
            'consumer_dir' => app_path() . '/queue/redis/fast'
        ]
    ],
    'redis_consumer_slow'  => [
        'handler'     => Webman\RedisQueue\Process\Consumer::class,
        'count'       => 8,
        'constructor' => [
            // Directory delle classi dei consumatori
            'consumer_dir' => app_path() . '/queue/redis/slow'
        ]
    ]
];
```
Attraverso la suddivisione delle directory e la relativa configurazione, è possibile impostare facilmente processi di consumo diversi per diversi consumatori.

## Configurazione di più database Redis
#### Configurazione
`config/plugin/webman/redis-queue/redis.php`
```php
<?php
return [
    'default' => [
        'host' => 'redis://192.168.0.1:6379',
        'options' => [
            'auth' => null,       // password, di tipo stringa, parametro opzionale
            'db' => 0,            // database
            'max_attempts'  => 5, // numero di tentativi dopo un fallimento del consumo
            'retry_seconds' => 5, // intervallo di ritentativo, in secondi
        ]
    ],
    'other' => [
        'host' => 'redis://192.168.0.2:6379',
        'options' => [
            'auth' => null,       // password, di tipo stringa, parametro opzionale
            'db' => 0,            // database
            'max_attempts'  => 5, // numero di tentativi dopo un fallimento del consumo
            'retry_seconds' => 5, // intervallo di ritentativo, in secondi
        ]
    ],
];
```
Si noti che è stata aggiunta una configurazione Redis con chiave `other`.

#### Invio di messaggi a più database Redis
```php
// Inviare un messaggio alla coda con chiave `default`
Client::connection('default')->send($queue, $data);
Redis::connection('default')->send($queue, $data);
// Equivalente a
Client::send($queue, $data);
Redis::send($queue, $data);

// Inviare un messaggio alla coda con chiave `other`
Client::connection('other')->send($queue, $data);
Redis::connection('other')->send($queue, $data);
```

#### Consumo da più database Redis
Configurazione del consumo dalla coda con chiave `other`
```php
namespace app\queue\redis;

use Webman\RedisQueue\Consumer;

class SendMail implements Consumer
{
    // Nome della coda da consumare
    public $queue = 'send-mail';

    // === Impostato su 'other', che corrisponde alla configurazione di consumo con chiave 'other' ===
    public $connection = 'other';

    // Consumo
    public function consume($data)
    {
        // Nessuna necessità di deserializzazione
        var_export($data);
    }
}
```

## Domande frequenti

**Perché ricevo l'errore `Workerman\Redis\Exception: Workerman Redis Wait Timeout (600 seconds)`?**

Questo errore si verifica solo nell'interfaccia di invio asincrono `Client::send()`. Inviando messaggi in modo asincrono, innanzitutto vengono memorizzati localmente e, quando il processo è inattivo, i messaggi vengono inviati a Redis. Se la velocità di ricezione di Redis è inferiore alla velocità di produzione dei messaggi o se il processo è occupato con altre attività e non ha abbastanza tempo per sincronizzare i messaggi in memoria con Redis, si verificherà un ingorgo dei messaggi. Se un messaggio rimane in coda per più di 600 secondi, si verificherà questo errore.

Soluzione: utilizzare l'interfaccia di invio sincrono `Redis::send()` per inviare i messaggi.
