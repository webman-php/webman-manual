## ThinkORM

### 安装ThinkORM

`composer require -W webman/think-orm`

安装后需要restart重启(reload无效)

> **提示**
> 如果安装失败，可能是因为你使用了composer代理，尝试运行 `composer config -g --unset repos.packagist` 取消composer代理试下

> [webman/think-orm](https://www.workerman.net/plugin/14) 实际上是一个自动化安装`toptink/think-orm` 的插件，如果你的webman版本低于`1.2`无法使用插件请参考文章[手动安装并配置think-orm](https://www.workerman.net/a/1289)。

### 配置文件
根据实际情况修改配置文件 `config/thinkorm.php`

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


