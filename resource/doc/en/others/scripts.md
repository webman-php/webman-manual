# Custom Scripts

Sometimes we need to write ad-hoc scripts where we can call arbitrary classes or interfaces like webman to do operations like data import, data update statistics, etc. This is something that is already very easy in webman, for example：

**New `scripts/update.php`** (Please create your own directory if it does not exist)
```php
<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../support/bootstrap.php';

use think\facade\Db;

$user = Db::table('user')->find(1);

var_dump($user);
```

Of course we can also use`webman/console`Custom commands to do such operations，See[command-line](../plugin/console.md)
