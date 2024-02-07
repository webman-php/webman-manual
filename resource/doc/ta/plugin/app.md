# விண்ணப்ப் பிளகின்கள்
ஒவ்வொரு பயன்பாடு கூடுதல் பயன்பாடு ஒரு முழு பயன்னர், மூலக் குறியீடு வைத்து`{முதுமை உருப்படி}/plugin` அடைவில் வைத்துக்கொண்டது

> **முற்படுத்தி**
> கட்டமை`php webman app-plugin:create {பிளகின் பெயர்}` (webman/console>=1.2.16 தேவை) ஒரு ஆப்பில் பிளகின் உருவாக்கவும், 
> உதாரணம் `php webman app-plugin:create cms` பயன்படுத்தி வேறு அடைவிக்கு பிரகாரமாக உருவாக்கினால் கீழே உள்ள அடைவு மேலாளம் உருவாக்கப்படும்

```  
plugin/
└── cms
    ├── app
    │   ├── controller
    │   │   └── IndexController.php
    │   ├── exception
    │   │   └── Handler.php
    │   ├── functions.php
    │   ├── middleware
    │   ├── model
    │   └── view
    │       └── index
    │           └── index.html
    ├── config
    │   ├── app.php
    │   ├── autoload.php
    │   ├── container.php
    │   ├── database.php
    │   ├── exception.php
    │   ├── log.php
    │   ├── middleware.php
    │   ├── process.php
    │   ├── redis.php
    │   ├── route.php
    │   ├── static.php
    │   ├── thinkorm.php
    │   ├── translation.php
    │   └── view.php
    └── public
```

நாங்கள் ஒரு விண்ணப்ப் பிளகின் ஒருவரையும் ஒரு ப்ளகின் ஆப் பிளகின் முதலான முனை அடைவு மற்றும் கட்டமை கோப்புகளைக் காணி. உடனடியாக, ஒரு பிளகின் பயன்பாடு, ஒரு வெப்மான் திட்டத்தைக் கட்டமை பெறுவதை ஒரு விண்ணப்புக்கு தெரிந்து கொள்ள வேண்டும், உறுதிசெய்கின்ற பாதைக்கு உதவுவதை எவ்வாறு கவனியாக மேல் கருதி

## பெயர் வீசிற்கு 
பிளகின் அடைவு, பெயர் உஷ்ணத்தை அனுஸரிக்கிறதுடையாம் PSR4 கொள்கை, படுக்கையாக பிளகின் எல்லா ஒட்டிகள் அடைவு பெயர் ஆரம்பிக்கின்றன, உதாரணமாக`plugin\cms\app\controller\UserController` இங்கு cms பிளகின் மூல அடைவு.

## url அணுக்கம்
விண்ணப்ப் பிளகின் url முகவரி பாதை `முதலீடு/பயன்பாடு` ஆனது, உதாரணமாக `plugin\cms\app\controller\UserController`url முகவரி `http://127.0.0.1:8787/app/cms/user` ஆகும்.

## நிலையான கோண்ட்ரோல்கள்
நிலையான கோண்ட்ரோல்கள் `plugin/{பிளகின்}/public` அடைவில் வைத்துக்கொள்ளப்பட்டுள்ளது, உதாரணமாக `http://127.0.0.1:8787/app/cms/avatar.png` ஊடகம் `plugin/cms/public/avatar.png` கோப்பைப் பெறுகிறது.

## உருவாக்கல் கோப்பு 
பிளகின் உருவாக்கல் சாத்தியமான பயனளவிற்காக மற்றும் முகவரி பிராஜெக்ட்களுக்கு கேடுவான அவசியம், பிளகின் உருவாக்குவது `config('plugin.{பிளகின்}.{செ஡்யுரி_முகவரி}');` என்பது இருக்க முடியும், உதாரணமாக `config('plugin/cms/config/app.php')` ஒவ்வொரு அமைப்பையும் `config('plugin.cms.app')` என்பதைப் பெறும்.

