웹맨은 기본적으로 [jenssegers/mongodb](https://github.com/jenssegers/laravel-mongodb)를 MongoDB 구성 요소로 사용하며, 이는 Laravel 프로젝트에서 분리된 것으로, 사용법은 Laravel과 동일합니다.

`jenssegers/mongodb`를 사용하기 전에는 `php-cli`에 MongoDB 확장을 설치해야합니다.

> 다음 명령어를 사용하여 `php-cli`에 MongoDB 확장이 설치되었는지 확인할 수 있습니다. 주의: `php-fpm`에 MongoDB 확장을 설치했더라도 `php-cli`에서 사용할 수 있는 것은 아닙니다. 왜냐하면 `php-cli`와 `php-fpm`은 다른 응용 프로그램이며 서로 다른 `php.ini` 설정을 사용할 수 있습니다. `php --ini` 명령어를 사용하여 현재 `php-cli`가 사용하는 `php.ini` 설정 파일을 확인할 수 있습니다.

## 설치

PHP>7.2일 경우
```php
composer require -W illuminate/database jenssegers/mongodb ^3.8.0
```
PHP=7.2일 경우
```php
composer require -W illuminate/database jenssegers/mongodb ^3.7.0
```

설치 후에는 restart를 해야합니다(reload는 작동하지 않습니다).

## 구성
`config/database.php` 파일에서 아래와 같이 `mongodb` 연결을 추가합니다:
```php
return [

    'default' => 'mysql',

    'connections' => [

         ...기타 구성은 생략...

        'mongodb' => [
            'driver'   => 'mongodb',
            'host'     => '127.0.0.1',
            'port'     =>  27017,
            'database' => 'test',
            'username' => null,
            'password' => null,
            'options' => [
                // 여기에는 Mongo Driver Manager에 더 많은 설정을 전달할 수 있으며, 전체 매개변수 목록은 https://www.php.net/manual/en/mongodb-driver-manager.construct.php의 "Uri Options"에 나열된 사항을 참조할 수 있습니다.

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

## 더 많은 정보를 보려면 다음을 방문하세요.

https://github.com/jenssegers/laravel-mongodb
