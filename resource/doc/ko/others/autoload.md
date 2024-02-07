# 자동로딩

## composer를 사용하여 PSR-0 규격 파일 로드
Webman은 `PSR-4` 자동로딩 규격을 준수합니다. 비즈니스에 `PSR-0` 규격의 코드 라이브러리를 로드해야 하는 경우 다음 절차를 따르십시오.

- `extend` 디렉토리를 만들어 `PSR-0` 규격의 코드 라이브러리를 저장합니다.
- `composer.json` 파일을 편집하여 `autoload`에 다음 내용을 추가합니다.

```js
"psr-0" : {
    "": "extend/"
}
```
최종 결과는 아래와 유사합니다.
![](../../assets/img/psr0.png)

- `composer dumpautoload`를 실행합니다.
- `php start.php restart`를 실행하여 webman을 다시 시작합니다. (주의: 다시 시작해야만 변경 사항이 적용됩니다)

## composer를 사용하여 특정 파일 로드

- `composer.json` 파일을 편집하여 `autoload.files`에 로드할 파일을 추가합니다.
```json
"files": [
    "./support/helpers.php",
    "./app/helpers.php"
]
```
- `composer dumpautoload`를 실행합니다.
- `php start.php restart`를 실행하여 webman을 다시 시작합니다. (주의: 다시 시작해야만 변경 사항이 적용됩니다)

> **팁**
> composer.json의 `autoload.files` 구성은 webman이 시작되기 전에 파일을 로드합니다. 한편, 프레임워크 `config/autoload.php`로 로드되는 파일은 webman이 시작된 후에 로드됩니다.
> composer.json의 `autoload.files`로 로드된 파일은 변경 후 재시작해야만 적용되며, 리로드로는 적용되지 않습니다. 반면, 프레임워크 `config/autoload.php`로 로드되는 파일은 핫 로드를 지원하며, 변경 후 리로드로 적용됩니다.

## 프레임워크를 사용하여 특정 파일 로드
일부 파일은 SPR 규격에 부합하지 않아 자동로딩을 할 수 없습니다. 이럴 때 `config/autoload.php`를 구성하여 이러한 파일을 로드할 수 있습니다. 예를 들어:

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
 > 우리는 `autoload.php`에서 `support/Request.php` 및 `support/Response.php` 두 파일을 로드하도록 설정했습니다. 이는 `vendor/workerman/webman-framework/src/support/`에 동일한 두 파일이 있기 때문입니다. `autoload.php`를 통해 프로젝트 루트 디렉토리의 `support/Request.php` 및 `support/Response.php`를 우선 로드하여, 이 두 파일의 내용을 수정할 수 있게 합니다. `vendor`의 파일을 수정할 필요 없이 이 두 파일을 사용자 지정화할 수 있게 합니다. 사용자 지정화가 필요하지 않다면 이 두 구성을 무시할 수 있습니다.
