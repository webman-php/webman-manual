#限流器
webman限流器，支持注解限流。
支持apcu、redis、memory驱动。

##源码地址
https://github.com/webman-php/rate-limiter

##安装
```
composer require webman/rate-limiter
```

##使用
```php
<?php
namespace app\controller;

use RuntimeException;
use Webman\RateLimiter\Annotation\RateLimiter;

class UserController
{

    #[RateLimiter(limit: 10)]
    public function index(): string
    {
        // 默认为IP限流，默认单位时间为1秒
        return '每个ip每秒最多10个请求';
    }

    #[RateLimiter(limit: 100, ttl: 60, key: RateLimiter::UID)]
    public function search(): string
    {
        // key: RateLimiter::UID，以用户ID为维度进行限流，要求session('user.id')不为空
        return '每个用户60秒最多100次搜索';
    }

    #[RateLimiter(limit: 1, ttl: 60, key: RateLimiter::SID, message: '每人每分钟只能发1次邮件')]
    public function sendMail(): string
    {
        // key: RateLimiter::SID，以session_id为维度进行限流
        return '邮件发送成功';
    }

    #[RateLimiter(limit: 100, ttl: 24*60*60, key: 'coupon', message: '今天的优惠券已经发完，请明天再来')]
    #[RateLimiter(limit: 1, ttl: 24*60*60, key: RateLimiter::UID, message: '每个用户每天只能领取一次优惠券')]
    public function coupon(): string
    {
        // key: 'coupon'， 这里coupon为自定义key，也就是全局以coupon为key进行限流，每天最多发100张优惠券
        // 同时以用户ID为维度进行限流，每个用户每天只能领取一次优惠券
        return '优惠券发送成功';
    }

    #[RateLimiter(limit: 5, ttl: 24*60*60, key: [UserController::class, 'getMobile'], message: '每个手机号一天最多5条短信')]
    public function sendSms2(): string
    {
        // 当key为变量时，可以使用[类, 静态方法]的方式获取key，例如[UserController::class, 'getMobile']会调用UserController的getMobile()方法的返回值为key
        return '短信发送成功';
    }

    /**
     * 自定义key，获取手机号，必须是静态方法
     * @return string
     */
    public static function getMobile(): string
    {
        return request()->get('mobile');
    }

    #[RateLimiter(limit: 1, ttl: 10, key: RateLimiter::IP, message: '频率受限', exception: RuntimeException::class)]
    public function testException(): string
    {
        // 超限默认异常为Webman\RateLimiter\RateLimitException，可以通过exception参数更改
        return 'ok';
    }

}
```

**说明**
* 默认单位时间间隔为1秒钟
* 可以通过ttl设置单位时间间隔，例如 `ttl:60` 为60秒
* 默认限流维度为IP限流(默认`127.0.0.1`不限流，参见下面配置部分)
* 内置IP限流、UID限流(要求`session('user.id')`不为空)，SID限流(根据`session_id`限流)
* 如果使用了nginx代理，IP限流时需要传递`X-Forwarded-For`头，参见[nginx代理](../others/nginx-proxy.md)
* 当超限时会触发`Webman\RateLimiter\RateLimitException`异常，可通过`exception:xx`来自定义异常类
* 超限触发异常时，错误信息默认为`Too Many Requests`，可通过`message:xx`自定义错误信息
* 默认错误信息也可通过[多语言](translation.md)来修改，Linux参考以下命令
```
composer require symfony/translation
mkdir resource/translations/zh_CN/ -p
echo "<?php
return [
    'Too Many Requests' => '请求频率受限'
];" > resource/translations/zh_CN/messages.php
php start.php restart
```

## 接口
有时候开发者想直接在代码中调用限流器，参考如下代码

```php
<?php
namespace app\controller;

use RuntimeException;
use Webman\RateLimiter\Limiter;

class UserController {

    public function sendSms(string $mobile): string
    {
        // 这里mobile作为key
        Limiter::check($mobile, 5, 24*60*60, '每个手机号一天最多5条短信');
        return '短信发送成功';
    }
}
```

##配置
**config/plugin/webman/rate-limiter/app.php**
```
<?php
return [
    'enable' => true,
    'driver' => 'auto', // auto, apcu, memory, redis
    'stores' => [
        'redis' => [
            'connection' => 'default',
        ]
    ],
    // 这些ip的请求不做频率限制(只有在key为 RateLimiter::IP 时有效)
    'ip_whitelist' => [
        '127.0.0.1',
    ],
];
```
* **enable**: 是否开启限流
* **driver**: `auto` `apcu` `memory` `redis`中的一个值，使用`auto`时会自动在`apcu`和`memory`中选一个值
* **stores**: `redis`配置，`connection`对应`config/redis.php`中对应的`key`
* **ip_whitelist**: 白名单的ip不会被限流(只在key为`RateLimiter::IP`时有效)

## driver选择

**memory**

* 介绍
  无需安装任何扩展，性能最好。

* 使用限制
  限流只对当前进程有效，多个进程间不共享限流数据，同时也不支持集群限流。

* 适用场景
  windows开发环境；不需要严格限流的业务；抵御CC攻击时。


**apcu**

* 安装扩展
  需要安装apcu扩展，并且php.ini中设置
```
apc.enabled=1
apc.enable_cli=1
```
如果不知道php.ini位置，可以通过命令`php --ini`寻找php.ini的位置

* 介绍
  性能略低于memory，支持多进程共享限流数据。

* 使用限制
  不支持集群

* 适用场景
  任何开发环境；线上单机限流场景；集群不需要严格限流的场景；抵御CC攻击。

**redis**

* 依赖
  需要安装redis扩展，并安装Redis组件，安装命令
```
composer require -W illuminate/redis illuminate/events
```

* 介绍
  性能低于apcu，支持单机也支持集群精确限流

* 适用场景
  开发环境；线上单机环境；集群环境