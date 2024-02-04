# Plugin-Anwendung
Jedes Plugin ist eine vollständige Anwendung, deren Quellcode im Verzeichnis `{Hauptprojekt}/plugin` platziert wird.

> **Hinweis**
> Mit dem Befehl `php webman app-plugin:create {Plugin-Name}` (erfordert webman/console>=1.2.16) kann lokal ein Anwendungs-Plugin erstellt werden.
> Zum Beispiel wird mit `php webman app-plugin:create cms` die folgende Verzeichnisstruktur erstellt:

```
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

Wir sehen, dass ein Anwendungs-Plugin die gleiche Verzeichnisstruktur und Konfigurationsdateien wie webman hat. Tatsächlich ist die Entwicklung eines Anwendungs-Plugins im Wesentlichen ähnlich wie die Entwicklung eines webman-Projekts, es gibt jedoch einige wenige Dinge zu beachten.

## Namensraum
Das Verzeichnis und der Name des Plugins folgen dem PSR4-Standard. Da die Plugins alle im Plugin-Verzeichnis liegen, beginnt der Namensraum immer mit `plugin`, z. B. `plugin\cms\app\controller\UserController`. Hier ist `cms` das Hauptverzeichnis des Plugin-Quellcodes.

## URL-Zugriff
Die URL-Adresspfade der Anwendungs-Plugins beginnen immer mit `/app`, z. B. ist die URL des `plugin\cms\app\controller\UserController` `http://127.0.0.1:8787/app/cms/user`.

## Statische Dateien
Statische Dateien werden unter `plugin/{Plugin}/public` platziert. Wenn Sie beispielsweise auf `http://127.0.0.1:8787/app/cms/avatar.png` zugreifen, wird tatsächlich die Datei `plugin/cms/public/avatar.png` abgerufen.

## Konfigurationsdateien
Die Konfigurationen des Plugins sind die gleichen wie die eines normalen webman-Projekts. Allerdings betreffen die Konfigurationen des Plugins in der Regel nur das aktuelle Plugin und haben in der Regel keine Auswirkungen auf das Hauptprojekt.
Zum Beispiel wirkt sich der Wert von `plugin.cms.app.controller_suffix` nur auf die Controller-Suffix des Plugins aus, nicht auf das Hauptprojekt.
Zum Beispiel wirkt sich der Wert von `plugin.cms.app.controller_reuse` nur darauf aus, ob der Controller des Plugins wiederverwendet wird oder nicht, hat aber keine Auswirkungen auf das Hauptprojekt.
Zum Beispiel wirkt sich der Wert von `plugin.cms.middleware` nur auf die Middleware des Plugins aus, nicht auf das Hauptprojekt.
Zum Beispiel wirkt sich der Wert von `plugin.cms.view` nur auf die von dem Plugin verwendete Ansicht aus, nicht auf das Hauptprojekt.
Zum Beispiel wirkt sich der Wert von `plugin.cms.container` nur auf den vom Plugin verwendeten Container aus, nicht auf das Hauptprojekt.
Zum Beispiel wirkt sich der Wert von `plugin.cms.exception` nur auf die Ausnahmeverarbeitungsklasse des Plugins aus, nicht auf das Hauptprojekt.

Da die Routen jedoch global sind, hat die Konfiguration der Routen des Plugins auch globale Auswirkungen.

## Konfiguration abrufen
Die Methode zum Abrufen einer bestimmten Plugin-Konfiguration lautet `config('plugin.{Plugin}.{Konkrete Konfiguration}');`, z. B. das Abrufen aller Konfigurationen von `plugin/cms/config/app.php` erfolgt über `config('plugin.cms.app')`.
Ebenso können das Hauptprojekt oder andere Plugins `config('plugin.cms.xxx')` verwenden, um die Konfiguration des CMS-Plugins abzurufen.

## Nicht unterstützte Konfigurationen
Anwendungs-Plugins unterstützen keine `server.php`, `session.php`-Konfigurationen und keine `app.request_class`, `app.public_path`, `app.runtime_path`-Konfigurationen.

## Datenbank
Plugins können ihre eigene Datenbank konfigurieren, z. B. der Inhalt von `plugin/cms/config/database.php` ist wie folgt:
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
Die Verwendung erfolgt über `Db::connection('plugin.{Plugin}.{Verbindungsnamen}');`, z. B.
```php
use support\Db;
Db::connection('plugin.cms.mysql')->table('user')->first();
Db::connection('plugin.cms.admin')->table('admin')->first();
```

Wenn Sie die Datenbank des Hauptprojekts verwenden möchten, können Sie diese direkt verwenden, z. B.
```php
use support\Db;
Db::table('user')->first();
// Angenommen, das Hauptprojekt hat auch eine Verbindung namens admin konfiguriert
Db::connection('admin')->table('admin')->first();
```

> **Hinweis**
> Thinkorm wird in ähnlicher Weise verwendet.

## Redis
Die Verwendung von Redis ist ähnlich wie die Verwendung der Datenbank, z. B. in `plugin/cms/config/redis.php`:
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
Die Verwendung erfolgt wie folgt:
```php
use support\Redis;
Redis::connection('plugin.cms.default')->get('key');
Redis::connection('plugin.cms.cache')->get('key');
```

Ebenso, wenn Sie die Redis-Konfiguration des Hauptprojekts wiederverwenden möchten:
```php
use support\Redis;
Redis::get('key');
// Angenommen, das Hauptprojekt hat auch eine Verbindung namens cache konfiguriert
Redis::connection('cache')->get('key');
```

## Protokollierung
Die Verwendung der Log-Klasse ähnelt ebenfalls der Verwendung der Datenbank, z. B.:
```php
use support\Log;
Log::channel('plugin.admin.default')->info('test');
```

Wenn Sie die Protokollierungskonfiguration des Hauptprojekts wiederverwenden möchten, verwenden Sie sie direkt, z. B.
```php
use support\Log;
Log::info('Protokolleinträge');
// Angenommen, das Hauptprojekt hat eine Test-Protokollierungskonfiguration
Log::channel('test')->info('Protokolleinträge');
```

# Installation und Deinstallation von Anwendungs-Plugins
Zur Installation eines Anwendungs-Plugins muss das Plugin-Verzeichnis in das Verzeichnis `{Hauptprojekt}/plugin` kopiert werden. Ein Reload oder Neustart ist erforderlich, um dies zu aktivieren.
Zum Deinstallieren löschen Sie einfach das entsprechende Plugin-Verzeichnis im Verzeichnis `{Hauptprojekt}/plugin`.
