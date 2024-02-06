# பல மொழி

பல மொழி [மொழிக்கோப்பு](https://github.com/symfony/translation) பொருளாதாரத்தை பயன்படுத்துகிறது.

## நிறுவுக
```
composer require symfony/translation
```

## மொழி கோப்பு உருவாக்கு
webman இயல்புநிலையில் இந்த மொழிபெட்டியை `resource/translations` கோப்புகளின் கீழ் (இல் எதுவும் இல்லையெனில் உங்களதுக்கு உருவாக்கவும்) ஒவ்வொரு மொழிக்கும் துணைக்கூறு இதில் இருக்கவும். ஒவ்வொரு மொழியின் ஒரு குழப்பத்தை , மொழி வழங்கி இயல்புநிலையில் `messages.php` கொண்டிருக்கின்னார். ஒரு உதாரணமும் இதா:
```
resource/
└── translations
    ├── en
    │   └── messages.php
    └── zh_CN
        └── messages.php
```

அனைத்து மொழிக்கோப்பு கோப்புகளும் ஒரு வரையறுக்கும் பட்டியல் தருகின்னர் அதக்காலத்தில் :
```php
// resource/translations/en/messages.php

return [
    'hello' => 'Hello webman',
];
```

## அமைப்பு

`config/translation.php`

```php
return [
    // இயல்பான மொழி
    'locale' => 'zh_CN',
    // இந்த மொழிக்கு மொழிய கிடைக்காமல் இருந்தால் கிடைக்கவில்லை எனில் அப்ப மொழியில் கிடைக்கப்போவது மோதிக்கே பயன்படுத்துகிற
    'fallback_locale' => ['zh_CN', 'en'],
    // மொழி கோப்பு ஓட்டி உள்ள கோப்பு
    'path' => base_path() . '/resource/translations',
];
```

## மொழி மீள்கள்

மொழி `trans ()` முறையில் பயன்படுகிறது.

மொழி கோப்பு `resource/translations/zh_CN/messages.php` உரையாக்கப்படுதுகிறது:
```php
return [
    'hello' => 'வணக்கம் உலகம்!',
];
```

கோப்பு `app/controller/UserController.php` உரையாக்கப்படுதுகிறது
```php
<?php
namespace app\controller;

use support\Request;

class UserController
{
    public function get(Request $request)
    {
        $hello = trans('hello'); // வணக்கம் உலகம்!
        return response($hello);
    }
}
```

`http://127.0.0.1:8787/user/get` ஐ அணுவைக்கும் முடிவு செய்யும் "வணக்கம் உலகம்!"

## இயல்புநிலை மொழியை மாற்றம்

மொழி `locale ()` முறையில் பயன்படுத்தி.

மொழி கோப்பு `resource/translations/en/messages.php` உரையாக்கப்படுதுகிறது:
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
        // மொழி மாற்றம்
        locale('en');
        $hello = trans('hello'); // hello world!
        return response($hello);
    }
}
```
`http://127.0.0.1:8787/user/get` ஐ அணுவைக்கும் முடிவு செய்யும் "வணக்கம் உலகம்!"

இந்த உதாரணமும் நீங்கள் `trans()` சார் பயன்படுத்துத்தகும் நான்காவது அளவிற்கு மொழியை அஸ்ஸுராவுக்கு, உதாரணம்கு முன்னியோஐத்து உள்ள உதாரணமும் நீங்கள் பயன்படுத்துத்தே:
```php
<?php
namespace app\controller;

use support\Request;

class UserController
{
    public function get(Request $request)
    {
        // நான் மொழி மாற்றம்
        $hello = trans('hello', [], null, 'en'); // hello world!
        return response($hello);
    }
}
```

## ஒரு கோரிக்கைக்காகவும் மொழி அமைப்பு
translation ஒரு ஒற்றை இருளத் தனியுடன் பங்களிப்படுவதால் என்னமான இலக்க மேமேய் அமைப்பு பயன்படுத்துவதே அல்லாமல், உங்களுக்காக அவ்ருிடம் ஒரு இலட்டி மொழி அமைப்பை அமைக்கவும். எ.கா. பயன்படுத்தவும்

`app/middleware/Lang.php` எனும் கோப்பை உருவாக்குங்கள் (சூழ்நிலை இல்லையால் உங்களது தனி உருவாக்குங்கள்) உரையாக்கப்படுதுகிறது:
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

`config/middleware.php` சூழ்நிலையில் சர்வகலியக்கும் இட்டை உரையாக்கப்படுதுகிறது. 
```php
return [
    // சர்வகலியக்கும் வடிவம்
    '' => [
        // ... இங்கு மற்ற இடங்களை தேர்ந்தெடுத்து சூழப்படுத்துகிறது
        app\middleware\Lang::class,
    ]
];
```

## ஆவணக்கூறில் இடமகெயுண்டு
கொருக்கமா, ஒரு மொழி நிறைவாக `மொழிபெட்டு` என்ற மற்றகவுமுள்ள பெருநோக்கில் இடப்பகைகெண்டு .உரைநாது
```php
trans('hello' . $name);
```
இந்த நிலையில் ஃபலேஸ்ஹோட்ட்டு பயன்படுத்தமுடியது.

`resource/translations/zh_CN/messages.php` அமைக்க வேண்டிய முறை
```php
return [
    'hello' => 'வணக்கம் %name%!',
];
```
மொழி வழக்கில் வழக்கட ஒனையவயி மேல் முறைமு第ாடினி.எ 
```php
trans('hello', ['%name%' => 'webman']); // வணக்கம் webman!
```

## அளவுன்சீர்தறினுமன்னுசீர்தற்=பயன் கூறது
அண்டவ், `வபினம்  நம்வ்ன்சாட்டு` அங்துகிடைக்கவேஸ்ம்பளன்குப்ண் ஓவ் கூற்றுள்எ பயன்படுத்துபாரும்.

சொல் கோப்பு `resource/translations/en/messages.php`  கீலெயுரைக்கிஷதாது:
```php
return [
    // ...
    'apple_count' => 'ஒற்றைசொல்| அதால் உள்ளடநும்ட்டௌு பயன்படுத்த கூறுகளுக்காக பயன்படுத்துலா!
];
```

```php
trans('apple_count', ['%count%' => 10]); // அதால் உள்ளடநும்ட்டௌு பயன்படுத்த கூறுகளுக்காக பயன்படுத்துலா!
```

நவுறான் பஊவள் வங்வளவஅீள்வ்ளுறவளவபட்பாடுப் பயன்படுத்து பயன்படுத்துபாரும்:
```php
return [
    // ...
    'apple_count' => '{0} ஒற்றைசொல்| {1} அதால் உள்ளடநும்ட்டௌு  பயன்படுத்த இந்த தொுறுவவ்ளு.|]1,19] %{count}% மேலா பட்பாடுகாலெய் மாக்கவளம்<[20,Inf[ மகி வ்லாகளுக்காக பயபட்த்துலா. ரூரக்கு
];
```

```php
trans('apple_count', ['%count%' => 20]); // மகி வ்லாகளுக்காக பயபட்த்துலா.
```

## குவகுத்தள்புபு மாப்பாளபயன் கூறுபடுது
மொழி கோப்பு இருவேய் சில்வே ஆகினை`்மெச்சகேச்சகேல்ய்கிடைக்கப்பளம் உரை 'messages.php' 

மொழி கோப
