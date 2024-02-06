# सरल उदाहरण

## स्ट्रिंग वापसी
**नया कंट्रोलर बनाएं**

निम्नलिखित तरीके से फ़ाइल `app/controller/UserController.php` बनाएं

```php
<?php
namespace app\controller;

use support\Request;

class UserController
{
    public function hello(Request $request)
    {
        $default_name = 'webman';
        // get अनुरोध से name पैरामीटर प्राप्त करें, यदि name पैरामीटर नहीं मिलता है तो $default_name वापस करें
        $name = $request->get('name', $default_name);
        // ब्राउज़र को स्ट्रिंग वापस करें
        return response('hello ' . $name);
    }
}
```

**दौरा**

ब्राउज़र में जाएं और `http://127.0.0.1:8787/user/hello?name=tom` पर जाएं

ब्राउज़र `hello tom` को वापस करेगा

## JSON वापसी
फ़ाइल `app/controller/UserController.php` को निम्नलिखित रूप में बदलें

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

**दौरा**

ब्राउज़र में जाएं और `http://127.0.0.1:8787/user/hello?name=tom` पर जाएं

ब्राउज़र `{"code":0,"msg":"ok","data":"tom"}` को वापस करेगा

JSON सहायक फ़ंक्शन का उपयोग करके डेटा वापस करने के साथ स्वचालित रूप से एक header `Content-Type: application/json` जोड़ा जाएगा।

## XML वापसी
उसी तरह से, सहायक फ़ंक्शन `xml($xml)` का उपयोग करके साथ `Content-Type: text/xml` शीर्षक वाले `xml` प्रतिक्रिया वापस करें।

यहां `$xml` पैरामीटर `xml` स्ट्रिंग या `SimpleXMLElement` ऑब्ज
