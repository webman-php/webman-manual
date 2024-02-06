# सत्र प्रबंधन

## उदाहरण
```php
<?php
namespace app\controller;

use support\Request;

class UserController
{
    public function hello(Request $request)
    {
        $name = $request->get('name');
        $session = $request->session();
        $session->set('name', $name);
        return response('hello ' . $session->get('name'));
    }
}
```

`$request->session();` के माध्यम से `Workerman\Protocols\Http\Session` इंस्टेंस प्राप्त करें, और विधियों के माध्यम से इंस्टेंस को बढ़ावा, संशोधित और सत्र डेटा हटाएं।

> ध्यान दें: सत्र ऑब्जेक्ट नष्ट होते समय सत्र डेटा स्वत: सहेजा जाता है, इसलिए कृपया सत्र डेटा को ग्लोबल एरे या कक्ष सदस्य में सहेजें नहीं, जिससे सत्र सहेजा न जा सके।

## सभी सत्र डेटा प्राप्त करें
```php
$session = $request->session();
$all = $session->all();
```
इससे एक एरे प्राप्त होता है। यदि कोई सत्र डेटा नहीं है, तो एक खाली सत्र मिलेगा।

## किसी सत्र में कोई भी मूल्य प्राप्त करें
```php
$session = $request->session();
$name = $session->get('name');
```
डेटा अपरिपक्व होने पर null प्रत्यार्पित होता है।

आप गेट विधि को दूसरे पैरामीटर के रूप में एक डिफॉल्ट मान भेज सकते हैं, यदि सत्र एरे में जोड़ता मान नहीं मिलता है, तो डिफॉल्ट मान प्राप्त होता है। उदाहरण के लिए:
```php
$session = $request->session();
$name = $session->get('name', 'tom');
```


## सत्र में डेटा संग्रहीत करें
जब किसी आइटम डेटा को संग्रहीत करना होता है, तो सेट विधि का प्रयोग करें।
```php
$session = $request->session();
$session->set('name', 'tom');
```
सेट कोई वापसी मूल्य नहीं है, सत्र ऑब्जेक्ट नष्ट होते समय सत्र स्वत: सहेजा जाता है।

जब अनेक मूल्यों को संग्रहीत करना हो, तो पुट विधि का प्रयोग करें।
```php
$session = $request->session();
$session->put(['name' => 'tom', 'age' => 12]);
```
यहां भी, पुट कोई वापसी मूल्य नहीं है।

## सत्र डेटा हटाएं
किसी एक या एक से अधिक सत्र डेटा को हटाने के लिए `forget` विधि का प्रयोग करें।
```php
$session = $request->session();
// एक आइटम हटाएं
$session->forget('name');
// अनेक आइटम हटाएं
$session->forget(['name', 'age']);
```

अतिरिक्त रूप से सिस्टम ने डिलीट विधि प्रदान की है, इसका अंतर forget विधि से है, जिसमें, delete केवल एक आइटम को हटा सकता है।
```php
$session = $request->session();
// $session->forget('name');
$session->delete('name');
```

## सत्र का मूल्य हासिल और हटाएं
```php
$session = $request->session();
$name = $session->pull('name');
```
यह भी किसी आइटम के लिए वापसी करने वाले कोड के प्रत्यार्पण के प्रति प्रभाव रहता है
```php
$session = $request->session();
$value = $session->get($name);
$session->delete($name);
```
यदि संबंधित सत्र मौजूद नहीं है, तो null प्रत्यार्पित होगा।


## सभी सत्र डेटा हटाएं
```php
$request->session()->flush();
```
कोई वापसी मूल्य नहीं है, सत्र ऑब्जेक्ट नष्ट होते समय सत्र स्वत: संग्रहण ड्राइवर से हटा दिया जाएगा।


## सत्र डेटा की उपस्थिति का निर्धारण करें
```php
$session = $request->session();
$has = $session->has('name');
```
इससे संबंधित सत्र मौजूद नहीं है और संबंधित सत्र मान null होता है तो false प्रत्यार्पित होगा, जिनके लिए true प्रत्यार्पित किया जाएगा।

```
$session = $request->session();
$has = $session->exists('name');
```
यह कोड भी सत्र डेटा की उपस्थिति का निर्धारण करने के लिए है, अंतर है कि जब संबंधित सत्र आइटम मान null होता है, तो true प्रत्यार्पित होगा।

## सहायक फ़ंक्शन session()
> 2020-12-09 जोड़ा गया

