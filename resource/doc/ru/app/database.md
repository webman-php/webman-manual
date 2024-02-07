# База данных
Плагин может настроить свою собственную базу данных, например, содержание `plugin/foo/config/database.php` следующее:
```php
return  [
    'default' => 'mysql',
    'connections' => [
        'mysql' => [ // mysql - имя подключения
            'driver'      => 'mysql',
            'host'        => '127.0.0.1',
            'port'        => 3306,
            'database'    => 'название_базы_данных',
            'username'    => 'имя_пользователя',
            'password'    => 'пароль',
            'charset'     => 'utf8mb4',
            'collation'   => 'utf8mb4_general_ci',
        ],
        'admin' => [ // admin - имя подключения
            'driver'      => 'mysql',
            'host'        => '127.0.0.1',
            'port'        => 3306,
            'database'    => 'название_базы_данных',
            'username'    => 'имя_пользователя',
            'password'    => 'пароль',
            'charset'     => 'utf8mb4',
            'collation'   => 'utf8mb4_general_ci',
        ],
    ],
];
```
Способ ссылки `Db::connection('plugin.{плагин}.{имя_подключения}');`, например:
```php
use support\Db;
Db::connection('plugin.foo.mysql')->table('user')->first();
Db::connection('plugin.foo.admin')->table('admin')->first();
```
Если вы хотите использовать базу данных основного проекта, просто используйте ее напрямую, например:
```php
use support\Db;
Db::table('user')->first();
// Предположим, что в основном проекте также настроено подключение admin
Db::connection('admin')->table('admin')->first();
```

## Настройка базы данных для модели

Мы можем создать базовый класс для модели, где с помощью `$connection` указывается подключение к базе данных плагина, например:

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
Таким образом, все модели в плагине будут унаследованы от Base и автоматически будут использовать базу данных плагина.

## Повторное использование настроек базы данных
Конечно, мы можем повторно использовать настройки базы данных основного проекта. Если вы используете [webman-admin](https://www.workerman.net/plugin/82), вы также можете повторно использовать [настройки базы данных](https://www.workerman.net/plugin/82) webman-admin, например:
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
