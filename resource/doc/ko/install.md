# 환경 요구

* PHP >= 7.2
* [Composer](https://getcomposer.org/) >= 2.0

### 1. 프로젝트 생성

```php
composer create-project workerman/webman
```

### 2. 실행

webman 디렉토리로 이동

#### Windows 사용자
`windows.bat`을 더블 클릭하거나 `php windows.php`를 실행하여 시작

> **팁**
> 오류가 발생하는 경우 함수가 비활성화되어 있을 수 있으므로 [함수 비활성화 확인](others/disable-function-check.md)을 참조하여 비활성화 해제

#### Linux 사용자
`debug` 모드로 실행 (개발 및 디버깅에 사용)

```php
php start.php start
```

`daemon` 모드로 실행 (실제 환경에 사용)

```php
php start.php start -d
```

> **팁**
> 오류가 발생하는 경우 함수가 비활성화되어 있을 수 있으므로 [함수 비활성화 확인](others/disable-function-check.md)을 참조하여 비활성화 해제

### 3. 접속

브라우저에서 `http://ip주소:8787`로 접속
