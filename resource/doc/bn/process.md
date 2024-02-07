# কাস্টম প্রসেস

webman-এ workerman এর মতন কাস্টম লিসেনার অথবা প্রসেস তৈরি করা যায়। 

> **লক্ষ্য করুন**
> windows ব্যবহারকারীদের পরিবার্তনের জন্য ওয়েবম্যান চালু করতে হলে `php windows.php` ব্যবহার করতে হবে।

## কাস্টম এইচটিটিপি সার্ভার
কিছু সময় আপনার কোন বিশেষ প্রয়োজনে হতে পারে এবং ওয়েবম্যান http সার্ভারের কোড পরিবর্তন করা প্রয়োজন হতে পারে, এটি সমাধান করার জন্য কাস্টম প্রসেস ব্যবহার করা যায়।

উদাহরণস্বরূপ নতুন app\Server.php তৈরি করুন

```php
<?php

namespace app;

use Webman\App;

class Server extends App
{
    // এখানে Webman\App এর মেথড পুনরায় লিখুন
}
```

`config/process.php` এ নিম্নলিখিত কনফিগারেশন যুক্ত করুন

```php
use Workerman\Worker;

return [
    // ... অন্যান্য কনফিগারেশন অংশগুলি অনুলিপি ...
    
    'my-http' => [
        'handler' => app\Server::class,
        'listen' => 'http://0.0.0.0:8686',
        'count' => 8, // প্রসেসের সংখ্যা
        'user' => '',
        'group' => '',
        'reusePort' => true,
        'constructor' => [
            'request_class' => \support\Request::class, // অনুরোধ ক্লাস সেট করুন
            'logger' => \support\Log::channel('default'), // লগ ইন্সট্যান্স
            'app_path' => app_path(), // app ফোল্ডারের অবস্থান
            'public_path' => public_path() // পাবলিক ফোল্ডারের অবস্থান
        ]
    ]
];
```

> **পরামর্শ**
> যদি ওয়েবম্যান-এর নিজস্ব http প্রসেসটি বন্ধ করতে চান, তবে কনফিগ সার্ভার.php তে `listen=>''` সেট করুন

## কাস্টম ওয়েবসকেট লিসেনার উদাহরণ

app/Pusher.php তৈরি করুন
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
> লক্ষ্য রাখুন: সব onXXX মেথডগুলি public হতে হবে

`config/process.php` এ নিম্নলিখিত কনফিগারেশন যুক্ত করুন
```php
return [
    // ... অন্যান্য প্রসেস কনফিগারেশন অংশগুলি অনুলিপি ...
    
    // websocket_test প্রসেসের জন্য নাম
    'websocket_test' => [
        // এখানে ক্লাস নির্দিষ্ট করুন, এর ভেতরে দেওয়া Pusher ক্লাস পরিভাষিত হয়েছে
        'handler' => app\Pusher::class,
        'listen'  => 'websocket://0.0.0.0:8888',
        'count'   => 1,
    ],
];
```

## কাস্টম নন-লিসেনিং প্রসেসের উদাহরণ

app/TaskTest.php তৈরি করুন
```php
<?php
namespace app;

use Workerman\Timer;
use support\Db;

class TaskTest
{
  
    public function onWorkerStart()
    {
        // প্রতি 10 সেকেন্ড পর ডাটাবেসে নতুন ব্যবহারকারীর নিবন্ধন আছে কি তা যাচাই করুন
        Timer::add(10, function(){
            Db::table('users')->where('regist_timestamp', '>', time()-10)->get();
        });
    }
    
}
```
`config/process.php` এ নিম্নলিখিত কনফিগারেশন যুক্ত করুন
```php
return [
    // ... অন্যান্য প্রসেস কনফিগারেশন অংশগুলি অনুলিপি...
    
    'task' => [
        'handler'  => app\TaskTest::class
    ],
];
```
> লক্ষ্য রাখুন: লিসেনিং অংশ ছাড়াও যদি অনুরোধ না করা হয়, তবে প্রসেস সংখ্যা ডিফল্ট হিসেবে 1 হবে।

## কনফিগারেশন ফাইল ব্যাখ্যা

একটি প্রসেসেরের পূর্ণাঙ্গ কনফিগারেশনের ডিফাইনেশন নিম্নলিখিত ভাবে দেওয়া হয়:
```php
return [
    // ... 
    
    // websocket_test প্রসেসের জন্য নাম
    'websocket_test' => [
        // এখানে প্রসেসের ক্লাস নির্দিষ্ট করুন
        'handler' => app\Pusher::class,
        // শোনার প্রোটোকল, আইপি এবং পোর্ট (ঐচটিটিপি হতে পারে) (ঐচটিটিপি)
        'listen'  => 'websocket://0.0.0.0:8888',
        // প্রসেসের সংখ্যা (ঐচটিটিপি, ডিফল্টভাবে 1)
        'count'   => 2,
        // প্রসেসের হ্যান্ডলার ব্যবহারকারী (ঐচটিটিপি, ডিফল্টভাবে বর্তমান ব্যবহারকারী)
        'user'    => '',
        // প্রসেসের সংগঠন (ঐচটিটিপি, ডিফল্টভাবে বর্তমান ব্যবহারকারী গ্রুপ)
        'group'   => '',
        // বর্তমান প্রসেস পুনরায় লোড করা যায় (অতিথিন, ডিফল্টভাবে সত্য)
        'reloadable' => true,
        // সুরক্ষিত পোর্ট শুরু করা হবে (অতিথি, এই অপশনটির জন্য php> = 7.0 প্রয়োজন, ডিফল্টভাবে সত্য)
        'reusePort'  => true,
        // পরিবহন (অতিথি, সারস্বত HTTPS চালাতে প্রয়োজন হওয়ার পরে এটি ssl হতে হবে, ডিফল্টভাবে টিসিপি)
        'transport'  => 'tcp',
        // চিনতি (অতিথি, পরিবহন ssl হওয়ার পরে, সারস্বত পথ পাঠাতে হবে)
        'context'    => [], 
        // প্রসেস ক্লাসের কনস্ট্রাক্টর আর্গুমেন্ট, এখানে process\Pusher::class ক্লাসের কনস্ট্রাক্টর আর্গুমেন্ট (অতিথি)
        'constructor' => [],
    ],
];
```

## সংক্ষিপ্তসার
ওয়েবম্যান এর কাস্টম প্রসেস প্রাকৃতিকভাবে workerman এর একটি সহজ পাকেট করা, এটি কনফিগারেশন এবং ব্যবসা পৃথক করে দেয় এবং workerman এর `onXXX` কলব্যাকগুলি ক্লাস মেথড দিয়ে পাঠায়, অন্য ব্যবহার এবং workerman এর সাথে পুরোপুরি পরিপূর্ণ।
