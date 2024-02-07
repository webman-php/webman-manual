# Casbin

## Description

Casbin est un puissant framework open source de contrôle d'accès, avec un mécanisme de gestion des autorisations prenant en charge plusieurs modèles de contrôle d'accès.

## Adresse du projet

https://github.com/teamones-open/casbin

## Installation

```php
composer require teamones/casbin
```

## Site Web de Casbin

Pour une utilisation détaillée, veuillez consulter la documentation officielle en chinois, nous aborderons ici comment configurer et utiliser dans webman.

https://casbin.org/docs/zh-CN/overview

## Structure du répertoire

```plaintext
.
├── config                        Répertoire de configuration
│   ├── casbin-restful-model.conf Fichier de configuration du modèle d'autorisation utilisé
│   ├── casbin.php                Configuration casbin
......
├── database                      Fichier de base de données
│   ├── migrations                Fichiers de migration
│   │   └── 20210218074218_create_rule_table.php
......
```

## Fichier de migration de la base de données

```php
<?php

use Phinx\Migration\AbstractMigration;

class CreateRuleTable extends AbstractMigration
{
    /**
     * Méthode de modification
     *
     * Écrivez vos migrations réversibles en utilisant cette méthode.
     *
     * Plus d'informations sur l'écriture des migrations sont disponibles ici :
     * http://docs.phinx.org/en/latest/migrations.html#the-abstractmigration-class
     *
     * Les commandes suivantes peuvent être utilisées dans cette méthode et Phinx les
     * inversera automatiquement lors du retour en arrière :
     *
     *    createTable
     *    renameTable
     *    addColumn
     *    addCustomColumn
     *    renameColumn
     *    addIndex
     *    addForeignKey
     *
     * Toute autre modification destructive provoquera une erreur lors de la
     * tentative d'annulation de la migration.
     *
     * N'oubliez pas d'appeler "create()" ou "update()" et NON "save()" lors du travail
     * avec la classe Table.
     */
    public function change()
    {
        $table = $this->table('rule', ['id' => false, 'primary_key' => ['id'], 'engine' => 'InnoDB', 'collation' => 'utf8mb4_general_ci', 'comment' => 'Table de règles']);

        // Ajout des champs de données
        $table->addColumn('id', 'integer', ['identity' => true, 'signed' => false, 'limit' => 11, 'comment' => 'ID primaire'])
            ->addColumn('ptype', 'char', ['default' => '', 'limit' => 8, 'comment' => "Type de règle"])
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

## Configuration Casbin

Veuillez consulter la syntaxe de configuration du modèle de règles d'autorisation : https://casbin.org/docs/zh-CN/syntax-for-models

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

L'adaptateur inclus dans le package actuel est la méthode model de think-orm. Pour d'autres orm, veuillez consulter vendor/teamones/src/adapters/DatabaseAdapter.php

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
            'type' => 'adapter', // ici le type est configuré en mode adaptateur
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

### Deux modes d'utilisation

```php
# 1. Utilisation par défaut de la configuration par défaut
Enforcer::addPermissionForUser('user1', '/user', 'read');

# 2. Utilisation de la configuration rbac personnalisée
Enforcer::instance('rbac')->addPermissionForUser('user1', '/user', 'read');
```

### Présentation des API courantes

Pour plus d'exemples d'utilisation des API, veuillez consulter le site officiel :

- API de gestion : https://casbin.org/docs/zh-CN/management-api
- API RBAC :  https://casbin.org/docs/zh-CN/rbac-api

```php
# Ajouter des autorisations pour un utilisateur

Enforcer::addPermissionForUser('user1', '/user', 'read');

# Supprimer les autorisations d'un utilisateur

Enforcer::deletePermissionForUser('user1', '/user', 'read');

# Obtenir toutes les autorisations de l'utilisateur

Enforcer::getPermissionsForUser('user1'); 

# Ajouter des rôles à un utilisateur

Enforcer::addRoleForUser('user1', 'role1');

# Ajouter des autorisations à un rôle

Enforcer::addPermissionForUser('role1', '/user', 'edit');

# Obtenir tous les rôles

Enforcer::getAllRoles();

# Obtenir tous les rôles de l'utilisateur

Enforcer::getRolesForUser('user1');

# Obtenir les utilisateurs pour un rôle donné

Enforcer::getUsersForRole('role1');

# Vérifier si un utilisateur appartient à un rôle

Enforcer::hasRoleForUser('use1', 'role1');

# Supprimer le rôle d'un utilisateur

Enforcer::deleteRoleForUser('use1', 'role1');

# Supprimer tous les rôles d'un utilisateur

Enforcer::deleteRolesForUser('use1');

# Supprimer le rôle

Enforcer::deleteRole('role1');

# Supprimer l'autorisation

Enforcer::deletePermission('/user', 'read');

# Supprimer toutes les autorisations d'un utilisateur ou d'un rôle

Enforcer::deletePermissionsForUser('user1');
Enforcer::deletePermissionsForUser('role1');

# Vérifier les autorisations, renvoie vrai ou faux

Enforcer::enforce("user1", "/user", "edit");
```
