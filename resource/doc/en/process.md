# Custom Processes

In webman you can customize the listener or process like workerman。

## Custom Listening Example

New `process/Pusher.php`
```php
<?php
namespace process;

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
> Note: all onXXX properties arepublic

Add the following configuration to `config/process.php`
```php
return [
    // ... Other process configurations are omitted
    
    // websocket_test For process names
    'websocket_test' => [
        // Specify the process class here, which is the Pusher class defined above
        'handler' => process\Pusher::class,
        'listen'  => 'websocket://0.0.0.0:8888',
        'count'   => 1,
    ],
];
```

## Custom non-listening process example
New `process/TaskTest.php`
```php
<?php
namespace process;

use Workerman\Timer;
use support\Db;

class TaskTest
{
  
    public function onWorkerStart()
    {
        // Check the database for new user registrations every 10 seconds
        Timer::add(10, function(){
            Db::table('users')->where('regist_timestamp', '>', time()-10)->get();
        });
    }
    
}
```
Add the following configuration to `config/process.php`
```php
return [
    // ... Other process configurations are omitted
    
    'task' => [
        'handler'  => process\TaskTest::class
    ],
];
```

> Note: listen is omitted to not listen to any ports, count is omitted to default the number of processes1。

## Configuration file description

The complete configuration of a process is defined as follows：
```php
return [
    // ... 
    
    // websocket_test For process names
    'websocket_test' => [
        // Specify process class here
        'handler' => process\Pusher::class,
        // Protocol ip and port to listen on (optional)）
        'listen'  => 'websocket://0.0.0.0:8888',
        // Number of processes (optional, default)1）
        'count'   => 2,
        // Process run user (optional, default current user)）
        'user'    => '',
        // Process run user group (optional, default current user group)）
        'group'   => '',
        // Whether the current process supports reload (optional, default)true）
        'reloadable' => true,
        // Whether to enable reusePort (optional, this option requires php>=7.0, default is true）
        'reusePort'  => true,
        // transport (Optional, set to ssl when ssl needs to be enabled, default istcp)
        'transport'  => 'tcp',
        // context （Optionally, when the transport is ssl, the certificate path needs to be passed）
        'context'    => [], 
        // process class constructor parameters，here for process\Pusher::class Class constructor parameters （Optional）
        'constructor' => [],
    ],
];
```

## Summary
webman的Custom ProcessesSee moreworkermanA simple wrapper for，It separates the configuration from the business，and willworkerman的`onXXX`Callbacks are implemented through methods of the class，microservices and so onworkermanExactly the same。