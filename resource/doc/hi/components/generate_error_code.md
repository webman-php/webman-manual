# त्रुटि कोड संयंत्र

## विवरण

निर्दिष्ट नियमों के आधार पर त्रुटि कोड की स्वचालित रूप से उत्पन्नरक्षण करने की क्षमता।

> प्रत्यक्ष पार्श्व डेटा में कोड पैरामीटर का समझौता। सभी स्वनिर्मित कोड , सकारात्मक संख्या सेवा कोड को दर्शाती है, नकारात्मक संख्या सेवा असामान्य कोड को दर्शाती है।

## प्रोजेक्ट पता

https://github.com/teamones-open/response-code-msg

## स्थापना

```php
composer require teamones/response-code-msg
```

## उपयोग

### खाली ErrorCode कक्ष फ़ाइल

- फ़ाइल पथ ./support/ErrorCode.php

```php
<?php
/**
 * स्वचालित रूप से उत्पन्न फ़ाइल ,कृपया मैन्युअल रूप से संशोधन न करें।
 * @Author:$Id$
 */
namespace support;

class ErrorCode
{
}
```

### कॉन्फ़िग फ़ाइल

त्रुटि कोड स्वचालित रूप से नीचे निर्धारित पैरामीटर के अनुसार बढ़ता है, उदाहरण के लिए वर्तमान प्रणाली_संख्या = 201,प्रारंभ_कम_संख्या = 10000,तो तब पहला त्रुटि कोड -20110001 होगा।

- फ़ाइल पथ ./config/error_code.php

```php
<?php

return [
    "class" => new \support\ErrorCode(), // ErrorCode कक्ष फ़ाइल
    "root_path" => app_path(), // वर्तमान कोड रूट डायरेक्टरी
    "system_number" => 201, // सिस्टम पहचान
    "start_min_number" => 10000 // त्रुटि कोड उत्पादन सीमा उदाहरण के लिए 10000-99999
];
```

### start.php में स्वचालित रूप से त्रुटि कोड उत्पन्न करने के लिए शुरूयात कोड जोड़ें

- फ़ाइल पथ ./start.php

```php
// Config::load(config_path(), ['route', 'container']); के पश्चात् रखें

// त्रुटि कोड उत्पन्न, केवल APP_DEBUG मोड में उत्पन्न होगा
if (config("app.debug")) {
    $errorCodeConfig = config('error_code');
    (new \teamones\responseCodeMsg\Generate($errorCodeConfig))->run();
}
```

### कोड में उपयोग

नीचे दिए गए कोड में **ErrorCode::ModelAddOptionsError** त्रुटि कोड है, जिसमें **ModelAddOptionsError** का उपयोग करने के लिए उपयोक्तता के हिसाब से अपने आप कोड लिखना होता है।

> आप लिखने के बाद देखेंगे कि आप इसे उपयोग नहीं कर सकते, पुनः शुरू करने के बाद उसका उत्पन्न होगा। ध्यान दें कभी-कभी दो बार पुनरारंभ करने की आवश्यकता हो सकती है।

```php
<?php
/**
 * नेविगेशन संबंधित कार्य सेवा कक्ष
 */

namespace app\service;

use app\model\Demo as DemoModel;

// ErrorCode कक्ष फ़ाइल शामिल करें
use support\ErrorCode;

class Demo
{
    /**
     * जोड़ना
     * @param $data
     * @return array|mixed
     * @throws \exception
     */
    public function add($data): array
    {
        try {
            $demo = new DemoModel();
            foreach ($data as $key => $value) {
                $demo->$key = $value;
            }

            $demo->save();

            return $demo->getData();
        } catch (\Throwable $e) {
            // त्रुटि संदेश प्रिंट करें
            throw_http_exception($e->getMessage(), ErrorCode::ModelAddOptionsError);
        }
        return [];
    }
}
```

### उत्पन्न की गई ./support/ErrorCode.php फ़ाइल 

```php
<?php
/**
 * स्वचालित रूप से उत्पन्न फ़ाइल, कृपया मैन्युअल रूप से संशोधन न करें।
 * @Author:$Id$
 */
namespace support;

class ErrorCode
{
    const LoginNameOrPasswordError = -20110001;
    const UserNotExist = -20110002;
    const TokenNotExist = -20110003;
    const InvalidToken = -20110004;
    const ExpireToken = -20110005;
    const WrongToken = -20110006;
    const ClientIpNotEqual = -20110007;
    const TokenRecordNotFound = -20110008;
    const ModelAddUserError = -20110009;
    const NoInfoToModify = -20110010;
    const OnlyAdminPasswordCanBeModified = -20110011;
    const AdminAccountCannotBeDeleted = -20110012;
    const DbNotExist = -20110013;
    const ModelAddOptionsError = -20110014;
    const UnableToDeleteSystemConfig = -20110015;
    const ConfigParamKeyRequired = -20110016;
    const ExpiryCanNotGreaterThan7days = -20110017;
    const GetPresignedPutObjectUrlError = -20110018;
    const ObjectStorageConfigNotExist = -20110019;
    const UpdateNavIndexSortError = -20110020;
    const TagNameAttNotExist = -20110021;
    const ModelUpdateOptionsError = -20110022;
}
```
