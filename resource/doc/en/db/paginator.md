# Pagination

# 1. Pagination using Laravel's ORM

Laravel's `illuminate/database` provides convenient pagination functionality.

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
| Method | Description |
| ------ | ----------- |
|$paginator->count()|Get the total count of data on the current page|
|$paginator->currentPage()|Get the current page number|
|$paginator->firstItem()|Get the number of the first data item in the result set|
|$paginator->getOptions()|Get the options of the paginator|
|$paginator->getUrlRange($start, $end)|Create URLs for a specified range of page numbers|
|$paginator->hasPages()|Determine if there are enough data to create multiple pages|
|$paginator->hasMorePages()|Determine if there are more pages to show|
|$paginator->items()|Get the items of the current page|
|$paginator->lastItem()|Get the number of the last data item in the result set|
|$paginator->lastPage()|Get the page number of the last page (not available in `simplePaginate`)|
|$paginator->nextPageUrl()|Get the URL for the next page|
|$paginator->onFirstPage()|Determine if the current page is the first page|
|$paginator->perPage()|Get the total number of items per page|
|$paginator->previousPageUrl()|Get the URL for the previous page|
|$paginator->total()|Get the total count of data in the result set (not available in `simplePaginate`)|
|$paginator->url($page)|Get the URL for the specified page|
|$paginator->getPageName()|Get the query string parameter name used to store the page number|
|$paginator->setPageName($name)|Set the query string parameter name used to store the page number|

> **Note**
> The `$paginator->links()` method is not supported

## Pagination Component
In webman, the `$paginator->links()` method cannot be used to render pagination buttons. However, we can use other components, such as `jasongrimes/php-paginator`, to render pagination.

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

**Template (Plain PHP)**
Create a new template file `app/view/user/get.html`
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
Create a new template file `app/view/user/get.html`
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
Create a new template file `app/view/user/get.blade.php`
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
Create a new template file `app/view/user/get.html`
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

The result is as follows:
![](../../assets/img/paginator.png)

# 2. Pagination using ThinkPHP's ORM

No additional libraries need to be installed, as long as ThinkORM has been installed.

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