## ஆதரவு கோப்புகள்
விண்ணப்ப் பிளகினின் ஆதரவு பொருள்களும் சாத்தியம் உடனடியாக அமைவின் ஆதரவு பொருள்கள் ஒருவேளையும் இல்லை, ஆனால் ஆவணக் கோப்புகள் பொருள் பொருள் பொருள்.

## தரவுத்தளம்
பிளகின் தரவுத்தளம் தன்னியக்க அமைக்கப் பெறும், உதாரணமாக`plugin/cms/config/database.php` உள்ளகடக்கம்

```php
return  [
    'default' => 'mysql',
    'connections' => [
        'mysql' => [ // mysql எனும் இணைப்பு பெயர்
            'driver'      => 'mysql',
            'host'        => '127.0.0.1',
            'port'        => 3306,
            'database'    => 'தரவுதளம்',
            'username'    => 'பயனாளர் பெயர்',
            'password'    => 'கடவுச்சொல்',
            'charset'     => 'utf8mb4',
            'collation'   => 'utf8mb4_general_ci',
        ],
        'admin' => [ // நிர்வாகி எனும் இணைப்பு பெயர்
            'driver'      => 'mysql',
            'host'        => '127.0.0.1',
            'port'        => 3306,
            'database'    => 'தரவுதளம்',
            'username'    => 'பயனாளர் பெயர்',
            'password'    => 'கடவுச்சொல்',
            'charset'     => 'utf8mb4',
            'collation'   => 'utf8mb4_general_ci',
        ],
    ],
];
```

மேலெழுந்த பெயர் `Db::connection('plugin.{பிளகின்}.{இணைப்பு பெயர்}');` உபயோகிக்கப்படுகின்றது, உதாரணமாக
```php
use support\Db;
Db::connection('plugin.cms.mysql')->table('user')->first();
Db::connection('plugin.cms.admin')->table('admin')->first();
```

முதல் திட்டத்தில் உங்கள் தரவுத்தளத்துடன் உபயோகிக்க விரும்பும் அனைத்து முழுவாய்ந்த,
```php
use support\Db;
Db::table('user')->first();
// பிக்கப் திட்டத்தில் admin இணைப்புவாக அமைத்தால்
Db::connection('admin')->table('admin')->first();
```

> **முற்படுத்தி**
> thinkorm அடுக்குபயன்பாடு போல் அதே பயன்பாடு.


## ரெடிஸ்
தரவுத்தளம் பயன்பாடு உற்சாகப்பட வரை, உதாரணமாக `plugin/cms/config/redis.php`
```php
return [
    'default' => [
        'host' => '127.0.0.1',
        'password' => null,
        'port' => 6379,
        'database' => 0,
    ],
    'cache' => [
        'host' => '127.0.0.1',
        'password' => null,
        'port' => 6379,
        'database' => 1,
    ],
];
```
பயன்படுத்துவது
```php
use support\Redis;
Redis::connection('plugin.cms.default')->get('key');
Redis::connection('plugin.cms.cache')->get('key');
```

அதை ஒருபுதியவர்கள் இணைப்புகளை பயன்படுத்த விரும்பினால்
```php
use support\Redis;
Redis::get('key');
// முகவரி திரும்ப   ஒவ்வொரு பிளகினுக்கு cache இணைப்பு அமைத்தால்
Redis::connection('cache')->get('key');
```

## பதிவுப்படுத்து
பதிவுப்படுத்துவது கட்டமை ஆகவேண்டியது. பிளகினை சரி பார்த்து உதாரணமாக தாக்கு அல்லது ரீஸ்டா என பெயர் இடமிருந்து :`{முதுமை உருப்படி}/plugin` அடைவில் அழைக்கவும். உறுதி செய்யவும் அல்லது மீளார்க மேலாணம் மோடரி சேர்க்கவும்
காட்டுக்காக

ை.
