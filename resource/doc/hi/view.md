## दृश्य
webman डिफ़ॉल्ट रूप से PHP मूलभूत सिंटैक्स का उपयोग करता है जो मौजूदा हालत में 'opcache' को खोलने पर सर्वश्रेष्ठ प्रदर्शन देता है। php मूलभूत टेम्पलेट के अलावा, webman प्रदान करता है [Twig](https://twig.symfony.com/doc/3.x/) , [Blade](https://learnku.com/docs/laravel/8.x/blade/9377) , [think-template](https://www.kancloud.cn/manual/think-template/content) टेम्पलेट इंजन।

## opcache चालू करें
दृश्य का उपयोग करते समय, php.ini में `opcache.enable` और `opcache.enable_cli` दोनों विकल्पों को सक्षम करने की गहरी सलाह दी जाती है, ताकि टेम्पलेट इंजन सबसे अच्छा प्रदर्शन कर सके।

## Twig का स्थापना करें
1. Composer स्थापित करें

```composer require twig/twig```


2. कॉन्फ़िगरेशन बदलें `config/view.php` को
```php
<?php
use support\view\Twig;

return [
    'handler' => Twig::class
];
```
> **सुझाव**
> अन्य कॉन्फ़िगरेशन विकल्पों को डेटा के माध्यम से पास करने के लिए ऑप्शन्स का उपयोग किया जा सकता है, जैसे

```php
return [
    'handler' => Twig::class,
    'options' => [
        'debug' => false,
        'charset' => 'utf-8'
    ]
];
```


## Blade का स्थापना करें
1. Composer स्थापित करें

```composer require psr/container ^1.1.1 webman/blade```


2. कॉन्फ़िगरेशन बदलें `config/view.php` को
```php
<?php
use support\view\Blade;

return [
    'handler' => Blade::class
];
```

## think-template का स्थापना करें
1. Composer स्थापित करें

```composer require topthink/think-template```


2. कॉन्फ़िगरेशन बदलें `config/view.php` को
```php
<?php
use support\view\ThinkPHP;

return [
    'handler' => ThinkPHP::class,
];
```
> **सुझाव**
> अन्य कॉन्फ़िगरेशन विकल्पों को ऑप्शन्स के माध्यम से पास करने के लिए यह उपयुक्त है, जैसे

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

## मूलभूत PHP टेम्पलेट इंजन उदाहरण
निम्नलिखित रूप में फ़ाइल बनाएं `app/controller/UserController.php`

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

निम्नलिखित रूप में फ़ाइल बनाएं `app/view/user/hello.html`

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

## Twig टेम्पलेट इंजन उदाहरण
कॉन्फ़िगरेशन बदलें `config/view.php` को
```php
<?php
use support\view\Twig;

return [
    'handler' => Twig::class
];
```

`app/controller/UserController.php` की तरह निम्नलिखित रूप में फ़ाइल बनाएं

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

फ़ाइल `app/view/user/hello.html` की तरह निम्नलिखित होगी

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

[Twig](https://twig.symfony.com/doc/3.x/) के अधिक डिटेल के लिए दस्तावेज़ देखें

## Bladeटेम्पलेट इंजन उदाहरण
कॉन्फ़िगरेशन बदलें `config/view.php` को
```php
<?php
use support\view\Blade;

return [
    'handler' => Blade::class
];
```

`app/controller/UserController.php` की तरह निम्नलिखित रूप में फ़ाइल बनाएं

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

फ़ाइल `app/view/user/hello.blade.php` की तरह निम्नलिखित होगी

> ध्यान दें कि ब्लेड टेम्पलेट का नामबद्धता संकेत बाद वाला होता है `.blade.php`

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

[Blade](https://learnku.com/docs/laravel/8.x/blade/9377) के अधिक डिटेल के लिए दस्तावेज़ देखें

## ThinkPHP टेम्पलेट इंजन उदाहरण
कॉन्फ़िगरेशन बदलें `config/view.php` को
```php
<?php
use support\view\ThinkPHP;

return [
    'handler' => ThinkPHP::class
];
```

`app/controller/UserController.php` की तरह निम्नलिखित रूप में फ़ाइल बनाएं

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

फ़ाइल `app/view/user/hello.html` की तरह निम्नलिखित होगी

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

[think-template](https://www.kancloud.cn/manual/think-template/content) के अधिक डिटेल के लिए दस्तावेज़ देखें

## टेम्पलेट मूल्यांकन
`view(टेम्पलेट, डेटा एरे)` का उपयोग करके ही टेम्पलेट को मूल्यांकित किया जा सकता है, हम यहां `View::assign()` को कॉल करके किसी भी स्थान पर टेम्पलेट को मूल्यांकित कर सकते हैं। उदाहरण के लिए:

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

`View::assign()` कुछ स्थितियों में बहुत उपयोगी होता है, जैसे कि किसी प्रणाली में प्रत्येक पृष्ठ के शीर्ष में वर्तमान उपयोगकर्ता की जानकारी प्रदर्शित करनी होती है, और यदि प्रत्येक पेज को 'view('टेम्पलेट', ['user_info' => 'उपयोगकर्ता जानकारी']);' के माध्यम से मूल्यांकित किया जाता है, तो यह बहुत परेशानीपूर्ण हो जाएगा। समाधान उपयुक्तता से है कि आप मध्यवर्ती में उपयोगकर्ता जानकारी प्राप्त करें, और फिर `View::assign()` के माध्यम से उपयोगकर्ता जानकारी को टेम्पलेट मूल्यांकित करें।

## दृश्य फ़ाइल पथ के बारे में

#### नियंत्रक
जब नियंत्रक `view('टेम्पलेटनाम',[]);` को कॉल करता है, तो दृश्य फ़ाइल निम्नलिखित नियमों के अनुसार खोजी जाती है।

1. अधिकारियों के लिए, `app/view/` के तहत संबंधित संवाद फ़ाइल का उपयोग करें
2. [मल्टी-ऐप](multiapp.md) के लिए, `app/प्रयोगनाम/view/` के तहत संबंधित संवाद फ़ाइल का उपयोग करें

संक्षेप में, इस बात से कहा जा सकता है कि यदि `$reqeust->app` खाली है, तो `app/view/` के तहत संवाद फ़ाइल का उपयोग किया जाता है, अन्यथा `app/{$request->app}/view/` के तहत संवाद फ़ाइल का उपयोग किया जाता है।

#### संधिवचन फ़ंक्शन
संधिवचन फ़ंक्शन `$request->app` खाली है, किसी भी एप्लीकेशन से संबद्ध नहीं है, इसलिए संधिक फ़ंक्शन `app/view/` के तहत संवाद फ़ाइल का उपयोग करता है, जैसे कि `config/route.php` में निर्दिष्ट रूप से यह प्रस्तावित करते हैं
```php
Route::any('/admin/user/get', function (Reqeust $reqeust) {
    return view('user', []);
});
```
`app/view/user.html` को संवाद फ़ाइल के रूप में उपयोग करता है (ब्लेड टेम्पलेट का उपयोग करते समय संवाद फ़ाइल `app/view/user.blade.php` के रूप में उपयोग करता है)।

#### ऐप निर्दिष्ट करें
मल्टी-ऐप मोड में संवाद संवाद का पुनर्प्रयोग किया जा सकता है, `view('टेम्पलेटनाम', डेटा, ऐप = null)` तीसरा पैरामीटर `$app` का उपयोग करके किस एप्लिकेशन निर्दिष्ट करने के लिए किया जा सकता है। उदाहरण के लिए `view('user',[], 'admin');`  `app/admin/view/` के तहत संबंधित संवाद फ़ाइल का उपयोग करने के लिए मजबूर रूप से किया जाएगा।

## twig विस
