# Componente di paginazione

## jasongrimes/php-paginator

### Indirizzo del progetto

https://github.com/jasongrimes/php-paginator

### Installazione

```php
composer require "jasongrimes/paginator:^1.0.3"
```

### Utilizzo

Creare `app/controller/UserController.php`
```php
<?php
namespace app\controller;

use support\Request;
use JasonGrimes\Paginator;

class UserController
{
    /**
     * Elenco degli utenti
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
**Modello (PHP nativo)**
Creare il modello `app/view/user/get.html`
```html
<html>
<head>
  <!-- Supporto integrato per lo stile di paginazione Bootstrap -->
  <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
</head>
<body>

<?= $paginator;?>

</body>
</html>
```

**Modello (twig)**
Creare il modello `app/view/user/get.html`
```html
<html>
<head>
  <!-- Supporto integrato per lo stile di paginazione Bootstrap -->
  <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
</head>
<body>

{% autoescape false %}
{{paginator}}
{% endautoescape %}

</body>
</html>
```

**Modello (blade)**
Creare il modello `app/view/user/get.blade.php`
```html
<html>
<head>
  <!-- Supporto integrato per lo stile di paginazione Bootstrap -->
  <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
</head>
<body>

{!! $paginator !!}

</body>
</html>
```

**Modello (thinkphp)**
Creare il modello `app/view/user/get.blade.php`
```html
<html>
<head>
    <!-- Supporto integrato per lo stile di paginazione Bootstrap -->
    <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
</head>
<body>

<?=$paginator?>

</body>
</html>
```

Ecco l'effetto:
![](../../assets/img/paginator.png)

### Ulteriori dettagli

Visita https://github.com/jasongrimes/php-paginator
