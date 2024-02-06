# Strumento di migrazione del database Phinx

## Descrizione

Phinx consente agli sviluppatori di modificare e mantenere facilmente il database. Evita la scrittura manuale di istruzioni SQL utilizzando una potente API PHP per gestire le migrazioni del database. Gli sviluppatori possono gestire le proprie migrazioni del database utilizzando il controllo di versione. Phinx consente di migrare facilmente i dati tra database diversi. È inoltre possibile tracciare quali script di migrazione sono stati eseguiti, consentendo agli sviluppatori di concentrarsi di più sulla scrittura di sistemi migliori anziché preoccuparsi dello stato del database.

## Indirizzo del progetto

https://github.com/cakephp/phinx

## Installazione

```php
composer require robmorgan/phinx
```

## Documentazione ufficiale in cinese

Per un utilizzo dettagliato, consultare la documentazione ufficiale in cinese. Qui verrà spiegato solo come configurarlo e utilizzarlo in webman.

https://tsy12321.gitbooks.io/phinx-doc/content/

## Struttura della directory dei file di migrazione

```
.
├── app                           Cartella dell'applicazione
│   ├── controller                Directory dei controller
│   │   └── Index.php             Controller
│   ├── model                     Directory dei modelli
......
├── database                      File del database
│   ├── migrations                File di migrazione
│   │   └── 20180426073606_create_user_table.php
│   ├── seeds                     Dati di test
│   │   └── UserSeeder.php
......
```

## Configurazione phinx.php

Creare il file phinx.php nella directory radice del progetto

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

Una volta che i file di migrazione sono stati uniti, non è consentito modificarli di nuovo. In caso di problemi, è necessario creare o eliminare file di operazioni di modifica.

#### Regole di denominazione dei file di creazione della tabella

`{time(creato automaticamente)}_create_{nome tabella in minuscolo}`

#### Regole di denominazione dei file di modifica della tabella

`{time(creato automaticamente)}_modify_{nome tabella in minuscolo+elemento specifico della modifica in minuscolo}`

#### Regole di denominazione dei file di eliminazione della tabella

`{time(creato automaticamente)}_delete_{nome tabella in minuscolo+elemento specifico della modifica in minuscolo}`

#### Regole di denominazione dei file di riempimento dei dati

`{time(creato automaticamente)}_fill_{nome tabella in minuscolo+elemento specifico della modifica in minuscolo}`
