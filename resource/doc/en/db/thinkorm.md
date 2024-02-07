## ThinkORM

### Installing ThinkORM

`composer require -W webman/think-orm`

After installation, restart is required (reload does not work)

> **Note**
> If the installation fails, it may be because you are using a composer proxy. Try running `composer config -g --unset repos.packagist` to cancel the composer proxy and try again.

> [webman/think-orm](https://www.workerman.net/plugin/14) is actually a plugin for automated installation of `toptink/think-orm`. If your webman version is lower than `1.2` and cannot use plugins, please refer to the article [Manually install and configure think-orm](https://www.workerman.net/a/1289).

### Configuration file
Modify the configuration file `config/thinkorm.php` according to your actual situation.

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

### Creating a model

The ThinkOrm model inherits `think\Model`, as shown below
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

You can also use the following command to create a model based on thinkorm
```
php webman make:model table_name
```

> **Note**
> This command requires the installation of `webman/console`, the installation command is `composer require webman/console ^1.2.13`

> **Note**
> If the make:model command detects that the main project is using `illuminate/database`, it will create a model file based on `illuminate/database`, not thinkorm. In this case, you can force the generation of think-orm models by adding an additional parameter `tp` to the command, similar to `php webman make:model table_name tp` (If it doesn't take effect, please upgrade `webman/console`)
