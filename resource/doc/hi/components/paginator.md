# पेजिनेटर कॉम्पोनेंट

## jasongrimes/php-paginator

### प्रोजेक्ट लिंक

https://github.com/jasongrimes/php-paginator

### स्थापना

```php
composer require "jasongrimes/paginator:^1.0.3"
```

### उपयोग

नया बनाएँ `app/controller/UserController.php`
```php
<?php
namespace app\controller;

use support\Request;
use JasonGrimes\Paginator;

class UserController
{
    /**
     * उपयोगकर्ता सूची
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
**टेम्पलेट (PHP मूल)**

नया टेम्पलेट बनाएँ app/view/user/get.html
```html
<html>
<head>
  <!-- इंटीग्रेटेड Bootstrap पेजिनेटर स्टाइल्स सपोर्ट -->
  <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
</head>
<body>

<?= $paginator; ?>

</body>
</html>
```

**टेम्पलेट (twig)**

नया टेम्पलेट बनाएँ app/view/user/get.html
```html
<html>
<head>
  <!-- इंटीग्रेटेड Bootstrap पेजिनेटर स्टाइल्स सपोर्ट -->
  <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
</head>
<body>

{% autoescape false %}
{{ paginator }}
{% endautoescape %}

</body>
</html>
```

**टेम्पलेट (ब्लेड)**

नया टेम्पलेट बनाएँ app/view/user/get.blade.php
```html
<html>
<head>
  <!-- इंटीग्रेटेड Bootstrap पेजिनेटर स्टाइल्स सपोर्ट -->
  <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
</head>
<body>

{!! $paginator !!}

</body>
</html>
```

**टेम्पलेट (thinkphp)**

नया टेम्पलेट बनाएँ app/view/user/get.blade.php
```html
<html>
<head>
    <!-- इंटीग्रेटेड Bootstrap पेजिनेटर स्टाइल्स सपोर्ट -->
    <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
</head>
<body>

<?=$paginator ?>

</body>
</html>
```

परिणाम निम्नलिखित है:
![](../../assets/img/paginator.png)

### अधिक जानकारी

परिवर्तन करने के लिए https://github.com/jasongrimes/php-paginator  विजिट करें।
