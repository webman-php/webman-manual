# डेटाबेस
प्लगइन अपना डेटाबेस कॉन्फ़िगर कर सकते हैं, उदाहरण के लिए `plugin/foo/config/database.php` की जानकारी निम्नलिखित है
```php
return  [
    'default' => 'mysql',
    'connections' => [
        'mysql' => [ // mysql के रूप में कनेक्शन का नाम
            'driver'      => 'mysql',
            'host'        => '127.0.0.1',
            'port'        => 3306,
            'database'    => 'डेटाबेस',
            'username'    => 'उपयोगकर्ता नाम',
            'password'    => 'पासवर्ड',
            'charset'     => 'utf8mb4',
            'collation'   => 'utf8mb4_general_ci',
        ],
        'admin' => [ // एडमिन के रूप में कनेक्शन का नाम
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
उपयोग का तरीका है`Db::connection('plugin.{प्लगइन}.{कनेक्शन नाम}');`, उदाहरण के लिए
```php
use support\Db;
Db::connection('plugin.foo.mysql')->table('user')->first();
Db::connection('plugin.foo.admin')->table('admin')->first();
```

यदि मुख्य परियोजना के डेटाबेस का उपयोग करना हो, तो सीधे उसका उपयोग करें, उदाहरण के लिए
```php
use support\Db;
Db::table('user')->first();
// मान लें कि मुख्य परियोजना ने एक एडमिन कनेक्शन भी कॉन्फ़िगर किया है
Db::connection('admin')->table('admin')->first();
```

## Model को डेटाबेस कॉन्फ़िगर करें

हम Model के लिए एक बेस कक्लास बना सकते हैं, बेस कक्लास में ` $connection` का उपयोग करके प्लगइन के अपने डेटाबेस कनेक्शन को निर्दिष्ट कर सकते हैं, उदाहरण

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

इस तरह से प्लगइन में सभी Model बेस से इंहेरिट करने से स्वचालित रूप से प्लगइन के अपने डेटाबेस का उपयोग हो जाता है।

## डेटाबेस का पुनःउपयोग करना

बेशक हम मुख्य परियोजना की डेटाबेस कॉन्फिगरेशन का पुनःउपयोग कर सकते हैं, यदि [webman-admin](https://www.workerman.net/plugin/82) को शामिल किया गया है, तो [webman-admin](https://www.workerman.net/plugin/82) की डेटाबेस कॉन्फ़िगरेशन का भी पुनःउपयोग किया जा सकता है, उदाहरण

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
