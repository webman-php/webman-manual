# vlucas/phpdotenv

## Description
`vlucas/phpdotenv` est un composant de chargement de variables d'environnement, utilisé pour différencier la configuration dans différents environnements (comme l'environnement de développement, l'environnement de test, etc.).

## Adresse du projet
https://github.com/vlucas/phpdotenv

## Installation
```php
composer require vlucas/phpdotenv
```

## Utilisation

#### Créer un fichier `.env` à la racine du projet
**.env**
```plaintext
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

    // Configurations de différentes bases de données
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

> **Remarque**
> Il est recommandé d'ajouter le fichier `.env` à la liste de `.gitignore` afin d'éviter de le soumettre au référentiel de code. Ajoutez un fichier de configuration exemple `.env.example` dans le référentiel de code. Lors du déploiement du projet, copiez `.env.example` en tant que `.env` et modifiez la configuration dans `.env` en fonction de l'environnement actuel, de sorte que le projet puisse charger différentes configurations dans des environnements différents.

> **Remarque**
> `vlucas/phpdotenv` peut présenter des bogues dans les versions PHP TS (Thread-Safe). Veuillez utiliser la version NTS (Non-Thread-Safe). Pour connaître la version actuelle de PHP, exécutez `php -v`.

## Pour plus d'informations

Visitez https://github.com/vlucas/phpdotenv
