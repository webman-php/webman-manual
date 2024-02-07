# Pagination

# 1. Pagination based on Laravel's ORM

The `illuminate/database` of Laravel provides convenient pagination functionality.

## Installation
`composer require illuminate/pagination`

## Usage
```php
public function index(Request $request)
{
    $per_page = 10;
    $users = Db::table('user')->paginate($per_page);
    return view('index/index', ['users' => $users]);
}
```

## Paginator Instance Methods
|  Method   | Description  |
|  ----  |-----|
|$paginator->count()|Get the total number of items for the current page|
|$paginator->currentPage()|Get the current page number|
|$paginator->firstItem()|Get the number of the first item in the results|
|$paginator->getOptions()|Get the pagination options|
|$paginator->getUrlRange($start, $end)|Create URLs for a given page range|
|$paginator->hasPages()|Determine if there are enough items to split into multiple pages|
|$paginator->hasMorePages()|Determine if there are more items in the result set than the last item in the current page|
|$paginator->items()|Get the items for the current page|
|$paginator->lastItem()|Get the number of the last item in the results|
|$paginator->lastPage()|Get the page number of the last available page (not available when using `simplePaginate`)|
|$paginator->nextPageUrl()|Get the URL for the next page|
|$paginator->onFirstPage()|Determine if the current page is the first page|
|$paginator->perPage()|Get the number of items to be displayed per page|
|$paginator->previousPageUrl()|Get the URL for the previous page|
|$paginator->total()|Get the total number of items in the result set (not available when using `simplePaginate`)|
|$paginator->url($page)|Get the URL for a given page|
|$paginator->getPageName()|Get the name of the query string parameter used to store the page number|
|$paginator->setPageName($name)|Set the name of the query string parameter used to store the page number|

> **Note**
> The `$paginator->links()` method is not supported.

## Pagination Components
In webman, the `$paginator->links()` method cannot be used to render pagination buttons. However, we can use other components for rendering, such as `jasongrimes/php-paginator`.

**Installation**
`composer require "jasongrimes/paginator:~1.0"`

**Backend**
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

**Template (PHP Native)**
Create a new template at app/view/user/get.html
```html
<html>
<head>
  <!-- Built-in support for Bootstrap pagination styles -->
  <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
</head>
<body>

<?= $paginator;?>

</body>
</html>
```

**Template (Twig)**
Create a new template at app/view/user/get.html
```html
<html>
<head>
  <!-- Built-in support for Bootstrap pagination styles -->
  <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
</head>
<body>

{% autoescape false %}
{{paginator}}
{% endautoescape %}

</body>
</html>
```

**Template (Blade)**
Create a new template at app/view/user/get.blade.php
```html
<html>
<head>
  <!-- Built-in support for Bootstrap pagination styles -->
  <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
</head>
<body>

{!! $paginator !!}

</body>
</html>
```

**Template (ThinkPHP)**
Create a new template at app/view/user/get.html
```html
<html>
<head>
    <!-- Built-in support for Bootstrap pagination styles -->
    <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
</head>
<body>

<?=$paginator?>

</body>
</html>
```

The effect is as follows:
![](../../assets/img/paginator.png)

# 2. Pagination based on Thinkphp's ORM

No additional library is required. You just need to install think-orm.

## Usage
```php
public function index(Request $request)
{
    $per_page = 10;
    $users = Db::table('user')->paginate(['list_rows' => $per_page, 'page' => $request->get('page', 1), 'path' => $request->path()]);
    return view('index/index', ['users' => $users]);
}
```

**Template (ThinkPHP)**
```html
<html>
<head>
    <!-- Built-in support for Bootstrap pagination styles -->
    <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
</head>
<body>

{$users|raw}

</body>
</html>
```
