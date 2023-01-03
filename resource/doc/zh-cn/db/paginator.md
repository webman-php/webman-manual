# 分页
Laravel的`illuminate/database`提供了方便的分页功能。

## 安装
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

## 分页器实例方法
|  方法   | 描述  |
|  ----  |-----|
|$paginator->count()|获取当前页的数据总数|
|$paginator->currentPage()|获取当前页码|
|$paginator->firstItem()|获取结果集中第一个数据的编号|
|$paginator->getOptions()|获取分页器选项|
|$paginator->getUrlRange($start, $end)|创建指定页数范围的 URL|
|$paginator->hasPages()|是否有足够多的数据来创建多个页面|
|$paginator->hasMorePages()|是否有更多的页面可供展示|
|$paginator->items()|获取当前页的数据项|
|$paginator->lastItem()|获取结果集中最后一个数据的编号|
|$paginator->lastPage()|获取最后一页的页码（在 simplePaginate 中不可用）|
|$paginator->nextPageUrl()|获取下一页的 URL|
|$paginator->onFirstPage()|当前页是否为第一页|
|$paginator->perPage()|获取每一页显示的数量总数|
|$paginator->previousPageUrl()|获取上一页的 URL|
|$paginator->total()|获取结果集中的数据总数（在 simplePaginate 中不可用）|
|$paginator->url($page)|获取指定页的 URL|
|$paginator->getPageName()|获取用于储存页码的查询参数名|
|$paginator->setPageName($name)|设置用于储存页码的查询参数名|

> **注意**
> 不支持 `$paginator->links()` 方法

## 分页组件
webman中无法使用 `$paginator->links()` 方法渲染分页按钮，不过我们可以使用其他组件来渲染，例如 `jasongrimes/php-paginator` 。

**安装**
`composer require "jasongrimes/paginator:~1.0"`


**后端**
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
  <!-- 内置支持 Bootstrap 分页样式 -->
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
  <!-- 内置支持 Bootstrap 分页样式 -->
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
  <!-- 内置支持 Bootstrap 分页样式 -->
  <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
</head>
<body>

{!! $paginator !!}

</body>
</html>
```

**模板(thinkphp)**
新建模版 app/view/user/get.blade.php
```html
<html>
<head>
    <!-- 内置支持 Bootstrap 分页样式 -->
    <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
</head>
<body>

<?=$paginator?>

</body>
</html>
```

效果如下：
![](../components/img/paginator.png)
