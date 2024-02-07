# प्रतिक्रिया
प्रतिक्रिया वास्तव में एक `support\Response` ऑब्जेक्ट होती है, इस ऑब्जेक्ट को बनाने के लिए आसानी से इसे बनाने के लिए, webman ने कुछ सहायक फ़ंक्शन प्रदान किए हैं।

## किसी भी प्रतिक्रिया वापस दें

**उदाहरण**
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

response() फ़ंक्शन निम्नलिखित रूप में निर्मित होता है:
```php
function response($body = '', $status = 200, $headers = array())
{
    return new Response($status, $headers, $body);
}
```


आप एक खाली `response` ऑब्जेक्ट बना सकते हैं और फिर उचित स्थान पर `$response->cookie()` `$response->header()` `$response->withHeaders()` `$response->withBody()` का उपयोग करके वापसी सामग्री को सेट कर सकते हैं।

```php
public function hello(Request $request)
{
    // एक ऑब्जेक्ट बनाएँ
    $response = response();
    
    // .... बिजनेस लॉजिक को छोड़ दें
    
    // कुकी सेट करें
    $response->cookie('foo', 'value');
    
    // .... बिजनेस लॉजिक को छोड़ दें
    
    // HTTP हेडर सेट करें
    $response->header('Content-Type', 'application/json');
    $response->withHeaders([
                'X-Header-One' => 'हेडर वैल्यू 1',
                'X-Header-Tow' => 'हेडर वैल्यू 2',
            ]);

    // .... बिजनेस लॉजिक को छोड़ दें

    // वापस भेजने के लिए डेटा सेट करें
    $response->withBody('वापस भेजा गया डेटा');
    return $response;
}
```
## JSON वापस देना
**उदाहरण**
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
json फ़ंक्शन का अनुरूपण निम्नलिखित है
```php
function json($data, $options = JSON_UNESCAPED_UNICODE)
{
    return new Response(200, ['Content-Type' => 'application/json'], json_encode($data, $options));
}
```
## XML वापस दें
**उदाहरण**
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

xml फ़ंक्शन का कोड निम्नलिखित है:
```php
function xml($xml)
{
    if ($xml instanceof SimpleXMLElement) {
        $xml = $xml->asXML();
    }
    return new Response(200, ['Content-Type' => 'text/xml'], $xml);
}
```
## व्यू वापसी
निम्नलिखित रूप में `app/controller/FooController.php` फ़ाइल बनाएं

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

निम्नलिखित रूप में `app/view/foo/hello.html` फ़ाइल बनाएं

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
## पुनर्निर्देशन
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

रिडायरेक्ट फ़ंक्शन निम्नलिखित रूप में होता है:
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
## हेडर सेटिंग
```php
<?php
namespace app\controller;

use support\Request;

class FooController
{
    public function hello(Request $request)
    {
        return response('हैलो वेबमैन', 200, [
            'Content-Type' => 'application/json',
            'X-Header-One' => 'हेडर मूल्य' 
        ]);
    }
}
```
आप `header` और `withHeaders` विधि का उपयोग करके हेडर को एकल या समूह में सेट कर सकते हैं।
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
आप पहले से ही हेडर को सेट कर सकते हैं, और अंत में वापस लौटने वाले डेटा को सेट कर सकते हैं।
```php
public function hello(Request $request)
{
    // एक ऑब्जेक्ट बनाएँ
    $response = response();
    
    // .... बिज़नेस लॉजिक छोड़ें
  
    // HTTP हेडर सेट करें
    $response->header('Content-Type', 'application/json');
    $response->withHeaders([
                'X-Header-One' => 'हेडर मान 1',
                'X-Header-Tow' => 'हेडर मान 2',
            ]);

    // .... बिज़नेस लॉजिक छोड़ें

    // वापस लौटाने के लिए डेटा सेट करें
    $response->withBody('वापस लौटने वाला डेटा');
    return $response;
}
```
## कुकी सेट करें

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

