# Plugin-Anwendung
Jedes Applikations-Plugin ist eine vollständige Applikation, deren Quellcode im Verzeichnis `{Hauptprojekt}/plugin` liegt.

> **Hinweis**
> Mit dem Befehl `php webman app-plugin:create {Plugin-Name}` (webman/console>=1.2.16 erforderlich) können Sie lokal ein Applikations-Plugin erstellen.
> Zum Beispiel erstellt `php webman app-plugin:create cms` die folgende Verzeichnisstruktur:

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

Wir sehen, dass ein Applikations-Plugin die gleiche Verzeichnisstruktur und Konfigurationsdateien wie webman hat. Tatsächlich ist die Entwicklung eines Applikations-Plugins im Wesentlichen das gleiche wie die Entwicklung eines webman-Projekts. Es gibt jedoch einige Aspekte zu beachten.

## Namensraum
Das Plugin-Verzeichnis und -Namen folgen dem PSR4-Standard. Da die Plugins im Plugin-Verzeichnis platziert sind, beginnen die Namensräume alle mit `plugin`, z.B. `plugin\cms\app\controller\UserController`. Hier ist `cms` das Hauptverzeichnis des Plugin-Quellcodes.

## URL-Zugriff
Die URL-Adressen der Applikations-Plugins beginnen alle mit `/app`. Zum Beispiel ist die URL-Adresse von `plugin\cms\app\controller\UserController` `http://127.0.0.1:8787/app/cms/user`.

## Statische Dateien
Statische Dateien befinden sich im Verzeichnis `plugin/{Plugin}/public`. Wenn also auf `http://127.0.0.1:8787/app/cms/avatar.png` zugegriffen wird, wird tatsächlich die Datei `plugin/cms/public/avatar.png` abgerufen.

## Konfigurationsdateien
Die Konfigurationen des Plugins sind genauso wie bei einem normalen webman-Projekt, jedoch betreffen die Plugin-Konfigurationen in der Regel nur das jeweilige Plugin und haben keine Auswirkungen auf das Hauptprojekt.
Zum Beispiel betrifft der Wert von `plugin.cms.app.controller_suffix` nur das Suffix des Controllers des Plugins und hat keine Auswirkungen auf das Hauptprojekt.
Zum Beispiel betrifft der Wert von `plugin.cms.app.controller_reuse` nur, ob der Controller des Plugins wiederverwendet wird, und hat keine Auswirkungen auf das Hauptprojekt.
Zum Beispiel betrifft der Wert von `plugin.cms.middleware` nur die Middleware des Plugins und hat keine Auswirkungen auf das Hauptprojekt.
Zum Beispiel betrifft der Wert von `plugin.cms.view` nur die vom Plugin verwendete Ansicht und hat keine Auswirkungen auf das Hauptprojekt.
Zum Beispiel betrifft der Wert von `plugin.cms.container` nur den vom Plugin verwendeten Container und hat keine Auswirkungen auf das Hauptprojekt.
Zum Beispiel betrifft der Wert von `plugin.cms.exception` nur die Behandlungsklasse für Ausnahmen des Plugins und hat keine Auswirkungen auf das Hauptprojekt.

Da die Routen jedoch global sind, wirken sich die konfigurierten Routen von Plugins auch global aus.

## Abrufen von Konfigurationen
Die Methode zum Abrufen einer Konfiguration eines Plugins lautet `config('plugin.{Plugin}.{spezifische Konfiguration}');`. Zum Beispiel kann die gesamte Konfiguration von `plugin/cms/config/app.php` mit `config('plugin.cms.app')` abgerufen werden.
Ebenso können das Hauptprojekt oder andere Plugins `config('plugin.cms.xxx')` verwenden, um die Konfiguration des CMS-Plugins abzurufen.

## Nicht unterstützte Konfigurationen
Applikations-Plugins unterstützen keine `server.php`, `session.php`-Konfigurationen und unterstützen nicht die Konfigurationen `app.request_class`, `app.public_path` und `app.runtime_path`.

## Datenbank
Plugins können ihre eigene Datenbank konfigurieren. Zum Beispiel enthält `plugin/cms/config/database.php` den folgenden Inhalt:
```php
return  [
    'default' => 'mysql',
    'connections' => [
        'mysql' => [ // mysql ist der Verbindungsnamen
            'driver'      => 'mysql',
            'host'        => '127.0.0.1',
            'port'        => 3306,
            'database'    => 'Datenbank',
            'username'    => 'Benutzername',
            'password'    => 'Passwort',
            'charset'     => 'utf8mb4',
            'collation'   => 'utf8mb4_general_ci',
        ],
        'admin' => [ // admin ist der Verbindungsnamen
            'driver'      => 'mysql',
            'host'        => '127.0.0.1',
            'port'        => 3306,
            'database'    => 'Datenbank',
            'username'    => 'Benutzername',
            'password'    => 'Passwort',
            'charset'     => 'utf8mb4',
            'collation'   => 'utf8mb4_general_ci',
        ],
    ],
];
```
Der Verweis erfolgt mit `Db::connection('plugin.{Plugin}.{Verbindungsnamen}');`, zum Beispiel
```php
use support\Db;
Db::connection('plugin.cms.mysql')->table('user')->first();
Db::connection('plugin.cms.admin')->table('admin')->first();
```

Wenn die Hauptprojektdatenbank verwendet werden soll, kann sie direkt verwendet werden, zum Beispiel
```php
use support\Db;
Db::table('user')->first();
// Angenommen, das Hauptprojekt hat auch eine Admin-Verbindung konfiguriert
Db::connection('admin')->table('admin')->first();
```

> **Hinweis**
> ThinkORM hat eine ähnliche Verwendung.

## Redis
Die Verwendung von Redis ähnelt der Datenbankverwendung. Zum Beispiel in `plugin/cms/config/redis.php`:
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
Verwendung:
```php
use support\Redis;
Redis::connection('plugin.cms.default')->get('key');
Redis::connection('plugin.cms.cache')->get('key');
```

Ebenso kann bei Bedarf die Redis-Konfiguration des Hauptprojekts wiederverwendet werden:
```php
use support\Redis;
Redis::get('key');
// Angenommen, das Hauptprojekt hat auch eine Cache-Verbindung konfiguriert
Redis::connection('cache')->get('key');
```

## Protokollierung
Die Verwendung des Protokolls ähnelt der Datenbankverwendung:
```php
use support\Log;
Log::channel('plugin.admin.default')->info('test');
```

Wenn die Protokollkonfiguration des Hauptprojekts wiederverwendet werden soll, kann sie direkt verwendet werden:
```php
use support\Log;
Log::info('Log-Eintrag');
// Angenommen, das Hauptprojekt hat eine Test-Protokollkonfiguration
Log::channel('test')->info('Log-Eintrag');
```

# Installieren und Deinstallieren von Applikations-Plugins
Die Installation eines Applikations-Plugins erfolgt, indem das Plugin-Verzeichnis ins Verzeichnis `{Hauptprojekt}/plugin` kopiert wird. Es ist erforderlich, `reload` oder `restart` auszuführen, um die Änderungen wirksam werden zu lassen.
Zum Deinstallieren reicht es aus, das entsprechende Plugin-Verzeichnis im Verzeichnis `{Hauptprojekt}/plugin` zu löschen.