webman प्राप्त करने के लिए `session()` सहायक फ़ंक्शन प्रदान करता है
```php
// सत्र इंस्टेंस प्राप्त करें
$session = session();
// समानूपरक संरूप है
$session = $request->session();

// किसी मूल्य को प्राप्त करें
$value = session('key', 'default');
// समानुपरक संरूप है
$value = session()->get('key', 'default');
// समानुपरक संरूप है
$value = $request->session()->get('key', 'default');

// सत्र को मूल्य असाइन करें
session(['key1'=>'value1', 'key2' => 'value2']);
// समानुपरक संरूप है
session()->put(['key1'=>'value1', 'key2' => 'value2']);
// समानुपरक संरूप है
$request->session()->put(['key1'=>'value1', 'key2' => 'value2']);

```

## विन्यास फ़ाइल
session विन्यास फ़ाइल को `config/session.php` में स्थानित है, जिसमें निम्नलिखित प्रकार की सामग्री होती है
```php
use Webman\Session\FileSessionHandler;
use Webman\Session\RedisSessionHandler;
use Webman\Session\RedisClusterSessionHandler;

return [
    // FileSessionHandler::class या RedisSessionHandler::class या RedisClusterSessionHandler::class 
    'handler' => FileSessionHandler::class,
    
    // हैंडलर के लिए file वैल्यू है तब
    // हैंडलर के लिए redis वैल्यू है तब
    // हैंडलर के लिए redis_cluster वैल्यू है तब जिसका अर्थ है redis क्लस्टर
    'type'    => 'file',

    // विभिन्न हैंडलर का उपयोग करते समय विभिन्न विन्यास का प्रयोग करें।
    'config' => [
        // फाइल के लिए टाइप विन्यास
        'file' => [
            'save_path' => runtime_path() . '/sessions',
        ],
        // रेडिस के लिए टाइप विन्यास
        'redis' => [
            'host'      => '127.0.0.1',
            'port'      => 6379,
            'auth'      => '',
            'timeout'   => 2,
            'database'  => '',
            'prefix'    => 'redis_session_',
        ],
        'redis_cluster' => [
            'host'    => ['127.0.0.1:7000', '127.0.0.1:7001', '127.0.0.1:7001'],
            'timeout' => 2,
            'auth'    => '',
            'prefix'  => 'redis_session_',
        ]
        
    ],

    'session_name' => 'PHPSID', // सत्र_आईडी कुकी नाम

    // === निम्नलिखित विन्यास webman-framework>=1.3.14 workerman>=4.0.37 चाहिए ===
    'auto_update_timestamp' => false,  // क्या सत्र को स्वचालित रूप से ताज़ा करें, डिफ़ॉल्ट रूप से बंद होता है
    'lifetime' => 7*24*60*60,          // सत्र समाप्ति समय
    'cookie_lifetime' => 365*24*60*60, // सत्र_आईडी कुकी समाप्ति समय
    'cookie_path' => '/',              // सत्र_आईडी कुकी पथ
    'domain' => '',                    // सत्र_आईडी कुकी डोमेन
    'http_only' => true,               // क्या httpOnly को सक्रिय करें, डिफ़ॉल्ट रूप से सक्रिय होता है
    'secure' => false,                 // केवल https के तहत सत्र को सक्रिय करें, डिफ़ॉल्ट रूप से बंद होता है
    'same_site' => '',                 // CSRF हमले और उपयोक्ता ट्रैकिंग से बचाने के लिए उपयोगी, strict/lax/none के लिए वैकल्पिक मान
    'gc_probability' => [1, 1000],     // सत्र को संग्रहीत करने की आशा


];
```

> **नोट** 
> webman 1.4.0 से SessionHandler को नेमस्पेस में बदल दिया गया है, पहले
> use Webman\FileSessionHandler;  
> use Webman\RedisSessionHandler;  
> use Webman\RedisClusterSessionHandler;  
> से 
> use Webman\Session\FileSessionHandler;  
> use Webman\Session\RedisSessionHandler;  
> use Webman\Session\RedisClusterSessionHandler;  
> बदल दिया गया है।


## समय-सीमा विन्यास
जब webman-framework < 1.3.14 होता है, तो वेबमैन में सत्र समय-सीमा को `php.ini` में विन्यासित किया जाना चाहिए।

```
session.gc_maxlifetime = x
session.cookie_lifetime = x
session.gc_probability = 1
session.gc_divisor = 1000
```

मान लें कि वैधता का समय 1440 सेकंड है, तो निम्न विन्यास को अपनाएं
```
session.gc_maxlifetime = 1440
session.cookie_lifetime = 1440
session.gc_probability = 1
session.gc_divisor = 1000
```

> **सुझाव**
> `php --ini` आदेश का उपयोग करके `php.ini` की स्थान पता करने के लिए कर सकते हैं।
