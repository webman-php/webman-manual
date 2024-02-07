## webman/push

`webman/push` is a free push service server-side plugin, The client is based on the subscription mode, compatible with [pusher](https://pusher.com), and it has many clients such as JS, Android (java), IOS (swift), IOS (Obj-C), uniapp, .NET, Unity, Flutter, AngularJS, and more. The backend push SDK supports PHP, Node, Ruby, Asp, Java, Python, Go, Swift, and so on. The client-side comes with heartbeat and automatic reconnection after disconnection, making it very easy and stable to use. It is suitable for many instant communication scenarios such as message push, chat, etc.

The plugin comes with a web page JS client push.js and a uniapp client `uniapp-push.js`, and other language clients can be downloaded at https://pusher.com/docs/channels/channels_libraries/libraries/

> The plugin requires webman-framework>=1.2.0

## Installation

```sh
composer require webman/push
```

## Client (Javascript)

**Import Javascript client**
```js
<script src="/plugin/webman/push/push.js"> </script>
```

**Client usage (public channel)**
```js
// Establish a connection
var connection = new Push({
    url: 'ws://127.0.0.1:3131', // websocket address
    app_key: '<app_key, obtained in config/plugin/webman/push/app.php>',
    auth: '/plugin/webman/push/auth' // subscription authentication (private channel only)
});
// Assuming user uid is 1
var uid = 1;
// Browser listens for messages on the user-1 channel, for user with uid 1
var user_channel = connection.subscribe('user-' + uid);

// When the user-1 channel has a message event
user_channel.on('message', function(data) {
    // the message content is in the data
    console.log(data);
});
// When the user-1 channel has a friendApply event
user_channel.on('friendApply', function (data) {
    // data contains information about the friend request
    console.log(data);
});

// Assuming the group id is 2
var group_id = 2;
// Browser listens for messages on the group-2 channel, for group 2
var group_channel = connection.subscribe('group-' + group_id);
// When group 2 has a message event
group_channel.on('message', function(data) {
    // data contains the message content
    console.log(data);
});
```

> **Tips**
> In the above example, subscribe is used to subscribe to a channel, `message` `friendApply` are events on the channel. The channel and events are arbitrary strings and do not need to be configured on the server in advance.

## Server-side Push (PHP)
```php
use Webman\Push\Api;
$api = new Api(
    // Using the config directly in webman to obtain the configuration, if not in a webman environment, the corresponding configuration needs to be manually written
    'http://127.0.0.1:3232',
    config('plugin.webman.push.app.app_key'),
    config('plugin.webman.push.app.app_secret')
);
// Push a message event to all clients subscribed to user-1
$api->trigger('user-1', 'message', [
    'from_uid' => 2,
    'content'  => 'Hello, this is the message content'
]);
```

## Private Channels
In the above examples, any user can subscribe to information through Push.js. If the information is sensitive, this is not secure.

`webman/push` supports subscription to private channels, private channels are channels that start with `private-`. For example
```js
var connection = new Push({
    url: 'ws://127.0.0.1:3131', // websocket address
    app_key: '<app_key>',
    auth: '/plugin/webman/push/auth' // subscription authentication (private channel only)
});

// Assuming user uid is 1
var uid = 1;
// The browser listens for messages on the private-user-1 private channel
var user_channel = connection.subscribe('private-user-' + uid);
```

When a client subscribes to a private channel (a channel starting with `private-`), the browser initiates an ajax authentication request (the ajax address is the address configured in the auth parameter when creating a new Push), where developers can determine if the current user has permission to listen to this channel. This ensures the security of the subscription.

> For authentication, refer to the code in `config/plugin/webman/push/route.php`

## Client-side Push
The above examples all involve clients subscribing to a particular channel and the server calling the API to push messages. webman/push also supports clients pushing messages directly.

> **Note**
> Client-to-client push only supports private channels (channels starting with `private-`) and clients can only trigger events starting with `client-`.

Example of triggering event push from the client side
```js
var user_channel = connection.subscribe('private-user-1');
user_channel.on('client-message', function (data) {
    // 
});
user_channel.trigger('client-message', {form_uid:2, content:"hello"});
```

> **Note**
> The above code pushes data for the `client-message` event to all clients (except the current client) subscribed to `private-user-1` (the pushing client will not receive the data they pushed).

## Webhooks

Webhooks are used to receive some events from channels.

**Currently, there are mainly two events:**

- 1. channel_added
  Triggered when a channel goes from having no clients online to having clients online, or in other words, the online event

- 2. channel_removed
  Triggered when all clients of a certain channel go offline, or in other words, the offline event

> **Tips**
> These events are very useful for maintaining user online status.

> **Note**
> The webhook address is configured in `config/plugin/webman/push/app.php`.
> The code for receiving and handling webhook events can be found in the logic in `config/plugin/webman/push/route.php`
> Since a brief user offline due to a page refresh shouldn't be counted as offline, webman/push would do a delayed judgment, so online/offline events will have a delay of 1-3 seconds.

## WSS Proxy (SSL)
WSS connections cannot be used under HTTPS and require the use of a WSS connection. In this case, Nginx can be used to proxy WSS, with configuration similar to the following:
```nginx
server {
    # .... other configurations here ...

    location /app/<app_key>
    {
        proxy_pass http://127.0.0.1:3131;
        proxy_http_version 1.1;
        proxy_set_header Upgrade $http_upgrade;
        proxy_set_header Connection "Upgrade";
        proxy_set_header X-Real-IP $remote_addr;
    }
}
```
**Note the `<app_key>` in the above configuration is obtained from `config/plugin/webman/push/app.php`**

After restarting Nginx, connect to the server using the following method
```js
var connection = new Push({
    url: 'wss://example.com',
    app_key: '<app_key, obtained from config/plugin/webman/push/app.php>',
    auth: '/plugin/webman/push/auth' // subscription authentication (private channel only)
});
```
> **Note**
> 1. The request address starts with wss
> 2. Port number is not specified
> 3. It is necessary to connect using the **domain of the SSL certificate**

## push-vue.js Usage Instructions

1. Copy the file push-vue.js to the project directory, for example: src/utils/push-vue.js

2. Import it in the vue page
```js
import { Push } from '../utils/push-vue'
```

## Other Client Addresses
`webman/push` is compatible with pusher, and the addresses for other language clients (Java, Swift, .NET, Objective-C, Unity, Flutter, Android, IOS, AngularJS, etc.) can be downloaded at:
https://pusher.com/docs/channels/channels_libraries/libraries/
