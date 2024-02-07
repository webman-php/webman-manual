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

`$request->session();` के माध्यम से `Workerman\Protocols\Http\Session` उदाहरण प्राप्त करें, उदाहरण की विधि के माध्यम से सत्र डेटा को जोड़ें, संशोधित करें, हटाएँ।

> ध्यान दें: सत्र ऑब्जेक्ट नष्ट होने पर सत्र डेटा स्वचालित रूप से सहेजा जाएगा, इसलिए कृपया न करें कि `$$request->session()` द्वारा वापस मिलने वाला ऑब्जेक्ट को ग्लोबल एरे या किसी भी क्लास सदस्य में सहेज कर सत्र सहेज नहीं सकता है।

## सभी सत्र डेटा प्राप्त करें
```php
$session = $request->session();
$all = $session->all();
```
यह एक एरे वापस करता है। यदि कोई सत्र डेटा नहीं है, तो एक खाली एरे लौटाया जाता है।

## कुछ मान प्राप्त करें
```php
$session = $request->session();
$name = $session->get('name');
```
अगर डेटा उपलब्ध नहीं है तो नल लौटाता है।

आप कुछ डीफ़ॉल्ट मान भेज सकते हैं get में दूसरे पैरामीटर के रूप में, अगर सत्र एरे में कोई मान नहीं मिलता है तो डीफ़ॉल्ट मान लौटाता है। उदाहरण के लिए:
```php
$session = $request->session();
$name = $session->get('name', 'tom');
```

## सत्र सहेजें
किसी विशिष्ट आइटम डेटा को सहेजने के लिए set विधि का उपयोग करें।
```php
$session = $request->session();
$session->set('name', 'tom');
```
set का कोई लौटाव नहीं है, सत्र ऑब्जेक्ट नष्ट होने पर सत्र स्वचालित रूप से सहेजा जाएगा।

जब बहुत से मान सहेजने होते हैं तो put विधि का उपयोग करें।
```php
$session = $request->session();
$session->put(['name' => 'tom', 'age' => 12]);
```
उसी प्रकार, put का कोई लौटाव नहीं है।

## सत्र डेटा हटाएँ
किसी अथवा कुछ सत्र डेटा को हटाने के लिए `forget` विधि का उपयोग करें।
```php
$session = $request->session();
// एक आइटम हटाएँ
$session->forget('name');
// कई आइटम हटाएँ
$session->forget(['name', 'age']);
```

अतिरिक्त रूप से सिस्टम `delete` विधि प्रदान करता है, भेद है, forget केवल एक आइटम हटा सकता है।
```php
$session = $request->session();
// $session->forget('name'); के समान
$session->delete('name');
```

## सत्र का कोई विशिष्ट मान प्राप्त और हटाएँ
```php
$session = $request->session();
$name = $session->pull('name');
```
यह प्रभाव निम्नलिखित कोड के समान है
```php
$session = $request->session();
$value = $session->get($name);
$session->delete($name);
```
यदि संबंधित सत्र उपलब्ध नहीं है, तो नल लौटाया जाता है।

## सभी सत्र डेटा हटाएँ
```php
$request->session()->flush();
```
कोई लौटाव नहीं है, सत्र ऑब्जेक्ट नष्ट होने पर सत्र स्वचालित रूप से संग्रहण से हटाया जाएगा।

## संबंधित सत्र डेटा की उपस्थिति को निर्धारित करें
```php
$session = $request->session();
$has = $session->has('name');
```
उपर्युक्त सत्र अनुपलब्ध है या यदि संबंधित सत्र मान नल है, तो लौटाता है false, अन्यथा true।

```php
$session = $request->session();
$has = $session->exists('name');
```
उपर्युक्त कोड भी सत्र डेटा की उपस्थिति को निर्धारित करने के लिए है, अंतर है कि संबंधित सत्र आइटम मान नल होने पर अंतर है, तो true लौटाता है।

## हेल्पर फ़ंक्शन सेशन()
> 2020-12-09 नया जोड़ा गया

