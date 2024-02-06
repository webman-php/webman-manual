# Sayfalama

# 1. Laravel ORM tabanlı sayfalama yöntemi
Laravel'in `illuminate/database` paketi kullanımı kolay sayfalama özellikleri sağlar.

## Kurulum
`composer require illuminate/pagination`

## Kullanım
```php
public function index(Request $request)
{
    $per_page = 10;
    $users = Db::table('user')->paginate($per_page);
    return view('index/index', ['users' => $users]);
}
```

## Sayfalama nesnesi yöntemleri
|  Yöntem   | Açıklama  |
|  ----  |-----|
|$paginator->count()|Mevcut sayfadaki veri toplamını alır|
|$paginator->currentPage()|Mevcut sayfa numarasını alır|
|$paginator->firstItem()|Sonuç kümesindeki ilk verinin numarasını alır|
|$paginator->getOptions()|Sayfalama seçeneklerini alır|
|$paginator->getUrlRange($start, $end)|Belirli sayfa numarası aralığı için URL oluşturur|
|$paginator->hasPages()|Birden fazla sayfa oluşturmak için yeterli veri var mı diye kontrol eder|
|$paginator->hasMorePages()|Görüntülemek için daha fazla sayfa var mı diye kontrol eder|
|$paginator->items()|Mevcut sayfadaki veri öğelerini alır|
|$paginator->lastItem()|Sonuç kümesindeki son verinin numarasını alır|
|$paginator->lastPage()|En son sayfa numarasını alır (simplePaginate'de kullanılamaz)|
|$paginator->nextPageUrl()|Bir sonraki sayfanın URL'sini alır|
|$paginator->onFirstPage()|Mevcut sayfa ilk sayfa mı diye kontrol eder|
|$paginator->perPage()|Her sayfada gösterilen toplam miktarı alır|
|$paginator->previousPageUrl()|Önceki sayfanın URL'sini alır|
|$paginator->total()|Sonuç kümesindeki toplam veri sayısını alır (simplePaginate'de kullanılamaz)|
|$paginator->url($page)|Belirli sayfanın URL'sini alır|
|$paginator->getPageName()|Sayfa numarasını saklamak için kullanılan sorgu parametre adını alır|
|$paginator->setPageName($name)|Sayfa numarasını saklamak için kullanılan sorgu parametre adını ayarlar|

> **Not**
> `$paginator->links()` methodu desteklenmez

## Sayfalama bileşeni
webman'de `$paginator->links()` methoduyla sayfa düğmeleri oluşturulamaz, ancak başka bir bileşen kullanarak oluşturulabilir, örneğin `jasongrimes/php-paginator`.

**Kurulum**
`composer require "jasongrimes/paginator:~1.0"`


**Arka Planda**
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

**Şablon (PHP orijinal)**
Yeni şablon oluşturun app/view/user/get.html
```html
<html>
<head>
  <!-- Dahili Bootstrap sayfa stili desteği -->
  <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
</head>
<body>

<?= $paginator;?>

</body>
</html>
```

**Şablon (twig)**
Yeni şablon oluşturun app/view/user/get.html
```html
<html>
<head>
  <!-- Dahili Bootstrap sayfa stili desteği -->
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
Yeni şablon oluşturun app/view/user/get.blade.php
```html
<html>
<head>
  <!-- Dahili Bootstrap sayfa stili desteği -->
  <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
</head>
<body>

{!! $paginator !!}

</body>
</html>
```

**Şablon (thinkphp)**
Yeni şablon oluşturun app/view/user/get.html
```html
<html>
<head>
    <!-- Dahili Bootstrap sayfa stili desteği -->
    <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
</head>
<body>

<?=$paginator?>

</body>
</html>
```

Sonuç olarak:
![](../../assets/img/paginator.png)

# 2. Thinkphp ORM tabanlı sayfalama yöntemi
Ek bir kütüphane kurmaya gerek yok, sadece think-orm'u kurmanız yeterlidir.
## Kullanım
```php
public function index(Request $request)
{
    $per_page = 10;
    $users = Db::table('user')->paginate(['list_rows' => $per_page, 'page' => $request->get('page', 1), 'path' => $request->path()]);
    return view('index/index', ['users' => $users]);
}
```

**Şablon (thinkphp)**
```html
<html>
<head>
    <!-- Dahili Bootstrap sayfa stili desteği -->
    <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
</head>
<body>

{$users|raw}

</body>
</html>
```
