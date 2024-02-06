The response is actually a `support\Response` object. To create this object, webman provides some helper functions.

## Returning Any Response

**Example**
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

The implementation of the `response` function is as follows:
```php
function response($body = '', $status = 200, $headers = array())
{
    return new Response($status, $headers, $body);
}
```

You can create an empty `response` object, and then set the content using the methods `$response->cookie()`, `$response->header()`, `$response->withHeaders()`, and `$response->withBody()` at the right time.
```php
public function hello(Request $request)
{
    // Create an object
    $response = response();

    // ... Business logic omitted

    // Set cookie
    $response->cookie('foo', 'value');

    // ... Business logic omitted

    // Set HTTP header
    $response->header('Content-Type', 'application/json');
    $response->withHeaders([
                'X-Header-One' => 'Header Value 1',
                'X-Header-Tow' => 'Header Value 2',
            ]);

    // ... Business logic omitted

    // Set the content
    $response->withBody('वापसी सामग्री');
    return $response;
}
```

## Returning JSON

**Example**
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
The implementation of the `json` function is as follows:
```php
function json($data, $options = JSON_UNESCAPED_UNICODE)
{
    return new Response(200, ['Content-Type' => 'application/json'], json_encode($data, $options));
}
```

## Returning XML

**Example**
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

The implementation of the `xml` function is as follows:
```php
function xml($xml)
{
    if ($xml instanceof SimpleXMLElement) {
        $xml = $xml->asXML();
    }
    return new Response(200, ['Content-Type' => 'text/xml'], $xml);
}
```

## Returning a View
Create a new file `app/controller/FooController.php` as follows:
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

Create a new file `app/view/foo/hello.html` as follows:
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

## Redirecting
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

The implementation of the `redirect` function is as follows:
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

## Setting Headers
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
You can set single or multiple headers using the 'header' and 'withHeaders' methods.
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
You can set headers first and then set the returning data at the end.

## Setting Cookies
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
You can set cookies first and then set the returning data at the end.
```php
public function hello(Request $request)
{
    // Create an object
    $response = response();
    
    // ... Business logic omitted
    
    // Set cookie
    $response->cookie('foo', 'value');
    
    // ... Business logic omitted

    // Set the content
    $response->withBody('वापसी सामग्री');
    return $response;
}
```
The full parameter of the cookie method is as follows:

`cookie($name, $value = '', $max_age = 0, $path = '', $domain = '', $secure = false, $http_only = false)`
## फ़ाइल स्ट्रीम लौटाना
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

- webman, सुपर बड़े फ़ाइल भेजने का समर्थन करता है
- बड़ी फ़ाइलों (2M से अधिक) के लिए, webman पूरी फ़ाइल को एक साथी दिन में साइनिक मेमरी में नहीं पढ़ेगा, बल्कि सही समय पर फ़ाइल को सेगमेंट में पढ़ेगा और भेजेगा
- webman क्लाइंट द्वारा प्राप्त गति के आधार पर फ़ाइल पठन और भेजने की गति को अनुकूलित करेगा, जिससे अधिक से अधिक फ़ाइल को तेजी से भेजा जा सके और मेमरी उपयोग को कम तक कर सके
- डेटा भेजना अब लागभाग बंद नहीं होगा, जो कि अन्य अनुरोध प्रसंस्थान पर प्रभाव नहीं डालेगा
- फ़ाइल मैथकुलेट हेडर आप्शनली `if-modified-since` हेडर जोड़ेगा और अगले अनुरोध में `if-modified-since` हेडर की जांच करेगा, यदि फ़ाइल अपडेट नहीं होती है तो सीधे 304 को जांचने के लिए वापस देंगे ताकि बवंडविड्थ को कम किया जा सके
- भेजी गई फ़ाइल ब्राउज़र को ऑटोमैटिक ही संगत `Content-Type` हेडर का प्रयोग करती है
- अगर फ़ाइल मैजूद नहीं है, तो स्वचालित रूप से 404 प्रतिक्रिया में बदल जाएगी

## फ़ाइल डाउनलोड
```php
<?php
namespace app\controller;

use support\Request;

class FooController
{
    public function hello(Request $request)
    {
        return response()->download(public_path() . '/favicon.ico', 'फ़ाइलनाम.ico');
    }
}
```
डाउनलोड फ़ंक्शन और फ़ाइल फ़ंक्शन के मूल रूप में है, अंतर है
1. डाउनलोड करें फ़ाइल नाम सेट होने के बाद, फ़ाइल ब्राउज़र में दिखाई नहीं देगी
2. डाउनलोड फ़ंक्शन `if-modified-since` हेडर की जाँच नहीं करेगा।

## आउटपुट प्राप्त करें
कुछ लाइब्रेरी फ़ाइल सामग्री को सीधे मानक आउटपुट के रूप में प्रिंट करती हैं, अर्थात डेटा कमांड लाइन टर्मिनल में प्रिंट होगा, वहां ब्राउज़र को नहीं भेजा जाएगा, इस स्थिति में हम एक वेरिएबल में डेटा को पकड़े और उसके बाद डेटा को ताज़ा करके ब्राउज़र को भेजने की ज़रूरत होती है, उदाहरण के लिए:

```php
<?php

namespace app\controller;

use support\Request;

class ImageController
{
    public function get(Request $request)
    {
        // छवि बनाना
        $im = imagecreatetruecolor(120, 20);
        $text_color = imagecolorallocate($im, 233, 14, 91);
        imagestring($im, 1, 5, 5,  'A Simple Text String', $text_color);

        // प्राप्ति प्रारंभ करें
        ob_start();
        // छवि प्रिंट करें
        imagejpeg($im);
        // छवि सामग्री प्राप्त करें
        $image = ob_get_clean();
        
        // छवि भेजें
        return response($image)->header('Content-Type', 'image/jpeg');
    }
}
```
