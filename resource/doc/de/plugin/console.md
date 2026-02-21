# Befehlszeile

Webman-Befehlszeilenkomponente

## Installation
```
composer require webman/console
```

## Inhaltsverzeichnis

### Code-Generierung
- [make:controller](#make-controller) - Controller-Klasse generieren
- [make:model](#make-model) - Modell-Klasse aus Datenbanktabelle generieren
- [make:crud](#make-crud) - Vollständiges CRUD generieren (Modell + Controller + Validator)
- [make:middleware](#make-middleware) - Middleware-Klasse generieren
- [make:command](#make-command) - Konsolenbefehl-Klasse generieren
- [make:bootstrap](#make-bootstrap) - Bootstrap-Initialisierungsklasse generieren
- [make:process](#make-process) - Benutzerdefinierte Prozess-Klasse generieren

### Build und Deployment
- [build:phar](#build-phar) - Projekt als PHAR-Archiv packen
- [build:bin](#build-bin) - Projekt als eigenständige Binärdatei packen
- [install](#install) - Webman-Installationsskript ausführen

### Hilfsbefehle
- [version](#version) - Webman-Framework-Version anzeigen
- [fix-disable-functions](#fix-disable-functions) - Deaktivierte Funktionen in php.ini reparieren
- [route:list](#route-list) - Alle registrierten Routen anzeigen

### App-Plugin-Verwaltung (app-plugin:*)
- [app-plugin:create](#app-plugin-create) - Neues App-Plugin erstellen
- [app-plugin:install](#app-plugin-install) - App-Plugin installieren
- [app-plugin:uninstall](#app-plugin-uninstall) - App-Plugin deinstallieren
- [app-plugin:update](#app-plugin-update) - App-Plugin aktualisieren
- [app-plugin:zip](#app-plugin-zip) - App-Plugin als ZIP packen

### Plugin-Verwaltung (plugin:*)
- [plugin:create](#plugin-create) - Neues Webman-Plugin erstellen
- [plugin:install](#plugin-install) - Webman-Plugin installieren
- [plugin:uninstall](#plugin-uninstall) - Webman-Plugin deinstallieren
- [plugin:enable](#plugin-enable) - Webman-Plugin aktivieren
- [plugin:disable](#plugin-disable) - Webman-Plugin deaktivieren
- [plugin:export](#plugin-export) - Plugin-Quellcode exportieren

### Dienstverwaltung
- [start](#start) - Webman-Worker-Prozesse starten
- [stop](#stop) - Webman-Worker-Prozesse stoppen
- [restart](#restart) - Webman-Worker-Prozesse neu starten
- [reload](#reload) - Code ohne Ausfallzeit neu laden
- [status](#status) - Worker-Prozessstatus anzeigen
- [connections](#connections) - Worker-Prozessverbindungsinformationen abrufen

## Code-Generierung

<a name="make-controller"></a>
### make:controller

Controller-Klasse generieren.

**Verwendung:**
```bash
php webman make:controller <name>
```

**Argumente:**

| Argument | Erforderlich | Beschreibung |
|----------|----------|-------------|
| `name` | Ja | Controller-Name (ohne Suffix) |

**Optionen:**

| Option | Kurzform | Beschreibung |
|--------|----------|-------------|
| `--plugin` | `-p` | Controller im angegebenen Plugin-Verzeichnis generieren |
| `--path` | `-P` | Benutzerdefinierter Controller-Pfad |
| `--force` | `-f` | Überschreiben, falls Datei existiert |
| `--no-suffix` | | Kein „Controller“-Suffix anhängen |

**Beispiele:**
```bash
# UserController in app/controller erstellen
php webman make:controller User

# Im Plugin erstellen
php webman make:controller AdminUser -p admin

# Benutzerdefinierter Pfad
php webman make:controller User -P app/api/controller

# Vorhandene Datei überschreiben
php webman make:controller User -f

# Ohne „Controller“-Suffix erstellen
php webman make:controller UserHandler --no-suffix
```

**Generierte Dateistruktur:**
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

**Hinweise:**
- Controller werden standardmäßig in `app/controller/` abgelegt
- Controller-Suffix aus der Konfiguration wird automatisch angehängt
- Bei vorhandener Datei wird eine Überschreibbestätigung abgefragt (gilt auch für andere Befehle)

<a name="make-model"></a>
### make:model

Modell-Klasse aus Datenbanktabelle generieren. Unterstützt Laravel ORM und ThinkORM.

**Verwendung:**
```bash
php webman make:model [name]
```

**Argumente:**

| Argument | Erforderlich | Beschreibung |
|----------|----------|-------------|
| `name` | Nein | Modell-Klassenname, kann im interaktiven Modus weggelassen werden |

**Optionen:**

| Option | Kurzform | Beschreibung |
|--------|----------|-------------|
| `--plugin` | `-p` | Modell im angegebenen Plugin-Verzeichnis generieren |
| `--path` | `-P` | Zielverzeichnis (relativ zum Projektstamm) |
| `--table` | `-t` | Tabellenname angeben; empfohlen, wenn der Tabellenname nicht der Konvention entspricht |
| `--orm` | `-o` | ORM wählen: `laravel` oder `thinkorm` |
| `--database` | `-d` | Datenbankverbindungsname angeben |
| `--force` | `-f` | Vorhandene Datei überschreiben |

**Pfad-Hinweise:**
- Standard: `app/model/` (Hauptanwendung) oder `plugin/<plugin>/app/model/` (Plugin)
- `--path` ist relativ zum Projektstamm, z. B. `plugin/admin/app/model`
- Bei gleichzeitiger Verwendung von `--plugin` und `--path` müssen beide auf dasselbe Verzeichnis verweisen

**Beispiele:**
```bash
# User-Modell in app/model erstellen
php webman make:model User

# Tabellenname und ORM angeben
php webman make:model User -t wa_users -o laravel

# Im Plugin erstellen
php webman make:model AdminUser -p admin

# Benutzerdefinierter Pfad
php webman make:model User -P plugin/admin/app/model
```

**Interaktiver Modus:** Bei weggelassenem Namen: interaktiver Ablauf: Tabelle auswählen → Modellname eingeben → Pfad eingeben. Unterstützt: Enter für mehr, `0` für leeres Modell, `/Schlüsselwort` zum Filtern von Tabellen.

**Generierte Dateistruktur:**
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

`@property`-Annotationen werden automatisch aus der Tabellenstruktur generiert. Unterstützt MySQL und PostgreSQL.

<a name="make-crud"></a>
### make:crud

Modell, Controller und Validator aus Datenbanktabelle in einem Durchgang generieren und vollständige CRUD-Funktionalität bereitstellen.

**Verwendung:**
```bash
php webman make:crud
```

**Optionen:**

| Option | Kurzform | Beschreibung |
|--------|----------|-------------|
| `--table` | `-t` | Tabellenname angeben |
| `--model` | `-m` | Modell-Klassenname |
| `--model-path` | `-M` | Modell-Verzeichnis (relativ zum Projektstamm) |
| `--controller` | `-c` | Controller-Klassenname |
| `--controller-path` | `-C` | Controller-Verzeichnis |
| `--validator` | | Validator-Klassenname (erfordert `webman/validation`) |
| `--validator-path` | | Validator-Verzeichnis (erfordert `webman/validation`) |
| `--plugin` | `-p` | Dateien im angegebenen Plugin-Verzeichnis generieren |
| `--orm` | `-o` | ORM: `laravel` oder `thinkorm` |
| `--database` | `-d` | Datenbankverbindungsname |
| `--force` | `-f` | Vorhandene Dateien überschreiben |
| `--no-validator` | | Keinen Validator generieren |
| `--no-interaction` | `-n` | Nicht-interaktiver Modus, Standardwerte verwenden |

**Ablauf:** Ohne `--table` interaktive Tabellenauswahl; Modellname standardmäßig aus Tabellenname abgeleitet; Controller-Name standardmäßig Modellname + Controller-Suffix; Validator-Name standardmäßig Controller-Name ohne Suffix + `Validator`. Standardpfade: Modell `app/model/`, Controller `app/controller/`, Validator `app/validation/`; bei Plugins: entsprechende Unterverzeichnisse unter `plugin/<plugin>/app/`.

**Beispiele:**
```bash
# Interaktive Generierung (schrittweise Bestätigung nach Tabellenauswahl)
php webman make:crud

# Tabellenname angeben
php webman make:crud --table=users

# Tabellenname und Plugin angeben
php webman make:crud --table=users --plugin=admin

# Pfade angeben
php webman make:crud --table=users --model-path=app/model --controller-path=app/controller

# Keinen Validator generieren
php webman make:crud --table=users --no-validator

# Nicht-interaktiv + Überschreiben
php webman make:crud --table=users --no-interaction --force
```

**Generierte Dateistruktur:**

Modell (`app/model/User.php`):
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

Validator (`app/validation/UserValidator.php`):
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
        'id' => 'Primärschlüssel',
        'username' => 'Benutzername'
    ];

    protected array $scenes = [
        'create' => ['username', 'nickname'],
        'update' => ['id', 'username'],
        'delete' => ['id'],
        'detail' => ['id'],
    ];
}
```

**Hinweise:**
- Validator-Generierung wird übersprungen, wenn `webman/validation` nicht installiert oder aktiviert ist (Installation mit `composer require webman/validation`)
- Validator-`attributes` werden automatisch aus Datenbankfeldkommentaren generiert; ohne Kommentare keine `attributes`
- Validator-Fehlermeldungen unterstützen i18n; Sprache wird aus `config('translation.locale')` gewählt

<a name="make-middleware"></a>
### make:middleware

Middleware-Klasse generieren und automatisch in `config/middleware.php` (bzw. `plugin/<plugin>/config/middleware.php` für Plugins) registrieren.

**Verwendung:**
```bash
php webman make:middleware <name>
```

**Argumente:**

| Argument | Erforderlich | Beschreibung |
|----------|----------|-------------|
| `name` | Ja | Middleware-Name |

**Optionen:**

| Option | Kurzform | Beschreibung |
|--------|----------|-------------|
| `--plugin` | `-p` | Middleware im angegebenen Plugin-Verzeichnis generieren |
| `--path` | `-P` | Zielverzeichnis (relativ zum Projektstamm) |
| `--force` | `-f` | Vorhandene Datei überschreiben |

**Beispiele:**
```bash
# Auth-Middleware in app/middleware erstellen
php webman make:middleware Auth

# Im Plugin erstellen
php webman make:middleware Auth -p admin

# Benutzerdefinierter Pfad
php webman make:middleware Auth -P plugin/admin/app/middleware
```

**Generierte Dateistruktur:**
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

**Hinweise:**
- Standardmäßig in `app/middleware/` abgelegt
- Klassenname wird automatisch zur Middleware-Konfigurationsdatei hinzugefügt und aktiviert

<a name="make-command"></a>
### make:command

Konsolenbefehl-Klasse generieren.

**Verwendung:**
```bash
php webman make:command <command-name>
```

**Argumente:**

| Argument | Erforderlich | Beschreibung |
|----------|----------|-------------|
| `command-name` | Ja | Befehlsname im Format `group:action` (z. B. `user:list`) |

**Optionen:**

| Option | Kurzform | Beschreibung |
|--------|----------|-------------|
| `--plugin` | `-p` | Befehl im angegebenen Plugin-Verzeichnis generieren |
| `--path` | `-P` | Zielverzeichnis (relativ zum Projektstamm) |
| `--force` | `-f` | Vorhandene Datei überschreiben |

**Beispiele:**
```bash
# user:list-Befehl in app/command erstellen
php webman make:command user:list

# Im Plugin erstellen
php webman make:command user:list -p admin

# Benutzerdefinierter Pfad
php webman make:command user:list -P plugin/admin/app/command

# Vorhandene Datei überschreiben
php webman make:command user:list -f
```

**Generierte Dateistruktur:**
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

**Hinweise:**
- Standardmäßig in `app/command/` abgelegt

<a name="make-bootstrap"></a>
### make:bootstrap

Bootstrap-Initialisierungsklasse generieren. Die Methode `start` wird beim Prozessstart automatisch aufgerufen, typischerweise für globale Initialisierung.

**Verwendung:**
```bash
php webman make:bootstrap <name>
```

**Argumente:**

| Argument | Erforderlich | Beschreibung |
|----------|----------|-------------|
| `name` | Ja | Bootstrap-Klassenname |

**Optionen:**

| Option | Kurzform | Beschreibung |
|--------|----------|-------------|
| `--plugin` | `-p` | Im angegebenen Plugin-Verzeichnis generieren |
| `--path` | `-P` | Zielverzeichnis (relativ zum Projektstamm) |
| `--force` | `-f` | Vorhandene Datei überschreiben |

**Beispiele:**
```bash
# MyBootstrap in app/bootstrap erstellen
php webman make:bootstrap MyBootstrap

# Ohne automatische Aktivierung erstellen
php webman make:bootstrap MyBootstrap no

# Im Plugin erstellen
php webman make:bootstrap MyBootstrap -p admin

# Benutzerdefinierter Pfad
php webman make:bootstrap MyBootstrap -P plugin/admin/app/bootstrap

# Vorhandene Datei überschreiben
php webman make:bootstrap MyBootstrap -f
```

**Generierte Dateistruktur:**
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

**Hinweise:**
- Standardmäßig in `app/bootstrap/` abgelegt
- Bei Aktivierung wird die Klasse zu `config/bootstrap.php` (bzw. `plugin/<plugin>/config/bootstrap.php` für Plugins) hinzugefügt

<a name="make-process"></a>
### make:process

Benutzerdefinierte Prozess-Klasse generieren und in `config/process.php` für automatischen Start eintragen.

**Verwendung:**
```bash
php webman make:process <name>
```

**Argumente:**

| Argument | Erforderlich | Beschreibung |
|----------|----------|-------------|
| `name` | Ja | Prozess-Klassenname (z. B. MyTcp, MyWebsocket) |

**Optionen:**

| Option | Kurzform | Beschreibung |
|--------|----------|-------------|
| `--plugin` | `-p` | Im angegebenen Plugin-Verzeichnis generieren |
| `--path` | `-P` | Zielverzeichnis (relativ zum Projektstamm) |
| `--force` | `-f` | Vorhandene Datei überschreiben |

**Beispiele:**
```bash
# In app/process erstellen
php webman make:process MyTcp

# Im Plugin erstellen
php webman make:process MyProcess -p admin

# Benutzerdefinierter Pfad
php webman make:process MyProcess -P plugin/admin/app/process

# Vorhandene Datei überschreiben
php webman make:process MyProcess -f
```

**Interaktiver Ablauf:** Nacheinander abgefragt: Port überwachen? → Protokolltyp (websocket/http/tcp/udp/unixsocket) → Überwachungsadresse (IP:Port oder Unix-Socket-Pfad) → Prozessanzahl. Beim HTTP-Protokoll zusätzlich: eingebauter oder benutzerdefinierter Modus.

**Generierte Dateistruktur:**

Nicht überwachender Prozess (nur `onWorkerStart`):
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

TCP/WebSocket-überwachende Prozesse generieren entsprechende `onConnect`-, `onMessage`-, `onClose`-Rückrufvorlagen.

**Hinweise:**
- Standardmäßig in `app/process/` abgelegt; Prozesskonfiguration wird in `config/process.php` geschrieben
- Konfigurationsschlüssel ist snake_case des Klassennamens; schlägt fehl, wenn bereits vorhanden
- HTTP-Eingebauter Modus verwendet die Prozessdatei `app\process\Http`, es wird keine neue Datei generiert
- Unterstützte Protokolle: websocket, http, tcp, udp, unixsocket

## Build und Deployment

<a name="build-phar"></a>
### build:phar

Projekt als PHAR-Archiv packen für Verteilung und Deployment.

**Verwendung:**
```bash
php webman build:phar
```

**Start:**

In das Build-Verzeichnis wechseln und ausführen

```bash
php webman.phar start
```

**Hinweise:**
* Das gepackte Projekt unterstützt kein Reload; zum Aktualisieren des Codes bitte Restart verwenden

* Um große Dateigröße und Speicherverbrauch zu vermeiden, können Sie exclude_pattern und exclude_files in config/plugin/webman/console/app.php konfigurieren, um unnötige Dateien auszuschließen.

* Beim Ausführen von webman.phar wird im gleichen Verzeichnis ein runtime-Verzeichnis für Logs und temporäre Dateien erstellt.

* Wenn Ihr Projekt eine .env-Datei verwendet, platzieren Sie .env im gleichen Verzeichnis wie webman.phar.

* webman.phar unterstützt keine benutzerdefinierten Prozesse unter Windows

* Speichern Sie niemals vom Benutzer hochgeladene Dateien im PHAR-Paket; der Betrieb über phar:// ist gefährlich (PHAR-Deserialisierungs-Schwachstelle). Benutzer-Uploads müssen separat außerhalb des PHAR auf der Festplatte gespeichert werden. Siehe unten.

* Wenn Ihre Anwendung Dateien ins öffentliche Verzeichnis hochladen muss, extrahieren Sie das public-Verzeichnis an den gleichen Ort wie webman.phar und konfigurieren Sie config/app.php:
```php
'public_path' => base_path(false) . DIRECTORY_SEPARATOR . 'public',
```
Verwenden Sie die Hilfsfunktion public_path($relative_path), um den tatsächlichen Pfad des öffentlichen Verzeichnisses zu erhalten.


<a name="build-bin"></a>
### build:bin

Projekt als eigenständige Binärdatei mit eingebetteter PHP-Laufzeit packen. Keine PHP-Installation in der Zielumgebung erforderlich.

**Verwendung:**
```bash
php webman build:bin [version]
```

**Argumente:**

| Argument | Erforderlich | Beschreibung |
|----------|----------|-------------|
| `version` | Nein | PHP-Version (z. B. 8.1, 8.2), Standard: aktuelle PHP-Version, mindestens 8.1 |

**Beispiele:**
```bash
# Aktuelle PHP-Version verwenden
php webman build:bin

# PHP 8.2 angeben
php webman build:bin 8.2
```

**Start:**

In das Build-Verzeichnis wechseln und ausführen

```bash
./webman.bin start
```

**Hinweise:**
* Dringend empfohlen: Lokale PHP-Version sollte der Build-Version entsprechen (z. B. PHP 8.1 lokal → mit 8.1 bauen), um Kompatibilitätsprobleme zu vermeiden
* Der Build lädt PHP-8-Quellcode herunter, installiert aber nicht lokal; beeinflusst die lokale PHP-Umgebung nicht
* webman.bin läuft derzeit nur auf x86_64 Linux; nicht unter macOS unterstützt
* Das gepackte Projekt unterstützt kein Reload; zum Aktualisieren des Codes bitte Restart verwenden
* .env wird standardmäßig nicht mitgepackt (gesteuert durch exclude_files in config/plugin/webman/console/app.php); platzieren Sie .env beim Start im gleichen Verzeichnis wie webman.bin
* Im webman.bin-Verzeichnis wird ein runtime-Verzeichnis für Logdateien erstellt
* webman.bin liest keine externe php.ini; für benutzerdefinierte php.ini-Einstellungen verwenden Sie custom_ini in config/plugin/webman/console/app.php
* Schließen Sie unnötige Dateien über config/plugin/webman/console/app.php aus, um eine zu große Paketgröße zu vermeiden
* Der Binär-Build unterstützt keine Swoole-Koroutinen
* Speichern Sie niemals vom Benutzer hochgeladene Dateien im Binärpaket; der Betrieb über phar:// ist gefährlich (PHAR-Deserialisierungs-Schwachstelle). Benutzer-Uploads müssen separat außerhalb des Pakets auf der Festplatte gespeichert werden.
* Wenn Ihre Anwendung Dateien ins öffentliche Verzeichnis hochladen muss, extrahieren Sie das public-Verzeichnis an den gleichen Ort wie webman.bin und konfigurieren Sie config/app.php wie folgt, dann neu bauen:
```php
'public_path' => base_path(false) . DIRECTORY_SEPARATOR . 'public',
```

<a name="install"></a>
### install

Webman-Framework-Installationsskript ausführen (ruft `\Webman\Install::install()` auf), für Projektinitialisierung.

**Verwendung:**
```bash
php webman install
```

## Hilfsbefehle

<a name="version"></a>
### version

workerman/webman-framework-Version anzeigen.

**Verwendung:**
```bash
php webman version
```

**Hinweise:** Version wird aus `vendor/composer/installed.php` gelesen; gibt Fehler zurück, wenn nicht lesbar.

<a name="fix-disable-functions"></a>
### fix-disable-functions

`disable_functions` in php.ini reparieren und von Webman benötigte Funktionen entfernen.

**Verwendung:**
```bash
php webman fix-disable-functions
```

**Hinweise:** Entfernt folgende Funktionen (und Präfix-Übereinstimmungen) aus `disable_functions`: `stream_socket_server`, `stream_socket_accept`, `stream_socket_client`, `pcntl_*`, `posix_*`, `proc_*`, `shell_exec`, `exec`. Überspringt, wenn php.ini nicht gefunden oder `disable_functions` leer ist. **Modifiziert die php.ini-Datei direkt**; Backup wird empfohlen.

<a name="route-list"></a>
### route:list

Alle registrierten Routen in Tabellenform anzeigen.

**Verwendung:**
```bash
php webman route:list
```

**Ausgabe-Beispiel:**
```
+-------+--------+-----------------------------------------------+------------+------+
| URI   | Method | Callback                                      | Middleware | Name |
+-------+--------+-----------------------------------------------+------------+------+
| /user | GET    | ["app\\controller\\UserController","index"] | null       |      |
| /api  | POST   | Closure                                      | ["Auth"]   | api  |
+-------+--------+-----------------------------------------------+------------+------+
```

**Ausgabespalten:** URI, Method, Callback, Middleware, Name. Closure-Callbacks werden als „Closure“ angezeigt.

## App-Plugin-Verwaltung (app-plugin:*)

<a name="app-plugin-create"></a>
### app-plugin:create

Neues App-Plugin erstellen mit vollständiger Verzeichnisstruktur und Basisdateien unter `plugin/<name>`.

**Verwendung:**
```bash
php webman app-plugin:create <name>
```

**Argumente:**

| Argument | Erforderlich | Beschreibung |
|----------|----------|-------------|
| `name` | Ja | Plugin-Name; muss `[a-zA-Z0-9][a-zA-Z0-9_-]*` entsprechen, darf kein `/` oder `\` enthalten |

**Beispiele:**
```bash
# App-Plugin namens foo erstellen
php webman app-plugin:create foo

# Plugin mit Bindestrich erstellen
php webman app-plugin:create my-app
```

**Generierte Verzeichnisstruktur:**
```
plugin/<name>/
├── app/
│   ├── controller/IndexController.php
│   ├── model/
│   ├── middleware/
│   ├── view/index/index.html
│   └── functions.php
├── config/          # app.php, route.php, menu.php, etc.
├── api/Install.php  # Install-/Uninstall-/Update-Hooks
├── public/
└── install.sql
```

**Hinweise:**
- Plugin wird unter `plugin/<name>/` erstellt; schlägt fehl, wenn Verzeichnis bereits existiert

<a name="app-plugin-install"></a>
### app-plugin:install

App-Plugin installieren, führt `plugin/<name>/api/Install::install($version)` aus.

**Verwendung:**
```bash
php webman app-plugin:install <name>
```

**Argumente:**

| Argument | Erforderlich | Beschreibung |
|----------|----------|-------------|
| `name` | Ja | Plugin-Name; muss `[a-zA-Z0-9][a-zA-Z0-9_-]*` entsprechen |

**Beispiele:**
```bash
php webman app-plugin:install foo
```

<a name="app-plugin-uninstall"></a>
### app-plugin:uninstall

App-Plugin deinstallieren, führt `plugin/<name>/api/Install::uninstall($version)` aus.

**Verwendung:**
```bash
php webman app-plugin:uninstall <name>
```

**Argumente:**

| Argument | Erforderlich | Beschreibung |
|----------|----------|-------------|
| `name` | Ja | Plugin-Name |

**Optionen:**

| Option | Kurzform | Beschreibung |
|--------|----------|-------------|
| `--yes` | `-y` | Bestätigung überspringen, direkt ausführen |

**Beispiele:**
```bash
php webman app-plugin:uninstall foo
php webman app-plugin:uninstall foo -y
```

<a name="app-plugin-update"></a>
### app-plugin:update

App-Plugin aktualisieren, führt nacheinander `Install::beforeUpdate($from, $to)` und `Install::update($from, $to, $context)` aus.

**Verwendung:**
```bash
php webman app-plugin:update <name>
```

**Argumente:**

| Argument | Erforderlich | Beschreibung |
|----------|----------|-------------|
| `name` | Ja | Plugin-Name |

**Optionen:**

| Option | Kurzform | Beschreibung |
|--------|----------|-------------|
| `--from` | `-f` | Von-Version, Standard: aktuelle Version |
| `--to` | `-t` | Bis-Version, Standard: aktuelle Version |

**Beispiele:**
```bash
php webman app-plugin:update foo
php webman app-plugin:update foo --from 1.0.0 --to 1.1.0
```

<a name="app-plugin-zip"></a>
### app-plugin:zip

App-Plugin als ZIP-Datei packen, Ausgabe nach `plugin/<name>.zip`.

**Verwendung:**
```bash
php webman app-plugin:zip <name>
```

**Argumente:**

| Argument | Erforderlich | Beschreibung |
|----------|----------|-------------|
| `name` | Ja | Plugin-Name |

**Beispiele:**
```bash
php webman app-plugin:zip foo
```

**Hinweise:**
- Schließt automatisch `node_modules`, `.git`, `.idea`, `.vscode`, `__pycache__` usw. aus

## Plugin-Verwaltung (plugin:*)

<a name="plugin-create"></a>
### plugin:create

Neues Webman-Plugin erstellen (Composer-Paketform), erzeugt `config/plugin/<name>`-Konfigurationsverzeichnis und `vendor/<name>`-Plugin-Quellverzeichnis.

**Verwendung:**
```bash
php webman plugin:create <name>
php webman plugin:create --name <name>
```

**Argumente:**

| Argument | Erforderlich | Beschreibung |
|----------|----------|-------------|
| `name` | Ja | Plugin-Paketname im Format `vendor/package` (z. B. `foo/my-admin`); muss Composer-Paketnamenskonvention entsprechen |

**Beispiele:**
```bash
php webman plugin:create foo/my-admin
php webman plugin:create --name foo/my-admin
```

**Generierte Struktur:**
- `config/plugin/<name>/app.php`: Plugin-Konfiguration (enthält `enable`-Schalter)
- `vendor/<name>/composer.json`: Plugin-Paketdefinition
- `vendor/<name>/src/`: Plugin-Quellverzeichnis
- Fügt automatisch PSR-4-Mapping zur Projektstamm-`composer.json` hinzu
- Führt `composer dumpautoload` zum Aktualisieren des Autoloadings aus

**Hinweise:**
- Name muss im Format `vendor/package` sein: Kleinbuchstaben, Zahlen, `-`, `_`, `.`, und muss ein `/` enthalten
- Schlägt fehl, wenn `config/plugin/<name>` oder `vendor/<name>` bereits existiert
- Fehler, wenn sowohl Argument als auch `--name` mit unterschiedlichen Werten angegeben werden

<a name="plugin-install"></a>
### plugin:install

Plugin-Installationsskript ausführen (`Install::install()`), kopiert Plugin-Ressourcen ins Projektverzeichnis.

**Verwendung:**
```bash
php webman plugin:install <name>
php webman plugin:install --name <name>
```

**Argumente:**

| Argument | Erforderlich | Beschreibung |
|----------|----------|-------------|
| `name` | Ja | Plugin-Paketname im Format `vendor/package` (z. B. `foo/my-admin`) |

**Optionen:**

| Option | Beschreibung |
|--------|-------------|
| `--name` | Plugin-Name als Option angeben; entweder diese oder das Argument verwenden |

**Beispiele:**
```bash
php webman plugin:install foo/my-admin
php webman plugin:install --name foo/my-admin
```

<a name="plugin-uninstall"></a>
### plugin:uninstall

Plugin-Deinstallationsskript ausführen (`Install::uninstall()`), entfernt Plugin-Ressourcen aus dem Projekt.

**Verwendung:**
```bash
php webman plugin:uninstall <name>
php webman plugin:uninstall --name <name>
```

**Argumente:**

| Argument | Erforderlich | Beschreibung |
|----------|----------|-------------|
| `name` | Ja | Plugin-Paketname im Format `vendor/package` |

**Optionen:**

| Option | Beschreibung |
|--------|-------------|
| `--name` | Plugin-Name als Option angeben; entweder diese oder das Argument verwenden |

**Beispiele:**
```bash
php webman plugin:uninstall foo/my-admin
```

<a name="plugin-enable"></a>
### plugin:enable

Plugin aktivieren, setzt `enable` in `config/plugin/<name>/app.php` auf `true`.

**Verwendung:**
```bash
php webman plugin:enable <name>
php webman plugin:enable --name <name>
```

**Argumente:**

| Argument | Erforderlich | Beschreibung |
|----------|----------|-------------|
| `name` | Ja | Plugin-Paketname im Format `vendor/package` |

**Optionen:**

| Option | Beschreibung |
|--------|-------------|
| `--name` | Plugin-Name als Option angeben; entweder diese oder das Argument verwenden |

**Beispiele:**
```bash
php webman plugin:enable foo/my-admin
```

<a name="plugin-disable"></a>
### plugin:disable

Plugin deaktivieren, setzt `enable` in `config/plugin/<name>/app.php` auf `false`.

**Verwendung:**
```bash
php webman plugin:disable <name>
php webman plugin:disable --name <name>
```

**Argumente:**

| Argument | Erforderlich | Beschreibung |
|----------|----------|-------------|
| `name` | Ja | Plugin-Paketname im Format `vendor/package` |

**Optionen:**

| Option | Beschreibung |
|--------|-------------|
| `--name` | Plugin-Name als Option angeben; entweder diese oder das Argument verwenden |

**Beispiele:**
```bash
php webman plugin:disable foo/my-admin
```

<a name="plugin-export"></a>
### plugin:export

Plugin-Konfiguration und angegebene Verzeichnisse aus dem Projekt nach `vendor/<name>/src/` exportieren und `Install.php` für Verpackung und Veröffentlichung generieren.

**Verwendung:**
```bash
php webman plugin:export <name> [--source=path]...
php webman plugin:export --name <name> [--source=path]...
```

**Argumente:**

| Argument | Erforderlich | Beschreibung |
|----------|----------|-------------|
| `name` | Ja | Plugin-Paketname im Format `vendor/package` |

**Optionen:**

| Option | Kurzform | Beschreibung |
|--------|----------|-------------|
| `--name` | | Plugin-Name als Option angeben; entweder diese oder das Argument verwenden |
| `--source` | `-s` | Zu exportierender Pfad (relativ zum Projektstamm); kann mehrfach angegeben werden |

**Beispiele:**
```bash
# Plugin exportieren, Standard enthält config/plugin/<name>
php webman plugin:export foo/my-admin

# Zusätzlich app, config usw. exportieren
php webman plugin:export foo/my-admin --source app --source config
php webman plugin:export --name foo/my-admin -s app -s config
```

**Hinweise:**
- Plugin-Name muss Composer-Paketnamenskonvention entsprechen (`vendor/package`)
- Wenn `config/plugin/<name>` existiert und nicht in `--source` ist, wird es automatisch zur Exportliste hinzugefügt
- Exportierte `Install.php` enthält `pathRelation` für die Verwendung durch `plugin:install` / `plugin:uninstall`
- `plugin:install` und `plugin:uninstall` erfordern, dass das Plugin in `vendor/<name>` existiert, mit `Install`-Klasse und `WEBMAN_PLUGIN`-Konstante

## Dienstverwaltung

<a name="start"></a>
### start

Webman-Worker-Prozesse starten. Standardmäßig DEBUG-Modus (Vordergrund).

**Verwendung:**
```bash
php webman start
```

**Optionen:**

| Option | Kurzform | Beschreibung |
|--------|----------|-------------|
| `--daemon` | `-d` | Im DAEMON-Modus starten (Hintergrund) |

<a name="stop"></a>
### stop

Webman-Worker-Prozesse stoppen.

**Verwendung:**
```bash
php webman stop
```

**Optionen:**

| Option | Kurzform | Beschreibung |
|--------|----------|-------------|
| `--graceful` | `-g` | Sanftes Stoppen; warten, bis aktuelle Anfragen abgeschlossen sind, bevor beendet wird |

<a name="restart"></a>
### restart

Webman-Worker-Prozesse neu starten.

**Verwendung:**
```bash
php webman restart
```

**Optionen:**

| Option | Kurzform | Beschreibung |
|--------|----------|-------------|
| `--daemon` | `-d` | Nach Neustart im DAEMON-Modus laufen |
| `--graceful` | `-g` | Sanftes Stoppen vor Neustart |

<a name="reload"></a>
### reload

Code ohne Ausfallzeit neu laden. Für Hot-Reload nach Code-Updates.

**Verwendung:**
```bash
php webman reload
```

**Optionen:**

| Option | Kurzform | Beschreibung |
|--------|----------|-------------|
| `--graceful` | `-g` | Sanftes Reload; warten, bis aktuelle Anfragen abgeschlossen sind, bevor neu geladen wird |

<a name="status"></a>
### status

Worker-Prozess-Laufstatus anzeigen.

**Verwendung:**
```bash
php webman status
```

**Optionen:**

| Option | Kurzform | Beschreibung |
|--------|----------|-------------|
| `--live` | `-d` | Details anzeigen (Echtzeit-Status) |

<a name="connections"></a>
### connections

Worker-Prozessverbindungsinformationen abrufen.

**Verwendung:**
```bash
php webman connections
```
