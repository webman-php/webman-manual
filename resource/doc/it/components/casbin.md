# Casbin

## Descrizione

Casbin è un potente e efficiente framework open source per il controllo degli accessi, il cui meccanismo di gestione dei permessi supporta vari modelli di controllo degli accessi.

## Indirizzo del progetto

https://github.com/teamones-open/casbin

## Installazione

  ```php
  composer require teamones/casbin
  ```

## Sito web di Casbin

Per un utilizzo dettagliato, consulta la documentazione ufficiale in cinese, qui parleremo solo di come configurarlo e utilizzarlo in webman.

https://casbin.org/docs/zh-CN/overview

## Struttura della directory

```
.
├── config                        Cartella di configurazione
│   ├── casbin-restful-model.conf File di configurazione del modello di autorizzazione utilizzato
│   ├── casbin.php                configurazione casbin
......
├── database                      File del database
│   ├── migrations                File di migrazione
│   │   └── 20210218074218_create_rule_table.php
......
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
     * Scrivi le migrazioni reversibili utilizzando questo metodo.
     *
     * Maggiori informazioni sulla scrittura delle migrazioni sono disponibili qui:
     * http://docs.phinx.org/en/latest/migrations.html#the-abstractmigration-class
     *
     * I seguenti comandi possono essere utilizzati in questo metodo e Phinx
     * li eseguirà automaticamente al rollback:
     *
     *    createTable
     *    renameTable
     *    addColumn
     *    addCustomColumn
     *    renameColumn
     *    addIndex
     *    addForeignKey
     *
     * Qualsiasi altra modifica distruttiva comporterà un errore durante il tentativo di
     * rollback della migrazione.
     *
     * Ricorda di chiamare "create()" o "update()" e NON "save()" quando si lavora
     * con la classe Tabella.
     */
    public function change()
    {
        $table = $this->table('rule', ['id' => false, 'primary_key' => ['id'], 'engine' => 'InnoDB', 'collation' => 'utf8mb4_general_ci', 'comment' => 'Tabella delle regole']);

        //Aggiunta dei campi dei dati
        $table->addColumn('id', 'integer', ['identity' => true, 'signed' => false, 'limit' => 11, 'comment' => 'ID primario'])
            ->addColumn('ptype', 'char', ['default' => '', 'limit' => 8, 'comment' => 'Tipo di regola'])
            ->addColumn('v0', 'string', ['default' => '', 'limit' => 128])
            ->addColumn('v1', 'string', ['default' => '', 'limit' => 128])
            ->addColumn('v2', 'string', ['default' => '', 'limit' => 128])
            ->addColumn('v3', 'string', ['default' => '', 'limit' => 128])
            ->addColumn('v4', 'string', ['default' => '', 'limit' => 128])
            ->addColumn('v5', 'string', ['default' => '', 'limit' => 128]);

        //Esecuzione della creazione
        $table->create();
    }
}

```

## Configurazione di casbin

Per la sintassi di configurazione del modello delle regole di autorizzazione, consulta: https://casbin.org/docs/zh-CN/syntax-for-models

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

L'adattatore attualmente incapsulato in composer è il metodo di modello di think-orm, per altri orm consulta vendor/teamones/src/adapters/DatabaseAdapter.php

Quindi modifica la configurazione

```php
return [
    'default' => [
        'model' => [
            'config_type' => 'file',
            'config_file_path' => config_path() . '/casbin-restful-model.conf', // File di configurazione del modello di autorizzazione
            'config_text' => '',
        ],
        'adapter' => [
            'type' => 'adapter', // qui il tipo è configurato come modalità adattatore
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

### Due modi per utilizzare

```php
# 1. Utilizzo predefinito della configurazione di default
Enforcer::addPermissionForUser('user1', '/user', 'read');

# 1. Utilizzo della configurazione rbac personalizzata
Enforcer::instance('rbac')->addPermissionForUser('user1', '/user', 'read');
```

### Introduzione dell'API comune

Per ulteriori modalità di utilizzo delle API, consulta il sito ufficiale
- API di gestione: https://casbin.org/docs/zh-CN/management-api
- API RBAC: https://casbin.org/docs/zh-CN/rbac-api

```php
# Aggiungi permessi per un utente

Enforcer::addPermissionForUser('user1', '/user', 'read');

# Elimina un permesso per un utente

Enforcer::deletePermissionForUser('user1', '/user', 'read');

# Ottieni tutti i permessi dell'utente

Enforcer::getPermissionsForUser('user1'); 

# Aggiungi un ruolo per un utente

Enforcer::addRoleForUser('user1', 'role1');

# Aggiungi permessi per un ruolo

Enforcer::addPermissionForUser('role1', '/user', 'edit');

# Ottieni tutti i ruoli

Enforcer::getAllRoles();

# Ottieni tutti i ruoli dell'utente

Enforcer::getRolesForUser('user1');

# Ottieni gli utenti in base al ruolo

Enforcer::getUsersForRole('ruolo1');

# Verifica se un utente appartiene a un ruolo

Enforcer::hasRoleForUser('utente1', 'ruolo1');

# Elimina ruolo utente

Enforcer::deleteRoleForUser('utente1', 'ruolo1');

# Elimina tutti i ruoli dell'utente

Enforcer::deleteRolesForUser('utente1');

# Elimina ruolo

Enforcer::deleteRole('ruolo1');

# Elimina permesso

Enforcer::deletePermission('/user', 'read');

# Elimina tutti i permessi dell'utente o del ruolo

Enforcer::deletePermissionsForUser('utente1');
Enforcer::deletePermissionsForUser('ruolo1');

# Verifica il permesso, restituisce true o false

Enforcer::enforce("utente1", "/user", "edit");
```
