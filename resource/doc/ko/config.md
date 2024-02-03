# 설정 파일

## 위치
webman의 설정 파일은 `config/` 디렉토리에 있으며, 프로젝트에서는 `config()` 함수를 사용하여 해당 설정을 가져올 수 있습니다.

## 설정 가져오기

모든 설정 가져오기
```php
config();
```

`config/app.php`의 모든 설정 가져오기
```php
config('app');
```

`config/app.php`의 `debug` 설정 가져오기
```php
config('app.debug');
```

설정이 배열인 경우 `.`을 사용하여 내부 요소의 값을 가져올 수 있습니다. 예를 들면
```php
config('file.key1.key2');
```

## 기본 값
```php
config($key, $default);
```
두 번째 매개변수를 사용하여 기본 값을 전달하고, 설정이 없는 경우 기본 값을 반환합니다.
설정이 없고 기본 값이 지정되지 않은 경우에는 null을 반환합니다.

## 사용자 정의 설정
개발자는 `config/` 디렉토리에 자신의 설정 파일을 추가할 수 있으며, 예를 들어

**config/payment.php**

```php
<?php
return [
    'key' => '...',
    'secret' => '...'
];
```

**설정 가져오기**
```php
config('payment');
config('payment.key');
config('payment.key');
```

## 설정 변경
webman은 설정을 동적으로 변경하는 것을 지원하지 않으며, 모든 설정은 해당 설정 파일을 수동으로 수정하고 다시로드하거나 다시 시작해야 합니다.

> **주의**
> 서버 설정인 `config/server.php` 및 프로세스 설정인 `config/process.php`는 다시로드를 지원하지 않으며, 다시 시작해야만 변경 사항이 적용됩니다.
