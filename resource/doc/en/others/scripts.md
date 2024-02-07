# Custom Scripts

Sometimes we need to write some temporary scripts, in which we can call any class or interface just like webman does, to complete operations such as data import, data update, and statistics. This is already very easy to do in webman, for example:

**Create `scripts/update.php`** (create the directory if it does not exist)
```php
<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../support/bootstrap.php';

use think\facade\Db;

$user = Db::table('user')->find(1);

var_dump($user);
```

Of course, we can also use `webman/console` to create custom commands to perform such operations, as described in [command line](../plugin/console.md).
