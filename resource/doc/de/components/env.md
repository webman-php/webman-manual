# vlucas/phpdotenv

## Beschreibung
`vlucas/phpdotenv` ist eine Komponente zum Laden von Umgebungsvariablen und dient dazu, Konfigurationen für verschiedene Umgebungen (wie Entwicklungsumgebung, Testumgebung usw.) zu unterscheiden.

## Projektadresse
https://github.com/vlucas/phpdotenv

## Installation
```php
composer require vlucas/phpdotenv
```

## Verwendung

#### Erstellen Sie eine neue Datei namens `.env` im Stammverzeichnis des Projekts
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
> Es wird empfohlen, die Datei `.env` in die `.gitignore`-Liste aufzunehmen, um zu vermeiden, dass sie dem Code-Repository hinzugefügt wird. Fügen Sie dem Code-Repository eine Beispielkonfigurationsdatei mit dem Namen `.env.example` hinzu, und kopieren Sie diese beim Projektdeploy als`.env`. Passen Sie dann die Konfiguration in `.env` je nach aktueller Umgebung an. Auf diese Weise können verschiedene Konfigurationen je nach Umgebung im Projekt geladen werden.

> **Anmerkung**
> `vlucas/phpdotenv` kann in der PHP-TS-Version (Thread-sichere Version) Fehler aufweisen. Verwenden Sie daher die NTS-Version (nicht Thread-sichere Version).
> Die aktuelle PHP-Version kann mit `php -v` überprüft werden.

## Weitere Informationen
Besuchen Sie https://github.com/vlucas/phpdotenv
