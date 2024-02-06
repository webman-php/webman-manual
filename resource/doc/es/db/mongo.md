# MongoDB

webman utiliza por defecto el componente [jenssegers/mongodb](https://github.com/jenssegers/laravel-mongodb) como MongoDB, que fue extraído del proyecto Laravel y se utiliza de la misma manera.

Antes de usar `jenssegers/mongodb`, debes instalar la extensión de MongoDB para `php-cli`.

> Usa el comando `php -m | grep mongodb` para verificar si la extensión de MongoDB está instalada para `php-cli`. Ten en cuenta que incluso si has instalado la extensión de MongoDB para `php-fpm`, no significa que la puedas usar en `php-cli`, ya que son aplicaciones diferentes y pueden usar diferentes archivos de configuración `php.ini`. Utiliza el comando `php --ini` para verificar qué archivo de configuración `php.ini` está utilizando tu `php-cli`.

## Instalación

PHP>7.2:
```php
composer require -W illuminate/database jenssegers/mongodb ^3.8.0
```
PHP=7.2:
```php
composer require -W illuminate/database jenssegers/mongodb ^3.7.0
```

Después de la instalación, es necesario reiniciar (restart) PHP (reload no funcionará).

## Configuración
En el archivo `config/database.php`, agrega la conexión `mongodb`, similar a lo siguiente:
```php
return [

    'default' => 'mysql',

    'connections' => [

         ...se omiten otras configuraciones...

        'mongodb' => [
            'driver'   => 'mongodb',
            'host'     => '127.0.0.1',
            'port'     =>  27017,
            'database' => 'test',
            'username' => null,
            'password' => null,
            'options' => [
                // aquí puedes pasar más configuraciones al administrador del controlador Mongo
                // consulta https://www.php.net/manual/en/mongodb-driver-manager.construct.php en "Uri Options" para ver una lista de parámetros completos que puedes usar

                'appname' => 'homestead'
            ],
        ],
    ],
];
```

## Ejemplo
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

## Para más información, visita

https://github.com/jenssegers/laravel-mongodb
