# Migration Datenbank-Migrationswerkzeug Phinx

## Beschreibung

Phinx ermöglicht es Entwicklern, Datenbanken auf einfache Weise zu ändern und zu warten. Es vermeidet das manuelle Schreiben von SQL-Anweisungen und verwendet eine leistungsstarke PHP-API zur Verwaltung von Datenbankmigrationen. Entwickler können ihre Datenbankmigrationen mit Versionskontrolle verwalten. Phinx ermöglicht es, Daten zwischen verschiedenen Datenbanken einfach zu migrieren. Außerdem können verfolgt werden, welche Migrations-Skripte ausgeführt wurden, sodass Entwickler sich keine Sorgen mehr um den Zustand der Datenbank machen müssen und sich stattdessen darauf konzentrieren können, ein besseres System zu entwickeln.

## Projektadresse

https://github.com/cakephp/phinx

## Installation

  ```php
  composer require robmorgan/phinx
  ```

## Offizielle chinesische Dokumentation Adresse

Für ausführlichere Informationen können Sie die offizielle chinesische Dokumentation besuchen. Hier wird nur erklärt, wie Sie Phinx in webman konfigurieren und verwenden können.

https://tsy12321.gitbooks.io/phinx-doc/content/

## Verzeichnisstruktur für Migrationsdateien

```
.
├── app                           Anwendungsverzeichnis
│   ├── controller                Controller-Verzeichnis
│   │   └── Index.php             Controller
│   ├── model                     Modelverzeichnis
......
├── database                      Datenbankdateien
│   ├── migrations                Migrationsdateien
│   │   └── 20180426073606_create_user_table.php
│   ├── seeds                     Testdaten
│   │   └── UserSeeder.php
......
```

## phinx.php Konfiguration

Erstellen Sie im Stammverzeichnis des Projekts die Datei phinx.php

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

## Verwendungsempfehlungen

Sobald Migrationsdateien zusammengeführt wurden, dürfen sie nicht mehr geändert werden. Wenn Probleme auftreten, müssen neue Änderungs- oder Löschungsoperationen erstellt werden.

#### Namenskonvention für Dateien zur Erstellung von Tabellen

`{Zeit (automatisch erstellt)}_create_{Kleinbuchstaben des Tabellennamens in Englisch}`

#### Namenskonvention für Dateien zur Modifikation von Tabellen

`{Zeit (automatisch erstellt)}_modify_{Kleinbuchstaben des Tabellennamens in Englisch+zusätzlicher kleingeschriebener Modifikationspunkt}`

### Namenskonvention für Dateien zum Löschen von Tabellen

`{Zeit (automatisch erstellt)}_delete_{Kleinbuchstaben des Tabellennamens in Englisch+zusätzlicher kleingeschriebener Modifikationspunkt}`

### Namenskonvention für Datenfüllungsdateien

`{Zeit (automatisch erstellt)}_fill_{Kleinbuchstaben des Tabellennamens in Englisch+zusätzlicher kleingeschriebener Modifikationspunkt}`
