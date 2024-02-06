# எளிமையான எடுத்துக்காட்சி

## சரம் மறை
**புதிய கட்டுப்பாடு**

புதிய கோள்க்களை `app/controller/UserController.php` இங்கே உருவாக்கவும்

```php
<?php
namespace app\controller;

use support\Request;

class UserController
{
    public function hello(Request $request)
    {
        $default_name = 'webman';
        // கெட் கோரி name பரமெடரைப் பெறுவதில், அதற்கு முன்பாகப் பெற்றிருக்கவில்லையே என்றால் $default_name பின்வரும்
        $name = $request->get('name', $default_name);
        // உலாவியில் ஒரு சரமை மட்டும் பின்கொடக்க
        return response('hello ' . $name);
    }
}
```

**அணுவங்கள்**

அணுவங்களில் அணுவங்கள் `http://127.0.0.1:8787/user/hello?name=tom` பயன்படுத்தப்படும்.

உலாவியில் `hello tom` பின்கொடும்.

## JSON மறை
கோள்க்களை `app/controller/UserController.php` பொதுவெளியிடவும்

```php
<?php
namespace app\controller;

use support\Request;

class UserController
{
    public function hello(Request $request)
    {
        $default_name = 'webman';
        $name = $request->get('name', $default_name);
        return json([
            'code' => 0, 
            'msg' => 'நல்லது', 
            'தரவு' => $name
        ]);
    }
}
```

**அணுவங்கள்**

அணுவங்களில் அணுவங்கள் `http://127.0.0.1:8787/user/hello?name=tom` பயன்படுத்தப்படும்.

உலாவியில் `{"code":0,"msg":"நல்லது","தரவு":"tom""}` பின்கொடும்.

JSON உதவி அனுவங்கள், தானாகவே ஒரு பதிவு தலைப்பு `Content-Type: application/json` ஐ சேர்க்கும்.

## XML மறை
மிகவும் அது, உதவி அணுவங்கள் `xml($xml)` ஒரு முறைகளில் ரிச்சுமப் `xml` பிரதிக்கைக் கொடும்.

அதில் `$xml` பக்கம் `xml` சரவணு, அல்லது `SimpleXMLElement` பொருள் இருக்கலாம்

## JSONP ரிச்சுமப்
அது, தமானுவ ஆதாரம் `jsonp($data, $callback_name = 'callback')` ஒரு `jsonp` பிரதிக்கைக் கொடும்.

## பார்ப்பு மறை
கோள்க்களை `app/controller/UserController.php` பொதுவெளியிடவும்

```php
<?php
namespace app\controller;

use support\Request;

class UserController
{
    public function hello(Request $request)
    {
        $default_name = 'webman';
        $name = $request->get('name', $default_name);
        return view('user/hello', ['name' => $name]);
    }
}
```

புதிய கோள்க்களை `app/view/user/hello.html` எப்படி கட்டளை

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

அணுவங்களில் அணுவங்கள் `http://127.0.0.1:8787/user/hello?name=tom` பயன்படுத்தப்படும்.

உலாவியில் `hello tom` பின்கொடும்.

குறிப்பு: webman இயல்பாகவே டி பி உணர்வுச்சமியை திட்டமிட்டுள்ளது. பிற பார்ப்புகள் பார்க்க [பார்ப்பு](view.md) அரபי வசதி.
