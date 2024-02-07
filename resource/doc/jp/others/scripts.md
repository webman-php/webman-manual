# カスタムスクリプト

時には一時的なスクリプトを書く必要があります。これらのスクリプトでは、webmanと同様に任意のクラスやインターフェースを呼び出して、データのインポート、データの更新、統計などの操作を行うことができます。これはwebmanでは非常に簡単に行うことができます。例えば：

**`scripts/update.php`を作成する**（ディレクトリが存在しない場合は自分で作成してください）
```php
<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../support/bootstrap.php';

use think\facade\Db;

$user = Db::table('user')->find(1);

var_dump($user);
```

もちろん、`webman/console`を使用してこのような操作を行うためのカスタムコマンドも作成することができます。詳細は[コマンドライン](../plugin/console.md)を参照してください。
