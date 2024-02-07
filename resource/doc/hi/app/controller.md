# नियंत्रक

PSR4 मानक के अनुसार, नियंत्रक कक्षा नामस्थान `plugin\{प्लगइन पहचान}` से शुरू होता है, उदाहरण के लिए

नया नियंत्रक फ़ाइल बनाएँ `plugin/foo/app/controller/FooController.php`।

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

`http://127.0.0.1:8787/app/foo/foo` की पहुंच के समय, पृष्ठ `hello index` वापस करता है।

`http://127.0.0.1:8787/app/foo/foo/hello` की पहुंच के समय, पृष्ठ `hello webman` वापस करता है।


## URL पहुंच
ऐप प्लगइन URL पते की पथ `द्वारा/app` से शुरू होती है, उसके बाद प्लगइन पहचान, और फिर विशिष्ट नियंत्रक और विधि।
उदाहरण के लिए `plugin\foo\app\controller\UserController` URL पता है `http://127.0.0.1:8787/app/foo/user`
