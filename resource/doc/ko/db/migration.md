# Migration 데이터베이스 이동 도구 Phinx

## 설명

Phinx를 사용하면 개발자가 데이터베이스를 간결하게 수정하고 유지할 수 있습니다. 이것은 인위적으로 SQL 문을 작성하는 것을 피하며, 강력한 PHP API를 사용하여 데이터베이스 이동을 관리합니다. 개발자들은 버전 관리를 사용하여 데이터베이스 이동을 할 수 있습니다. 또한 Phinx를 사용하여 여러 데이터베이스 간에 데이터 이동을 쉽게 할 수 있습니다. 또한 실행된 이동 스크립트를 추적할 수 있으므로, 개발자는 데이터베이스 상태에 대해 더 이상 걱정하지 않고 더 나은 시스템을 작성하는 데 집중할 수 있습니다.

## 프로젝트 주소

https://github.com/cakephp/phinx

## 설치

  ```php
  composer require robmorgan/phinx
  ```
  
## 공식 중국어 문서 주소

자세한 사용법은 공식 중국어 문서를 참조하십시오. 여기서는 webman에서의 구성 및 사용법에 대해서만 설명하겠습니다.

https://tsy12321.gitbooks.io/phinx-doc/content/

## 이동 파일 디렉토리 구조

```
.
├── app                           애플리케이션 디렉토리
│   ├── controller                컨트롤러 디렉토리
│   │   └── Index.php             컨트롤러
│   ├── model                     모델 디렉토리
......
├── database                      데이터베이스 파일
│   ├── migrations                이동 파일
│   │   └── 20180426073606_create_user_table.php
│   ├── seeds                     테스트 데이터
│   │   └── UserSeeder.php
......
```

## phinx.php 구성

프로젝트 루트 디렉토리에 phinx.php 파일을 생성합니다.

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

## 사용 권장사항

이동 파일은 코드 병합 후에는 더 이상 수정할 수 없으며 문제가 발생하면 수정 또는 삭제 작업 파일을 새로 만들어야 합니다.

#### 데이터 테이블 생성 작업 파일 명명 규칙

`{time(자동 생성)}_create_{테이블명 영문 소문자}`

#### 데이터 테이블 수정 작업 파일 명명 규칙

`{time(자동 생성)}_modify_{테이블명 영문 소문자+구체적 수정 항목 영문 소문자}`

### 데이터 테이블 삭제 작업 파일 명명 규칙

`{time(자동 생성)}_delete_{테이블명 영문 소문자+구체적 수정 항목 영문 소문자}`

### 데이터 채우기 파일 명명 규칙

`{time(자동 생성)}_fill_{테이블명 영문 소문자+구체적 수정 항목 영문 소문자}`
