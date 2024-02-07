# ページネーション

# 1. LaravelのORMに基づいたページネーション
Laravelの`illuminate/database`は便利なページネーション機能を提供しています。

## インストール
`composer require illuminate/pagination`

## 使用方法
```php
public function index(Request $request)
{
    $per_page = 10;
    $users = Db::table('user')->paginate($per_page);
    return view('index/index', ['users' => $users]);
}
```

## ページネーションのインスタンスメソッド
|  メソッド   | 説明  |
|  ----  |-----|
|$paginator->count()|現在のページのデータ総数を取得|
|$paginator->currentPage()|現在のページ番号を取得|
|$paginator->firstItem()|結果セット内の最初のデータの番号を取得|
|$paginator->getOptions()|ページネータのオプションを取得|
|$paginator->getUrlRange($start, $end)|指定されたページ番号範囲のURLを作成|
|$paginator->hasPages()|複数のページを作成するための十分なデータがあるかどうか|
|$paginator->hasMorePages()|表示可能な追加のページがあるかどうか|
|$paginator->items()|現在のページのデータ項目を取得|
|$paginator->lastItem()|結果セット内の最後のデータの番号を取得|
|$paginator->lastPage()|最後のページのページ番号を取得（simplePaginateでは使用不可）|
|$paginator->nextPageUrl()|次のページのURLを取得|
|$paginator->onFirstPage()|現在のページが最初のページかどうか|
|$paginator->perPage()|1ページに表示するデータの総数を取得|
|$paginator->previousPageUrl()|前のページのURLを取得|
|$paginator->total()|結果セット内のデータ総数を取得（simplePaginateでは使用不可）|
|$paginator->url($page)|指定されたページのURLを取得|
|$paginator->getPageName()|ページ番号を格納するためのクエリパラメータ名を取得|
|$paginator->setPageName($name)|ページ番号を格納するためのクエリパラメータ名を設定|

> **注意**
> `$paginator->links()` メソッドはサポートされていません

## ページネーションコンポーネント
Webmanでは`$paginator->links()` メソッドを使用してページネーションボタンをレンダリングすることはできませんが、他のコンポーネントを使用してレンダリングすることができます。例えば `jasongrimes/php-paginator` を使用することができます。

**インストール**
`composer require "jasongrimes/paginator:~1.0"`

**バックエンド**
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

**テンプレート(php原生)**
新しいテンプレートを作成 app/view/user/get.html
```html
<html>
<head>
  <!-- Bootstrapページネーションスタイルをネイティブサポート -->
  <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
</head>
<body>

<?= $paginator;?>

</body>
</html>
```

**テンプレート(twig)**
新しいテンプレートを作成 app/view/user/get.html
```html
<html>
<head>
  <!-- Bootstrapページネーションスタイルをネイティブサポート -->
  <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
</head>
<body>

{% autoescape false %}
{{paginator}}
{% endautoescape %}

</body>
</html>
```

**テンプレート(blade)**
新しいテンプレートを作成 app/view/user/get.blade.php
```html
<html>
<head>
  <!-- Bootstrapページネーションスタイルをネイティブサポート -->
  <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
</head>
<body>

{!! $paginator !!}

</body>
</html>
```

**テンプレート(thinkphp)**
新しいテンプレートを作成 app/view/user/get.html
```html
<html>
<head>
    <!-- Bootstrapページネーションスタイルをネイティブサポート -->
    <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
</head>
<body>

<?=$paginator?>

</body>
</html>
```

効果は以下の通りです：
![](../../assets/img/paginator.png)

# 2. ThinkphpのORMに基づいたページネーション方法
追加のライブラリをインストールする必要はありません。think-ormがインストールされていれば使用できます。

## 使用方法
```php
public function index(Request $request)
{
    $per_page = 10;
    $users = Db::table('user')->paginate(['list_rows' => $per_page, 'page' => $request->get('page', 1), 'path' => $request->path()]);
    return view('index/index', ['users' => $users]);
}
```

**テンプレート(thinkphp)**
```html
<html>
<head>
    <!-- Bootstrapページネーションスタイルをネイティブサポート -->
    <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
</head>
<body>

{$users|raw}

</body>
</html>
```
