## Stomp কিউ

Stomp হল একটি সিম্পল (স্ট্রিমিং) টেক্সট বেস ম্যাসেজ প্রোটোকল, যা একটি ইন্টারঅপারেবল কানেক্ট ফরম্যাট সরবরাহ করে এবং একটি STOMP ক্লায়েন্টকে যে কোনও STOMP ম্যাসেজ ব্রোকার (Broker) এর সাথে ইন্টারঅপারেবল করার অনুমতি দেয়। [workerman/stomp](https://github.com/walkor/stomp) stomp ক্লায়েন্টের অনুরূপ, প্রধানত RabbitMQ, Apollo, ActiveMQ ইত্যাদি ম্যাসেজ কিউ সিনারিও এপ্লিকেশনের জন্য ব্যবহৃত।

## Install
`composer require webman/stomp`

## Configure
কনফিগারেশন ফাইল এর অধীনে `config/plugin/webman/stomp`

## ম্যাসেজ ডেলিভারি
```php
<?php
namespace app\controller;

use support\Request;
use Webman\Stomp\Client;

class Index
{
    public function queue(Request $request)
    {
        // কিউ
        $queue = 'examples';
        // ডেটা (অ্যারে পাস করাতে হলে ডেটা সিরিয়ালাইজেশনের প্রয়োজন হবে, উদাহরণস্বরূপ json_encode, serialize ইত্যাদি ব্যবহার করুন)
        $data = json_encode(['to' => 'tom@gmail.com', 'content' => 'hello']);
        // ডেলিভারি কার্যকর করুন
        Client::send($queue, $data);

        return response('redis queue test');
    }

}
```
> অন্যান্য প্রজেক্টের সাথে সামঞ্জস্যপূর্ণতা সংযোগ নিয়ে বিবেচনা করে, stomp প্লাগিন স্বয়ংক্রিয় সিরিয়ালাইজেশন এবং ডিসিরিয়ালাইজেশন সুযোগ সরবরাহ করে না। যদি আপনি একটি অ্যারে ডাটা পাঠান, তবে আপনার নিজেরা সিরিয়ালাইজেশন করতে হবে এবং আপনার নিজেরা ডিসিরিয়ালাইজেশন করতে হবে।

## ম্যাসেজ কনসিউম
`app/queue/stomp/MyMailSend.php` তৈরি করুন (ক্লাস নাম সাধারণভাবে psr4 মান অনুসারে দিতে হবে)।
```php
<?php
namespace app\queue\stomp;

use Workerman\Stomp\AckResolver;
use Webman\Stomp\Consumer;

class MyMailSend implements Consumer
{
    // কিউ নাম
    public $queue = 'examples';

    // কানেকশন নাম, stomp.php ফাইলে নির্ধারিত কানেকশন সাথে মিলবে`
    public $connection = 'default';

    // যখন মান client তখন সার্ভারকে সফলভাবে কনসিউম হলে, $ack_resolver->ack() কল করতে হবে
    // যখন মান auto হল তখন $ack_resolver->ack() কল করা প্রয়োজন নেই
    public $ack = 'auto';

    // কনসিউম করুন
    public function consume($data, AckResolver $ack_resolver = null)
    {
        // যদি ডাটা অ্যারে হয়, তবে সিরিয়ালাইজেশনের প্রয়োজন
        var_export(json_decode($data, true)); // আউটপুট ['to' => 'tom@gmail.com', 'content' => 'hello']
        // কানেকশন সার্ভারে বলুন যে, ম্যাসেজটি সফলভাবে কনসিউম করা হয়েছে
        $ack_resolver->ack(); // যদি ack মান auto হয়, তবে এই কল অনুপ্রেরণ করা যেতে পারে
    }
}
```

# র‌্‌‌্‌‌‌‌‌‌‌‌‌‌‌‌‌‌‌।यब्‌‌‌‌‌‌‌‌‌‌न‌‌ratmqটीন(तेर्stompद्वनेरीदेस्सीखेजनायलत‌नेगर)

र‌्‌‌‌‌‌‌‌‌‌‌‌‌‌‌‌‌‌‌।यब्‌‌‌‌‌‌‌‌‌‌न‌‌ratmqतूूटूयल्राउदफेरदेरलाटयबरगरतयलसयकरलाडेकल
```shell
rabbitmq-plugins enable rabbitmq_stomp
```दारुन् useClassकरी

ratmqजरबएदचेstompतुल्यषमुलर1010थेवाशहाइलदरारदीगरतल।दानकेंरSelf-contained activities environment created to interact with customers 
