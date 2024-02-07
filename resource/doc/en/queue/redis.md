## Redis Queue

A message queue based on Redis, supports delayed message processing.

## Installation
`composer require webman/redis-queue`

## Configuration File
The Redis configuration file is automatically generated in `config/plugin/webman/redis-queue/redis.php`, and its content is similar to the following:
```php
<?php
return [
    'default' => [
        'host' => 'redis://127.0.0.1:6379',
        'options' => [
            'auth' => '',       // Password, optional
            'db' => 0,          // Database
            'max_attempts'  => 5, // Retry times after consumption failure
            'retry_seconds' => 5, // Retry interval in seconds
        ]
    ],
];
```

### Consumption Failure Retry
If a consumption failure (exception occurs), the message will be placed in the delayed queue and wait for the next retry. The retry times is controlled by the parameter `max_attempts`, while the retry interval is jointly controlled by `retry_seconds` and `max_attempts`. For example, if `max_attempts` is 5 and `retry_seconds` is 10, the interval for the first retry is `1*10` seconds, the interval for the second retry is `2*10` seconds, and so on, up to 5 retries. If the retry times exceeds the `max_attempts` setting, the message will be placed in the failed queue with the key `{redis-queue}-failed`.

## Message Delivery (Synchronous)
> **Note**
> Requires webman/redis >= 1.2.0, dependent on the redis extension

```php
<?php
namespace app\controller;

use support\Request;
use Webman\RedisQueue\Redis;

class Index
{
    public function queue(Request $request)
    {
        // Queue name
        $queue = 'send-mail';
        // Data, can be passed as an array without serialization
        $data = ['to' => 'tom@gmail.com', 'content' => 'hello'];
        // Deliver the message
        Redis::send($queue, $data);
        // Deliver delayed message, to be processed after 60 seconds
        Redis::send($queue, $data, 60);

        return response('redis queue test');
    }

}
```
Upon successful delivery, `Redis::send()` returns true, otherwise it returns false or throws an exception.

> **Tip**
> There may be some deviation in the delayed queue consumption time. For example, if the consumption speed is slower than the production speed, causing queue backlog and consequently consumption delay, the solution is to open more consumption processes.

## Message Delivery (Asynchronous)
```php
<?php
namespace app\controller;

use support\Request;
use Webman\RedisQueue\Client;

class Index
{
    public function queue(Request $request)
    {
        // Queue name
        $queue = 'send-mail';
        // Data, can be passed as an array without serialization
        $data = ['to' => 'tom@gmail.com', 'content' => 'hello'];
        // Deliver the message
        Client::send($queue, $data);
        // Deliver delayed message, to be processed after 60 seconds
        Client::send($queue, $data, 60);

        return response('redis queue test');
    }

}
```
`Client::send()` does not return a value. It is an asynchronous push and does not guarantee 100% delivery to Redis.

> **Tip**
> The principle of `Client::send()` is to establish an in-memory queue in the local memory and asynchronously synchronize the messages to Redis (the synchronization speed is very fast, about 10,000 messages per second). If the process restarts and the data in the local memory queue has not been synchronized, it may cause message loss. `Client::send()` is suitable for asynchronous delivery of unimportant messages.

> **Tip**
> `Client::send()` is asynchronous and can only be used in the workerman runtime environment. For command line scripts, use the synchronous interface `Redis::send()`.

## Delivering Messages in Other Projects
Sometimes you may need to deliver messages in other projects and cannot use `webman\redis-queue`. In this case, you can refer to the following function to deliver messages to the queue.

```php
function redis_queue_send($redis, $queue, $data, $delay = 0) {
    $queue_waiting = '{redis-queue}-waiting';
    $queue_delay = '{redis-queue}-delayed';
    $now = time();
    $package_str = json_encode([
        'id'       => rand(),
        'time'     => $now,
        'delay'    => $delay,
        'attempts' => 0,
        'queue'    => $queue,
        'data'     => $data
    ]);
    if ($delay) {
        return $redis->zAdd($queue_delay, $now + $delay, $package_str);
    }
    return $redis->lPush($queue_waiting.$queue, $package_str);
}
```

In the function, the parameter `$redis` is the Redis instance. For example, the usage of the redis extension is similar to the following:
```php
$redis = new Redis;
$redis->connect('127.0.0.1', 6379);
$queue = 'user-1';
$data= ['some', 'data'];
redis_queue_send($redis, $queue, $data);
```

## Consumption
The consumer process configuration file is in `config/plugin/webman/redis-queue/process.php`. The consumer directory is in `app/queue/redis/`.

Executing the command `php webman redis-queue:consumer my-send-mail` will generate the file `app/queue/redis/MyMailSend.php`.

> **Tip**
> If the command does not exist, you can also generate it manually.

