# session管理

## 例子
```php
<?php
namespace app\controller;

use support\Request;

class User
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
以上代码也是用来判断session数据是否存在，区别时当对应的session项值为null时，也返回true。

## 配置文件
session配置文件在`config/session.php`，内容类似如下：
```php
return [
    // Webman\FileSessionHandler::class 或者 Webman\RedisSessionHandler::class 或者 Webman\RedisClusterSessionHandler::class 
    'handler' => Webman\FileSessionHandler::class,
    
    // handler为Webman\FileSessionHandler::class时值为file，
    // handler为Webman\RedisSessionHandler::class时值为redis
    // handler为Webman\RedisClusterSessionHandler::class时值为redis_cluster 既redis集群
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

    'session_name' => 'PHPSID',
];
```

> 注意：`Webman\RedisClusterSessionHandler::class` 需要 webman-framework >= 1.0.4
