## 뷰
webman은 기본적으로 PHP 네이티브 구문을 템플릿으로 사용하며, `opcache`를 활성화하면 최상의 성능을 제공합니다. PHP 네이티브 템플릿 외에도 [Twig](https://twig.symfony.com/doc/3.x/)、 [Blade](https://learnku.com/docs/laravel/8.x/blade/9377)、 [think-template](https://www.kancloud.cn/manual/think-template/content) 템플릿 엔진을 제공합니다.

## opcache 활성화
뷰를 사용할 때는 php.ini의 `opcache.enable` 및 `opcache.enable_cli` 옵션을 활성화하는 것이 좋으며, 이를 통해 템플릿 엔진의 최상의 성능을 얻을 수 있습니다.

## Twig 설치
1. composer로 설치
   ```
   composer require twig/twig
   ```
2. `config/view.php` 구성 변경
   ```php
   <?php
   use support\view\Twig;
  
   return [
       'handler' => Twig::class
   ];
   ```
   > **참고**
   > 다른 구성 옵션은 options를 통해 전달할 수 있습니다. 예를 들어
   ```php
   return [
       'handler' => Twig::class,
       'options' => [
           'debug' => false,
           'charset' => 'utf-8'
       ]
   ];
   ```

## Blade 설치
1. composer로 설치
   ```
   composer require psr/container ^1.1.1 webman/blade
   ```
2. `config/view.php` 구성 변경
   ```php
   <?php
   use support\view\Blade;

   return [
       'handler' => Blade::class
   ];
   ```

## think-template 설치
1. composer로 설치
   ```
   composer require topthink/think-template
   ```
2. `config/view.php` 구성 변경
   ```php
   <?php
   use support\view\ThinkPHP;

   return [
       'handler' => ThinkPHP::class,
   ];
   ```
   > **참고**
   > 다른 구성 옵션은 options를 통해 전달할 수 있습니다. 예를 들어
   ```php
   return [
       'handler' => ThinkPHP::class,
       'options' => [
           'view_suffix' => 'html',
           'tpl_begin' => '{',
           'tpl_end' => '}'
       ]
   ];
   ```

## 네이티브 PHP 템플릿 엔진 예시
다음과 같이 `app/controller/UserController.php` 파일을 생성합니다.
```php
<?php
namespace app\controller;

use support\Request;

class UserController
{
    public function hello(Request $request)
    {
        return view('user/hello', ['name' => 'webman']);
    }
}
```
다음과 같이 `app/view/user/hello.html` 파일을 생성합니다.
```html
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>webman</title>
</head>
<body>
hello <?=htmlspecialchars($name)?>
</body>
</html>
```

## Twig 템플릿 엔진 예시
`config/view.php`를 다음과 같이 수정합니다.
```php
<?php
use support\view\Twig;

return [
    'handler' => Twig::class
];
```
다음과 같이 `app/controller/UserController.php` 파일을 작성합니다.
```php
<?php
namespace app\controller;

use support\Request;

class UserController
{
    public function hello(Request $request)
    {
        return view('user/hello', ['name' => 'webman']);
    }
}
```
다음과 같이 `app/view/user/hello.html` 파일을 작성합니다.
```html
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>webman</title>
</head>
<body>
hello {{name}}
</body>
</html>
```
자세한 내용은[Twig](https://twig.symfony.com/doc/3.x/)를 참조하세요.

## Blade 템플릿 예시
`config/view.php`를 다음과 같이 수정합니다.
```php
<?php
use support\view\Blade;

return [
    'handler' => Blade::class
];
```
다음과 같이 `app/controller/UserController.php` 파일을 작성합니다.
```php
<?php
namespace app\controller;

use support\Request;

class UserController
{
    public function hello(Request $request)
    {
        return view('user/hello', ['name' => 'webman']);
    }
}
```
다음과 같이 `app/view/user/hello.blade.php` 파일을 작성합니다.
> blade 템플릿의 경우 확장자는`.blade.php` 입니다.
```html
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>webman</title>
</head>
<body>
hello {{$name}}
</body>
</html>
```
자세한 내용은 [Blade](https://learnku.com/docs/laravel/8.x/blade/9377)를 참조하세요.

## ThinkPHP 템플릿 예시
`config/view.php`를 다음과 같이 수정합니다.
```php
<?php
use support\view\ThinkPHP;

return [
    'handler' => ThinkPHP::class
];
```
다음과 같이 `app/controller/UserController.php` 파일을 작성합니다.
```php
<?php
namespace app\controller;

use support\Request;

class UserController
{
    public function hello(Request $request)
    {
        return view('user/hello', ['name' => 'webman']);
    }
}
```
다음과 같이 `app/view/user/hello.html` 파일을 작성합니다.
```html
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>webman</title>
</head>
<body>
hello {$name}
</body>
</html>
```
자세한 내용은 [think-template](https://www.kancloud.cn/manual/think-template/content)를 참조하세요.

## 템플릿 할당
`view(템플릿, 변수 배열)`을 사용하여 템플릿에 할당하는 것 외에도 어디서든 `View::assign()`을 호출하여 템플릿에 값을 할당할 수 있습니다. 예를 들어:
```php
<?php
namespace app\controller;

use support\Request;
use support\View;

class UserController
{
    public function hello(Request $request)
    {
        View::assign([
            'name1' => 'value1',
            'name2'=> 'value2',
        ]);
        View::assign('name3', 'value3');
        return view('user/test', ['name' => 'webman']);
    }
}
```
`View::assign()`은 일부 상황에서 매우 유용합니다. 예를 들어 어떤 시스템의 각 페이지 상단에는 현재 로그인한 사용자 정보를 표시해야 하는 경우, 각 페이지에 이 정보를 `view('템플릿', ['user_info' => '사용자 정보']);`를 통해 할당하는 것은 매우 번거로워집니다. 해결 방법은 미들웨어에서 사용자 정보를 얻고, 그런 다음 `View::assign()`를 사용하여 템플릿에 사용자 정보를 할당하는 것입니다.

## 뷰 파일 경로에 대해

#### 컨트롤러
컨트롤러가 `view('템플릿명',[]);`을 호출할 때, 뷰 파일은 다음과 같은 규칙에 따라 찾습니다:

1. 다중 애플리케이션을 사용하지 않는 경우, `app/view/`에서 해당하는 뷰 파일을 사용합니다.
2. [다중 애플리케이션](multiapp.md)을 사용하는 경우, `app/애플리케이션명/view/`에서 해당하는 뷰 파일을 사용합니다.

종합하면, `$request->app`이 비어있을 경우 `app/view/`의 뷰 파일을 사용하고, 그렇지 않은 경우 `app/{$request->app}/view/`의 뷰 파일을 사용합니다.

#### 클로저 함수
클로저 함수의 경우 `$request->app`이 비어있으므로, 클로저 함수는 `app/view/`의 뷰 파일을 사용합니다. 예를 들어 `config/route.php`에서 라우트를 정의할 경우:
```php
Route::any('/admin/user/get', function (Request $request) {
    return view('user', []);
});
```
`app/view/user.html` 파일을 모든 클로저 함수의 템플릿 파일로 사용합니다(Blade 템플릿을 사용하는 경우 `app/view/user.blade.php` 파일을 사용한다).

#### 애플리케이션 지정
다중 애플리케이션 모드에서 템플릿을 재사용할 수 있도록 하기 위해 `view($template, $data, $app = null)`은 세 번째 매개변수 `$app`을 사용하여 어떤 애플리케이션 디렉토리의 템플릿을 사용할지 지정할 수 있습니다. 예를 들어 `view('user', [], 'admin');`의 경우, `app/admin/view/`의 뷰 파일을 강제로 사용합니다.

## Twig 확장
> **참고**
> 이 기능은 webman-framework>=1.4.8 이상이 필요합니다.

`view.extension` 설정을 통해 Twig 뷰 인스턴스를 확장할 수 있습니다. 예를 들어 `config/view.php`는 다음과 같습니다.
```php
<?php
use support\view\Twig;
return [
    'handler' => Twig::class,
    'extension' => function (Twig\Environment $twig) {
        $twig->addExtension(new your\namespace\YourExtension()); // 확장 추가
        $twig->addFilter(new Twig\TwigFilter('rot13', 'str_rot13')); // 필터 추가
        $twig->addFunction(new Twig\TwigFunction('function_name', function () {})); // 함수 추가
    }
];
```
## 블레이드 확장
> **주의**
> 이 기능은 webman-framework>=1.4.8이 필요합니다.
동일한 방법으로 `view.extension` 콜백을 구성하여 블레이드 뷰 인스턴스를 확장할 수 있습니다. 예를들어 `config/view.php`은 다음과 같습니다.

```php
<?php
use support\view\Blade;
return [
    'handler' => Blade::class,
    'extension' => function (Jenssegers\Blade\Blade $blade) {
        // 블레이드에 지시문 추가
        $blade->directive('mydate', function ($timestamp) {
            return "<?php echo date('Y-m-d H:i:s', $timestamp); ?>";
        });
    }
];
```

## 블레이드에서 component 구성 사용

> **주의**
> webman/blade>=1.5.2 가 필요합니다.

Alert component를 추가해야하는 경우

**`app/view/components/Alert.php`를 만듭니다**.
```php
<?php

namespace app\view\components;

use Illuminate\View\Component;

class Alert extends Component
{
    
    public function __construct()
    {
    
    }
    
    public function render()
    {
        return view('components/alert')->rawBody();
    }
}
```

**`app/view/components/alert.blade.php`를 만듭니다**.
```php
<div>
    <b style="color: red">hello blade component</b>
</div>
```

**`/config/view.php`는 다음과 같은 코드입니다.**
```php
<?php
use support\view\Blade;
return [
    'handler' => Blade::class,
    'extension' => function (Jenssegers\Blade\Blade $blade) {
        $blade->component('alert', app\view\components\Alert::class);
    }
];
```

이로써 Blade component Alert가 설정되었습니다. 템플릿에서 사용하면 다음과 같습니다.
```html
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>webman</title>
</head>
<body>

<x-alert/>

</body>
</html>
```


## think-template 확장
think-template은 `view.options.taglib_pre_load`를 사용하여 태그 라이브러리를 확장합니다. 예를들어
```php
<?php
use support\view\ThinkPHP;
return [
    'handler' => ThinkPHP::class,
    'options' => [
        'taglib_pre_load' => your\namspace\Taglib::class,
    ]
];
```

자세한 내용은 [think-template tag extension](https://www.kancloud.cn/manual/think-template/1286424)를 참조하십시오.
