## ThinkORM

### 安装ThinkORM

`composer config -g --unset repos.packagist && composer -W require psr/container ^1.1.1 webman/think-orm`

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

class Foo
{
    public function get(Request $request)
    {
        $user = Db::table('user')->where('uid', '>', 1)->find();
        return json($user);
    }
}
```
