# vlucas/phpdotenv

## Description
`vlucas/phpdotenv` est un composant de chargement des variables d'environnement qui permet de différencier la configuration pour différents environnements tels que le développement, les tests, etc.

## Adresse du Projet

https://github.com/vlucas/phpdotenv
  
## Installation
 
```php
composer require vlucas/phpdotenv
 ```
  
## Utilisation

#### Créer un fichier `.env` dans le répertoire racine du projet
**.env**
```
DB_HOST = 127.0.0.1
DB_PORT = 3306
DB_NAME = test
DB_USER = foo
DB_PASSWORD = 123456
```

#### Modifier le fichier de configuration
**config/database.php**
```php
return [
    // Base de données par défaut
    'default' => 'mysql',

    // Configuration pour différentes bases de données
    'connections' => [
        'mysql' => [
            'driver'      => 'mysql',
            'host'        => getenv('DB_HOST'),
            'port'        => getenv('DB_PORT'),
            'database'    => getenv('DB_NAME'),
            'username'    => getenv('DB_USER'),
            'password'    => getenv('DB_PASSWORD'),
            'unix_socket' => '',
            'charset'     => 'utf8',
            'collation'   => 'utf8_unicode_ci',
            'prefix'      => '',
            'strict'      => true,
            'engine'      => null,
        ],
    ],
];
```

> **Note**
> Il est recommandé d'ajouter le fichier `.env` à la liste `.gitignore` pour éviter de le soumettre au référentiel de code. Ajoutez un fichier de configuration d'exemple `.env.example` au référentiel de code, et lors du déploiement du projet, copiez `.env.example` en `.env` et modifiez la configuration dans `.env` en fonction de l'environnement actuel. Cela permettra au projet de charger des configurations différentes selon l'environnement.

> **Remarque**
> `vlucas/phpdotenv` peut présenter des bugs dans les versions de PHP TS (Thread-Safe). Veuillez utiliser la version NTS (Non-Thread-Safe).
> Vous pouvez vérifier la version actuelle de PHP en exécutant `php -v`.

## Pour plus d'informations

Visitez https://github.com/vlucas/phpdotenv
