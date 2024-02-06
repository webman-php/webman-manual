# डेटाबेस
प्लगइन अपने डेटाबेस को कॉन्फ़िगर कर सकते हैं, जैसे `प्लगइन/फू/कॉन्फ़िग/डेटाबेस.php` की सामग्री निम्नलिखित होती है
```php
return  [
    'default' => 'mysql',
    'connections' => [
        'mysql' => [ // mysql कोनेक्शन नाम
            'driver'      => 'mysql',
            'host'        => '127.0.0.1',
            'port'        => 3306,
            'database'    => 'डेटाबेस',
            'username'    => 'उपयोगकर्ता नाम',
            'password'    => 'पासवर्ड',
            'charset'     => 'utf8mb4',
            'collation'   => 'utf8mb4_general_ci',
        ],
        'admin' => [ // एडमिन कोनेक्शन नाम
            'driver'      => 'mysql',
            'host'        => '127.0.0.1',
            'port'        => 3306,
            'database'    => 'डेटाबेस',
            'username'    => 'उपयोगकर्ता नाम',
            'password'    => 'पासवर्ड',
            'charset'     => 'utf8mb4',
            'collation'   => 'utf8mb4_general_ci',
        ],
    ],
];
```
इसका उपयोग करने का तरीका है `Db::connection('plugin.{प्लगइन}.{कनेक्शन_नाम}');`, उदाहरण के लिए
```php
use support\Db;
Db::connection('plugin.foo.mysql')->table('user')->first();
Db::connection('plugin.foo.admin')->table('admin')->first();
```

यदि प्रमुख परियोजना का डेटाबेस उपयोग करना चाहते हैं, तो सीधे उपयोग करें, उदाहरण के लिए
```php
use support\Db;
Db::table('user')->first();
// मान लें कि प्रमुख परियोजना ने एक एडमिन कनेक्शन भी कॉन्फ़िगर किया है
Db::connection('admin')->table('admin')->first();
```

## Model के लिए डेटाबेस कॉन्फ़िगर करना

हम Model के लिए एक बेस कक्षा बना सकते हैं, बेस कक्षा में `$connection` का उपयोग करके प्लगइन का अपना डेटाबेस कनेक्शन निर्दिष्ट किया जा सकता है, जैसे 

```php
<?php

namespace plugin\foo\app\model;

use DateTimeInterface;
use support\Model;

class Base extends Model
{
    /**
     * @var string
     */
    protected $connection = 'plugin.foo.mysql';

}
```

इस प्रकार, प्लगइन में सभी Model बेस से वंश होती हैं, तो स्वचालित रूप से प्लगइन का अपना डेटाबेस उपयोग करती हैं।

## डेटाबेस कॉन्फ़िगर को पुनःउपयोग करना
बेशक, हम मुख्य प्रोजेक्ट को डेटाबेस कॉन्फ़िगर को पुनःउपयोग कर सकते हैं, यदि आपने [webman-admin](https://www.workerman.net/plugin/82) को एक्सेस किया है, तो [webman-admin](https://www.workerman.net/plugin/82) डेटाबेस कॉन्फ़िगर को पुनः उपयोग किया जा सकता है, उदाहरण के लिए
```php
<?php

namespace plugin\foo\app\model;

use DateTimeInterface;
use support\Model;

class Base extends Model
{
    /**
     * @var string
     */
    protected $connection = 'plugin.admin.mysql';

}
```
