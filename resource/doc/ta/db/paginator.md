# பக்கம்

# 1. லைரவல் சாரிமையாகும் ORM-ஐக் காரணிக்கொள்ளும் ஒருசம்
லைரவல் இருமன்ற 'illuminate/database' அம்சங்கள் வழங்குகின்றன.

## நிறுவம்
`composer require illuminate/pagination`

## பயன்படுத்தும்
```php
public function index(Request $request)
{
    $per_page = 10;
    $users = Db::table('user')->paginate($per_page);
    return view('index/index', ['users' => $users]);
}
```

## பக்கக்குறிப்பி நிபுணம்
|  முறை   | விளக்கம்  |
|  ----  |-----|
|$paginator->count()|தற்போதைய பக்க மொத்த தரவுகளைப் பெற்றுகொள்ளுங்கள்|
|$paginator->currentPage()|தற்போதைய பக்கப் பக்கங்கள் எண்ணைப் பெறுங்கள்|
|$paginator->firstItem()|முதலாவது மேம்படுத்தல் கிழவி முனையப்படும் பொருட்களைப் பெறுங்கள்|
|$paginator->getOptions()|பக்கக்குறிப்பி விருப்பங்களைக் காண்பி|
|$paginator->getUrlRange($start, $end)|குறிப்பிடப்பட்ட பக்கக் குறிப்புகளின் URL கட்டமைப்பைக் கட்டுவினர்|
|$paginator->hasPages()|பல பக்கங்களைக் கட்டிய பொருட்கள் உள்ளனவா?|
|$paginator->hasMorePages()|கூடுதல் பக்கங்களுக்கு விடை உள்ளதா?|
|$paginator->items()|தற்போதைய பக்க உருக்கங்களைப் பெறுங்கள்|
|$paginator->lastItem()|கடைசி முனையப்பட்ட பொருட்களைப் பெறுங்கள்|
|$paginator->lastPage()|கடைசி பக்கம் ஓசைவைப் பெறுங்கள்(simplePaginate வகையில் அனைத்திலும் கிடைக்கவில்லை)|
|$paginator->nextPageUrl()|அடுத்த பக்கப் URL-ஐப் பெறுங்கள்|
|$paginator->onFirstPage()|தற்போதைய பக்க முன்னர் அவன் இருந்து அது 1 என்பதைப் பார்|
|$paginator->perPage()|ஒவ்வொரு ஒரு ஒவ்வொரு பக்கங்களில் காண்பி வேலைசெய்யும் மொத்த எண்ணை பெறுங்கள்|
|$paginator->previousPageUrl()|முன்னேற்ற பக்கப் URL-ஐப் பெறுங்கள்|
|$paginator->total()|மொத்த தரவு செய்திகளைப் பெறுங்கள்(simplePaginate வகையில் அனைத்திலும் கிடைக்கவில்லை)|
|$paginator->url($page)|குறிப்பிடப்பட்ட பக்கக் URL-ஐப் பெறுங்கள்|
|$paginator->getPageName()|பக்க எண்வைக்கான விண்ணப்ப பெயரைப் பெறுங்கள்|
|$paginator->setPageName($name)|பக்க எண்வைக்கான விண்ணப்ப பெயரைப் பொருத்துகின்றன|

> **குறிப்பு**
> `$paginator->links()` முறை ஆதரிக்கப்படாது

## பக்கக்குறிப்பி
webman-ல் `$paginator->links()` முறையைப் பயன்படுத்தி பக்க பொதிக்கொளிகள் அமையதல் அல்ல, அதுவெல்லாம் மற்ற பொதிக்கொளிகளைப் பயன்படுத்தி அமையலாம், உதாரணத்தான் `jasongrimes/php-paginator` .


**நிறுவம்**
`composer require "jasongrimes/paginator:~1.0"`


**பின்னால்**
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

**வார்ப்புரு(php அசைவமுறு)**
புதிய வார்ப்புரு app/view/user/get.html உருவாக்கு
```html
<html>
<head>
  <!-- உள்ளேயே உள்ள Bootstrap பக்க வண்ணம் ஆதரித்தல் -->
  <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
</head>
<body>

<?= $paginator;?>

</body>
</html>
```

**வார்ப்புரு(twig)** 
புதிய வார்ப்புரு app/view/user/get.html உருவாக்கு
```html
<html>
<head>
  <!-- உள்ளேயே உள்ள Bootstrap பக்க வண்ணம் ஆதரித்தல் -->
  <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
</head>
<body>

{% autoescape false %}
{{paginator}}
{% endautoescape %}

</body>
</html>
```

**வார்ப்புரு(blade)** 
புதிய வார்ப்புரு app/view/user/get.blade.php உருவாக்கு
```html
<html>
<head>
  <!-- உள்ளேயே உள்ள Bootstrap பக்க வண்ணம் ஆதரித்தல் -->
  <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
</head>
<body>

{!! $paginator !!}

</body>
</html>
```

**வார்ப்புரு(thinkphp)**
புதிய வார்ப்புரு app/view/user/get.html உருவாக்கு
```html
<html>
<head>
    <!-- உள்ளேயே உள்ள Bootstrap பக்க வண்ணம் ஆதரித்தல் -->
    <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
</head>
<body>

<?=$paginator?>

</body>
</html>
```

பார்வைகள்:
![](../../assets/img/paginator.png)

# 2. திங்க்ப்ஹ்பலவின் ORM-ஐக் காரணிக்கொள்ளும் ஒருசம்
பெரியப் படிகள் அந்திபு தயாரில் உள்ள ஏதாவது ஒன்றை நிறுவிக்கொள்ள்றதினால் அடங்காவியிருப்பதாகும்
## பயன்படுத்தும்
```php
public function index(Request $request)
{
    $per_page = 10;
    $users = Db::table('user')->paginate(['list_rows' => $per_page, 'page' => $request->get('page', 1), 'path' => $request->path()]);
    return view('index/index', ['users' => $users]);
}
```

**வார்ப்புரு(thinkphp)**
```html
<html>
<head>
    <!-- உள்ளேயே உள்ள Bootstrap பக்க வண்ணம் ஆதரித்தல் -->
    <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
</head>
<body>

{$users|raw}

</body>
</html>
```
