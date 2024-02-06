# কাস্টম প্রোসেস

ওয়েবম্যানে workerman এর মতো কাস্টম লিস্টেন অথবা প্রসেস করা যায়।

> **লক্ষ্য করুন**
> windows ব্যবহারকারীরা `php windows.php` ব্যবহার করে ওয়েবম্যান চালাতে সক্ষম হবেন কাস্টম প্রোসেসটি স্থাপন করতে।

## কাস্টম HTTP সার্ভার
সময়ের সাথে সাথে আপনার মতো কোনও বিশেষ প্রয়োজন হতে পারে যা, ওয়েবম্যান HTTP সার্ভারের কার্যতাত্ত্বিক কোড পরিবর্তন করতে হবে, এটা কাস্টম প্রোসেসের মাধ্যমে পুরোনো করা যেতে পারে।

উদাহরণস্বরূপ app\Server.php নতুন করে স্থাপন করুন

```php
<?php

namespace app;

use Webman\App;

class Server extends App
{
    // এখানে Webman\App এর পদক্ষেপ পুনরায় লিখুন
}
```

`config/process.php` এ নিম্নলিখিত কনফিগ যোগ করুন

```php
use Workerman\Worker;

return [
    // ... অন্যান্য কনফিগারেশন জাতীয় ...
    
    'my-http' => [
        'handler' => app\Server::class,
        'listen' => 'http://0.0.0.0:8686',
        'count' => 8, // প্রোসেসের সংখ্যা
        'user' => '',
        'group' => '',
        'reusePort' => true,
        'constructor' => [
            'request_class' => \support\Request::class, // অনুরোধের ক্লাস সেট
            'logger' => \support\Log::channel('default'), // লগ ইনস্ট্যান্স
            'app_path' => app_path(), // app ডিরেক্টরির অবস্থান
            'public_path' => public_path() // public ডিরেক্টরির অবস্থান
        ]
    ]
];
```

> **ইঙ্গিত**
> যদি ওয়েবম্যান স্বয়ংক্রিয়ভাবে হাব প্রোসেস বন্ধ করতে চান, তবে `config/server.php` তে `listen=>''` সেট করতে হবে।

## কাস্টম ওয়েবসকেট লিসেন উদাহরণ

নতুন হোল্ডার `app/Pusher.php`
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
> লক্ষ্য করুন: সমস্ত onXXX বৈশিষ্ট্যগুলি পাবলিক

`config/process.php` এ নিম্নলিখিত কনফিগ যোগ করুন
```php
return [
    // ... অন্যান্য প্রসেস কনফিগারেশন অন্দর করা আছে ...
    
    // ওয়েবসকেট_পরীক্ষা প্রসেস নাম
    'websocket_test' => [
        // এখানে প্রসেস ক্লাস পরিনিয়োয়ক। উপরে ডিফাইন Pusher ক্লাস টা
        'handler' => app\Pusher::class,
        'listen'  => 'websocket://0.0.0.0:8888',
        'count'   => 1,
    ],
];
```

## কাস্টম গৈর-শুনোমুখী প্রসেস উদাহরণ

ওয়েবম্যান `\`
```php
namespace app;

use Workerman\Timer;
use support\Db;

class TaskTest
{
  
    public function onWorkerStart()
    {
        // প্রতি 10 সেকেন্ডে ডাটাবেসে নতুন ব্যবহারকারী রেজিস্টারেড কি আছে তা চেক করুন
        Timer::add(10, function(){
            Db::table('users')->where('regist_timestamp', '>', time()-10)->get();
        });
    }
    
}
```
`config/process.php` এ নিম্নলিখিত কনফিগ যোগ করুন
```php
return [
    // ... অন্যান্য কনফিগারেশন জাতীয় ...
    
    'task' => [
        'handler'  => app\TaskTest::class
    ],
];
```

> লক্ষ্য করুন: শুনূকের কোনো জায়গা না দেওয়া হলে প্রত্যেক শুনূকের সংখ্যা ডিফল্টভাবে 1।

## কনফিগ ফাইলের ব্যাখ্যা

একটি সম্পূর্ণ প্রসেসের কনফিগ ডেফিনিশন নিম্নলিখিত মতে দেওয়া হয়:

```php
return [
    // ... 
    
    // ওয়েবসকেট_টেস্ট হল প্রসেস নাম
    'websocket_test' => [
        // এখানে প্রসেস ক্লাস নির্দিষ্ট করুন
        'handler' => app\Pusher::class,
        // এখানে বিচারে নাম, আইপি এবং পোর্ট শুনুন (ঐচ্ছিক)
        'listen'  => 'websocket://0.0.0.0:8888',
        // প্রসেসের সংখ্যা (ঐচ্ছিক, পূর্বনির্ধারিত 1)
        'count'   => 2,
        // প্রসেসে চালু ব্যবহারকারী (ঐচ্ছিক, বর্তমান ব্যবহারকারী)
        'user'    => '',
        // প্রসেসে চালু ব্যবহারকারী গ্রুপ (ঐচ্ছিক, বর্তমান ব্যবহারকারী গ্রুপ)
        'group'   => '',
        // বর্তমান প্রসেস যদি রিলোড সমর্থন করে তাহলে (ঐচ্ছিক, পূর্বনির্ধারিত হ্যাঁ)
        'reloadable' => true,
        // ক্যান রিউজপর্টটি চালু করা হয়েছে (ঐচ্ছিক, এই অপশনটি php>=7.0 চালু থাকতে হবে, পূর্বনির্ধারিত হলো true)
        'reusePort'  => true,
        // ট্রান্সপোর্ট (ঐচ্ছিক, যখন ssl চালু হতে চাইলে ssl যোগ্যতা)
        'transport'  => 'tcp',
        // সন্দর্ভ (ঐচ্ছিক, যখন ssl হয় তাহলে, সার্টিফিকেট পথ বেসামুহিক করেব সেট করতে পারে)
        'context'    => [], 
        // প্রসেস ক্লাস নির্মিতকারী ফাংশন প্যারামিটার, এখানে process\Pusher::class এর নির্মিতকারীর প্যারামিটার (ঐচ্ছিক)
        'constructor' => [],
    ],
];
```

## সারাংশ
ওয়েবম্যানের কাস্টম প্রোসেস প্রাকৃতদর্শকের মর্মেও workerman এর একটি সাধারণ পিকোডিং করা হয়, এটি কনফিগ এবং ব্যাবসায়িক পালটা করে, এবং workerman এর `onXXX` কলব্যাকগুলি ক্লাসের মেথড দিয়ে রয়েছে, অন্যদূর যা workerman এর ব্যুবহার ভাব পূর্ণান্বিত রাখে।
