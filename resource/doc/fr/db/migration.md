# Outil de migration de base de données Phinx

## Description

Phinx permet aux développeurs de modifier et de maintenir la base de données de manière concise. Il évite la rédaction manuelle de requêtes SQL en utilisant une puissante API PHP pour gérer les migrations de base de données. Les développeurs peuvent utiliser le contrôle de version pour gérer leurs migrations de base de données. Phinx permet de facilement réaliser des migrations de données entre différentes bases de données. Il est également possible de suivre les scripts de migration qui ont été exécutés, ce qui permet aux développeurs de se concentrer davantage sur la rédaction d'un meilleur système sans se soucier de l'état de la base de données.

## Adresse du projet

https://github.com/cakephp/phinx

## Installation

  ```php
  composer require robmorgan/phinx
  ```

## Documentation officielle en chinois

Pour une utilisation détaillée, veuillez consulter la documentation officielle en chinois. Ici, nous allons seulement expliquer comment configurer et utiliser Phinx dans webman.

https://tsy12321.gitbooks.io/phinx-doc/content/

## Structure des fichiers de migration

```
.
├── app                           Répertoire de l'application
│   ├── controller                Répertoire des contrôleurs
│   │   └── Index.php             Contrôleur
│   ├── model                     Répertoire des modèles
......
├── database                      Fichiers de base de données
│   ├── migrations                Fichiers de migration
│   │   └── 20180426073606_create_user_table.php
│   ├── seeds                     Données de test
│   │   └── UserSeeder.php
......
```

## Configuration de phinx.php

Créez un fichier phinx.php à la racine du projet

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

## Recommandations d'utilisation

Une fois fusionnés, les fichiers de migration ne peuvent plus être modifiés. En cas de problème, il est nécessaire de créer un nouveau fichier de modification ou de suppression pour y remédier.

#### Règles de nommage des fichiers d'opérations de création de table

`{time (créé automatiquement)}_create_{nom de la table en minuscules}`

#### Règles de nommage des fichiers d'opérations de modification de table

`{time (créé automatiquement)}_modify_{nom de la table en minuscules + élément spécifique de modification en minuscules}`

#### Règles de nommage des fichiers d'opérations de suppression de table

`{time (créé automatiquement)}_delete_{nom de la table en minuscules + élément spécifique de modification en minuscules}`

#### Règles de nommage des fichiers de remplissage de données

`{time (créé automatiquement)}_fill_{nom de la table en minuscules + élément spécifique de modification en minuscules}`

