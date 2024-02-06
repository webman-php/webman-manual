# База данных
Плагин может настроить свою собственную базу данных, например, содержимое `plugin/foo/config/database.php` следующее:
```php
return  [
    'default' => 'mysql',
    'connections' => [
        'mysql' => [ // mysql это имя подключения
            'driver'      => 'mysql',
            'host'        => '127.0.0.1',
            'port'        => 3306,
            'database'    => 'база_данных',
            'username'    => 'имя_пользователя',
            'password'    => 'пароль',
            'charset'     => 'utf8mb4',
            'collation'   => 'utf8mb4_general_ci',
        ],
        'admin' => [ // admin это имя подключения
            'driver'      => 'mysql',
            'host'        => '127.0.0.1',
            'port'        => 3306,
            'database'    => 'база_данных',
            'username'    => 'имя_пользователя',
            'password'    => 'пароль',
            'charset'     => 'utf8mb4',
            'collation'   => 'utf8mb4_general_ci',
        ],
    ],
];
```
Способ импорта `Db::connection('plugin.{плагин}.{имя_подключения}');`, например,
```php
use support\Db;
Db::connection('plugin.foo.mysql')->table('user')->first();
Db::connection('plugin.foo.admin')->table('admin')->first();
```

Если вы хотите использовать базу данных основного проекта, просто используйте ее напрямую, например,
```php
use support\Db;
Db::table('user')->first();
// Предположим, что основной проект также настроил подключение администратора
Db::connection('admin')->table('admin')->first();
```

## Настройка базы данных для модели

Мы можем создать базовый класс для модели, где базовый класс использует `$connection` для указания собственного подключения к базе данных, например:

```php
<?php

namespace plugin\foo\app\model;

use DateTimeInterface;
use support\Model;

class Base extends Model
{
    /**
     * @var string
     */
    protected $connection = 'plugin.foo.mysql';

}
```

Таким образом, все модели в плагине наследуют базовый класс и автоматически используют собственную базу данных плагина.

## Повторное использование конфигурации базы данных
Конечно, мы можем повторно использовать конфигурацию базы данных основного проекта. Если подключен [webman-admin](https://www.workerman.net/plugin/82), также можно повторно использовать конфигурацию базы данных [webman-admin](https://www.workerman.net/plugin/82), например,
```php
<?php

namespace plugin\foo\app\model;

use DateTimeInterface;
use support\Model;

class Base extends Model
{
    /**
     * @var string
     */
    protected $connection = 'plugin.admin.mysql';

}
```
