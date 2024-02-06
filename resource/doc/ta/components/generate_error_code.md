# பிழை குறியீடு கோம்பொனென்ட்

## விளக்கம்

கொடுக்கப்பட்ட விதிகள் அடிப்படையில் பிழை குறியீடுகளை தானாகவே உருவாக்க மாற்றமைப்பதாகும்.

> அல்லதுண் கனி அளவு குறியீடுகள் சரியான சேவையைக் குறிக்கின்றன.

## திட்ட முகவரி

https://github.com/teamones-open/response-code-msg

## நிறுவுக

```php
composer require teamones/response-code-msg
```

## பயன்பாடு

### காலிய் பிழை குறியீடு முறைபாடு

- கோப்பு பாதை ./support/ErrorCode.php

```php
<?php
/**
 * தானாக உருவாக்கப்பட்ட கோப்பு , தயவுசெய்து கைமாறாதே.
 * @Author:$Id$
 */
namespace support;

class ErrorCode
{
}
```

### உள்ளடக்க கோப்பு

பிழை குறியீடு பாரம்பரியமாக அளவிடங்கள் அடங்கலாம், உதாரணம் தற்போது system_number = 201, start_min_number = 10000, அப்பால் உருவாக்கும் முதல் பிழை குறியீடு -20110001 ஆகும்.

- கோப்பு பாதை ./config/error_code.php

```php
<?php

return [
    "class" => new \support\ErrorCode(), // பிழை குறியீடு வரிசை கோப்பு
    "root_path" => app_path(), // தற்போதைய குறுக்கு மூல இட்டவோல்
    "system_number" => 201, // அமைவு அடையாளம்
    "start_min_number" => 10000 // பிழை குறியீடு உருவாக்கும் வெளிப்புற விவரங்கள்  உதாரணம் 10000-99999
];
```

### start.php இல் தங்கேமது பிழை குறியீடு குறிக்குவதற்காக ஆரம்பிக்க

- கோப்பு பாதை ./start.php

```php
// உடனடி Config::load(config_path(), ['route', 'container']); பின் இடப்பில் வை

// பிழை குறியீடு உருவாக்கவும், இதனிடம் மடிப்பாக் முலமை மோட் கஃபிக கஃபிக்(பதிவு)
if (config("app.debug")) {
    $errorCodeConfig = config('error_code');
    (new \teamones\responseCodeMsg\Generate($errorCodeConfig))->run();
}
```

### கோடியினப் பயன்பாட்டில் பயன்பாடு

கீழே குறியீடில் **ErrorCode::ModelAddOptionsError** பிழை குறியீடு, **ModelAddOptionsError** அதே பயனர் தனது விரைவில் அவசியமாக எழுத வேண்டும் வரை வழக்கவமையம் மேலாண்மை உனத்திற்கு எழுதலாம்.

> நீங்கள் பூர்ண வழக்கமாக உள்ளதை காண்ஞ்சிக்கலாம், பிழை வரிசையெழுதுவுடவேற்கின்றன, அப்போது இறக்கை பிழை உருவாக்கக்கு உள்ளியலாம். கெத்தாக அமைந்திலும் இறக்கை இரண்டு முறை மீண்டும் த்துவும்.

```php
<?php
/**
 * வழக்கம் தொடர்பு சேவை வகை
 */

namespace app\service;

use app\model\Demo as DemoModel;

// ErrorCode வகை கோப்பு விழு
use support\ErrorCode;

class Demo
{
    /**
     * சேர்
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
            // வழக்கம் தகவலை வெள் நிகரல்
            throw_http_exception($e->getMessage(), ErrorCode::ModelAddOptionsError);
        }
        return [];
    }
}
```

### உருவாக்கப்பட்ட ./support/ErrorCode.php கோப்பு

```php
<?php
/**
 * தானாக உருவாக்கப்பட்ட கோப்பு , தயவுசெய்து கைமாறாதே.
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
