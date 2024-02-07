# MongoDB

webman utiliza por padrão o [jenssegers/mongodb](https://github.com/jenssegers/laravel-mongodb) como componente MongoDB, que foi extraído do projeto Laravel e tem o mesmo uso que o Laravel.

Antes de usar `jenssegers/mongodb`, é necessário instalar a extensão MongoDB para `php-cli`.

> Use o comando `php -m | grep mongodb` para verificar se a extensão MongoDB está instalada para `php-cli`. Atenção: mesmo que você tenha instalado a extensão MongoDB para `php-fpm`, isso não significa que você possa usá-la para `php-cli`, pois `php-cli` e `php-fpm` são aplicativos diferentes e podem usar arquivos de configuração `php.ini` diferentes. Use o comando `php --ini` para verificar qual arquivo de configuração `php.ini` é usado para `php-cli`.

## Instalação

Para PHP>7.2:
```php
composer require -W illuminate/database jenssegers/mongodb ^3.8.0
```

Para PHP=7.2:
```php
composer require -W illuminate/database jenssegers/mongodb ^3.7.0
```

Após a instalação, é necessário reiniciar (reload não é válido).

## Configuração
No arquivo `config/database.php`, adicione uma conexão `mongodb`, semelhante ao seguinte:
```php
return [

    'default' => 'mysql',

    'connections' => [

         ...configurações omitidas...

        'mongodb' => [
            'driver'   => 'mongodb',
            'host'     => '127.0.0.1',
            'port'     =>  27017,
            'database' => 'test',
            'username' => null,
            'password' => null,
            'options' => [
                // aqui você pode passar mais configurações para o Mongo Driver Manager
                // consulte https://www.php.net/manual/en/mongodb-driver-manager.construct.php em "Uri Options" para uma lista de parâmetros completos que você pode usar

                'appname' => 'homestead'
            ],
        ],
    ],
];
```

## Exemplo
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

## Para mais informações, visite

https://github.com/jenssegers/laravel-mongodb
