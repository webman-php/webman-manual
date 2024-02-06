# Plugin dell'applicazione
Ogni plugin dell'applicazione è un'applicazione completa il cui codice sorgente è posizionato nella directory `{progetto principale}/plugin`

> **Suggerimento**
> Utilizzando il comando `php webman app-plugin:create {nome del plugin}` (richiede webman/console>=1.2.16) è possibile creare localmente un'applicazione plugin.
> Ad esempio, `php webman app-plugin:create cms` creerà la seguente struttura di directory

```plaintext
plugin/
└── cms
    ├── app
    │   ├── controller
    │   │   └── IndexController.php
    │   ├── exception
    │   │   └── Handler.php
    │   ├── functions.php
    │   ├── middleware
    │   ├── model
    │   └── view
    │       └── index
    │           └── index.html
    ├── config
    │   ├── app.php
    │   ├── autoload.php
    │   ├── container.php
    │   ├── database.php
    │   ├── exception.php
    │   ├── log.php
    │   ├── middleware.php
    │   ├── process.php
    │   ├── redis.php
    │   ├── route.php
    │   ├── static.php
    │   ├── thinkorm.php
    │   ├── translation.php
    │   └── view.php
    └── public
```

Qui vediamo che un'applicazione plugin ha la stessa struttura di directory e file di configurazione di webman. In realtà, sviluppare un'applicazione plugin è praticamente identico allo sviluppo di un progetto webman, con solo alcune differenze da tenere presenti.

## Spazio dei nomi
La directory e il nome del plugin seguono la specifica PSR4, in quanto i plugin sono posizionati nella directory plugin, quindi lo spazio dei nomi inizia con plugin, ad esempio `plugin\cms\app\controller\UserController`, dove cms è la directory principale del codice sorgente del plugin.

## Accesso url
Il percorso dell'URL dell'applicazione plugin inizia con `/app`, ad esempio l'URL per `plugin\cms\app\controller\UserController` è `http://127.0.0.1:8787/app/cms/user`.

## File statici
I file statici vengono posizionati in `plugin/{nome del plugin}/public`, ad esempio l'accesso a `http://127.0.0.1:8787/app/cms/avatar.png` in realtà ottiene il file `plugin/cms/public/avatar.png`.

## File di configurazione
La configurazione del plugin è simile a quella del progetto webman, tuttavia la configurazione del plugin di solito ha effetto solo sul plugin corrente e non sul progetto principale.
Ad esempio, il valore di `plugin.cms.app.controller_suffix` influisce solo sul suffisso del controller del plugin e non ha alcun effetto sul progetto principale.
Analogamente, il valore di `plugin.cms.app.controller_reuse` influisce solo sull'eventuale riutilizzo del controller del plugin e non ha alcun effetto sul progetto principale.
Allo stesso modo, il valore di `plugin.cms.middleware` influisce solo sul middleware del plugin e non ha alcun effetto sul progetto principale.
E così via per `plugin.cms.view`, `plugin.cms.container`, `plugin.cms.exception`.

Tuttavia, poiché le route sono globali, la configurazione delle route del plugin influisce anche globalmente.

## Ottenere la configurazione
Il modo per ottenere la configurazione di un plugin specifico è `config('plugin.{nome del plugin}.{configurazione specifica}');`, ad esempio per ottenere tutte le configurazioni di `plugin/cms/config/app.php` si utilizza `config('plugin.cms.app')`.
Allo stesso modo, il progetto principale o altri plugin possono utilizzare `config('plugin.cms.xxx')` per ottenere la configurazione del plugin cms.

## Configurazioni non supportate
Gli applicazioni plugin non supportano le configurazioni server.php e session.php, e non supportano le configurazioni `app.request_class`, `app.public_path` e `app.runtime_path`.

## Database
I plugin possono configurare il proprio database, ad esempio il contenuto di `plugin/cms/config/database.php` è il seguente
```php
return  [
    'default' => 'mysql',
    'connections' => [
        'mysql' => [ // mysql è il nome della connessione
            'driver'      => 'mysql',
            'host'        => '127.0.0.1',
            'port'        => 3306,
            'database'    => 'nome del database',
            'username'    => 'nome utente',
            'password'    => 'password',
            'charset'     => 'utf8mb4',
            'collation'   => 'utf8mb4_general_ci',
        ],
        'admin' => [ // admin è il nome della connessione
            'driver'      => 'mysql',
            'host'        => '127.0.0.1',
            'port'        => 3306,
            'database'    => 'nome del database',
            'username'    => 'nome utente',
            'password'    => 'password',
            'charset'     => 'utf8mb4',
            'collation'   => 'utf8mb4_general_ci',
        ],
    ],
];
```
L'uso è `Db::connection('plugin.{nome del plugin}.{nome della connessione}');`, ad esempio
```php
use support\Db;
Db::connection('plugin.cms.mysql')->table('user')->first();
Db::connection('plugin.cms.admin')->table('admin')->first();
```

Se si desidera utilizzare il database del progetto principale, è sufficiente utilizzarlo direttamente, ad esempio
```php
use support\Db;
Db::table('user')->first();
// Supponendo che il progetto principale abbia anche una connessione admin
Db::connection('admin')->table('admin')->first();
```

> **Suggerimento**
> Anche thinkorm segue un approccio simile

## Redis
L'utilizzo di Redis è simile a quello del database, ad esempio `plugin/cms/config/redis.php` è il seguente
```php
return [
    'default' => [
        'host' => '127.0.0.1',
        'password' => null,
        'port' => 6379,
        'database' => 0,
    ],
    'cache' => [
        'host' => '127.0.0.1',
        'password' => null,
        'port' => 6379,
        'database' => 1,
    ],
];
```
L'utilizzo è il seguente
```php
use support\Redis;
Redis::connection('plugin.cms.default')->get('chiave');
Redis::connection('plugin.cms.cache')->get('chiave');
```

Analogamente, se si desidera riutilizzare la configurazione di Redis del progetto principale
```php
use support\Redis;
Redis::get('chiave');
// Supponendo che il progetto principale abbia anche una connessione cache
Redis::connection('cache')->get('chiave');
```

## Log
L'uso della classe di log è simile a quello del database
```php
use support\Log;
Log::channel('plugin.admin.default')->info('test');
```

Se si desidera riutilizzare la configurazione del log del progetto principale, è sufficiente utilizzarla direttamente
```php
use support\Log;
Log::info('contenuto del log');
// Supponendo che il progetto principale abbia una configurazione di log denominata test
Log::channel('test')->info('contenuto del log');
```

# Installazione e disinstallazione del plugin dell'applicazione
Per installare un plugin dell'applicazione è sufficiente copiare la directory del plugin nella directory `{progetto principale}/plugin` e quindi eseguire il comando reload o restart affinché diventi effettiva. Per disinstallare un plugin, basta eliminare la directory del plugin corrispondente nella directory `{progetto principale}/plugin`.
