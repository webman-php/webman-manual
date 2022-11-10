# MongoDB

webmannative templates [jenssegers/mongodb](https://github.com/jenssegers/laravel-mongodb) asmongodbComponent，It is fromlaravelextracted from the project，Usage withlaravelsame。

You must install the mongodb extension to `php-cli` before using `jenssegers/mongodb`。

> Use the command `php -m | grep mongodb` to see if `php-cli` has the mongodb extension installed. Note: Even if you have the mongodb extension installed in `php-fpm`, it doesn't mean you can use it in `php-cli` because `php-cli` and `php-fpm` are different applications and may use different `php.ini` configurations. Use the command `php --ini` to see which `php.ini` configuration file your `php-cli` uses 。

## Install

```php
composer require psr/container ^1.1.1 illuminate/database jenssegers/mongodb ^3.8.0
```

## Configure
Add `mongodb` connection in `config/database.php`, similar to the following：
```php
return [

    'default' => 'mysql',

    'connections' => [

         ...Other configuration omitted here...

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

                'database' => 'admin', // required with Mongo 3+
            ],
        ],
    ],
];
```

## Examples
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

## Read more at 

https://github.com/jenssegers/laravel-mongodb

