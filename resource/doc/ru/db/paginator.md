# Пагинация

# 1. Основанный на Laravel ORM способ пагинации
`illuminate/database` в Laravel предоставляет удобную функцию для создания пагинации.

## Установка
`composer require illuminate/pagination`

## Использование
```php
public function index(Request $request)
{
    $per_page = 10;
    $users = Db::table('user')->paginate($per_page);
    return view('index/index', ['users' => $users]);
}
```

## Методы экземпляра пагинатора
|  Метод   | Описание  |
|  ----  |-----|
|$paginator->count()|Получить общее количество данных на текущей странице|
|$paginator->currentPage()|Получить номер текущей страницы|
|$paginator->firstItem()|Получить номер первого элемента в наборе данных|
|$paginator->getOptions()|Получить опции пагинатора|
|$paginator->getUrlRange($start, $end)|Создать URL для указанного диапазона страниц|
|$paginator->hasPages()|Проверить, достаточно ли данных для создания нескольких страниц|
|$paginator->hasMorePages()|Проверить, есть ли ещё страницы для отображения|
|$paginator->items()|Получить элементы текущей страницы|
|$paginator->lastItem()|Получить номер последнего элемента в наборе данных|
|$paginator->lastPage()|Получить номер последней страницы (недоступно при использовании simplePaginate)|
|$paginator->nextPageUrl()|Получить URL следующей страницы|
|$paginator->onFirstPage()|Проверить, является ли текущая страница первой|
|$paginator->perPage()|Получить общее количество элементов на странице|
|$paginator->previousPageUrl()|Получить URL предыдущей страницы|
|$paginator->total()|Получить общее количество данных в наборе (недоступно при использовании simplePaginate)|
|$paginator->url($page)|Получить URL для указанной страницы|
|$paginator->getPageName()|Получить имя параметра запроса для хранения номера страницы|
|$paginator->setPageName($name)|Установить имя параметра запроса для хранения номера страницы|

> **Примечание**
> Метод `$paginator->links()` не поддерживается.

## Компонент пагинации
В webman нельзя использовать метод `$paginator->links()` для отображения кнопок пагинации, однако можно использовать другие компоненты для отображения, например `jasongrimes/php-paginator`.

**Установка**
`composer require "jasongrimes/paginator:~1.0"`

**На серверной стороне**
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

**Шаблон (чистый PHP)**
Создайте шаблон app/view/user/get.html
```html
<html>
<head>
  <!-- Поддержка стилей пагинации Bootstrap по умолчанию -->
  <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
</head>
<body>

<?= $paginator;?>

</body>
</html>
```

**Шаблон (twig)**
Создайте шаблон app/view/user/get.html
```html
<html>
<head>
  <!-- Поддержка стилей пагинации Bootstrap по умолчанию -->
  <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
</head>
<body>

{% autoescape false %}
{{paginator}}
{% endautoescape %}

</body>
</html>
```

**Шаблон (blade)**
Создайте шаблон app/view/user/get.blade.php
```html
<html>
<head>
  <!-- Поддержка стилей пагинации Bootstrap по умолчанию -->
  <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
</head>
<body>

{!! $paginator !!}

</body>
</html>
```

**Шаблон (thinkphp)**
Создайте шаблон app/view/user/get.html
```html
<html>
<head>
    <!-- Поддержка стилей пагинации Bootstrap по умолчанию -->
    <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
</head>
<body>

<?=$paginator?>

</body>
</html>
```

Результат будет следующим:
![](../../assets/img/paginator.png)

# 2. Основанный на ORM в Thinkphp способ пагинации
Не требуется устанавливать дополнительные библиотеки, достаточно установить think-orm.

## Использование
```php
public function index(Request $request)
{
    $per_page = 10;
    $users = Db::table('user')->paginate(['list_rows' => $per_page, 'page' => $request->get('page', 1), 'path' => $request->path()]);
    return view('index/index', ['users' => $users]);
}
```

**Шаблон (thinkphp)**
```html
<html>
<head>
    <!-- Поддержка стилей пагинации Bootstrap по умолчанию -->
    <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
</head>
<body>

{$users|raw}

</body>
</html>
```
