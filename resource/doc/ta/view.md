## காட்சிகள்
இயல்பான நிரல் உள்ளது மற்றும் `opcache` ஐ திறக்கவும் பல பேர் PHP மூல மொழியைக் குறிமுறைப்படுத்துகின்றன. PHP மூல வார்ப்பு தாவரத்தை பதிவேற்றப்படும் உருவாக்குபவர்கள், webman இன் அதிக பரிமாண மொழியைப் பொறுப்பாக பயன்படுத்தலாம். பொதுவாக php மூல வார்ப்பு தாவர ஆதரவு தகவப்பவர்களுக்கு மேல் இருக்கும் செருகு தீவிரமான பொருளாக இருப்பதை உறுதி செய்யப்பட்டுள்ளது.

## opcache ஐ தேர்வு செய்யுங்கள்
காட்சி பயன்படுத்தும்போது, php.ini மையத்தில் உள்ள `opcache.enable` மற்றும் `opcache.enable_cli` என்பவையை மிகவும் ஏற்றுக்கொள்ள முடியும் என்பதாகப் பார்க்கப்படுகின்றன.

## கொண்டாடம்: நீங்கள் எப்போது நீங்கள் முடிவு ஆன பைக்பரம்வை மாற்றுகின்றீர்கள், பின்வரும் பரிவர்த்தனைகளை மட்டுமே ஆரம்பிக்க வேண்டும். உதாரணமாக,

```php
return [
    'handler' => Twig::class,
    'options' => [
        'debug' => false,
        'charset' => 'utf-8'
    ]
];
```

## Twig ஐ நிறுவுக
1、composer மூலம் நிறுவுக

`composer require twig/twig`

2、`config/view.php` மூலம் கட்டமைக்கவும்
```php
<?php
use support\view\Twig;

return [
    'handler' => Twig::class
];
```
> **குறிப்பு**
> பின்வரும் கட்டமைப்புகள் மூலம் options ஐ பகுப்பாய்வு செய்க, உதாரணமாக,

```php
return [
    'handler' => Twig::class,
    'options' => [
        'debug' => false,
        'charset' => 'utf-8'
    ]
];
```


# Blade ஐ நிறுவுக
1、composer மூலம் நிறுவுக

```composer require psr/container ^1.1.1 webman/blade```

2、`config/view.php` மூலம் கட்டமைக்கவும்
```php
<?php
use support\view\Blade;

return [
    'handler' => Blade::class
];
```

# think-template ஐ நிறுவுக
1、composer மூலம் நிறுவுக

`composer require topthink/think-template`

2、`config/view.php` மூலம் கட்டமைக்கவும்
```php
<?php
use support\view\ThinkPHP;

return [
    'handler' => ThinkPHP::class,
];
```
> **குறிப்பு**
> பின்வரும் கட்டமைப்புகள் மூலம் options ஐ பகுப்பாய்வு செய்க, உதாரணமாக,

```php
return [
    'handler' => ThinkPHP::class,
    'options' => [
        'view_suffix' => 'html',
        'tpl_begin' => '{',
        'tpl_end' => '}'
    ]
];
```
## மூல PHP டெமோ என்பது
கோப்பை `app/controller/UserController.php` உருவாக்குங்கள் பின்வரும் வார்த்தைகளைப் பயன்படுத்தி

```php
<?php
namespace app\controller;

use support\Request;

class UserController
{
    public function hello(Request $request)
    {
        return view('user/hello', ['name' => 'webman']);
    }
}
```

`app/view/user/hello.html` என்ற புதிய கோப்பை உருவாக்கவும் பின்வரும் மாறி

```html
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>webman</title>
</head>
<body>
hello <?=htmlspecialchars($name)?>
</body>
</html>
```

## டிவிக் டெம்ப்ளேட் என்பது
உருப்படி மாற்று அமைப்பை`config/view.php` மாற்றுங்கள்
```php
<?php
use support\view\Twig;

return [
    'handler' => Twig::class
];
```

`app/controller/UserController.php` ப்பை மாற்றுங்கள்

```php
<?php
namespace app\controller;

use support\Request;

class UserController
{
    public function hello(Request $request)
    {
        return view('user/hello', ['name' => 'webman']);
    }
}
```

`app/view/user/hello.html` ப்பை மாற்றுங்கள்

```html
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>webman</title>
</head>
<body>
hello {{name}}
</body>
</html>
```