```php
<?php

namespace app\queue\redis;

use Webman\RedisQueue\Consumer;

class MyMailSend implements Consumer
{
    // Queue name to consume
    public $queue = 'send-mail';

    // Connection name, corresponding to the connection in `plugin/webman/redis-queue/redis.php`
    public $connection = 'default';

    // Consumption
    public function consume($data)
    {
        // No need for deserialization
        var_export($data); // Outputs ['to' => 'tom@gmail.com', 'content' => 'hello']
    }
}
```

> **Note**
> The consumption process without throwing exceptions and errors is considered successful consumption. Otherwise, it is considered consumption failure and will enter the retry queue. Redis-queue does not have an ack mechanism. You can consider it as an automatic ack (no exceptions or errors). If you want to mark the current message as not successfully consumed during consumption, you can manually throw an exception to put the current message into the retry queue. In practice, this is no different from an ack mechanism.

> **Tip**
> The consumer supports multiple servers and processes, and the same message will not be consumed repeatedly. Consumed messages will be automatically removed from the queue, no need to manually delete them.

> **Tip**
> Consumption processes can consume multiple different queues at the same time. Adding a new queue does not require modification of the configuration in `process.php`. When adding a new queue consumer, only a corresponding `Consumer` class needs to be added under `app/queue/redis`, and use the class attribute `$queue` to specify the queue name to be consumed.

> **Tip**
> Windows users need to execute `php windows.php` to start webman, otherwise the consumption process will not start.

## Setting Different Consumption Processes for Different Queues
By default, all consumers share the same consumption process. However, sometimes we need to separate the consumption of some queues, for example, slow business consumption in one group of processes, and fast business consumption in another group of processes. To achieve this, we can divide the consumers into two directories, for example, `app_path() . '/queue/redis/fast'` and `app_path() . '/queue/redis/slow'` (note that the namespace of the consumption class needs to be correspondingly modified). Then the configuration is as follows:
```php
return [
    ...other configurations here...
    
    'redis_consumer_fast'  => [
        'handler'     => Webman\RedisQueue\Process\Consumer::class,
        'count'       => 8,
        'constructor' => [
            // Consumer class directory
            'consumer_dir' => app_path() . '/queue/redis/fast'
        ]
    ],
    'redis_consumer_slow'  => [
        'handler'     => Webman\RedisQueue\Process\Consumer::class,
        'count'       => 8,
        'constructor' => [
            // Consumer class directory
            'consumer_dir' => app_path() . '/queue/redis/slow'
        ]
    ]
];
```

By categorizing directories and configuring accordingly, we can easily set different consumption processes for different consumers.
## Multiple Redis Configurations
### Configuration
`config/plugin/webman/redis-queue/redis.php`
```php
<?php
return [
    'default' => [
        'host' => 'redis://192.168.0.1:6379',
        'options' => [
            'auth' => null,       // password, string type, optional
            'db' => 0,            // database
            'max_attempts'  => 5, // retry times after consumption failure
            'retry_seconds' => 5, // retry interval in seconds
        ]
    ],
    'other' => [
        'host' => 'redis://192.168.0.2:6379',
        'options' => [
            'auth' => null,       // password, string type, optional
            'db' => 0,            // database
            'max_attempts'  => 5, // retry times after consumption failure
            'retry_seconds' => 5, // retry interval in seconds
        ]
    ],
];
```

Note that an additional configuration for `other` key has been added to the configuration.

### Publishing Messages to Multiple Redis
```php
// publish message to the queue with key `default`
Client::connection('default')->send($queue, $data);
Redis::connection('default')->send($queue, $data);
//  the same as
Client::send($queue, $data);
Redis::send($queue, $data);

// publish message to the queue with key `other`
Client::connection('other')->send($queue, $data);
Redis::connection('other')->send($queue, $data);
```

### Consuming from Multiple Redis
Consuming messages from the queue with key `other` in the configuration
```php
namespace app\queue\redis;

use Webman\RedisQueue\Consumer;

class SendMail implements Consumer
{
    // queue name to consume from
    public $queue = 'send-mail';

    // === set to 'other' here, indicating the queue key in the consumption configuration is 'other' ===
    public $connection = 'other';

    // consumption
    public function consume($data)
    {
        // deserialization is not needed
        var_export($data);
    }
}
```

## FAQs
**Why do I get the error `Workerman\Redis\Exception: Workerman Redis Wait Timeout (600 seconds)`?**

This error only occurs in the asynchronous publishing interface `Client::send()`. Asynchronous publishing first saves the message in local memory and then sends it to Redis when the process is idle. If the speed at which Redis receives messages is slower than the message production speed, or if the process is busy with other tasks and does not have enough time to synchronize the messages from memory to Redis, it can cause message congestion. If there is message congestion for more than 600 seconds, this error will be triggered.

Solution: Use the synchronous publishing interface `Redis::send()` for message publishing.
