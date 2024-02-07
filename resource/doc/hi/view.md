## दृश्य
webman डिफ़ॉल्ट रूप से php मूल संवाद का उपयोग करता है जैसा कि टेम्पलेट के रूप में, 'opcache' को खोलने के बाद इसमें सर्वोत्तम प्रदर्शन होता है। php मूल टेम्पलेट के अलावा, webman ने [Twig](https://twig.symfony.com/doc/3.x/) 、[Blade](https://learnku.com/docs/laravel/8.x/blade/9377) 、[think-template](https://www.kancloud.cn/manual/think-template/content) template engine भी प्रदान किया है।

## opcache को खोलना
दृश्य का उपयोग करते समय, php.ini में `opcache.enable` और `opcache.enable_cli` दोनों विकल्पों को खोलने की सलाह दी जाती है, ताकि टेम्पलेट इंजन को सर्वोत्तम प्रदर्शन प्राप्त हो सके।

## Twig इंस्टॉल करें
1. कॉम्पोजर द्वारा स्थापित करें

   `composer require twig/twig`

2. कॉन्फ़िगरेशन फ़ाइल `config/view.php` को निम्नलिखित रूप में संशोधित करें
   ```php
   <?php
   use support\view\Twig;

   return [
       'handler' => Twig::class
   ];
   ```
   > **सुझाव**
   > अन्य कॉन्फ़िगरेशन विकल्प options के माध्यम से प्रविष्ट किए जा सकते हैं, जैसे

   ```php
   return [
       'handler' => Twig::class,
       'options' => [
           'debug' => false,
           'charset' => 'utf-8'
       ]
   ];
   ```

## Blade इंस्टॉल करें
1. कॉम्पोजर द्वारा स्थापित करें

   ```composer require psr/container ^1.1.1 webman/blade```

2. कॉन्फ़िगरेशन फ़ाइल `config/view.php` को निम्नलिखित रूप में संशोधित करें
   ```php
   <?php
   use support\view\Blade;

   return [
       'handler' => Blade::class
   ];
   ```

## think-template इंस्टॉल करें
1. कॉम्पोजर द्वारा स्थापित करें

   `composer require topthink/think-template`

2. कॉन्फ़िगरेशन फ़ाइल `config/view.php` को निम्नलिखित रूप में संशोधित करें
   ```php
   <?php
   use support\view\ThinkPHP;

   return [
       'handler' => ThinkPHP::class,
   ];
   ```
   > **सुझाव**
   > अन्य कॉन्फ़िगरेशन विकल्प options के माध्यम से प्रविष्ट किए जा सकते हैं, जैसे

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

## मूल PHP टेम्पलेट इंजन उदाहरण
फ़ाइल `app/controller/UserController.php` निम्नलिखित प्रकार होगा

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

निम्नलिखित प्रकार की नई फ़ाइल `app/view/user/hello.html` बनाएं

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
कॉन्फ़िगरेशन फ़ाइल `config/view.php` को निम्नलिखित रूप में संशोधित करें
```php
<?php
use support\view\Twig;

return [
    'handler' => Twig::class
];
```

`app/controller/UserController.php` निम्नलिखित प्रकार होगा

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

फ़ाइल `app/view/user/hello.html` निम्नलिखित प्रकार की होगी

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

अधिक दस्तावेज़ [Twig](https://twig.symfony.com/doc/3.x/) देखें

## Blade टेम्पलेट उदाहरण
कॉन्फ़िगरेशन फ़ाइल `config/view.php` को निम्नलिखित रूप में संशोधित करें
```php
<?php
use support\view\Blade;

return [
    'handler' => Blade::class
];
```

`app/controller/UserController.php` निम्नलिखित प्रकार होगा

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

फ़ाइल `app/view/user/hello.blade.php` निम्नलिखित प्रकार की होगी
> ध्यान दें कि ब्लेड टेम्पलेट की एक्सटेंशन `.blade.php` होती है।

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

अधिक दस्तावेज़ [Blade](https://learnku.com/docs/laravel/8.x/blade/9377) देखें

## ThinkPHP टेम्पलेट उदाहरण
कॉन्फ़िगरेशन फ़ाइल `config/view.php` को निम्नलिखित रूप में संशोधित करें
```php
<?php
use support\view\ThinkPHP;

return [
    'handler' => ThinkPHP::class
];
```

`app/controller/UserController.php` निम्नलिखित प्रकार होगा

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

फ़ाइल `app/view/user/hello.html` निम्नलिखित प्रकार की होगी

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

अधिक दस्तावेज़ [think-template](https://www.kancloud.cn/manual/think-template/content) देखें
## टेम्प्लेट मूल्यांकन
`view(टेम्प्लेट, परिवर्तन_सरणी)` का उपयोग करने के अलावा, हम किसी भी स्थान पर `View::assign()` को कॉल करके टेम्प्लेट को मूल्यांकन दे सकते हैं। उदाहरण के लिए:
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

`View::assign()` कुछ स्थितियों में काफी उपयोगी होता है, उदाहरण के लिए किसी सिस्टम के हर पेज के शीर्ष में लॉगिन करने वाले व्यक्ति की जानकारी दिखाना होता है, अगर हर पेज को इस जानकारी को `view('टेम्प्लेट', ['user_info' => 'उपयोगकर्ता जानकारी']);` के माध्यम से मूल्यांकित किया जाता है तो यह काफी जटिल हो जाएगा। समाधान है कि मध्यवर्ती में उपयोगकर्ता की जानकारी प्राप्त करें, और फिर `View::assign()` के माध्यम से उपयोगकर्ता की जानकारी को टेम्प्लेट को मूल्यांकित करें।

## दृश्य फ़ाइल पथ के बारे में

#### नियंत्रक
जब नियंत्रक `view('टेम्प्लेटनाम',[]);` को कॉल करता है, दृश्य फ़ाइल निम्नलिखित नियमों के अनुसार खोजी जाती है:

1. यदि बहु-एप्लिकेशन नहीं है, तो `app/view/` के अनुसार संबंधित दृश्य फ़ाइल का उपयोग करें
2. [बहु-एप्लिकेशन](multiapp.md) के समय, `app/एप्लिकेशननाम/view/` के अनुसार संबंधित दृश्य फ़ाइल का उपयोग करें

संक्षेप में कहा जाए कि यदि `$request->app` खाली है, तो `app/view/` में दृश्य फ़ाइल का उपयोग करें, अन्यथा `app/{$request->app}/view/` में दृश्य फ़ाइल का उपयोग करें।

#### क्लोजर फ़ंक्शन
क्लोजर फ़ंक्शन में `$request->app` खाली होता है, किसी भी ऐप से संबंधित नहीं होता है, इसलिए क्लोजर फ़ंक्शन `app/view/` में दृश्य फ़ाइल का उपयोग करता है, उदाहरण के लिए `config/route.php` में रूट की परिभाषा
```php
Route::any('/admin/user/get', function (Reqeust $reqeust) {
    return view('user', []);
});
```
यह `app/view/user.html` के रूप में दृश्य फ़ाइल का उपयोग करेगा (जब ब्लेड दृश्य का उपयोग किया जाता है तो दृश्य फ़ाइल `app/view/user.blade.php` होगी)।

#### ऐप को निर्दिष्ट करें
बहु-एप्लिकेशन मोड में टेम्पलेट को पुनः उपयोग करने के लिए view($template, $data, $app = null) तीसरे पैरामीटर `$app` द्वारा ऐप निर्दिष्ट करने का विकल्प प्रदान करता है। उदाहरण के लिए `view('user', [], 'admin');` `app/admin/view/` में संबंधित दृश्य फ़ाइल का उपयोग करेगा।

## ट्विग को विस्तारित करें

> **ध्यान दें**
> यह सुविधा webman-framework>=1.4.8 की आवश्यकता है।

हम `view.extension` कॉन्फ़िगरेशन को ट्विग दृश्य इंस्टेंस को विस्तारित करने के लिए प्रदान करके यहाँ दिये गए `config/view.php` की तरह ट्विग को विस्तारित कर सकते हैं
```php
<?php
use support\view\Twig;
return [
    'handler' => Twig::class,
    'extension' => function (Twig\Environment $twig) {
        $twig->addExtension(new your\namespace\YourExtension()); // विस्तार करने के लिए एक्सटेंशन जोड़ें
        $twig->addFilter(new Twig\TwigFilter('rot13', 'str_rot13')); // फ़िल्टर जोड़ें
        $twig->addFunction(new Twig\TwigFunction('फ़ंक्शन_नाम', function () {})); // फ़ंक्शन जोड़ें
    }
];
```

## ब्लेड को विस्तारित करें

> **ध्यान दें**
> यह सुविधा webman-framework>=1.4.8 की आवश्यकता है

वैसे ही हम `view.extension` कॉन्फ़िगरेशन को ब्लेड दृश्य इंस्टेंस को विस्तारित करने के लिए प्रदान करके यहाँ दिये गए `config/view.php` की तरह ब्लेड को विस्तारित कर सकते हैं
```php
<?php
use support\view\Blade;
return [
    'handler' => Blade::class,
    'extension' => function (Jenssegers\Blade\Blade $blade) {
        // ब्लेड को नेता जोड़ें
        $blade->directive('mydate', function ($timestamp) {
            return "<?php echo date('Y-m-d H:i:s', $timestamp); ?>";
        });
    }
];
```

## ब्लेड कंपोनेंट का उपयोग करना

> **ध्यान दें
> webman/blade>=1.5.2 की आवश्यकता है**

मान लिया जाए कि एक Alert कंपोनेंट जोड़ना चाहिए

**`app/view/components/Alert.php` नया बनाएँ**
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

**`app/view/components/alert.blade.php` नया बनाएँ**
```php
<div>
    <b style="color: red">हेलो ब्लेड कम्पोनेंट</b>
</div>
```

**`/config/view.php` के बराबर कुछ इस तरह का कोड**
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

इसके बाद, ब्लेड कंपोनेंट Alert को सेट कर दिया गया है, जब टेम्प्लेट में उपयोग करते हैं तो चित्रित रूप में निम्नलिखित दिखाई देगा
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

