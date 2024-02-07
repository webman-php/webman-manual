# பதில்

பதில் மேலாண்மையானது உள்ளது, இதுவரை ஒரு `support\Response` பொருட்டை உருவாக்க எளிதாக்கினாலும், webman பல ஆதரவாளர் செயலாக்க வழி வழங்குகிறது.

## எந்த பதிலும் மேற்படுத்துதல்

**உதாரணமாக**
```php
<?php
namespace app\controller;

use support\Request;

class FooController
{
    public function hello(Request $request)
    {
        return response('வணக்கம் வார்ப்புரு');
    }
}
```

response செயலாக்க உள்ளது:
```php
function response($body = '', $status = 200, $headers = array())
{
    return new Response($status, $headers, $body);
}
```

நீங்கள் இயல்புநிலையாக ஒரு காலியெழுத்தை உருவாக்கி, பின்பு தீர்ப்புக்கான இடத்தில் `$response->cookie()` `$response->header()` `$response->withHeaders()` `$response->withBody()` மூலம் மேல் திரும்பி விருப்பத்தை அமைக்கலாம்.
```php
public function hello(Request $request)
{
    // ஒரு பொருட்டை உருவாக்குக
    $response = response();
    
    // .... வணிகம் கருவிகள் நீண்டதில்
    
    // cookie ஐ அமைக்குக
    $response->cookie('foo', 'மதிப்பை');
    
    // .... வணிகம் கருவிகள் நீண்டதில்
    
    // எண்ணிக்கை திட்டம் ஐ அமைக்கவும்
    $response->header('Content-Type', 'application/json');
    $response->withHeaders([
                'X-Header-One' => 'தலைப்பு மதிப்பு 1',
                'X-Header-Tow' => 'தலைப்பு மதிப்பு 2',
            ]);

    // .... வணிகம் கருவிகள் நீண்டதில்

    // மேலோட்டத்தை அசைய மேலும் விரும்பும்பொழுது வரும் தரவை அமைக்கவும்
    $response->withBody('மேலோட்டம் வருகிறது');
    return $response;
}
```

## json பதில் மேற்கொள்ளுதல்
**உதாரணமாக**
```php
<?php
namespace app\controller;

use support\Request;

class FooController
{
    public function hello(Request $request)
    {
        return json(['குறியீடு' => 0, 'செய்தி' => 'நலம்']);
    }
}
```
json செயலாக்க உள்ளது
```php
function json($data, $options = JSON_UNESCAPED_UNICODE)
{
    return new Response(200, ['Content-Type' => 'application/json'], json_encode($data, $options));
}
```
## எக்ஸ்எம்எல் பின்பற்றுக்கு
**எடுத்துக்காட்டாக்கல்**
```php
<?php
namespace app\controller;

use support\Request;

class FooController
{
    public function hello(Request $request)
    {
        $xml = <<<XML
               <?xml version='1.0' standalone='yes'?>
               <values>
                   <truevalue>1</truevalue>
                   <falsevalue>0</falsevalue>
               </values>
               XML;
        return xml($xml);
    }
}
```

இந்த xml சுருக்கம் பின்வரும் முதன்முதல் வழிமுறையை செயலிழப்பதன் மூலம் உரைக்கப்படுகிறது:
```php
function xml($xml)
{
    if ($xml instanceof SimpleXMLElement) {
        $xml = $xml->asXML();
    }
    return new Response(200, ['Content-Type' => 'text/xml'], $xml);
}
```

## பார்வை திட்டத்தைப் பின்வரும்
app/controller/FooController.php எனும் புதிய கோப்பை உருவாக்குங்கள் பின்வரும் வடிவத்தில்:

```php
<?php
namespace app\controller;

use support\Request;

class FooController
{
    public function hello(Request $request)
    {
        return view('foo/hello', ['name' => 'webman']);
    }
}
```

app/view/foo/hello.html எனும் புதிய கோப்பை உருவாக்குங்கள் பின்வரும் வடிவத்தில்:

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

## மீள்பார்ப்பல்
```php
<?php
namespace app\controller;

use support\Request;

class FooController
{
    public function hello(Request $request)
    {
        return redirect('/user');
    }
}
```

redirect செயல்முறையை பின்வரும் மூலம் உரைக்கப்படுகிறது:
```php
function redirect($location, $status = 302, $headers = [])
{
    $response = new Response($status, ['Location' => $location]);
    if (!empty($headers)) {
        $response->withHeaders($headers);
    }
    return $response;
}
```
## ஹெட்டர் அமைப்பு

```php
<?php
namespace app\controller;

use support\Request;

class FooController
{
    public function hello(Request $request)
    {
        return response('hello webman', 200, [
            'Content-Type' => 'application/json',
            'X-Header-One' => 'ஹெட்டர் மதிப்பு' 
        ]);
    }
}
```
ஹெட்டருக்காக `header` மற்றும் `withHeaders` முறைகளைப் பயன்படுத்தி அமைப்பை ஒருங்கிணைத்து அல்லது மறுமலாப்பு அமைக்கலாம்.
```php
<?php
namespace app\controller;

use support\Request;

class FooController
{
    public function hello(Request $request)
    {
        return response('hello webman')
        ->header('Content-Type', 'application/json')
        ->withHeaders([
            'X-Header-One' => 'ஹெட்டர் மதிப்பு 1',
            'X-Header-Tow' => 'ஹெட்டர் மதிப்பு 2',
        ]);
    }
}
```
நீங்கள் முதலில் ஹெட்டரை அமைத்துக் கொண்டுள்ளது, கடைசில் திரும்பிப் பொருத்தப்படும் தரவை அமைக்கலாம்.
```php
public function hello(Request $request)
{
    // ஒரு பொருளை உருவாக்குக
    $response = response();
    
    // .... விருப்பங்கள் முரண்படுத்தப்பட்டன
  

    // ஆட்சியாக http தலைப்பை அமைக்க
    $response->header('Content-Type', 'application/json');
    $response->withHeaders([
                'X-Header-One' => 'ஹெட்டர் மதிப்பு 1',
                'X-Header-Tow' => 'ஹெட்டர் மதிப்பு 2',
            ]);

    // .... விருப்பங்கள் முரண்படுத்தப்பட்டன

    // திரும்ப வருகின்ற தரவை அமைக்க
    $response->withBody('திரும்ப வந்த தரவு');
    return $response;
}
```
## குக்கீ அமைப்பு

