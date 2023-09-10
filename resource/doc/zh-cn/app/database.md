# 数据库
插件可以配置自己的数据库，例如`plugin/foo/config/database.php`内容如下
```php
return  [
    'default' => 'mysql',
    'connections' => [
        'mysql' => [ // mysql为连接名
            'driver'      => 'mysql',
            'host'        => '127.0.0.1',
            'port'        => 3306,
            'database'    => '数据库',
            'username'    => '用户名',
            'password'    => '密码',
            'charset'     => 'utf8mb4',
            'collation'   => 'utf8mb4_general_ci',
        ],
        'admin' => [ // admin为连接名
            'driver'      => 'mysql',
            'host'        => '127.0.0.1',
            'port'        => 3306,
            'database'    => '数据库',
            'username'    => '用户名',
            'password'    => '密码',
            'charset'     => 'utf8mb4',
            'collation'   => 'utf8mb4_general_ci',
        ],
    ],
];
```
引用方式为`Db::connection('plugin.{插件}.{连接名}');`，例如
```php
use support\Db;
Db::connection('plugin.foo.mysql')->table('user')->first();
Db::connection('plugin.foo.admin')->table('admin')->first();
```

如果想使用主项目的数据库，则直接使用即可，例如
```php
use support\Db;
Db::table('user')->first();
// 假设主项目还配置了一个admin连接
Db::connection('admin')->table('admin')->first();
```

## 给Model配置数据库

我们可以为Model创建一个Base类，Base类用`$connection`指定插件自己的数据库连接，例如

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

这样插件里所有的Model继承自Base，就自动使用了插件自己的数据库。

## 复用数据库配置
当然我们可以复用主项目的数据库配置，如果接入了[webman-admin](https://www.workerman.net/plugin/82)，也可以复用[webman-admin](https://www.workerman.net/plugin/82)数据库配置，例如
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

## 数据库表导入  
如果插件用到了自己的数据库表, 需要自行实现导入, 可参考以下方法, 也可以自行实现:  
1. 导入Composer包
   ```bash
   composer require zjkal/mysql-helper
   ```
   **需要提示用户, 插件依赖此包, 引到用户在安装插件之前安装.**
2. 在`plugin/foo/api/Install.php`的install方法中处理, 完整的install方法如下:
   ```php
   public static function install($version)
    {
        // 导入菜单
        if ($menus = static::getMenus()) {
            Menu::import($menus);
        }
        //导入数据库表
        $mysqlHelper = new MysqlHelper();
        $mysqlHelper->setConfig(config('database.connections.mysql'));
        $mysqlHelper->importSqlFile(base_path() . '/plugin/foo/install.sql');
    }
   ```
   **install.sql文件放在`plugin/foo`根目录**
