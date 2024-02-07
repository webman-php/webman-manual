# MongoDB

webman uses [jenssegers/mongodb](https://github.com/jenssegers/laravel-mongodb) as the default MongoDB component, which is extracted from the Laravel project and has the same usage as Laravel.

Before using `jenssegers/mongodb`, you must first install the MongoDB extension for `php-cli`.

> Use the command `php -m | grep mongodb` to check if the MongoDB extension is installed for `php-cli`. Note: Even if you have installed the MongoDB extension for `php-fpm`, it does not mean that you can use it in `php-cli`, because `php-cli` and `php-fpm` are different applications and may use different `php.ini` configurations. Use the command `php --ini` to see which `php.ini` configuration file your `php-cli` is using.

## Installation

For PHP>7.2
```php
composer require -W illuminate/database jenssegers/mongodb ^3.8.0
```
For PHP=7.2
```php
composer require -W illuminate/database jenssegers/mongodb ^3.7.0
```

After installation, you need to restart (reload is invalid).

## Configuration
Add the `mongodb` connection in `config/database.php`, similar to the following:
```php
return [

    'default' => 'mysql',

    'connections' => [

         ...Other configurations are omitted here...

        'mongodb' => [
            'driver'   => 'mongodb',
            'host'     => '127.0.0.1',
            'port'     =>  27017,
            'database' => 'test',
            'username' => null,
            'password' => null,
            'options' => [
                // here you can pass more settings to the Mongo Driver Manager
                // see https://www.php.net/manual/en/mongodb-driver-manager.construct.php under "Uri Options" for a list of complete parameters that you can use

                'appname' => 'homestead'
            ],
        ],
    ],
];
```

## Example
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

## For more information, please visit

https://github.com/jenssegers/laravel-mongodb
