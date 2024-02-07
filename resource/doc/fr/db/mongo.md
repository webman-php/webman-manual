# MongoDB

webman utilise par défaut [jenssegers/mongodb](https://github.com/jenssegers/laravel-mongodb) comme composant MongoDB, qui a été extrait du projet Laravel et s'utilise de la même manière.

Avant d'utiliser `jenssegers/mongodb`, vous devez d'abord installer l'extension mongodb pour `php-cli`.

> Utilisez la commande `php -m | grep mongodb` pour vérifier si l'extension mongodb est installée pour `php-cli`. Notez que même si vous avez installé l'extension mongodb pour `php-fpm`, cela ne signifie pas que vous pouvez l'utiliser pour `php-cli` car `php-cli` et `php-fpm` sont des applications différentes et peuvent utiliser des fichiers de configuration `php.ini` différents. Utilisez la commande `php --ini` pour voir quel fichier de configuration `php.ini` est utilisé par votre `php-cli`.

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
Ajoutez une connexion `mongodb` dans le fichier `config/database.php`, similaire à ce qui suit :
```php
return [

    'default' => 'mysql',

    'connections' => [

         ... autres configurations ici ...

        'mongodb' => [
            'driver'   => 'mongodb',
            'host'     => '127.0.0.1',
            'port'     =>  27017,
            'database' => 'test',
            'username' => null,
            'password' => null,
            'options' => [
                // ici vous pouvez passer plus de paramètres au gestionnaire de pilote MongoDB
                // voir https://www.php.net/manual/en/mongodb-driver-manager.construct.php sous "Uri Options" pour une liste complète des paramètres que vous pouvez utiliser

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
