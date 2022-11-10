## ThinkORM

### InstallThinkORM

`composer -W require psr/container ^1.1.1 webman/think-orm`

> **hint**
> ifInstallfailed，Downloaded file nameUsage了composerproxy，AgentTry `composer config -g --unset repos.packagist` cancelcomposerUserInfo

> [webman/think-orm](https://www.workerman.net/plugin/14) is actually an automationInstall`toptink/think-orm` plugins，illegal accesswebmanused in`1.2`UnableUsagePlease refer to the article for the plugin[ManualInstalland configurethink-orm](https://www.workerman.net/a/1289)。

### configuration file
Modify configuration file according to actual situation `config/thinkorm.php`

### Usage

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

### Create Model

ThinkOrmalso provides`think\Model`，Similar to the following
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

You also use the following command to create a thinkorm-based model
```
php webman make:model Table Name
```

> **hint**
> This command requires the installation of `webman/console`, the installation command is`composer require webman/console ^1.2.13`

> **Note**
> make:model  command if it detects that the main project uses `illuminate/database`, it will create a model file based on `illuminate/database` instead of think-orm's, in which case you can force the generation of think-orm's model by appending a parameter tp, with a command like `php webman make:model table name tp` (if it doesn't work, please upgrade `webman/console`)