```php
<?php
namespace app\controller;

use support\Request;

class FooController
{
    public function hello(Request $request)
    {
        return response('hello webman')
        ->cookie('foo', 'value');
    }
}
```

நீங்கள் முன்பதிவு செய்யக் கூடிய பதிவுகளை அமைக்கலாம், கடைசியாக பின்னப்பட்ட தரவை அமைக்கலாம்.
```php
public function hello(Request $request)
{
    // ஒரு பொருள் உருவாக்குக
    $response = response();
    
    // .... வேலையின் மிகுந்த சாத்தியத்தை குறிப்பிடாது
    
    // அமைக்குகிறோம் குக்கி
    $response->cookie('foo', 'value');
    
    // .... வேலையின் மிகுந்த சாத்தியத்தை குறிப்பிடாது

    // மின்னஞ்சலில் அனுப்ப விரும்புகிறது தரவை அமைக்குக
    $response->withBody('மின்னஞ்சலில் அனுப்ப விரும்புகிறது தரவை');
    return $response;
}
```

குக்கி மெதாடு முழுமையாக இருக்கும் அளவுருக்கள்:

`cookie($name, $value = '', $max_age = 0, $path = '', $domain = '', $secure = false, $http_only = false)`

## கோப்பு பரப்புரை
```php
<?php
namespace app\controller;

use support\Request;

class FooController
{
    public function hello(Request $request)
    {
        return response()->file(public_path() . '/favicon.ico');
    }
}
```

- webman மிகுந்த கோப்புகளை அனுப்ப முடியும்
- பெரிய கோப்புகளுக்கு(2M க்கும் மேற்பட்டது) முழு கோப்பையும் நினைவுற்ற நேரத்தில் வாசிக்கால் கோப்பையே அனுப்ப வேண்டியிருக்கின்றது அல்லது அனுப்ப முடியாது
- webman முறியடி மாறியமைக்கப்பட்ட நேரத்தில் கிலையிடும் பிரித்தியமாக குறிப்பிடப்பட்டிருக்கின்றது, தெரிந்துகொள்ள மேடையில் `if-modified-since` தலைப்பைச் சேர்ப்பதைப் போல, கோப்பு மாறானால் மின்னஞ்சல் அனுப்புவது போன்றதை சரிபார்த்துக்கொள்ள அடுத்த வழியை அழித்துவிடும் 304 ஐ சேர்த்துக்கொள்ள வேண்டும்
- அனுப்பப்படும் கோப்பின் மூலம் உட்செலுத்தப்படும் 'Content-Type' தலைப்பைப் பயன்படுத்த புதிய FireFox க்கும் Chrome க்கும் ஊடாக ஏற்படும் செயல்பாகவும் மேற்கொள்கின்றது
- கோப்பு இல்லையெனில், தனக்குரிய 404 பதிலைக் கிடைக்க தானாகவே மாறுவிக்கப்படும்
## கோப்பைப் பதிவிறக்கு
```php
<?php
namespace app\controller;

use support\Request;

class FooController
{
    public function hello(Request $request)
    {
        return response()->download(public_path() . '/favicon.ico', 'கோப்புப் பெயர்.ico');
    }
}
```
download முறையை file முறையைப் பொருத்தப்படுகிறது ஆனால், 
1. கோப்பின் பெயரை நிரப்பப்படுவதன் பின் கோப்பு பதிவிறக்கமாகச் செயல்படும், அல்லது உலாவி முகவரியில் காட்டப்படும்
2. download முறையில் `if-modified-since` தலைப்புக்குத் தழுவப்படாது.


## வெளியீட்டைப்பெறுதல்
பெயர்கின்ற சில நூலகங்கள் தகவல் நேரடியாக முடிவில் அச்சிடுகின்றன, ஒருங்கிணைக்கப்பட்டு வளர்ச்சியான விகிதத்தினைப் பதிப்பில் அணைத்தும் அனுப்ப, உங்களுக்குத் தேவையானதைப் பதிவிறக்கும், உங்களுக்குத் தேவையானதை அனுப்புவதற்கு நாங்கள் `ob_start();` `ob_get_clean();` செய்ய, அப்படியானால் தகவலை மின்னூலத்தின் அடிப்படைக்குள் கட்டணமாக அதை கடைசியாக அனுப்பினோம், உதாரணமாக:

```php
<?php

namespace app\controller;

use support\Request;

class ImageController
{
    public function get(Request $request)
    {
        // படம் உருவாக்குவது
        $im = imagecreatetruecolor(120, 20);
        $text_color = imagecolorallocate($im, 233, 14, 91);
        imagestring($im, 1, 5, 5,  'A Simple Text String', $text_color);

        // வெளியீடுகளைப் பெறுவதைத் தொடங்குங்கள்
        ob_start();
        // படத்தை வெளியீடு செய்யுங்கள்
        imagejpeg($im);
        // பட உள்ளடக்கத்தைப் பெறுங்கள்
        $image = ob_get_clean();
        
        // படத்தைப் பதிவி செய்யுங்கள்
        return response($image)->header('Content-Type', 'image/jpeg');
    }
}
```
