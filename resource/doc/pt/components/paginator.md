# Componente de paginação

## jasongrimes/php-paginator

### Endereço do projeto

https://github.com/jasongrimes/php-paginator
  
### Instalação

```php
composer require "jasongrimes/paginator:^1.0.3"
```
  
### Utilização

Crie um novo arquivo `app/controller/UserController.php`
```php
<?php
namespace app\controller;

use support\Request;
use JasonGrimes\Paginator;

class UserController
{
    /**
     * Lista de usuários
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
**Modelo (nativo php)**
Crie um novo modelo em app/view/user/get.html
```html
<html>
<head>
  <!-- Suporte embutido para o estilo de paginação do Bootstrap -->
  <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
</head>
<body>

<?= $paginator;?>

</body>
</html>
```

**Modelo (twig)**
Crie um novo modelo em app/view/user/get.html
```html
<html>
<head>
  <!-- Suporte embutido para o estilo de paginação do Bootstrap -->
  <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
</head>
<body>

{% autoescape false %}
{{paginator}}
{% endautoescape %}

</body>
</html>
```

**Modelo (blade)**
Crie um novo modelo em app/view/user/get.blade.php
```html
<html>
<head>
  <!-- Suporte embutido para o estilo de paginação do Bootstrap -->
  <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
</head>
<body>

{!! $paginator !!}

</body>
</html>
```

**Modelo (thinkphp)**
Crie um novo modelo em app/view/user/get.blade.php
```html
<html>
<head>
    <!-- Suporte embutido para o estilo de paginação do Bootstrap -->
    <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
</head>
<body>

<?=$paginator?>

</body>
</html>
```

Efeito visual:
![](../../assets/img/paginator.png)
  
### Mais conteúdo

Visite https://github.com/jasongrimes/php-paginator
