# Migration Datenbank-Migrationswerkzeug Phinx

## Erklärung

Phinx ermöglicht es Entwicklern, Datenbanken auf einfache Weise zu ändern und zu warten. Es vermeidet das manuelle Schreiben von SQL-Anweisungen und verwendet eine leistungsstarke PHP-API zur Verwaltung von Datenbankmigrationen. Entwickler können ihre Datenbankmigrationen mit Versionskontrolle verwalten. Phinx kann Daten zwischen verschiedenen Datenbanken einfach migrieren. Es kann auch verfolgen, welche Migrationskripte ausgeführt wurden, sodass Entwickler sich keine Sorgen mehr um den Zustand der Datenbank machen müssen und sich stattdessen darauf konzentrieren können, ein besseres System zu entwickeln.

## Projektadresse

https://github.com/cakephp/phinx

## Installation

```php
composer require robmorgan/phinx
```

## Offizielle chinesische Dokumentation

Für detailliertere Informationen können Sie die offizielle chinesische Dokumentation überprüfen. Hier wird nur erläutert, wie man es in webman konfiguriert und verwendet.

https://tsy12321.gitbooks.io/phinx-doc/content/

## Verzeichnisstruktur der Migrationsdateien

```text
.
├── app                           Anwendungsverzeichnis
│   ├── controller                Controller-Verzeichnis
│   │   └── Index.php             Controller
│   ├── model                     Modelverzeichnis
......
├── database                      Datenbankdateien
│   ├── migrations                Migrationsdateien
│   │   └── 20180426073606_create_user_table.php
│   ├── seeds                     Testdaten
│   │   └── UserSeeder.php
......
```

## phinx.php Konfiguration

Erstellen Sie eine phinx.php-Datei im Stammverzeichnis des Projekts.

```php
<?php
return [
    "paths" => [
        "migrations" => "database/migrations",
        "seeds"      => "database/seeds"
    ],
    "environments" => [
        "default_migration_table" => "phinxlog",
        "default_database"        => "dev",
        "default_environment"     => "dev",
        "dev" => [
            "adapter" => "DB_CONNECTION",
            "host"    => "DB_HOST",
            "name"    => "DB_DATABASE",
            "user"    => "DB_USERNAME",
            "pass"    => "DB_PASSWORD",
            "port"    => "DB_PORT",
            "charset" => "utf8"
        ]
    ]
];
```

## Verwendungsempfehlung

Sobald die Migrationsdatei zusammengeführt wurde, darf sie nicht mehr geändert werden. Bei Problemen muss eine neue Änderung oder Löschoperation durchgeführt werden.

#### Benennungskonvention für Dateien zur Erstellung von Tabellen

`{Zeit(automatisch erstellt)}_create_{kleingeschriebener Tabellenname auf Englisch}`

#### Benennungskonvention für Dateien zur Änderung von Tabellen

`{Zeit(automatisch erstellt)}_modify_{kleingeschriebener Tabellenname+kleines englisches zu änderndes Element}`

#### Benennungskonvention für Dateien zum Löschen von Tabellen

`{Zeit(automatisch erstellt)}_delete_{kleingeschriebener Tabellenname+kleines englisches zu löschendes Element}`

#### Benennungskonvention für Datenbefüllungsdateien

`{Zeit(automatisch erstellt)}_fill_{kleingeschriebener Tabellenname+kleines englisches zu änderndes Element}`
