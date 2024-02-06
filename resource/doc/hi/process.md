# कस्टम प्रक्रियाओं

webman में आप workerman तरह से सुन सकते हैं या प्रक्रियाओं को स्वयं सुना सकते हैं।

> **ध्यान दें**
> विंडोज़ उपयोगकर्ताओं को webman को चलाने के लिए कस्टम प्रक्रिया को चलाने के लिए `php windows.php` का उपयोग करना होगा।

## कस्टम HTTP सेवा
कभी-कभी आपको webman HTTP सेवा के कर्नल कोड को बदलने की कुछ विशेष आवश्यकता हो सकती है, इस समय आप कस्टम प्रक्रिया का उपयोग कर सकते हैं।

उदाहरण के लिए app\Server.php नया बनाएँ

```php
<?php

namespace app;

use Webman\App;

class Server extends App
{
    // यहाँ आप Webman\App में विधियों को फिर से लिख सकते हैं
}
```

`config/process.php` में निम्नलिखित कॉन्फ़िगरेशन जोड़ें

```php
use Workerman\Worker;

return [
    // ... अन्य कॉन्फ़िगरेशन छोड़ दें...
    
    'my-http' => [
        'handler' => app\Server::class,
        'listen' => 'http://0.0.0.0:8686',
        'count' => 8, // प्रक्रियाएँ
        'user' => '',
        'group' => '',
        'reusePort' => true,
        'constructor' => [
            'request_class' => \support\Request::class, // अनुरोध की वर्ग सेट करें
            'logger' => \support\Log::channel('default'), // लॉग उदाहरण
            'app_path' => app_path(), // app निर्देशिका स्थान
            'public_path' => public_path() // पब्लिक निर्देशिका स्थान
        ]
    ]
];
```

> **सुझाव**
> यदि आप webman के साथ आते हुए अपने पास HTTP प्रक्रिया को बंद करना चाहते हैं, तो बस config/server.php में `listen=>''` सेट करें।

## कस्टम वेबसॉकेट सुनने का उदाहरण

`app/Pusher.php` को नया बनाएँ
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
> ध्यान दें: सभी onXXX गुणसूचियाँ सार्वजनिक हैं

`config/process.php` में निम्नलिखित कॉन्फ़िगरेशन जोड़ें
```php
return [
    // ... अन्य प्रक्रिया समाकों की छूट ...
    
    // websocket_test प्रक्रिया नाम है
    'websocket_test' => [
        // यहाँ प्रक्रिया वर्ग स्पष्ट करता है, जैसा कि पहले परिभाषित किया गया है Pusher वर्ग के रूप में।
        'handler' => app\Pusher::class,
        'listen'  => 'websocket://0.0.0.0:8888',
        'count'   => 1,
    ],
];
```

## कस्टम ना सुनने वाली प्रक्रिया का उदाहरण
`app/TaskTest.php` नया बनाएँ
```php
<?php
namespace app;

use Workerman\Timer;
use support\Db;

class TaskTest
{
  
    public function onWorkerStart()
    {
        // हर 10 सेकंड में डेटाबेस की जाँच करें कि क्या नए उपयोगकर्ता रजिस्टर हुए हैं
        Timer::add(10, function(){
            Db::table('users')->where('regist_timestamp', '>', time()-10)->get();
        });
    }
    
}
```
`config/process.php` में निम्नलिखित कॉन्फ़िगरेशन जोड़ें
```php
return [
    // ... अन्य प्रक्रिया कॉन्फ़िगरेशन छोड़...
    
    'task' => [
        'handler'  => app\TaskTest::class
    ],
];
```

> ध्यान दें: listen को छोड़ देने पर कोई भी पोर्ट सुना नहीं जाएगा, और count को छोड़ने पर प्रक्रियाओं की संख्या डिफ़ॉल्ट रूप से 1 होगी।

## कॉन्फ़िगरेशन फ़ाइल की समझावट

एक प्रक्रिया का पूर्ण कॉन्फ़िगरेशन निमंत्रण निम्नलिखित रूप में होता है:
```php
return [
    // ... 

    // websocket_test प्रक्रिया नाम है
    'websocket_test' => [
        // यहाँ प्रक्रिया वर्ग स्पष्ट करता है
        'handler' => app\Pusher::class,
        // सुनने का कानून आईपी और पोर्ट (वैकल्पिक)
        'listen'  => 'websocket://0.0.0.0:8888',
        // प्रक्रियाएँ (वैकल्पिक, डिफ़ॉल्ट 1)
        'count'   => 2,
        // प्रक्रिया चलाने वाला उपयोगकर्ता (वैकल्पिक, डिफ़ॉल्ट वर्तमान उपयोगकर्ता)
        'user'    => '',
        // प्रक्रिया चलाने वाला उपयोगकर्ता समूह (वैकल्पिक, डिफ़ॉल्ट वर्तमान उपयोगकर्ता समूह)
        'group'   => '',
        // वर्तमान प्रक्रिया का आप उधारण तब्दील कर सकते हैं (वैकल्पिक, डिफ़ॉल्ट सच)
        'reloadable' => true,
        // reusePort को चालू करें (वैकल्पिक, यह विकल्प php>=7.0 की जरूरत होती है, डिफ़ॉल्ट रूप से सच होता है)
        'reusePort'  => true,
        // परिवहन (वैकल्पिक, जब आपको ssl को सक्रिय करना होता है तो सेट करें)
        'transport'  => 'tcp',
        // संदर्भ (वैकल्पिक, जब परिवहन ssl होता है, प्रमाणपत्र पथ पास करना होता है)
        'context'    => [], 
        // प्रक्रिया वर्ग निर्माणकोश आदान-प्रदान पैरामीटर, यहाँ process\Pusher::class के निर्माणकोश पैरामीटर हैं (वैकल्प, डिफ़ॉल्ट रूप से सच)
        'constructor' => [],
    ],
];
```

## संक्षेप
webman की कस्टम प्रक्रियाएँ वास्तव में workerman की एक सरल पैकेज है, जो कॉन्फ़िगरेशन और व्यापार को अलग करता है, और workerman के `onXXX` कोलबैक को कक्षा के विधियों के माध्यम से प्राप्त करता है, इसके अलावा workerman के अन्य उपयोग का पूरा नियमित है।
