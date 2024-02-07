# 分頁

# 1. 基於Laravel的ORM的分頁方式
Laravel的`illuminate/database`提供了方便的分頁功能。

## 安裝
`composer require illuminate/pagination`

## 使用
```php
public function index(Request $request)
{
    $per_page = 10;
    $users = Db::table('user')->paginate($per_page);
    return view('index/index', ['users' => $users]);
}
```

## 分頁器實例方法
|  方法   | 描述  |
|  ----  |-----|
|$paginator->count()|獲取當前頁的數據總數|
|$paginator->currentPage()|獲取當前頁碼|
|$paginator->firstItem()|獲取結果集中第一個數據的編號|
|$paginator->getOptions()|獲取分頁器選項|
|$paginator->getUrlRange($start, $end)|創建指定頁數範圍的 URL|
|$paginator->hasPages()|是否有足夠多的數據來創建多個頁面|
|$paginator->hasMorePages()|是否有更多的頁面可供展示|
|$paginator->items()|獲取當前頁的數據項|
|$paginator->lastItem()|獲取結果集中最後一個數據的編號|
|$paginator->lastPage()|獲取最後一頁的頁碼（在 simplePaginate 中不可用）|
|$paginator->nextPageUrl()|獲取下一頁的 URL|
|$paginator->onFirstPage()|當前頁是否為第一頁|
|$paginator->perPage()|獲取每一頁顯示的數量總數|
|$paginator->previousPageUrl()|獲取上一頁的 URL|
|$paginator->total()|獲取結果集中的數據總數（在 simplePaginate 中不可用）|
|$paginator->url($page)|獲取指定頁的 URL|
|$paginator->getPageName()|獲取用於儲存頁碼的查詢參數名|
|$paginator->setPageName($name)|設置用於儲存頁碼的查詢參數名|
> **注意**
> 不支持 `$paginator->links()` 方法

## 分頁組件
在webman中無法使用 `$paginator->links()` 方法渲染分頁按鈕，不過我們可以使用其他組件來渲染，例如 `jasongrimes/php-paginator` 。

**安裝**
`composer require "jasongrimes/paginator:~1.0"`


**後端**
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

**模板(php原生)**
新建模版 app/view/user/get.html
```html
<html>
<head>
  <!-- 內置支援 Bootstrap 分頁樣式 -->
  <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
</head>
<body>

<?= $paginator;?>

</body>
</html>
```

**模板(twig)** 
新建模版 app/view/user/get.html
```html
<html>
<head>
  <!-- 內置支援 Bootstrap 分頁樣式 -->
  <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
</head>
<body>

{% autoescape false %}
{{paginator}}
{% endautoescape %}

</body>
</html>
```

**模板(blade)** 
新建模版 app/view/user/get.blade.php
```html
<html>
<head>
  <!-- 內置支援 Bootstrap 分頁樣式 -->
  <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
</head>
<body>

{!! $paginator !!}

</body>
</html>
```

**模板(thinkphp)**
新建模版 app/view/user/get.html
```html
<html>
<head>
    <!-- 內置支援 Bootstrap 分頁樣式 -->
    <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
</head>
<body>

<?=$paginator?>

</body>
</html>
```

效果如下：
![](../../assets/img/paginator.png)

# 2. 基於Thinkphp的ORM的分頁方式
無須額外安裝類庫,只要安裝過think-orm即可
## 使用
```php
public function index(Request $request)
{
    $per_page = 10;
    $users = Db::table('user')->paginate(['list_rows' => $per_page, 'page' => $request->get('page', 1), 'path' => $request->path()]);
    return view('index/index', ['users' => $users]);
}
```

**模板(thinkphp)**
```html
<html>
<head>
    <!-- 內置支援 Bootstrap 分頁樣式 -->
    <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
</head>
<body>

{$users|raw}

</body>
</html>
```
