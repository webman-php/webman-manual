# कस्टम प्रोसेस

webman में आप workerman की तरह सुनवाई या प्रोसेस को स्वयं परिभाषित कर सकते हैं।

> **ध्यान दें**
> विंडोज़ उपयोगकर्ताओं को "php windows.php" का उपयोग करके webman को स्वयं परिभाषित प्रक्रिया चालू करने के लिए webman प्रारंभ करने की आवश्यकता होती है।

## कस्टम HTTP सेवा
कभी-कभी आपको किसी विशेष आवश्यकता का सामना करना पड़ सकता है, जिसमें webman HTTP सेवा के कोर कोड में परिवर्तन करना पड़ सकता है, इसके लिए कस्टम प्रोसेस का उपयोग किया जा सकता है।

उदाहरण के लिए नया बनाएँ app\Server.php

```php
<?php

namespace app;

use Webman\App;

class Server extends App
{
    // यहां Webman\App के विधियों को फिर से लिखें
}
```

`config/process.php` में निम्न रूप में कॉन्फ़िगरेशन जोड़ें

```php
use Workerman\Worker;

return [
    // ... अन्य कॉन्फ़िगरेशन छोड़ दी गई है ...
    
    'my-http' => [
        'handler' => app\Server::class,
        'listen' => 'http://0.0.0.0:8686',
        'count' => 8, // प्रोसेस संख्या
        'user' => '',
        'group' => '',
        'reusePort' => true,
        'constructor' => [
            'request_class' => \support\Request::class, // अनुरोध कक्षा सेट करें
            'logger' => \support\Log::channel('default'), // लॉग उदाहरण
            'app_path' => app_path(), // ऐप निर्देशिका स्थान
            'public_path' => public_path() // सार्वजनिक निर्देशिका स्थान
        ]
    ]
];
```

> **सुझाव**
> यदि webman के साथ आने वाले HTTP प्रोसेस को बंद करना चाहते हैं, तो केवल config/server.php में `listen=>''` सेट करें

## कस्टम वेबसॉकेट सुनवाई उदाहरण

नया बनाएँ `app/Pusher.php`
```php
<?php
namespace app;

use Workerman\Connection\TcpConnection;

class Pusher
{
    public function onConnect(TcpConnection $connection)
    {
        echo "onConnect\n";
    }

    public function onWebSocketConnect(TcpConnection $connection, $http_buffer)
    {
        echo "onWebSocketConnect\n";
    }

    public function onMessage(TcpConnection $connection, $data)
    {
        $connection->send($data);
    }

    public function onClose(TcpConnection $connection)
    {
        echo "onClose\n";
    }
}
```
> ध्यान दें: सभी onXXX गुण सार्वजनिक होते हैं

`config/process.php` में निम्न रूप में कॉन्फ़िगरेशन जोड़ें
```php
return [
    // ... अन्य प्रोसेस कॉन्फ़िगरेशन छोड़ें ...
    
    // websocket_test प्रोसेस नाम
    'websocket_test' => [
        // यहां प्रोसेस कक्षा की निर्देशित करें, यानी उपर वर्णित Pusher कक्षा
        'handler' => app\Pusher::class,
        'listen'  => 'websocket://0.0.0.0:8888',
        'count'   => 1,
    ],
];
```

## कस्टम गैर-सुनवाई प्रोसेस उदाहरण
नया बनाएँ `app/TaskTest.php`
```php
<?php
namespace app;

use Workerman\Timer;
use support\Db;

class TaskTest
{
  
    public function onWorkerStart()
    {
        // हर 10 सेकंड में डेटाबेस की जाँच करें कि क्या नए उपयोगकर्ता ने पंजीकरण किया है
        Timer::add(10, function(){
            Db::table('users')->where('regist_timestamp', '>', time()-10)->get();
        });
    }
    
}
```
`config/process.php` में निम्न रूप में कॉन्फ़िगरेशन जोड़ें
```php
return [
    // ... अन्य प्रोसेस कॉन्फ़िगरेशन छोड़ें
    
    'task' => [
        'handler'  => app\TaskTest::class
    ],
];
```

> ध्यान दें: लिसेन छोड़ें तो कोई भी पोर्ट सुनने के लिए नहीं, गणना छोड़ें तो प्रोसेस की संख्या डिफ़ॉल्ट रूप से 1 होती है।

## कॉन्फ़िगरेशन फ़ाइल समझौता

एक प्रोसेस का पूरा कॉन्फ़िगरेशन निर्धारण निम्नलिखित है:
```php
return [
    // ... 
    
    // websocket_test प्रोसेस नाम
    'websocket_test' => [
        // यहां प्रोसेस कक्षा निर्दिष्ट करें
        'handler' => app\Pusher::class,
        // सुनवाई प्रोटोकॉल आईपी और पोर्ट सुनवाई (ऐचटीटीपीएस://0.0.0.0:8888) (वैकल्पिक)
        'listen'  => 'websocket://0.0.0.0:8888',
        // प्रक्रिया संख्या (वैकल्पिक, डिफ़ॉल्ट 1)
        'count'   => 2,
        // प्रक्रिया चलाने वाला उपयोगकर्ता (वैकल्पिक, डिफ़ॉल्ट वर्तमान उपयोगकर्ता)
        'user'    => '',
        // प्रक्रिया चलाने वाला उपयोगकर्ता समूह (वैकल्पिक, डिफ़ॉल्ट वर्तमान उपयोगकर्ता समूह)
        'group'   => '',
        // वर्तमान प्रक्रिया का मुख्य क्या यह पुनः लोड होता है (वैकल्पिक, डिफ़ॉल्ट सच्चा)
        'reloadable' => true,
        // reusePort (वैकल्पिक, इस विकल्प की आवश्यकता है कि php >= 7.0, डिफ़ॉल्ट true)
        'reusePort'  => true,
        // परिवहन (वैकल्पिक, जब सुरक्षा प्रारम्भ होती है तो सेट करें)
        'transport'  => 'tcp',
        // संदर्भ (वैकल्पिक, परिवहन ssl होने पर, प्रमाणपत्र पथ पारित करना आवश्यक है)
        'context'    => [], 
        // प्रक्रिया कक्षा का निर्माणकर्ता प्रारंभिकीकरण पैरामीटर, यहां process\Pusher::class कक्षा के निर्माणकर्ता पैरामीटर (वैकल्पिक)
        'constructor' => [],
    ],
];
```

## सारांश
webman का कस्टम प्रोसेस वास्तविक रूप से workerman की एक सरल ढकेल है, जो इसे विन्यास और व्यावसायिक पृथक करता है, और workerman की `onXXX` कॉलबैक को विधि द्वारा कार्यान्वित करता है, अन्य उपयोग workerman के साथ पूरी तरह से मेल खाते हैं।
