# ページネーション

# 1. LaravelのORMベースのページング方法
Laravelの`illuminate/database`には便利なページング機能が提供されています。

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

## ページネータインスタンスメソッド
|  メソッド   | 説明  |
|  ----  |-----|
|$paginator->count()|現在のページのデータ総数を取得します|
|$paginator->currentPage()|現在のページ番号を取得します|
|$paginator->firstItem()|結果セット内の最初のデータの番号を取得します|
|$paginator->getOptions()|ページネータのオプションを取得します|
|$paginator->getUrlRange($start, $end)|指定されたページ範囲のURLを作成します|
|$paginator->hasPages()|複数のページを作成するのに十分なデータがあるかどうかをが取得します|
|$paginator->hasMorePages()|表示可能な追加のページがあるかどうかを取得します|
|$paginator->items()|現在のページのデータ項目を取得します|
|$paginator->lastItem()|結果セット内の最後のデータの番号を取得します|
|$paginator->lastPage()|最後のページ番号を取得します（simplePaginateでは使用できません）|
|$paginator->nextPageUrl()|次のページのURLを取得します|
|$paginator->onFirstPage()|現在のページが最初のページであるかを取得します|
|$paginator->perPage()|ページごとに表示するデータ数を取得します|
|$paginator->previousPageUrl()|前のページのURLを取得します|
|$paginator->total()|結果セット内のデータの総数を取得します（simplePaginateでは使用できません）|
|$paginator->url($page)|指定したページのURLを取得します|
|$paginator->getPageName()|ページ番号を格納するためのクエリパラメータ名を取得します|
|$paginator->setPageName($name)|ページ番号を格納するためのクエリパラメータ名を設定します|

> **注意**
> `$paginator->links()` メソッドはサポートされていません

## ページングコンポーネント
webmanでは `$paginator->links()` メソッドを使用してページボタンをレンダリングすることはできませんが、代わりに他のコンポーネントを使用してレンダリングすることができます。例えば、 `jasongrimes/php-paginator` を使用できます。

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

**テンプレート(phpネイティブ)**
新しいテンプレート app/view/user/get.html を作成します。
```html
<html>
<head>
  <!-- 組み込みのBootstrapページングスタイルのサポート -->
  <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
</head>
<body>

<?= $paginator;?>

</body>
</html>
```

**テンプレート(twig)**
新しいテンプレート app/view/user/get.html を作成します。
```html
<html>
<head>
  <!-- 組み込みのBootstrapページングスタイルのサポート -->
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
新しいテンプレート app/view/user/get.blade.php を作成します。
```html
<html>
<head>
  <!-- 組み込みのBootstrapページングスタイルのサポート -->
  <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
</head>
<body>

{!! $paginator !!}

</body>
</html>
```

**テンプレート(thinkphp)**
新しいテンプレート app/view/user/get.html を作成します。
```html
<html>
<head>
    <!-- 組み込みのBootstrapページングスタイルのサポート -->
    <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
</head>
<body>

<?=$paginator?>

</body>
</html>
```

効果は以下のようになります：
![](../../assets/img/paginator.png)

# 2. ThinkphpのORMベースのページング方法
追加のライブラリをインストールする必要はありません。 think-ormをインストールしていれば、利用可能です。

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
    <!-- 組み込みのBootstrapページングスタイルのサポート -->
    <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
</head>
<body>

{$users|raw}

</body>
</html>
```
