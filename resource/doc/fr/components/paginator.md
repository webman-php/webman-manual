# Composant de pagination

## jasongrimes/php-paginator

### Lien du projet

https://github.com/jasongrimes/php-paginator
  
### Installation

```php
composer require "jasongrimes/paginator:^1.0.3"
```
  
### Utilisation

Créer `app/controller/UserController.php`
```php
<?php
namespace app\controller;

use support\Request;
use JasonGrimes\Paginator;

class UserController
{
    /**
     * Liste des utilisateurs
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
**Modèle (PHP natif)**
Créer le modèle `app/view/user/get.html`
```html
<html>
<head>
  <!-- Prise en charge intégrée du style de pagination Bootstrap -->
  <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
</head>
<body>

<?= $paginator;?>

</body>
</html>
```

**Modèle (twig)**
Créer le modèle `app/view/user/get.html`
```html
<html>
<head>
  <!-- Prise en charge intégrée du style de pagination Bootstrap -->
  <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
</head>
<body>

{% autoescape false %}
{{paginator}}
{% endautoescape %}

</body>
</html>
```

**Modèle (blade)**
Créer le modèle `app/view/user/get.blade.php`
```html
<html>
<head>
  <!-- Prise en charge intégrée du style de pagination Bootstrap -->
  <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
</head>
<body>

{!! $paginator !!}

</body>
</html>
```

**Modèle (thinkphp)**
Créer le modèle `app/view/user/get.blade.php`
```html
<html>
<head>
    <!-- Prise en charge intégrée du style de pagination Bootstrap -->
    <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
</head>
<body>

<?=$paginator?>

</body>
</html>
```

Rendu visuel :
![](../../assets/img/paginator.png)
  
### Plus d'informations

Visitez https://github.com/jasongrimes/php-paginator
