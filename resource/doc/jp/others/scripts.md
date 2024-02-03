# カスタムスクリプト

時々、一時的なスクリプトを書く必要があります。これらのスクリプトでは、webmanのように任意のクラスやインターフェースを呼び出すことができ、データのインポート、データの更新、統計などの操作を行うことができます。webmanでは、これは非常に簡単なことです。例えば：

**`scripts/update.php`を新規作成** （ディレクトリが存在しない場合は自分で作成してください）
```php
<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../support/bootstrap.php';

use think\facade\Db;

$user = Db::table('user')->find(1);

var_dump($user);
```

もちろん、`webman/console`を使用してこのような操作を行うカスタムコマンドも作成できます。詳細については[コマンドライン](../plugin/console.md)を参照してください。
