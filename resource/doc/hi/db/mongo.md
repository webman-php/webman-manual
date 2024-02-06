वेबमैन डिफ़ॉल्ट रूप से [jenssegers/mongodb](https://github.com/jenssegers/laravel-mongodb) को MongoDB कंपोनेंट के रूप में उपयोग करता है, यह लारावेल परियोजना से निकाला गया है, और इसका उपयोग लारावेल के साथ किया जाता है।

`jenssegers/mongodb` का उपयोग करने से पहले `php-cli` को MongoDB एक्सटेंशन इंस्टॉल करना आवश्यक है।

> `php -m | grep mongodb` कमांड का उपयोग करके देखें कि `php-cli` में MongoDB एक्सटेंशन इंस्टॉल है या नहीं। ध्यान दें: यदि आपने `php-fpm` में MongoDB एक्सटेंशन इंस्टॉल कर दिया है, तो यह नहीं मानता कि आप `php-cli` में इसका उपयोग कर सकते हैं, क्योंकि `php-cli` और `php-fpm` अलग-अलग अनुप्रयोग हैं, और वे संभावित रूप से अलग-अलग `php.ini` कॉन्फ़िगरेशन का उपयोग कर रहे होंगे। आप जिस `php.ini` कॉन्फ़िगरेशन फ़ाइल का उपयोग कर रहे हैं, उसको देखने के लिए `php --ini` कमांड का उपयोग करें।

## इंस्टॉलेशन

PHP>7.2 होने पर
```php
composer require -W illuminate/database jenssegers/mongodb ^3.8.0
```
PHP=7.2 होने पर
```php
composer require -W illuminate/database jenssegers/mongodb ^3.7.0
```

इंस्टॉल करने के बाद पुनः शुरूकरने की आवश्यकता होती है (रिलोड नहीं करना चाहिए)

## कॉन्फ़िगरेशन
`config/database.php` में `mongodb` कनेक्शन जोड़ें, निम्नलिखित समानांतर मन्त्र का उपयोग करें:
```php
return [

    'default' => 'mysql',

    'connections' => [

         ...अन्य कॉन्फ़िगरेशन छोड़ दिया गया है...

        'mongodb' => [
            'driver'   => 'mongodb',
            'host'     => '127.0.0.1',
            'port'     =>  27017,
            'database' => 'test',
            'username' => null,
            'password' => null,
            'options' => [
                // यहां आप मोंगो ड्राइवर मैनेजर को और अधिक सेटिंग्स पास कर सकते हैं
                // पूर्ण पैरामीटर की सूची के लिए देखें https://www.php.net/manual/en/mongodb-driver-manager.construct.php "Uri Options" अन्डर "Uri Options"

                'appname' => 'homestead'
            ],
        ],
    ],
];
```

## उदाहरण
```php
<?php
namespace app\controller;

use support\Request;
use support\Db;

class UserController
{
    public function db(Request $request)
    {
        Db::connection('mongodb')->collection('test')->insert([1,2,3]);
        return json(Db::connection('mongodb')->collection('test')->get());
    }
}
```

## अधिक जानकारी के लिए कृपया यहां जाएं

https://github.com/jenssegers/laravel-mongodb
