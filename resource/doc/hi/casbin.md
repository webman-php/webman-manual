# Casbin ऍक्सेस कंट्रोल लाइब्रेरी वेबमैन-परमिशन

## विवरण

यह [PHP-Casbin](https://github.com/php-casbin/php-casbin) पर आधारित है, एक शक्तिशाली, दक्षिणपंथी ओपन-सोर्स ऍक्सेस कंट्रोल फ्रेमवर्क है, जो `ACL`, `RBAC`, `ABAC` आदि ऍक्सेस कंट्रोल मॉडल समर्थन करता है।

## प्रोजेक्ट यूआरऍल

https://github.com/Tinywan/webman-permission

## स्थापना

```php
composer require tinywan/webman-permission
```
> यह एक्सटेंशन PHP 7.1+ और [ThinkORM](https://www.kancloud.cn/manual/think-orm/1257998) की आवश्यकता है, आधिकारिक मैनुअल: https://www.workerman.net/doc/webman#/db/others

## कॉन्फ़िगरेशन

### सर्विस रजिस्टर

नया कॉन्फ़िगरेशन फ़ाइल बनाएं `config/bootstrap.php` और निम्नलिखित समान सामग्री होनी चाहिए:

```php
// ...
webman\permission\Permission::class,
```
### मॉडल कॉन्फ़िगरेशन फ़ाइल

नया कॉन्फ़िगरेशन फ़ाइल बनाएं `config/casbin-basic-model.conf` और निम्नलिखित समान सामग्री होनी चाहिए:

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
### पॉलिसी कॉन्फ़िगरेशन फ़ाइल

नया कॉन्फ़िगरेशन फ़ाइल बनाएं `config/permission.php` और निम्नलिखित समान सामग्री होनी चाहिए:

```php
<?php

return [
    /*
     *डिफ़ॉल्ट अनुमति
     */
    'default' => 'basic',

    'log' => [
        'enabled' => false,
        'logger' => 'log',
    ],

    'enforcers' => [
        'basic' => [
            /*
            * मॉडल सेटिंग्स 
            */
            'model' => [
                'config_type' => 'file',
                'config_file_path' => config_path() . '/casbin-basic-model.conf',
                'config_text' => '',
            ],

            // एडाप्टर .
            'adapter' => webman\permission\adapter\DatabaseAdapter::class,

            /*
            * डेटाबेस सेटिंग्स.
            */
            'database' => [
                // डेटाबेस कनेक्शन नाम, डिफ़ॉल्ट कॉन्फ़िगरेशन के लिए छोड़ें.
                'connection' => '',
                // नीति तालिका नाम (प्राथमिक प्राथमिक के बिना)
                'rules_name' => 'rule',
                // नीति तालिका पूर्ण नाम.
                'rules_table' => 'train_rule',
            ],
        ],
    ],
];
```
## त्वरित आरंभ

```php
use webman\permission\Permission;

// एक उपयोगकर्ता को अनुमतियां जोड़ता है
Permission::addPermissionForUser('eve', 'articles', 'read');
// एक उपयोगकर्ता के लिए किसी भूमिका को जोड़ता है।
Permission::addRoleForUser('eve', 'writer');
// एक नियम के लिए अनुमतियां जोड़ता है
Permission::addPolicy('writer', 'articles','edit');
```

आप यह जांच सकते हैं कि क्या उपयोगकर्ता के पास इस प्रकार की अनुमति है

```php
if (Permission::enforce("eve", "articles", "edit")) {
    // eve को लेखों को संपादित करने की अनुमति देता है
} else {
    // अनुरोध को अस्वीकार करें, एक त्रुटि दिखाएँ
}
````

## अनुमति मध्यवर्ती

`app/middleware/AuthorizationMiddleware.php` फ़ाइल बनाएं (यदि निर्दिष्ट निर्देशिका नहीं है, तो स्वयं बनाएं) निम्नलिखित:

```php
<?php

/**
 * अनुमति मध्यवर्ती
 * लेखक शाओबो वान (टाइनीवान)
 * @datetime 2021/09/07 14:15
 */

declare(strict_types=1);

namespace app\middleware;

use Webman\MiddlewareInterface;
use Webman\Http\Response;
use Webman\Http\Request;
use Casbin\Exceptions\CasbinException;
use webman\permission\Permission;

class AuthorizationMiddleware implements MiddlewareInterface
{
	public function process(Request $request, callable $next): Response
	{
		$uri = $request->path();
		try {
			$userId = 10086;
			$action = $request->method();
			if (!Permission::enforce((string) $userId, $uri, strtoupper($action))) {
				throw new \Exception('माफ़ कीजिए, आपके पास इस एपीआई का उपयोग करने की अनुमति नहीं है');
			}
		} catch (CasbinException $exception) {
			throw new \Exception('अधिकृतता में त्रुटि' . $exception->getMessage());
		}
		return $next($request);
	}
}
```

`config/middleware.php` में निम्नलिखित रूप में ग्लोबल मध्यवर्ती जोड़ें:

```php
return [
    // ग्लोबल मध्यवर्ती
    '' => [
        // ... इसमें अन्य मध्यवर्ती छोड़ दिया गया है
        app\middleware\AuthorizationMiddleware::class,
    ]
];
```

## धन्यवाद

[Casbin](https://github.com/php-casbin/php-casbin), आप इसके [ऑफिसियल वेबसाइट](https://casbin.org/) पर सभी दस्तावेज़ देख सकते हैं।

## लाइसेंस

इस प्रोजेक्ट का लाइसेंस [एपाची 2.0 लाइसेंस](LICENSE) के अधीन है।
