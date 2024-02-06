# असंतोष की स्थिति का प्रबंधन

## संरूपण
`config/exception.php`
```php
return [
    // यहां अपशिष्ट प्रसंस्करण क्लास को संरूपित करें
    '' => support\exception\Handler::class,
];
```
बहु-एप्लिकेशन मोड में, आप हर एप्लिकेशन के लिए विशिष्ट रूप से असंतोष प्रसंस्करण क्लास की विन्‍यासित कर सकते हैं, कृपया [मल्टीएप्लिकेशन](multiapp.md) देखें

## डिफ़ॉल्ट असंतोष प्रबंधन कक्षा
वेबमैन में असंतोष डिफ़ॉल्ट रूप से `support\exception\Handler` कक्षा द्वारा प्रबंधित किया जाता है। डिफ़ॉल्ट असंतोष प्रसंस्करण कक्षा को बदलने के लिए कॉन्फ़िग फ़ाइल `config/exception.php` को संशोधित किया जा सकता है। असंतोष प्रसंस्करण कक्षा को `Webman\Exception\ExceptionHandlerInterface` इंटरफेस के अनुसार कार्यान्वित किया जाना चाहिए।
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
     * प्रस्तुति का संवाद
     * @param Request $request
     * @param Throwable $e
     * @return Response
     */
    public function render(Request $request, Throwable $e): Response;
}
```

## प्रतिक्रिया का संवाद
असंतोष प्रसंस्करण कक्षा में `render` विधि का उपयोग प्रतिक्रिया का संवाद करने के लिए किया जाता है।

यदि कॉन्फ़िग फ़ाइल `config/app.php` में `debug` मान `true` है (निम्नलिखित से `app.debug=true`), तो विस्तृत असंतोष सूचनाएँ वापस की जाएगी, अन्यथा संक्षिप्त असंतोष सूचनाएँ वापस की जाएगी।

यदि अनुरोध को जेसोन वापसी की आशा है, तो वापसी असंतोष सूचना जेसोन प्रारूप में की जाएगी, जैसे
```json
{
    "code": "500",
    "msg": "असंतोष सूचना"
}
```
यदि `app.debug=true` है, तो जेसन डेटा में एक अतिरिक्त `ट्रेस` फ़ील्ड विस्तृत पुनः कॉल स्टैक जोड़ता है।

आप अपनी असंतोष प्रसंस्करण कक्षा लिखकर डिफ़ॉल्ट असंतोष प्रसंस्करण तर्क को बदल सकते हैं।

# व्यावसायिक असंतोष व्यवसाय असंतोष
कभी-कभी हमें किसी संदर्भ में अनुरोध को रोकना और ग्राहक को एक त्रुटि संदेश वापस भेजना चाहिए, इस स्थिति में इस काम को करने के लिए `BusinessException` को फेंकना संभव है।
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

उपर्युक्त उदाहरण में निम्नलिखित को वापस करेगा
```json
{"code": 3000, "msg": "पैरामीटर त्रुटि"}
```

> **नोट**
> व्यावसायिक असंतोष BusinessException को प्रयोग करने के लिए बिजनेस ट्राई कैच आवश्यक नहीं है, फ्रेमवर्क स्वचालित रूप से ट्राई को आवश्यक नहीं मानता है और अनुरोध प्रकार के अनुसार उचित प्रोडल वापसियां करता है।

## व्यक्तिगत व्यावसायिक असंतोष
यदि उपर्युक्त प्रतिक्रिया आपकी आवश्यकताओं से मेल नहीं खाती है, उदाहरणार्थ, आप 'msg' को 'message' में बदलना चाहते हैं, तो आप एक `MyBusinessException` स्वयं निर्मित कर सकते हैं

`app/exception/MyBusinessException.php` नया बनाएं जिसमें निम्नलिखित का संदर्भ हो
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
        // जेसोन अनुरोध जेसोन डेटा को वापस करता है
        if ($request->expectsJson()) {
            return json(['code' => $this->getCode() ?: 500, 'message' => $this->getMessage()]);
        }
        // गैर-जेसन अनुरोध वापसें केवल एक पृष्ठ को वापस करता है
        return new Response(200, [], $this->getMessage());
    }
}
```

इस प्रकार जब व्यावसायिक योगांक
```php
use app\exception\MyBusinessException;

throw new MyBusinessException('पैरामीटर त्रुटि', 3000);
```
जेसन अनुरोध एक जैसा नीचे दिखाई दे गा
```json
{"code": 3000, "message": "पैरामीटर त्रुटि"}
```

> **सुचना**
> क्योंकि BusinessException असंतोष व्यावसायिक होता है (उदाहरण के लिए उपयोगकर्ता इनपुट पैरामीटर गलत है), इसे पूर्वानुमानित किया जा सकता है, इसलिए फ्रेमवर्क इसे घातक त्रुटि नहीं समझता है और इसे लॉग नहीं करेगा।

## संक्षेप
किसी भी समय वर्तमान अनुरोध को रोकने और ग्राहक को जानकारी वापस करने के लिए `BusinessException` का उपयोग करना विचार करें।
