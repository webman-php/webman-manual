# Компонент пагинации

## jasongrimes/php-paginator

### Ссылка на проект

https://github.com/jasongrimes/php-paginator
  
### Установка

```php
composer require "jasongrimes/paginator:^1.0.3"
```
  
### Использование

Create `app/controller/UserController.php`
```php
<?php
namespace app\controller;

use support\Request;
use JasonGrimes\Paginator;

class UserController
{
    /**
     * Список пользователей
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
**Шаблон (оригинальный PHP)**
Create template app/view/user/get.html
```html
<html>
<head>
  <!-- Поддерживает стили пагинации Bootstrap по умолчанию -->
  <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
</head>
<body>

<?= $paginator;?>

</body>
</html>
```

**Шаблон (twig)**
Create template app/view/user/get.html
```html
<html>
<head>
  <!-- Поддерживает стили пагинации Bootstrap по умолчанию -->
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
Create template app/view/user/get.blade.php
```html
<html>
<head>
  <!-- Поддерживает стили пагинации Bootstrap по умолчанию -->
  <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
</head>
<body>

{!! $paginator !!}

</body>
</html>
```

**Шаблон (thinkphp)**
Create template app/view/user/get.blade.php
```html
<html>
<head>
    <!-- Поддерживает стили пагинации Bootstrap по умолчанию -->
    <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
</head>
<body>

<?=$paginator?>

</body>
</html>
```

Эффект выглядит следующим образом:
![](../../assets/img/paginator.png)
  
### Дополнительная информация

Посетите https://github.com/jasongrimes/php-paginator
