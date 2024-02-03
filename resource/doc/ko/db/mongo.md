웹맨은 기본적으로 [jenssegers/mongodb](https://github.com/jenssegers/laravel-mongodb)를 사용하여 MongoDB 컴포넌트를 구현합니다. 이는 라라벨 프로젝트에서 분리된 것으로 사용 방법은 라라벨과 동일합니다.

`jenssegers/mongodb`를 사용하기 전에 먼저`php-cli`에 MongoDB 확장 기능을 설치해야 합니다.

> `php -m | grep mongodb` 명령을 사용하여 `php-cli`에 MongoDB 확장 기능이 설치되었는지 확인할 수 있습니다. 참고: `php-fpm`에 MongoDB 확장을 설치했더라도 `php-cli`에서 사용할 수 있는 것은 아닙니다. 왜냐하면 `php-cli`와 `php-fpm`은 다른 응용 프로그램이며, 다른 `php.ini` 구성을 사용할 수 있습니다. 사용 중인 `php.ini` 설정 파일은 `php --ini` 명령으로 확인할 수 있습니다.

## 설치

PHP>7.2일 때
```php
composer require -W illuminate/database jenssegers/mongodb ^3.8.0
```
PHP=7.2일 때
```php
composer require -W illuminate/database jenssegers/mongodb ^3.7.0
```

설치 후 restart(다시 시작)해야 합니다(reload는 적용되지 않음)

## 구성
 `config/database.php` 파일에 `mongodb` 연결을 추가하십시오. 다음과 유사한 방식입니다.
```php
return [

    'default' => 'mysql',

    'connections' => [

         ...다른 구성들은 생략...

        'mongodb' => [
            'driver'   => 'mongodb',
            'host'     => '127.0.0.1',
            'port'     =>  27017,
            'database' => 'test',
            'username' => null,
            'password' => null,
            'options' => [
                // 여기에는 Mongo 드라이버 관리자에 더 많은 설정을 전달할 수 있습니다.
                // 사용 가능한 전체 매개변수 목록은 https://www.php.net/manual/en/mongodb-driver-manager.construct.php의 "Uri 옵션"을 참조하십시오.

                'appname' => 'homestead'
            ],
        ],
    ],
];
```

## 예시
```php
<?php
namespace app\controller;

use support\Request;
use support\Db;

class UserController
{
    public function db(Request $request)
    {
        Db::connection('mongodb')->collection('test')->insert([1,2,3]);
        return json(Db::connection('mongodb')->collection('test')->get());
    }
}
```

## 더 많은 정보
https://github.com/jenssegers/laravel-mongodb
