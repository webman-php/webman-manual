# 데이터베이스
플러그인은 자체 데이터베이스를 구성할 수 있으며, 예를 들어 `plugin/foo/config/database.php`의 내용은 다음과 같습니다.
```php
return  [
    'default' => 'mysql',
    'connections' => [
        'mysql' => [ // mysql은 연결 이름입니다
            'driver'      => 'mysql',
            'host'        => '127.0.0.1',
            'port'        => 3306,
            'database'    => '데이터베이스',
            'username'    => '사용자 이름',
            'password'    => '암호',
            'charset'     => 'utf8mb4',
            'collation'   => 'utf8mb4_general_ci',
        ],
        'admin' => [ // admin은 연결 이름입니다
            'driver'      => 'mysql',
            'host'        => '127.0.0.1',
            'port'        => 3306,
            'database'    => '데이터베이스',
            'username'    => '사용자 이름',
            'password'    => '암호',
            'charset'     => 'utf8mb4',
            'collation'   => 'utf8mb4_general_ci',
        ],
    ],
];
```
참조 방법은 `Db::connection('plugin.{플러그인}.{연결 이름}');`입니다. 예를 들어
```php
use support\Db;
Db::connection('plugin.foo.mysql')->table('user')->first();
Db::connection('plugin.foo.admin')->table('admin')->first();
```
프로젝트의 기본 데이터베이스를 사용하려면 직접 사용하면 됩니다. 예를 들어
```php
use support\Db;
Db::table('user')->first();
// 가정컨대, 기본 프로젝트에 admin 연결을 구성했다고 가정합시다
Db::connection('admin')->table('admin')->first();
```

## 모델에 데이터베이스 구성
모델마다 `Base` 클래스를 만들어 `$connection`을 사용하여 플러그인의 데이터베이스 연결을 지정할 수 있습니다. 예를 들어
```php
<?php

namespace plugin\foo\app\model;

use DateTimeInterface;
use support\Model;

class Base extends Model
{
    /**
     * @var string
     */
    protected $connection = 'plugin.foo.mysql';

}
```
이렇게 하면 플러그인 내의 모든 모델이 `Base`를 상속하게 되며 자동으로 플러그인의 데이터베이스를 사용하게 됩니다.

## 데이터베이스 구성 재사용
또한 주 프로젝트의 데이터베이스 구성을 재사용할 수 있습니다. [webman-admin](https://www.workerman.net/plugin/82)을 통합했다면 [webman-admin](https://www.workerman.net/plugin/82) 데이터베이스 구성을 재사용할 수도 있습니다. 예를 들어
```php
<?php

namespace plugin\foo\app\model;

use DateTimeInterface;
use support\Model;

class Base extends Model
{
    /**
     * @var string
     */
    protected $connection = 'plugin.admin.mysql';

}
```
