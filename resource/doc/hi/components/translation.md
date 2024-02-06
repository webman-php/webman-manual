# बहुभाषा

बहुभाषा के लिए [symfony/translation](https://github.com/symfony/translation) component का उपयोग किया जाता है।

## स्थापना
```
कॉम्पोज़र require symfony/translation
```

## भाषा पैक बनाएँ
webman डिफ़ॉल्ट रूप से `resource/translations` निर्देशिका में भाषा पैक रखता है (यदि नहीं है तो कृपया खुद बनाएँ)। यदि आप निर्देशिका को बदलना चाहते हैं, तो `config/translation.php` में सेट करें।
प्रत्येक भाषा के लिए एक सब निर्देशिका होता है, जो  `messages.php` में भाषा परिभाषाएँ डिफ़ॉल्ट रूप से रखता है। नीचे उदाहरण दिया गया है:
```
resource/
└── translations
    ├── en
    │   └── messages.php
    └── zh_CN
        └── messages.php
```

सभी भाषा फ़ाइलें एक सरणी लौटाती हैं, जैसे कि:
```php
// resource/translations/en/messages.php

return [
    'hello' => 'Hello webman',
];
```

## संरचना

`config/translation.php`

```php
return [
    // डिफ़ॉल्ट भाषा
    'locale' => 'zh_CN',
    // वापसी भाषा, जब वर्तमान भाषा में अनुवाद नहीं मिलता है तो वापसी भाषा का प्रयास करें
    'fallback_locale' => ['zh_CN', 'en'],
    // भाषा फ़ाइलों को रखने की निर्देशिका
    'path' => base_path() . '/resource/translations',
];
```

## अनुवाद

अनुवाद के लिए `trans()` विधि का उपयोग किया जाता है।

`resource/translations/zh_CN/messages.php` को निम्नलिखित रूप में बनाएं:
```php
return [
    'hello' => 'नमस्ते वेबमैन!',
];
```

`app/controller/UserController.php` फ़ाइल बनाएँ
```php
<?php
namespace app\controller;

use support\Request;

class UserController
{
    public function get(Request $request)
    {
        $hello = trans('hello'); // नमस्ते वेबमैन!
        return response($hello);
    }
}
```

`http://127.0.0.1:8787/user/get` पर जाएँ तो "नमस्ते वेबमैन!" वापस मिलेगा!

## डिफ़ॉल्ट भाषा बदलें

भाषा को बदलने के लिए `locale()` विधि का उपयोग किया जाता है।

नया भाषा फ़ाइल `resource/translations/en/messages.php` बनाएँ जैसे कि:
```php
return [
    'hello' => 'hello world!',
];
```

```php
<?php
namespace app\controller;

use support\Request;

class UserController
{
    public function get(Request $request)
    {
        // भाषा बदलें
        locale('en');
        $hello = trans('hello'); // hello world!
        return response($hello);
    }
}
```

`http://127.0.0.1:8787/user/get` पर जाएँ तो "hello world!" वापस मिलेगा!

आप `trans()` फ़ंक्शन के चौथे पैरामीटर का उपयोग करके भाषा अस्थायी रूप से बदल सकते हैं, जैसे ऊपर का उदाहरण और नीचे वाला यह बराबर है:
```php
<?php
namespace app\controller;

use support\Request;

class UserController
{
    public function get(Request $request)
    {
        // चौथे पैरामीटर द्वारा भाषा बदलें
        $hello = trans('hello', [], null, 'en'); // hello world!
        return response($hello);
    }
}
```

## हर अनुरोध के लिए भाषा स्पष्ट रूप से सेट करें
translation एक सिंगलटन है, इसका अर्थ है कि सभी अनुरोध इस उदाहरण को साझा करते हैं, यदि किसी अनुरोध ने `locale()` का उपयोग करके डिफ़ॉल्ट भाषा सेट की है, तो यह इस प्रक्रिया के बाद के सभी अनुरोधों पर प्रभाव डाल सकता है। इसलिए हमें हर अनुरोध के लिए भाषा स्पष्ट रूप से सेट करना चाहिए। उदाहरण के लिए निम्नलिखित मध्यवर्ती का उपयोग करें

`app/middleware/Lang.php` फ़ाइल बनाएँ (अगर निर्देशिका मौजूद नहीं है, तो कृपया खुद बनाएँ) जैसे कि:
```php
<?php
namespace app\middleware;

use Webman\MiddlewareInterface;
use Webman\Http\Response;
use Webman\Http\Request;

class Lang implements MiddlewareInterface
{
    public function process(Request $request, callable $handler) : Response
    {
        locale(session('lang', 'zh_CN'));
        return $handler($request);
    }
}
```

`config/middleware.php` में निम्नलिखित ग्लोबल मध्यवर्ती जोड़ें:
```php
return [
    // ग्लोबल मध्यवर्ती
    '' => [
        // ... अन्य मध्यवर्ती को यहां छोड़ें
        app\middleware\Lang::class,
    ]
];
```

## माध्यम सहित उपयोग करें
कभी-कभी, एक संदेश में अनुवाद की आवश्यकता होती है जिसमें अपेक्षाकृत भरपूर हो सकते हैं, जैसे
```php
trans('hello ' . $name);
```
इस तरह की स्थिति को हैंडल करने के लिए हम प्लेसहोल्डर का उपयोग करते हैं।

`resource/translations/zh_CN/messages.php` को निम्नलिखित रूप में बदलें:
```php
return [
    'hello' => 'नमस्ते %name%!',
```
अनुवाद के समय २वे पैरामीटर के माध्यम से प्लेसहोल्डर के मूल्य को पारित करें
```php
trans('hello', ['%name%' => 'webman']); // नमस्ते webman!
```

## बहुभूज सम्बोधन
कुछ भाषाएँ कारण से वस्तुओं की संख्या के कारण विभिन्न वाक्य रूपों में दिखाई देती हैं, जैसे `There is %count% apple`, जब `%count%` 1 होता है, तो वाक्य सही होता है, जब 1 से अधिक होता है, तो गलत होता है।

इस तरह की स्थिति का सामना करने के लिए हम संबोधन (|) का उपयोग करते हैं।

भाषा फ़ाइल `resource/translations/en/messages.php` में `apple_count` जोड़ें:
```php
return [
    // ...
    'apple_count' => 'There is one apple|There are %count% apples',
];
```

```php
trans('apple_count', ['%count%' => 10]); // There are 10 apples
```

हम आंतरिक रूप से संख्या सीमा निर्दिष्ट कर सकते हैं, और अधिक जटिल बहुभूज नियम बना सकते हैं:
```php
return [
    // ...
    'apple_count' => '{0} There are no apples|{1} There is one apple|]1,19] There are %count% apples|[20,Inf[ There are many apples'
];
```

```php
trans('apple_count', ['%count%' => 20]); // There are many apples
```

## निर्दिष्ट भाषा फ़ाइल

भाषा फ़ाइल का डिफ़ॉल्ट नाम `messages.php` है, वास्तव में आप अन्य नाम की भाषा फ़ाइल बना सकते हैं।

`resource/translations/zh_CN/admin.php` जैसे नाम से भाषा फ़ाइल बनाएँ:
```php
return [
    'hello_admin' => 'नमस्ते प्रशासक!',
];
```

`trans()` का तीसरा पैरामीटर (`.php` विस्तार को छोड़ते हुए) के माध्यम से भाषा फ़ाइल निर्दिष्ट करें।
```php
trans('hello', [], 'admin', 'zh_CN'); // नमस्ते प्रशासक!
```

## और जानकारी
[सिंफोनी/अनुवाद मैनुअल](https://symfony.com/doc/current/translation.html) को संदर्भित करें
