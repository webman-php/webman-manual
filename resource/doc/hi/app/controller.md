# नियंत्रक

PSR4 मानक के अनुसार, नियंत्रक कक्षा का नामस्थान `plugin\{प्लगइन पहचान}` से शुरू होता है, जैसे

नया नियंत्रक फ़ाइल बनाएं `plugin/foo/app/controller/FooController.php`।

```php
<?php
namespace plugin\foo\app\controller;

use support\Request;

class FooController
{
    public function index(Request $request)
    {
        return response('hello index');
    }
    
    public function hello(Request $request)
    {
        return response('hello webman');
    }
}
```

`http://127.0.0.1:8787/app/foo/foo` पर पहुँच करने पर, पृष्ठ `hello index` वापस लौटता है।

`http://127.0.0.1:8787/app/foo/foo/hello` पर पहुँच करने पर, पृष्ठ `hello webman` वापस लौटता है।


## URL पहुँच
एप्लिकेशन प्लगइन URL पते का पाठ `/app` से शुरू होता है, उसके बाद प्लगइन पहचान आती है, और फिर कॉन्ट्रोलर और विधि होती है।
उदाहरण के लिए `plugin\foo\app\controller\UserController` URL पता है `http://127.0.0.1:8787/app/foo/user`
