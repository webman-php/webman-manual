# session管理

## 例子
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

通过`$request->session();` 获得`Workerman\Protocols\Http\Session`实例，通过实例的方法来增加、修改、删除session数据。

> 注意：session对象销毁时会自动保存session数据，所以不要把`$request->session()`返回的对象保存在全局数组或者类成员中导致session无法保存。

## 获取所有session数据
```php
$session = $request->session();
$all = $session->all();
```
返回的是一个数组。如果没有任何session数据，则返回一个空数组。



## 获取session中某个值
```php
$session = $request->session();
$name = $session->get('name');
```
如果数据不存在则返回null。

你也可以给get方法第二个参数传递一个默认值，如果session数组中没找到对应值则返回默认值。例如：
```php
$session = $request->session();
$name = $session->get('name', 'tom');
```


## 存储session
存储某一项数据时用set方法。
```php
$session = $request->session();
$session->set('name', 'tom');
```
set没有返回值，session对象销毁时session会自动保存。

当存储多个值时使用put方法。
```php
$session = $request->session();
$session->put(['name' => 'tom', 'age' => 12]);
```
同样的，put也没有返回值。

## 删除session数据
删除某个或者某些session数据时用`forget`方法。
```php
$session = $request->session();
// 删除一项
$session->forget('name');
// 删除多项
$session->forget(['name', 'age']);
```

另外系统提供了delete方法，与forget方法区别是，delete只能删除一项。
```php
$session = $request->session();
// 等同于 $session->forget('name');
$session->delete('name');
```

## 获取并删除session某个值
```php
$session = $request->session();
$name = $session->pull('name');
```
效果与如下代码相同
```php
$session = $request->session();
$value = $session->get($name);
$session->delete($name);
```
如果对应session不存在，则返回null。


## 删除所有session数据
```php
$request->session()->flush();
```
没有返回值，session对象销毁时session会自动从存储中删除。


## 判断对应session数据是否存在
```php
$session = $request->session();
$has = $session->has('name');
```
以上当对应的session不存在或者对应的session值为null时返回false，否则返回true。

```
$session = $request->session();
$has = $session->exists('name');
```
以上代码也是用来判断session数据是否存在，区别是当对应的session项值为null时，也返回true。

## 助手函数session()
> 2020-12-09 新增

webman提供了助手函数`session()`完成相同的功能。
```php
// 获取session实例
$session = session();
// 等价于
$session = $request->session();

// 获取某个值
$value = session('key', 'default');
// 等价与
$value = session()->get('key', 'default');
// 等价于
$value = $request->session()->get('key', 'default');

// 给session赋值
session(['key1'=>'value1', 'key2' => 'value2']);
// 相当于
session()->put(['key1'=>'value1', 'key2' => 'value2']);
// 相当于
$request->session()->put(['key1'=>'value1', 'key2' => 'value2']);

```

## 配置文件
session配置文件在`config/session.php`，内容类似如下：
```php
use Webman\Session\FileSessionHandler;
use Webman\Session\RedisSessionHandler;
use Webman\Session\RedisClusterSessionHandler;

return [
    // FileSessionHandler::class 或者 RedisSessionHandler::class 或者 RedisClusterSessionHandler::class 
    'handler' => FileSessionHandler::class,
    
    // handler为FileSessionHandler::class时值为file，
    // handler为RedisSessionHandler::class时值为redis
    // handler为RedisClusterSessionHandler::class时值为redis_cluster 既redis集群
    'type'    => 'file',

    // 不同的handler使用不同的配置
    'config' => [
        // type为file时的配置
        'file' => [
            'save_path' => runtime_path() . '/sessions',
        ],
        // type为redis时的配置
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

    'session_name' => 'PHPSID', // 存储session_id的cookie名
    'auto_update_timestamp' => false,  // 是否自动刷新session，默认关闭
    'lifetime' => 7*24*60*60,          // session过期时间
    'cookie_lifetime' => 365*24*60*60, // 存储session_id的cookie过期时间
    'cookie_path' => '/',              // 存储session_id的cookie路径
    'domain' => '',                    // 存储session_id的cookie域名
    'http_only' => true,               // 是否开启httpOnly，默认开启
    'secure' => false,                 // 仅在https下开启session，默认关闭
    'same_site' => '',                 // 用于防止CSRF攻击和用户追踪，可选值strict/lax/none
    'gc_probability' => [1, 1000],     // 回收session的几率
];
```

> **注意** 
> webman从1.4.0起更改了SessionHandler的命名空间，由原来的
> use Webman\FileSessionHandler;  
> use Webman\RedisSessionHandler;  
> use Webman\RedisClusterSessionHandler;  
> 改为  
> use Webman\Session\FileSessionHandler;  
> use Webman\Session\RedisSessionHandler;  
> use Webman\Session\RedisClusterSessionHandler;  



## 有效期配置
当webman-framework < 1.3.14时，webman中session过期时间需要在`php.ini`配置。

```
session.gc_maxlifetime = x
session.cookie_lifetime = x
session.gc_probability = 1
session.gc_divisor = 1000
```

假设设定有效期为1440秒，则配置如下
```
session.gc_maxlifetime = 1440
session.cookie_lifetime = 1440
session.gc_probability = 1
session.gc_divisor = 1000
```

> **提示**
> 可使用命令 `php --ini` 来查找`php.ini`的位置