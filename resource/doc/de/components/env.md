# webman

## Beschreibung
`vlucas/phpdotenv` ist ein Komponente zum Laden von Umgebungsvariablen, die dazu dient, Konfigurationen für verschiedene Umgebungen (z. B. Entwicklungsumgebung, Testumgebung usw.) zu unterscheiden.

## Projektadresse
https://github.com/vlucas/phpdotenv

## Installation
```php
composer require vlucas/phpdotenv
```

## Verwendung

#### Erstellen Sie eine `.env`-Datei im Stammverzeichnis des Projekts
**.env**
```
DB_HOST = 127.0.0.1
DB_PORT = 3306
DB_NAME = test
DB_USER = foo
DB_PASSWORD = 123456
```

#### Ändern Sie die Konfigurationsdatei
**config/database.php**
```php
return [
    // Standarddatenbank
    'default' => 'mysql',

    // Verschiedene Datenbankkonfigurationen
    'connections' => [
        'mysql' => [
            'driver'      => 'mysql',
            'host'        => getenv('DB_HOST'),
            'port'        => getenv('DB_PORT'),
            'database'    => getenv('DB_NAME'),
            'username'    => getenv('DB_USER'),
            'password'    => getenv('DB_PASSWORD'),
            'unix_socket' => '',
            'charset'     => 'utf8',
            'collation'   => 'utf8_unicode_ci',
            'prefix'      => '',
            'strict'      => true,
            'engine'      => null,
        ],
    ],
];
```

> **Hinweis**
> Es wird empfohlen, die `.env`-Datei in die `.gitignore`-Liste aufzunehmen, um zu verhindern, dass sie dem Code-Repository hinzugefügt wird. Fügen Sie dem Repository eine Beispielkonfigurationsdatei `.env.example` hinzu und kopieren Sie diese bei der Bereitstellung des Projekts als `.env`. Passen Sie die Konfiguration in der `.env` an die aktuelle Umgebung an. Auf diese Weise können verschiedene Konfigurationen je nach Umgebung im Projekt geladen werden.

> **Achtung**
> `vlucas/phpdotenv` kann in der PHP TS-Version (Thread-Safe-Version) Fehler aufweisen. Verwenden Sie die NTS-Version (Non-Thread-Safe-Version).
> Die aktuelle PHP-Version können Sie durch Ausführen von `php -v` überprüfen.

## Weitere Informationen

Besuchen Sie https://github.com/vlucas/phpdotenv
