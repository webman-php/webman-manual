# Paginação

# 1. Paginação baseada no ORM do Laravel
O `illuminate/database` do Laravel fornece uma maneira conveniente de realizar a paginação.

## Instalação
`composer require illuminate/pagination`

## Utilização
```php
public function index(Request $request)
{
    $per_page = 10;
    $users = Db::table('user')->paginate($per_page);
    return view('index/index', ['users' => $users]);
}
```

## Métodos de instância do paginador
| Método  | Descrição |
| ----  |-----|
|$paginator->count()|Obtém o número total de dados na página atual|
|$paginator->currentPage()|Obtém o número da página atual|
|$paginator->firstItem()|Obtém o número do primeiro item no conjunto de resultados|
|$paginator->getOptions()|Obtém as opções do paginador|
|$paginator->getUrlRange($start, $end)|Cria URL para um intervalo específico de páginas|
|$paginator->hasPages()|Verifica se há dados suficientes para criar várias páginas|
|$paginator->hasMorePages()|Verifica se há mais páginas a serem exibidas|
|$paginator->items()|Obtém os itens da página atual|
|$paginator->lastItem()|Obtém o número do último item no conjunto de resultados|
|$paginator->lastPage()|Obtém o número da última página (não disponível em simplePaginate)|
|$paginator->nextPageUrl()|Obtém a URL da próxima página|
|$paginator->onFirstPage()|Verifica se a página atual é a primeira|
|$paginator->perPage()|Obtém o número total de itens por página|
|$paginator->previousPageUrl()|Obtém a URL da página anterior|
|$paginator->total()|Obtém o número total de dados no conjunto de resultados (não disponível em simplePaginate)|
|$paginator->url($page)|Obtém a URL para uma página específica|
|$paginator->getPageName()|Obtém o nome do parâmetro de consulta utilizado para armazenar o número da página|
|$paginator->setPageName($name)|Define o nome do parâmetro de consulta utilizado para armazenar o número da página|

> **Observação**
> O método `$paginator->links()` não é suportado.

## Componente de Paginação
No webman, não é possível usar o método `$paginator->links()` para renderizar os botões de paginação. No entanto, é possível utilizar outros componentes para renderizar, como o `jasongrimes/php-paginator`.

**Instalação**
`composer require "jasongrimes/paginator:~1.0"`

**Lado do Servidor**
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

**Modelo (PHP nativo)**
Crie o modelo app/view/user/get.html
```html
<html>
<head>
  <!-- Suporte integrado ao estilo de paginação do Bootstrap -->
  <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
</head>
<body>

<?= $paginator;?>

</body>
</html>
```

**Modelo (twig)**
Crie o modelo app/view/user/get.html
```html
<html>
<head>
  <!-- Suporte integrado ao estilo de paginação do Bootstrap -->
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
Crie o modelo app/view/user/get.blade.php
```html
<html>
<head>
  <!-- Suporte integrado ao estilo de paginação do Bootstrap -->
  <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
</head>
<body>

{!! $paginator !!}

</body>
</html>
```

**Modelo (thinkphp)**
Crie o modelo app/view/user/get.html
```html
<html>
<head>
    <!-- Suporte integrado ao estilo de paginação do Bootstrap -->
    <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
</head>
<body>

<?=$paginator?>

</body>
</html>
```

O resultado é este:
![](../../assets/img/paginator.png)

# 2. Paginação baseada no ORM do Thinkphp
Não é necessário instalar bibliotecas adicionais, basta instalar o think-orm.

## Utilização
```php
public function index(Request $request)
{
    $per_page = 10;
    $users = Db::table('user')->paginate(['list_rows' => $per_page, 'page' => $request->get('page', 1), 'path' => $request->path()]);
    return view('index/index', ['users' => $users]);
}
```

**Modelo (thinkphp)**
```html
<html>
<head>
    <!-- Suporte integrado ao estilo de paginação do Bootstrap -->
    <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
</head>
<body>

{$users|raw}

</body>
</html>
```
