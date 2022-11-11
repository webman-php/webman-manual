# Pagination
LaravelThe `illuminate/database` provides a convenient paging feature。

## Install
`composer require illuminate/pagination`

## Usage
```php
public function index(Request $request)
{
    $per_page = 10;
    $users = Db::table('user')->paginate($per_page, '*', 'page', $request->input('page'));
    return view('index/index', ['users' => $users]);
}
```

## Pager Example Methods
|  Method | Description  |
|  ----  |-----|
|$paginator->count()|Get the total number of data on the current page|
|$paginator->currentPage()|Get the current page number|
|$paginator->firstItem()|Get the number of the first data in the result set|
|$paginator->getOptions()|Get pager options|
|$paginator->getUrlRange($start, $end)|Create the specified page range URL|
|$paginator->hasPages()|Is there enough data to create multiple pages|
|$paginator->hasMorePages()|Are there more pages available for display|
|$paginator->items()|Get data items for the current page|
|$paginator->lastItem()|Get the number of the last data in the result set|
|$paginator->lastPage()|Get the page number of the last page (not available in simplePaginate)）|
|$paginator->nextPageUrl()|Get the next page URL|
|$paginator->onFirstPage()|Is the current page the first page|
|$paginator->perPage()|Get the total number of displayed pages per page|
|$paginator->previousPageUrl()|Get the previous page's URL|
|$paginator->total()|Get the total number of data in the result set (not available in simplePaginate)）|
|$paginator->url($page)|Get the specified page URL|
|$paginator->getPageName()|Get the name of the query parameter used to store the page number|
|$paginator->setPageName($name)|Set the name of the query parameter used to store the page number|

> **Note**
> The `$paginator->links()` method is not supported

## Pagination Components
webmanWe can't use the `$paginator->links()` method to render the pagination button, but we can use other components to render it, for example `jasongrimes/php-paginator` 。

**Install**
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

**Template (php native)**
New Template app/view/user/get.html
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

**Template(twig)** 
New Template app/view/user/get.html
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

**Template(blade)** 
New Template app/view/user/get.blade.php
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

**Template(thinkphp)**
New Template app/view/user/get.blade.php
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

The effect is as follows：
![](../components/img/paginator.png)