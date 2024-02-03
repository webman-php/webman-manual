# 자동로드

## composer를 사용하여 PSR-0 규격 파일을 로드하기
webman은 `PSR-4` 자동로드 규격을 준수합니다. 비즈니스에서 `PSR-0` 규격의 코드 라이브러리를 로드해야 하는 경우 다음 단계를 참고하십시오.

- `PSR-0` 규격의 코드 라이브러리를 저장할 `extend` 디렉토리를 만듭니다.
- `composer.json` 파일을 편집하여 아래 내용을 `autoload` 아래에 추가합니다.

```js
"psr-0" : {
    "": "extend/"
}
```
결과적으로 다음과 유사하게 됩니다.
![](../../assets/img/psr0.png)

- `composer dumpautoload` 명령어를 실행합니다.
- `php start.php restart` 명령어를 실행하여 webman을 다시 시작합니다(주의: 다시 시작해야만 적용됩니다).

## composer를 사용하여 특정 파일 로드하기

- `composer.json` 파일을 편집하여 `autoload.files`에 로드하려는 파일을 추가합니다.
```
"files": [
    "./support/helpers.php",
    "./app/helpers.php"
]
```

- `composer dumpautoload` 명령어를 실행합니다.
- `php start.php restart` 명령어를 실행하여 webman을 다시 시작합니다(주의: 다시 시작해야만 적용됩니다).

> **팁**
> composer.json 파일의 `autoload.files` 설정 파일은 webman이 시작되기 전에 로드됩니다. 반면에 프레임워크에서 로드하는 `config/autoload.php` 파일은 webman이 시작된 후에 로드됩니다. 
> composer.json 파일의 `autoload.files`에 로드된 파일은 변경 후에 다시 시작해야만 적용되며, 다시 로드해서는 적용되지 않습니다. 반면에 프레임워크에서 로드하는 `config/autoload.php` 파일은 변경 후에 다시 로드하여 적용됩니다.

## 프레임워크를 사용하여 특정 파일 로드하기
일부 파일은 SPR 규격과 일치하지 않아 자동으로 로드할 수 없습니다. 이러한 파일은 `config/autoload.php` 파일을 구성하여 로드할 수 있습니다. 예를 들어:

```php
return [
    'files' => [
        base_path() . '/app/functions.php',
        base_path() . '/support/Request.php', 
        base_path() . '/support/Response.php',
    ]
];
```
 > **팁**
 > `autoload.php`에서 `support/Request.php` 및 `support/Response.php` 파일을 로드하도록 설정되어 있습니다. 이것은 `vendor/workerman/webman-framework/src/support/` 아래에 동일한 파일이 두 개 있기 때문에 가능합니다. 이 설정을 통해 프로젝트 루트 디렉토리의 `support/Request.php` 및 `support/Response.php` 를 먼저 로드할 수 있어서, 이 두 파일의 내용을 수정할 수 있고 `vendor` 폴더의 파일을 수정할 필요가 없습니다. 이를 필요로 하지 않는 경우 이 두 설정을 무시할 수 있습니다.
