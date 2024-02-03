## ThinkORM

### Install ThinkORM

Run the following command to install ThinkORM:

```bash
composer require -W webman/think-orm
```

After installation, you need to restart the server (reload won't work).

> **Note**
> If the installation fails, it may be due to your use of a composer proxy. Try running `composer config -g --unset repos.packagist` to disable the composer proxy.

> [webman/think-orm](https://www.workerman.net/plugin/14) is actually a plugin for automatically installing `toptink/think-orm`. If your webman version is lower than `1.2`, and you cannot use the plugin, please refer to the article [Manual installation and configuration of think-orm](https://www.workerman.net/a/1289).

### Configuration File
Modify the configuration file as needed: `config/thinkorm.php`.

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

### Create a Model

ThinkOrm models inherit `think\Model` and look like this:
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

You can also use the following command to create models based on thinkorm:
```bash
php webman make:model table_name
```

> **Note**
> This command requires the installation of `webman/console`, which can be done using the command `composer require webman/console ^1.2.13`.

> **Attention**
> If the `make:model` command detects that the main project uses `illuminate/database`, it will create a model file based on `illuminate/database` instead of thinkorm. In this case, you can force the generation of a think-orm model by appending a 'tp' parameter to the command, like `php webman make:model table_name tp` (if this doesn't work, please upgrade `webman/console`).