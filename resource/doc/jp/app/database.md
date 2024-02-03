# データベース

プラグインは独自のデータベースを設定することができます。 たとえば`plugin/foo/config/database.php`の内容は次のようになります

```php
return  [
    'default' => 'mysql',
    'connections' => [
        'mysql' => [ // mysqlは接続名
            'driver'      => 'mysql',
            'host'        => '127.0.0.1',
            'port'        => 3306,
            'database'    => 'データベース',
            'username'    => 'ユーザー名',
            'password'    => 'パスワード',
            'charset'     => 'utf8mb4',
            'collation'   => 'utf8mb4_general_ci',
        ],
        'admin' => [ // adminは接続名
            'driver'      => 'mysql',
            'host'        => '127.0.0.1',
            'port'        => 3306,
            'database'    => 'データベース',
            'username'    => 'ユーザー名',
            'password'    => 'パスワード',
            'charset'     => 'utf8mb4',
            'collation'   => 'utf8mb4_general_ci',
        ],
    ],
];
```

参照方法は`Db::connection('plugin.{プラグイン}.{接続名}');`、たとえば
```php
use support\Db;
Db::connection('plugin.foo.mysql')->table('user')->first();
Db::connection('plugin.foo.admin')->table('admin')->first();
```

主プロジェクトのデータベースを使用したい場合は、直接使用できます。 たとえば
```php
use support\Db;
Db::table('user')->first();
// 主プロジェクトにadmin接続が設定されている場合
Db::connection('admin')->table('admin')->first();
```

## モデルにデータベースを設定する

ModelのBaseクラスを作成し、Baseクラスで`$connection`を使用してプラグイン固有のデータベース接続を指定できます。 たとえば

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

これにより、プラグイン内のすべてのModelはBaseを継承したもので、自動的にプラグイン固有のデータベースを使用します。

## データベース設定の再利用

もちろん、主プロジェクトのデータベース設定を再利用することもできます。 [webman-admin](https://www.workerman.net/plugin/82)を使用している場合は、[webman-admin](https://www.workerman.net/plugin/82)のデータベース設定を再利用することもできます。 たとえば
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
