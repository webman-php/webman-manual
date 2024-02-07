# Migration 데이터베이스 이전 도구 Phinx

## 설명

Phinx를 사용하면 개발자가 데이터베이스를 간결하게 수정하고 유지할 수 있습니다. 이는 인간의 손으로 SQL 문을 작성하는 것을 피하고 강력한 PHP API를 사용하여 데이터베이스 이전을 관리합니다. 개발자는 버전 관리를 사용하여 데이터베이스 이전을 관리할 수 있습니다. Phinx를 사용하여 서로 다른 데이터베이스 간에 데이터 이동을 쉽게 할 수 있습니다. 또한 실행된 이전 스크립트를 추적할 수 있으므로 개발자는 더 나은 시스템을 작성하는 데 집중할 수 있습니다.

## 프로젝트 주소

https://github.com/cakephp/phinx

## 설치

```php
composer require robmorgan/phinx
```

## 공식 중국어 문서 주소

자세한 사용법은 공식 중국어 문서를 참조하고, 여기에서는 webman에서의 구성 및 사용 방법에 대해 설명합니다.

https://tsy12321.gitbooks.io/phinx-doc/content/

## 이전 파일 디렉토리 구조

```   
.
├── app                           응용 프로그램 디렉토리
│   ├── controller                컨트롤러 디렉토리
│   │   └── Index.php             컨트롤러
│   ├── model                     모델 디렉토리
......
├── database                      데이터베이스 파일
│   ├── migrations                이전 파일
│   │   └── 20180426073606_create_user_table.php
│   ├── seeds                     테스트 데이터
│   │   └── UserSeeder.php
......
```

## phinx.php 설정

프로젝트 루트 디렉토리에 phinx.php 파일을 만듭니다.

```php
<?php
return [
    "paths" => [
        "migrations" => "database/migrations",
        "seeds"      => "database/seeds"
    ],
    "environments" => [
        "default_migration_table" => "phinxlog",
        "default_database"        => "dev",
        "default_environment"     => "dev",
        "dev" => [
            "adapter" => "DB_CONNECTION",
            "host"    => "DB_HOST",
            "name"    => "DB_DATABASE",
            "user"    => "DB_USERNAME",
            "pass"    => "DB_PASSWORD",
            "port"    => "DB_PORT",
            "charset" => "utf8"
        ]
    ]
];
```

## 사용 권고사항

이전 파일은 코드가 병합된 후에 다시 수정할 수 없고, 문제가 발생하면 수정 또는 삭제 작업 파일을 새로 만들어야 합니다.

#### 데이터 테이블 생성 작업 파일 명명 규칙

`{time(자동 생성)}_create_{테이블명 영문 소문자}`

#### 데이터 테이블 수정 작업 파일 명명 규칙

`{time(자동 생성)}_modify_{테이블명 영문 소문자+특정 수정사항 영문 소문자}`

### 데이터 테이블 삭제 작업 파일 명명 규칙

`{time(자동 생성)}_delete_{테이블명 영문 소문자+특정 수정사항 영문 소문자}`

### 데이터 채우기 파일 명명 규칙

`{time(자동 생성)}_fill_{테이블명 영문 소문자+특정 수정사항 영문 소문자}`
