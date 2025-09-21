# 数据库

由于大部分插件都会安装[webman-admin](https://www.workerman.net/plugin/82)，所以建议直接复用`webman-admin`的数据库配置。

模型基类使用`plugin\admin\app\model\Base`则会自动使用`webman-admin`的数据库配置。
```php
<?php

namespace plugin\foo\app\model;

use plugin\admin\app\model\Base;

class Orders extends Base
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'foo_orders';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'id';
    
}
```

也可以通过`plugin.admin.mysql`操作`webman-admin`的数据库，例如

```php
Db::connection('plugin.admin.mysql')->table('user')->first();
```


## 使用自己的数据库
插件也可以选择使用自己的数据库，例如`plugin/foo/config/database.php`内容如下
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

#### 给Model配置数据库

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

## 自动导入数据库
运行 ` php webman app-plugin:create foo` 会自动创建foo插件，其中包含 `plugin/foo/api/Install.php` 和 `plugin/foo/install.sql`。

> **提示**
> 如果没有生成install.sql文件请尝试升级`webman/console`，命令为`composer require webman/console ^1.3.6`

#### 插件安装时导入数据库
安装插件时会执行Install.php里的`install`方法，该方法会自动导入`install.sql`里的sql语句，从而实现自动导入数据库表的目的。

`install.sql`文件内容是创建数据库表以及对表历史修改sql语句，注意每个语句必须用`;`结束，例如
```sql
CREATE TABLE `foo_orders` (
  `id` int NOT NULL AUTO_INCREMENT COMMENT '主键',
  `order_id` varchar(50) NOT NULL COMMENT '订单id',
  `user_id` int NOT NULL COMMENT '用户id',
  `total_amount` decimal(10,2) NOT NULL COMMENT '须支付金额',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB COMMENT='订单';

CREATE TABLE `foo_goods` (
  `id` int NOT NULL AUTO_INCREMENT COMMENT '主键',
  `name` varchar(50) NOT NULL COMMENT '名称',
  `price` int NOT NULL COMMENT '价格',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB COMMENT='商品';
```

**更改数据库连接**

`install.sql`导入的目标数据库默认为`webman-admin`的数据库，如果想导入到其它数据库，可以修改`Install.php`里的`$connection`属性，例如
```php
<?php

class Install
{
    // 指定插件自己的数据库
    protected static $connection = 'plugin.admin.mysql';
    
    // ...
}
```

**测试**

执行`php webman app-plugin:install foo`安装插件，然后查看数据库，会发现`foo_orders`和`foo_goods`表已经创建了。

#### 插件升级时更改表结构
有时候插件升级需要新建表或更改表结构，可以直接在`install.sql`后面追加对应的语句即可，注意每个语句后面用`;`结束，例如追加一个`foo_user`表以及给`foo_orders`表增加一个`status`字段
```sql
CREATE TABLE `foo_orders` (
  `id` int NOT NULL AUTO_INCREMENT COMMENT '主键',
  `order_id` varchar(50) NOT NULL COMMENT '订单id',
  `user_id` int NOT NULL COMMENT '用户id',
  `total_amount` decimal(10,2) NOT NULL COMMENT '须支付金额',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB COMMENT='订单';

CREATE TABLE `foo_goods` (
 `id` int NOT NULL AUTO_INCREMENT COMMENT '主键',
 `name` varchar(50) NOT NULL COMMENT '名称',
 `price` int NOT NULL COMMENT '价格',
 PRIMARY KEY (`id`)
) ENGINE=InnoDB COMMENT='商品';


CREATE TABLE `foo_user` (
 `id` int NOT NULL AUTO_INCREMENT COMMENT '主键',
 `name` varchar(50) NOT NULL COMMENT '名字'
 PRIMARY KEY (`id`)
) ENGINE=InnoDB COMMENT='用户';

ALTER TABLE `foo_orders` ADD `status` tinyint NOT NULL DEFAULT 0 COMMENT '状态';
```

升级时会执行Install.php里的`update`方法，该方法同样会执行`install.sql`里的语句，如果有新的语句会执行新的语句，如果是旧的语句会跳过，从而实现升级对数据库的修改。

#### 卸载插件时删除数据库
卸载插件时`Install.php`的`uninstall`方法会被调用，它会自动分析`install.sql`里有哪些建表语句，并自动删除这些表，达到卸载插件时删除数据库表的目的。
如果想卸载时只想执行自己的`uninstall.sql`，不执行自动的删表操作，则只需要创建`plugin/插件名/uninstall.sql`即可，这样`uninstall`方法只会执行`uninstall.sql`文件里的语句。