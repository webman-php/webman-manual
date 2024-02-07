वेबमन डिफ़ॉल्ट रूप से [jenssegers/mongodb](https://github.com/jenssegers/laravel-mongodb) का उपयोग mongodb component के रूप में करता है, जो कि laravel प्रोजेक्ट से नि: शृंखला से निकाला गया है, इसका उपयोग laravel के समान है।

`jenssegers/mongodb` का उपयोग करने से पहले, `php-cli` में mongodb extension को स्थापित करना आवश्यक है।

> `php -m | grep mongodb` कमांड का उपयोग करके देखें कि `php-cli` में mongodb extension स्थापित है या नहीं। ध्यान दें: यहां तक कि अगर आपने `php-fpm` में mongodb extension स्थापित कर लिया है, तो यह नहीं संकेत देता है कि आप `php-cli` में इसका उपयोग कर सकते हैं, क्योंकि `php-cli` और `php-fpm` अलग-अलग अनुप्रयोग हैं, शायद वे विभिन्न `php.ini` कॉन्फ़िगरेशन का उपयोग कर रहे हों। अपनी `php-cli` द्वारा किस `php.ini` कॉन्फ़िगरेशन फ़ाइल का उपयोग हो रहा है, उसे देखने के लिए यह कमांड का उपयोग करें `php --ini`।

## स्थापना

PHP>7.2 के लिए
```php
composer require -W illuminate/database jenssegers/mongodb ^3.8.0
```
PHP=7.2 के लिए
```php
composer require -W illuminate/database jenssegers/mongodb ^3.7.0
```

स्थापना के बाद restart  की जरूरत होती है (reload फायदेमंद नहीं होता)

## कॉन्फ़िगरेशन
`config/database.php` में `mongodb` कनेक्शन जोड़ें, निम्नलिखित तरह से:

```php
return [ 
    'default' => 'mysql', 
    'connections' => [
        ...अन्य कॉन्फ़िगरेशन यहां छोड़ दी गई है...
        'mongodb' => [ 
            'driver'   => 'mongodb', 
            'host'     => '127.0.0.1', 
            'port'     =>  27017, 
            'database' => 'test', 
            'username' => null, 
            'password' => null, 
            'options' => [ 
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

## अधिक जानकारी के लिए यहां जाएं
https://github.com/jenssegers/laravel-mongodb
