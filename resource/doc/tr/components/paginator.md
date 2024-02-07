# Sayfa Bileşeni

## jasongrimes/php-paginator

### Proje Adresi

https://github.com/jasongrimes/php-paginator
  
### Kurulum

```php
composer require "jasongrimes/paginator:^1.0.3"
```
  
### Kullanım

Yeni oluştur `app/controller/UserController.php`
```php
<?php
namespace app\controller;

use support\Request;
use JasonGrimes\Paginator;

class UserController
{
    /**
     * Kullanıcı Listesi
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
**Şablon (php orijinal)**
Yeni şablon oluştur `app/view/user/get.html`
```html
<html>
<head>
  <!-- Dahili olarak Bootstrap sayfalama stili desteği -->
  <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
</head>
<body>

<?= $paginator;?>

</body>
</html>
```

**Şablon (twig)**
Yeni şablon oluştur `app/view/user/get.html`
```html
<html>
<head>
  <!-- Dahili olarak Bootstrap sayfalama stili desteği -->
  <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
</head>
<body>

{% autoescape false %}
{{paginator}}
{% endautoescape %}

</body>
</html>
```

**Şablon (blade)**
Yeni şablon oluştur `app/view/user/get.blade.php`
```html
<html>
<head>
  <!-- Dahili olarak Bootstrap sayfalama stili desteği -->
  <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
</head>
<body>

{!! $paginator !!}

</body>
</html>
```

**Şablon (thinkphp)**
Yeni şablon oluştur `app/view/user/get.blade.php`
```html
<html>
<head>
    <!-- Dahili olarak Bootstrap sayfalama stili desteği -->
    <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
</head>
<body>

<?=$paginator?>

</body>
</html>
```

Sonuç:
![](../../assets/img/paginator.png)
  
### Daha Fazla İçerik

Ziyaret et: https://github.com/jasongrimes/php-paginator
