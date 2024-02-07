# असामान्यता प्रसंस्करण

## विन्यास
`config/exception.php`

```php
return [
    // यहां असामान्यता प्रसंस्करण कक्ष को विन्यसित करें
    '' => support\exception\Handler::class,
];
```

एकाधिक एप्लीकेशन मोड में, आप प्रत्येक एप्लीकेशन के लिए असामान्यता प्रसंस्करण कक्ष को विन्यसित कर सकते हैं, देखें [मल्टीऐप (multiapp.md)](multiapp.md)

## डिफ़ॉल्ट असामान्यता प्रसंस्करण कक्ष
वेबमैन में असामान्यता डिफ़ॉल्ट रूप से `support\exception\Handler` कक्ष द्वारा प्रसंस्कृत की जाती है। डिफ़ॉल्ट असामान्यता प्रसंस्करण कक्ष को बदलने के लिए विन्यास फ़ाइल `config/exception.php` को संशोधित करें। असामान्यता प्रसंस्करण कक्ष को विन्यास को करने के लिए असामान्यता प्रसंस्कृत कक्ष नियंत्रित करना आवश्यक है `Webman\Exception\ExceptionHandlerInterface` इंटरफ़ेस को अमल करना।

```php
interface ExceptionHandlerInterface
{
    /**
     * लॉग दर्ज करें
     * @param Throwable $e
     * @return mixed
     */
    public function report(Throwable $e);

    /**
     * प्रस्तुत करना
     * @param Request $request
     * @param Throwable $e
     * @return Response
     */
    public function render(Request $request, Throwable $e) : Response;
}
```

## प्रतिक्रिया चित्रण
असामान्यता प्रसंस्कृत कक्ष में `रेंडर` विधि का उपयोग प्रतिक्रिया चित्रण के लिए किया जाता है।

यदि विन्यास फ़ाइल `config/app.php` में `debug` मान `true` है (निम्नलिखित में `app.debug=true`), तो थोक असामान्यता सूचनाओं को लौटाने के लिए विस्तृत रूप से विस्तारित होगा, अन्यथा संक्षिप्त असामान्यता सूचनाएं लौटाई जाएगी।

यदि अनुरोध जेसन लौटने की उम्मीद है, तो असंचालित असामान्यता सूचनाएँ जेसन प्रारूप में लौटाई जाएगी, जैसे
```json
{
    "code": "500",
    "msg": "असामान्यता सूचना"
}
```

यदि `app.debug=true` है, तो जेसन डेटा में विस्तारित कॉल स्टैक को लौटाने के लिए, जेसन में एक अतिरिक्त `ट्रेस` फ़ील्ड वृद्धि होगी।

आप अपने खुद के असामान्यता प्रसंस्करण कक्ष को लिखकर डिफ़ॉल्ट असामान्यता प्रसंस्करण तर्तीब बदल सकते हैं।

# व्यावसायिक असामान्यता व्यावसायिक असामान्यता
कभी कभी हमे किसी नेस्टेड फ़ंक्शन में अनुरोध को रद्द करना और ग्राहक को एक त्रुटि सूचना लौटाना चाहिए, इस समय इसे प्राप्त करने के लिए `BusinessException` को फेंककर एक स्थिति कर सकते हैं।
उदाहरण के लिए:

```php
<?php
namespace app\controller;

use support\Request;
use support\exception\BusinessException;

class FooController
{
    public function index(Request $request)
    {
        $this->chackInpout($request->post());
        return response('hello index');
    }
    
    protected function chackInpout($input)
    {
        if (!isset($input['token'])) {
            throw new BusinessException('पैरामीटर त्रुटि', 3000);
        }
    }
}
```

उपरोक्त उदाहरण में एक त्रुटि लौटाएगा
```json
{"code": 3000, "msg": "पैरामीटर त्रुटि"}
```

> **ध्यान दें**
> व्यावसायिक असामान्यता BusinessException को कोई कारोबार try नहीं पकड़ना होता, परिदेश आपूर्ति प्रकार के अनुसार समायोजित आउटपुट वापस किया जाएगा।

## स्वनिर्धारित व्यावसायिक असामान्यता

यदि उपरोक्त प्रतिक्रिया आपकी आवश्यकताओं को पूरा नहीं करती है, जैसे सूचना को `msg` को `message` में बदलना चाहते हैं, तो आप एक `MyBusinessException` स्वनिर्धारित कर सकते हैं


`app/exception/MyBusinessException.php` नमक एक नया फ़ाइल बनाएँ
```php
<?php

namespace app\exception;

use support\exception\BusinessException;
use Webman\Http\Request;
use Webman\Http\Response;

class MyBusinessException extends BusinessException
{
    public function render(Request $request): ?Response
    {
        // जेसन अनुरोध जेसन डेटा को वापस करता है
        if ($request->expectsJson()) {
            return json(['code' => $this->getCode() ?: 500, 'message' => $this->getMessage()]);
        }
        // गैर-जेसन अनुरोध तो एक पृष्ठ वापिस देगा
        return new Response(200, [], $this->getMessage());
    }
}
```

इस तरह, जब व्यावसायिक `throw new MyBusinessException('पैरामीटर त्रुटि', 3000);` को कॉल किया जाए, तो जेसन अनुरोध स्वरूप में निम्नलिखित जेसन को प्राप्त होगा
```json
{"code": 3000, "message": "पैरामीटर त्रुटि"}
```

> **सूचना**
> क्योंकि BusinessException असामान्यता व्यावसायिक असामान्यता के अंतर्गत आती है (उदाहरण के लिए उपयोगकर्ता इनपुट पैरामीटर त्रुटि), इसलिए यह पूर्वीनिर्धारित त्रुटि के रूप में नहीं लिया जाएगा और नहीं रिकॉर्ड किया जाएगा।

## संक्षेप
किसी भी समय जब वर्तमान अनुरोध को रोकना और ग्राहक को प्रतिक्रिया देने के लिए विचार किया जा सकता है, तो विचार से `BusinessException` असामान्यता का उपयोग करना उपयुक्त है।
