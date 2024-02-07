# Casbin அணுக்குமுறை லிப்ரரி webman-permission

## விளக்கம்

இது [PHP-Casbin](https://github.com/php-casbin/php-casbin) சொல்லுபடி அதிகப்படியாக மற்றும் வெற்றுபெற்ற ஒதுக்கீடு கட்டமைக்கப்பட்ட ஒலிக்கானவை ஆகும். அது `ACL`, `RBAC`, `ABAC` போன்ற அணுக்குமுறை மாதிரிகள் தொகுதிகள் காணக்கூடியவைகளை ஆதரிக்கின்றது.

## பிராரம்ப இடம்

https://github.com/Tinywan/webman-permission

## நிறுவு

```php
composer require tinywan/webman-permission
```
> இந்த விரிவான PHP 7.1+ மற்றும் [ThinkORM](https://www.kancloud.cn/manual/think-orm/1257998) ஐக்கியமாக வேண்டும். அதனுடைய அதிகாரிகம்: https://www.workerman.net/doc/webman#/db/others

## அமைப்பது

### பணியாளரை பதிவு செய்
`config/bootstrap.php` புதிய உள்ளடக்கத்தை உருவாக்குக, உதாரணமாக கீழே உள்ளடக்கம் போல்:

```php
    // ...
    webman\permission\Permission::class,
```

### மாதிரி உள்ளடக்கம் கோப்பு

`config/casbin-basic-model.conf` புதிய உள்ளடக்க கோப்பை உருவாக்குக, உதாரணமாக கீழே உள்ளடக்கம் போல்:

```conf
[request_definition]
r = sub, obj, act

[policy_definition]
p = sub, obj, act

[role_definition]
g = _, _

[policy_effect]
e = some(where (p.eft == allow))

[matchers]
m = g(r.sub, p.sub) && r.obj == p.obj && r.act == p.act
```

### அனுமதி உள்ளடக்கம் கோப்பு

`config/permission.php` புதிய உள்ளடக்கம் கோப்பை உருவாக்குக, உதாரணமாக கீழே உள்ளடக்க போல்:

```php
<?php

return [
    /*
     *இயல்பு அனுமதி
     */
    'default' => 'basic',

    'பதிவு' => [
        'செயல்பாட்டில்' => false,
        'பதிவினர்' => 'பதிவு',
    ],

    'enforcers' => [
        'basic' => [
            /*
            * மாதிரி அமைப்பு
            */
            'model' => [
                'config_type' => 'file',
                'config_file_path' => config_path() . '/casbin-basic-model.conf',
                'config_text' => '',
            ],

            // அமைப்பாக்கை உள்ளடக்குதல்
            'adapter' => webman\permission\adapter\DatabaseAdapter::class,

            /*
            * தரவுத்தள அமைத்தல்
            */
            'database' => [
                // தரவுத்தள இணையத்தன்மை, இல்லாமல் இருந்தால் இயல்பு அமைப்புஆகும்.
                'connection' => '',
                // கொடுப்பள்ளி அடைவின் பெயர் (முன் தலைவரை முட்டாள்க்கும்)
                'rules_name' => 'rule',
                // கொடுப்பள்ளி அடைவின் முழு பெயர்.
                'rules_table' => 'train_rule',
            ],
        ],
    ],
];
```

## வேகமாக தொடங்க

```php
பயனருக்கு அனுமதிகளை சேர்க்கின்றேன்
Permission::addPermissionForUser('eve', 'articles', 'read');
// பயனருக்கு ஒரு பங்கு சேர்க்கிறேன்.
Permission::addRoleForUser('eve', 'writer');
// rule க்கு அனுமதிகளை சேர்க்கிறேன்
Permission::addPolicy('writer', 'articles','edit');
```

நீங்கள் பயனர் க்கு இந்த வகையில் அனுமதியை வைக்க முடியும்

```php
if (Permission::enforce("eve", "articles", "edit")) {
    // முடக்கு அனுமதிக்கு அனுமதி கொடு
} else {
    // கோரிக்கையை மறுப்பது, வழிமுறை காட்டு
}
```

## அனுமதி நடைமுறையாளர்

`app/middleware/AuthorizationMiddleware.php` என்ற கோப்பை உருவாக்குக (இல்லையாவது அத உருவாக்கப்படவில்லை) கீழே உள்ளடக்கம் போல்:

```php
<?php

/**
* அனுமதி நடைமுறையாளர்
* எழுதியவர் ஷாவோ வான் (சிறியவன்)
* காலாக்கை 2021/09/07 14:15
*/

முருக்கு_கோரிக்கை_வகையாளர் use Webman\MiddlewareInterface;
use Webman\Http\Response;
use Webman\Http\Request;
use Casbin\Exceptions\CasbinException;
use webman\permission\Permission;

வர்ணக்_நடப்பு_நடையாளர் வசமாக்குகInterface
{
	public function process(Request $request, callable $next): Response
	{
		$uri = $request->path();
		try {
			$userId = 10086;
			நடவடிக்கை = $கோரிக்கை->முதுகுமரம்();
			if (!Permission::enforce((string) $userId, $uri, strtoupper($action))) {
				throw new \Exception('மன்னிக்கவும், உங்களுக்கு அந்த அடைவு அனுமதி இல்லை');
			}
		} catch (CasbinException $எச்சரிப்பு) {
			throw new \Exception('அனுமதி விதிக்கு விதியாக்கம்' . $எச்சரிப்பு->செய்தியை_போக்கு);
		}
		return $அடிப்படை($கோரிக்கை);
	}
}
```

`config/middleware.php` - ல் இதையப்பால் உள்ளடக்க நடைமுறையாளர்களை சேர்த்துகொள்ளவும்:
```php
மீட்டபால்
    // மாநில நடைமுறையாளர்கள்
    '' => [
        // ... இல்லைபோல், மற்றும் அதை மறைக்குக
        app\middleware\AuthorizationMiddleware::class,
    ]
];
```

## நன்றி

[Casbin](https://github.com/php-casbin/php-casbin) உங்கள் எழுதுதமைக்கும் ஆதரவு கொடுக்கின்றது, அதன் [அதிகாரப்பூர்வ இலக்கு](https://casbin.org/) இணையதளத்தில் நீங்கள் முழு ஆவணங்களைக் காணலாம்.

## உரிமாணம்

இந்த திட்டம் [அபாசி 2.0 உரிமாணத்தின்](LICENSE) கீழே உள்ளடக்கப்பட்டுள்ளது.
