# vlucas/phpdotenv

## 소개
`vlucas/phpdotenv`은 환경변수 로딩을 위한 컴포넌트로, 서로 다른 환경(예: 개발 환경, 테스트 환경 등)의 설정을 구분하기 위해 사용됩니다.

## 프로젝트 주소

https://github.com/vlucas/phpdotenv
  
## 설치
 
```php
composer require vlucas/phpdotenv
 ```
  
## 사용

#### 프로젝트 루트 디렉토리에 `.env` 파일 생성
**.env**
```
DB_HOST = 127.0.0.1
DB_PORT = 3306
DB_NAME = test
DB_USER = foo
DB_PASSWORD = 123456
```

#### 설정 파일 수정
**config/database.php**
```php
return [
    // 기본 데이터베이스
    'default' => 'mysql',

    // 다양한 데이터베이스 구성
    'connections' => [
        'mysql' => [
            'driver'      => 'mysql',
            'host'        => getenv('DB_HOST'),
            'port'        => getenv('DB_PORT'),
            'database'    => getenv('DB_NAME'),
            'username'    => getenv('DB_USER'),
            'password'    => getenv('DB_PASSWORD'),
            'unix_socket' => '',
            'charset'     => 'utf8',
            'collation'   => 'utf8_unicode_ci',
            'prefix'      => '',
            'strict'      => true,
            'engine'      => null,
        ],
    ],
];
```

> **참고**
> `.env` 파일을 `.gitignore` 목록에 추가하여 코드 리포지토리에 제출하는 것을 피하십시오. 프로젝트에 `.env.example` 구성 예제 파일을 추가하고, 프로젝트를 배포할 때 `.env.example`을 `.env`으로 복사하여 현재 환경에 맞게 `.env`의 구성을 수정하면, 프로젝트가 다른 환경에서 다른 구성을 로드할 수 있습니다.

> **주의**
> `vlucas/phpdotenv`은 PHP TS 버전(스레드 안전 버전)에서 버그가 있을 수 있으므로 NTS 버전(스레드 안전하지 않은 버전)을 사용해야 합니다.
> 현재 php 버전은 `php -v`를 실행하여 확인할 수 있습니다.

## 더 많은 내용

https://github.com/vlucas/phpdotenv 방문
