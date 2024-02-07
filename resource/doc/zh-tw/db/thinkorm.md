## ThinkORM

### 安裝ThinkORM

`composer require -W webman/think-orm`

安裝後需要restart重啟(reload無效)

> **提示**
> 如果安裝失敗，可能是因為你使用了composer代理，嘗試運行 `composer config -g --unset repos.packagist` 取消composer代理試下

> [webman/think-orm](https://www.workerman.net/plugin/14) 實際上是一個自動化安裝`toptink/think-orm` 的插件，如果你的webman版本低於`1.2`無法使用插件請參考文章[手動安裝並配置think-orm](https://www.workerman.net/a/1289)。

### 配置文件
根據實際情況修改配置文件 `config/thinkorm.php`

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

### 創建模型

ThinkOrm模型繼承`think\Model`，類似如下
```php
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

你也使用以下命令創建基於thinkorm的模型
```shell
php webman make:model 表名
```

> **提示**
> 此命令需要安裝`webman/console`，安裝命令為`composer require webman/console ^1.2.13`

> **注意**
> make:model 命令如果檢測到主專案使用了`illuminate/database`，會創建基於`illuminate/database`的模型文件，而不是thinkorm的，這時可以通過附加一個參數tp來強制生成think-orm的模型，命令類似 `php webman make:model 表名 tp` (如果不生效請升級`webman/console`)
