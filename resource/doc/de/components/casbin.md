# Casbin

## Beschreibung

Casbin ist ein leistungsfähiges, effizientes Open-Source-Zugriffskontroll-Framework, dessen Berechtigungsverwaltungsmechanismus mehrere Zugriffskontrollmodelle unterstützt.

## Projektadresse

https://github.com/teamones-open/casbin

## Installation

```php
  composer require teamones/casbin
```

## Casbin-Website

Für detaillierte Anleitungen besuchen Sie bitte die offizielle chinesische Dokumentation, hier wird nur erklärt, wie man es in webman konfiguriert und verwendet.

https://casbin.org/docs/zh-CN/overview

## Verzeichnisstruktur

```bash
.
├── config                        Konfigurationsverzeichnis
│   ├── casbin-restful-model.conf Verwendete Berechtigungsmodellkonfigurationsdatei
│   ├── casbin.php                Casbin-Konfiguration
......
├── database                      Datenbankdateien
│   ├── migrations                Umzugsdateien
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
     * Methode ändern.
     *
     * Schreiben Sie Ihre umkehrbaren Migrationen mit dieser Methode.
     *
     * Weitere Informationen zur Erstellung von Migrationen finden Sie hier:
     * http://docs.phinx.org/en/latest/migrations.html#the-abstractmigration-class
     *
     * Die folgenden Befehle können in dieser Methode verwendet werden und Phinx wird
     * sie automatisch rückgängig machen, wenn Sie die Migration zurückrollen:
     *
     *    createTable
     *    renameTable
     *    addColumn
     *    addCustomColumn
     *    renameColumn
     *    addIndex
     *    addForeignKey
     *
     * Jede andere destructive Änderung führt zu einem Fehler beim Versuch, die
     * Migration zurückzurollen.
     *
     * Denken Sie daran, "create()" oder "update()" aufzurufen und NICHT "speichern()", wenn Sie mit der Klassen-Tabelle arbeiten.
     */
    public function change()
    {
        $table = $this->table('rule', ['id' => false, 'primary_key' => ['id'], 'engine' => 'InnoDB', 'collation' => 'utf8mb4_general_ci', 'comment' => 'Regel-Tabelle']);

        // Hinzufügen von Datenfeldern
        $table->addColumn('id', 'integer', ['identity' => true, 'signed' => false, 'limit' => 11, 'comment' => 'Primärschlüssel-ID'])
            ->addColumn('ptype', 'char', ['default' => '', 'limit' => 8, 'comment' => 'Regeltyp'])
            ->addColumn('v0', 'string', ['default' => '', 'limit' => 128])
            ->addColumn('v1', 'string', ['default' => '', 'limit' => 128])
            ->addColumn('v2', 'string', ['default' => '', 'limit' => 128])
            ->addColumn('v3', 'string', ['default' => '', 'limit' => 128])
            ->addColumn('v4', 'string', ['default' => '', 'limit' => 128])
            ->addColumn('v5', 'string', ['default' => '', 'limit' => 128]);

        // Erstellung ausführen
        $table->create();
    }
}
```

## Casbin-Konfiguration

Syntax der Berechtigungsregelmodellkonfiguration finden Sie unter: https://casbin.org/docs/zh-CN/syntax-for-models

```php
<?php

return [
    'default' => [
        'model' => [
            'config_type' => 'file',
            'config_file_path' => config_path() . '/casbin-restful-model.conf', // Berechtigungsregelmodellkonfigurationsdatei
            'config_text' => '',
        ],
        'adapter' => [
            'type' => 'model', // Modell oder Adapter
            'class' => \app\model\Rule::class,
        ],
    ],
    // Mehrere Berechtigungsmodelle konfigurierbar
    'rbac' => [
        'model' => [
            'config_type' => 'file',
            'config_file_path' => config_path() . '/casbin-rbac-model.conf', // Berechtigungsregelmodellkonfigurationsdatei
            'config_text' => '',
        ],
        'adapter' => [
            'type' => 'model', // Modell oder Adapter
            'class' => \app\model\RBACRule::class,
        ],
    ],
];
```

### Adapter

Das aktuelle Composer-Paket enthält die Methode `model` von think-orm, für andere ORMs siehe `vendor/teamones/src/adapters/DatabaseAdapter.php`

Dann ändern Sie die Konfiguration:

```php
return [
    'default' => [
        'model' => [
            'config_type' => 'file',
            'config_file_path' => config_path() . '/casbin-restful-model.conf', // Berechtigungsregelmodellkonfigurationsdatei
            'config_text' => '',
        ],
        'adapter' => [
            'type' => 'adapter', // Hier wird der Typ als Adaptermodus konfiguriert
            'class' => \app\adapter\DatabaseAdapter::class,
        ],
    ],
];
```

## Anleitung zur Verwendung

### Einbinden

```php
# Einbinden
use teamones\casbin\Enforcer;
```

### Zwei Arten der Verwendung

```php
# 1. Standardmäßig die default-Konfiguration verwenden
Enforcer::addPermissionForUser('Benutzer1', '/Benutzer', 'lesen');

# 1. Verwendung der benutzerdefinierten rbac-Konfiguration
Enforcer::instance('rbac')->addPermissionForUser('Benutzer1', '/Benutzer', 'lesen');
```

### Übliche API-Einführung

Weitere API-Verwendungen finden Sie in der offiziellen Dokumentation

- Management-API: https://casbin.org/docs/zh-CN/management-api
- RBAC-API: https://casbin.org/docs/zh-CN/rbac-api

```php
# Berechtigung für Benutzer hinzufügen

Enforcer::addPermissionForUser('Benutzer1', '/Benutzer', 'lesen');

# Berechtigung eines Benutzers löschen

Enforcer::deletePermissionForUser('Benutzer1', '/Benutzer', 'lesen');

# Alle Berechtigungen eines Benutzers erhalten

Enforcer::getPermissionsForUser('Benutzer1');

# Rolle für Benutzer hinzufügen

Enforcer::addRoleForUser('Benutzer1', 'Rolle1');

# Berechtigung für eine Rolle hinzufügen

Enforcer::addPermissionForUser('Rolle1', '/Benutzer', 'bearbeiten');

# Alle Rollen erhalten

Enforcer::getAllRoles();

# Alle Rollen eines Benutzers erhalten

Enforcer::getRolesForUser('Benutzer1');

# Benutzer anhand der Rolle erhalten

Enforcer::getUsersForRole('Rolle1');

# Überprüfen, ob ein Benutzer zu einer Rolle gehört

Enforcer::hasRoleForUser('Benutzer1', 'Rolle1');

# Benutzerrolle löschen

Enforcer::deleteRoleForUser('Benutzer1', 'Rolle1');

# Alle Rollen eines Benutzers löschen

Enforcer::deleteRolesForUser('Benutzer1');

# Rolle löschen

Enforcer::deleteRole('Rolle1');

# Berechtigung löschen

Enforcer::deletePermission('/Benutzer', 'lesen');

# Alle Berechtigungen eines Benutzers oder einer Rolle löschen

Enforcer::deletePermissionsForUser('Benutzer1');
Enforcer::deletePermissionsForUser('Rolle1');

# Berechtigung prüfen, true oder false zurückgeben

Enforcer::enforce("Benutzer1", "/Benutzer", "bearbeiten");
```