webman ने समान कार्य करने के लिए हेल्पर फ़ंक्शन `session()` प्रदान किया है।
```php
// सत्र उदाहरण प्राप्त करें
$session = session();
// बराबरी द
$session = $request->session();

// कुछ मान प्राप्त करें
$value = session('key', 'default');
// बराबरी द
$value = session()->get('key', 'default');
// बराबरी द
$value = $request->session()->get('key', 'default');

// सत्र को मान से भरें
session(['key1'=>'value1', 'key2' => 'value2']);
// बराबरी द
session()->put(['key1'=>'value1', 'key2' => 'value2']);
// बराबरी द
$request->session()->put(['key1'=>'value1', 'key2' => 'value2']);
```

## कॉन्फ़िग फ़ाइल
सत्र कॉन्फ़िग फ़ाइल `config/session.php` में होती है, यहां कुछ इस प्रकार होता है:
```php
use Webman\Session\FileSessionHandler;
use Webman\Session\RedisSessionHandler;
use Webman\Session\RedisClusterSessionHandler;

return [
    // FileSessionHandler::class या RedisSessionHandler::class या RedisClusterSessionHandler::class 
    'handler' => FileSessionHandler::class,
    
    // handler के रूप में FileSessionHandler::class होने पर मान 'file', 
    // handler के रूप में RedisSessionHandler::class होने पर मान 'redis'
    // handler के रूप में RedisClusterSessionHandler::class होने पर मान 'redis_cluster', अर्थात् रेडिस क्लस्टर
    'type'    => 'file',

    // विभिन्न handler के लिए विभिन्न कॉन्फ़िगरेशन का उपयोग करें
    'config' => [
        // टाइप file होने पर कॉन्फ़िगरेशन
        'file' => [
            'save_path' => runtime_path() . '/sessions',
        ],
        // टाइप redis होने पर कॉन्फ़िगरेशन
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

    'session_name' => 'PHPSID', // सत्र आईडी कुकी का नाम
    
    // === नीचे दिए गए कॉन्फ़िगरेशन को आवश्यकता है webman-framework>=1.3.14 workerman>=4.0.37 ===
    'auto_update_timestamp' => false,  // क्या सत्र स्वचालित रूप से तारीख को अपडेट करेगा, डिफ़ॉल्ट बंद है
    'lifetime' => 7*24*60*60,          // सत्र समाप्ति समय
    'cookie_lifetime' => 365*24*60*60, // सत्र आईडी कुकी समाप्ति समय
    'cookie_path' => '/',              // सत्र आईडी कुकी पथ
    'domain' => '',                    // सत्र आईडी कुकी डोमेन
    'http_only' => true,               // क्या httpOnly, डिफ़ॉल्ट बंद
    'secure' => false,                 // सत्र केवल https के नीचे खोलें, डिफ़ॉल्ट बंद
    'same_site' => '',                 // CSRF हमला और उपयोगकर्ता ट्रैकिंग से बचाव के लिए, विकल्पित मान strict/lax/none
    'gc_probability' => [1, 1000],     // सत्र यद्यपि
];
```

> **ध्यान दें** 
> webman 1.4.0 से SessionHandler का नामस्थान बदल गया है, पहले
> use Webman\FileSessionHandler;  
> use Webman\RedisSessionHandler;  
> use Webman\RedisClusterSessionHandler;  
> _से बदलकर_  
> use Webman\Session\FileSessionHandler;  
> use Webman\Session\RedisSessionHandler;  
> use Webman\Session\RedisClusterSessionHandler;  

## समय सीमा विन्यास
जब webman-framework < 1.3.14 होता है, तो सत्र अवधि को `php.ini` में कॉन्फ़िगर करनी होती है।

```ini
session.gc_maxlifetime = x
session.cookie_lifetime = x
session.gc_probability = 1
session.gc_divisor = 1000
```

मान देखते हैं कि 1440 सेकंड के लिए समय सीमा है, इसकी विन्यासिकता निम्नलिखित होगी
```ini
session.gc_maxlifetime = 1440
session.cookie_lifetime = 1440
session.gc_probability = 1
session.gc_divisor = 1000
```

> **सुझाव**
> `php --ini` कमांड का उपयोग करके `php.ini` की स्थिति खोज सकते हैं।
