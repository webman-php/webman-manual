# 分頁組件

## jasongrimes/php-paginator

### 項目地址

https://github.com/jasongrimes/php-paginator
  
### 安裝

```php
composer require "jasongrimes/paginator:^1.0.3"
```
  
### 使用

新建 `app/controller/UserController.php`
```php
<?php
namespace app\controller;

use support\Request;
use JasonGrimes\Paginator;

class UserController
{
    /**
     * 用戶列表
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
**模板(php原生)**
新建模版 app/view/user/get.html
```html
<html>
<head>
  <!-- 內置支持 Bootstrap 分頁樣式 -->
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
  <!-- 內置支持 Bootstrap 分頁樣式 -->
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
  <!-- 內置支持 Bootstrap 分頁樣式 -->
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
    <!-- 內置支持 Bootstrap 分頁樣式 -->
    <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
</head>
<body>

<?=$paginator?>

</body>
</html>
```

效果如下：
![](../../assets/img/paginator.png)
  
### 更多內容

訪問 https://github.com/jasongrimes/php-paginator
