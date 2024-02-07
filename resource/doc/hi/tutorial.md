# सरल उदाहरण

## स्ट्रिंग रिटर्न करना
**एक नया कंट्रोलर बनाएं**

निम्नलिखित रूप में `app/controller/UserController.php` नामक फ़ाइल बनाएं

```php
<?php
namespace app\controller;

use support\Request;

class UserController
{
    public function hello(Request $request)
    {
        $default_name = 'webman';
        //  जो नाम parameter में मिले है, अगर नाम parameter नहीं मिला तो $default_name लौटा दिया जाए
        $name = $request->get('name', $default_name);
        //  ब्राउज़र को स्ट्रिंग रिटर्न करें
        return response('hello ' . $name);
    }
}
```


**दर्शन**

ब्राउज़र में जाएं और `http://127.0.0.1:8787/user/hello?name=tom` लिंक पर जाएं

ब्राउज़र में `hello tom` वापस मिलेगा

## JSON रिटर्न करना
`app/controller/UserController.php` फ़ाइल को निम्नलिखित रूप में बदलें

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
            'msg' => 'ok', 
            'data' => $name
        ]);
    }
}
```

**दर्शन**

ब्राउज़र में जाएं और `http://127.0.0.1:8787/user/hello?name=tom` लिंक पर जाएं

ब्राउज़र में `{"code":0,"msg":"ok","data":"tom"}` वापस मिलेगा

डेटा वापस करने के लिए json हेल्पर फ़ंक्शन का उपयोग करते समय एक header `Content-Type: application/json` स्वचालित रूप से जोड़ दिया जाएगा।

## XML रिटर्न करना
उसी तरह, हेल्पर फ़ंक्शन `xml($xml)` का उपयोग करके एक `xml` प्रतिक्रिया जोड़ता है जिसमें `Content-Type: text/xml` शीर्षक शामिल होता है।

यहां `$xml` पैरामीटर एक `xml` स्ट्रिंग हो सकता है, या फिर `SimpleXMLElement` ऑब्जेक्ट हो सकता है।

## JSONP रिटर्न करना
उसी तरह, हेल्पर फ़ंक्शन `jsonp($data, $callback_name = 'callback')` का उपयोग करके `jsonp` प्रतिक्रिया जोड़ता है।

## भेजें दृश्य
`app/controller/UserController.php` फ़ाइल को निम्नलिखित रूप में बदलें

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

निम्नलिखित रूप में नई फ़ाइल `app/view/user/hello.html` बनाएं

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

ब्राउज़र में जाएं और `http://127.0.0.1:8787/user/hello?name=tom` लिंक पर जाएं
तो एक `hello tom` वाला html पृष्ठ वापस आ जाएगा।

ध्यान दें: webman डिफ़ॉल्ट रूप से टेम्प्लेट के रूप में php मूलभूत संवेदनशीलता का स्वागत करता है। अगर अन्य दृश्य का उपयोग करना चाहते हैं, तो [दृश्य](view.md) के लिए देखें।
