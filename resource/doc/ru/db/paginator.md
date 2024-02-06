# Пагинация

# 1. Способ пагинации с использованием Laravel ORM
`illuminate/database` в Laravel предоставляет удобную функцию пагинации.

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
| Метод   | Описание  |
|  ----  |-----|
|$paginator->count()|Получить общее количество данных на текущей странице|
|$paginator->currentPage()|Получить номер текущей страницы|
|$paginator->firstItem()|Получить номер первого элемента в наборе результатов|
|$paginator->getOptions()|Получить опции пагинатора|
|$paginator->getUrlRange($start, $end)|Создать диапазон URL для указанных страниц|
|$paginator->hasPages()|Есть ли достаточное количество данных для создания нескольких страниц|
|$paginator->hasMorePages()|Есть ли еще страницы для отображения|
|$paginator->items()|Получить элементы текущей страницы|
|$paginator->lastItem()|Получить номер последнего элемента в наборе результатов|
|$paginator->lastPage()|Получить номер последней страницы (недоступно в simplePaginate)|
|$paginator->nextPageUrl()|Получить URL следующей страницы|
|$paginator->onFirstPage()|Является ли текущая страница первой|
|$paginator->perPage()|Получить общее количество элементов на странице|
|$paginator->previousPageUrl()|Получить URL предыдущей страницы|
|$paginator->total()|Получить общее количество данных в наборе результатов (недоступно в simplePaginate)|
|$paginator->url($page)|Получить URL для указанной страницы|
|$paginator->getPageName()|Получить имя запроса для хранения номера страницы|
|$paginator->setPageName($name)|Установить имя запроса для хранения номера страницы|

> **Примечание**
> Метод `$paginator->links()` не поддерживается.

## Компонент пагинации
В webman нельзя использовать метод `$paginator->links()` для отображения кнопок пагинации, однако мы можем использовать другие компоненты для отображения, такие как `jasongrimes/php-paginator`.

**Установка**
`composer require "jasongrimes/paginator:~1.0"`


**На сервере**
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

**Шаблон (чистый php)**
Создайте шаблон app/view/user/get.html
```html
<html>
<head>
  <!-- Встроенная поддержка стилей пагинации Bootstrap -->
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
  <!-- Встроенная поддержка стилей пагинации Bootstrap -->
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
  <!-- Встроенная поддержка стилей пагинации Bootstrap -->
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
    <!-- Встроенная поддержка стилей пагинации Bootstrap -->
    <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
</head>
<body>

<?=$paginator?>

</body>
</html>
```

Результат:
![](../../assets/img/paginator.png)

# 2. Способ пагинации с использованием ORM в Thinkphp
Нет необходимости устанавливать дополнительные библиотеки, просто установите think-orm.
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
    <!-- Встроенная поддержка стилей пагинации Bootstrap -->
    <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
</head>
<body>

{$users|raw}

</body>
</html>
```
