# Strumento di migrazione del database Phinx

## Descrizione

Phinx consente agli sviluppatori di apportare modifiche e mantenere il database in modo efficiente. Evita la scrittura manuale di istruzioni SQL, utilizzando invece un potente API PHP per gestire le migrazioni del database. Gli sviluppatori possono gestire le proprie migrazioni del database utilizzando il controllo della versione. Phinx facilita il trasferimento dei dati tra database diversi e tiene traccia degli script di migrazione eseguiti, consentendo agli sviluppatori di concentrarsi sulla scrittura di sistemi migliori anziché preoccuparsi dello stato del database.

## Indirizzo del progetto

https://github.com/cakephp/phinx

## Installazione

```php
composer require robmorgan/phinx
```

## Indirizzo della documentazione ufficiale in cinese

Per un utilizzo dettagliato, è possibile consultare la documentazione ufficiale in cinese. Qui si spiega solo come configurare e utilizzare in webman.

https://tsy12321.gitbooks.io/phinx-doc/content/

## Struttura della directory dei file di migrazione

```plaintext
.
├── app                           Directory dell'applicazione
│   ├── controller                Directory dei controller
│   │   └── Index.php             Controller
│   ├── model                     Directory dei modelli
......
├── database                      File di database
│   ├── migrations                File di migrazione
│   │   └── 20180426073606_create_user_table.php
│   ├── seeds                     Dati di test
│   │   └── UserSeeder.php
......
```

## Configurazione phinx.php

Creare un file phinx.php nella directory radice del progetto

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

## Suggerimenti per l'uso

Una volta uniti i file di migrazione, non è consentito modificarli nuovamente. In caso di problemi, è necessario creare un nuovo file di modifica o eliminazione per gestirli.

#### Regole di denominazione dei file di operazioni di creazione delle tabelle

`{time(auto create)}_create_{nome tabella in minuscolo}`

#### Regole di denominazione dei file di operazioni di modifica delle tabelle

`{time(auto create)}_modify_{nome tabella in minuscolo + elemento di modifica in minuscolo}`

#### Regole di denominazione dei file di operazioni di eliminazione delle tabelle

`{time(auto create)}_delete_{nome tabella in minuscolo + elemento di modifica in minuscolo}`

#### Regole di denominazione dei file di popolamento dei dati

`{time(auto create)}_fill_{nome tabella in minuscolo + elemento di modifica in minuscolo}`
