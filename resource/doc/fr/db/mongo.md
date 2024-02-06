# MongoDB

webman utilise par défaut [jenssegers/mongodb](https://github.com/jenssegers/laravel-mongodb) en tant que composant MongoDB, qui a été extrait du projet Laravel et utilisé de la même manière.

Avant d'utiliser `jenssegers/mongodb`, vous devez d'abord installer l'extension MongoDB pour `php-cli`.

> Utilisez la commande `php -m | grep mongodb` pour vérifier si l'extension MongoDB est installée pour `php-cli`. Remarque : même si vous avez installé l'extension MongoDB pour `php-fpm`, cela ne signifie pas que vous pouvez l'utiliser pour `php-cli`, car `php-cli` et `php-fpm` sont des applications distinctes et peuvent utiliser des fichiers de configuration `php.ini` différents. Utilisez la commande `php --ini` pour vérifier quel fichier de configuration `php.ini` est utilisé par votre `php-cli`.

## Installation

Pour PHP>7.2
```php
composer require -W illuminate/database jenssegers/mongodb ^3.8.0
```
Pour PHP=7.2
```php
composer require -W illuminate/database jenssegers/mongodb ^3.7.0
```

Après l'installation, redémarrez (reload ne fonctionne pas).

## Configuration
Ajoutez une connexion `mongodb` dans le fichier `config/database.php`, de la manière suivante :
```php
return [

    'default' => 'mysql',

    'connections' => [

         ...autres configurations ici...

        'mongodb' => [
            'driver'   => 'mongodb',
            'host'     => '127.0.0.1',
            'port'     =>  27017,
            'database' => 'test',
            'username' => null,
            'password' => null,
            'options' => [
                // ici vous pouvez passer plus de paramètres au gestionnaire du pilote Mongo
                // consultez https://www.php.net/manual/en/mongodb-driver-manager.construct.php sous "Options de l'URI" pour une liste des paramètres complets que vous pouvez utiliser

                'appname' => 'homestead'
            ],
        ],
    ],
];
```

## Exemple
```php
<?php
namespace app\controller;

use support\Request;
use support\Db;

class UserController
{
    public function db(Request $request)
    {
        Db::connection('mongodb')->collection('test')->insert([1,2,3]);
        return json(Db::connection('mongodb')->collection('test')->get());
    }
}
```

## Pour plus d'informations, veuillez visiter

https://github.com/jenssegers/laravel-mongodb
