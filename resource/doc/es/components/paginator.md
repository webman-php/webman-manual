# Componente de paginación

## jasongrimes/php-paginator

### Dirección del proyecto

https://github.com/jasongrimes/php-paginator
  
### Instalación

```php
composer require "jasongrimes/paginator:^1.0.3"
```
  
### Uso

Crear `app/controller/UserController.php`
```php
<?php
namespace app\controller;

use support\Request;
use JasonGrimes\Paginator;

class UserController
{
    /**
     * Lista de usuarios
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
**Plantilla (PHP nativo)**
Crear la plantilla app/view/user/get.html
```html
<html>
<head>
  <!-- Soporte incorporado para estilos de paginación Bootstrap -->
  <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
</head>
<body>

<?= $paginator;?>

</body>
</html>
```

**Plantilla (twig)**
Crear la plantilla app/view/user/get.html
```html
<html>
<head>
  <!-- Soporte incorporado para estilos de paginación Bootstrap -->
  <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
</head>
<body>

{% autoescape false %}
{{paginator}}
{% endautoescape %}

</body>
</html>
```

**Plantilla (blade)**
Crear la plantilla app/view/user/get.blade.php
```html
<html>
<head>
  <!-- Soporte incorporado para estilos de paginación Bootstrap -->
  <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
</head>
<body>

{!! $paginator !!}

</body>
</html>
```

**Plantilla (thinkphp)**
Crear la plantilla app/view/user/get.blade.php
```html
<html>
<head>
    <!-- Soporte incorporado para estilos de paginación Bootstrap -->
    <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
</head>
<body>

<?=$paginator?>

</body>
</html>
```

Efecto visual:
![](../../assets/img/paginator.png)
  
### Más contenido

Visita https://github.com/jasongrimes/php-paginator
