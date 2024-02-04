# Paginierung

# 1. Paginierungsmethode basierend auf dem ORM von Laravel
Das Paket `illuminate/database` von Laravel bietet eine bequeme Paginierungsfunktion.

## Installation
`composer require illuminate/pagination`

## Verwendung
```php
public function index(Request $request)
{
    $per_page = 10;
    $users = Db::table('user')->paginate($per_page);
    return view('index/index', ['users' => $users]);
}
```

## Instanzmethoden des Paginierers
|  Methode   | Beschreibung  |
|  ----  |-----|
|$paginator->count()|Erhalten Sie die Gesamtzahl der Daten auf der aktuellen Seite|
|$paginator->currentPage()|Aktuelle Seitennummer abrufen|
|$paginator->firstItem()|Die Nummer des ersten Datensatzes im Ergebnissatz erhalten|
|$paginator->getOptions()|Paginierungsoptionen abrufen|
|$paginator->getUrlRange($start, $end)|URL für einen bestimmten Seitenbereich erstellen|
|$paginator->hasPages()|Gibt an, ob genügend Daten vorhanden sind, um mehrere Seiten zu erstellen|
|$paginator->hasMorePages()|Gibt an, ob es mehr Seiten gibt, die angezeigt werden können|
|$paginator->items()|Die Daten der aktuellen Seite abrufen|
|$paginator->lastItem()|Die Nummer des letzten Datensatzes im Ergebnissatz erhalten|
|$paginator->lastPage()|Die Seitenzahl der letzten Seite erhalten (bei simplePaginate nicht verfügbar)|
|$paginator->nextPageUrl()|Die URL der nächsten Seite abrufen|
|$paginator->onFirstPage()|Gibt an, ob die aktuelle Seite die erste Seite ist|
|$paginator->perPage()|Die Gesamtanzahl der angezeigten Elemente pro Seite erhalten|
|$paginator->previousPageUrl()|Die URL der vorherigen Seite abrufen|
|$paginator->total()|Die Gesamtanzahl der Daten im Ergebnissatz erhalten (bei simplePaginate nicht verfügbar)|
|$paginator->url($page)|Die URL für eine bestimmte Seite abrufen|
|$paginator->getPageName()|Den Namen des Abfrageparameters zum Speichern der Seitenzahl abrufen|
|$paginator->setPageName($name)|Den Namen des Abfrageparameters zum Speichern der Seitenzahl festlegen|

> **Hinweis**
> Die Methode `$paginator->links()` wird nicht unterstützt.

## Paginierungskomponente
In webman kann die Methode `$paginator->links()` nicht verwendet werden, um die Seitenzahl-Knöpfe zu rendern. Stattdessen können wir andere Komponenten zum Rendern verwenden, wie z. B. `jasongrimes/php-paginator`.

**Installation**
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

**Vorlage (PHP-Rohdatei)**
Neues Template erstellen: app/view/user/get.html
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

**Vorlage (Twig)**
Neues Template erstellen: app/view/user/get.html
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

**Vorlage (Blade)**
Neues Template erstellen: app/view/user/get.blade.php
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

**Vorlage (ThinkPHP)**
Neues Template erstellen: app/view/user/get.html
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

# 2. Paginierungsmethode basierend auf dem ORM von ThinkPHP
Es ist keine zusätzliche Bibliothek erforderlich. Es reicht, think-orm zu installieren.

## Verwendung
```php
public function index(Request $request)
{
    $per_page = 10;
    $users = Db::table('user')->paginate(['list_rows' => $per_page, 'page' => $request->get('page', 1), 'path' => $request->path()]);
    return view('index/index', ['users' => $users]);
}
```

**Vorlage (ThinkPHP)**
```html
<html>
<head>
    <!-- Eingebaute Unterstützung für Bootstrap-Paginierungsstile -->
    <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
</head>
<body>

{$users|raw}

</body>
</html>
```
