# 페이징 구성 요소

## jasongrimes/php-paginator

### 프로젝트 주소

https://github.com/jasongrimes/php-paginator
  
### 설치

```php
composer require "jasongrimes/paginator:^1.0.3"
```
  
### 사용

`app/controller/UserController.php` 파일을 만듭니다.
```php
<?php
namespace app\controller;

use support\Request;
use JasonGrimes\Paginator;

class UserController
{
    /**
     * 사용자 목록
     */
    public function get(Request $request)
    {
        $total_items = 1000;
        $items_perPage = 50;
        $current_page = (int)$request->get('page', 1);
        $url_pattern = '/user/get?page=(:num)';
        $paginator = new Paginator($total_items, $items_perPage, $current_page, $url_pattern);
        return view('user/get', ['paginator' => $paginator]);
    }
    
}
```
**템플릿 (php 기본)**
`app/view/user/get.html` 파일을 만듭니다.
```html
<html>
<head>
  <!-- 내장된 부트스트랩 페이지 스타일 지원 -->
  <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
</head>
<body>

<?= $paginator;?>

</body>
</html>
```


**템플릿 (twig)**
`app/view/user/get.html` 파일을 만듭니다.
```html
<html>
<head>
  <!-- 내장된 부트스트랩 페이지 스타일 지원 -->
  <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
</head>
<body>

{% autoescape false %}
{{paginator}}
{% endautoescape %}

</body>
</html>
```


**템플릿 (blade)**
`app/view/user/get.blade.php` 파일을 만듭니다.
```html
<html>
<head>
  <!-- 내장된 부트스트랩 페이지 스타일 지원 -->
  <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
</head>
<body>

{!! $paginator !!}

</body>
</html>
```

**템플릿 (thinkphp)**
`app/view/user/get.blade.php` 파일을 만듭니다.
```html
<html>
<head>
    <!-- 내장된 부트스트랩 페이지 스타일 지원 -->
    <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
</head>
<body>

<?=$paginator?>

</body>
</html>
```

아래는 결과입니다:

![](../../assets/img/paginator.png)
  
### 더 많은 내용

https://github.com/jasongrimes/php-paginator를 방문하세요.
