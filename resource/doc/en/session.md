# sessionManage

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

Pass`$request->session();` get`Workerman\Protocols\Http\Session`instance，Methods to add by instance、modify、Delete session data。

> Note：sessionObjects are automatically saved when they are destroyedsessiondata，Create one on`$request->session()`The returned objects are saved in global arrays or class members resulting insessionwhen the content of。

## Get all session data
```php
$session = $request->session();
$all = $session->all();
```
Returns an array. If there is no session data, then an empty array is returned。



## Get a value in session
```php
$session = $request->session();
$name = $session->get('name');
```
Return if data does not existnull。

You can also pass a default value to the second parameter of the get method, and return the default value if the corresponding value is not found in the session array. For example：
```php
$session = $request->session();
$name = $session->get('name', 'tom');
```


## Storagesession
Use set method when storing a particular piece of data。
```php
$session = $request->session();
$session->set('name', 'tom');
```
setNo return value, session is automatically saved when session object is destroyed。

Use the put method when storing multiple values。
```php
$session = $request->session();
$session->put(['name' => 'tom', 'age' => 12]);
```
Similarly, put does not return a value。

## Delete session data
Use the `forget` method when deleting some or all session data。
```php
$session = $request->session();
// Delete one item
$session->forget('name');
// Delete multiple
$session->forget(['name', 'age']);
```

In addition, the system provides the delete method, the difference with the forget method is that delete can only delete one item。
```php
$session = $request->session();
// Equivalent to $session->forget('name');
$session->delete('name');
```

## Get and delete a value of session
```php
$session = $request->session();
$name = $session->pull('name');
```
The effect is the same as the following code
```php
$session = $request->session();
$value = $session->get($name);
$session->delete($name);
```
If the corresponding session does not exist, returnnull。


## Delete all session data
```php
$request->session()->flush();
```
No return value, session is automatically removed from storage when the session object is destroyed。


## determine if the corresponding session data exists
```php
$session = $request->session();
$has = $session->has('name');
```
The above returns false when the corresponding session does not exist or when the corresponding session value is null, otherwise it returnstrue。

```
$session = $request->session();
$has = $session->exists('name');
```
The above code is also used to determine whether session data exists, the difference is that when the corresponding session item value is null, it also returnstrue。

## Helper functionssession()
> 2020-12-09 Add

webmanHelper function `session()` is provided to accomplish the same functionality。
```php
// Get session instance
$session = session();
// Equivalent to
$session = $request->session();

// Get a value
$value = session('key', 'default');
// Equivalent to
$value = session()->get('key', 'default');
// Equivalent to
$value = $request->session()->get('key', 'default');

// Assign a value to session
session(['key1'=>'value1', 'key2' => 'value2']);
// equivalent
session()->put(['key1'=>'value1', 'key2' => 'value2']);
// equivalent
$request->session()->put(['key1'=>'value1', 'key2' => 'value2']);

```

## configuration file
sessionThe configuration file is in `config/session.php` and the content is similar to the following：
```php
use Webman\Session\FileSessionHandler;
use Webman\Session\RedisSessionHandler;
use Webman\Session\RedisClusterSessionHandler;

return [
    // FileSessionHandler::class or RedisSessionHandler::class or RedisClusterSessionHandler::class 
    'handler' => FileSessionHandler::class,
    
    // handlerFor FileSessionHandler::class with value file，
    // handlerfor RedisSessionHandler::class when the value isredis
    // handlerfor RedisClusterSessionHandler::class when the value is redis_cluster Both redis cluster
    'type'    => 'file',

    // Different handlers use different configurations
    'config' => [
        // typeConfigure for file when
        'file' => [
            'save_path' => runtime_path() . '/sessions',
        ],
        // typeConfiguration when for redis
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

    'session_name' => 'PHPSID', // Cookie name to store session_id
    
    // === Required for the following configurations webman-framework>=1.3.14 workerman>=4.0.37 ===
    'auto_update_timestamp' => false,  // Whether to automatically refresh the session, default off
    'lifetime' => 7*24*60*60,          // sessionExpiration time
    'cookie_lifetime' => 365*24*60*60, // Store cookie expiration time for session_id
    'cookie_path' => '/',              // Path to the cookie where session_id is stored
    'domain' => '',                    // Cookie domain name where session_id is stored
    'http_only' => true,               // Whether to enable httpOnly, default is on
    'secure' => false,                 // Turn on session only under https, off by default
    'same_site' => '',                 // Used to prevent CSRF attacks and user tracking, optional valuestrict/lax/none
    'gc_probability' => [1, 1000],     // Chance to recycle session
];
```

> **Note** 
> webmanChanged the namespace of SessionHandler from 1.4.0 onwards, from the original 
> use Webman\FileSessionHandler;  
> use Webman\RedisSessionHandler;  
> use Webman\RedisClusterSessionHandler;  
> read  
> use Webman\Session\FileSessionHandler;  
> use Webman\Session\RedisSessionHandler;  
> use Webman\Session\RedisClusterSessionHandler;  



## Expiration Date Configuration
When webman-framework < 1.3.14, the session expiration time in webman needs to be configured in `php.ini`。

```
session.gc_maxlifetime = x
session.cookie_lifetime = x
session.gc_probability = 1
session.gc_divisor = 1000
```

Assuming a validity of 1440 seconds, configure the following
```
session.gc_maxlifetime = 1440
session.cookie_lifetime = 1440
session.gc_probability = 1
session.gc_divisor = 1000
```

> **hint**
> You can use the command `php --ini` to find the location of `php.ini`