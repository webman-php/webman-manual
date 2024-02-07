# Paginazione

# 1. Paginazione basata su ORM Laravel
Illuminate/database di Laravel fornisce una comoda funzionalità di paginazione.

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
|$paginator->count()|Ottenere il totale dei dati nella pagina corrente|
|$paginator->currentPage()|Ottenere il numero di pagina corrente|
|$paginator->firstItem()|Ottenere il numero di serie del primo dato nel risultato|
|$paginator->getOptions()|Ottenere le opzioni del paginatore|
|$paginator->getUrlRange($start, $end)|Creare URL per un intervallo di pagine specifico|
|$paginator->hasPages()|Se ci sono abbastanza dati per creare più pagine|
|$paginator->hasMorePages()|Se ci sono altre pagine da mostrare|
|$paginator->items()|Ottenere gli elementi di dati nella pagina corrente|
|$paginator->lastItem()|Ottenere il numero di serie dell'ultimo dato nel risultato|
|$paginator->lastPage()|Ottenere il numero dell'ultima pagina (non disponibile in simplePaginate)|
|$paginator->nextPageUrl()|Ottenere l'URL della pagina successiva|
|$paginator->onFirstPage()|Se la pagina corrente è la prima pagina|
|$paginator->perPage()|Ottenere il numero totale di elementi da mostrare per pagina|
|$paginator->previousPageUrl()|Ottenere l'URL della pagina precedente|
|$paginator->total()|Ottenere il totale dei dati nel risultato (non disponibile in simplePaginate)|
|$paginator->url($page)|Ottenere l'URL di una pagina specifica|
|$paginator->getPageName()|Ottenere il nome del parametro di query per memorizzare il numero di pagina|
|$paginator->setPageName($name)|Impostare il nome del parametro di query per memorizzare il numero di pagina|

> **Nota**
> Non supporta il metodo `$paginator->links()`

## Componente di paginazione
In webman non è possibile utilizzare il metodo `$paginator->links()` per creare i pulsanti della paginazione, tuttavia è possibile utilizzare altri componenti per renderli, ad esempio `jasongrimes/php-paginator`.

**Installazione**
`composer require "jasongrimes/paginator:~1.0"`

**Lato server**
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
Creare il template app/view/user/get.html
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

**Template (twig)**
Creare il template app/view/user/get.html
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

**Template (blade)**
Creare il template app/view/user/get.blade.php
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

**Template (thinkphp)**
Creare il template app/view/user/get.html
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

Esempio:
![](../../assets/img/paginator.png)

# 2. Paginazione basata su ORM Thinkphp
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
    <!-- Supporto integrato per lo stile di paginazione Bootstrap -->
    <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
</head>
<body>

{$users|raw}

</body>
</html>
```
