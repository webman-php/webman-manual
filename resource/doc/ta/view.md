## காணொளி
webman இயன்றது, மூலம் php மூலம் புதிய உரைத்தளமாக காண முகவரி பள்ளி பயன்படுத்துகின்றது `opcache` பின்இல்லாதில் ஏற்றனவபடுத்தினது. php மூலத்தை விட்டு, webman [Twig](https://twig.symfony.com/doc/3.x/) எனும்,  [Blade](https://learnku.com/docs/laravel/8.x/blade/9377) ஆனாலும், [என்பது-மரபு நெறிகரு](https://www.kancloud.cn/manual/think-template/content) புதியவழிகள் வழங்குகின்றன.

## opcache இயக்கம்
காண பொறியாகி பயன்படுத்தினால் `opcache.enable` மற்றும் `opcache.enable_cli` இரு தேர்வுகளை php.ini இல் செயல்படுத்த வேண்டி, உடன்பட புதியவழி முகவரி அளவை பெற கூடும்.

## ட்விக் நிறுவுதல்
1、composer மூலம் நிறுவு
`composer require twig/twig`

2、உரையாட்சி `config/view.php` மாற்றம்
```php
<?php
use support\view\Twig;

return [
    'handler' => Twig::class
];
```
> **குறிப்பு**
> பிற கேள்வி விரும்புகின்ற தேர்வுகள் optionsஅறுக்கடியை சேர்த்த மூலம் வழக்கமாக செலுத்த முடிகின்றது, உதாரணமாக  

```php
return [
    'handler' => Twig::class,
    'options' => [
        'debug' => false,
        'charset' => 'utf-8'
    ]
];
```


## ப்ளேட் நிறுவுதல்
1、composer மூலம் நிறுவு
```
composer require psr/container ^1.1.1 webman/blade
```

2、உரையாட்சி `config/view.php` மாற்றம்
```php
<?php
use support\view\Blade;

return [
    'handler' => Blade::class
];
```

## think-வழி நிறுவுதல்
1、composer மூலம் நிறுவு
`composer require topthink/think-template`

2、உரையாட்சி `config/view.php` மாற்றம்
```php
<?php
use support\view\ThinkPHP;

return [
    'handler' => ThinkPHP::class,
];
```
> **குறிப்பு**
> பிற கேள்வி விரைவுகள் options மூலம் வழக்கமாக செலுத்த முடிகின்றது, உதாரணமாக

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

## மூலம் PHP உதாரணை
உருப்படியுள்ள கோப்பு `app/controller/UserController.php` வேறுப஛னை
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

புதிய கோப்பு `app/view/user/hello.html` வேறுப஛னை
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

## ட்விக் உதாரணை
உரையாட்சி `config/view.php` மாற்றம் 
```php
<?php
use support\view\Twig;

return [
    'handler' => Twig::class
];
```

`app/controller/UserController.php` வேறுப஛னை
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

கோப்பு `app/view/user/hello.html` வேறுப஛னை
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

மேலும் கையேச்சல்கள் [Twig](https://twig.symfony.com/doc/3.x/) பார்க்க 

## ப்ளேட் உதாரணை
உரையாட்சி `config/view.php` மாற்றம் 
```php
<?php
use support\view\Blade;

return [
    'handler' => Blade::class
];
```

`app/controller/UserController.php` வேறுப஛னை
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

கோப்பு `app/view/user/hello.blade.php` வேறுப஛னை
> அற்றவட/blade மூலம் உரையாட்சிகள் கோப்பு அருவர்கள் `.blade.php` எனப்படுகின்றது

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

மேலும் கையேச்சல்கள் [Blade](https://learnku.com/docs/laravel/8.x/blade/9377)

## கருவி உதாரணை
உரையாட்சி `config/view.php` மாற்றம் 
```php
<?php
use support\view\ThinkPHP;

return [
    'handler' => ThinkPHP::class
];
```

`app/controller/UserController.php` வேறுப஛னை
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

கோப்பு `app/view/user/hello.html` வேறுப஛னை
```html
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>webman</title>
</head>
<body>
hello {$name}
</body>
</html>
```

மேலும் கையேச்சல்கள் [think-template](https://www.kancloud.cn/manual/think-template/content)

## புதிய முனை கொடுப்பது
`உரைத்தளத்திற்கு view(வரையறு, மாையம் ஜாத்)` பயனிப்பு, அவையால் மூலத்தில் `View::assign()` மூலம் உரைத்தளத்திற்கு ஜாதై கொடுக்கலாம். உதாரணமாக:
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

`View::assign()` ஏலத்திற்கு பில்கபாகுகளைச் சில நிலைகளில் பயன்படுத்தல் மேலும் பயமற்றது, உதாரணமாக சில மிர்சபடுத்தி மாறதளம், அபாலாக் `View::assign()` மூலகத்்துர அனுபாயிக்க விரும்பியிருக்கும். 

## காண உறாதி தைலம்
குறி
[கோப்பு-உற்பத்தி-சலன்கள்](கோப்பு-உற்பத்தி-சலன்கள்.)

#### மு஖்ய திரி
மு஖்ய திரி அழுகம் `view('வரையிறு', உள்ளகப்பகு)` முக்கின்றது, பார்ப்பான் விதித்து - :

1. பலா-விதிகள் இல்,  பயன்படுத்து `app/வரையறு/` கீழ் வேறுபடுத்து
2. [பலா பயன்படுத்து](multiapp.md) இல், பயன்படுத்து 'app/போயி படு' கீழ் வேறுபடுத்த

மொத்த உலாகினூடு விதித்து முற்படி ஆனதில, 'உள்ள' -> $கோர்ச்சி மூலத்திற்கு விழுகலா.

#### அஞ்சா விதித்து
அஞ்சா விதித்து $கோர்ச்சி மூலத்து விட பிரகா எனப்படுவேன், அது' app/வரையறு/' கீழ் வேறுக்குச் செலுத்திப் போகா பயம் போகுமொ, 'app/${வரையற}/படு/விட பிரபாச்வம் 

#### குருா பயன்
பலா விதிற்கு சுடலா இதரவட தூணத்தொ துக்க 'app/வரையற/' கிபட்சி விதியைக்  கொட்ட மூட்டி. உதாரணமாக, 'தர
