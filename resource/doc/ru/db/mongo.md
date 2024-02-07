# MongoDB

webman по умолчанию использует [jenssegers/mongodb](https://github.com/jenssegers/laravel-mongodb) в качестве компонента mongodb, он был извлечен из проекта laravel и используется так же, как и в laravel.

Перед использованием `jenssegers/mongodb` необходимо установить расширение mongodb для `php-cli`.

> Используйте команду `php -m | grep mongodb` для проверки установлено ли расширение mongodb для `php-cli`. Обратите внимание: даже если вы установили расширение mongodb для `php-fpm`, это не означает, что вы можете использовать его для `php-cli`, поскольку `php-cli` и `php-fpm` - разные приложения, которые могут использовать разные конфигурационные файлы `php.ini`. Чтобы узнать, какой файл конфигурации `php.ini` используется для вашего `php-cli`, используйте команду `php --ini`.

## Установка

PHP>7.2
```php
composer require -W illuminate/database jenssegers/mongodb ^3.8.0
```
PHP=7.2
```php
composer require -W illuminate/database jenssegers/mongodb ^3.7.0
```

После установки необходимо выполнить restart (перезагрузку), reload не сработает.

## Настройка
Добавьте соединение с `mongodb` в файле `config/database.php`, примерно так:

```php
return [

    'default' => 'mysql',

    'connections' => [

         ...здесь опущены другие настройки...

        'mongodb' => [
            'driver'   => 'mongodb',
            'host'     => '127.0.0.1',
            'port'     =>  27017,
            'database' => 'test',
            'username' => null,
            'password' => null,
            'options' => [
                // здесь вы можете передать дополнительные настройки в Mongo Driver Manager
                // см. https://www.php.net/manual/en/mongodb-driver-manager.construct.php раздел "Uri Options" для списка всех параметров, которые вы можете использовать

                'appname' => 'homestead'
            ],
        ],
    ],
];
```

## Пример
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

## Дополнительная информация
Для получения дополнительной информации посетите:

https://github.com/jenssegers/laravel-mongodb
