# 환경 요구사항

* PHP >= 7.2
* [Composer](https://getcomposer.org/) >= 2.0

### 1. 프로젝트 생성

```php
composer create-project workerman/webman
```

### 2. 실행

webman 디렉토리로 이동

#### Windows 사용자
`windows.bat`을 더블 클릭하거나 `php windows.php`를 실행하여 시작합니다.

> **팁**
> 에러가 발생하면 일부 함수가 비활성화되어 있을 수 있으므로 [비활성화된 함수 확인](others/disable-function-check.md)을 참조하여 비활성화를 해제하세요.

#### Linux 사용자
`debug` 모드로 실행 (개발 및 디버깅용)

```php
php start.php start
```

`daemon` 모드로 실행 (본격적인 환경용)

```php
php start.php start -d
```

> **팁**
> 에러가 발생하면 일부 함수가 비활성화되어 있을 수 있으므로 [비활성화된 함수 확인](others/disable-function-check.md)을 참조하여 비활성화를 해제하세요.

### 3. 접속

브라우저에서 `http://ip주소:8787`을 입력하여 접속합니다.
