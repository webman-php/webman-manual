## think-orm

[webman/think-orm](https://github.com/webman-php/think-orm) 是基于 [top-think/think-orm](https://github.com/top-think/think-orm) 开发的数据库组件，支持连接池，支持协程和非协程环境。

> **注意**
> 当前手册为 webman v2 版本，如果您使用的是webman v1版本，请查看 [v1版本手册](/doc/webman-v1/db/thinkorm.html)

### 安装think-orm

`composer require -W webman/think-orm`

安装后需要restart重启(reload无效)

### 配置文件
根据实际情况修改配置文件 `config/think-orm.php`

### 文档地址
https://www.kancloud.cn/manual/think-orm

### 使用

```php
<?php
namespace app\controller;

use support\Request;
use support\think\Db;

class FooController
{
    public function get(Request $request)
    {
        $user = Db::table('user')->where('uid', '>', 1)->find();
        return json($user);
    }
}
```

### 创建模型

think-orm模型继承`support\think\Model`，类似如下
```
<?php
namespace app\model;

use support\think\Model;

class User extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'user';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $pk = 'id';

}
```

你也使用以下命令创建基于think-orm的模型
```
php webman make:model 表名
```

> **提示**
> 此命令需要安装`webman/console`，安装命令为`composer require webman/console ^1.2.13`

> **注意**
> make:model 命令如果检测到主项目使用了`illuminate/database`，会创建基于`illuminate/database`的模型文件，而不是think-orm的，这时可以通过附加一个参数tp来强制生成think-orm的模型，命令类似 `php webman make:model 表名 tp` (如果不生效请升级`webman/console`)


