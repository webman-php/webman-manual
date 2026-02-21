# Limitatore di frequenza

Limitatore di frequenza webman con supporto limitazione tramite annotazioni.
Supporta driver apcu, redis e memory.

## Repository sorgente

https://github.com/webman-php/limiter

## Installazione

```
composer require webman/limiter
```

## Utilizzo

```php
<?php
namespace app\controller;

use RuntimeException;
use support\limiter\annotation\Limit;

class UserController
{

    #[Limit(limit: 10)]
    public function index(): string
    {
        // Default limitazione per IP, finestra temporale default 1 secondo
        return 'Massimo 10 richieste per IP per secondo';
    }

    #[Limit(limit: 100, ttl: 60, key: Limit::UID)]
    public function search(): string
    {
        // key: Limit::UID, limitazione per ID utente, richiede session('user.id') non vuota
        return 'Massimo 100 ricerche per utente per 60 secondi';
    }

    #[Limit(limit: 1, ttl: 60, key: Limit::SID, message: 'Solo 1 email per persona per minuto')]
    public function sendMail(): string
    {
        // key: Limit::SID, limitazione per session_id
        return 'Email inviata con successo';
    }

    #[Limit(limit: 100, ttl: 24*60*60, key: 'coupon', message: 'Coupon di oggi esauriti, riprova domani')]
    #[Limit(limit: 1, ttl: 24*60*60, key: Limit::UID, message: 'Ogni utente può riscattare un solo coupon al giorno')]
    public function coupon(): string
    {
        // key: 'coupon', chiave personalizzata per limitazione globale, max 100 coupon al giorno
        // Anche limitazione per ID utente, ogni utente un coupon al giorno
        return 'Coupon inviato con successo';
    }

    #[Limit(limit: 5, ttl: 24*60*60, key: [UserController::class, 'getMobile'], message: 'Massimo 5 SMS per numero al giorno')]
    public function sendSms2(): string
    {
        // Quando key è variabile: [classe, metodo_statico], es. [UserController::class, 'getMobile'] usa il valore di ritorno di UserController::getMobile() come chiave
        return 'SMS inviato con successo';
    }

    /**
     * Chiave personalizzata, ottenere numero mobile, deve essere metodo statico
     * @return string
     */
    public static function getMobile(): string
    {
        return request()->get('mobile');
    }

    #[Limit(limit: 1, ttl: 10, key: Limit::IP, message: 'Frequenza limitata', exception: RuntimeException::class)]
    public function testException(): string
    {
        // Eccezione default al superamento: support\limiter\RateLimitException, modificabile via parametro exception
        return 'ok';
    }

}
```

**Note**

* Usa algoritmo a finestra fissa
* Finestra temporale ttl default: 1 secondo
* Impostare finestra via ttl, es. `ttl:60` per 60 secondi
* Dimensione limitazione default: IP (default `127.0.0.1` non limitato, vedi configurazione sotto)
* Integrato: limitazione IP, UID (richiede `session('user.id')` non vuota), SID (per `session_id`)
* Con proxy nginx, passare header `X-Forwarded-For` per limitazione IP, vedi [proxy nginx](../others/nginx-proxy.md)
* Genera `support\limiter\RateLimitException` al superamento, classe eccezione personalizzata via `exception:xx`
* Messaggio errore default al superamento: `Too Many Requests`, messaggio personalizzato via `message:xx`
* Messaggio errore default modificabile via [traduzione](translation.md), riferimento Linux:

```
composer require symfony/translation
mkdir resource/translations/zh_CN/ -p
echo "<?php
return [
    'Too Many Requests' => '请求频率受限'
];" > resource/translations/zh_CN/messages.php
php start.php restart
```

## API

A volte gli sviluppatori vogliono chiamare il limitatore direttamente nel codice, vedere il seguente esempio:

```php
<?php
namespace app\controller;

use RuntimeException;
use support\limiter\Limiter;

class UserController {

    public function sendSms(string $mobile): string
    {
        // mobile usato come chiave qui
        Limiter::check($mobile, 5, 24*60*60, 'Massimo 5 SMS per numero al giorno');
        return 'SMS inviato con successo';
    }
}
```

## Configurazione

**config/plugin/webman/limiter/app.php**

```
<?php

use support\limiter\RateLimitException;

return [
    'enable' => true,
    'driver' => 'auto', // auto, apcu, memory, redis
    'stores' => [
        'redis' => [
            'connection' => 'default',
        ]
    ],
    // Questi IP non sono limitati (efficace solo quando key è Limit::IP)
    'ip_whitelist' => [
        '127.0.0.1',
    ],
    'exception' => RateLimitException::class
];
```

* **enable**: Abilitare limitazione frequenza
* **driver**: Uno di `auto`, `apcu`, `memory`, `redis`; `auto` sceglie automaticamente tra `apcu` (prioritario) e `memory`
* **stores**: Configurazione Redis, `connection` corrisponde alla chiave in `config/redis.php`
* **ip_whitelist**: Gli IP in whitelist non sono limitati (efficace solo quando key è `Limit::IP`)

## Scelta del driver

**memory**

* Introduzione
  Nessuna estensione richiesta, migliori prestazioni.

* Limitazioni
  Limitazione solo per processo corrente, nessuna condivisione dati tra processi, limitazione cluster non supportata.

* Casi d'uso
  Ambiente sviluppo Windows; scenari senza limitazione rigorosa; difesa da attacchi CC.

**apcu**

* Installazione estensione
  Estensione apcu richiesta, impostazioni php.ini:

```
apc.enabled=1
apc.enable_cli=1
```

Posizione php.ini con `php --ini`

* Introduzione
  Ottime prestazioni, supporta condivisione multi-processo.

* Limitazioni
  Cluster non supportato

* Casi d'uso
  Qualsiasi ambiente sviluppo; limitazione server singolo in produzione; cluster senza limitazione rigorosa; difesa da attacchi CC.

**redis**

* Dipendenze
  Estensione redis e componente Redis richiesti, installazione:

```
composer require -W webman/redis illuminate/events
```

* Introduzione
  Prestazioni inferiori a apcu, supporta limitazione precisa server singolo e cluster

* Casi d'uso
  Ambiente sviluppo; server singolo in produzione; ambiente cluster
