# webman/push 推送插件

`webman/push` 是一个推送插件，客户端基于订阅模式，兼容 **pusher**，拥有众多客户端如JS、安卓(java)、IOS(swift)、IOS(Obj-C)、uniapp。后端推送SDK支持PHP、Node、Ruby、Asp、Java、Python、Go等。使用起来非常简单稳定。适用于消息推送、聊天等诸多即时通讯场景。

> 插件需要webman>=1.2.0 webman-framework>=1.2.0

## 安装
 
```sh
composer require webman/push
```

## 客户端 (javascript)

**引入javascript客户端**
```js
<script src="/plugin/webman/push/push.js"> </script>
```

**客户端使用(公有频道)**
```js
// 建立连接
var connection = new Push({
    url: 'ws://127.0.0.1:3131', // websocket地址
    app_key: '<app_key，在config/plugin/webman/push/app.php里获取>',
    auth: '/plugin/webman/push/auth' // 订阅鉴权(仅限于私有频道)
});
// 假设用户uid为1
var uid = 1;
// 浏览器监听user-1频道的消息，也就是用户uid为1的用户消息
var user_channel = connection.subscribe('user-' + uid);

// 当user-1频道有message事件的消息时
user_channel.on('message', function(data) {
    // data里是消息内容
    console.log(data);
});
// 当user-1频道有friendApply事件时消息时
user_channel.on('friendApply', function (data) {
    // data里是好友申请相关信息
    console.log(data);
});

// 假设群组id为2
var group_id = 2;
// 浏览器监听group-2频道的消息，也就是监听群组2的群消息
var group_channel = connection.subscribe('group-' + group_id);
// 当群组2有message消息事件时
group_channel.on('message', function(data) {
    // data里是消息内容
    console.log(data);
});
```

> **Tips**
> 以上例子中subscribe实现频道订阅，`message` `friendApply` 是频道上的事件。频道和事件是任意字符串，不需要服务端预先配置。

## 服务端推送(PHP)
```php
use Webman\Push\Api;
$api = new Api(
    // webman下可以直接使用config获取配置，非webman环境需要手动写入相应配置
    config('plugin.webman.push.app.api'),
    config('plugin.webman.push.app.app_key'),
    config('plugin.webman.push.app.app_secret')
);
// 给订阅 user-1 的所有客户端推送 message 事件的消息
$api->trigger('user-1', 'message', [
    'from_uid' => 2,
    'content'  => '你好，这个是消息内容'
]);
```

## 私有频道
以上例子里任何用户都可以通过 Push.js 订阅信息，如果信息是敏感信息，这样是不安全的。

`webman/push`支持私有频道订阅，私有频道是以 `private-` 开头的频道。例如
```js
var connection = new Push({
    url: 'ws://127.0.0.1:3131', // websocket地址
    app_key: '<app_key>',
    auth: '/plugin/webman/push/auth' // 订阅鉴权(仅限于私有频道)
});

// 假设用户uid为1
var uid = 1;
// 浏览器监听private-user-1私有频道的消息
var user_channel = connection.subscribe('private-user-' + uid);
```

当客户端订阅私有频道时(`private-`开头的频道)，浏览器会发起一个ajax鉴权请求(ajax地址为new Push时auth参数配置的地址)，开发者可以在这里判断，当前用户是否有权限监听这个频道。这样就保证了订阅的安全性。

> 关于鉴权参见 `config/plugin/webman/push/route.php` 中的代码

## 客户端推送
以上例子都是客户端订阅某个频道，服务端调用API接口推送。webman/push 也支持客户端直接推送消息。

> **注意**
> 客户端间推送仅支持私有频道(`private-`开头的频道)，并且客户端只能触发以 `client-` 开头的事件。

客户端触发事件推送的例子
```js
var user_channel = connection.subscribe('private-user-1');
user_channel.on('client-message', function (data) {
    // 
});
user_channel.trigger('client-message', {form_uid:2, content:"hello"});
```

> **注意**
> 以上代码给所有(除了当前客户端)订阅了 `private-user-1` 的客户端推送 `client-message` 事件的数据(推送客户端不会收到自己推送的数据)。

## webhooks

webhook用来接收频道的一些事件。

**目前主要有2个事件：**

- 1、channel_added
当某个频道从没有客户端在线到有客户端在线时触发的事件，或者说是在线事件

- 2、channel_removed
当某个频道的所有客户端都下线时触发的事件，或者说是离线事件

> **Tips**
> 这些事件在维护用户在线状态非常有用。

> **注意**
> webhook地址在`config/plugin/webman/push/app.php`中配置。
> 接收处理webhook事件的代码参考 `config/plugin/webman/push/route.php` 里面的逻辑
> 由于刷新页面导致用户短暂离线不应该算作离线，webman/push会做延迟判断，所以在线/离线事件会有1-3秒的延迟。

## nginx代理wss
https需要使用wss才能正常通讯，nginx请参考以下配置设置ssl代理(需要重启nginx)
```
server {
    listen 443 ssl;
        
    # 这里省略其他配置 . . .
        
    location /app
    {
        proxy_pass http://127.0.0.1:3131;
        proxy_http_version 1.1;
        proxy_set_header Upgrade $http_upgrade;
        proxy_set_header Connection "Upgrade";
        proxy_set_header X-Real-IP $remote_addr;
    }
    
}
```

配置好nginx代理后， 前端使用wss连接，并省略端口号，例如
```js
var connection = new Push({
    url: 'wss://域名.com', // 使用wss，省略端口号
    app_key: '<app_key>',
    auth: '/plugin/webman/push/auth' // 订阅鉴权(仅限于私有频道)
});
```

> **注意**
> ssl证书一般是与域名绑定的，所以wss请使用证书所对应的域名，不要使用ip或者其它域名。