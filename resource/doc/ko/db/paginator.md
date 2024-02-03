# 페이징

# 1. 라라벨 ORM 기반의 페이징 방법
라라벨의 `illuminate/database`는 편리한 페이지 구성 기능을 제공합니다.

## 설치
`composer require illuminate/pagination`

## 사용
```php
public function index(Request $request)
{
    $per_page = 10;
    $users = Db::table('user')->paginate($per_page);
    return view('index/index', ['users' => $users]);
}
```

## 페이징 객체 메소드
|  메소드  | 설명  |
|  ----  |-----|
|$paginator->count()|현재 페이지의 데이터 총 수 가져오기|
|$paginator->currentPage()|현재 페이지 번호 가져오기|
|$paginator->firstItem()|결과 집합에서 첫 번째 데이터의 번호 가져오기|
|$paginator->getOptions()|페이징 옵션 가져오기|
|$paginator->getUrlRange($start, $end)|지정된 페이지 범위의 URL 생성|
|$paginator->hasPages()|여러 페이지를 생성할 충분한 데이터가 있는지 확인하기|
|$paginator->hasMorePages()|더 많은 페이지를 표시할 수 있는지 확인하기|
|$paginator->items()|현재 페이지의 데이터 항목 가져오기|
|$paginator->lastItem()|결과 집합에서 마지막 데이터의 번호 가져오기|
|$paginator->lastPage()|마지막 페이지 번호 가져오기( simplePaginate에서는 사용 불가)|
|$paginator->nextPageUrl()|다음 페이지의 URL 가져오기|
|$paginator->onFirstPage()|현재 페이지가 첫 번째 페이지인지 확인하기|
|$paginator->perPage()|각 페이지에 보여지는 총 수 가져오기|
|$paginator->previousPageUrl()|이전 페이지의 URL 가져오기|
|$paginator->total()|결과 집합의 데이터 총 수 가져오기( simplePaginate에서는 사용 불가)|
|$paginator->url($page)|지정된 페이지의 URL 가져오기|
|$paginator->getPageName()|페이지 번호를 저장하는 데 사용되는 쿼리 매개변수 이름 가져오기|
|$paginator->setPageName($name)|페이지 번호를 저장하는 데 사용되는 쿼리 매개변수 이름 설정하기|

> **주의**
> `$paginator->links()` 메소드는 지원되지 않습니다.

## 페이징 컴포넌트
webman에서는 `$paginator->links()` 메소드를 사용하여 페이지 버튼을 렌더링할 수 없지만, 대신 `jasongrimes/php-paginator`와 같은 다른 컴포넌트를 사용하여 렌더링할 수 있습니다.

**설치**
`composer require "jasongrimes/paginator:~1.0"`


**백엔드**
```php
<?php
namespace app\controller;

use JasonGrimes\Paginator;
use support\Request;
use support\Db;

class UserController
{
    public function get(Request $request)
    {
        $per_page = 10;
        $current_page = $request->input('page', 1);
        $users = Db::table('user')->paginate($per_page, '*', 'page', $current_page);
        $paginator = new Paginator($users->total(), $per_page, $current_page, '/user/get?page=(:num)');
        return view('user/get', ['users' => $users, 'paginator'  => $paginator]);
    }
}
```

**템플릿(php 원시)**
app/view/user/get.html에 새로운 템플릿 추가
```html
<html>
<head>
  <!-- 내장된 Bootstrap 페이지 스타일 지원 -->
  <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
</head>
<body>

<?= $paginator;?>

</body>
</html>
```

**템플릿(twig)**
app/view/user/get.html에 새로운 템플릿 추가
```html
<html>
<head>
  <!-- 내장된 Bootstrap 페이지 스타일 지원 -->
  <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
</head>
<body>

{% autoescape false %}
{{paginator}}
{% endautoescape %}

</body>
</html>
```

**템플릿(blade)**
app/view/user/get.blade.php에 새로운 템플릿 추가
```html
<html>
<head>
  <!-- 내장된 Bootstrap 페이지 스타일 지원 -->
  <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
</head>
<body>

{!! $paginator !!}

</body>
</html>
```

**템플릿(thinkphp)**
app/view/user/get.html에 새로운 템플릿 추가
```html
<html>
<head>
    <!-- 내장된 Bootstrap 페이지 스타일 지원 -->
    <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
</head>
<body>

<?=$paginator?>

</body>
</html>
```

결과는 다음과 같습니다:
![](../../assets/img/paginator.png)

# 2. ThinkPHP ORM 기반의 페이징 방법
추가적인 라이브러리 설치가 필요하지 않으며, think-orm을 설치한 상태에서 사용할 수 있습니다.
## 사용
```php
public function index(Request $request)
{
    $per_page = 10;
    $users = Db::table('user')->paginate(['list_rows' => $per_page, 'page' => $request->get('page', 1), 'path' => $request->path()]);
    return view('index/index', ['users' => $users]);
}
```

**템플릿(thinkphp)**
```html
<html>
<head>
    <!-- 내장된 Bootstrap 페이지 스타일 지원 -->
    <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
</head>
<body>

{$users|raw}

</body>
</html>
```
