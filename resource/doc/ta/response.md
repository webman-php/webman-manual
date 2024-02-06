# பதில்
பதில் மேலாண் ஒரு `support\Response` பொருள் இருக்கிறது, இந்த பொருளை உருவாக்குவதற்கு சுழற்சி செய்வதற்கு, வெப்மான் கெட்டிபுகளை வழங்குகின்றது.

## எந்த பொருளுமில்லையாக்கு

**உதாரணம்**
```php
<?php
namespace app\controller;

use support\Request;

class FooController
{
    public function hello(Request $request)
    {
        return response('hello webman');
    }
}
```

பதில் ஒரு பொருள் பட்டியலை விளக்க கீழ் பதிவிடப்பட்டது:
```php
function response($body = '', $status = 200, $headers = array())
{
    return new Response($status, $headers, $body);
}
```

நீங்கள் அதேப்போல response பொருளை முன்வரி, பிறகு வார்த்தை `$response->cookie()` `$response->header()` `$response->withHeaders()` `$response->withBody()` பயன்படுத்தி வழுத்து உள்ளிட்ட உள்ளீகளை அமைத்துக் கொள்ளலாம்.
```php
public function hello(Request $request)
{
    // ஒன்றைஉருப்படுத்து
    $response = response();
    
    // .... வணிக செயல்பாடு மறைவுடன்
    
    // குடியெழுத்து அமைக்க
    $response->cookie('foo', 'value');
    
    // .... வணிக செயல்பாடு மறைவுடன்
    
    // ஐசிசக்கள் தொடங்கி
    $response->header('Content-Type', 'application/json');
    $response->withHeaders([
                'X-Header-One' => 'Header Value 1',
                'X-Header-Tow' => 'Header Value 2',
            ]);

    // .... வணிக செயல்பாடு மறைவுடன்

    // பின்னர் ஒளிபரப்பப்படுத்தப்பட்ட தரவை அமைக்க 
    $response->withBody('தரவு முடியும்');
    return $response;
}
```

## JSON குளம்

**உதாரணம்**
```php
<?php
namespace app\controller;

use support\Request;

class FooController
{
    public function hello(Request $request)
    {
        return json(['code' => 0, 'msg' => 'ok']);
    }
}
```
json கெட்டி விளக்க கீழே வருகிறது:
```php
function json($data, $options = JSON_UNESCAPED_UNICODE)
{
    return new Response(200, ['Content-Type' => 'application/json'], json_encode($data, $options));
}
```

## XML குளம்
**உதாரணம்**
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

xml கெட்டி விளக்க கீழே வருகிறது:
```php
function xml($xml)
{
    if ($xml instanceof SimpleXMLElement) {
        $xml = $xml->asXML();
    }
    return new Response(200, ['Content-Type' => 'text/xml'], $xml);
}
```

## காட்சியை கிளிக் செய்ய
புதிய கோப்பு `app/controller/FooController.php` இப்போது உள்ளது.

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

புதிய கோப்பு `app/view/foo/hello.html` இப்போது உள்ளது

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

## மீள்பாளு
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

redirect கெட்டி கீழே வருகிறது:
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

## தலைப்பு அமைப்பை அமை
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
            'X-Header-One' => 'Header Value' 
        ]);
    }
}
```
நீங்கள் `header` மற்றும் `withHeaders` முறைகளைப் பயன்படுத்தி ஒருதவியினால் முன்னே ஒளிபரப்பு தலைப்பை அல்லது குளத்தை அமைப்பது முக்கியமாக காணப்படும்.
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
            'X-Header-One' => 'Header Value 1',
            'X-Header-Tow' => 'Header Value 2',
        ]);
    }
}
```
நீங்கள் முன்னே தலைப்பை அமைக்க முன், கடைசியாக ஒளிபரப்பு செய்ய விரும்பும் தரவை அமைக்கலாம்.
```php
public function hello(Request $request)
{
    // ஒன்றைஉருப்படுத்து
    $response = response();
    
    // .... வணிக செயல்பாடு மறைவுடன்
  
    // ஐசிசக்குளத்தை அமைக்க 
    $response->header('Content-Type', 'application/json');
    $response->withHeaders([
                'X-Header-One' => 'Header Value 1',
                'X-Header-Tow' => 'Header Value 2',
            ]);

    // .... வணிக செயல்பாடு ம்
  
    // பின்னர் ஒளிபரப்பப்படுத்த விரும்பும் தரவை அமைக்க 
    $response->withBody('தரவு முடியும்');
    return $response;
}
```

## குக்கி அமை
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

நீங்கள் முன்னே குக்கி அமைக்க முன், கடைசி வெளியீட்டு தரவை அமைக்க முக்கியமாக காணப்படும்.
```php
public function hello(Request $request)
{
    // ஒன்றைஉருப்படுத்து
    $response = response();
    
    // .... வணிக செயல்பாடு மறைவுடன்

    // குக்கி அமைக்க 
    $response->cookie('foo', 'value');
    
    // .... வணிக செயல்பாடு மறைவுடன்

    // பின்னர் ஒளிபரப்பப்படுத்தப்பட்ட தரவை அமைக்க
    $response->withBody('தரவு முடியும்');
    return $response;
}
```

cookie முழு அளவுக்கு மதிப்பீடு என்ன, அளவு, பாதை, முகவரி, பாதை, ஒளிவற்ற ஆகியவற்கு உதிர்வாக, அப்பாலான முகவரி, சுரக்கம் பிறகு தகுப்பானாக இருக்கின்றது.

## கோப்பு ஆவணம்
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

- வெப்மான் அதிகபாரான கோப்புகளுக்கு நகர்த்தம் செய்யும்
- பெரிய கோப்புகளுக்கு(2M மேல்) வெப்மான் முழு கோப்பை ஒன்றுள்ள அமைத்திடல் ஫ைல், புதுசரிவில் வாசிக்காமல், பிற அவசியமில்லாமலும் அவசியமில்லை
- வலுவான கோப்புகள் (2M க்கு மேல்) உருவாக்குவதன் மூலம் உள்ள வெக்கத்தை வருவாக சீரமைக்கும் போது வெப்மான் கோப்பை வாசிப்பதை முக்கியமாக காப்பாறும்
- தரவு அனுப்பும் விடை நிலவாக்கப்படாது, மற்ற வந்து விடமாமலே அதில் முதலாவது லோட் செய்யும்
- file வழைவு தனித்தனியான`if-modified-since` தலைலை சேர்க்கப்படும் மீதமுள்ள பிணைய மறைக்கப்படாதுநடக்கப்படீகிறது, கேள்விக்குமுறை எடுக்கும்
- தொடர்கூறுவான புதிய கோப்பு தானாக ஏவுகணையில் அல்லடும் பிசி சேர்ப்பதன் மூலம் மற்ற கோரிக்கைகள் மத்தியமாகப் புதுப்பி கேள்விச்சிடும்

## கோப்பொன்றைப் பதிவின
