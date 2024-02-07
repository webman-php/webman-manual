# 自訂腳本

有時我們需要撰寫一些臨時腳本，這些腳本可以像webman一樣呼叫任意的類或接口，執行像資料導入、資料更新統計等操作。在webman中這是非常容易的，例如：

**新建 `scripts/update.php`**（如果目錄不存在，請自行創建）
```php
<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../support/bootstrap.php';

use think\facade\Db;

$user = Db::table('user')->find(1);

var_dump($user);
```

當然，我們也可以使用`webman/console`自訂命令來完成這樣的操作，請參考[命令行](../plugin/console.md)。
