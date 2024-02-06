# Pagination

# 1. Pagination using Laravel's ORM

The `illuminate/database` package of Laravel provides convenient pagination functionality.

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
|$paginator->count()|Get the total number of items for the current page|
|$paginator->currentPage()|Get the current page number|
|$paginator->firstItem()|Get the number of the first item in the result set|
|$paginator->getOptions()|Get the pagination options|
|$paginator->getUrlRange($start, $end)|Create a range of URLs for a given page|
|$paginator->hasPages()|Determine if there are enough items to split into multiple pages|
|$paginator->hasMorePages()|Determine if there are more items in the result set|
|$paginator->items()|Get the items for the current page|
|$paginator->lastItem()|Get the number of the last item in the result set|
|$paginator->lastPage()|Get the last page number available (not available in simplePaginate)|
|$paginator->nextPageUrl()|Get the URL for the next page|
|$paginator->onFirstPage()|Determine if the current page is the first page|
|$paginator->perPage()|Get the number of items to be shown per page|
|$paginator->previousPageUrl()|Get the URL for the previous page|
|$paginator->total()|Get the total number of items in the result set (not available in simplePaginate)|
|$paginator->url($page)|Get the URL for a given page|
|$paginator->getPageName()|Get the query parameter name used to store the page|
|$paginator->setPageName($name)|Set the query parameter name used to store the page|

> **Note**
> The method `$paginator->links()` is not supported

## Pagination Components
In webman, the `$paginator->links()` method is not supported for rendering pagination buttons. However, we can use other components for rendering, such as `jasongrimes/php-paginator`.

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

**Template (PHP native)**
Create a new template app/view/user/get.html
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

**Template (twig)**
Create a new template app/view/user/get.html
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

**Template (blade)**
Create a new template app/view/user/get.blade.php
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

**Template (thinkphp)**
Create a new template app/view/user/get.html
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

The effect is as shown in the image:
![](../../assets/img/paginator.png)

# 2. Pagination using Thinkphp's ORM

No additional library is needed, just install think-orm.

## Usage
```php
public function index(Request $request)
{
    $per_page = 10;
    $users = Db::table('user')->paginate(['list_rows' => $per_page, 'page' => $request->get('page', 1), 'path' => $request->path()]);
    return view('index/index', ['users' => $users]);
}
```

**Template (thinkphp)**
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
