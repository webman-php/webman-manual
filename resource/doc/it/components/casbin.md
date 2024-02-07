# Casbin

## Descrizione

Casbin è un potente e efficiente framework open source per il controllo degli accessi, il cui meccanismo di gestione delle autorizzazioni supporta vari modelli di controllo degli accessi.

## Indirizzo del progetto

https://github.com/teamones-open/casbin

## Installazione

```php
composer require teamones/casbin
```

## Sito web di Casbin

Per ulteriori informazioni sull'uso, consultare la documentazione ufficiale in cinese. Qui verrà spiegato come configurare e utilizzare Casbin in webman.

https://casbin.org/docs/zh-CN/overview

## Struttura delle directory

``` 
.
├── config                        Cartella di configurazione
│   ├── casbin-restful-model.conf File di configurazione del modello di autorizzazione utilizzato
│   ├── casbin.php                Configurazione di casbin
......
├── database                      File del database
│   ├── migrations                File di migrazione
│   │   └── 20210218074218_create_rule_table.php
.....
```

## File di migrazione del database

```php
<?php

use Phinx\Migration\AbstractMigration;

class CreateRuleTable extends AbstractMigration
{
    /**
     * Change Method.
     *
     * Scrivi le tue migrazioni reversibili utilizzando questo metodo.
     */
    public function change()
    {
        $table = $this->table('rule', ['id' => false, 'primary_key' => ['id'], 'engine' => 'InnoDB', 'collation' => 'utf8mb4_general_ci', 'comment' => 'Tabella delle regole']);

        // Aggiungi campi dati
        $table->addColumn('id', 'integer', ['identity' => true, 'signed' => false, 'limit' => 11, 'comment' => 'ID principale'])
            ->addColumn('ptype', 'char', ['default' => '', 'limit' => 8, 'comment' => 'Tipo di regola'])
            ->addColumn('v0', 'string', ['default' => '', 'limit' => 128])
            ->addColumn('v1', 'string', ['default' => '', 'limit' => 128])
            ->addColumn('v2', 'string', ['default' => '', 'limit' => 128])
            ->addColumn('v3', 'string', ['default' => '', 'limit' => 128])
            ->addColumn('v4', 'string', ['default' => '', 'limit' => 128])
            ->addColumn('v5', 'string', ['default' => '', 'limit' => 128]);

        // Esegui la creazione
        $table->create();
    }
}

```

## Configurazione di Casbin

Per la sintassi di configurazione del modello di regole di autorizzazione, consultare: https://casbin.org/docs/zh-CN/syntax-for-models

```php
<?php

return [
    'default' => [
        'model' => [
            'config_type' => 'file',
            'config_file_path' => config_path() . '/casbin-restful-model.conf', // File di configurazione del modello di autorizzazione
            'config_text' => '',
        ],
        'adapter' => [
            'type' => 'model', // modello o adattatore
            'class' => \app\model\Rule::class,
        ],
    ],
    // È possibile configurare più modelli di autorizzazione
    'rbac' => [
        'model' => [
            'config_type' => 'file',
            'config_file_path' => config_path() . '/casbin-rbac-model.conf', // File di configurazione del modello di autorizzazione
            'config_text' => '',
        ],
        'adapter' => [
            'type' => 'model', // modello o adattatore
            'class' => \app\model\RBACRule::class,
        ],
    ],
];
``` 

### Adattatore

Nell'attuale wrapper di composer, l'adattatore implementato è il metodo model di think-orm, per altri orm fare riferimento a vendor/teamones/src/adapters/DatabaseAdapter.php

Successivamente modificare la configurazione

```php
return [
    'default' => [
        'model' => [
            'config_type' => 'file',
            'config_file_path' => config_path() . '/casbin-restful-model.conf', // File di configurazione del modello di autorizzazione
            'config_text' => '',
        ],
        'adapter' => [
            'type' => 'adapter', // Tipo configurato qui come modalità adattatore
            'class' => \app\adapter\DatabaseAdapter::class,
        ],
    ],
];
``` 

## Istruzioni per l'uso

### Importazione

```php
# Importazione
use teamones\casbin\Enforcer;
``` 

### Due modi d'uso

```php
# 1. Utilizzo predefinito della configurazione 'default'
Enforcer::addPermissionForUser('user1', '/user', 'read');

# 1. Utilizzo della configurazione personalizzata 'rbac'
Enforcer::instance('rbac')->addPermissionForUser('user1', '/user', 'read');
``` 

### Introduzione alle API più utilizzate

Per ulteriori informazioni sull'uso delle API, fare riferimento ai link seguenti:

- API di gestione: https://casbin.org/docs/zh-CN/management-api
- API RBAC: https://casbin.org/docs/zh-CN/rbac-api

```php
# Aggiungi autorizzazioni a un utente

Enforcer::addPermissionForUser('user1', '/user', 'read');

# Rimuovi le autorizzazioni di un utente

Enforcer::deletePermissionForUser('user1', '/user', 'read');

# Ottieni tutte le autorizzazioni dell'utente

Enforcer::getPermissionsForUser('user1'); 

# Aggiungi un ruolo a un utente

Enforcer::addRoleForUser('user1', 'role1');

# Aggiungi autorizzazioni a un ruolo

Enforcer::addPermissionForUser('role1', '/user', 'edit');

# Ottieni tutti i ruoli

Enforcer::getAllRoles();

# Ottieni tutti i ruoli dell'utente

Enforcer::getRolesForUser('user1');

# Ottieni gli utenti per ruolo

Enforcer::getUsersForRole('role1');

# Verifica se un utente appartiene a un ruolo

Enforcer::hasRoleForUser('use1', 'role1');

# Rimuovi il ruolo dell'utente

Enforcer::deleteRoleForUser('use1', 'role1');

# Rimuovi tutti i ruoli dell'utente

Enforcer::deleteRolesForUser('use1');

# Rimuovi il ruolo

Enforcer::deleteRole('role1');

# Rimuovi l'autorizzazione

Enforcer::deletePermission('/user', 'read');

# Rimuovi tutte le autorizzazioni dell'utente o del ruolo

Enforcer::deletePermissionsForUser('user1');
Enforcer::deletePermissionsForUser('role1');

# Verifica le autorizzazioni, restituendo true o false

Enforcer::enforce("user1", "/user", "edit");
```
