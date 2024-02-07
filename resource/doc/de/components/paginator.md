# Paginierungskomponente

## jasongrimes/php-paginator

### Projektadresse

https://github.com/jasongrimes/php-paginator
  
### Installation

```php
composer require "jasongrimes/paginator:^1.0.3"
```
  
### Verwendung

Erstellen Sie `app/controller/UserController.php` neu
```php
<?php
namespace app\controller;

use support\Request;
use JasonGrimes\Paginator;

class UserController
{
    /**
     * Benutzerliste
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
**Template (reines PHP)**
Neues Template erstellen `app/view/user/get.html`
```html
<html>
<head>
  <!-- Eingebaute Unterstützung für Bootstrap-Paginierungsstile -->
  <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
</head>
<body>

<?= $paginator;?>

</body>
</html>
```

**Template (Twig)**
Neues Template erstellen `app/view/user/get.html`
```html
<html>
<head>
  <!-- Eingebaute Unterstützung für Bootstrap-Paginierungsstile -->
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
Neues Template erstellen `app/view/user/get.blade.php`
```html
<html>
<head>
  <!-- Eingebaute Unterstützung für Bootstrap-Paginierungsstile -->
  <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
</head>
<body>

{!! $paginator !!}

</body>
</html>
```

**Template (ThinkPHP)**
Neues Template erstellen `app/view/user/get.blade.php`
```html
<html>
<head>
    <!-- Eingebaute Unterstützung für Bootstrap-Paginierungsstile -->
    <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
</head>
<body>

<?=$paginator?>

</body>
</html>
```

Das Ergebnis sieht wie folgt aus:
![](../../assets/img/paginator.png)
  
### Mehr erfahren

Besuchen Sie https://github.com/jasongrimes/php-paginator
