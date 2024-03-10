# event事件处理
`webman/event` 提供一种精巧的事件机制，可实现在不侵入代码的情况下执行一些业务逻辑，实现业务模块之间的解耦。典型的场景如一个新用户注册成功时，只要发布一个自定义事件如`user.register`，各个模块遍能收到该事件执行相应的业务逻辑。

## 安装
`composer require webman/event`

## 订阅事件
订阅事件统一通过文件`config/event.php`来配置
```php
<?php
return [
    'user.register' => [
        [app\event\User::class, 'register'],
        // ...其它事件处理函数...
    ],
    'user.logout' => [
        [app\event\User::class, 'logout'],
        // ...其它事件处理函数...
    ]
];
```
**说明：**
- `user.register` `user.logout` 等是事件名称，字符串类型，建议小写单词并以点(`.`)分割
- 一个事件可以对应多个事件处理函数，调用顺序为配置的顺序

## 事件处理函数
事件处理函数可以是任意的类方法、函数、闭包函数等。
例如创建事件处理类 `app/event/User.php` (目录不存在请自行创建)
```php
<?php
namespace app\event;
class User
{
    function register($user)
    {
        var_export($user);
    }
 
    function logout($user)
    {
        var_export($user);
    }
}
```

## 发布事件
使用 `Event::dispatch($event_name, $data);` 或 `Event::emit($event_name, $data);` 发布事件，例如
```php
<?php
namespace app\controller;
use support\Request;
use Webman\Event\Event;
class User
{
    public function register(Request $request)
    {
        $user = [
            'name' => 'webman',
            'age' => 2
        ];
        Event::dispatch('user.register', $user);
    }
}
```

发布事件有两个函数，`Event::dispatch($event_name, $data);` 和 `Event::emit($event_name, $data);` 二者参数一样。
区别是emit内部会自动捕获异常，也就是说如果一个事件有多个处理函数，某个处理函数发生异常不会影响其它处理函数的执行。
而dispatch则内部不会自动捕获异常，当前事件的任何一个处理函数发生异常，则停止执行下一个处理函数并直接向上抛出异常。

> **提示**
> 参数$data可以是任意的数据，例如数组、类实例、字符串等


## 通配符事件监听
通配符注册监听允许您在同一个监听器上处理多个事件，例如`config/event.php`里配置
```php
<?php
return [
    'user.*' => [
        [app\event\User::class, 'deal']
    ],
];
```
我们可以通过事件处理函数第二个参数`$event_data`获得具体的事件名
```php
<?php
namespace app\event;
class User
{
    function deal($user, $event_name)
    {
        echo $event_name; // 具体的事件名，如 user.register user.logout 等
        var_export($user);
    }
}
```

## 停止事件广播
当我们在事件处理函数里返回`false`时，该事件将停止广播

## 闭包函数处理事件
事件处理函数可以是类方法，也可以是闭包函数例如

```php
<?php
return [
    'user.login' => [
        function($user){
            var_dump($user);
        }
    ]
];
```

##  查看事件及监听器
使用命令 `php webman event:list` 查看项目配置的所有事件及监听器

## 注意事项
event事件处理并不是异步的，event不适合处理慢业务，慢业务应该用消息队列处理，例如[webman/redis-queue](https://www.workerman.net/plugin/12)
