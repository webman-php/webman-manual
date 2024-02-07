# பக்கம் தாட்டு

## jasongrimes/php-paginator

### திட்டம் இடம்

https://github.com/jasongrimes/php-paginator

### நிறுவு

```php
composer require "jasongrimes/paginator:^1.0.3"
```

### பயன்பாடு

புதிய `app/controller/UserController.php` உருவாக்கவும்
```php
<?php
namespace app\controller;

use support\Request;
use JasonGrimes\Paginator;

class UserController
{
    /**
     * பயனர் பட்டியல்
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
**டெம்ப்ளேட்(PHP உருவாக்கம்)**

புதிய டெம்ப்ளேட் app/view/user/get.html
```html
<html>
<head>
  <!-- உள்ளூரினவரை Bootstrap பக்க பார்வை முறை -->
  <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
</head>
<body>

<?= $paginator;?>

</body>
</html>
```

**டெம்ப்ளேட்(twig)**

புதிய டெம்ப்ளேட் app/view/user/get.html
```html
<html>
<head>
  <!-- உள்ளூரினவரை Bootstrap பக்க பார்வை முறை -->
  <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
</head>
<body>

{% autoescape false %}
{{paginator}}
{% endautoescape %}

</body>
</html>
```

**டெம்ப்ளேட்(பிளேட்)**

புதிய டெம்ப்ளேட் app/view/user/get.blade.php
```html
<html>
<head>
  <!-- உள்ளூரினவரை Bootstrap பக்க பார்வை முறை -->
  <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
</head>
<body>

{!! $paginator !!}

</body>
</html>
```

**டெம்ப்ளேட்(திங்க்பிபி)**

புதிய டெம்ப்ளேட் app/view/user/get.blade.php
```html
<html>
<head>
    <!-- உள்ளூரினவரை Bootstrap பக்க பார்வை முறை -->
    <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
</head>
<body>

<?=$paginator?>

</body>
</html>
```

பார்வையிலேயே பாகங்கள் பட்டிக்கள்![](../../assets/img/paginator.png)

### மேலும் உள்ளடக்கங்களுக்கு

https://github.com/jasongrimes/php-paginator
