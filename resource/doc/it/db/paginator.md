# Paginazione

# 1. Paginazione basata su ORM di Laravel
L'illuminazione/database di Laravel fornisce un comodo supporto per la paginazione.

## Installazione
`composer require illuminate/pagination`

## Utilizzo
```php
public function index(Request $request)
{
    $per_page = 10;
    $users = Db::table('user')->paginate($per_page);
    return view('index/index', ['users' => $users]);
}
```

## Metodi di istanza del paginatore
| Metodo | Descrizione |
| ---- |-----|
|$paginator->count()| Ottiene il numero totale di dati nella pagina corrente|
|$paginator->currentPage()| Ottiene il numero di pagina attuale|
|$paginator->firstItem()| Ottiene il numero del primo elemento nel set di risultati|
|$paginator->getOptions()| Ottiene le opzioni del paginatore|
|$paginator->getUrlRange($start, $end)| Crea l'URL per un intervallo specifico di pagine|
|$paginator->hasPages()| Indica se ci sono abbastanza dati per creare più pagine|
|$paginator->hasMorePages()| Indica se ci sono altre pagine disponibili per la visualizzazione|
|$paginator->items()| Ottiene gli elementi della pagina corrente|
|$paginator->lastItem()| Ottiene il numero dell'ultimo elemento nel set di risultati|
|$paginator->lastPage()| Ottiene il numero dell'ultima pagina (non disponibile in simplePaginate)|
|$paginator->nextPageUrl()| Ottiene l'URL della pagina successiva|
|$paginator->onFirstPage()| Indica se la pagina attuale è la prima pagina|
|$paginator->perPage()| Ottiene il numero totale di elementi da mostrare per pagina|
|$paginator->previousPageUrl()| Ottiene l'URL della pagina precedente|
|$paginator->total()| Ottiene il numero totale di elementi nel set di risultati (non disponibile in simplePaginate)|
|$paginator->url($page)| Ottiene l'URL di una pagina specifica|
|$paginator->getPageName()| Ottiene il nome del parametro di query per memorizzare il numero di pagina|
|$paginator->setPageName($name)| Imposta il nome del parametro di query per memorizzare il numero di pagina|

> **Nota**
> Il metodo `$paginator->links()` non è supportato

## Componente di paginazione
In webman non è possibile utilizzare il metodo `$paginator->links()` per renderizzare i pulsanti di paginazione, tuttavia è possibile utilizzare un'altra libreria per farlo, ad esempio `jasongrimes/php-paginator`.

**Installazione**
`composer require "jasongrimes/paginator:~1.0"`


**Backend**
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

**Template (PHP nativo)**
Crea il template app/view/user/get.html
```html
<html>
<head>
  <!-- Supporto integrato per lo stile di paginazione di Bootstrap -->
  <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
</head>
<body>

<?= $paginator;?>

</body>
</html>
```

**Template (twig)**
Crea il template app/view/user/get.html
```html
<html>
<head>
  <!-- Supporto integrato per lo stile di paginazione di Bootstrap -->
  <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
</head>
<body>

{% autoescape false %}
{{paginator}}
{% endautoescape %}

</body>
</html>
```

**Template (blade)**
Crea il template app/view/user/get.blade.php
```html
<html>
<head>
  <!-- Supporto integrato per lo stile di paginazione di Bootstrap -->
  <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
</head>
<body>

{!! $paginator !!}

</body>
</html>
```

**Template (thinkphp)**
Crea il template app/view/user/get.html
```html
<html>
<head>
    <!-- Supporto integrato per lo stile di paginazione di Bootstrap -->
    <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
</head>
<body>

<?=$paginator?>

</body>
</html>
```

Ecco un esempio dell'aspetto:
![](../../assets/img/paginator.png)

# 2. Paginazione basata su ORM di Thinkphp
Non è necessario installare alcuna libreria aggiuntiva, è sufficiente aver installato think-orm.

## Utilizzo
```php
public function index(Request $request)
{
    $per_page = 10;
    $users = Db::table('user')->paginate(['list_rows' => $per_page, 'page' => $request->get('page', 1), 'path' => $request->path()]);
    return view('index/index', ['users' => $users]);
}
```

**Template (thinkphp)**
```html
<html>
<head>
    <!-- Supporto integrato per lo stile di paginazione di Bootstrap -->
    <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
</head>
<body>

{$users|raw}

</body>
</html>
```
