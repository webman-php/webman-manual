# Paginación

# 1. Forma de paginación basada en ORM de Laravel

La biblioteca `illuminate/database` de Laravel proporciona una práctica función de paginación.

## Instalación
`composer require illuminate/pagination`

## Uso
```php
public function index(Request $request)
{
    $per_page = 10;
    $users = Db::table('user')->paginate($per_page);
    return view('index/index', ['users' => $users]);
}
```

## Métodos de instancia del paginador
|  Método   | Descripción  |
|  ----  |-----|
|$paginator->count()|Obtener el número total de datos en la página actual|
|$paginator->currentPage()|Obtener el número de página actual|
|$paginator->firstItem()|Obtener el número de la primera entidad en el conjunto de resultados|
|$paginator->getOptions()|Obtener las opciones del paginador|
|$paginator->getUrlRange($start, $end)|Crear un rango de URL para páginas específicas|
|$paginator->hasPages()|Indicar si hay suficientes datos para crear múltiples páginas|
|$paginator->hasMorePages()|Indicar si hay más páginas disponibles para mostrar|
|$paginator->items()|Obtener los elementos de datos de la página actual|
|$paginator->lastItem()|Obtener el número de la última entidad en el conjunto de resultados|
|$paginator->lastPage()|Obtener el número de la última página (no disponible en simplePaginate)|
|$paginator->nextPageUrl()|Obtener la URL de la página siguiente|
|$paginator->onFirstPage()|Indicar si la página actual es la primera página|
|$paginator->perPage()|Obtener el número total de elementos mostrados por página|
|$paginator->previousPageUrl()|Obtener la URL de la página anterior|
|$paginator->total()|Obtener el número total de datos en el conjunto de resultados (no disponible en simplePaginate)|
|$paginator->url($page)|Obtener la URL de una página específica|
|$paginator->getPageName()|Obtener el nombre del parámetro de consulta utilizado para almacenar el número de página|
|$paginator->setPageName($name)|Establecer el nombre del parámetro de consulta utilizado para almacenar el número de página|

> **Nota**
> No se admite el método `$paginator->links()`.

## Componente de paginación
En webman, no se puede utilizar el método `$paginator->links()` para renderizar los botones de paginación, sin embargo, se puede utilizar otro componente para hacerlo, como `jasongrimes/php-paginator`.

**Instalación**
`composer require "jasongrimes/paginator:~1.0"`


**Lado del servidor**
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

**Plantilla (PHP nativo)**
Crear la plantilla app/view/user/get.html
```html
<html>
<head>
  <!-- Soporte integrado para estilos de paginación de Bootstrap -->
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
  <!-- Soporte integrado para estilos de paginación de Bootstrap -->
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
  <!-- Soporte integrado para estilos de paginación de Bootstrap -->
  <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
</head>
<body>

{!! $paginator !!}

</body>
</html>
```

**Plantilla (thinkphp)**
Crear la plantilla app/view/user/get.html
```html
<html>
<head>
    <!-- Soporte integrado para estilos de paginación de Bootstrap -->
    <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
</head>
<body>

<?=$paginator?>

</body>
</html>
```

Resultado:
![](../../assets/img/paginator.png)

# 2. Forma de paginación basada en ORM de Thinkphp

No se necesita instalar bibliotecas adicionales, solo se necesita haber instalado think-orm.

## Uso
```php
public function index(Request $request)
{
    $per_page = 10;
    $users = Db::table('user')->paginate(['list_rows' => $per_page, 'page' => $request->get('page', 1), 'path' => $request->path()]);
    return view('index/index', ['users' => $users]);
}
```

**Plantilla (thinkphp)**
```html
<html>
<head>
    <!-- Soporte integrado para estilos de paginación de Bootstrap -->
    <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
</head>
<body>

{$users|raw}

</body>
</html>
```
