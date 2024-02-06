# MongoDB

O webman usa por padrão o [jenssegers/mongodb](https://github.com/jenssegers/laravel-mongodb) como componente MongoDB, que foi retirado do projeto Laravel e tem o mesmo uso.

Antes de usar o `jenssegers/mongodb`, você precisa instalar a extensão MongoDB para o `php-cli`.

> Use o comando `php -m | grep mongodb` para verificar se a extensão MongoDB está instalada no `php-cli`. Observação: mesmo que você tenha instalado a extensão MongoDB no `php-fpm`, isso não significa que você pode usá-la no `php-cli`, porque o `php-cli` e o `php-fpm` são aplicativos diferentes e podem usar diferentes arquivos de configuração `php.ini`. Use o comando `php --ini` para verificar qual arquivo de configuração `php.ini` o seu `php-cli` está usando.

## Instalação

PHP>7.2
```php
composer require -W illuminate/database jenssegers/mongodb ^3.8.0
```
PHP=7.2
```php
composer require -W illuminate/database jenssegers/mongodb ^3.7.0
```

Após a instalação, é necessário reiniciar (restart) (reload não funciona).

## Configuração
Em `config/database.php`, adicione a conexão `mongodb`, semelhante ao exemplo a seguir:
```php
return [

    'default' => 'mysql',

    'connections' => [

         ...outros configurações aqui...

        'mongodb' => [
            'driver'   => 'mongodb',
            'host'     => '127.0.0.1',
            'port'     =>  27017,
            'database' => 'test',
            'username' => null,
            'password' => null,
            'options' => [
                // aqui você pode passar mais configurações para o Mongo Driver Manager
                // consulte https://www.php.net/manual/en/mongodb-driver-manager.construct.php em "Uri Options" para obter uma lista de parâmetros completos que você pode usar

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
