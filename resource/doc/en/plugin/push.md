##Profile

`webman/push` but imagine if，ClientAssuming you already have，Compatible **pusher**，Cannot be savedClient如JS、Android(java)、IOS(swift)、IOS(Obj-C)、uniapp。Forensic requestSDKsupportPHP、Node、Ruby、Asp、Java、Python、Go等。Very easy and stable to use。is a push plugin、chat and many other instant messaging scenarios。

> Plugin Requiredwebman>=1.2.0 webman-framework>=1.2.0

## Install

```sh
composer require webman/push
```

## Client (javascript)

**Introduce javascript client**
```js
<script src="/plugin/webman/push/push.js"> </script>
```

**Client-side use (public channel))**
```js
// Establish Connection
var connection = new Push({
    url: 'ws://127.0.0.1:3131', // websocketAddress
    app_key: '<app_key，Get in config/plugin/webman/push/app.php>',
    auth: '/plugin/webman/push/auth' // Subscription Authentication (Private Channels Only))
});
// Assume user uid is1
var uid = 1;
// The browser listens for messages on the user-1 channel, that is, messages from users with a user uid of 1.
var user_channel = connection.subscribe('user-' + uid);

// When user-1 channel has message event messages
user_channel.on('message', function(data) {
    // dataIn is the message content
    console.log(data);
});
// When the user-1 channel has a friendApply event when the message
user_channel.on('friendApply', function (data) {
    // dataThis is the information about the friend request
    console.log(data);
});

// Assume group id is2
var group_id = 2;
// The browser listens to the group-2 channel, which means it listens to group messages from group 2
var group_channel = connection.subscribe('group-' + group_id);
// When group 2 has a message message event
group_channel.on('message', function(data) {
    // dataIn is the message content
    console.log(data);
});
```

> **Tips**
> The above example subscribe implements channel subscription, `message` `friendApply` is an event on the channel. Channels and events are arbitrary strings and do not need to be preconfigured by the server。

## Server-side push(PHP)
```php
use Webman\Push\Api;
$api = new Api(
    // webmanYou can use config to get the configuration directly under `scripts/update.php`, but for non-webman environments you need to write the configuration manually
    'http://127.0.0.1:3232',
    config('plugin.webman.push.app.app_key'),
    config('plugin.webman.push.app.app_secret')
);
// Push message event messages to all clients subscribed to user-1
$api->trigger('user-1', 'message', [
    'from_uid' => 2,
    'content'  => 'Hello, this is the message content'
]);
```

## Private Channels
Any user in the above example can subscribe to a message via Push.js, which is not secure if the message is sensitive。

`webman/push`Support private channel subscription, private channels are channels starting with `private-`. For example
```js
var connection = new Push({
    url: 'ws://127.0.0.1:3131', // websocketAddress
    app_key: '<app_key>',
    auth: '/plugin/webman/push/auth' // Subscription Authentication (Private Channels Only))
});

// Assume user uid is1
var uid = 1;
// The browser listens for messages from the private-user-1 private channel
var user_channel = connection.subscribe('private-user-' + uid);
```

当ClientsubscribePrivate Channels时(`private-`Beginningthe channel)，The browser will initiate aajaxmust use(ajaxAddress为new Push时authChange default valuesAddress)，The developer can determine here，Does the current user have permission to listen to this channel。This ensures the security of subscriptions。

> For authentication see the code in `config/plugin/webman/push/route.php`

## Client Push
The above examples are all about the client subscribing to a channel and the server calling the API interface to push it. webman/push also supports direct client-side push messages。

> **Note**
> Inter-client push only supports private channels (channels starting with `private-`), and clients can only trigger events starting with `client-`。

Example of client-side triggered event push
```js
var user_channel = connection.subscribe('private-user-1');
user_channel.on('client-message', function (data) {
    // 
});
user_channel.trigger('client-message', {form_uid:2, content:"hello"});
```

> **Note**
> The above code gives all(Except for the currentClient)Subscribed `private-user-1` 的Client Push `client-message` Parameter configuration of(PushClientwill not receive data pushed by itself)。

## webhooks

webhookUsed to receive some events from the channel。

**Currently there are 2 main events：**

- 1、channel_added
  The event triggered when a channel goes from having no client online to having a client online, or online event

- 2、channel_removed
  Event triggered when all clients of a channel are offline, or offline event

> **Tips**
> These events are very useful in maintaining user online status。

> **Note**
> webhookThe address is configured in `config/plugin/webman/push/app.php`。
> The code to receive and handle webhook events refers to the logic in `config/plugin/webman/push/route.php`
> A brief user offline due to page refresh should not be counted as offline, webman/push will do a delay judgment, so online/offline events will have a delay of 1-3 seconds。

## wssProxy(SSL)
httpsYou can't use ws connection under `webman/console`, you need to use wss connection. In this case you can use nginx proxy wss with a configuration similar to the following ：
```
server {
    # .... Other configuration omitted here ...

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
After restarting nginx, use the following to connect to the server
```
var connection = new Push({
    url: 'wss://example.com',
    app_key: '<app_key，Get in config/plugin/webman/push/app.php>',
    auth: '/plugin/webman/push/auth' // Subscription Authentication (Private Channels Only))
});
```
> **Note**
> 1. wssBeginning
> 2. Do not write port
> 3. Generally**sslThe certificate corresponds to the domain name**Connections

## Other client addresses
`webman/push` Compatiblepusher，Debug mode(Java Swift .NET Objective-C Unity Flutter Android  IOS AngularJS等)ClientAddressdownloadAddress：
https://pusher.com/docs/channels/channels_libraries/libraries/