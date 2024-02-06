# Casbin

## Description

Casbin est un puissant et efficace framework open source de contrôle d'accès, dont le mécanisme de gestion des autorisations supporte plusieurs modèles de contrôle d'accès.

## Adresse du projet

https://github.com/teamones-open/casbin

## Installation

```php
composer require teamones/casbin
```

## Site web de Casbin

Pour des utilisations détaillées, veuillez consulter la documentation officielle en chinois. Nous allons voir ici comment configurer et utiliser dans webman.

https://casbin.org/docs/zh-CN/overview

## Structure des répertoires

```
.
├── config                        Répertoire de configuration
│   ├── casbin-restful-model.conf Fichier de configuration du modèle d'autorisation utilisé
│   ├── casbin.php                Configuration de casbin
......
├── database                      Fichier de base de données
│   ├── migrations                Fichier de migration
│   │   └── 20210218074218_create_rule_table.php
......
```

## Fichier de migration de base de données

```php
<?php

use Phinx\Migration\AbstractMigration;

class CreateRuleTable extends AbstractMigration
{
    /**
     * Change Method.
     *
     * Écrivez vos migrations réversibles en utilisant cette méthode.
     *
     * Plus d'informations sur l'écriture des migrations sont disponibles ici :
     * http://docs.phinx.org/en/latest/migrations.html#the-abstractmigration-class
     *
     * Les commandes suivantes peuvent être utilisées dans cette méthode et Phinx
     * les annulera automatiquement lors du retour en arrière :
     *
     *    createTable
     *    renameTable
     *    addColumn
     *    addCustomColumn
     *    renameColumn
     *    addIndex
     *    addForeignKey
     *
     * Toute autre modification destructive entraînera une erreur lors de la
     * tentative de retour en arrière de la migration.
     *
     * N'oubliez pas d'appeler "create()" ou "update()" et NON "save()" lors du travail
     * avec la classe Table.
     */
    public function change()
    {
        $table = $this->table('rule', ['id' => false, 'primary_key' => ['id'], 'engine' => 'InnoDB', 'collation' => 'utf8mb4_general_ci', 'comment' => 'Table de règles']);

        // Ajout des champs de données
        $table->addColumn('id', 'integer', ['identity' => true, 'signed' => false, 'limit' => 11, 'comment' => 'ID principal'])
            ->addColumn('ptype', 'char', ['default' => '', 'limit' => 8, 'comment' => 'Type de règle'])
            ->addColumn('v0', 'string', ['default' => '', 'limit' => 128])
            ->addColumn('v1', 'string', ['default' => '', 'limit' => 128])
            ->addColumn('v2', 'string', ['default' => '', 'limit' => 128])
            ->addColumn('v3', 'string', ['default' => '', 'limit' => 128])
            ->addColumn('v4', 'string', ['default' => '', 'limit' => 128])
            ->addColumn('v5', 'string', ['default' => '', 'limit' => 128]);

        // Exécution de la création
        $table->create();
    }
}

```

## Configuration de casbin

Veuillez consulter la syntaxe de configuration du modèle d'autorisation à l'adresse suivante : https://casbin.org/docs/zh-CN/syntax-for-models

```php

<?php

return [
    'default' => [
        'model' => [
            'config_type' => 'file',
            'config_file_path' => config_path() . '/casbin-restful-model.conf', // Fichier de configuration du modèle d'autorisation
            'config_text' => '',
        ],
        'adapter' => [
            'type' => 'model', // modèle ou adaptateur
            'class' => \app\model\Rule::class,
        ],
    ],
    // Vous pouvez configurer plusieurs modèles d'autorisation
    'rbac' => [
        'model' => [
            'config_type' => 'file',
            'config_file_path' => config_path() . '/casbin-rbac-model.conf', // Fichier de configuration du modèle d'autorisation
            'config_text' => '',
        ],
        'adapter' => [
            'type' => 'model', // modèle ou adaptateur
            'class' => \app\model\RBACRule::class,
        ],
    ],
];
```

### Adaptateur

L'adaptateur encapsulé actuel de composer utilise la méthode model de think-orm, veuillez consulter le fichier vendor/teamones/src/adapters/DatabaseAdapter.php pour d'autres ORM.

Ensuite, modifiez la configuration

```php
return [
    'default' => [
        'model' => [
            'config_type' => 'file',
            'config_file_path' => config_path() . '/casbin-restful-model.conf', // Fichier de configuration du modèle d'autorisation
            'config_text' => '',
        ],
        'adapter' => [
            'type' => 'adapter', // Ici, configurez le type en mode adaptateur
            'class' => \app\adapter\DatabaseAdapter::class,
        ],
    ],
];
```

## Instructions d'utilisation

### Importation

```php
# Importation
use teamones\casbin\Enforcer;
```

### Deux façons d'utiliser

```php
# 1. Utilisation par défaut de la configuration default
Enforcer::addPermissionForUser('user1', '/user', 'read');

# 1. Utilisation d'une configuration rbac personnalisée
Enforcer::instance('rbac')->addPermissionForUser('user1', '/user', 'read');
```

### Présentation des API couramment utilisées

Pour plus d'utilisation des API, veuillez consulter le site officiel

- API de gestion : https://casbin.org/docs/zh-CN/management-api
- API RBAC : https://casbin.org/docs/zh-CN/rbac-api

```php
# Ajouter des autorisations à un utilisateur

Enforcer::addPermissionForUser('user1', '/user', 'read');

# Supprimer une autorisation pour un utilisateur

Enforcer::deletePermissionForUser('user1', '/user', 'read');

# Obtenir toutes les autorisations d'un utilisateur

Enforcer::getPermissionsForUser('user1'); 

# Ajouter un rôle à un utilisateur

Enforcer::addRoleForUser('user1', 'role1');

# Ajouter une autorisation à un rôle

Enforcer::addPermissionForUser('role1', '/user', 'edit');

# Obtenir tous les rôles

Enforcer::getAllRoles();

# Obtenir tous les rôles d'un utilisateur

Enforcer::getRolesForUser('user1');

# Obtenir les utilisateurs pour un rôle

Enforcer::getUsersForRole('role1');

# Vérifier si un utilisateur appartient à un rôle

Enforcer::hasRoleForUser('use1', 'role1');

# Supprimer le rôle d'un utilisateur

Enforcer::deleteRoleForUser('use1', 'role1');

# Supprimer tous les rôles d'un utilisateur

Enforcer::deleteRolesForUser('use1');

# Supprimer un rôle

Enforcer::deleteRole('role1');

# Supprimer une autorisation

Enforcer::deletePermission('/user', 'read');

# Supprimer toutes les autorisations d'un utilisateur ou d'un rôle

Enforcer::deletePermissionsForUser('user1');
Enforcer::deletePermissionsForUser('role1');

# Vérifier une autorisation, renvoie true ou false

Enforcer::enforce("user1", "/user", "edit");
```
