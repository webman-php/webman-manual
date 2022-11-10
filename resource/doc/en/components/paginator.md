# Pagination Components

## jasongrimes/php-paginator

### Project address

https://github.com/jasongrimes/php-paginator
  
### Install

```php
composer require "jasongrimes/paginator:^1.0.3"
```
  
### Usage

New `app/controller/UserController.php`
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
![](img/paginator.png)
  
### More content

Access https://github.com/jasongrimes/php-paginator
  

