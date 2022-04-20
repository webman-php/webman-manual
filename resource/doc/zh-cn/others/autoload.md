# 自动加载

## PSR-0
webman遵循`PSR-4`自动加载规范。如果你的业务需要加载`PSR-0`规范的代码库，参考以下操作。

- 新建 `extend` 目录用户存放`PSR-0`规范的代码库
- 编辑`composer.json`，在`"autoload"`下增加以下内容

```js
"psr-0" : {
    "": "extend/"
}
```
最终结果类似
![](../../assets/img/psr0.png)

- 执行 `composer dumpautoload`
- 执行 `php start.php restart` 重启webman (注意，必须重启才能生效) 

## 自动加载某个文件
有些文件可能不符合SPR规范，无法自动加载，我们可以通过配置`config/autoload.php`加载这些文件，例如：
```php
return [
    'files' => [
        base_path() . '/app/functions.php',
        base_path() . '/support/Request.php', 
        base_path() . '/support/Response.php',
    ]
];
```
 > **提示**
 > 我们看到`autoload.php`里设置了加载 `support/Request.php` `support/Response.php`两个文件，这是因为在`vendor/workerman/webman-framework/src/support/`下也有两个相同的文件，我们通过`autoload.php`优先加载项目根目录下的`support/Request.php` `support/Response.php`，这样允许我们可以定制这两个文件的内容而不需要修改`vendor`中的文件。如果你不需要定制它们，则可以忽略这两个配置。

 
## 自定义脚本
有时我们需要写一些临时脚本，在这些脚本可以像webman一样调用任意的类或接口，完成比如数据导入，数据更新统计等操作。这在webman中是已经非常容易的事情，例如：
```php
<?php
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/support/bootstrap.php';

use think\facade\Db;

$user = Db::table('user')->find(1);

var_dump($user);
```

当然我们也可以使用`webman/console`自定义命令来完成这样的操作，参见[命令行](../plugin/console.md)
