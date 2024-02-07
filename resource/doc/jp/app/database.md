# データベース
プラグインは独自のデータベースを構成することができます。例えば、`plugin/foo/config/database.php`の内容は以下の通りです
```php
return  [
    'default' => 'mysql',
    'connections' => [
        'mysql' => [ // mysqlが接続名
            'driver'      => 'mysql',
            'host'        => '127.0.0.1',
            'port'        => 3306,
            'database'    => 'データベース',
            'username'    => 'ユーザー名',
            'password'    => 'パスワード',
            'charset'     => 'utf8mb4',
            'collation'   => 'utf8mb4_general_ci',
        ],
        'admin' => [ // adminが接続名
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
参照方法は`Db::connection('plugin.{プラグイン}.{接続名}');`、例えば
```php
use support\Db;
Db::connection('plugin.foo.mysql')->table('user')->first();
Db::connection('plugin.foo.admin')->table('admin')->first();
```

主プロジェクトのデータベースを使用したい場合、直接使用できます。例えば
```php
use support\Db;
Db::table('user')->first();
// 主プロジェクトでadmin接続も構成されていると仮定します
Db::connection('admin')->table('admin')->first();
```

## Modelにデータベースを構成する
ModelにBaseクラスを作成し、Baseクラスで`$connection`を使用してプラグイン独自のデータベース接続を指定できます。例えば

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

これにより、プラグイン内のすべてのModelはBaseを継承しており、自動的にプラグイン独自のデータベースを使用します。

## データベース構成の再利用
もちろん、主プロジェクトのデータベース構成を再利用することもできます。[webman-admin](https://www.workerman.net/plugin/82)を使用している場合は、[webman-admin](https://www.workerman.net/plugin/82)のデータベース構成も再利用できます。例えば
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
