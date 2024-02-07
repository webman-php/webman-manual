# 빠른 시작

webman에서는 기본적으로 [illuminate/database](https://github.com/illuminate/database)를 사용합니다. 이는 [laravel의 데이터베이스](https://learnku.com/docs/laravel/8.x/database/9400)와 동일한 사용법을 가지고 있습니다.

물론, 다른 데이터베이스 구성을 사용하려면 [다른 데이터베이스 구성 사용](others.md) 섹션을 참고할 수 있습니다.

## 설치

`composer require -W illuminate/database illuminate/pagination illuminate/events symfony/var-dumper`

설치 후에는 restart로 재시작해야합니다(reload는 작동하지 않습니다).

> **팁**
> 페이지네이션, 데이터베이스 이벤트, SQL 출력이 필요 없다면, 다음 명령만 실행하면 됩니다.
> `composer require -W illuminate/database`

## 데이터베이스 구성
`config/database.php`
```php

return [
    // 기본 데이터베이스
    'default' => 'mysql',

    // 다양한 데이터베이스 구성
    'connections' => [
        'mysql' => [
            'driver'      => 'mysql',
            'host'        => '127.0.0.1',
            'port'        => 3306,
            'database'    => 'test',
            'username'    => 'root',
            'password'    => '',
            'unix_socket' => '',
            'charset'     => 'utf8',
            'collation'   => 'utf8_unicode_ci',
            'prefix'      => '',
            'strict'      => true,
            'engine'      => null,
            'options' => [
                \PDO::ATTR_TIMEOUT => 3
            ]
        ],
    ],
];
```

## 사용
```php
<?php
namespace app\controller;

use support\Request;
use support\Db;

class UserController
{
    public function db(Request $request)
    {
        $default_uid = 29;
        $uid = $request->get('uid', $default_uid);
        $name = Db::table('users')->where('uid', $uid)->value('username');
        return response("안녕 $name");
    }
}
```
