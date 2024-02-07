# பக்கங்கள் பிரித்தல்

# 1. Laravel ORM ஆதாரம் எடுத்துக்காட்டப்பட்ட பகிர்வு முறைகள்
Laravel இன் `illuminate/database` சுவாரஸ்யமான பகிர்வு அமைப்பை வழங்குகின்றது.

## நிறுவும்
`composer require illuminate/pagination`

## பயன்படுத்துகின்றது
```php
public function index(Request $request)
{
    $per_page = 10;
    $users = Db::table('user')->paginate($per_page);
    return view('index/index', ['users' => $users]);
}
```

## பக்கப் பட்டியல் நிரல் முறைகள்
|  முறை   | விளக்கம்  |
|  ----  |-----|
|$paginator->count()|தற்போதைய பக்கத்தின் மொத்த மதிப்பைப் பெறுங்கள்|
|$paginator->currentPage()|தற்போதைய பக்கப் பட்டியின் பக்கக் கோப்புக்கினைப் பெறுங்கள்|
|$paginator->firstItem()|முதல் விளக்கங்களில் முதன்வரிசையின் மதிப்பைப் பெறுங்கள்|
|$paginator->getOptions()|பக்கப்பட்டி விருப்பங்களைப் பெறுங்கள்|
|$paginator->getUrlRange($start, $end)|குறுகிய பக்க எண் வரம்பைக் கொண்ட URL படிப்பை உருவாக்குங்கள்|
|$paginator->hasPages()|பல பக்கங்களைத் தயாரிப்பதற்கு போது போதுமான பதிவுகள் உள்ளனவா?|
|$paginator->hasMorePages()|மேலும் பக்கங்கள் காட்ட முடியுமா?|
|$paginator->items()|தற்போதைய பக்கத்தின் தரவு பொருட்களைப் பெறுங்கள்|
|$paginator->lastItem()|கொடுக்கப்படும் முடிவுப் படிவத்தின் இறுதி பொருளின் மதிப்பைப் பெறுங்கள்|
|$paginator->lastPage()|இறுதி பக்கத்தின் பக்கம் எண் பெறுங்கள் (simplePaginate இல் கிடைக்கவில்லை)|
|$paginator->nextPageUrl()|அடுத்த பக்கத்தின் URL ஐப் பெறுங்கள்|
|$paginator->onFirstPage()|தற்போதைய பக்கம் முதல் பக்கமானதா?|
|$paginator->perPage()|ஒவ்வொன்றுக்கு காட்டப்படும் எண்ணிக்கையைப் பெறுங்கள்|
|$paginator->previousPageUrl()|முந்தைய பக்கத்தின் URL ஐப் பெறுங்கள்|
|$paginator->total()|முடிவுப் பட்டியலில் உள்ள மொத்த தரவுகளைப் பெறுங்கள் (simplePaginate இல் கிடைக்கவில்லை)|
|$paginator->url($page)|குறிப்பிட்ட பக்கத்தின் URL ஐப் பெறுங்கள்|
|$paginator->getPageName()|பக்க எண்களை சேமிக்கும் வினாக்கள் பெயரைப் பெறுங்கள்|
|$paginator->setPageName($name)|பக்க எண்களை சேமிக்கும் வினாக்கள் பெயரை அமைக்குங்கள்|

> **குறிப்பு**
> `$paginator->links()` முறையை ஆதரிக்காது
## பக்கம் ஐகட்டும் பொருட்கள்
போன்றவை கருவிகளை பயன்படுத்தி பக்கம் ஐகட்டுவதற்கு webman இல் `$paginator->links()` முறையை பயன்படுத்த முடியவில்லை, ஆனால் மற்ற பொருட்களை பயன்படுத்தி செதுக்கப்படுவது முடியும், உதாரணம் `jasongrimes/php-paginator`

**நிறுவு**
`composer require "jasongrimes/paginator:~1.0"`


**விருப்பங்கள்**
```php
<?php
பிரிவு app\controller;

பயன்பாட்டை JasonGrimes\Paginator க்கு பயன்படுத்துகின்றேன்;
பயன்பாட்டை Request எனும் use செய்தல்;
பயன்பாட்டை Db எனும் use செய்தல்;

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

**வடிவமைப்பு(php மூல)**
புதிய வார்த்தைடம் app/view/user/get.html எனும் வடிவமைப்பை உருவாக்கவும்
```html
<html>
<head>
  <!-- உள்ளமைவிடப்பில் உள்ளது Bootstrap பக்க மடவை -->
  <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
</head>
<body>

<?= $paginator;?>

</body>
</html>
```

**வடிவமைப்பு(ட்விக்)** 
புதிய வார்த்தைடம் app/view/user/get.html எனும் வடிவமைப்பை உருவாக்கவும்
```html
<html>
<head>
  <!-- உள்ளமைவிடப்பில் உள்ளது Bootstrap பக்க மடவை -->
  <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
</head>
<body>

{% autoescape false %}
{{paginator}}
{% endautoescape %}

</body>
</html>
```

**வடிவமைப்பு(பிளேட்)** 
புதிய வார்த்தைடம் app/view/user/get.blade.php எனும் வடிவமைப்பை உருவாக்கவும்
```html
<html>
<head>
  <!-- உள்ளமைவிடப்பில் உள்ளது Bootstrap பக்க மடவை -->
  <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
</head>
<body>

{!! $paginator !!}

</body>
</html>
```

**வடிவமைப்பு(thinkphp)**
புதிய வார்த்தைடம் app/view/user/get.html எனும் வடிவமைப்பை உருவாக்கவும்
```html
<html>
<head>
    <!-- உள்ளமைவிடப்பில் உள்ளது Bootstrap பக்க மடவை -->
    <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
</head>
<body>

<?=$paginator?>

</body>
</html>
```

வேலைசெய்தது:
![](../../assets/img/paginator.png)

# 2. Thinkphp அடிப்படையான OR மூலத்தின் பக்கம் வழங்குதல்
வேறு தொகுப்பி பார்வைச் செய்ய தேவையானவரை நிறுவுகின்றது, ஒரு பொருத்தமான வார்த்தைடத்தை நிறுவிய பின்பு think-orm நிறுவாக போதனையிலிருந்து எந்தவொரு வேலையையும் செய்யத் தேவை இல்லை
## பயன்படுத்தல்
```php
public function index(Request $request)
{
    $per_page = 10;
    $users = Db::table('user')->paginate(['list_rows' => $per_page, 'page' => $request->get('page', 1), 'path' => $request->path()]);
    return view('index/index', ['users' => $users]);
}
```

**template (thinkphp)**
```html
<html>
<head>
    <!-- உள்ளே பூட்டப்பட்டுள்ள Bootstrap பக்க அமைப்புகள் -->
    <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
</head>
<body>

{$users|raw}

</body>
</html>
```
