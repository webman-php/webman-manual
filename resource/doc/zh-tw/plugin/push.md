## webman/push

`webman/push` 是一個免費的推送服務端插件，客戶端基於訂閱模式，兼容 [pusher](https://pusher.com)，擁有眾多客戶端如JS、安卓(java)、IOS(swift)、IOS(Obj-C)、uniapp、.NET、 Unity、Flutter、AngularJS等。後端推送SDK支持PHP、Node、Ruby、Asp、Java、Python、Go、Swift等。客戶端自帶心跳和斷線自動重連，使用起來非常簡單穩定。適用於消息推送、聊天等諸多即時通訊場景。

插件中自帶一個網頁js客戶端push.js以及uniapp客戶端`uniapp-push.js`，其它語言客戶端在 https://pusher.com/docs/channels/channels_libraries/libraries/ 下載

> 插件需要webman-framework>=1.2.0

## 安裝

```sh
composer require webman/push
```

## 客戶端 (javascript)

**引入javascript客戶端**
```js
<script src="/plugin/webman/push/push.js"> </script>
```

**客戶端使用(公有頻道)**
```js
// 建立連線
var connection = new Push({
    url: 'ws://127.0.0.1:3131', // websocket地址
    app_key: '<app_key，在config/plugin/webman/push/app.php裡獲取>',
    auth: '/plugin/webman/push/auth' // 訂閱鑒權(僅限於私有頻道)
});
// 假設用戶uid為1
var uid = 1;
// 瀏覽器監聽user-1頻道的消息，也就是用戶uid為1的用戶消息
var user_channel = connection.subscribe('user-' + uid);

// 當user-1頻道有message事件的消息時
user_channel.on('message', function(data) {
    // data裡是消息內容
    console.log(data);
});
// 當user-1頻道有friendApply事件時消息時
user_channel.on('friendApply', function (data) {
    // data裡是好友申請相關信息
    console.log(data);
});

// 假設群組id為2
var group_id = 2;
// 瀏覽器監聽group-2頻道的消息，也就是監聽群組2的群消息
var group_channel = connection.subscribe('group-' + group_id);
// 當群組2有message消息事件時
group_channel.on('message', function(data) {
    // data裡是消息內容
    console.log(data);
});
```

> **Tips**
> 以上例子中subscribe實現頻道訂閱，`message` `friendApply` 是頻道上的事件。頻道和事件是任意字串，不需要服務端預先配置。

## 服務端推送(PHP)
```php
use Webman\Push\Api;
$api = new Api(
    // webman下可以直接使用config獲取配置，非webman環境需要手動寫入相應配置
    'http://127.0.0.1:3232',
    config('plugin.webman.push.app.app_key'),
    config('plugin.webman.push.app.app_secret')
);
// 給訂閱 user-1 的所有客戶端推送 message 事件的消息
$api->trigger('user-1', 'message', [
    'from_uid' => 2,
    'content'  => '你好，這個是消息內容'
]);
```

## 私有頻道
以上例子裡任何用戶都可以通過 Push.js 訂閱信息，如果信息是敏感信息，這樣是不安全的。

`webman/push`支持私有頻道訂閱，私有頻道是以 `private-` 開頭的頻道。例如
```js
var connection = new Push({
    url: 'ws://127.0.0.1:3131', // websocket地址
    app_key: '<app_key>',
    auth: '/plugin/webman/push/auth' // 訂閱鑒權(僅限於私有頻道)
});

// 假設用戶uid為1
var uid = 1;
// 瀏覽器監聽private-user-1私有頻道的消息
var user_channel = connection.subscribe('private-user-' + uid);
```

當客戶端訂閱私有頻道時(`private-`開頭的頻道)，瀏覽器會發起一個ajax鑒權請求(ajax地址為new Push時auth參數配置的地址)，開發者可以在這裡判斷，當前用戶是否有權限監聽這個頻道。這樣就保證了訂閱的安全性。

> 關於鑒權參見 `config/plugin/webman/push/route.php` 中的代碼

## 客戶端推送
以上例子都是客戶端訂閱某個頻道，服務端調用API接口推送。webman/push 也支持客戶端直接推送消息。

> **注意**
> 客戶端間推送僅支持私有頻道(`private-`開頭的頻道)，並且客戶端只能觸發以 `client-` 開頭的事件。

客戶端觸發事件推送的例子
```js
var user_channel = connection.subscribe('private-user-1');
user_channel.on('client-message', function (data) {
    // 
});
user_channel.trigger('client-message', {form_uid:2, content:"hello"});
```

> **注意**
> 以上代碼給所有(除了當前客戶端)訂閱了 `private-user-1` 的客戶端推送 `client-message` 事件的數據(推送客戶端不會收到自己推送的數據)。

## webhooks

webhook用來接收頻道的一些事件。

**目前主要有2個事件：**

- 1、channel_added
  當某個頻道從沒有客戶端在線到有客戶端在線時觸發的事件，或者說是在線事件

- 2、channel_removed
  當某個頻道的所有客戶端都下線時觸發的事件，或者說是離線事件

> **Tips**
> 這些事件在維護用戶在線狀態非常有用。

> **注意**
> webhook地址在`config/plugin/webman/push/app.php`中配置。
> 接收處理webhook事件的代碼參考 `config/plugin/webman/push/route.php` 裡面的邏輯
> 由於刷新頁面導致用戶短暫離線不應該算作離線，webman/push會做延遲判斷，所以在線/離線事件會有1-3秒的延遲。
## wss代理(SSL)
https下無法使用ws連線，需要使用wss連線。這種情況可以使用nginx代理wss，配置類似如下：
```
server {
    # .... 這裡省略了其他配置 ...

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
**注意 上面配置中的`<app_key>`在`config/plugin/webman/push/app.php`中获取**

重啟nginx後，使用以下方式連接服務端
```
var connection = new Push({
    url: 'wss://example.com',
    app_key: '<app_key，在config/plugin/webman/push/app.php裡取得>',
    auth: '/plugin/webman/push/auth' // 訂閱驗證(僅限於私有頻道)
});
```
> **注意**
> 1. 請求地址為wss開頭
> 2. 不寫端口
> 3. 必須使用**ssl證書對應的域名**連接

## push-vue.js 使用說明

1、將文件 push-vue.js複製到專案目錄下，如：src/utils/push-vue.js

2、在vue頁面內引入
```js

<script lang="ts" setup>
import {  onMounted } from 'vue'
import { Push } from '../utils/push-vue'

onMounted(() => {
  console.log('組件已經掛載') 

  //實例化webman-push

  // 建立連接
  var connection = new Push({
    url: 'ws://127.0.0.1:3131', // websocket地址
    app_key: '<app_key，在config/plugin/webman/push/app.php裡取得>',
    auth: '/plugin/webman/push/auth' // 訂閱驗證(僅限於私有頻道)
  });

  // 假設使用者uid為1
  var uid = 1;
  // 瀏覽器監聽user-1頻道的訊息，也就是使用者uid為1的使用者訊息
  var user_channel = connection.subscribe('user-' + uid);

  // 當user-1頻道有message事件的訊息時
  user_channel.on('message', function (data) {
    // data裡是訊息內容
    console.log(data);
  });
  // 當user-1頻道有friendApply事件時訊息時
  user_channel.on('friendApply', function (data) {
    // data裡是好友申請相關信息
    console.log(data);
  });

  // 假設群組id為2
  var group_id = 2;
  // 瀏覽器監聽group-2頻道的訊息，也就是監聽群組2的群訊息
  var group_channel = connection.subscribe('group-' + group_id);
  // 當群組2有message訊息事件時
  group_channel.on('message', function (data) {
    // data裡是訊息內容
    console.log(data);
  });


})

</script>
```

## 其他客戶端地址
`webman/push` 兼容pusher，其他語言(Java Swift .NET Objective-C Unity Flutter Android  IOS AngularJS等)客戶端地址下載地址：
https://pusher.com/docs/channels/channels_libraries/libraries/
