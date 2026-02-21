# Riga di comando

Componente da riga di comando per Webman

## Installazione
```
composer require webman/console
```

## Indice

### Generazione codice
- [make:controller](#make-controller) - Genera la classe controller
- [make:model](#make-model) - Genera la classe modello dalla tabella del database
- [make:crud](#make-crud) - Genera CRUD completo (modello + controller + validatore)
- [make:middleware](#make-middleware) - Genera la classe middleware
- [make:command](#make-command) - Genera la classe comando da console
- [make:bootstrap](#make-bootstrap) - Genera la classe di inizializzazione bootstrap
- [make:process](#make-process) - Genera la classe processo personalizzato

### Build e distribuzione
- [build:phar](#build-phar) - Impacchetta il progetto come archivio PHAR
- [build:bin](#build-bin) - Impacchetta il progetto come binario standalone
- [install](#install) - Esegue lo script di installazione Webman

### Comandi utility
- [version](#version) - Mostra la versione del framework Webman
- [fix-disable-functions](#fix-disable-functions) - Corregge le funzioni disabilitate in php.ini
- [route:list](#route-list) - Mostra tutte le route registrate

### Gestione plugin applicazione (app-plugin:*)
- [app-plugin:create](#app-plugin-create) - Crea un nuovo plugin applicazione
- [app-plugin:install](#app-plugin-install) - Installa un plugin applicazione
- [app-plugin:uninstall](#app-plugin-uninstall) - Disinstalla un plugin applicazione
- [app-plugin:update](#app-plugin-update) - Aggiorna un plugin applicazione
- [app-plugin:zip](#app-plugin-zip) - Impacchetta un plugin applicazione come ZIP

### Gestione plugin (plugin:*)
- [plugin:create](#plugin-create) - Crea un nuovo plugin Webman
- [plugin:install](#plugin-install) - Installa un plugin Webman
- [plugin:uninstall](#plugin-uninstall) - Disinstalla un plugin Webman
- [plugin:enable](#plugin-enable) - Abilita un plugin Webman
- [plugin:disable](#plugin-disable) - Disabilita un plugin Webman
- [plugin:export](#plugin-export) - Esporta il codice sorgente del plugin

### Gestione servizi
- [start](#start) - Avvia i processi worker Webman
- [stop](#stop) - Arresta i processi worker Webman
- [restart](#restart) - Riavvia i processi worker Webman
- [reload](#reload) - Ricarica il codice senza interruzioni
- [status](#status) - Visualizza lo stato dei processi worker
- [connections](#connections) - Ottiene le informazioni di connessione dei processi worker

## Generazione codice

<a name="make-controller"></a>
### make:controller

Genera la classe controller.

**Utilizzo:**
```bash
php webman make:controller <name>
```

**Argomenti:**

| Argomento | Obbligatorio | Descrizione |
|----------|----------|-------------|
| `name` | Yes | Nome del controller (senza suffisso) |

**Opzioni:**

| Opzione | Scorciatoia | Descrizione |
|--------|----------|-------------|
| `--plugin` | `-p` | Genera il controller nella directory del plugin specificato |
| `--path` | `-P` | Percorso personalizzato per il controller |
| `--force` | `-f` | Sovrascrive se il file esiste già |
| `--no-suffix` | | Non aggiunge il suffisso "Controller" |

**Esempi:**
```bash
# Crea UserController in app/controller
php webman make:controller User

# Crea nel plugin
php webman make:controller AdminUser -p admin

# Percorso personalizzato
php webman make:controller User -P app/api/controller

# Sovrascrive il file esistente
php webman make:controller User -f

# Crea senza suffisso "Controller"
php webman make:controller UserHandler --no-suffix
```

**Struttura del file generato:**
```php
<?php
namespace app\controller;

use support\Request;

class UserController
{
    public function index(Request $request)
    {
        return response('hello user');
    }
}
```

**Note:**
- I controller vengono inseriti in `app/controller/` per impostazione predefinita
- Il suffisso del controller dalla configurazione viene aggiunto automaticamente
- Richiede conferma di sovrascrittura se il file esiste già (come per gli altri comandi)

<a name="make-model"></a>
### make:model

Genera la classe modello dalla tabella del database. Supporta Laravel ORM e ThinkORM.

**Utilizzo:**
```bash
php webman make:model [name]
```

**Argomenti:**

| Argomento | Obbligatorio | Descrizione |
|----------|----------|-------------|
| `name` | No | Nome della classe modello, può essere omesso in modalità interattiva |

**Opzioni:**

| Opzione | Scorciatoia | Descrizione |
|--------|----------|-------------|
| `--plugin` | `-p` | Genera il modello nella directory del plugin specificato |
| `--path` | `-P` | Directory di destinazione (relativa alla root del progetto) |
| `--table` | `-t` | Specifica il nome della tabella; consigliato quando il nome non segue la convenzione |
| `--orm` | `-o` | Scegli ORM: `laravel` o `thinkorm` |
| `--database` | `-d` | Specifica il nome della connessione al database |
| `--force` | `-f` | Sovrascrive il file esistente |

**Note sui percorsi:**
- Predefinito: `app/model/` (app principale) o `plugin/<plugin>/app/model/` (plugin)
- `--path` è relativo alla root del progetto, es. `plugin/admin/app/model`
- Usando sia `--plugin` che `--path`, devono puntare alla stessa directory

**Esempi:**
```bash
# Crea il modello User in app/model
php webman make:model User

# Specifica nome tabella e ORM
php webman make:model User -t wa_users -o laravel

# Crea nel plugin
php webman make:model AdminUser -p admin

# Percorso personalizzato
php webman make:model User -P plugin/admin/app/model
```

**Modalità interattiva:** Quando il nome è omesso, entra nel flusso interattivo: seleziona tabella → inserisci nome modello → inserisci percorso. Supporta: Invio per vedere altro, `0` per creare modello vuoto, `/keyword` per filtrare le tabelle.

**Struttura del file generato:**
```php
<?php
namespace app\model;

use support\Model;

/**
 * @property integer $id (primary key)
 * @property string $name
 */
class User extends Model
{
    protected $connection = 'mysql';
    protected $table = 'users';
    protected $primaryKey = 'id';
    public $timestamps = true;
}
```

Le annotazioni `@property` vengono generate automaticamente dalla struttura della tabella. Supporta MySQL e PostgreSQL.

<a name="make-crud"></a>
### make:crud

Genera modello, controller e validatore dalla tabella del database in un'unica operazione, creando funzionalità CRUD completa.

**Utilizzo:**
```bash
php webman make:crud
```

**Opzioni:**

| Opzione | Scorciatoia | Descrizione |
|--------|----------|-------------|
| `--table` | `-t` | Specifica il nome della tabella |
| `--model` | `-m` | Nome della classe modello |
| `--model-path` | `-M` | Directory del modello (relativa alla root del progetto) |
| `--controller` | `-c` | Nome della classe controller |
| `--controller-path` | `-C` | Directory del controller |
| `--validator` | | Nome della classe validatore (richiede `webman/validation`) |
| `--validator-path` | | Directory del validatore (richiede `webman/validation`) |
| `--plugin` | `-p` | Genera i file nella directory del plugin specificato |
| `--orm` | `-o` | ORM: `laravel` o `thinkorm` |
| `--database` | `-d` | Nome della connessione al database |
| `--force` | `-f` | Sovrascrive i file esistenti |
| `--no-validator` | | Non genera il validatore |
| `--no-interaction` | `-n` | Modalità non interattiva, usa i valori predefiniti |

**Flusso di esecuzione:** Quando `--table` non è specificato, entra nella selezione interattiva della tabella; il nome del modello è dedotto dal nome della tabella; il nome del controller è modello + suffisso controller; il nome del validatore è nome controller senza suffisso + `Validator`. Percorsi predefiniti: modello `app/model/`, controller `app/controller/`, validatore `app/validation/`; per i plugin: sottodirectory corrispondenti sotto `plugin/<plugin>/app/`.

**Esempi:**
```bash
# Generazione interattiva (conferma passo-passo dopo la selezione della tabella)
php webman make:crud

# Specifica nome tabella
php webman make:crud --table=users

# Specifica nome tabella e plugin
php webman make:crud --table=users --plugin=admin

# Specifica i percorsi
php webman make:crud --table=users --model-path=app/model --controller-path=app/controller

# Non genera il validatore
php webman make:crud --table=users --no-validator

# Non interattivo + sovrascrittura
php webman make:crud --table=users --no-interaction --force
```

**Struttura dei file generati:**

Modello (`app/model/User.php`):
```php
<?php

namespace app\model;

use support\Model;

class User extends Model
{
    protected $connection = 'mysql';
    protected $table = 'users';
    protected $primaryKey = 'id';
}
```

Controller (`app/controller/UserController.php`):
```php
<?php

namespace app\controller;

use support\Request;
use support\Response;
use app\model\User;
use app\validation\UserValidator;
use support\validation\annotation\Validate;

class UserController
{
    #[Validate(validator: UserValidator::class, scene: 'create', in: ['body'])]
    public function create(Request $request): Response
    {
        $data = $request->post();
        $model = new User();
        foreach ($data as $key => $value) {
            $model->setAttribute($key, $value);
        }
        $model->save();
        return json(['code' => 0, 'msg' => 'ok', 'data' => $model]);
    }

    #[Validate(validator: UserValidator::class, scene: 'update', in: ['body'])]
    public function update(Request $request): Response
    {
        if (!$model = User::find($request->post('id'))) {
            return json(['code' => 1, 'msg' => 'not found']);
        }
        $data = $request->post();
        unset($data['id']);
        foreach ($data as $key => $value) {
            $model->setAttribute($key, $value);
        }
        $model->save();
        return json(['code' => 0, 'msg' => 'ok', 'data' => $model]);
    }

    #[Validate(validator: UserValidator::class, scene: 'delete', in: ['body'])]
    public function delete(Request $request): Response
    {
        if (!$model = User::find($request->post('id'))) {
            return json(['code' => 1, 'msg' => 'not found']);
        }
        $model->delete();
        return json(['code' => 0, 'msg' => 'ok']);
    }

    #[Validate(validator: UserValidator::class, scene: 'detail')]
    public function detail(Request $request): Response
    {
        if (!$model = User::find($request->input('id'))) {
            return json(['code' => 1, 'msg' => 'not found']);
        }
        return json(['code' => 0, 'msg' => 'ok', 'data' => $model]);
    }
}
```

Validatore (`app/validation/UserValidator.php`):
```php
<?php
declare(strict_types=1);

namespace app\validation;

use support\validation\Validator;

class UserValidator extends Validator
{
    protected array $rules = [
        'id' => 'required|integer|min:0',
        'username' => 'required|string|max:32'
    ];

    protected array $messages = [];

    protected array $attributes = [
        'id' => 'Chiave primaria',
        'username' => 'Nome utente'
    ];

    protected array $scenes = [
        'create' => ['username', 'nickname'],
        'update' => ['id', 'username'],
        'delete' => ['id'],
        'detail' => ['id'],
    ];
}
```

**Note:**
- La generazione del validatore viene saltata se `webman/validation` non è installato o abilitato (installa con `composer require webman/validation`)
- Gli `attributes` del validatore sono generati automaticamente dai commenti dei campi del database; senza commenti non vengono generati `attributes`
- I messaggi di errore del validatore supportano i18n; la lingua è selezionata da `config('translation.locale')`

<a name="make-middleware"></a>
### make:middleware

Genera la classe middleware e la registra automaticamente in `config/middleware.php` (o `plugin/<plugin>/config/middleware.php` per i plugin).

**Utilizzo:**
```bash
php webman make:middleware <name>
```

**Argomenti:**

| Argomento | Obbligatorio | Descrizione |
|----------|----------|-------------|
| `name` | Yes | Nome del middleware |

**Opzioni:**

| Opzione | Scorciatoia | Descrizione |
|--------|----------|-------------|
| `--plugin` | `-p` | Genera il middleware nella directory del plugin specificato |
| `--path` | `-P` | Directory di destinazione (relativa alla root del progetto) |
| `--force` | `-f` | Sovrascrive il file esistente |

**Esempi:**
```bash
# Crea il middleware Auth in app/middleware
php webman make:middleware Auth

# Crea nel plugin
php webman make:middleware Auth -p admin

# Percorso personalizzato
php webman make:middleware Auth -P plugin/admin/app/middleware
```

**Struttura del file generato:**
```php
<?php
namespace app\middleware;

use Webman\MiddlewareInterface;
use Webman\Http\Response;
use Webman\Http\Request;

class Auth implements MiddlewareInterface
{
    public function process(Request $request, callable $handler) : Response
    {
        return $handler($request);
    }
}
```

**Note:**
- Inserito in `app/middleware/` per impostazione predefinita
- Il nome della classe viene aggiunto automaticamente al file di configurazione del middleware per l'attivazione

<a name="make-command"></a>
### make:command

Genera la classe comando da console.

**Utilizzo:**
```bash
php webman make:command <command-name>
```

**Argomenti:**

| Argomento | Obbligatorio | Descrizione |
|----------|----------|-------------|
| `command-name` | Yes | Nome del comando nel formato `group:action` (es. `user:list`) |

**Opzioni:**

| Opzione | Scorciatoia | Descrizione |
|--------|----------|-------------|
| `--plugin` | `-p` | Genera il comando nella directory del plugin specificato |
| `--path` | `-P` | Directory di destinazione (relativa alla root del progetto) |
| `--force` | `-f` | Sovrascrive il file esistente |

**Esempi:**
```bash
# Crea il comando user:list in app/command
php webman make:command user:list

# Crea nel plugin
php webman make:command user:list -p admin

# Percorso personalizzato
php webman make:command user:list -P plugin/admin/app/command

# Sovrascrive il file esistente
php webman make:command user:list -f
```

**Struttura del file generato:**
```php
<?php

namespace app\command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand('user:list', 'user list')]
class UserList extends Command
{
    protected function configure(): void
    {
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('<info>Hello</info> <comment>' . $this->getName() . '</comment>');
        return self::SUCCESS;
    }
}
```

**Note:**
- Inserito in `app/command/` per impostazione predefinita

<a name="make-bootstrap"></a>
### make:bootstrap

Genera la classe di inizializzazione bootstrap. Il metodo `start` viene chiamato automaticamente all'avvio del processo, tipicamente per l'inizializzazione globale.

**Utilizzo:**
```bash
php webman make:bootstrap <name>
```

**Argomenti:**

| Argomento | Obbligatorio | Descrizione |
|----------|----------|-------------|
| `name` | Yes | Nome della classe Bootstrap |

**Opzioni:**

| Opzione | Scorciatoia | Descrizione |
|--------|----------|-------------|
| `--plugin` | `-p` | Genera nella directory del plugin specificato |
| `--path` | `-P` | Directory di destinazione (relativa alla root del progetto) |
| `--force` | `-f` | Sovrascrive il file esistente |

**Esempi:**
```bash
# Crea MyBootstrap in app/bootstrap
php webman make:bootstrap MyBootstrap

# Crea senza abilitazione automatica
php webman make:bootstrap MyBootstrap no

# Crea nel plugin
php webman make:bootstrap MyBootstrap -p admin

# Percorso personalizzato
php webman make:bootstrap MyBootstrap -P plugin/admin/app/bootstrap

# Sovrascrive il file esistente
php webman make:bootstrap MyBootstrap -f
```

**Struttura del file generato:**
```php
<?php

namespace app\bootstrap;

use Webman\Bootstrap;

class MyBootstrap implements Bootstrap
{
    public static function start($worker)
    {
        $is_console = !$worker;
        if ($is_console) {
            return;
        }
        // ...
    }
}
```

**Note:**
- Inserito in `app/bootstrap/` per impostazione predefinita
- All'abilitazione, la classe viene aggiunta a `config/bootstrap.php` (o `plugin/<plugin>/config/bootstrap.php` per i plugin)

<a name="make-process"></a>
### make:process

Genera la classe processo personalizzato e la scrive in `config/process.php` per l'avvio automatico.

**Utilizzo:**
```bash
php webman make:process <name>
```

**Argomenti:**

| Argomento | Obbligatorio | Descrizione |
|----------|----------|-------------|
| `name` | Yes | Nome della classe processo (es. MyTcp, MyWebsocket) |

**Opzioni:**

| Opzione | Scorciatoia | Descrizione |
|--------|----------|-------------|
| `--plugin` | `-p` | Genera nella directory del plugin specificato |
| `--path` | `-P` | Directory di destinazione (relativa alla root del progetto) |
| `--force` | `-f` | Sovrascrive il file esistente |

**Esempi:**
```bash
# Crea in app/process
php webman make:process MyTcp

# Crea nel plugin
php webman make:process MyProcess -p admin

# Percorso personalizzato
php webman make:process MyProcess -P plugin/admin/app/process

# Sovrascrive il file esistente
php webman make:process MyProcess -f
```

**Flusso interattivo:** Chiede in ordine: ascoltare su porta? → tipo di protocollo (websocket/http/tcp/udp/unixsocket) → indirizzo di ascolto (IP:porta o percorso unix socket) → numero di processi. Il protocollo HTTP chiede anche modalità integrata o personalizzata.

**Struttura del file generato:**

Processo non in ascolto (solo `onWorkerStart`):
```php
<?php
namespace app\process;

use Workerman\Worker;

class MyProcess
{
    public function onWorkerStart(Worker $worker)
    {
        // TODO: Write your business logic here.
    }
}
```

I processi TCP/WebSocket in ascolto generano i template di callback corrispondenti `onConnect`, `onMessage`, `onClose`.

**Note:**
- Inserito in `app/process/` per impostazione predefinita; la configurazione del processo viene scritta in `config/process.php`
- La chiave di configurazione è snake_case del nome della classe; fallisce se già esistente
- La modalità HTTP integrata riutilizza il file del processo `app\process\Http`, non genera un nuovo file
- Protocolli supportati: websocket, http, tcp, udp, unixsocket

## Build e distribuzione

<a name="build-phar"></a>
### build:phar

Impacchetta il progetto come archivio PHAR per la distribuzione e il deployment.

**Utilizzo:**
```bash
php webman build:phar
```

**Avvio:**

Spostati nella directory build ed esegui

```bash
php webman.phar start
```

**Note:**
* Il progetto impacchettato non supporta reload; usa restart per aggiornare il codice

* Per evitare file di grandi dimensioni e uso eccessivo di memoria, configura exclude_pattern e exclude_files in config/plugin/webman/console/app.php per escludere i file non necessari.

* L'esecuzione di webman.phar crea una directory runtime nella stessa posizione per log e file temporanei.

* Se il progetto utilizza il file .env, posiziona .env nella stessa directory di webman.phar.

* webman.phar non supporta processi personalizzati su Windows

* Non memorizzare mai i file caricati dagli utenti all'interno del pacchetto phar; operare sui caricamenti utente tramite phar:// è pericoloso (vulnerabilità di deserializzazione phar). I caricamenti utente devono essere memorizzati separatamente su disco fuori dal phar. Vedi sotto.

* Se la tua applicazione deve caricare file nella directory public, estrai la directory public nella stessa posizione di webman.phar e configura config/app.php:
```php
'public_path' => base_path(false) . DIRECTORY_SEPARATOR . 'public',
```
Usa la funzione helper public_path($relative_path) per ottenere il percorso effettivo della directory public.


<a name="build-bin"></a>
### build:bin

Impacchetta il progetto come binario standalone con runtime PHP incorporato. Non è richiesta l'installazione di PHP nell'ambiente di destinazione.

**Utilizzo:**
```bash
php webman build:bin [version]
```

**Argomenti:**

| Argomento | Obbligatorio | Descrizione |
|----------|----------|-------------|
| `version` | No | Versione PHP (es. 8.1, 8.2), predefinita versione PHP corrente, minimo 8.1 |

**Esempi:**
```bash
# Usa la versione PHP corrente
php webman build:bin

# Specifica PHP 8.2
php webman build:bin 8.2
```

**Avvio:**

Spostati nella directory build ed esegui

```bash
./webman.bin start
```

**Note:**
* Fortemente consigliato: la versione PHP locale dovrebbe corrispondere alla versione di build (es. PHP 8.1 locale → build con 8.1) per evitare problemi di compatibilità
* La build scarica il sorgente PHP 8 ma non lo installa localmente; non influisce sull'ambiente PHP locale
* webman.bin attualmente funziona solo su Linux x86_64; non supportato su macOS
* Il progetto impacchettato non supporta reload; usa restart per aggiornare il codice
* .env non viene impacchettato per impostazione predefinita (controllato da exclude_files in config/plugin/webman/console/app.php); posiziona .env nella stessa directory di webman.bin all'avvio
* Viene creata una directory runtime nella directory di webman.bin per i file di log
* webman.bin non legge php.ini esterno; per impostazioni php.ini personalizzate, usa custom_ini in config/plugin/webman/console/app.php
* Escludi i file non necessari tramite config/plugin/webman/console/app.php per evitare pacchetti di grandi dimensioni
* La build binaria non supporta le coroutine Swoole
* Non memorizzare mai i file caricati dagli utenti all'interno del pacchetto binario; operare tramite phar:// è pericoloso (vulnerabilità di deserializzazione phar). I caricamenti utente devono essere memorizzati separatamente su disco fuori dal pacchetto.
* Se la tua applicazione deve caricare file nella directory public, estrai la directory public nella stessa posizione di webman.bin e configura config/app.php come segue, poi ricompila:
```php
'public_path' => base_path(false) . DIRECTORY_SEPARATOR . 'public',
```

<a name="install"></a>
### install

Esegue lo script di installazione del framework Webman (chiama `\Webman\Install::install()`), per l'inizializzazione del progetto.

**Utilizzo:**
```bash
php webman install
```

## Comandi utility

<a name="version"></a>
### version

Mostra la versione di workerman/webman-framework.

**Utilizzo:**
```bash
php webman version
```

**Note:** Legge la versione da `vendor/composer/installed.php`; restituisce errore se non riesce a leggere.

<a name="fix-disable-functions"></a>
### fix-disable-functions

Corregge `disable_functions` in php.ini, rimuovendo le funzioni richieste da Webman.

**Utilizzo:**
```bash
php webman fix-disable-functions
```

**Note:** Rimuove le seguenti funzioni (e corrispondenze per prefisso) da `disable_functions`: `stream_socket_server`, `stream_socket_accept`, `stream_socket_client`, `pcntl_*`, `posix_*`, `proc_*`, `shell_exec`, `exec`. Salta se php.ini non viene trovato o `disable_functions` è vuoto. **Modifica direttamente il file php.ini**; si consiglia il backup.

<a name="route-list"></a>
### route:list

Elenca tutte le route registrate in formato tabella.

**Utilizzo:**
```bash
php webman route:list
```

**Esempio di output:**
```
+-------+--------+-----------------------------------------------+------------+------+
| URI   | Method | Callback                                      | Middleware | Name |
+-------+--------+-----------------------------------------------+------------+------+
| /user | GET    | ["app\\controller\\UserController","index"] | null       |      |
| /api  | POST   | Closure                                      | ["Auth"]   | api  |
+-------+--------+-----------------------------------------------+------------+------+
```

**Colonne di output:** URI, Method, Callback, Middleware, Name. I callback Closure vengono mostrati come "Closure".

## Gestione plugin applicazione (app-plugin:*)

<a name="app-plugin-create"></a>
### app-plugin:create

Crea un nuovo plugin applicazione, generando la struttura completa delle directory e i file base sotto `plugin/<name>`.

**Utilizzo:**
```bash
php webman app-plugin:create <name>
```

**Argomenti:**

| Argomento | Obbligatorio | Descrizione |
|----------|----------|-------------|
| `name` | Yes | Nome del plugin; deve corrispondere a `[a-zA-Z0-9][a-zA-Z0-9_-]*`, non può contenere `/` o `\` |

**Esempi:**
```bash
# Crea il plugin applicazione foo
php webman app-plugin:create foo

# Crea plugin con trattino
php webman app-plugin:create my-app
```

**Struttura delle directory generate:**
```
plugin/<name>/
├── app/
│   ├── controller/IndexController.php
│   ├── model/
│   ├── middleware/
│   ├── view/index/index.html
│   └── functions.php
├── config/          # app.php, route.php, menu.php, ecc.
├── api/Install.php  # Hook installazione/disinstallazione/aggiornamento
├── public/
└── install.sql
```

**Note:**
- Il plugin viene creato sotto `plugin/<name>/`; fallisce se la directory esiste già

<a name="app-plugin-install"></a>
### app-plugin:install

Installa un plugin applicazione, eseguendo `plugin/<name>/api/Install::install($version)`.

**Utilizzo:**
```bash
php webman app-plugin:install <name>
```

**Argomenti:**

| Argomento | Obbligatorio | Descrizione |
|----------|----------|-------------|
| `name` | Yes | Nome del plugin; deve corrispondere a `[a-zA-Z0-9][a-zA-Z0-9_-]*` |

**Esempi:**
```bash
php webman app-plugin:install foo
```

<a name="app-plugin-uninstall"></a>
### app-plugin:uninstall

Disinstalla un plugin applicazione, eseguendo `plugin/<name>/api/Install::uninstall($version)`.

**Utilizzo:**
```bash
php webman app-plugin:uninstall <name>
```

**Argomenti:**

| Argomento | Obbligatorio | Descrizione |
|----------|----------|-------------|
| `name` | Yes | Nome del plugin |

**Opzioni:**

| Opzione | Scorciatoia | Descrizione |
|--------|----------|-------------|
| `--yes` | `-y` | Salta la conferma, esegue direttamente |

**Esempi:**
```bash
php webman app-plugin:uninstall foo
php webman app-plugin:uninstall foo -y
```

<a name="app-plugin-update"></a>
### app-plugin:update

Aggiorna un plugin applicazione, eseguendo in ordine `Install::beforeUpdate($from, $to)` e `Install::update($from, $to, $context)`.

**Utilizzo:**
```bash
php webman app-plugin:update <name>
```

**Argomenti:**

| Argomento | Obbligatorio | Descrizione |
|----------|----------|-------------|
| `name` | Yes | Nome del plugin |

**Opzioni:**

| Opzione | Scorciatoia | Descrizione |
|--------|----------|-------------|
| `--from` | `-f` | Versione di partenza, predefinita versione corrente |
| `--to` | `-t` | Versione di destinazione, predefinita versione corrente |

**Esempi:**
```bash
php webman app-plugin:update foo
php webman app-plugin:update foo --from 1.0.0 --to 1.1.0
```

<a name="app-plugin-zip"></a>
### app-plugin:zip

Impacchetta un plugin applicazione come file ZIP, output in `plugin/<name>.zip`.

**Utilizzo:**
```bash
php webman app-plugin:zip <name>
```

**Argomenti:**

| Argomento | Obbligatorio | Descrizione |
|----------|----------|-------------|
| `name` | Yes | Nome del plugin |

**Esempi:**
```bash
php webman app-plugin:zip foo
```

**Note:**
- Esclude automaticamente `node_modules`, `.git`, `.idea`, `.vscode`, `__pycache__`, ecc.

## Gestione plugin (plugin:*)

<a name="plugin-create"></a>
### plugin:create

Crea un nuovo plugin Webman (in forma di pacchetto Composer), generando la directory di configurazione `config/plugin/<name>` e la directory sorgente del plugin `vendor/<name>`.

**Utilizzo:**
```bash
php webman plugin:create <name>
php webman plugin:create --name <name>
```

**Argomenti:**

| Argomento | Obbligatorio | Descrizione |
|----------|----------|-------------|
| `name` | Yes | Nome del pacchetto plugin nel formato `vendor/package` (es. `foo/my-admin`); deve seguire la denominazione dei pacchetti Composer |

**Esempi:**
```bash
php webman plugin:create foo/my-admin
php webman plugin:create --name foo/my-admin
```

**Struttura generata:**
- `config/plugin/<name>/app.php`: Configurazione del plugin (include switch `enable`)
- `vendor/<name>/composer.json`: Definizione del pacchetto plugin
- `vendor/<name>/src/`: Directory sorgente del plugin
- Aggiunge automaticamente il mapping PSR-4 al composer.json della root del progetto
- Esegue `composer dumpautoload` per aggiornare l'autoload

**Note:**
- Il nome deve essere nel formato `vendor/package`: lettere minuscole, numeri, `-`, `_`, `.`, e deve contenere un `/`
- Fallisce se `config/plugin/<name>` o `vendor/<name>` esistono già
- Errore se vengono forniti sia l'argomento che `--name` con valori diversi

<a name="plugin-install"></a>
### plugin:install

Esegue lo script di installazione del plugin (`Install::install()`), copiando le risorse del plugin nella directory del progetto.

**Utilizzo:**
```bash
php webman plugin:install <name>
php webman plugin:install --name <name>
```

**Argomenti:**

| Argomento | Obbligatorio | Descrizione |
|----------|----------|-------------|
| `name` | Yes | Nome del pacchetto plugin nel formato `vendor/package` (es. `foo/my-admin`) |

**Opzioni:**

| Opzione | Descrizione |
|--------|-------------|
| `--name` | Specifica il nome del plugin come opzione; usa questo o l'argomento |

**Esempi:**
```bash
php webman plugin:install foo/my-admin
php webman plugin:install --name foo/my-admin
```

<a name="plugin-uninstall"></a>
### plugin:uninstall

Esegue lo script di disinstallazione del plugin (`Install::uninstall()`), rimuovendo le risorse del plugin dal progetto.

**Utilizzo:**
```bash
php webman plugin:uninstall <name>
php webman plugin:uninstall --name <name>
```

**Argomenti:**

| Argomento | Obbligatorio | Descrizione |
|----------|----------|-------------|
| `name` | Yes | Nome del pacchetto plugin nel formato `vendor/package` |

**Opzioni:**

| Opzione | Descrizione |
|--------|-------------|
| `--name` | Specifica il nome del plugin come opzione; usa questo o l'argomento |

**Esempi:**
```bash
php webman plugin:uninstall foo/my-admin
```

<a name="plugin-enable"></a>
### plugin:enable

Abilita il plugin, impostando `enable` a `true` in `config/plugin/<name>/app.php`.

**Utilizzo:**
```bash
php webman plugin:enable <name>
php webman plugin:enable --name <name>
```

**Argomenti:**

| Argomento | Obbligatorio | Descrizione |
|----------|----------|-------------|
| `name` | Yes | Nome del pacchetto plugin nel formato `vendor/package` |

**Opzioni:**

| Opzione | Descrizione |
|--------|-------------|
| `--name` | Specifica il nome del plugin come opzione; usa questo o l'argomento |

**Esempi:**
```bash
php webman plugin:enable foo/my-admin
```

<a name="plugin-disable"></a>
### plugin:disable

Disabilita il plugin, impostando `enable` a `false` in `config/plugin/<name>/app.php`.

**Utilizzo:**
```bash
php webman plugin:disable <name>
php webman plugin:disable --name <name>
```

**Argomenti:**

| Argomento | Obbligatorio | Descrizione |
|----------|----------|-------------|
| `name` | Yes | Nome del pacchetto plugin nel formato `vendor/package` |

**Opzioni:**

| Opzione | Descrizione |
|--------|-------------|
| `--name` | Specifica il nome del plugin come opzione; usa questo o l'argomento |

**Esempi:**
```bash
php webman plugin:disable foo/my-admin
```

<a name="plugin-export"></a>
### plugin:export

Esporta la configurazione del plugin e le directory specificate dal progetto a `vendor/<name>/src/`, e genera `Install.php` per il packaging e il rilascio.

**Utilizzo:**
```bash
php webman plugin:export <name> [--source=path]...
php webman plugin:export --name <name> [--source=path]...
```

**Argomenti:**

| Argomento | Obbligatorio | Descrizione |
|----------|----------|-------------|
| `name` | Yes | Nome del pacchetto plugin nel formato `vendor/package` |

**Opzioni:**

| Opzione | Scorciatoia | Descrizione |
|--------|----------|-------------|
| `--name` | | Specifica il nome del plugin come opzione; usa questo o l'argomento |
| `--source` | `-s` | Percorso da esportare (relativo alla root del progetto); può essere specificato più volte |

**Esempi:**
```bash
# Esporta il plugin, include di default config/plugin/<name>
php webman plugin:export foo/my-admin

# Esporta inoltre app, config, ecc.
php webman plugin:export foo/my-admin --source app --source config
php webman plugin:export --name foo/my-admin -s app -s config
```

**Note:**
- Il nome del plugin deve seguire la denominazione dei pacchetti Composer (`vendor/package`)
- Se `config/plugin/<name>` esiste e non è in `--source`, viene aggiunto automaticamente alla lista di esportazione
- L'`Install.php` esportato include `pathRelation` per l'uso da `plugin:install` / `plugin:uninstall`
- `plugin:install` e `plugin:uninstall` richiedono che il plugin esista in `vendor/<name>`, con classe `Install` e costante `WEBMAN_PLUGIN`

## Gestione servizi

<a name="start"></a>
### start

Avvia i processi worker Webman. Modalità DEBUG predefinita (primo piano).

**Utilizzo:**
```bash
php webman start
```

**Opzioni:**

| Opzione | Scorciatoia | Descrizione |
|--------|----------|-------------|
| `--daemon` | `-d` | Avvia in modalità DAEMON (background) |

<a name="stop"></a>
### stop

Arresta i processi worker Webman.

**Utilizzo:**
```bash
php webman stop
```

**Opzioni:**

| Opzione | Scorciatoia | Descrizione |
|--------|----------|-------------|
| `--graceful` | `-g` | Arresto graceful; attende il completamento delle richieste correnti prima di uscire |

<a name="restart"></a>
### restart

Riavvia i processi worker Webman.

**Utilizzo:**
```bash
php webman restart
```

**Opzioni:**

| Opzione | Scorciatoia | Descrizione |
|--------|----------|-------------|
| `--daemon` | `-d` | Esegue in modalità DAEMON dopo il riavvio |
| `--graceful` | `-g` | Arresto graceful prima del riavvio |

<a name="reload"></a>
### reload

Ricarica il codice senza interruzioni. Per hot-reload dopo aggiornamenti del codice.

**Utilizzo:**
```bash
php webman reload
```

**Opzioni:**

| Opzione | Scorciatoia | Descrizione |
|--------|----------|-------------|
| `--graceful` | `-g` | Ricarica graceful; attende il completamento delle richieste correnti prima di ricaricare |

<a name="status"></a>
### status

Visualizza lo stato di esecuzione dei processi worker.

**Utilizzo:**
```bash
php webman status
```

**Opzioni:**

| Opzione | Scorciatoia | Descrizione |
|--------|----------|-------------|
| `--live` | `-d` | Mostra i dettagli (stato in tempo reale) |

<a name="connections"></a>
### connections

Ottiene le informazioni di connessione dei processi worker.

**Utilizzo:**
```bash
php webman connections
```
