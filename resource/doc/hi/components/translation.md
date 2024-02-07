# बहुभाषा

बहुभाषा का उपयोग [symfony/translation](https://github.com/symfony/translation) संघ का उपयोग करता है।

## स्थापना
```composer require symfony/translation```

## भाषा पैक बनाएं
webman डिफ़ॉल्ट रूप से भाषा पैक को `resource/translations` निचे रखेगा (यदि नहीं है तो कृपया खुद बनाएं)। यदि फ़ोल्डर को बदलना है, तो `config/translation.php` में सेट करें।
प्रत्येक भाषा के लिए, इसमें एक सब-फ़ोल्डर होता है, और भाषा की परिभाषा डिफ़ॉल्ट रूप से `messages.php` में रखी जाती है। नीचे दी गई उदाहरण है:
```resource/
└── translations
    ├── en
    │   └── messages.php
    └── zh_CN
        └── messages.php
```

सभी भाषा फ़ाइलें सामग्री के रूप में एक ऐरे लौटाती हैं, उदाहरण:
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
    // फॉलबैक भाषा, वर्तमान भाषा में अनुवाद नहीं मिला हो तो वापस भाषा द्वारा कोशिश की जाएगी
    'fallback_locale' => ['zh_CN', 'en'],
    // भाषा फ़ाइलें रखने का फ़ोल्डर
    'path' => base_path() . '/resource/translations',
];
```

## अनुवाद

अनुवाद `trans()` मेथड का उपयोग करता है।

भाषा फ़ाइल बनाएं `resource/translations/zh_CN/messages.php` की तरह:
```php
return [
    'hello' => 'नमस्ते वेबमैन!',
];
```

फ़ाइल बनाएं `app/controller/UserController.php`
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

`http://127.0.0.1:8787/user/get` पर जाएँगे "नमस्ते वेबमैन!" वापस मिलेगा।

## डिफ़ॉल्ट भाषा बदलें

भाषा बदलने के लिए `locale()` मेथड का उपयोग करें।

नयी भाषा फ़ाइल बनाएं `resource/translations/en/messages.php` की तरह:
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
`http://127.0.0.1:8787/user/get` पर जाएँगे "hello world!" वापस मिलेगा।

आप `trans()` फ़ंक्शन के चौथे पैरामीटर का उपयोग करके भी अस्थायी रूप से भाषा बदल सकते हैं, उपरोक्त उदाहरण और नीचे दिया गया उदाहरण समान है:
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

## प्रत्येक अनुरोध के लिए भाषा प्राथमिकता निर्धारण करें
translation एकल है, जिसका अर्थ है कि सभी अनुरोध इस इंस्टेंस को साझा करते हैं, यदि किसी अनुरोध ने `locale()` द्वारा डिफ़ॉल्ट भाषा को सेट किया है, तो यह प्रक्रिया के बाद के सभी अनुरोधों पर प्रभाव डालेगा। इसलिए हमें प्रत्येक अनुरोध के लिए भाषा को स्पष्ट रूप से सेट करना चाहिए। उदाहरण के लिए निम्नलिखित मध्यवर्ती का उपयोग करें

फ़ाइल बनाएं `app/middleware/Lang.php` (यदि नहीं है तो कृपया खुद बनाएं) जैसा कि नीचे दिखाया गया है:
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

`config/middleware.php` में ग्लोबल मध्यवर्ती को निम्नलिखित रूप में जोड़ें:
```php
return [
    // ग्लोबल मध्यवर्ती
    '' => [
        // ... अन्य मध्यवर्तियों को यहाँ छोड़ दिया गया है
        app\middleware\Lang::class,
    ]
];
```

## प्लेसहोल्डर का उपयोग करें
कभी-कभी, एक संदेश में अनुवाद की आवश्यकता होती है, जैसे कि
```php
trans('hello ' . $name);
```
जैसी स्थितियों का सामना करते समय, हम प्लेसहोल्डर का उपयोग करते हैं।

`resource/translations/zh_CN/messages.php` को नीचे दी गई तरह से बदलें:
```php
return [
    'hello' => 'नमस्ते %name%!',
```
अनुवाद के समय, डेटा को दूसरे पैरामीटर के माध्यम से प्लेसहोल्डर के मान द्वारा पारित करें
```php
trans('hello', ['%name%' => 'वेबमैन']); // नमस्ते वेबमैन!
```


## बहुसंख्या का प्रसंस्करण
कुछ भाषाएँ संख्या के कारण विभिन्न वाक्य रचना को प्रदर्शित करती हैं, जैसे कि `There is %count% apple`, जब `%count%` 1 होता है तो वाक्य-रचना सही होती है, 1 से अधिक होने पर गलत।

इस तरह की स्थिति का सामना करते समय हम विशेष रूप से प्रसंख्या रूप का उपयोग करते हैं।

भाषा फ़ाइल `resource/translations/en/messages.php` में `apple_count` को नीचे दी गई तरह से जोड़ें:
```php
return [
    // ...
    'apple_count' => 'There is one apple|There are %count% apples',
```

```php
trans('apple_count', ['%count%' => 10]); // There are 10 apples
```

हम यहाँ तक कि नंबर रेंज द्वारा घोषित भी कर सकते हैं, और अधिक संख्या के लिए एक कंप्लेक्स प्रसंख्या नियम बना सकते हैं:
```php
return [
    // ...
    'apple_count' => '{0} There are no apples|{1} There is one apple|]1,19] There are %count% apples|[20,Inf[ There are many apples'
];
```

```php
trans('apple_count', ['%count%' => 20]); // There are many apples
```

## निर्दिष्ट भाषा फ़ाइल का उपयोग

भाषा फ़ाइल का डिफ़ॉल्ट नाम `messages.php` है, वास्तव में आप अन्य नाम की भाषा फ़ाइल बना सकते हैं।
`resource/translations/zh_CN/admin.php` की तरह, भाषा फ़ाइल बनाएं:
```php
return [
    'hello_admin' => 'नमस्ते प्रशासक!',
];
```

`trans()` के तीसरे पैरामीटर का उपयोग करके भाषा फ़ाइल को स्पष्ट करें (`.php` सफ़ेद करें)।
```php
trans('hello', [], 'admin', 'zh_CN'); // नमस्ते प्रशासक!
```

## अधिक जानकारी
संदर्भ [symfony/translation मैनुअल](https://symfony.com/doc/current/translation.html)
