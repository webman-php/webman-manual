# 빠른 시작

webman 데이터베이스는 기본적으로 [illuminate/database](https://github.com/illuminate/database)를 사용하며, 이것은 [laravel의 데이터베이스](https://learnku.com/docs/laravel/8.x/database/9400)입니다. 이를 사용하는 방법은 라라벨과 동일합니다.

물론 [다른 데이터베이스 구성 사용](others.md) 장에서 ThinkPHP 또는 다른 데이터베이스를 사용할 수 있습니다.

## 설치

`composer require -W illuminate/database illuminate/pagination illuminate/events symfony/var-dumper`

설치 후에는 재시작(restart)이 필요합니다(reload는 작동하지 않음)

> **팁**
> 페이징, 데이터베이스 이벤트, SQL 출력이 필요하지 않은 경우에는
> `composer require -W illuminate/database` 명령만 실행하면 됩니다.

## 데이터베이스 구성
`config/database.php`
```php

return [
    // 기본 데이터베이스
    'default' => 'mysql',

    // 각종 데이터베이스 구성
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
        return response("hello $name");
    }
}
```
