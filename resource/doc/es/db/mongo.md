# MongoDB

webman utiliza [jenssegers/mongodb](https://github.com/jenssegers/laravel-mongodb) como componente de MongoDB de forma predeterminada. Este componente fue extraído del proyecto Laravel y se utiliza de la misma manera que en Laravel.

Antes de utilizar `jenssegers/mongodb`, es necesario instalar la extensión de MongoDB para `php-cli`.

> Utiliza el comando `php -m | grep mongodb` para verificar si la extensión de MongoDB está instalada para `php-cli`. Ten en cuenta que incluso si has instalado la extensión de MongoDB para `php-fpm`, no significa que esté disponible para `php-cli`, ya que `php-cli` y `php-fpm` son aplicaciones diferentes que pueden utilizar configuraciones diferentes de `php.ini`. Utiliza el comando `php --ini` para verificar qué archivo de configuración `php.ini` está utilizando tu `php-cli`.

## Instalación

Para PHP>7.2
```php
composer require -W illuminate/database jenssegers/mongodb ^3.8.0
```
Para PHP=7.2
```php
composer require -W illuminate/database jenssegers/mongodb ^3.7.0
```

Después de la instalación, es necesario reiniciar (restart) (reload es ineficaz).

## Configuración
Agrega la conexión `mongodb` en el archivo `config/database.php`, de la siguiente manera:
```php
return [

    'default' => 'mysql',

    'connections' => [

         ...Otras configuraciones se omiten aquí...

        'mongodb' => [
            'driver'   => 'mongodb',
            'host'     => '127.0.0.1',
            'port'     =>  27017,
            'database' => 'test',
            'username' => null,
            'password' => null,
            'options' => [
                // Aquí puedes pasar más configuraciones al administrador del controlador MongoDB
                // consulta https://www.php.net/manual/en/mongodb-driver-manager.construct.php en "Uri Options" para ver una lista de parámetros completos que puedes utilizar

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
