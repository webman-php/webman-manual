# Pagination Component

## jasongrimes/php-paginator

### Project Address

https://github.com/jasongrimes/php-paginator

### Installation

```php
composer require "jasongrimes/paginator:^1.0.3"
```

### Usage

Create a new `app/controller/UserController.php` file.

```php
<?php
namespace app\controller;

use support\Request;
use JasonGrimes\Paginator;

class UserController
{
    /**
     * User list
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

**Template (PHP Native)**
Create a new template `app/view/user/get.html`.

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
Create a new template `app/view/user/get.html`.

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
Create a new template `app/view/user/get.blade.php`.

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
Create a new template `app/view/user/get.blade.php`.

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

The result will be as follows:
![](../../assets/img/paginator.png)

### More Information

Visit https://github.com/jasongrimes/php-paginator.
