# ページネーションコンポーネント

## jasongrimes/php-paginator

### プロジェクトのURL

https://github.com/jasongrimes/php-paginator
  
### インストール

```php
composer require "jasongrimes/paginator:^1.0.3"
```
  
### 使用方法

新しい `app/controller/UserController.php` を作成する
```php
<?php
namespace app\controller;

use support\Request;
use JasonGrimes\Paginator;

class UserController
{
    /**
     * ユーザーリスト
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
**テンプレート(生PHP)**<br>新しいテンプレート app/view/user/get.html
```html
<html>
<head>
  <!-- 組み込みのBootstrapページネーションスタイルをサポート -->
  <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
</head>
<body>

<?= $paginator;?>

</body>
</html>
```

**テンプレート(twig)**<br>新しいテンプレート app/view/user/get.html
```html
<html>
<head>
  <!-- 組み込みのBootstrapページネーションスタイルをサポート -->
  <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
</head>
<body>

{% autoescape false %}
{{paginator}}
{% endautoescape %}

</body>
</html>
```

**テンプレート(blade)**<br>新しいテンプレート app/view/user/get.blade.php
```html
<html>
<head>
  <!-- 組み込みのBootstrapページネーションスタイルをサポート -->
  <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
</head>
<body>

{!! $paginator !!}

</body>
</html>
```

**テンプレート(thinkphp)**<br>新しいテンプレート app/view/user/get.blade.php
```html
<html>
<head>
    <!-- 組み込みのBootstrapページネーションスタイルをサポート -->
    <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
</head>
<body>

<?=$paginator?>

</body>
</html>
```

以下は効果です：
![](../../assets/img/paginator.png)

### 追加情報

https://github.com/jasongrimes/php-paginator をご覧ください。
