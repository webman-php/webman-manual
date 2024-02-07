# Session management

## Example
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

Get the `Workerman\Protocols\Http\Session` instance through `$request->session();` and use its methods to add, modify, or delete session data.

> Note: When the session object is destroyed, the session data will be automatically saved. Therefore, do not save the object returned by `$request->session()` in a global array or a class member, which can prevent the session from being saved.


## Get all session data
```php
$session = $request->session();
$all = $session->all();
```
It returns an array. If there is no session data, it returns an empty array.


## Get a value from the session
```php
$session = $request->session();
$name = $session->get('name');
```
If the data does not exist, it returns null.

You can also pass a default value as the second parameter to the `get` method. If the corresponding value is not found in the session array, it returns the default value. For example:
```php
$session = $request->session();
$name = $session->get('name', 'tom');
```


## Store session data
Use the `set` method to store a particular piece of data.
```php
$session = $request->session();
$session->set('name', 'tom');
```
The `set` method does not return a value. When the session object is destroyed, the session will be automatically saved.

When storing multiple values, use the `put` method.
```php
$session = $request->session();
$session->put(['name' => 'tom', 'age' => 12]);
```
Similarly, `put` does not return a value.


## Delete session data
Use the `forget` method to delete one or more session data.
```php
$session = $request->session();
// Delete a single item
$session->forget('name');
// Delete multiple items
$session->forget(['name', 'age']);
```

Additionally, the system provides a `delete` method, which, unlike `forget`, can only delete one item.
```php
$session = $request->session();
// Equivalent to $session->forget('name');
$session->delete('name');
```


## Get and delete a value from the session
```php
$session = $request->session();
$name = $session->pull('name');
```
The effect is the same as the following code:
```php
$session = $request->session();
$value = $session->get($name);
$session->delete($name);
```
If the corresponding session does not exist, it returns null.


## Delete all session data
```php
$request->session()->flush();
```
It does not return a value. When the session object is destroyed, the session data will be automatically removed from the storage.


## Check if a specific session data exists
```php
$session = $request->session();
$has = $session->has('name');
```
If the corresponding session does not exist or the session value is null, it returns false; otherwise, it returns true.

```php
$session = $request->session();
$has = $session->exists('name');
```
The above code is also used to check if the session data exists. The difference is that it returns true even if the corresponding session item value is null.


## Helper function `session()`
> Added on 2020-12-09

webman provides the helper function `session()` to achieve the same functionality.
```php
// Get the session instance
$session = session();
// Equivalent to
$session = $request->session();

// Get a value
$value = session('key', 'default');
// Equivalent to
$value = session()->get('key', 'default');
// Equivalent to
$value = $request->session()->get('key', 'default');

// Assign values to the session
session(['key1'=>'value1', 'key2' => 'value2']);
// Equivalent to
session()->put(['key1'=>'value1', 'key2' => 'value2']);
// Equivalent to
$request->session()->put(['key1'=>'value1', 'key2' => 'value2']);

```


## Configuration file
The session configuration file is located at `config/session.php` and its content is similar to the following:
```php
use Webman\Session\FileSessionHandler;
use Webman\Session\RedisSessionHandler;
use Webman\Session\RedisClusterSessionHandler;

return [
    // FileSessionHandler::class or RedisSessionHandler::class or RedisClusterSessionHandler::class 
    'handler' => FileSessionHandler::class,
    
    // When handler is FileSessionHandler::class, the value is 'file',
    // When handler is RedisSessionHandler::class, the value is 'redis',
    // When handler is RedisClusterSessionHandler::class, the value is 'redis_cluster' (Redis cluster)
    'type'    => 'file',

    // Different handlers use different configurations
    'config' => [
        // Configuration for type 'file'
        'file' => [
            'save_path' => runtime_path() . '/sessions',
        ],
        // Configuration for type 'redis'
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

    'session_name' => 'PHPSID', // Cookie name for storing the session Id
    
    // === The following configuration requires webman-framework>=1.3.14 workerman>=4.0.37 ===
    'auto_update_timestamp' => false,  // Whether to automatically refresh the session, default is off
    'lifetime' => 7*24*60*60,          // Session expiration time
    'cookie_lifetime' => 365*24*60*60, // Cookie expiration time for storing the session Id
    'cookie_path' => '/',              // Cookie path for storing the session Id
    'domain' => '',                    // Cookie domain for storing the session Id
    'http_only' => true,               // Whether to enable httpOnly, default is enabled
    'secure' => false,                 // Enable session only in HTTPS, default is off
    'same_site' => '',                 // Used to prevent CSRF attacks and user tracking, optional values: strict/lax/none
    'gc_probability' => [1, 1000],     // Probability of session garbage collection
];
```

> **Note** 
> Starting from webman 1.4.0, the namespace of SessionHandler has been changed from:
> use Webman\FileSessionHandler;  
> use Webman\RedisSessionHandler;  
> use Webman\RedisClusterSessionHandler;  
> to
> use Webman\Session\FileSessionHandler;  
> use Webman\Session\RedisSessionHandler;  
> use Webman\Session\RedisClusterSessionHandler;  



## Expiry configuration
When webman-framework < 1.3.14, the session expiry time in webman needs to be configured in `php.ini`.

```ini
session.gc_maxlifetime = x
session.cookie_lifetime = x
session.gc_probability = 1
session.gc_divisor = 1000
```

Assuming the expiry time is 1440 seconds, the configuration will be as follows:
```ini
session.gc_maxlifetime = 1440
session.cookie_lifetime = 1440
session.gc_probability = 1
session.gc_divisor = 1000
```

> **Hint**
> You can use the `php --ini` command to find the location of `php.ini`.
