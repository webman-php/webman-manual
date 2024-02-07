# Custom Processes

In webman, you can customize listeners or processes just like workerman.

> **Note**
> Windows users need to use `php windows.php` to start webman in order to run custom processes.

## Custom HTTP Service
Sometimes you may have a special requirement to modify the core code of the webman HTTP service. In such cases, you can use a custom process to achieve this.

For example, create a new file `app\Server.php`.

```php
<?php

namespace app;

use Webman\App;

class Server extends App
{
    // Override methods from Webman\App here
}
```

Add the following configuration to `config/process.php`.

```php
use Workerman\Worker;

return [
    // ... other configurations are omitted ...

    'my-http' => [
        'handler' => app\Server::class,
        'listen' => 'http://0.0.0.0:8686',
        'count' => 8, // Number of processes
        'user' => '',
        'group' => '',
        'reusePort' => true,
        'constructor' => [
            'request_class' => \support\Request::class, // Set request class
            'logger' => \support\Log::channel('default'), // Logger instance
            'app_path' => app_path(), // Location of app directory
            'public_path' => public_path() // Location of public directory
        ]
    ]
];
```

> **Tip**
> To disable the built-in HTTP process of webman, simply set `listen=>''` in the `config/server.php` file.

## Custom Websocket Listener Example
Create `app/Pusher.php`.

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

> Note: All `onXXX` methods are public.

Add the following configuration to `config/process.php`.

```php
return [
    // ... Other process configurations are omitted ...

    // websocket_test is the process name
    'websocket_test' => [
        // Specify the process class as the one defined above
        'handler' => app\Pusher::class,
        'listen'  => 'websocket://0.0.0.0:8888',
        'count'   => 1,
    ],
];
```

## Custom Non-listening Process Example
Create `app/TaskTest.php`.

```php
<?php
namespace app;

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
Add the following configuration to `config/process.php`.

```php
return [
    // ... Other process configurations are omitted ...

    'task' => [
        'handler'  => app\TaskTest::class
    ],
];
```

> Note: If listen is omitted, the process will not listen on any port. If count is omitted, the default number of processes is 1.

## Configuration File Explanation
The complete definition of a process configuration is as follows:

```php
return [
    // ... 

    // websocket_test is the process name
    'websocket_test' => [
        // Specify the process class here
        'handler' => app\Pusher::class,
        // Protocol, IP, and port to listen on (optional)
        'listen'  => 'websocket://0.0.0.0:8888',
        // Number of processes (optional, default is 1)
        'count'   => 2,
        // User to run the process (optional, default is the current user)
        'user'    => '',
        // User group to run the process (optional, default is the current user group)
        'group'   => '',
        // Whether the current process supports reload (optional, default is true)
        'reloadable' => true,
        // Whether to enable reusePort (optional, requires php>=7.0, default is true)
        'reusePort'  => true,
        // Transport (optional, set to 'ssl' when SSL is required, default is 'tcp')
        'transport'  => 'tcp',
        // Context (optional, pass certificate path when transport is 'ssl')
        'context'    => [], 
        // Constructor parameters for the process class, here for process\Pusher::class (optional)
        'constructor' => [],
    ],
];
```

## Conclusion
The custom processes in webman are actually a simple encapsulation of workerman. It separates configuration from business logic and implements workerman's `onXXX` callbacks through class methods, making it fully compatible with workerman's other usage.
