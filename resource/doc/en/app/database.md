# Database
Plugins can configure their own databases, for example `plugin/foo/config/database.php` content as follows
```php
return  [
    'default' => 'mysql',
    'connections' => [
        'mysql' => [ // mysql is the connection name
            'driver'      => 'mysql',
            'host'        => '127.0.0.1',
            'port'        => 3306,
            'database'    => 'database',
            'username'    => 'username',
            'password'    => 'password',
            'charset'     => 'utf8mb4',
            'collation'   => 'utf8mb4_general_ci',
        ],
        'admin' => [ // admin is the connection name
            'driver'      => 'mysql',
            'host'        => '127.0.0.1',
            'port'        => 3306,
            'database'    => 'database',
            'username'    => 'username',
            'password'    => 'password',
            'charset'     => 'utf8mb4',
            'collation'   => 'utf8mb4_general_ci',
        ],
    ],
];
```
The reference method is `Db::connection('plugin.{plugin}.{connection name}');`, for example
```php
use support\Db;
Db::connection('plugin.foo.mysql')->table('user')->first();
Db::connection('plugin.foo.admin')->table('admin')->first();
```

If you want to use the main project database, you can use it directly, for example
```php
use support\Db;
Db::table('user')->first();
// Assume the main project also configured an admin connection
Db::connection('admin')->table('admin')->first();
```

## Configuring the database for the Model

We can create a Base class for the Model, and use `$connection` to specify the plugin's own database connection, for example

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

This way, all Models in the plugin inherit from Base and automatically use the plugin's own database.

## Reusing the database configuration
Of course, we can reuse the main project's database configuration. If [webman-admin](https://www.workerman.net/plugin/82) is integrated, we can also reuse the [webman-admin](https://www.workerman.net/plugin/82) database configuration, for example
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
