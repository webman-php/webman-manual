# 사용자 지정 스크립트

가끔 우리는 일시적인 스크립트를 작성해야 합니다. 이러한 스크립트를 사용하여 데이터 가져오기, 데이터 업데이트 및 통계 작업과 같은 작업을 수행할 수 있습니다. 이러한 작업은 webman에서 매우 쉽게 수행할 수 있습니다. 예를 들어 다음과 같이 할 수 있습니다.

**새로 만들기 `scripts/update.php`** (디렉토리가 없는 경우 직접 만드세요)

```php
<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../support/bootstrap.php';

use think\facade\Db;

$user = Db::table('user')->find(1);

var_dump($user);
```

물론 `webman/console`을 사용하여 사용자 정의 명령을 수행할 수도 있습니다. 자세한 내용은 [커맨드 라인](../plugin/console.md)을 참조하십시오.
