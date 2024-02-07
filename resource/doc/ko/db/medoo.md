## Medoo

Medoo는 가벼운 데이터베이스 조작 플러그인입니다. [Medoo 공식 웹사이트](https://medoo.in/).

## 설치
`composer require webman/medoo`

## 데이터베이스 구성
구성 파일은 `config/plugin/webman/medoo/database.php`에 위치합니다.

## 사용법
```php
<?php
namespace app\controller;

use support\Request;
use Webman\Medoo\Medoo;

class Index
{
    public function index(Request $request)
    {
        $user = Medoo::get('user', '*', ['uid' => 1]);
        return json($user);
    }
}
```

> **팁**
> `Medoo::get('user', '*', ['uid' => 1]);`
> 와 같습니다
> `Medoo::instance('default')->get('user', '*', ['uid' => 1]);`

## 다중 데이터베이스 구성

**구성**  
`config/plugin/webman/medoo/database.php`에 새 구성을 추가하십시오. 키는 임의로 선택할 수 있으며, 여기에서 'other'를 사용했습니다.

```php
<?php
return [
    'default' => [
        'type' => 'mysql',
        'host' => 'localhost',
        'database' => 'database',
        'username' => 'username',
        'password' => 'password',
        'charset' => 'utf8mb4',
        'collation' => 'utf8mb4_general_ci',
        'port' => 3306,
        'prefix' => '',
        'logging' => false,
        'error' => PDO::ERRMODE_EXCEPTION,
        'option' => [
            PDO::ATTR_CASE => PDO::CASE_NATURAL
        ],
        'command' => [
            'SET SQL_MODE=ANSI_QUOTES'
        ]
    ],
    // 여기에 'other' 구성이 추가되었습니다
    'other' => [
        'type' => 'mysql',
        'host' => 'localhost',
        'database' => 'database',
        'username' => 'username',
        'password' => 'password',
        'charset' => 'utf8mb4',
        'collation' => 'utf8mb4_general_ci',
        'port' => 3306,
        'prefix' => '',
        'logging' => false,
        'error' => PDO::ERRMODE_EXCEPTION,
        'option' => [
            PDO::ATTR_CASE => PDO::CASE_NATURAL
        ],
        'command' => [
            'SET SQL_MODE=ANSI_QUOTES'
        ]
    ],
];
```

**사용**
```php
$user = Medoo::instance('other')->get('user', '*', ['uid' => 1]);
```

## 자세한 문서
[Medoo 공식 문서](https://medoo.in/api/select)를 참조하십시오.
