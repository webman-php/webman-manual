webman은 기본적으로 PHP 기본 구문을 템플릿으로 사용하며, `opcache`를 활성화하면 최상의 성능을 얻을 수 있습니다. 또한, webman은 [Twig](https://twig.symfony.com/doc/3.x/)、 [Blade](https://learnku.com/docs/laravel/8.x/blade/9377)、 [think-template](https://www.kancloud.cn/manual/think-template/content) 템플릿 엔진을 제공합니다.

## opcache 활성화
템플릿 사용 시, php.ini의 `opcache.enable` 및 `opcache.enable_cli` 옵션을 활성화하는 것이 좋으며, 이렇게 하면 템플릿 엔진이 최상의 성능을 발휘할 수 있습니다.

## Twig 설치
1. composer를 사용하여 설치합니다.

    `composer require twig/twig`

2. 구성을 변경하여 `config/view.php`를 다음과 같이 합니다.
   ```php
   <?php
   use support\view\Twig;

   return [
       'handler' => Twig::class
   ];
   ```
   > **참고**
   > 다른 구성 옵션은 options로 전달됩니다. 예를 들어  

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
1. composer를 사용하여 설치합니다.

    `composer require psr/container ^1.1.1 webman/blade`

2. 구성을 변경하여 `config/view.php`를 다음과 같이 합니다.
   ```php
   <?php
   use support\view\Blade;

   return [
       'handler' => Blade::class
   ];
   ```

## think-template 설치
1. composer를 사용하여 설치합니다.

    `composer require topthink/think-template`

2. 구성을 변경하여 `config/view.php`를 다음과 같이 합니다.
   ```php
   <?php
   use support\view\ThinkPHP;

   return [
       'handler' => ThinkPHP::class,
   ];
   ```
   > **참고**
   > 다른 구성 옵션은 options로 전달됩니다. 예를 들어

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

## 기본 PHP 템플릿 엔진 예시
다음과 같이 파일 `app/controller/UserController.php`를 작성합니다.

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

다음과 같이 파일 `app/view/user/hello.html`을 작성합니다.

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
`config/view.php`를 다음과 같이 변경합니다.
```php
<?php
use support\view\Twig;

return [
    'handler' => Twig::class
];
```

`app/controller/UserController.php`를 다음과 같이 작성합니다.
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

`app/view/user/hello.html` 파일을 다음과 같이 작성합니다.

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

더 많은 문서는 [Twig](https://twig.symfony.com/doc/3.x/)에서 확인할 수 있습니다.

## Blade 템플릿 예시
`config/view.php`를 다음과 같이 변경합니다.
```php
<?php
use support\view\Blade;

return [
    'handler' => Blade::class
];
```

`app/controller/UserController.php`를 다음과 같이 작성합니다.
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

`app/view/user/hello.blade.php` 파일을 다음과 같이 작성합니다. 
> 참고: blade 템플릿 확장자는 `.blade.php`입니다.

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

더 많은 문서는 [Blade](https://learnku.com/docs/laravel/8.x/blade/9377)에서 확인할 수 있습니다.

## ThinkPHP 템플릿 예시
`config/view.php`를 다음과 같이 변경합니다.
```php
<?php
use support\view\ThinkPHP;

return [
    'handler' => ThinkPHP::class
];
```

`app/controller/UserController.php`를 다음과 같이 작성합니다.
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

`app/view/user/hello.html` 파일을 다음과 같이 작성합니다.

```php
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

더 많은 문서는 [think-template](https://www.kancloud.cn/manual/think-template/content)에서 확인할 수 있습니다.

## 템플릿 할당
`view(템플릿, 변수 배열)`을 사용하여 템플릿에 값을 할당하는 것 외에도 `View::assign()`을 호출하여 어디에서든지 템플릿에 값을 할당할 수 있습니다. 예를 들어:

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

`View::assign()`은 특정 상황에서 매우 유용하며, 예를 들어 시스템의 각 페이지 상단에 현재 로그인한 사용자 정보를 표시해야하는 경우에 매우 편리합니다. 모든 페이지에 이 정보를 `view('템플릿', ['user_info' => '사용자 정보']);` 형식으로 할당하는 것은 매우 귀찮을 수 있습니다. 이 문제를 해결하기 위해 미들웨어에서 사용자 정보를 가져 와서 `View::assign()`를 통해 사용자 정보를 템플릿에 할당할 수 있습니다.

## 뷰 파일 경로에 대해
#### 컨트롤러
컨트롤러가 `view('템플릿명',[]);`를 호출할 때, 뷰 파일은 다음과 같은 규칙으로 찾습니다.

1. 다중 애플리케이션이 아닌 경우, `app/view/` 아래에서 해당하는 뷰 파일을 사용합니다.
2. [다중 애플리케이션](multiapp.md)인 경우, `app/애플리케이션명/view/` 아래에서 해당하는 뷰 파일을 사용합니다.

요약하자면, `$request->app`이 비어있다면 `app/view/` 아래의 뷰 파일을 사용하고, 그렇지 않다면 `app/{$request->app}/view/` 아래의 뷰 파일을 사용합니다.

#### 클로저 함수
클로저 함수의 경우 `$request->app`이 비어있고, 어떤 애플리케이션에 속하지 않기 때문에 `app/view/` 아래의 뷰 파일을 사용합니다. 예를 들어 `config/route.php`에서 라우트를 정의하는 경우 다음과 같이 사용됩니다.
```php
Route::any('/admin/user/get', function (Reqeust $reqeust) {
    return view('user', []);
});
```
이는 `app/view/user.html`을 모텝 파일로 사용합니다(blade 템플릿을 사용할 때는 `app/view/user.blade.php`).

#### 특정 애플리케이션 지정
다중 애플리케이션 모드에서 뷰가 재사용되도록 하려면 `view($template, $data, $app = null)` 함수의 세 번째 매개변수 `$app`을 사용하여 뷰 파일을 지정할 수 있습니다. 예를들어 `view('user', [], 'admin');`를 사용하면 `app/admin/view/` 아래의 뷰 파일을 강제로 사용합니다.

## Twig 확장

> **참고**
> 이 기능은 webman-framework>=1.4.8 이상을 필요로 합니다

`config/view.php`에 대한 `view.extension`을 콜백하여 Twig 뷰 인스턴스를 확장할 수 있습니다. 예를들어`config/view.php`는 다음과 같습니다.
```php
<?php
use support\view\Twig;
return [
    'handler' => Twig::class,
    'extension' => function (Twig\Environment $twig) {
        $twig->addExtension(new your\namespace\YourExtension()); // Extension 추가
        $twig->addFilter(new Twig\TwigFilter('rot13', 'str_rot13')); // 필터 추가
        $twig->addFunction(new Twig\TwigFunction('function_name', function () {})); // 함수 추가
    }
];
```

## Blade 확장
> **참고**
> 이 기능은 webman-framework>=1.4.8 이상을 필요로 합니다

마찬가지로 `config/view.php`에 대한 `view.extension`을 콜백하여 Blade 뷰 인스턴스를 확장할 수 있습니다. 예를들어 `config/view.php`는 다음과 같습니다.

```php
<?php
use support\view\Blade;
return [
    'handler' => Blade::class,
    'extension' => function (Jenssegers\Blade\Blade $blade) {
        // Blade 지시어 추가
        $blade->directive('mydate', function ($timestamp) {
            return "<?php echo date('Y-m-d H:i:s', $timestamp); ?>";
        });
    }
];
```

## blade에서 컴포넌트 사용

> **참고
> webman/blade>=1.5.2가 필요합니다**

예를 들어 Alert 컴포넌트를 추가해야하는 경우

**`app/view/components/Alert.php`를 만듭니다.**
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

**`app/view/components/alert.blade.php`를 만듭니다.**
```
<div>
    <b style="color: red">hello blade component</b>
</div>
```

**`/config/view.php`의 유사 코드**
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

다음으로, Blade 컴포넌트 Alert를 설정했습니다. 이제 템플릿에서 사용할 때 다음과 같습니다.
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
think-template에서는 `view.options.taglib_pre_load`를 사용하여 태그 라이브러리를 확장할 수 있습니다. 예를 들어
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
자세한 내용은 [think-template 태그 확장](https://www.kancloud.cn/manual/think-template/1286424)에서 확인할 수 있습니다.