आप पहले से ही कुकी सेट कर सकते हैं और अंत में वापसी करने के लिए डेटा सेट कर सकते हैं।
```php
public function hello(Request $request)
{
    // एक ऑब्जेक्ट बनाएं
    $response = response();
    
    // .... व्यावसायिक तर्क छोड़ें
    
    // कुकी सेट करें
    $response->cookie('foo', 'value');
    
    // .... व्यावसायिक तर्क छोड़ें

    // वापसी करने के लिए डेटा सेट करें
    $response->withBody('वापसी करने का डेटा');
    return $response;
}
```

`cookie` मेथड के पूरे पैरामीटर निम्नलिखित होते हैं:

`cookie($name, $value = '', $max_age = 0, $path = '', $domain = '', $secure = false, $http_only = false)`

## फ़ाइल स्ट्रीम वापस दें
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
- webman सुपर लार्ज फ़ाइलें भेजने का समर्थन करता है।
- बड़े फ़ाइलों (2M से अधिक) के लिए, webman पूरी फ़ाइल को एक साथ मेमोरी में नहीं डालेगा, बल्कि सही समय पर चंक के रूप में फ़ाइल को पढ़ेगा और भेजेगा।
- webman ग्राहक की स्वीकृति दर के आधार पर फ़ाइल पठन और भेजने की गति को अनुकूलित करेगा, साथ ही साथ सर्वोत्तम रूप से मेमोरी उपयोग को कम करके सबसे तेज़ फ़ाइल भेजने की गारंटी देगा।
- डेटा भेजना ब्लॉकिंग नहीं है, और यह अन्य अनुरोधों को प्रबंधन पर कोई प्रभाव नहीं डालेगा।
- file मेथड स्वचालित रूप से `if-modified-since` हेडर जोड़ेगा और अगले अनुरोध के साथ `if-modified-since` हेडर की जांच करेगा, यदि फ़ाइल में कोई परिवर्तन नहीं हुआ है तो सीधे 304 वापस भेजेगा ताकि बैंडविड्थ बचाया जा सके।
- भेजी जाने वाली फ़ाइल को स्वचालित रूप से उपयुक्त `Content-Type` हेडर के साथ ब्राउज़र को भेजा जाएगा।
- यदि फ़ाइल मौजूद नहीं है, तो स्वचालित रूप से 404 प्रतिक्रिया में परिवर्तित हो जाएगा।
## फ़ाइल डाउनलोड करें
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
downloadमेथड और fileमेथड के बीच बेसिक रूप से एक ही है, फर्क यह है
1. डाउनलोड करने के बाद फ़ाइल का नाम सेट हो जाएगा, जिसके बाद फ़ाइल ब्राउज़र में नहीं दिखाई देगी
2. downloadमेथड `if-modified-since` हेडर का जांच नहीं करेगा।
## परिणाम प्राप्त करें
कुछ लाइब्रेरी फ़ाइल की सामग्री को सीधे मानक आउटपुट पर प्रिंट कर देती हैं, अर्थात डेटा टर्मिनल में प्रिंट हो जाता है और ब्राउज़र को नहीं भेजा जाता है, इस स्थिति में हमें `ob_start();` और `ob_get_clean();` का उपयोग करके डेटा को एक वेरिएबल में कैच करने की आवश्यकता होती है, और फिर डेटा को ब्राउज़र को भेजने के लिए, जैसे:
```php
<?php

namespace app\controller;

use support\Request;

class ImageController
{
    public function get(Request $request)
    {
        // छवि बनाएं
        $im = imagecreatetruecolor(120, 20);
        $text_color = imagecolorallocate($im, 233, 14, 91);
        imagestring($im, 1, 5, 5,  'एक साधारण पाठ स्ट्रिंग', $text_color);

        // आउटपुट प्राप्ति शुरू
        ob_start();
        // छवि आउटपुट
        imagejpeg($im);
        // छवि सामग्री प्राप्त करें
        $image = ob_get_clean();
        
        // छवि भेजें
        return response($image)->header('Content-Type', 'image/jpeg');
    }
}
```
