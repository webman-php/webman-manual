# Paginación

# 1. Método de paginación basado en el ORM de Laravel
`illuminate/database` de Laravel proporciona una manera conveniente de paginar.

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
|$paginator->count()|Obtener el total de datos en la página actual|
|$paginator->currentPage()|Obtener el número de página actual|
|$paginator->firstItem()|Obtener el número del primer elemento en el conjunto de resultados|
|$paginator->getOptions()|Obtener las opciones del paginador|
|$paginator->getUrlRange($start, $end)|Crear una URL para un rango de páginas especificado|
|$paginator->hasPages()|Determinar si hay suficientes datos para crear múltiples páginas|
|$paginator->hasMorePages()|Determinar si hay más páginas para mostrar|
|$paginator->items()|Obtener los elementos de datos de la página actual|
|$paginator->lastItem()|Obtener el número del último elemento en el conjunto de resultados|
|$paginator->lastPage()|Obtener el número de la última página (no disponible en simplePaginate)|
|$paginator->nextPageUrl()|Obtener la URL de la siguiente página|
|$paginator->onFirstPage()|Determinar si la página actual es la primera página|
|$paginator->perPage()|Obtener el número total de elementos para mostrar por página|
|$paginator->previousPageUrl()|Obtener la URL de la página anterior|
|$paginator->total()|Obtener el total de elementos en el conjunto de resultados (no disponible en simplePaginate)|
|$paginator->url($page)|Obtener la URL de una página específica|
|$paginator->getPageName()|Obtener el nombre del parámetro de consulta utilizado para almacenar el número de página|
|$paginator->setPageName($name)|Establecer el nombre del parámetro de consulta utilizado para almacenar el número de página|

> **Nota**
> No se admite el método `$paginator->links()`

## Componente de paginación
En webman no se puede utilizar el método `$paginator->links()` para renderizar los botones de paginación, sin embargo, se pueden usar otros componentes para renderizarlos, como `jasongrimes/php-paginator`.

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
Crear una plantilla llamada app/view/user/get.html
```html
<html>
<head>
  <!-- Se admite el estilo de paginación de Bootstrap de forma nativa -->
  <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
</head>
<body>

<?= $paginator;?>

</body>
</html>
```

**Plantilla (twig)**
Crear una plantilla llamada app/view/user/get.html
```html
<html>
<head>
  <!-- Se admite el estilo de paginación de Bootstrap de forma nativa -->
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
Crear una plantilla llamada app/view/user/get.blade.php
```html
<html>
<head>
  <!-- Se admite el estilo de paginación de Bootstrap de forma nativa -->
  <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
</head>
<body>

{!! $paginator !!}

</body>
</html>
```

**Plantilla (thinkphp)**
Crear una plantilla llamada app/view/user/get.html
```html
<html>
<head>
    <!-- Se admite el estilo de paginación de Bootstrap de forma nativa -->
    <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
</head>
<body>

<?=$paginator?>

</body>
</html>
```

El resultado es el siguiente:
![](../../assets/img/paginator.png)

# 2. Método de paginación basado en el ORM de Thinkphp
No es necesario instalar bibliotecas adicionales, solo es necesario haber instalado think-orm.

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
    <!-- Se admite el estilo de paginación de Bootstrap de forma nativa -->
    <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
</head>
<body>

{$users|raw}

</body>
</html>
```