மேலும் ஆசிரியர்களுக்கு [டிவிக்](https://twig.symfony.com/doc/3.x/) பார்க்க [பார்க்க](https://twig.symfony.com/doc/3.x/)

## முாதலான டெமோ முக்கியவிதி
உருப்படி மாற்றவும் `config/view.php` அமைப்பை
```php
<?php
use support\view\Blade;

return [
    'handler' => Blade::class
];
```

`app/controller/UserController.php` ப்பை மாற்றவும்

```php
<?php
namespace app\controller;

use support\Request;

class UserController
{
    public function hello(Request $request)
    {
        return view('user/hello', ['name' => 'webman']);
    }
}
```

`app/view/user/hello.blade.php` ப்பை மாற்றவும்

> பேர்க்கு இருந்தேன்பால் முடிவில் `.blade.php` என்பதைப் பின்பற்றவும்

```html
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>webman</title>
</head>
<body>
hello {{$name}}
</body>
</html>
```

மேலும் ஆசிரியர்களுக்கு [பிளேட்](https://learnku.com/docs/laravel/8.x/blade/9377) பார்க்கவும்.
## தின்க்பி பி மாதிரி உதாரணம்
உள்ளடக்கைக் கோப்பு 'config/view.php' ஐ மாற்றுக `config/view.php`ஆக மாற்றுக
```php
<?php
use support\view\ThinkPHP;

return [
    'handler' => ThinkPHP::class
];
```

'அப்ப் / கட்டுப்பாட்டாளர் / UserController.php':

```php
<?php
namespace app\controller;

use support\Request;

class UserController
{
    public function hello(Request $request)
    {
        return view('user/hello', ['name' => 'webman']);
    }
}
```

கோப்பு 'app / view / user / hello.html' ஏலம்:

```html
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>வெப்மான்</title>
</head>
<body>
வணக்கம் {$name}
</body>
</html>
```

மேலும் ஆவணங்களைப் பார்க்க [தின்க்-டெம்ப்ளேட்](https://www.kancloud.cn/manual/think-template/content)

## வருமான வழங்கி
மேலும் நிபந்தனை பகுதியில் 'view (அவதானம், மாற்றுபகுதி அணி)' பயன்படுத்தவும், எந்த இடத்தில் முன்னாள் View :: assign () ஐஅழைப்பதால் மாதிரிக்கும். உதாரணமாக:
```php
<?php
namespace app\controller;

use support\Request;
use support\View;

class UserController
{
    public function hello(Request $request)
    {
        View::assign([
            'name1' => 'value1',
            'name2'=> 'value2',
        ]);
        View::assign('name3', 'value3');
        return view('user/test', ['name' => 'webman']);
    }
}
```

View :: assign () யை அனைத்து சீனங்களின் மீது பயன்படுத்தி வழுநீக்கமாகப் பயன்படுத்தப்படுகின்றது. உதாரணமாக, ஒரு முகவரி ஒவ்வொன்றும் நடவடிக்கையிலும் தற்போதைய உள்நுதவி தகவலைக் காட்ட வேண்டும், ஒவ்வொன்றும் 'view(வழக்கு, ['பயனர்_விவரம்' => 'பதிவு விவரம்']);' மூலம் துண்டு. தீர்வு முறையில், மையைப் பெற, பின்பற்றிய மூன்றும் முனை மூலங்களில் எடுக்கப்படீது, சிகாரமாக 'View :: assign (வழக்கு, ['பயனர்_விவரங்கள்' => 'பயனர்_விவரம்']);' பயன்படுத்துகின்றது.
## பார்த்தல் கோப்பு பாதைகளைப் பற்றியது

#### கட்டுப்பாடுகளால்
கட்டுப்பாடக்கி̇டமான பார்த்தல் கோப்பு `$request-> மூலப்படிapp` கால் அழிக்கப்படும்போது தேடப்படும் வழங்குகின்றனது.
 
கூடுதல் அபுதம் ரெப்போர்ட் போன்ற கட்டுப்பாடங்கள்,`app/ பயன்பாடுகள் பெயர்/பார்த்தல்` கோப்புகளைப் பயன்படுத்துகிறது. 

முழுமையாக பற்றிய பேச்சு,  `$request-> மூலப்படிapp` கால் இல்லை என்பதால், `app/view/` கீழே உள்ள பார்த்தல் கோப்புகளைப் பயன்படுத்துகின்றன. அப்போது `app/{$request-> மூலப்படிapp}/view/`-ன் கீழே உள்ள பார்த்தல் கோப்புகளைப் பயன்படுத்துகின்றன. 

#### அடிப்படை செல்லுபடியான பொதுப்பிரிவு
அடிப்படைப் போன்ற அடிப்படை அடிப்படை செல்லுபடியான் `$request-> மூலப்படிapp` பாகுபடாமல் ஏதும் பயன்படாது, எனவே அடிப்படை அமைப்பில் `app/view/` அடுக்கில் உள்ள பார்த்தல் கோப்புகளை பயன்படுத்துகிறது. உதாரணமாக, தட்டச்சு தலையங்கம் வரைபடத்தில் வழங்கும்போது வழக்க வழக்கு
```php
வழக்கு::எந்தத்('/நிர்வாகம்/பயனர்/பெற',  முன்பு (Reqeust $reqeust) {
    return view('பயனர்', []);
});
```
`வழக்கு::எந்தத்('/நிர்வாகம்/பயனர்/பெற',  முன்பு (Reqeust $reqeust) {
    return view('பயனர்', []);
});` யைப் பயன்படுத்தி `app/view/பயனர்.html` படிப்பு ஆகலாம் (பிளேட் படிப்புப்பின் படிப்பு `app/view/பயனர்.blade.php`). 

#### வரைபடக்கைக் குறிக்குக
பல பயன்பாடு முறை பிரச்சினைகள் பற்றியும் பயன்பாடு, view($template, $data, $app = null) மூலம் மூலப்படி எந்த பயன்பாட்டிலும் பயன்படத்தை குறியுக்க முடியும். அதைப் பற்றி உதாரணமாக,  view('பயனர்', [], 'நிர்வாகம்');  எப்படி எந்த பயன்பாடு அடைவில் உள்ள வரைபடத்தைப் பயன்படுத்துவது.`பயனர், [], 'நிர்வாகம்'` ஓரளவுக்கு, `app/admin/view/` அடுக்கில் உள்ள பார்த்தல் கோப்புகளைப் பயன்படுத்தும். 

## திட்டத்தை விரிவாக்குதல் டுவிக்

> **குறிக்கேடு**
> இந்த அம்சத்தைத் தகவலைப் பெறவேற்கொள்ளும்போது webman-framework>=1.4.8 பின்னர் இருக்க வேண்டும்

நாம் webman கோப்புகளை விரிவாக்க திட்டத்தைப் பல்வேறு முறைகளில் நீக்கி வைக்கலாம்  config/பார்த்தல்.பாதைகள் தொகுத்தல் `அமைப்பு` மாற்றத்தின் ஒரு புதியதை சேர்க்கும்படியாகிவிடும். `config/பார்த்தல்.php` போன்று இதுவும் குறித்துரையாகும்
```php
<?php
use support\view\Twig;
return [
    'handler' => Twig::class,
    'extension' => function (Twig\Environment $twig) {
        $twig->addExtension(new your\namespace\YourExtension()); // நீங்கள் விரிவாக்கம் சேர்க்க
        $twig->addFilter(new Twig\TwigFilter('rot13', 'str_rot13')); // நீங்கள் அருக்கானதை சேர்க்க
        $twig->addFunction(new Twig\TwigFunction('செயல்புலம்_பெயர்', function () {})); // வரிசை சேர்க்க.
    }
];
```

## பிளேடை விரிவகம் செய்தல்
> **குறிப்பு**
> இந்த அம்சம் webman-framework>=1.4.8 பதிப்பில் கிடைக்கும்
நமது விண்ணப்பத்தை வெளியிட்டும் `view.extension` அமைப்பை மூலப்படுத்தவும் ஆதரிக்கிறது, உதாரணமாக `config/view.php` என்பது:

```php
<?php
use support\view\Blade;
return [
    'handler' => Blade::class,
    'extension' => function (Jenssegers\Blade\Blade $blade) {
        // பிளேடை உருவாக்கத்திற்கு அடிப்படையான ஆணைகளைச் சேர்க்கவும்
        $blade->directive('mydate', function ($timestamp) {
            return "<?php echo date('Y-m-d H:i:s', $timestamp); ?>";
        });
    }
];
```

## பிளேட் பயன்பாடு component

> **குறிப்பு**
>webman/blade>=1.5.2 பதிப்பினை தேவைப்படுகின்றது

என்னைப் பார்ப்பதற்கு ஒரு Alert கொம்பினைச் சேர்க்க வேண்டும் 

**புதிய `app/view/components/Alert.php` உருவாக்கவும்**
```php
<?php

namespace app\view\components;

use Illuminate\View\Component;

class Alert extends Component
{
    
    public function __construct()
    {
    
    }
    
    public function render()
    {
        return view('components/alert')->rawBody();
    }
}
```

**புதிய`app/view/components/alert.blade.php` உருவாக்கவும்**
```
<div>
    <b style="color: red">hello blade component</b>
</div>
```

**`/config/view.php` என்பது பின்வரும் நிரலை**

```php
<?php
use support\view\Blade;
return [
    'handler' => Blade::class,
    'extension' => function (Jenssegers\Blade\Blade $blade) {
        $blade->component('alert', app\view\components\Alert::class);
    }
];
```

இப்போது ப்ளேட் கொம்பினை Alert முடிவுக்கு தயாராக்கியுள்ளது, வார்த்தைப்படத்தில் பயன்படுத்தும்போது போக்கப்படுவது போன்றுள்ளது
```html
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>webman</title>
</head>
<body>

<x-alert/>

</body>
</html>
```
## திங்கு-டெம்ப்ளேட்டை விரிவாக்குவதற்கு
think-template ஐ `view.options.taglib_pre_load` பயன்படுத்தி குறிச்சொல்ல் நூலகத்தை விரிவாக்குவதற்கான பெருமக்களம் பயன்படுத்துகின்றது. உதவி எளிதாக்கியதானது
```php
<?php
use support\view\ThinkPHP;
return [
    'handler' => ThinkPHP::class,
    'options' => [
        'taglib_pre_load' => your\namspace\Taglib::class,
    ]
];
```

விவரங்களுக்கு [think-template உருவாக்குதல் குறியிடுதலை](https://www.kancloud.cn/manual/think-template/1286424) பார்க்கவும்
