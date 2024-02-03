# 사용자 정의 스크립트

가끔 우리는 일시적인 스크립트를 작성해야 할 때가 있습니다. 이러한 스크립트에서는 웹맨과 마찬가지로 임의의 클래스나 인터페이스를 호출하여 데이터 가져오기, 데이터 업데이트, 통계 등의 작업을 수행할 수 있습니다. 이러한 작업은 웹맨에서 이미 매우 쉽게 할 수 있습니다. 예를 들어:

**`scripts/update.php`를 새로 만듭니다** (디렉토리가 없는 경우 직접 만드세요)
```php
<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../support/bootstrap.php';

use think\facade\Db;

$user = Db::table('user')->find(1);

var_dump($user);
```

물론 이러한 작업은 `webman/console`을 사용하여 사용자 정의 명령으로도 수행할 수 있습니다. 자세한 내용은 [커맨드 라인](../plugin/console.md)을 참조하십시오.
