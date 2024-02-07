# Casbin एक्सेस नियंत्रण पुस्तकालय webman-permission

## विवरण

यह [PHP-Casbin](https://github.com/php-casbin/php-casbin) पर आधारित है, एक शक्तिशाली और दक्ष ओपन-सोर्स एक्सेस नियंत्रण फ़्रेमवर्क है, जो `ACL`, `RBAC`, `ABAC` आदि एक्सेस नियंत्रण मॉडल का समर्थन करता है।

## परियोजना पता

https://github.com/Tinywan/webman-permission

## स्थापना

```php
composer require tinywan/webman-permission
```

> इस एक्सटेंशन को PHP 7.1+ और [ThinkORM](https://www.kancloud.cn/manual/think-orm/1257998) की आवश्यकता है, आधिकारिक मैन्युअल: https://www.workerman.net/doc/webman#/db/others
## विन्यास

### सेवा पंजीकरण
नया विन्यास फ़ाइल `config/bootstrap.php` बनाएँ, जैसे निम्नलिखित सामग्री:
```php
// ...
webman\permission\Permission::class,
```

### मॉडल विन्यास फ़ाइल
नया विन्यास फ़ाइल `config/casbin-basic-model.conf` बनाएँ, जैसे निम्नलिखित सामग्री:
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

### नीति विन्यास फ़ाइल
नया विन्यास फ़ाइल `config/permission.php` बनाएँ, जैसे निम्नलिखित सामग्री:
```php
<?php

return [
    /*
     * डिफ़ॉल्ट परमिशन
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

            // अडैप्टर
            'adapter' => webman\permission\adapter\DatabaseAdapter::class,

            /*
            * डेटाबेस सेटिंग्स
            */
            'database' => [
                // डेटाबेस कनेक्शन नाम, यदि खाली है तो डिफ़ॉल्ट कन्फ़िगरेशन होगी।
                'connection' => '',
                // नीति तालिका नाम (प्रीफ़िक्स सहित)
                'rules_name' => 'rule',
                // नीति तालिका पूर्ण नाम।
                'rules_table' => 'train_rule',
            ],
        ],
    ],
];
```
## तेज़ आरंभ

```php
use webman\permission\Permission;

// उपयोगकर्ता के लिए अनुमतियों को जोड़ें
Permission::addPermissionForUser('eve', 'articles', 'read');
// उपयोगकर्ता के लिए एक भूमिका जोड़ें।
Permission::addRoleForUser('eve', 'writer');
// नियमों के लिए अनुमतियाँ जोड़ें
Permission::addPolicy('writer', 'articles','edit');
``` 

आप जांच सकते हैं कि क्या उपयोगकर्ता के पास ऐसी अनुमति है

```php
if (Permission::enforce("eve", "articles", "edit")) {
    // eve को लेखों को संपादित करने की अनुमति दें
} else {
    // अनुरोध को मना करें, एक त्रुटि दिखाएं
}
```
## अधिकृतता मध्यवर्ती

`app/middleware/AuthorizationMiddleware.php` नामक फ़ाइल बनाएं (यदि डायरेक्टरी मौजूद नहीं है तो स्वयं बना लें) निम्नलिखित जैसा:

```php
<?php

/**
 * अधिकृतता मध्यवर्ती
 * लेखक: शाओबो वान (टाइनीवान)
 * समय: 2021/09/07 14:15
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
				throw new \Exception('माफ़ कीजिए, आपको इस एपीआई तक पहुँचने की अनुमति नहीं है');
			}
		} catch (CasbinException $exception) {
			throw new \Exception('अधिकृतता की असामान्यता' . $exception->getMessage());
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
        // ... यहां अन्य मध्यवर्ती को छोड़ दिया गया है
        app\middleware\AuthorizationMiddleware::class,
    ]
];
```
## धन्यवाद

[Casbin](https://github.com/php-casbin/php-casbin), आप इसके [आधिकारिक वेबसाइट](https://casbin.org/) पर सभी दस्तावेज़ देख सकते हैं।

## लाइसेंस

इस परियोजना का लाइसेंस [एपाचे 2.0 लाइसेंस](LICENSE) के तहत है।
