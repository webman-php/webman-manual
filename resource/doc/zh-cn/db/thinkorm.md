## ThinkORM

### 安装ThinkORM

`composer require -W webman/think-orm`

安装后需要restart重启(reload无效)

### 配置文件
根据实际情况修改配置文件 `config/thinkorm.php`

### 文档地址
https://www.kancloud.cn/manual/think-orm

### 使用

```php
<?php
namespace app\controller;

use support\Request;
use think\facade\Db;

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

ThinkOrm模型继承`think\Model`，类似如下
```
<?php
namespace app\model;

use think\Model;

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

你也使用以下命令创建基于thinkorm的模型
```
php webman make:model 表名
```

> **提示**
> 此命令需要安装`webman/console`，安装命令为`composer require webman/console ^1.2.13`

> **注意**
> make:model 命令如果检测到主项目使用了`illuminate/database`，会创建基于`illuminate/database`的模型文件，而不是thinkorm的，这时可以通过附加一个参数tp来强制生成think-orm的模型，命令类似 `php webman make:model 表名 tp` (如果不生效请升级`webman/console`)


