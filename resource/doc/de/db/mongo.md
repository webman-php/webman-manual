# MongoDB

webman verwendet standardmäßig [jenssegers/mongodb](https://github.com/jenssegers/laravel-mongodb) als MongoDB-Komponente. Es wurde aus dem Laravel-Projekt extrahiert und wird genauso verwendet.

Bevor Sie `jenssegers/mongodb` verwenden, müssen Sie die MongoDB-Erweiterung für `php-cli` installieren.

> Verwenden Sie den Befehl `php -m | grep mongodb`, um zu überprüfen, ob die MongoDB-Erweiterung für `php-cli` installiert ist. Beachten Sie: Selbst wenn Sie die MongoDB-Erweiterung für `php-fpm` installiert haben, bedeutet dies nicht, dass Sie sie für `php-cli` verwenden können, da `php-cli` und `php-fpm` separate Anwendungen sind und möglicherweise unterschiedliche `php.ini`-Konfigurationen verwenden. Verwenden Sie den Befehl `php --ini`, um zu überprüfen, welche `php.ini`-Konfigurationsdatei von Ihrem `php-cli` verwendet wird.

## Installation

PHP>7.2:
```php
composer require -W illuminate/database jenssegers/mongodb ^3.8.0
```
PHP=7.2:
```php
composer require -W illuminate/database jenssegers/mongodb ^3.7.0
```

Nach der Installation ist ein Neustart erforderlich (ein Reload funktioniert nicht).

## Konfiguration
Fügen Sie in der Datei `config/database.php` eine `mongodb`-Verbindung hinzu, ähnlich wie folgt:
```php
return [

    'default' => 'mysql',

    'connections' => [

         ...andere Konfiguration hier ausgelassen...

        'mongodb' => [
            'driver'   => 'mongodb',
            'host'     => '127.0.0.1',
            'port'     =>  27017,
            'database' => 'test',
            'username' => null,
            'password' => null,
            'options' => [
                // Hier können Sie weitere Einstellungen an den Mongo Driver Manager übergeben
                // siehe https://www.php.net/manual/en/mongodb-driver-manager.construct.php unter "Uri Options" für eine Liste der vollständigen Parameter, die Sie verwenden können
                
                'appname' => 'homestead'
            ],
        ],
    ],
];
```

## Beispiel
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

## Weitere Informationen finden Sie unter

https://github.com/jenssegers/laravel-mongodb
