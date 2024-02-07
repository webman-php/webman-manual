# Plugin dell'applicazione
Ogni plugin dell'applicazione è un'applicazione completa il cui codice sorgente è posizionato nella directory `{progetto principale}/plugin`

> **Suggerimento**
L'utilizzo del comando `php webman app-plugin:create {nome del plugin}` (richiede webman/console>=1.2.16) consente di creare localmente un'applicazione plugin, ad esempio `php webman app-plugin:create cms` creerà la seguente struttura di directory

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

Notiamo che un'applicazione plugin ha la stessa struttura di directory e file di configurazione di webman. Di fatto, lo sviluppo di un'applicazione plugin è fondamentalmente simile allo sviluppo di un progetto webman, con alcune considerazioni da tenere presenti.

## Namespace
La directory e il nome dei plugin seguono la specifica PSR4. Poiché i plugin sono tutti posizionati nella directory del plugin, lo spazio dei nomi inizia con `plugin\`, ad esempio `plugin\cms\app\controller\UserController`, dove "cms" è la directory sorgente del plugin.

## Accesso URL
Il percorso dell'indirizzo URL dell'applicazione plugin inizia con `/app`. Ad esempio, l'indirizzo URL per `plugin\cms\app\controller\UserController` è `http://127.0.0.1:8787/app/cms/user`.

## File statici
I file statici vengono posizionati in `plugin/{plugin}/public`. Ad esempio, l'accesso a `http://127.0.0.1:8787/app/cms/avatar.png` in realtà recupera il file `plugin/cms/public/avatar.png`.

## File di configurazione
La configurazione del plugin è simile a quella di un progetto webman normale. Tuttavia, la configurazione del plugin di solito ha effetto solo sul plugin corrente e non sul progetto principale.
Per esempio, il valore di `plugin.cms.app.controller_suffix` influenza solo il suffisso del controller del plugin e non ha alcun effetto sul progetto principale.
Allo stesso modo, il valore di `plugin.cms.app.controller_reuse` influenza solo se il plugin riutilizza i controller e non ha alcun effetto sul progetto principale.
Analogamente, il valore di `plugin.cms.middleware` influenza solo i middleware del plugin e non ha alcun effetto sul progetto principale.
Del resto, il valore di `plugin.cms.view` influenza solo la vista utilizzata dal plugin e non ha alcun effetto sul progetto principale.
Infine, il valore di `plugin.cms.container` influenza solo il contenitore utilizzato dal plugin e non ha alcun effetto sul progetto principale.
Allo stesso modo, il valore di `plugin.cms.exception` influenza solo la classe di gestione delle eccezioni del plugin e non ha alcun effetto sul progetto principale.

Tuttavia, poiché il router è globale, la configurazione del router del plugin influisce anche globalmente.

## Reperimento della configurazione
Il reperimento della configurazione di un determinato plugin è eseguito tramite `config('plugin.{plugin}.{configurazione specifica}');`, ad esempio per ottenere tutte le configurazioni di `plugin/cms/config/app.php` si utilizza `config('plugin.cms.app')`.
Allo stesso modo, anche il progetto principale o altri plugin possono utilizzare `config('plugin.cms.xxx')` per ottenere la configurazione del plugin cms.

## Configurazioni non supportate
Gli applicativi plugin non supportano le configurazioni `server.php`, `session.php`, `app.request_class`, `app.public_path` e `app.runtime_path`.

## Database
I plugin possono configurare il proprio database. Ad esempio, il contenuto di `plugin/cms/config/database.php` è il seguente:

```php
return  [
    'default' => 'mysql',
    'connections' => [
        'mysql' => [ // mysql è il nome della connessione
            'driver'      => 'mysql',
            'host'        => '127.0.0.1',
            'port'        => 3306,
            'database'    => 'database',
            'username'    => 'username',
            'password'    => 'password',
            'charset'     => 'utf8mb4',
            'collation'   => 'utf8mb4_general_ci',
        ],
        'admin' => [ // admin è il nome della connessione
            'driver'      => 'mysql',
            'host'        => '127.0.0.1',
            'port'        => 3306,
            'database'    => 'database',
            'username'    => 'username',
            'password'    => 'password',
            'charset'     => 'utf8mb4',
            'collation'   => 'utf8mb4_general_ci',
        ],
    ],
];
```
Il modo per richiamarlo è `Db::connection('plugin.{plugin}.{nome connessione}');`. Ad esempio:

```php
use support\Db;
Db::connection('plugin.cms.mysql')->table('user')->first();
Db::connection('plugin.cms.admin')->table('admin')->first();
```

Se si desidera utilizzare il database del progetto principale, basta utilizzarlo direttamente, ad esempio:

```php
use support\Db;
Db::table('user')->first();
// Supponendo che il progetto principale abbia anche una connessione admin
Db::connection('admin')->table('admin')->first();
```

> **Suggerimento**
> thinkorm utilizza un metodo simile

## Redis
L'utilizzo di Redis è simile a quello del database. Ad esempio, in `plugin/cms/config/redis.php`:

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
Il suo utilizzo è il seguente:

```php
use support\Redis;
Redis::connection('plugin.cms.default')->get('key');
Redis::connection('plugin.cms.cache')->get('key');
```

Analogamente, se si desidera riutilizzare la configurazione Redis del progetto principale:

```php
use support\Redis;
Redis::get('key');
// Supponendo che il progetto principale abbia anche una connessione cache
Redis::connection('cache')->get('key');
```

## Log
L'utilizzo della classe di log è simile a quello del database

```php
use support\Log;
Log::channel('plugin.admin.default')->info('test');
```

Se si desidera riutilizzare la configurazione del log del progetto principale, basta utilizzarla direttamente:

```php
use support\Log;
Log::info('Contenuto log');
// Supponendo che il progetto principale abbia una configurazione di log test
Log::channel('test')->info('Contenuto log');
```

# Installazione e disinstallazione del plugin dell'applicazione
Per installare un'applicazione plugin, è sufficiente copiare la directory del plugin nella directory `{progetto principale}/plugin`, è necessario eseguire reload o restart per renderla effettiva.
Per disinstallare, basta eliminare la directory del plugin corrispondente in `{progetto principale}/plugin`.
