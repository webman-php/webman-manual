# webman

## Beschreibung

Webman ist ein leistungsfähiges, effizientes Open-Source-Zugriffskontroll-Framework, dessen Berechtigungsverwaltungsmechanismus mehrere Zugriffskontrollmodelle unterstützt.

## Projektadresse

https://github.com/teamones-open/casbin

## Installation

  ```php
  composer require teamones/casbin
  ```

## Webman Offizielle Website

Für detaillierte Anleitungen besuchen Sie die offizielle chinesische Dokumentation. Hier zeigen wir nur, wie Sie es in webman konfigurieren und verwenden können.

https://casbin.org/docs/zh-CN/overview

## Verzeichnisstruktur

```
.
├── config                        Konfigurationsverzeichnis
│   ├── casbin-restful-model.conf Verwendete Berechtigungsmodellkonfigurationsdatei
│   ├── casbin.php                Casbin-Konfiguration
......
├── database                      Datenbankdateien
│   ├── migrations                Migrationsdateien
│   │   └── 20210218074218_create_rule_table.php
......
```

## Datenbank-Migrationsdatei

```php
<?php

use Phinx\Migration\AbstractMigration;

class CreateRuleTable extends AbstractMigration
{
    /**
     * Change Method.
     *
     * Write your reversible migrations using this method.
     *
     * More information on writing migrations is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-abstractmigration-class
     *
     * The following commands can be used in this method and Phinx will
     * automatically reverse them when rolling back:
     *
     *    createTable
     *    renameTable
     *    addColumn
     *    addCustomColumn
     *    renameColumn
     *    addIndex
     *    addForeignKey
     *
     * Any other destructive changes will result in an error when trying to
     * rollback the migration.
     *
     * Remember to call "create()" or "update()" and NOT "save()" when working
     * with the Table class.
     */
    public function change()
    {
        $table = $this->table('rule', ['id' => false, 'primary_key' => ['id'], 'engine' => 'InnoDB', 'collation' => 'utf8mb4_general_ci', 'comment' => 'Regeltabelle']);

        // Datenfelder hinzufügen
        $table->addColumn('id', 'integer', ['identity' => true, 'signed' => false, 'limit' => 11, 'comment' => 'Primärschlüssel-ID'])
            ->addColumn('ptype', 'char', ['default' => '', 'limit' => 8, 'comment' => 'Regeltyp'])
            ->addColumn('v0', 'string', ['default' => '', 'limit' => 128])
            ->addColumn('v1', 'string', ['default' => '', 'limit' => 128])
            ->addColumn('v2', 'string', ['default' => '', 'limit' => 128])
            ->addColumn('v3', 'string', ['default' => '', 'limit' => 128])
            ->addColumn('v4', 'string', ['default' => '', 'limit' => 128])
            ->addColumn('v5', 'string', ['default' => '', 'limit' => 128]);

        // Erstellen ausführen
        $table->create();
    }
}

```

## Casbin-Konfiguration

Für die Syntax der Berechtigungsregelmodellkonfiguration siehe: https://casbin.org/docs/zh-CN/syntax-for-models

```php

<?php

return [
    'default' => [
        'model' => [
            'config_type' => 'file',
            'config_file_path' => config_path() . '/casbin-restful-model.conf', // Konfigurationsdatei des Berechtigungsregelmodells
            'config_text' => '',
        ],
        'adapter' => [
            'type' => 'model', // model oder adapter
            'class' => \app\model\Rule::class,
        ],
    ],
    // Mehrere Berechtigungsmodelle konfigurieren
    'rbac' => [
        'model' => [
            'config_type' => 'file',
            'config_file_path' => config_path() . '/casbin-rbac-model.conf', // Konfigurationsdatei des Berechtigungsregelmodells
            'config_text' => '',
        ],
        'adapter' => [
            'type' => 'model', // model oder adapter
            'class' => \app\model\RBACRule::class,
        ],
    ],
];
```

### Adapter

Der aktuelle Komponistenummantelung ist auf die Methode des Think-ORM-Modells angepasst, für andere ORMs siehe vendor/teamones/src/adapters/DatabaseAdapter.php

Ändern Sie dann die Konfiguration

```php
return [
    'default' => [
        'model' => [
            'config_type' => 'file',
            'config_file_path' => config_path() . '/casbin-restful-model.conf', // Konfigurationsdatei des Berechtigungsregelmodells
            'config_text' => '',
        ],
        'adapter' => [
            'type' => 'adapter', // Hier Typ als Adaptermodus konfigurieren
            'class' => \app\adapter\DatabaseAdapter::class,
        ],
    ],
];
```

## Verwendungsanleitung

### Einbeziehung

```php
# Einbeziehung
use teamones\casbin\Enforcer;
```

### Zwei Verwendungsarten

```php
# 1. Standardmäßig Default-Konfiguration verwenden
Enforcer::addPermissionForUser('user1', '/user', 'read');

# 1. Verwendung der benutzerdefinierten rbac-Konfiguration
Enforcer::instance('rbac')->addPermissionForUser('user1', '/user', 'read');
```

### Übliche API-Einführung

Weitere API-Verwendungsmöglichkeiten finden Sie auf der offiziellen Website

- Management-API: https://casbin.org/docs/zh-CN/management-api
- RBAC-API: https://casbin.org/docs/zh-CN/rbac-api

```php
# Berechtigungen für einen Benutzer hinzufügen

Enforcer::addPermissionForUser('user1', '/user', 'read');

# Berechtigung eines Benutzers löschen

Enforcer::deletePermissionForUser('user1', '/user', 'read');

# Alle Berechtigungen eines Benutzers abrufen

Enforcer::getPermissionsForUser('user1'); 

# Rolle für Benutzer hinzufügen

Enforcer::addRoleForUser('user1', 'role1');

# Berechtigung für Rolle hinzufügen

Enforcer::addPermissionForUser('role1', '/user', 'edit');

# Alle Rollen abrufen

Enforcer::getAllRoles();

# Alle Rollen eines Benutzers abrufen

Enforcer::getRolesForUser('user1');

# Benutzer anhand der Rolle abrufen

Enforcer::getUsersForRole('role1');

# Überprüfen, ob ein Benutzer zu einer Rolle gehört

Enforcer::hasRoleForUser('use1', 'role1');

# Benutzerrolle löschen

Enforcer::deleteRoleForUser('use1', 'role1');

# Alle Rollen eines Benutzers löschen

Enforcer::deleteRolesForUser('use1');

# Rolle löschen

Enforcer::deleteRole('role1');

# Berechtigung löschen

Enforcer::deletePermission('/user', 'read');

# Alle Berechtigungen für Benutzer oder Rolle löschen

Enforcer::deletePermissionsForUser('user1');
Enforcer::deletePermissionsForUser('role1');

# Berechtigung überprüfen und true oder false zurückgeben

Enforcer::enforce("user1", "/user", "edit");
```
