# 自定义脚本

有时我们需要写一些临时脚本，在这些脚本可以像webman一样调用任意的类或接口，完成比如数据导入，数据更新统计等操作。这在webman中是已经非常容易的事情，例如：

**新建 `scripts/update.php`** (目录不存在请自行创建)
```php
<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../support/bootstrap.php';

use think\facade\Db;

$user = Db::table('user')->find(1);

var_dump($user);
```

当然我们也可以使用`webman/console`自定义命令来完成这样的操作，参见[命令行](../plugin/console.md)
