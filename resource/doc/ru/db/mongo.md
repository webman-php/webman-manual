# MongoDB

webman использует [jenssegers/mongodb](https://github.com/jenssegers/laravel-mongodb) в качестве компонента mongodb по умолчанию. Он был извлечен из проекта laravel и имеет такое же использование.

Прежде чем использовать `jenssegers/mongodb`, необходимо установить расширение mongodb для `php-cli`.

> Используйте команду `php -m | grep mongodb` для проверки наличия расширения mongodb для `php-cli`. Обратите внимание: даже если вы установили расширение mongodb для `php-fpm`, это не означает, что вы можете использовать его в `php-cli`, поскольку `php-cli` и `php-fpm` - это разные программы, которые могут использовать разные файлы настройки `php.ini`. Используйте команду `php --ini`, чтобы узнать, какой файл настройки `php.ini` используется для вашего `php-cli`.

## Установка

Для PHP>7.2
```php
composer require -W illuminate/database jenssegers/mongodb ^3.8.0
```
Для PHP=7.2
```php
composer require -W illuminate/database jenssegers/mongodb ^3.7.0
```

После установки необходимо перезапустить (restart) (reload не действует).

## Настройка

Добавьте подключение `mongodb` в файле `config/database.php`, подобно следующему:
```php
return [

    'default' => 'mysql',

    'connections' => [

         ...другие конфигурации здесь...

        'mongodb' => [
            'driver'   => 'mongodb',
            'host'     => '127.0.0.1',
            'port'     =>  27017,
            'database' => 'test',
            'username' => null,
            'password' => null,
            'options' => [
                // здесь можно передать больше настроек для менеджера драйвера Mongo
                // см. https://www.php.net/manual/en/mongodb-driver-manager.construct.php раздел "Uri Options" для списка полных параметров, которые вы можете использовать

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

## Дополнительную информацию можно найти на

https://github.com/jenssegers/laravel-mongodb
