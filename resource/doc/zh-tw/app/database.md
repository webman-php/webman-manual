# 資料庫
插件可以配置自己的資料庫，例如 `plugin/foo/config/database.php` 的內容如下
```php
return  [
    'default' => 'mysql',
    'connections' => [
        'mysql' => [ // mysql為連接名
            'driver'      => 'mysql',
            'host'        => '127.0.0.1',
            'port'        => 3306,
            'database'    => '資料庫',
            'username'    => '使用者名稱',
            'password'    => '密碼',
            'charset'     => 'utf8mb4',
            'collation'   => 'utf8mb4_general_ci',
        ],
        'admin' => [ // admin為連接名
            'driver'      => 'mysql',
            'host'        => '127.0.0.1',
            'port'        => 3306,
            'database'    => '資料庫',
            'username'    => '使用者名稱',
            'password'    => '密碼',
            'charset'     => 'utf8mb4',
            'collation'   => 'utf8mb4_general_ci',
        ],
    ],
];
```
引用方式為 `Db::connection('plugin.{插件}.{連接名}');`，例如
```php
use support\Db;
Db::connection('plugin.foo.mysql')->table('user')->first();
Db::connection('plugin.foo.admin')->table('admin')->first();
```

如果想使用主項目的資料庫，則直接使用即可，例如
```php
use support\Db;
Db::table('user')->first();
// 假設主項目還配置了一個admin連接
Db::connection('admin')->table('admin')->first();
```

## 給Model配置資料庫

我們可以為Model創建一個Base類，Base類用 `$connection` 指定插件自己的資料庫連接，例如

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
這樣插件裡所有的Model繼承自Base，就自動使用了插件自己的資料庫。

## 複用資料庫配置

當然我們可以複用主項目的資料庫配置，如果接入了 [webman-admin](https://www.workerman.net/plugin/82) ，也可以複用 [webman-admin](https://www.workerman.net/plugin/82) 資料庫配置，例如
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
