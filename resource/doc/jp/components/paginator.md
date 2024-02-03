# ページネーションコンポーネント

## jasongrimes/php-paginator

### プロジェクトアドレス

https://github.com/jasongrimes/php-paginator

### インストール

```php
composer require "jasongrimes/paginator:^1.0.3"
```

### 使用方法

新しい `app/controller/UserController.php` を作成します。
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
**テンプレート（PHPネイティブ）**
新しいテンプレート `app/view/user/get.html` を作成します。
```html
<html>
<head>
  <!-- 組み込み Bootstrap ページネーションスタイル -->
  <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
</head>
<body>

<?= $paginator;?>

</body>
</html>
```

**テンプレート（twig）**
新しいテンプレート `app/view/user/get.html` を作成します。
```html
<html>
<head>
  <!-- 組み込み Bootstrap ページネーションスタイル -->
  <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
</head>
<body>

{% autoescape false %}
{{paginator}}
{% endautoescape %}

</body>
</html>
```

**テンプレート（blade）**
新しいテンプレート `app/view/user/get.blade.php` を作成します。
```html
<html>
<head>
  <!-- 組み込み Bootstrap ページネーションスタイル -->
  <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
</head>
<body>

{!! $paginator !!}

</body>
</html>
```

**テンプレート（thinkphp）**
新しいテンプレート `app/view/user/get.blade.php` を作成します。
```html
<html>
<head>
    <!-- 組み込み Bootstrap ページネーションスタイル -->
    <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
</head>
<body>

<?=$paginator?>

</body>
</html>
```

効果は以下の通りです：
![](../../assets/img/paginator.png)

### もっと詳しく

https://github.com/jasongrimes/php-paginator を訪問してください。
