# Sayfa Numaralandırma

# 1. Laravel ORM tabanlı Sayfa Numaralandırma

Laravel'in `illuminate/database` sunulan kolay sayfalama işlevselliği sağlar.

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

## Sayfalayıcı Nesnesi Yöntemleri
|  Yöntem   | Açıklama  |
|  ----  |-----|
|$paginator->count()|Mevcut sayfadaki veri toplamını alır|
|$paginator->currentPage()|Mevcut sayfa numarasını alır|
|$paginator->firstItem()|Sonuç kümesindeki ilk verinin numarasını alır|
|$paginator->getOptions()|Sayfalama seçeneklerini alır|
|$paginator->getUrlRange($start, $end)|Belirtilen sayfa numarası aralığının URL'sini oluşturur|
|$paginator->hasPages()|Birden fazla sayfa oluşturmak için yeterli veri var mı kontrol eder|
|$paginator->hasMorePages()|Gösterilebilecek daha fazla sayfa var mı kontrol eder|
|$paginator->items()|Mevcut sayfadaki veri öğelerini alır|
|$paginator->lastItem()|Sonuç kümesindeki son verinin numarasını alır|
|$paginator->lastPage()|En son sayfanın sayfa numarasını alır (simplePaginate içinde kullanılamaz)|
|$paginator->nextPageUrl()|Sonraki sayfanın URL'sini alır|
|$paginator->onFirstPage()|Mevcut sayfa ilk sayfa mı kontrol eder|
|$paginator->perPage()|Sayfa başına gösterilecek toplam öğe sayısını alır|
|$paginator->previousPageUrl()|Önceki sayfanın URL'sini alır|
|$paginator->total()|Sonuç kümesindeki toplam veri sayısını alır (simplePaginate içinde kullanılamaz)|
|$paginator->url($page)|Belirtilen sayfanın URL'sini alır|
|$paginator->getPageName()|Sayfa numarasını saklamak için sorgu parametre adını alır|
|$paginator->setPageName($name)|Sayfa numarasını saklamak için sorgu parametre adını ayarlar|

> **Not**
> `$paginator->links()` yöntemi desteklenmez

## Sayfa Numaralandırma Bileşeni
Webman'da `$paginator->links()` yöntemi kullanılamaz, ancak başka bir bileşen kullanarak sayfa düğmelerini oluşturabiliriz, örneğin `jasongrimes/php-paginator`.

**Kurulum**
`composer require "jasongrimes/paginator:~1.0"`


**Sunucu tarafı**
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

**Şablon (php orijinal)**
Yeni şablon oluşturun: app/view/user/get.html
```html
<html>
<head>
  <!-- Dahili olarak Bootstrap sayfa stillerini destekler -->
  <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
</head>
<body>

<?= $paginator;?>

</body>
</html>
```

**Şablon (twig)**
Yeni şablon oluşturun: app/view/user/get.html
```html
<html>
<head>
  <!-- Dahili olarak Bootstrap sayfa stillerini destekler -->
  <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
</head>
<body>

{% autoescape false %}
{{paginator}}
{% endautoescape %}

</body>
</html>
```

**Şablon (bıçak)**
Yeni şablon oluşturun: app/view/user/get.blade.php
```html
<html>
<head>
  <!-- Dahili olarak Bootstrap sayfa stillerini destekler -->
  <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
</head>
<body>

{!! $paginator !!}

</body>
</html>
```

**Şablon (thinkphp)**
Yeni şablon oluşturun: app/view/user/get.html
```html
<html>
<head>
    <!-- Dahili olarak Bootstrap sayfa stillerini destekler -->
    <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
</head>
<body>

<?=$paginator?>

</body>
</html>
```

Görüntü:
![](../../assets/img/paginator.png)

# 2. Thinkphp ORM tabanlı Sayfa Numaralandırma

Extra bir kütüphane kurmaya gerek yok, sadece think-orm kuruluysa yeterlidir.
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
    <!-- Dahili olarak Bootstrap sayfa stillerini destekler -->
    <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
</head>
<body>

{$users|raw}

</body>
</html>
```
